<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductInquiry;
use App\Models\SubCategory;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductMemberController extends Controller
{
    public function products(Request $request)
    {
        $status = $request->get('status', 'All');
        $type   = $request->get('type', 'all');
        $search = trim((string) $request->get('search', ''));

        return view('member.products.index', compact('status', 'type', 'search'));
    }

    public function product_list(Request $request)
    {
        $status  = $request->get('status', 'All');
        $type    = strtolower($request->get('type', 'all'));
        $search  = trim((string) $request->get('search', ''));
        $perPage = 10;
        $user    = auth()->user();

        // LIKE wildcards (% _) escape karo taaki user input literal search ho
        $like = '%' . addcslashes($search, '%_\\') . '%';

        $products = Product::with(['categorys'])
            ->where('member_id', $user->id)
            // 'All' / 'all' dono par saare products dikhao
            // 'Completed' = deal poori ho gayi (completed_at bhara hai)
            ->when(strtolower($status) === 'completed', function ($query) {
                $query->whereNotNull('completed_at');
            })
            // Pending / Approved / Rejected me completed products nahi aayenge
            ->when(!in_array(strtolower($status), ['all', 'completed'], true), function ($query) use ($status) {
                $query->where('status', $status)->whereNull('completed_at');
            })
            // I Have / I Want filter
            ->when(in_array($type, ['have', 'want'], true), function ($query) use ($type) {
                $query->where('listing_type', $type);
            })
            // Search: title / sku / tags / category name
            ->when($search !== '', function ($query) use ($like) {
                $query->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                        ->orWhere('sku', 'like', $like)
                        ->orWhere('product_tags', 'like', $like)
                        ->orWhereHas('categorys', function ($c) use ($like) {
                            $c->where('name', 'like', $like);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Requested page last page se aage ho (e.g. delete ke baad) to last page dikhao
        if ($products->isEmpty() && $products->currentPage() > 1 && $products->lastPage() >= 1) {
            $request->merge(['page' => $products->lastPage()]);
            return $this->product_list($request);
        }

        return view('member.products.table', compact('products', 'status', 'type', 'search'));
    }

    public function product_create()
    {
        $categories = Category::all();
        $subCategories = $this->subCategoriesByCategory();
        return view('member.products.create', compact('categories', 'subCategories'));
    }

    public function product_store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'listing_type' => 'required|in:have,want',
            'title'        => 'required|string|max:255',
            'category'     => 'required|exists:categories,id',
            // Sub category optional (I Have / I Want dono me), par selected category ki hi honi chahiye
            'sub_category_id' => ['nullable', Rule::exists('sub_categories', 'id')->where('category_id', (int) $request->category)],
            'product_tags' => 'nullable|string|max:255',
            'main_image'   => 'required_if:listing_type,have|nullable|file|image|mimes:jpeg,png,jpg,webp|max:51200',
            'sub_images'   => 'nullable|array|max:4',
            'sub_images.*' => 'file|image|mimes:jpeg,png,jpg,webp|max:51200',
            'brochure'     => 'nullable|file|mimes:pdf|max:51200',
            'description'  => 'nullable|string',
            'spec_label'   => 'nullable|array',
            'spec_label.*' => 'nullable|string|max:255',
            'spec_value'   => 'nullable|array',
            'spec_value.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $product = new Product();

            do {
                $sku = random_int(100000, 999999);
            } while (Product::where('sku', $sku)->exists());

            $product->sku          = $sku;
            $product->member_id    = $user->id;
            $product->title        = trim($request->title);
            $product->product_tags = $request->product_tags;
            $product->category     = $request->category;
            $product->sub_category_id = $request->filled('sub_category_id') ? (int) $request->sub_category_id : null;
            $product->listing_type = $request->listing_type;
            $product->description  = $request->description;
            $product->status       = 'Approved';
            // $product->status       = 'Pending';

            if ($request->hasFile('main_image')) {
                $product->main_image = $this->storeUploadedFile($request->file('main_image'), 'uploads/product_images', 'main');
            }

            if ($request->hasFile('sub_images')) {
                $paths = [];
                foreach ($request->file('sub_images') as $image) {
                    $paths[] = $this->storeUploadedFile($image, 'uploads/product_images', 'sub');
                }
                $product->sub_images = json_encode($paths);
            }

            if ($request->hasFile('brochure')) {
                $product->brochure = $this->storeUploadedFile($request->file('brochure'), 'uploads/brochures', 'brochure');
            }

            $product->save();

            $this->saveSpecifications($request, $product);

            // Admin ko "New product for approval" notification
            Notify::productAdded($product);

            return response()->json([
                'status'  => true,
                'message' => 'Product created successfully.',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Product create failed: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'An error occurred while creating the product. Please try again.',
            ], 500);
        }
    }

    public function product_edit($id)
    {
        $user = auth()->user();
        $product = Product::where('member_id', $user->id)->findOrFail($id);
        $existingSpecs = ProductAttribute::where('product_id', $product->id)->get();
        $categories = Category::all();
        $subCategories = $this->subCategoriesByCategory();

        return view('member.products.edit', compact('product', 'existingSpecs', 'categories', 'subCategories'));
    }

    /** { category_id: [{id, name}, ...] } : form me category badalte hi sub category dropdown bharne ke liye */
    private function subCategoriesByCategory()
    {
        return SubCategory::orderBy('name')->get(['id', 'category_id', 'name'])
            ->groupBy('category_id')
            ->map(fn ($items) => $items->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values());
    }

    public function product_update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'id'           => 'required|exists:products,id',
            'listing_type' => 'required|in:have,want',
            'title'        => 'required|string|max:255',
            'category'     => 'required|exists:categories,id',
            // Sub category optional (I Have / I Want dono me), par selected category ki hi honi chahiye
            'sub_category_id' => ['nullable', Rule::exists('sub_categories', 'id')->where('category_id', (int) $request->category)],
            'product_tags' => 'nullable|string|max:255',
            'main_image'   => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:51200',
            'sub_images'   => 'nullable|array|max:4',
            'sub_images.*' => 'file|image|mimes:jpeg,png,jpg,webp|max:51200',
            'brochure'     => 'nullable|file|mimes:pdf|max:51200',
            'description'  => 'nullable|string',
            'spec_label'   => 'nullable|array',
            'spec_label.*' => 'nullable|string|max:255',
            'spec_value'   => 'nullable|array',
            'spec_value.*' => 'nullable|string|max:255',
        ]);

        // I Have me main image zaroori hai: pehle se image nahi hai (jaise I Want se I Have kiya) to nayi upload karni padegi
        $validator->after(function ($v) use ($request, $user) {
            if ($request->listing_type !== 'have' || $request->hasFile('main_image')) {
                return;
            }
            $existing = Product::where('member_id', $user->id)->whereKey($request->id)->value('main_image');
            if (empty($existing)) {
                $v->errors()->add('main_image', 'Main image is required for I Have listing.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // member_id se scope: dusre member ka product edit nahi hoga
            $product = Product::where('member_id', $user->id)->findOrFail($request->id);

            $product->title        = trim($request->title);
            $product->product_tags = $request->product_tags;
            $product->category     = $request->category;
            $product->sub_category_id = $request->filled('sub_category_id') ? (int) $request->sub_category_id : null;
            $product->listing_type = $request->listing_type;
            $product->description  = $request->description;

            if ($request->hasFile('main_image')) {
                $this->deleteFile($product->main_image);
                $product->main_image = $this->storeUploadedFile($request->file('main_image'), 'uploads/product_images', 'main');
            }

            if ($request->hasFile('sub_images')) {
                // sub_images accessor already array return karta hai
                foreach ($product->sub_images as $oldPath) {
                    $this->deleteFile($oldPath);
                }
                $paths = [];
                foreach ($request->file('sub_images') as $image) {
                    $paths[] = $this->storeUploadedFile($image, 'uploads/product_images', 'sub');
                }
                $product->sub_images = json_encode($paths);
            }

            if ($request->hasFile('brochure')) {
                $this->deleteFile($product->brochure);
                $product->brochure = $this->storeUploadedFile($request->file('brochure'), 'uploads/brochures', 'brochure');
            }

            $product->save();

            $this->saveSpecifications($request, $product);

            return response()->json([
                'status'  => true,
                'message' => 'Product updated successfully.',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Product update failed: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'An error occurred while updating the product. Please try again.',
            ], 500);
        }
    }

    public function product_delete(Request $request)
    {
        $user = auth()->user();

        $product = Product::where('member_id', $user->id)->find($request->id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $this->deleteFile($product->main_image);
        // sub_images accessor already array return karta hai
        foreach ($product->sub_images as $path) {
            $this->deleteFile($path);
        }
        $this->deleteFile($product->brochure);

        ProductAttribute::where('product_id', $product->id)->delete();
        $product->delete();

        // index blade 'response.success' check karta hai
        return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
    }

    /**
     * Deal poori hone par product "Completed" (website se hide), ya wapas "Reopen".
     * completed = 1 -> Completed, completed = 0 -> Reopen
     * Admin approval (status column) ko nahi chhedta, isliye reopen par product seedha website par wapas aa jata hai.
     */
    public function product_complete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|integer',
            'completed' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid request.'], 422);
        }

        // member_id se scope: dusre member ka product change nahi hoga
        $product = Product::where('member_id', auth()->id())->find($request->id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $complete = (int) $request->completed === 1;

        if ($complete && $product->status !== 'Approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved products can be marked as completed.',
            ], 422);
        }

        $product->completed_at = $complete ? now() : null;
        $product->save();

        return response()->json([
            'success'   => true,
            'completed' => $complete,
            'message'   => $complete
                ? 'Product marked as completed. It is now hidden from the website.'
                : 'Product reopened. It is visible on the website again.',
        ]);
    }

    private function saveSpecifications(Request $request, Product $product): void
    {
        $labels = $request->input('spec_label', []);
        $values = $request->input('spec_value', []);

        $existingAttributes = ProductAttribute::where('product_id', $product->id)->get()->keyBy('label');

        $processedLabels = [];

        foreach ($labels as $index => $label) {
            $label = trim((string) $label);
            $value = trim((string) ($values[$index] ?? ''));

            if ($label === '' || $value === '') {
                continue;
            }

            $processedLabels[] = $label;

            if ($existingAttributes->has($label)) {
                $attribute = $existingAttributes->get($label);
                $attribute->value = $value;
                $attribute->save();
            } else {
                ProductAttribute::create([
                    'product_id' => $product->id,
                    'label'      => $label,
                    'value'      => $value,
                ]);
            }
        }

        // Form se hataye gaye specs DB se bhi hata do
        foreach ($existingAttributes as $attribute) {
            if (!in_array($attribute->label, $processedLabels, true)) {
                $attribute->delete();
            }
        }
    }

    private function storeUploadedFile($file, string $folder, string $prefix): string
    {
        $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path($folder);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        return $folder . '/' . $filename;
    }

    private function deleteFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $fullPath = public_path($relativePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Product Inquiries (product-wise cards)
    |--------------------------------------------------------------------------
    | Har product ka ek card. Filters sirf ye decide karte hain ki kaunse product
    | dikhenge; card par us product ke saare inquiry counts dikhte hain.
    */
    public function products_inquiries_list(Request $request)
    {
        $request->validate([
            'search'    => 'nullable|string|max:100',
            'status'    => 'nullable|in:0,1,2',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
            'sort'      => 'nullable|in:latest,most,pending',
            'per_page'  => 'nullable|integer',
        ]);

        $memberId = auth()->id();

        // 1) Filters se match hone wali inquiries
        $matching = ProductInquiry::query()
            ->where('member_id', $memberId)
            ->whereNotNull('product_id');

        if ($search = trim((string) $request->search)) {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $matching->where(function ($q) use ($like) {
                $q->where('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('message', 'like', $like)
                    // products table me naam ka column `title` hai
                    ->orWhereHas('product', fn ($p) => $p->where('title', 'like', $like)
                        ->orWhere('sku', 'like', $like));
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

        // 3) Products ek hi query me load
        $products = Product::whereIn('id', $groups->pluck('product_id'))->get()->keyBy('id');
        $groups->getCollection()->each(fn ($g) => $g->setRelation('product', $products->get($g->product_id)));

        $counts = $this->getInquiryCounts($memberId);

        if ($request->ajax()) {
            return response()->json([
                'cards'      => view('member.products.partials.inquiries_cards', compact('groups'))->render(),
                'pagination' => view('member.products.partials.inquiries_pagination', ['inquiries' => $groups])->render(),
                'total'      => $groups->total(),
                'counts'     => $counts,
            ]);
        }

        return view('member.products.inquiries_list', compact('groups', 'counts'));
    }

    /** Ek product ki saari inquiries (modal ke liye) */
    public function products_inquiries_by_product(Request $request, $productId)
    {
        $memberId = auth()->id();

        $inquiries = ProductInquiry::where('member_id', $memberId)
            ->where('product_id', $productId)
            ->latest()
            ->get();

        if ($inquiries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No inquiries found for this product.',
            ], 404);
        }

        $product = Product::find($productId);

        return response()->json([
            'success' => true,
            'product' => [
                'id'        => $product->id ?? null,
                'completed' => (bool) ($product->completed_at ?? null),
                'status'    => $product->status ?? null,
                'title'     => $product->title ?? 'Product removed',
                'sku'       => $product->sku ?? null,
                'image'     => $product && $product->main_image ? asset($product->main_image) : null,
                'url'       => $product && $product->slug && !$product->completed_at ? route('products.show', $product->slug) : null,
            ],
            'counts' => $this->getInquiryCounts($memberId, (int) $productId),
            'html'   => view('member.products.partials.inquiry_modal_items', compact('inquiries'))->render(),
        ]);
    }

    public function products_inquiry_status_update(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer',
            'status' => 'required|in:0,1,2',
        ]);

        $memberId = auth()->id();

        $inquiry = ProductInquiry::where('id', $request->id)
            ->where('member_id', $memberId)
            ->first();

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found or unauthorized.',
            ], 404);
        }

        $inquiry->status = (int) $request->status;
        $inquiry->save();

        return response()->json([
            'success'        => true,
            'message'        => 'Status updated successfully.',
            'counts'         => $this->getInquiryCounts($memberId),
            'product_id'     => $inquiry->product_id,
            'product_counts' => $this->getInquiryCounts($memberId, $inquiry->product_id),
        ]);
    }

    private function getInquiryCounts(int $memberId, ?int $productId = null): array
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