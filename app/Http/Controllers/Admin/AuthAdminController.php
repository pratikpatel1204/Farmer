<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthAdminController extends Controller
{
    public function login_form()
    {
        return view('admin.auth.login');
    }

    public function login_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:6'],
        ], [
            'email.required'    => 'Email is required',
            'email.email'       => 'Enter a valid email address',
            'password.required' => 'Password is required',
            'password.min'      => 'Password must be at least 6 characters',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return response()->json([
                'status'  => true,
                'message' => 'Login Successful',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Invalid email or password',
        ], 401);
    }

    public function admin_profile()
    {
        $states = State::orderBy('name')->get();
        return view('admin.auth.profile', compact('states'));
    }

    public function admin_profile_update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        // ---------- Change Password ----------
        if ($request->input('form_type') === 'password') {
            $validator = Validator::make($request->all(), [
                'current_password' => ['required', 'string'],
                'new_password'     => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'errors' => ['current_password' => ['Current password is incorrect.']],
                ], 422);
            }

            $user->password = Hash::make($request->new_password);
            $user->temp_password = null; // plain-text password column clear kar diya
            $user->save();

            return response()->json([
                'status'  => true,
                'message' => 'Password updated successfully.',
            ]);
        }

        // ---------- Profile Details ----------
        $validator = Validator::make($request->all(), [
            'full_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile'        => ['required', 'digits:10', Rule::unique('users', 'mobile')->ignore($user->id)],
            'state'         => ['nullable', 'string', 'max:100'],
            'city'          => ['nullable', 'string', 'max:100'],
            'area'          => ['nullable', 'string', 'max:255'],
            'pincode'       => ['nullable', 'digits:6'],
            'address'       => ['nullable', 'string', 'max:1000'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->full_name = $request->full_name;
        $user->email     = $request->email;
        $user->mobile    = $request->mobile;
        $user->state     = $request->state;
        $user->city      = $request->city;
        $user->area      = $request->area;
        $user->pincode   = $request->pincode;
        $user->address   = $request->address;

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                @unlink(public_path($user->profile_image));
            }

            $uploadPath = public_path('uploads/profile');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $photo    = $request->file('profile_image');
            $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move($uploadPath, $fileName);
            $user->profile_image = 'uploads/profile/' . $fileName;
        }

        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully.',
        ]);
    }
    
    public function seller_login($unique_id)
    {
        $user = User::where('unique_id', $unique_id)->first();
        if ($user) {
            Auth::guard('web')->login($user);
            return redirect()->route('seller.dashboard')->with('message', 'You are now logged in as the business!');
        } else {
            return redirect()->back()->withErrors(['email' => 'Business not found with the provided email.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
