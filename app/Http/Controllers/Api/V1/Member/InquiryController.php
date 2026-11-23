<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Product;
use App\Models\ProductInquiry;
use App\Support\ApiData;
use Illuminate\Http\Request;

/**
 * Enquiries: mere products par aayi (received, product-wise) + maine bheji (sent).
 * status: 0 Pending, 1 Contacted, 2 Closed (website jaisa)
 */
class InquiryController extends ApiController
{
    /** GET /me/inquiries?search=&status=&date_from=&date_to=&sort=latest|most|pending&page=&per_page=12|24|48 */
    public function index(Request $request)
    {
        $this->check($request, [
            'search'    => 'nullable|string|max:100',
            'status'    => 'nullable|in:0,1,2',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
            'sort'      => 'nullable|in:latest,most,pending',
        ]);

        $memberId = $request->user()->id;

        // 1) Filter se match hone wali enquiries
        $matching = ProductInquiry::query()->where('member_id', $memberId)->whereNotNull('product_id');

        if ($search = trim((string) $request->search)) {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $matching->where(function ($q) use ($like) {
                $q->where('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('message', 'like', $like)
                    ->orWhereIn('product_id', Product::select('id')->where(fn ($p) => $p->where('title', 'like', $like)->orWhere('sku', 'like', $like)));
            });
        }
        if ($request->filled('status')) {
            $matching->where('status', (int) $request->status);
        }
        if ($request->filled('date_from')) {
            $matching->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $matching->whereDate('created_at', '<=', $request->date_to);
        }

        // 2) Product-wise group (ek product = ek card)
        $groups = ProductInquiry::query()
            ->where('member_id', $memberId)
            ->whereIn('product_id', $matching->select('product_id'))
            ->select('product_id')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_count')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as contacted_count')
            ->selectRaw('SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as closed_count')
            ->selectRaw('MAX(created_at) as last_inquiry_at')
            ->groupBy('product_id');

        match ($request->sort) {
            'most'    => $groups->orderByDesc('total_count')->orderByDesc('last_inquiry_at'),
            'pending' => $groups->orderByDesc('pending_count')->orderByDesc('last_inquiry_at'),
            default   => $groups->orderByDesc('last_inquiry_at'),
        };

        $perPage = in_array((int) $request->per_page, [12, 24, 48], true) ? (int) $request->per_page : 12;
        $groups  = $groups->paginate($perPage)->withQueryString();

        $products = Product::whereIn('id', collect($groups->items())->pluck('product_id'))->get()->keyBy('id');

        return $this->paginated($groups, fn ($group) => [
            'product'         => $this->productMini($products->get($group->product_id)),
            'total'           => (int) $group->total_count,
            'pending'         => (int) $group->pending_count,
            'contacted'       => (int) $group->contacted_count,
            'closed'          => (int) $group->closed_count,
            'last_inquiry_at' => ApiData::date($group->last_inquiry_at),
        ], ['counts' => $this->counts($memberId)]);
    }

    /** GET /me/inquiries/products/{productId}  (ek product ki saari enquiries) */
    public function byProduct(Request $request, int $productId)
    {
        $memberId = $request->user()->id;

        $inquiries = ProductInquiry::where('member_id', $memberId)
            ->where('product_id', $productId)
            ->latest()
            ->get();

        if ($inquiries->isEmpty()) {
            return $this->fail('No inquiries found for this product.', 404);
        }

        return $this->ok([
            'product'   => $this->productMini(Product::find($productId)),
            'counts'    => $this->counts($memberId, $productId),
            'inquiries' => $inquiries->map(fn ($inquiry) => ApiData::inquiry($inquiry))->values(),
        ]);
    }

    /** PATCH /me/inquiries/{id}/status { status: 0|1|2 } */
    public function status(Request $request, int $id)
    {
        $this->check($request, ['status' => 'required|in:0,1,2']);

        $memberId = $request->user()->id;
        $inquiry  = ProductInquiry::where('id', $id)->where('member_id', $memberId)->first();

        if (! $inquiry) {
            return $this->fail('Inquiry not found or unauthorized.', 404);
        }

        $inquiry->status = (int) $request->status;
        $inquiry->save();

        return $this->ok([
            'inquiry'        => ApiData::inquiry($inquiry),
            'counts'         => $this->counts($memberId),
            'product_id'     => $inquiry->product_id,
            'product_counts' => $this->counts($memberId, (int) $inquiry->product_id),
        ], 'Status updated successfully.');
    }

    /** GET /me/inquiries/sent  (maine dusron ke products par bheji) */
    public function sent(Request $request)
    {
        $inquiries = ProductInquiry::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($this->perPage($request, 15, 50))
            ->withQueryString();

        $products = Product::with(ApiData::PRODUCT_WITH)
            ->whereIn('id', collect($inquiries->items())->pluck('product_id')->filter())
            ->get()
            ->keyBy('id');

        return $this->paginated($inquiries, function ($inquiry) use ($products) {
            $product = $products->get($inquiry->product_id);
            $live    = $product && $product->status === 'Approved' && ! $product->completed_at;

            return ApiData::inquiry($inquiry) + [
                'product'      => $product ? ApiData::productCard($product) : null,
                'product_live' => $live,
            ];
        });
    }

    private function productMini($product): array
    {
        return [
            'id'    => $product->id ?? null,
            'title' => $product->title ?? 'Product removed',
            'sku'   => $product->sku ?? null,
            'slug'  => $product ? ($product->slug ?: (string) $product->id) : null,
            'image' => ApiData::url($product->main_image ?? null),
            'type'  => $product->listing_type ?? null,
        ];
    }

    private function counts(int $memberId, ?int $productId = null): array
    {
        $row = ProductInquiry::where('member_id', $memberId)
            ->when($productId !== null, fn ($q) => $q->where('product_id', $productId))
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as contacted')
            ->selectRaw('SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as closed')
            ->first();

        return [
            'total'     => (int) ($row->total ?? 0),
            'pending'   => (int) ($row->pending ?? 0),
            'contacted' => (int) ($row->contacted ?? 0),
            'closed'    => (int) ($row->closed ?? 0),
        ];
    }
}
