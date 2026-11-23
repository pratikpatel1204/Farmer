<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Profile;
use App\Models\User;
use App\Support\ApiData;
use App\Support\Market;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

/**
 * Business profile (website Member\ProfileMemberController jaisa).
 * About / photos / video save hone par profile status = Pending (admin dobara approve karega).
 */
class ProfileController extends ApiController
{
    /** GET /me/profile */
    public function show(Request $request)
    {
        $user    = $request->user();
        $profile = Profile::where('user_id', $user->id)->first();

        return $this->ok([
            'personal' => [
                'full_name'     => $user->full_name,
                'mobile'        => $user->mobile,
                'email'         => $user->email,
                'image'         => ApiData::url($user->profile_image),
                // Website form jaisa: ek baar bhar diya to mobile / email locked
                'mobile_locked' => ! empty($user->mobile),
                'email_locked'  => ! empty($user->email),
            ],
            'business' => $profile ? [
                'status'              => $profile->status,
                'reject_reason'       => $profile->status === 'Rejected' ? ($profile->reject_reason ?? null) : null,
                'description_html'    => Market::safeHtml($profile->description),
                'establishment_year'  => $profile->establishment_year,
                'number_of_workers'   => $profile->number_of_workers,
                'area_in_square_yard' => $profile->area_in_square_yard,
                'website'             => $profile->website,
                'youtube'             => $profile->youtube,
                'facebook'            => $profile->facebook,
                'instagram'           => $profile->instagram,
                'linkedin'            => $profile->linkedin,
                // path = delete ke liye (DELETE /me/profile/media), url = dikhane ke liye
                'images'              => array_map(fn ($path) => ['path' => $path, 'url' => ApiData::url($path)], Market::imageList($profile->company_images)),
                'video'               => $profile->company_video ? ['path' => $profile->company_video, 'url' => ApiData::url($profile->company_video)] : null,
            ] : null,
            'public_url' => ($profile->status ?? null) === 'Approved' ? ApiData::webUrl('profile/' . $user->unique_id) : null,
        ]);
    }

    /** POST /me/profile/personal (multipart): full_name, mobile, email, password + password_confirmation, profile_image */
    public function personal(Request $request)
    {
        $user = User::findOrFail($request->user()->id);

        $this->check($request, [
            'full_name'     => ['nullable', 'string', 'max:255'],
            'mobile'        => ['nullable', 'digits:10', 'regex:/^[6-9]\d{9}$/', Rule::unique('users', 'mobile')->ignore($user->id)],
            'email'         => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'      => ['nullable', 'min:6', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->filled('full_name')) {
            $user->full_name = trim($request->full_name);
        }
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
            $image = $request->file('profile_image');
            $this->ensureDir('uploads/profile');
            $this->deleteFile($user->profile_image);

            $name = 'profile_' . $user->id . '_' . time() . '.' . strtolower($image->getClientOriginalExtension());
            $image->move(public_path('uploads/profile'), $name);
            $user->profile_image = 'uploads/profile/' . $name;
        }

        $user->save();

        return $this->ok(ApiData::me($user), 'Profile updated successfully.');
    }

    /** POST /me/profile/about */
    public function about(Request $request)
    {
        $user = $request->user();

        $this->check($request, [
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

        Profile::updateOrCreate(['user_id' => $user->id], [
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
        ]);

        Notify::profileSubmitted($user);

        return $this->ok(['status' => 'Pending'], 'About updated successfully!');
    }

    /** POST /me/profile/images (multipart): images[] */
    public function images(Request $request)
    {
        $user    = $request->user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $this->check($request, [
            'images'   => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $this->ensureDir('uploads/company-images');
        $existing = Market::imageList($profile->company_images);

        foreach ($request->file('images') as $file) {
            $name = time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
            $file->move(public_path('uploads/company-images'), $name);
            $existing[] = 'uploads/company-images/' . $name;
        }

        $profile->update([
            'company_images' => json_encode(array_values($existing)),
            'status'         => 'Approved',
            // 'status'         => 'Pending',
            'reject_reason'  => null,
        ]);

        Notify::profileSubmitted($user);

        return $this->ok([
            'images' => array_map(fn ($path) => ['path' => $path, 'url' => ApiData::url($path)], $existing),
            'status' => 'Approved',
            // 'status' => 'Pending',
        ], 'Images uploaded successfully!');
    }

    /** POST /me/profile/video (multipart): video (mp4/mov/avi, 5 MB) */
    public function video(Request $request)
    {
        $user    = $request->user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $this->check($request, ['video' => 'required|file|mimes:mp4,mov,avi|max:5120']);

        $this->ensureDir('uploads/company-video');
        $this->deleteFile($profile->company_video);

        $file = $request->file('video');
        $name = time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
        $file->move(public_path('uploads/company-video'), $name);

        $profile->update([
            'company_video' => 'uploads/company-video/' . $name,
            'status'        => 'Approved',
            // 'status'        => 'Pending',
            'reject_reason' => null,
        ]);

        Notify::profileSubmitted($user);

        return $this->ok([
            'video'  => ['path' => $profile->company_video, 'url' => ApiData::url($profile->company_video)],
            'status' => 'Approved',
            // 'status' => 'Pending',
        ], 'Video uploaded successfully!');
    }

    /** DELETE /me/profile/media { type: images|video, image: "uploads/..." (path) } */
    public function deleteMedia(Request $request)
    {
        $this->check($request, [
            'type'  => 'required|string|in:images,video',
            'image' => 'required|string',
        ]);

        $profile = Profile::where('user_id', $request->user()->id)->first();

        if (! $profile) {
            return $this->fail('Profile not found.', 404);
        }

        if ($request->type === 'images') {
            $images = Market::imageList($profile->company_images);

            if (! in_array($request->image, $images, true)) {
                return $this->fail('Image does not belong to this profile.', 422);
            }

            $this->deleteFile($request->image);
            $profile->company_images = json_encode(array_values(array_filter($images, fn ($img) => $img !== $request->image)));
            $profile->save();

            return $this->ok(null, 'Image deleted successfully.');
        }

        if ($profile->company_video !== $request->image) {
            return $this->fail('Video does not belong to this profile.', 422);
        }

        $this->deleteFile($profile->company_video);
        $profile->company_video = null;
        $profile->save();

        return $this->ok(null, 'Video deleted successfully.');
    }

    private function ensureDir(string $folder): void
    {
        if (! File::exists(public_path($folder))) {
            File::makeDirectory(public_path($folder), 0755, true);
        }
    }

    private function deleteFile(?string $relativePath): void
    {
        if ($relativePath && File::exists(public_path($relativePath))) {
            File::delete(public_path($relativePath));
        }
    }
}
