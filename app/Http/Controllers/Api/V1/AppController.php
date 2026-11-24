<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\City;
use App\Models\ContactInquiry;
use App\Models\ContactSetting;
use App\Models\State;
use App\Support\Market;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * App settings, About stats, Contact Us, State / City list.
 */
class AppController extends ApiController
{
    /** GET /app/config  (splash par ek baar) */
    public function config()
    {
        return $this->ok([
            'min_version'      => config('kisanmandi_app.min_version'),
            'store_url'        => config('kisanmandi_app.store_url'),
            'languages'        => config('kisanmandi_app.languages'),
            'default_language' => config('kisanmandi_app.default_language', 'hi'),
            'web_url'          => url('/'),
            'logo'             => asset('front/image/logo.png'),
            'contact'          => $this->contactData(),
            'per_page'         => [
                'products' => (int) config('kisanmandi.per_page.products', 12),
                'blogs'    => (int) config('kisanmandi.per_page.blogs', 9),
            ],
            'inquiry_status'   => ['0' => 'Pending', '1' => 'Contacted', '2' => 'Closed'],
        ]);
    }

    /** GET /pages/about  (website About page ke numbers) */
    public function about()
    {
        $members = fn (string $role) => DB::table('users')->where('role', $role)->where('status', 1)->whereNull('deleted_at')->count();

        // Content config/about_page.php se, ":brand" ki jagah app ka naam (RGM Mart)
        $brand = (string) config('app.name');
        $content = $this->replaceBrand(config('about_page', []), $brand);

        return $this->ok([
            // stats purane app version ke liye waise hi
            'stats' => [
                'farmers'    => $members('Farmer'),
                'buyers'     => $members('Non Farmer'),
                'products'   => Market::productQuery()->count(),
                'categories' => Market::tableCategories()->count(),
            ],
            'brand' => $brand,
        ] + $content);
    }

    /** Array ke har string me ":brand" → app ka naam */
    private function replaceBrand(array $data, string $brand): array
    {
        array_walk_recursive($data, function (&$value) use ($brand) {
            if (is_string($value)) {
                $value = str_replace(':brand', $brand, $value);
            }
        });

        return $data;
    }

    /** GET /contact */
    public function contact()
    {
        return $this->ok($this->contactData() + [
            'subjects' => config('kisanmandi_app.contact_subjects', []),
        ]);
    }

    /** POST /contact  (website ka sendContact: contact_inquiries me save + admin notification) */
    public function sendContact(Request $request)
    {
        $data = $this->check($request, [
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

        try {
            ContactInquiry::create([
                'full_name' => trim($data['name']),
                'phone'     => $data['mobile'],
                'email'     => $data['email'] ?? null,
                'subject'   => $data['subject'],
                'message'   => trim(strip_tags($data['message'])),
                'status'    => 0, // Pending
            ]);
        } catch (\Throwable $e) {
            Log::error('API contact inquiry save failed: ' . $e->getMessage());

            return $this->fail('Sorry, your message could not be sent. Please call or email us.', 500);
        }

        Notify::contactInquiry($data['name'], $data['subject']);

        return $this->ok(null, 'Thank you! Your message has been sent. Our team will contact you soon.', 201);
    }

    /** GET /states */
    public function states()
    {
        return $this->ok(State::orderBy('name')->get(['id', 'name']));
    }

    /** GET /states/{stateId}/cities */
    public function cities($stateId)
    {
        return $this->ok(City::where('state_id', $stateId)->orderBy('name')->get(['id', 'name']));
    }

    /** Contact settings (sirf bhare hue fields) */
    private function contactData(): array
    {
        $contact = ContactSetting::current();
        if (! $contact) {
            return [];
        }

        $fields = ['phone', 'phone_link', 'whatsapp_number', 'whatsapp_link', 'whatsapp_message', 'email', 'email_link', 'office_address', 'working_hours', 'map_embed_url'];
        $data = [];
        foreach ($fields as $field) {
            $value = $contact->{$field} ?? null;
            $data[$field] = ($value === null || $value === '') ? null : $value;
        }

        return $data;
    }
}
