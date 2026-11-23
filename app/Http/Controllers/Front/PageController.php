<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Models\ContactSetting;
use App\Support\Market;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        // About page ke number (sirf COUNT query)
        $stats = [
            'farmers'    => DB::table('users')->where('role', 'Farmer')->where('status', 1)->whereNull('deleted_at')->count(),
            'buyers'     => DB::table('users')->where('role', 'Non Farmer')->where('status', 1)->whereNull('deleted_at')->count(),
            'products'   => Market::products()->count(),
            'categories' => count(Market::categories()),
        ];

        return view('front.pages.about', compact('stats'));
    }

    public function contact()
    {
        $contact = ContactSetting::current();

        return view('front.pages.contact', compact('contact'));
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'mobile'  => ['required', 'regex:/^[0-9+\s-]{10,15}$/'],
            'email'   => ['nullable', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'name.required'    => 'Please enter your full name.',
            'mobile.required'  => 'Please enter your mobile number.',
            'mobile.regex'     => 'Please enter a valid mobile number.',
            'email.email'      => 'Please enter a valid email address.',
            'subject.required' => 'Please select a subject.',
            'message.required' => 'Please write your message.',
            'message.min'      => 'Message must be at least 10 characters.',
        ]);

        // 1) Database me save
        try {
            ContactInquiry::create([
                'full_name' => $data['name'],
                'phone'     => $data['mobile'],
                'email'     => $data['email'] ?? null,
                'subject'   => $data['subject'],
                'message'   => $data['message'],
                'status'    => 0, // Pending
            ]);
        } catch (\Throwable $e) {
            Log::error('Contact inquiry save failed: ' . $e->getMessage());

            return $this->contactResponse(
                $request,
                false,
                'Sorry, your message could not be sent. Please call or email us.',
                500
            );
        }

        // 2) Admin ko "New contact message" notification
        Notify::contactInquiry($data['name'], $data['subject']);

        return $this->contactResponse(
            $request,
            true,
            'Thank you! Your message has been sent. Our team will contact you soon.'
        );
    }

    private function contactResponse(Request $request, bool $success, string $message, int $status = 200)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ], $status);
        }

        return $success
            ? back()->with('contact_success', $message)
            : back()->withInput()->with('contact_error', $message);
    }
}