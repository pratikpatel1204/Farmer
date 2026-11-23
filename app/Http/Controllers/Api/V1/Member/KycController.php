<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Profile;
use App\Support\Notify;
use Illuminate\Http\Request;

/**
 * KYC + bank (website kyc_update jaisa). Har field ek baar bharne ke baad lock.
 * GET me sirf masked value jaati hai, KYC photo ka URL kabhi nahi.
 */
class KycController extends ApiController
{
    private const FIELDS = ['bank_name', 'account_number', 'branch_name', 'ifsc_code', 'aadhar_number', 'pan_number'];

    /** GET /me/kyc */
    public function show(Request $request)
    {
        $profile = Profile::where('user_id', $request->user()->id)->first();

        $filled = fn ($field) => ! empty($profile?->{$field});

        return $this->ok([
            'bank_name'        => $profile->bank_name ?? null,
            'account_number'   => $this->mask($profile->account_number ?? null, 4),
            'branch_name'      => $profile->branch_name ?? null,
            'ifsc_code'        => $profile->ifsc_code ?? null,
            'aadhar_number'    => $this->mask($profile->aadhar_number ?? null, 4),
            'pan_number'       => $this->mask($profile->pan_number ?? null, 4),
            'aadhar_image'     => $filled('aadhar_image'),
            'pan_image'        => $filled('personal_pan_image'),
            // kaunse field abhi bhar sakte hain (khali wale)
            'locked'           => collect(self::FIELDS)->mapWithKeys(fn ($f) => [$f => $filled($f)])
                ->merge(['aadhar_image' => $filled('aadhar_image'), 'personal_pan_image' => $filled('personal_pan_image')]),
            'fully_submitted'  => collect(self::FIELDS)->every($filled) && $filled('aadhar_image') && $filled('personal_pan_image'),
        ]);
    }

    /** POST /me/kyc (multipart) */
    public function update(Request $request)
    {
        $user    = $request->user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $request->merge([
            'ifsc_code'  => $request->filled('ifsc_code') ? strtoupper(trim($request->ifsc_code)) : null,
            'pan_number' => $request->filled('pan_number') ? strtoupper(trim($request->pan_number)) : null,
        ]);

        $this->check($request, [
            'bank_name'          => 'nullable|string|max:255',
            'account_number'     => 'nullable|numeric|digits_between:9,18',
            'branch_name'        => 'nullable|string|max:255',
            'ifsc_code'          => 'nullable|string|max:11|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            'aadhar_number'      => 'nullable|digits:12',
            'aadhar_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pan_number'         => 'nullable|string|max:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
            'personal_pan_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [];

        foreach (self::FIELDS as $field) {
            if (empty($profile->{$field}) && $request->filled($field)) {
                $data[$field] = $request->{$field};
            }
        }

        $kycPath = public_path('uploads/kyc');
        if (! file_exists($kycPath)) {
            mkdir($kycPath, 0755, true);
        }

        foreach (['aadhar_image', 'personal_pan_image'] as $field) {
            if (empty($profile->{$field}) && $request->hasFile($field)) {
                $file = $request->file($field);
                $name = time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
                $file->move($kycPath, $name);
                $data[$field] = 'uploads/kyc/' . $name;
            }
        }

        if (empty($data)) {
            return $this->fail('KYC details are already submitted and cannot be changed.', 422);
        }

        $data['status']        = 'Pending';
        $data['reject_reason'] = null;

        $profile->update($data);

        Notify::profileSubmitted($user);

        return $this->ok(null, 'KYC details updated successfully!');
    }

    private function mask($value, int $keep): ?string
    {
        $value = (string) $value;
        if ($value === '') {
            return null;
        }

        return str_repeat('X', max(strlen($value) - $keep, 0)) . substr($value, -$keep);
    }
}
