<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactSettingController extends Controller
{
    public function index()
    {
        $contact = ContactSetting::current();
        return view('admin.contact.index', compact('contact'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'string', 'max:20', 'regex:/^[0-9+\s\-]+$/'],
            'whatsapp_number'  => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-]+$/'],
            'whatsapp_message' => ['nullable', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:150'],
            'office_address'   => ['required', 'string', 'max:1000'],
            'working_hours'    => ['required', 'string', 'max:150'],
            'map_embed_url'    => ['nullable', 'string', 'max:5000'],
        ], [
            'phone.regex'           => 'Phone number me sirf digits, +, space aur - allowed hain.',
            'whatsapp_number.regex' => 'WhatsApp number me sirf digits, +, space aur - allowed hain.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Map: pura <iframe> paste kiya ho to usme se sirf src nikaal lo
        $mapUrl = trim((string) $request->map_embed_url);
        if ($mapUrl !== '' && preg_match('/src=["\']([^"\']+)["\']/i', $mapUrl, $m)) {
            $mapUrl = $m[1];
        }

        if ($mapUrl !== '' && !str_starts_with($mapUrl, 'https://www.google.com/maps/embed')) {
            return response()->json([
                'status' => false,
                'errors' => ['map_embed_url' => ['Google Maps ka "Embed a map" wala iframe ya link daalein.']],
            ], 422);
        }

        $contact = ContactSetting::current();
        $contact->update([
            'phone'            => $request->phone,
            'whatsapp_number'  => $request->whatsapp_number,
            'whatsapp_message' => $request->whatsapp_message,
            'email'            => $request->email,
            'office_address'   => $request->office_address,
            'working_hours'    => $request->working_hours,
            'map_embed_url'    => $mapUrl ?: null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Contact details updated successfully.',
            'map_url' => $contact->map_embed_url,
        ]);
    }
}
