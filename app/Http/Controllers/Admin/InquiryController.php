<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Models\ProductInquiry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /*
    |--------------------------------------------------------------------------
    | Contact Us Inquiries
    |--------------------------------------------------------------------------
    */
    public function contactList(Request $request)
    {
        $this->validateFilters($request, ['subject' => 'nullable|string|max:150']);

        $query = ContactInquiry::query();

        if ($search = trim((string) $request->search)) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('subject', 'like', $like)
                    ->orWhere('message', 'like', $like);
            });
        }

        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        $this->applyCommonFilters($query, $request);

        $inquiries = $query->latest()->paginate($this->perPage($request))->withQueryString();
        $counts    = $this->counts(ContactInquiry::query());

        if ($request->ajax()) {
            return $this->ajaxResponse('admin.inquiries.partials.contact_rows', $inquiries, $counts);
        }

        $subjects = ContactInquiry::query()
            ->whereNotNull('subject')->where('subject', '!=', '')
            ->distinct()->orderBy('subject')->pluck('subject');

        return view('admin.inquiries.contact_list', compact('inquiries', 'counts', 'subjects'));
    }

    public function contactStatusUpdate(Request $request)
    {
        return $this->updateStatus($request, ContactInquiry::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Product Inquiries (all members)
    |--------------------------------------------------------------------------
    */
    public function productList(Request $request)
    {
        $this->validateFilters($request);

        $query = ProductInquiry::with(['product', 'member']);

        if ($search = trim((string) $request->search)) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('message', 'like', $like)
                    ->orWhereHas('product', fn ($p) => $p->where('title', 'like', $like))
                    ->orWhereHas('member', fn ($m) => $m->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like));
            });
        }

        $this->applyCommonFilters($query, $request);

        $inquiries = $query->latest()->paginate($this->perPage($request))->withQueryString();
        $counts    = $this->counts(ProductInquiry::query());

        if ($request->ajax()) {
            return $this->ajaxResponse('admin.inquiries.partials.product_rows', $inquiries, $counts);
        }

        return view('admin.inquiries.product_list', compact('inquiries', 'counts'));
    }

    public function productStatusUpdate(Request $request)
    {
        return $this->updateStatus($request, ProductInquiry::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Shared helpers
    |--------------------------------------------------------------------------
    */
    private function validateFilters(Request $request, array $extra = []): void
    {
        $request->validate(array_merge([
            'search'    => 'nullable|string|max:100',
            'status'    => 'nullable|in:0,1,2',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
            'per_page'  => 'nullable|integer',
        ], $extra));
    }

    private function applyCommonFilters(Builder $query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }

    private function perPage(Request $request): int
    {
        $perPage = (int) $request->per_page;

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 10;
    }

    private function counts(Builder $query): array
    {
        $row = $query
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

    private function ajaxResponse(string $rowsView, $inquiries, array $counts)
    {
        return response()->json([
            'rows'       => view($rowsView, compact('inquiries'))->render(),
            'pagination' => view('admin.inquiries.partials.pagination', compact('inquiries'))->render(),
            'total'      => $inquiries->total(),
            'counts'     => $counts,
        ]);
    }

    private function updateStatus(Request $request, string $modelClass)
    {
        $request->validate([
            'id'     => 'required|integer',
            'status' => 'required|in:0,1,2',
        ]);

        $inquiry = $modelClass::find($request->id);

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found.',
            ], 404);
        }

        $inquiry->status = (int) $request->status;
        $inquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'counts'  => $this->counts($modelClass::query()),
        ]);
    }
}