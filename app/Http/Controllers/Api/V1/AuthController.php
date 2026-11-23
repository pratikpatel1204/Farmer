<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Middleware\ApiActiveMember;
use App\Models\City;
use App\Models\DeviceToken;
use App\Models\User;
use App\Support\ApiData;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * App login / register / forgot password.
 * Rules aur messages website ke Member\AuthMemberController jaise hi.
 * Fark sirf itna: session ki jagah Sanctum token milta hai.
 */
class AuthController extends ApiController
{
    private const OTP_MINUTES   = 10;
    private const OTP_ATTEMPTS  = 5;
    private const RESEND_AFTER  = 30; // seconds
    private const RESET_MINUTES = 15;

    /* ================= LIVE CHECKS (register) ================= */

    /** GET /auth/check-sponsor/{id} */
    public function checkSponsor(string $id)
    {
        $name = $this->findSponsor($id);

        return $name
            ? $this->ok(['name' => $name])
            : $this->fail('Invalid Sponsor ID', 404);
    }

    /** GET /auth/check-mobile?mobile= */
    public function checkMobile(Request $request)
    {
        $mobile = trim((string) $request->query('mobile'));

        return $this->ok(['available' => $mobile === '' || ! User::where('mobile', $mobile)->exists()]);
    }

    /** GET /auth/check-email?email= (email optional hai) */
    public function checkEmail(Request $request)
    {
        $email = strtolower(trim((string) $request->query('email')));

        return $this->ok(['available' => $email === '' || ! User::where('email', $email)->exists()]);
    }

    /* ================= REGISTER ================= */

