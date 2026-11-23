<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ProductInquiry;
use App\Support\Market;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Product detail page ka "Contact seller" / "I can supply this" enquiry form.
 * Route:
 *   Route::post('product/{slug}/enquiry', [ProductInquiryController::class, 'store'])
 *       ->middleware('throttle:5,1')->name('products.enquiry');
 *
 * product_inquiries table me ek row add hoti hai:
 *   product_id, member_id (product ka maalik), user_id (enquiry bhejne wala),
 *   full_name, email, phone, message, status
 */
class ProductInquiryController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'  => false,
                'message'  => 'Please log in first to send an enquiry.',
                'loginUrl' => route('login', ['redirect' => '/product/' . $slug]),
            ], 401);
        }

        $product = Market::productQuery()->where('slug', $slug)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'This listing is no longer available.'], 404);
        }

        // Deal complete ho chuki hai (member ne "Completed" mark kiya), ab enquiry nahi jayegi
        if (!empty($product->completed_at)) {
            return response()->json(['success' => false, 'message' => 'This deal is already completed. The listing is no longer available.'], 410);
        }

        if ((int) $product->member_id === (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'This is your own listing.'], 422);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => 'Please check the highlighted fields and try again.',
            ], 422);
        }

        $already = DB::table('product_inquiries')
            ->where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'You have already sent an enquiry for this listing. The ' . (Market::typeOf($product) === 'want' ? 'buyer' : 'seller') . ' will contact you soon.',
            ], 422);
        }

        try {
            ProductInquiry::create([
                'product_id' => $product->id,
                'member_id'  => $product->member_id,
                'user_id'    => $user->id,
                'full_name'  => trim($request->full_name),
                'email'      => $request->filled('email')
                    ? strtolower(trim($request->email))
                    : null,
                'phone'      => $request->phone,
                'message'    => trim(strip_tags($request->message)),
                'status'     => '0',
            ]);
        } catch (\Throwable $e) {
            Log::error('Product enquiry failed: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Could not send your enquiry. Please try again.'], 500);
        }

        // Product ke maalik (member) ko + Admin ko "New enquiry" notification
        Notify::productInquiry($product, trim($request->full_name));

        return response()->json([
            'success' => true,
            'message' => 'Your enquiry has been sent. The ' . (Market::typeOf($product) === 'want' ? 'buyer' : 'seller') . ' will contact you soon.',
        ]);
    }
}
