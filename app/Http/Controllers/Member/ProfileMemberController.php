<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileMemberController extends Controller
{
    public function profile()
    {
        $user    = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        return view('member.profile', compact('user', 'profile'));
    }

    public function personal_info_update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validator = Validator::make($request->all(), [
            'full_name'     => ['nullable', 'string', 'max:255'],
            'mobile'        => ['nullable', 'digits:10', 'regex:/^[6-9]\d{9}$/', Rule::unique('users', 'mobile')->ignore($user->id)],
            'email'         => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'      => ['nullable', 'min:6', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->filled('full_name')) {
            $user->full_name = trim($request->full_name);
        }

        // Mobile aur email ek baar set hone ke baad form me locked hain, to yahan bhi sirf khali hone par set hote hain
        if ($request->filled('mobile') && empty($user->mobile)) {
            $user->mobile = $request->mobile;
        }

        if ($request->filled('email') && empty($user->email)) {
            $user->email = strtolower(trim($request->email));
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('profile_image')) {
            $image      = $request->file('profile_image');
            $uploadPath = public_path('uploads/profile');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            if (!empty($user->profile_image) && File::exists(public_path($user->profile_image))) {
                File::delete(public_path($user->profile_image));
            }

            $imageName = 'profile_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $imageName);
            $user->profile_image = 'uploads/profile/' . $imageName;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ]);
    }

    public function about_update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'description'         => 'nullable|string',
            'establishment_year'  => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'number_of_workers'   => 'nullable|integer|min:0',
            'area_in_square_yard' => 'nullable|numeric|min:0',
            'website'             => 'nullable|url|max:255',
            'youtube'             => 'nullable|url|max:255',
            'facebook'            => 'nullable|url|max:255',
            'instagram'           => 'nullable|url|max:255',
            'linkedin'            => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'description'         => $request->description,
                'establishment_year'  => $request->establishment_year,
                'number_of_workers'   => $request->number_of_workers,
                'area_in_square_yard' => $request->area_in_square_yard,
                'website'             => $request->website,
                'youtube'             => $request->youtube,
                'facebook'            => $request->facebook,
                'instagram'           => $request->instagram,
                'linkedin'            => $request->linkedin,
                'status'              => 'Approved',
                // 'status'              => 'Pending',
                'reject_reason'       => null,
            ]
        );

        // Admin ko "Profile waiting for approval"
        Notify::profileSubmitted($user);

        return response()->json([
            'success' => true,
            'message' => 'About updated successfully!',
        ]);
    }

    public function kyc_update(Request $request)
    {
        $user    = Auth::user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        // IFSC / PAN hamesha capital me
        $request->merge([
            'ifsc_code'  => $request->filled('ifsc_code') ? strtoupper(trim($request->ifsc_code)) : null,
            'pan_number' => $request->filled('pan_number') ? strtoupper(trim($request->pan_number)) : null,
        ]);

        $validator = Validator::make($request->all(), [
            'bank_name'          => 'nullable|string|max:255',
            'account_number'     => 'nullable|numeric|digits_between:9,18',
            'branch_name'        => 'nullable|string|max:255',
            'ifsc_code'          => 'nullable|string|max:11|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            'aadhar_number'      => 'nullable|digits:12',
            'aadhar_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pan_number'         => 'nullable|string|max:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
            'personal_pan_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = [];

        // Ek baar submit hone ke baad ye fields badle nahi ja sakte
        foreach (['bank_name', 'account_number', 'branch_name', 'ifsc_code', 'aadhar_number', 'pan_number'] as $field) {
            if (empty($profile->$field) && $request->filled($field)) {
                $data[$field] = $request->$field;
            }
        }

        $kycPath = public_path('uploads/kyc');
        if (!file_exists($kycPath)) {
            mkdir($kycPath, 0755, true);
        }

        foreach (['aadhar_image', 'personal_pan_image'] as $field) {
            if (empty($profile->$field) && $request->hasFile($field)) {
                $file     = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($kycPath, $filename);
                $data[$field] = 'uploads/kyc/' . $filename;
            }
        }

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'KYC details are already submitted and cannot be changed.',
            ], 422);
        }

        // $data['status']        = 'Pending';
        $data['status']        = 'Approved';
        $data['reject_reason'] = null;

        $profile->update($data);

        // Admin ko "Profile waiting for approval"
        Notify::profileSubmitted($user);

        return response()->json([
            'success' => true,
            'message' => 'KYC details updated successfully!',
        ]);
    }

    public function images_update(Request $request)
    {
        $user    = Auth::user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'images'   => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $uploadPath = public_path('uploads/company-images');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // DB column pehle jaisa hi (company_images) rakha hai
        $existing = json_decode($profile->company_images ?? '[]', true) ?? [];

        foreach ($request->file('images') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $existing[] = 'uploads/company-images/' . $filename;
        }

        $profile->update([
            'company_images' => json_encode($existing),
            'status'         => 'Approved',
            // 'status'         => 'Pending',
            'reject_reason'  => null,
        ]);

        // Admin ko "Profile waiting for approval"
        Notify::profileSubmitted($user);

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully!',
        ]);
    }

    public function video_update(Request $request)
    {
        $user    = Auth::user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimes:mp4,mov,avi|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $uploadPath = public_path('uploads/company-video');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Purana video hai to hata do
        if (!empty($profile->company_video) && File::exists(public_path($profile->company_video))) {
            File::delete(public_path($profile->company_video));
        }

        $file     = $request->file('video');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        // DB column pehle jaisa hi (company_video) rakha hai
        $profile->update([
            'company_video' => 'uploads/company-video/' . $filename,
            'status'        => 'Approved',
            // 'status'        => 'Pending',
            'reject_reason' => null,
        ]);

        // Admin ko "Profile waiting for approval"
        Notify::profileSubmitted($user);

        return response()->json([
            'success' => true,
            'message' => 'Video uploaded successfully!',
        ]);
    }

    public function delete_image(Request $request)
    {
        $request->validate([
            'type'  => 'required|string|in:images,video',
            'image' => 'required|string',
        ]);

        $profile = Profile::where('user_id', Auth::id())->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found.',
            ], 404);
        }

        if ($request->type === 'images') {
            $images = json_decode($profile->company_images ?? '[]', true) ?? [];

            // Pehle ownership check, tabhi file ko haath lagao
            if (!in_array($request->image, $images, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image does not belong to this profile.',
                ], 422);
            }

            $filePath = public_path($request->image);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $profile->company_images = json_encode(
                array_values(array_filter($images, fn($img) => $img !== $request->image))
            );
            $profile->save();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.',
            ]);
        }

        // video
        if ($profile->company_video !== $request->image) {
            return response()->json([
                'success' => false,
                'message' => 'Video does not belong to this profile.',
            ], 422);
        }

        $filePath = public_path($profile->company_video);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $profile->company_video = null;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully.',
        ]);
    }
}