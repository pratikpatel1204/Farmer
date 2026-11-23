<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Models\ProductInquiry;
use App\Support\Market;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * POST /products/{slug}/enquiry  (website ka "Contact seller" / "I can supply this" popup)
 * product_inquiries: product_id, member_id (maalik), user_id (bhejne wala), full_name, email, phone, message, status 0
 */
class EnquiryController extends ApiController
{
    public function store(Request $request, string $slug)
    {
        $user = $request->user();

        // Completed product bhi dhundo, taaki sahi message (410) de sakein
        $product = Product::query()
            ->where('status', 'Approved')
            ->whereHas('member', fn ($q) => $q->where('status', 1)->whereNull('deleted_at'))
            ->where(fn ($q) => $q->where('slug', $slug)->when(ctype_digit($slug), fn ($w) => $w->orWhere('id', (int) $slug)))
            ->first();

        if (! $product) {
            return $this->fail('This listing is no longer available.', 404);
        }

        if (! empty($product->completed_at)) {
            return $this->fail('This deal is already completed. The listing is no longer available.', 410);
        }

        if ((int) $product->member_id === (int) $user->id) {
            return $this->fail('This is your own listing.', 422);
        }

        $data = $this->check($request, [
            'full_name' => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'phone'     => ['required', 'regex:/^[6-9]\d{9}$/'],
            'message'   => 'required|string|min:10|max:1000',
        ], [
            'full_name.required' => 'Please enter your name.',
            'phone.required'     => 'Please enter your mobile number.',
            'phone.regex'        => 'Please enter a valid 10-digit mobile number.',
            'email.email'        => 'Please enter a valid email address.',
            'message.required'   => 'Please write a message.',
            'message.min'        => 'Message must be at least 10 characters.',
            'message.max'        => 'Message can be up to 1000 characters.',
        ]);

        $who = Market::typeOf($product) === 'want' ? 'buyer' : 'seller';

        $already = DB::table('product_inquiries')
            ->where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($already) {
            return $this->fail("You have already sent an enquiry for this listing. The {$who} will contact you soon.", 422);
        }

        try {
            $inquiry = ProductInquiry::create([
                'product_id' => $product->id,
                'member_id'  => $product->member_id,
                'user_id'    => $user->id,
                'full_name'  => trim($data['full_name']),
                'email'      => ! empty($data['email']) ? strtolower(trim($data['email'])) : null,
                'phone'      => $data['phone'],
                'message'    => trim(strip_tags($data['message'])),
                'status'     => '0',
            ]);
        } catch (\Throwable $e) {
            Log::error('API product enquiry failed: ' . $e->getMessage());

            return $this->fail('Could not send your enquiry. Please try again.', 500);
        }

        // Maalik ko + Admin ko notification (maalik ko app push bhi, PushOnNotification se)
        Notify::productInquiry($product, trim($data['full_name']));

        return $this->ok(['id' => $inquiry->id], "Your enquiry has been sent. The {$who} will contact you soon.", 201);
    }
}