    /** POST /auth/register */
    public function register(Request $request)
    {
        $this->check($request, [
            'user_type'      => 'required|in:farmer,non_farmer',
            'full_name'      => 'required|string|max:255',
            'mobile'         => ['required', 'regex:/^[6-9]\d{9}$/', 'unique:users,mobile'],
            'email'          => 'nullable|email|max:255|unique:users,email',
            'state_id'       => 'required|exists:states,id',
            'city_id'        => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if (! City::where('id', $value)->where('state_id', $request->state_id)->exists()) {
                        $fail('Selected city does not belong to the selected state.');
                    }
                },
            ],
            'area'           => 'required|string|max:150',
            'pincode'        => ['required', 'regex:/^[1-9]\d{5}$/'],
            'address'        => 'required|string|max:1000',
            'address_source' => 'nullable|in:google,manual',
            'sponsor_id'     => 'required|string|max:50',
            'password'       => 'required|string|min:6|confirmed',
            'terms'          => 'accepted',
            'device_name'    => 'nullable|string|max:100',
        ], [
            'mobile.regex'        => 'Please enter a valid 10-digit mobile number.',
            'mobile.unique'       => 'This mobile number is already registered.',
            'email.unique'        => 'This email is already registered.',
            'pincode.regex'       => 'Please enter a valid 6-digit pincode.',
            'terms.accepted'      => 'You must accept the Terms and Conditions.',
            'sponsor_id.required' => 'Sponsor ID is required.',
        ]);

        $sponsorName = $this->findSponsor($request->sponsor_id);
        if (! $sponsorName) {
            return $this->fail('Invalid Sponsor ID.', 422, ['sponsor_id' => ['Invalid Sponsor ID.']]);
        }

        try {
            DB::beginTransaction();

            $isFarmer = $request->user_type === 'farmer';
            $role     = $isFarmer ? 'Farmer' : 'Non Farmer';
            $prefix   = $isFarmer ? 'FRM' : 'NFR';

            do {
                $uniqueId = $prefix . random_int(100000, 999999);
            } while (User::where('unique_id', $uniqueId)->exists());

            // Website ke register_submit jaisa hi (same columns)
            $user                 = new User();
            $user->role           = $role;
            $user->unique_id      = $uniqueId;
            $user->sponsor_id     = $request->sponsor_id;
            $user->sponsor_name   = $sponsorName;
            $user->full_name      = trim($request->full_name);
            $user->mobile         = $request->mobile;
            $user->email          = $request->filled('email') ? strtolower(trim($request->email)) : null;
            $user->password       = Hash::make($request->password);
            $user->temp_password  = $request->password;
            $user->state          = $request->state_id;
            $user->city           = $request->city_id;
            $user->area           = trim($request->area);
            $user->pincode        = $request->pincode;
            $user->address        = trim($request->address);
            $user->address_source = $request->address_source ?: 'manual';
            $user->status         = 1;
            $user->save();

            $user->assignRole($role);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API registration failed: ' . $e->getMessage());

            return $this->fail('Registration failed, please try again.', 500);
        }

        Notify::memberRegistered($user);

        $token = $user->createToken($this->deviceName($request))->plainTextToken;

        return $this->ok([
            'token'     => $token,
            'unique_id' => $uniqueId,
            'user'      => ApiData::me($user->fresh()),
        ], 'Registration successful!', 201);
    }

    /* ================= LOGIN / LOGOUT ================= */

    /** POST /auth/login { login: mobile ya unique_id, password, device_name } */
    public function login(Request $request)
    {
        $this->check($request, [
            'login'       => 'required|string',
            'password'    => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $loginValue = preg_replace('/^\+?91(?=\d{10}$)/', '', trim($request->input('login')));
        $field      = preg_match('/^[0-9]+$/', $loginValue) ? 'mobile' : 'unique_id';

        $user = User::where($field, $loginValue)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return $this->fail('Invalid mobile number / unique ID or password. Please try again.', 422);
        }

        if ((int) $user->status === 0 || ! empty($user->deleted_at)) {
            return $this->fail('Your account is inactive. Please contact support to activate.', 403);
        }

        if (! in_array($user->role, ApiActiveMember::ROLES, true)) {
            return $this->fail('This app is for Farmer and Non Farmer members. Please use the admin panel on the website.', 403);
        }

        $token = $user->createToken($this->deviceName($request))->plainTextToken;

        return $this->ok([
            'token' => $token,
            'user'  => ApiData::me($user),
        ], 'Logged in successfully.');
    }

    /** POST /auth/logout { device_token? } */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($request->filled('device_token')) {
            DeviceToken::where('user_id', $user->id)->where('token', $request->device_token)->delete();
        }

        $user->currentAccessToken()?->delete();

        return $this->ok(null, 'Logged out.');
    }

    /* ================= FORGOT PASSWORD (email OTP) ================= */

    /** Step 1: POST /auth/forgot-password { email } */
    public function forgotPassword(Request $request)
    {
        $this->check($request, ['email' => 'required|email|max:255'], [
            'email.required' => 'Please enter your email.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->where('status', 1)->first();

        if (! $user) {
            return $this->fail('No active account found with this email.', 422);
        }

        $token = Str::random(48);

        if (! $this->sendOtp($token, $user)) {
            return $this->fail('Unable to send OTP right now. Please try again later.', 500);
        }

        return $this->ok([
            'token'          => $token,
            'email'          => $this->maskEmail($user->email),
            'expires_in'     => self::OTP_MINUTES * 60,
            'resend_after'   => self::RESEND_AFTER,
        ], 'OTP sent to your email.');
    }

    /** POST /auth/forgot-password/resend { token } */
    public function resendOtp(Request $request)
    {
        $token = (string) $request->input('token');
        $data  = $this->otpData($token);

        if (! $data) {
            return $this->fail('Session expired. Please start again.', 422);
        }

        if (now()->timestamp - $data['sent_at'] < self::RESEND_AFTER) {
            return $this->fail('Please wait a few seconds before resending.', 429);
        }

        $user = User::where('id', $data['user_id'])->where('status', 1)->first();

        if (! $user || ! $this->sendOtp($token, $user)) {
            return $this->fail('Could not resend OTP.', 500);
        }

        return $this->ok(null, 'A new OTP has been sent to your email.');
    }

    /** Step 2: POST /auth/forgot-password/verify { token, otp } -> reset_token */
    public function verifyOtp(Request $request)
    {
        $token = (string) $request->input('token');
        $otp   = preg_replace('/\D/', '', (string) $request->input('otp'));
        $data  = $this->otpData($token);

        if (! $data) {
            return $this->fail('OTP expired. Please request a new one.', 422);
        }

        if ($data['attempts'] >= self::OTP_ATTEMPTS) {
            Cache::forget($this->otpKey($token));

            return $this->fail('Too many wrong attempts. Please request a new OTP.', 429);
        }

        if (strlen($otp) !== 6 || ! Hash::check($otp, $data['otp'])) {
            $data['attempts']++;
            Cache::put($this->otpKey($token), $data, now()->addMinutes(self::OTP_MINUTES));

            return $this->fail('Invalid OTP. Please try again.', 422);
        }

        Cache::forget($this->otpKey($token));

        // Website session me user id rakhti hai; app ke liye 15 min ka reset_token
        $resetToken = Str::random(64);
        Cache::put($this->resetKey($resetToken), $data['user_id'], now()->addMinutes(self::RESET_MINUTES));

        return $this->ok([
            'reset_token' => $resetToken,
            'expires_in'  => self::RESET_MINUTES * 60,
        ], 'OTP verified. Please set your new password.');
    }

    /** Step 3: POST /auth/reset-password { reset_token, password, password_confirmation } */
    public function resetPassword(Request $request)
    {
        $resetToken = (string) $request->input('reset_token');
        $userId     = $resetToken !== '' ? Cache::get($this->resetKey($resetToken)) : null;

        if (! $userId) {
            return $this->fail('Session expired. Please request a new OTP.', 422);
        }

        // Website ke save_password jaisa strong password rule
        $this->check($request, [
            'password' => ['required', 'string', 'min:6', 'confirmed', 'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{6,}$/'],
        ], [
            'password.regex'     => 'Password must have 1 capital letter, 1 number and 1 special character (min 6).',
            'password.confirmed' => 'Confirm password does not match.',
        ]);

        $user = User::where('id', $userId)->where('status', 1)->first();

        if (! $user) {
            Cache::forget($this->resetKey($resetToken));

            return $this->fail('Account not found or inactive.', 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget($this->resetKey($resetToken));

        // Purane saare app login band (security)
        $user->tokens()->delete();

        return $this->ok(null, 'Password updated successfully. Please log in.');
    }

    /* ================= HELPERS ================= */

    private function findSponsor(string $id): ?string
    {
        $id = trim($id);
        if (! preg_match('/^[A-Za-z0-9_-]{3,30}$/', $id)) {
            return null;
        }

        $sponsor = User::where('unique_id', $id)->where('status', 1)->first();

        return $sponsor ? trim((string) $sponsor->full_name) : null;
    }

    private function deviceName(Request $request): string
    {
        return Str::limit(trim((string) $request->input('device_name')) ?: 'mobile-app', 100, '');
    }

    /** Website wali hi cache key, taaki dono flow ek jaise rahein */
    private function otpKey(string $token): string
    {
        return 'forgot_otp_' . hash('sha256', $token);
    }

    private function resetKey(string $token): string
    {
        return 'api_reset_' . hash('sha256', $token);
    }

    private function otpData(string $token): ?array
    {
        return strlen($token) === 48 ? Cache::get($this->otpKey($token)) : null;
    }

    /** OTP banao, cache me (hash) rakho, email bhejo. Website ka EmailForgotOtp mail ho to wahi design. */
    private function sendOtp(string $token, User $user): bool
    {
        $otp = (string) random_int(100000, 999999);

        try {
            if (class_exists(\App\Mail\EmailForgotOtp::class)) {
                Mail::to($user->email)->send(new \App\Mail\EmailForgotOtp($user, $otp));
            } else {
                Mail::raw(
                    'Your ' . config('app.name') . " password reset OTP is {$otp}.\n\n"
                        . 'It is valid for ' . self::OTP_MINUTES . " minutes. Do not share it with anyone.\n"
                        . 'If you did not request this, you can ignore this email.',
                    fn ($message) => $message->to($user->email)->subject(config('app.name') . ' - Password reset OTP')
                );
            }
        } catch (\Throwable $e) {
            Log::error('API forgot password OTP mail failed: ' . $e->getMessage());

            return false;
        }

        Cache::put($this->otpKey($token), [
            'user_id'  => $user->id,
            'otp'      => Hash::make($otp),
            'attempts' => 0,
            'sent_at'  => now()->timestamp,
        ], now()->addMinutes(self::OTP_MINUTES));

        return true;
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $keep = min(2, strlen($name));

        return substr($name, 0, $keep) . str_repeat('*', max(strlen($name) - $keep, 3)) . '@' . $domain;
    }
}
