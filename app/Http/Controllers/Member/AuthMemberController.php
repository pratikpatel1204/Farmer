<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Mail\EmailForgotOtp;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthMemberController extends Controller
{
    /* =====================================================================
     | LOGIN
     ===================================================================== */

    public function login_form()
    {
        return view('front.auth.login');
    }

    public function login_check(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginValue = trim($request->input('login'));
        $password   = $request->input('password');

        $loginValue = preg_replace('/^\+?91(?=\d{10}$)/', '', $loginValue);

        $field = preg_match('/^[0-9]+$/', $loginValue) ? 'mobile' : 'unique_id';

        $user = User::where($field, $loginValue)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid mobile number / unique ID or password. Please try again.',
                'status'  => 0,
            ], 422);
        }

        if ((int) $user->status === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support to activate.',
                'status'  => 0,
            ], 422);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return response()->json([
            'success'     => true,
            'message'     => 'Logged in successfully.',
            'redirectUrl' => route('home'),
            'status'      => 1,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /* =====================================================================
     | REGISTER
     ===================================================================== */

    public function register_form(Request $request)
    {
        $states         = State::orderBy('name')->get(['id', 'name']);
        $prefillSponsor = $this->cleanSponsorId($request->query('sponsor_id'));

        return view('front.auth.register', compact('states', 'prefillSponsor'));
    }

    public function cities($stateId)
    {
        $cities = City::where('state_id', $stateId)->orderBy('name')->get(['id', 'name']);
        return response()->json(['success' => true, 'cities' => $cities]);
    }

    public function register_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type'      => 'required|in:farmer,non_farmer',
            'full_name'      => 'required|string|max:255',
            'mobile'         => ['required', 'regex:/^[6-9]\d{9}$/', 'unique:users,mobile'],
            'email'          => 'nullable|email|max:255|unique:users,email',
            'state_id'       => 'required|exists:states,id',
            'city_id'        => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $ok = City::where('id', $value)->where('state_id', $request->state_id)->exists();
                    if (!$ok) {
                        $fail('Selected city does not belong to the selected state.');
                    }
                },
            ],
            'area'           => 'required|string|max:150',
            'pincode'        => ['required', 'regex:/^[1-9]\d{5}$/'],
            'address'        => 'required|string|max:1000',
            'address_source' => 'nullable|in:google,manual',
            'sponsor_id'     => 'nullable|string|max:50',
            'password'       => 'required|string|min:6|confirmed',
            'terms'          => 'accepted',
        ], [
            'mobile.regex'       => 'Please enter a valid 10-digit mobile number.',
            'mobile.unique'      => 'This mobile number is already registered.',
            'email.unique'       => 'This email is already registered.',
            'pincode.regex'      => 'Please enter a valid 6-digit pincode.',
            'terms.accepted'     => 'You must accept the Terms and Conditions.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => 'Please check the highlighted fields and try again.',
            ], 422);
        }

        // Sponsor ID optional hai: khali ho to null save hoga, bhara ho to valid hona chahiye
        $sponsorId   = $request->filled('sponsor_id') ? trim($request->sponsor_id) : null;
        $sponsorName = null;
        if ($sponsorId !== null) {
            $sponsorName = $this->findSponsor($sponsorId);
        }
        if ($sponsorId !== null && !$sponsorName) {
            return response()->json([
                'success' => false,
                'errors'  => ['sponsor_id' => ['Invalid Sponsor ID.']],
                'message' => 'Invalid Sponsor ID.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $isFarmer = $request->user_type === 'farmer';
            $role     = $isFarmer ? 'Farmer' : 'Non Farmer';
            $prefix   = $isFarmer ? 'FRM' : 'NFR';

            do {
                $uniqueId = $prefix . random_int(100000, 999999);
            } while (User::where('unique_id', $uniqueId)->exists());

            $user                 = new User();
            $user->role           = $role;
            $user->unique_id      = $uniqueId;
            $user->sponsor_id     = $sponsorId;
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
            Log::error('User registration failed: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Registration failed, please try again.',
            ], 500);
        }

        // Notification commit ke BAAD: Admin ko "New member registered" + member ko Welcome.
        // Notify ke andar try/catch hai, isliye ye fail ho to bhi registration nahi rukta.
        Notify::memberRegistered($user);

        return response()->json([
            'success'     => true,
            'message'     => 'Registration successful! Please login.',
            'redirectUrl' => route('login'),
            'login'       => [
                'unique_id' => $uniqueId,
                'mobile'    => $user->mobile,
                'password'  => $request->password,
            ],
        ]);
    }

    /* =====================================================================
     | LIVE CHECKS (register page)
     ===================================================================== */

    public function checkSponsorId($id)
    {
        try {
            $name = $this->findSponsor($id);

            if ($name) {
                return response()->json(['success' => true, 'name' => $name]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid Sponsor ID']);
        } catch (\Throwable $e) {
            Log::error('Sponsor ID check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to verify Sponsor ID right now.',
            ], 500);
        }
    }

    // Email optional hai: khali ho to available. Sirf unique check, koi OTP / verification nahi.
    public function checkEmail(Request $request)
    {
        try {
            $email = strtolower(trim((string) $request->query('email')));

            if ($email === '') {
                return response()->json(['success' => true, 'available' => true]);
            }

            return response()->json([
                'success'   => true,
                'available' => !User::where('email', $email)->exists(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Email check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to check email right now.',
            ], 500);
        }
    }

    public function checkPhone(Request $request)
    {
        try {
            $phone = trim((string) $request->query('phone'));

            if ($phone === '') {
                return response()->json(['success' => true, 'available' => true]);
            }

            return response()->json([
                'success'   => true,
                'available' => !User::where('mobile', $phone)->exists(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Phone check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to check mobile number right now.',
            ], 500);
        }
    }

    /* =====================================================================
     | FORGOT PASSWORD (email OTP)
     | OTP cache me rehta hai (10 min), database me koi naya column nahi chahiye.
     | Step 1 email -> OTP, Step 2 OTP check -> update-password page, Step 3 save.
     ===================================================================== */

    private const OTP_MINUTES  = 10;
    private const OTP_ATTEMPTS = 5;
    private const RESEND_AFTER = 30; // seconds

    public function forgot_password()
    {
        return view('front.auth.forgot-password');
    }

    public function check_forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'Please enter your email.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first('email')], 422);
        }

        $email = strtolower(trim($request->email));
        $user  = User::where('email', $email)->where('status', 1)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No active account found with this email.',
            ], 422);
        }

        $token = Str::random(48);

        if (!$this->sendForgotOtp($token, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send OTP right now. Please try again later.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'token'   => $token,
            'email'   => $this->maskEmail($user->email),
            'message' => 'OTP sent to your email.',
        ]);
    }

    public function resend_forgot_otp(Request $request)
    {
        $data = $this->forgotData($request->input('token'));

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please start again.'], 422);
        }

        if (now()->timestamp - $data['sent_at'] < self::RESEND_AFTER) {
            return response()->json(['success' => false, 'message' => 'Please wait a few seconds before resending.'], 429);
        }

        $user = User::where('id', $data['user_id'])->where('status', 1)->first();

        if (!$user || !$this->sendForgotOtp($request->input('token'), $user)) {
            return response()->json(['success' => false, 'message' => 'Could not resend OTP.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'A new OTP has been sent to your email.']);
    }

    public function check_forgot_otp(Request $request)
    {
        $token = (string) $request->input('token');
        $otp   = preg_replace('/\D/', '', (string) $request->input('otp'));
        $data  = $this->forgotData($token);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'OTP expired. Please request a new one.'], 422);
        }

        if ($data['attempts'] >= self::OTP_ATTEMPTS) {
            Cache::forget($this->forgotKey($token));

            return response()->json(['success' => false, 'message' => 'Too many wrong attempts. Please request a new OTP.'], 429);
        }

        if (strlen($otp) !== 6 || !Hash::check($otp, $data['otp'])) {
            $data['attempts']++;
            Cache::put($this->forgotKey($token), $data, now()->addMinutes(self::OTP_MINUTES));

            return response()->json(['success' => false, 'message' => 'Invalid OTP. Please try again.'], 422);
        }

        Cache::forget($this->forgotKey($token));
        $request->session()->put('forgot_user_id', $data['user_id']);

        return response()->json([
            'success'     => true,
            'message'     => 'OTP verified. Please set your new password.',
            'redirectUrl' => route('update.password', encrypt($data['user_id'])),
        ]);
    }

    public function update_password(Request $request, $encryptedUserId)
    {
        try {
            $userId = decrypt($encryptedUserId);
        } catch (\Throwable $e) {
            $userId = null;
        }

        // Sirf wahi user jisne abhi OTP verify kiya hai
        if (!$userId || (int) $userId !== (int) $request->session()->get('forgot_user_id')) {
            return redirect()->route('forgot.password')->with('error', 'This link has expired. Please request a new OTP.');
        }

        return view('front.auth.update-password', compact('encryptedUserId'));
    }

    public function save_password(Request $request)
    {
        $userId = $request->session()->get('forgot_user_id');

        if (!$userId) {
            return response()->json([
                'success'     => false,
                'message'     => 'Session expired. Please request a new OTP.',
                'redirectUrl' => route('forgot.password'),
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'password'         => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ], [
            'password.min'          => 'Password must be at least 6 characters.',
            'confirm_password.same' => 'Confirm password does not match.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('id', $userId)->where('status', 1)->first();

        if (!$user) {
            $request->session()->forget('forgot_user_id');

            return response()->json([
                'success'     => false,
                'message'     => 'Account not found or inactive.',
                'redirectUrl' => route('forgot.password'),
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $request->session()->forget('forgot_user_id');

        return response()->json([
            'success'     => true,
            'message'     => 'Password updated successfully. Please log in.',
            'redirectUrl' => route('login'),
        ]);
    }

    /* =====================================================================
     | HELPERS
     ===================================================================== */

    /** URL se aayi sponsor ID saaf karo (sirf letters, numbers, - aur _) */
    private function cleanSponsorId($value): string
    {
        $value = trim((string) $value);

        return preg_match('/^[A-Za-z0-9_-]{3,30}$/', $value) ? $value : '';
    }

    /** Sponsor: koi bhi active user. Naam return karta hai, na mile to null. */
    private function findSponsor(string $id): ?string
    {
        $sponsor = User::where('unique_id', $id)->where('status', 1)->first();

        return $sponsor ? trim((string) $sponsor->full_name) : null;
    }

    private function forgotKey(string $token): string
    {
        return 'forgot_otp_' . hash('sha256', $token);
    }

    private function forgotData($token): ?array
    {
        if (!is_string($token) || strlen($token) !== 48) {
            return null;
        }

        return Cache::get($this->forgotKey($token));
    }

    /** Naya OTP banao, cache me rakho (hash), email bhejo. Mail fail ho to false. */
    private function sendForgotOtp(string $token, User $user): bool
    {
        $otp = (string) random_int(100000, 999999);

        try {
            Mail::to($user->email)->send(new EmailForgotOtp($user,$otp));
        } catch (\Throwable $e) {
            Log::error('Forgot password OTP mail failed: ' . $e->getMessage());

            return false;
        }

        Cache::put($this->forgotKey($token), [
            'user_id'  => $user->id,
            'otp'      => Hash::make($otp),
            'attempts' => 0,
            'sent_at'  => now()->timestamp,
        ], now()->addMinutes(self::OTP_MINUTES));

        return true;
    }

    /** ra****@gmail.com */
    private function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $keep = min(2, strlen($name));

        return substr($name, 0, $keep) . str_repeat('*', max(strlen($name) - $keep, 3)) . '@' . $domain;
    }
}