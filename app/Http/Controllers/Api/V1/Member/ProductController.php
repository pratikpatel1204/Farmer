<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductInquiry;
use App\Support\ApiData;
use App\Support\Market;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Member ke apne products (website Member\ProductMemberController jaisa).
 * Har query member_id = login user se scoped, dusre ka product kabhi nahi milta.
 */
class ProductController extends ApiController
{
    private const RULES = [
        'listing_type' => 'required|in:have,want',
        'title'        => 'required|string|max:255',
        'category'     => 'required|exists:categories,id',
        'product_tags' => 'nullable|string|max:255',
        'sub_images'   => 'nullable|array|max:4',
        'sub_images.*' => 'file|image|mimes:jpeg,png,jpg,webp|max:51200',
        'brochure'     => 'nullable|file|mimes:pdf|max:51200',
        'description'  => 'nullable|string',
        'spec_label'   => 'nullable|array',
        'spec_label.*' => 'nullable|string|max:255',
        'spec_value'   => 'nullable|array',
        'spec_value.*' => 'nullable|string|max:255',
    ];

    /** Sub category optional (I Have / I Want dono me), par selected category ki hi honi chahiye */
    private function subCategoryRule(Request $request): array
    {
        return [
            'sub_category_id' => ['nullable', Rule::exists('sub_categories', 'id')->where('category_id', (int) $request->category)],
        ];
    }

    /** GET /me/products?status=All|Pending|Approved|Rejected|Completed&type=all|have|want&search=&page= */
    public function index(Request $request)
    {
        $status = strtolower((string) $request->query('status', 'all'));
        $type   = strtolower((string) $request->query('type', 'all'));
        $search = trim((string) $request->query('search', ''));
        $like   = '%' . addcslashes($search, '%_\\') . '%';

        $products = Product::with(['categorys', 'subCategory', 'member.states', 'member.citys'])
            ->addSelect(['inquiries_count' => ProductInquiry::selectRaw('COUNT(*)')->whereColumn('product_inquiries.product_id', 'products.id')])
            ->where('member_id', $request->user()->id)
            ->when($status === 'completed', fn ($q) => $q->whereNotNull('completed_at'))
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($q) => $q->where('status', ucfirst($status))->whereNull('completed_at'))
            ->when(in_array($type, ['have', 'want'], true), fn ($q) => $q->where('listing_type', $type))
            ->when($search !== '', function ($query) use ($like) {
                $query->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                        ->orWhere('sku', 'like', $like)
                        ->orWhere('product_tags', 'like', $like)
                        ->orWhereHas('categorys', fn ($c) => $c->where('name', 'like', $like));
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return $this->paginated($products, fn ($product) => $this->row($product));
    }

    /** GET /me/products/{id}  (edit form ke liye poori detail) */
    public function show(Request $request, int $id)
    {
        $product = Product::with(['categorys', 'subCategory'])->where('member_id', $request->user()->id)->find($id);

        if (! $product) {
            return $this->fail('Product not found.', 404);
        }

        // Edit form: main_image asli value (null ho sakti hai), category image fallback nahi
        return $this->ok($this->row($product) + [
            'listing_type' => $product->listing_type,
            'category_id'  => $product->{Market::categoryKey()},
            'sub_category_id' => $product->sub_category_id,
            'sub_category'    => ApiData::subCategory($product->subCategory),
            'product_tags' => $product->product_tags,
            'description'  => $product->description,
            'main_image'   => ApiData::url($product->main_image),
            'sub_images'   => ApiData::urls($product->sub_images),
            'brochure'     => ApiData::url($product->brochure),
            'attributes'   => ProductAttribute::where('product_id', $product->id)->orderBy('id')->get(['label', 'value']),
        ]);
    }

    /** POST /me/products  (multipart/form-data) */
    public function store(Request $request)
    {
        // Main image: I Have me required, I Want me optional (na ho to null)
        $this->check($request, self::RULES + $this->subCategoryRule($request) + [
            'main_image' => 'required_if:listing_type,have|nullable|file|image|mimes:jpeg,png,jpg,webp|max:51200',
        ], [
            'main_image.required_if' => 'Main image is required for I Have listing.',
        ]);

        try {
            $product = new Product();

            do {
                $sku = random_int(100000, 999999);
            } while (Product::where('sku', $sku)->exists());

            $product->sku          = $sku;
            $product->member_id    = $request->user()->id;
            $product->title        = trim($request->title);
            $product->product_tags = $request->product_tags;
            $product->category     = $request->category;
            $product->sub_category_id = $request->filled('sub_category_id') ? (int) $request->sub_category_id : null;
            $product->listing_type = $request->listing_type;
            $product->description  = $request->description;
            $product->status       = 'Approved';
            // $product->status       = 'Pending';

            if ($request->hasFile('main_image')) {
                $product->main_image = $this->storeFile($request->file('main_image'), 'uploads/product_images', 'main');
            }

            if ($request->hasFile('sub_images')) {
                $paths = [];
                foreach ($request->file('sub_images') as $image) {
                    $paths[] = $this->storeFile($image, 'uploads/product_images', 'sub');
                }
                $product->sub_images = json_encode($paths);
            }

            if ($request->hasFile('brochure')) {
                $product->brochure = $this->storeFile($request->file('brochure'), 'uploads/brochures', 'brochure');
            }

            $product->save();

            $this->saveSpecifications($request, $product);

            Notify::productAdded($product);
        } catch (\Throwable $e) {
            Log::error('API product create failed: ' . $e->getMessage());

            return $this->fail('An error occurred while creating the product. Please try again.', 500);
        }

        return $this->ok(['id' => $product->id, 'status' => 'Pending'], 'Product submitted. It will be live after admin approval.', 201);
    }

    /**
     * POST /me/products/{id}  (multipart, update)
     * main_image optional. sub_images bheje to purane sab replace (website jaisa). Brochure bheja to replace.
     * remove_brochure=1 se brochure hata sakte hain.
     */
    public function update(Request $request, int $id)
    {
        $product = Product::where('member_id', $request->user()->id)->find($id);

        if (! $product) {
            return $this->fail('Product not found.', 404);
        }

        $this->check($request, self::RULES + $this->subCategoryRule($request) + [
            'main_image'      => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:51200',
            'remove_brochure' => 'nullable|boolean',
        ]);

        // I Have me main image zaroori: pehle se image nahi hai (jaise I Want se I Have kiya) to nayi bhejni padegi
        if ($request->listing_type === 'have' && ! $request->hasFile('main_image') && empty($product->main_image)) {
            return $this->fail('Please check the highlighted fields and try again.', 422, [
                'main_image' => ['Main image is required for I Have listing.'],
            ]);
        }

        try {
            $product->title        = trim($request->title);
            $product->product_tags = $request->product_tags;
            $product->category     = $request->category;
            // Purana app version field nahi bhejta: tab website se set ki gayi sub category mat mitao.
            // Par category badli ho aur purani sub category nayi category ki nahi hai, to hata do.
            if ($request->has('sub_category_id')) {
                $product->sub_category_id = $request->filled('sub_category_id') ? (int) $request->sub_category_id : null;
            } elseif ($product->sub_category_id && $product->isDirty('category')) {
                $product->sub_category_id = null;
            }
            $product->listing_type = $request->listing_type;
            $product->description  = $request->description;

            if ($request->hasFile('main_image')) {
                $this->deleteFile($product->main_image);
                $product->main_image = $this->storeFile($request->file('main_image'), 'uploads/product_images', 'main');
            }

            if ($request->hasFile('sub_images')) {
                foreach (Market::imageList($product->sub_images) as $oldPath) {
                    $this->deleteFile($oldPath);
                }
                $paths = [];
                foreach ($request->file('sub_images') as $image) {
                    $paths[] = $this->storeFile($image, 'uploads/product_images', 'sub');
                }
                $product->sub_images = json_encode($paths);
            }

            if ($request->hasFile('brochure')) {
                $this->deleteFile($product->brochure);
                $product->brochure = $this->storeFile($request->file('brochure'), 'uploads/brochures', 'brochure');
            } elseif ($request->boolean('remove_brochure')) {
                $this->deleteFile($product->brochure);
                $product->brochure = null;
            }

            $product->save();

            $this->saveSpecifications($request, $product);
        } catch (\Throwable $e) {
            Log::error('API product update failed: ' . $e->getMessage());

            return $this->fail('An error occurred while updating the product. Please try again.', 500);
        }

        return $this->ok(['id' => $product->id, 'status' => $product->status], 'Product updated successfully.');
    }

    /** DELETE /me/products/{id} */
    public function destroy(Request $request, int $id)
    {
        $product = Product::where('member_id', $request->user()->id)->find($id);

        if (! $product) {
            return $this->fail('Product not found.', 404);
        }

        $this->deleteFile($product->main_image);
        foreach (Market::imageList($product->sub_images) as $path) {
            $this->deleteFile($path);
        }
        $this->deleteFile($product->brochure);

        ProductAttribute::where('product_id', $product->id)->delete();
        $product->delete();

        return $this->ok(null, 'Product deleted successfully.');
    }

    /** POST /me/products/{id}/complete { completed: 1 | 0 } */
    public function complete(Request $request, int $id)
    {
        $this->check($request, ['completed' => 'required|in:0,1']);

        $product = Product::where('member_id', $request->user()->id)->find($id);

        if (! $product) {
            return $this->fail('Product not found.', 404);
        }

        $complete = (int) $request->completed === 1;

        if ($complete && $product->status !== 'Approved') {
            return $this->fail('Only approved products can be marked as completed.', 422);
        }

        $product->completed_at = $complete ? now() : null;
        $product->save();

        return $this->ok(
            ['completed' => $complete, 'completed_at' => ApiData::date($product->completed_at)],
            $complete
                ? 'Product marked as completed. It is now hidden from the website.'
                : 'Product reopened. It is visible on the website again.'
        );
    }

    /* ---------------- helpers ---------------- */

    private function row(Product $product): array
    {
        return ApiData::productCard($product) + [
            'sku'              => $product->sku,
            'status'           => $product->completed_at ? 'Completed' : $product->status,
            'approval_status'  => $product->status,
            'completed'        => (bool) $product->completed_at,
            'completed_at'     => ApiData::date($product->completed_at),
            'rejection_reason' => $product->status === 'Rejected' ? $product->rejection_reason : null,
            'inquiries_count'  => (int) ($product->inquiries_count ?? ProductInquiry::where('product_id', $product->id)->count()),
            'view_url'         => $product->status === 'Approved' && ! $product->completed_at
                ? ApiData::webUrl('product/' . ($product->slug ?: $product->id))
                : null,
        ];
    }

    /** Website wala hi tareeka: label same ho to update, naya ho to add, hata diya to delete */
    private function saveSpecifications(Request $request, Product $product): void
    {
        $labels = $request->input('spec_label', []);
        $values = $request->input('spec_value', []);

        $existing  = ProductAttribute::where('product_id', $product->id)->get()->keyBy('label');
        $processed = [];

        foreach ((array) $labels as $index => $label) {
            $label = trim((string) $label);
            $value = trim((string) ($values[$index] ?? ''));

            if ($label === '' || $value === '') {
                continue;
            }

            $processed[] = $label;

            if ($existing->has($label)) {
                $attribute = $existing->get($label);
                $attribute->value = $value;
                $attribute->save();
            } else {
                ProductAttribute::create(['product_id' => $product->id, 'label' => $label, 'value' => $value]);
            }
        }

        foreach ($existing as $attribute) {
            if (! in_array($attribute->label, $processed, true)) {
                $attribute->delete();
            }
        }
    }

    private function storeFile($file, string $folder, string $prefix): string
    {
        $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
        $path     = public_path($folder);

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $file->move($path, $filename);

        return $folder . '/' . $filename;
    }

    private function deleteFile(?string $relativePath): void
    {
        if ($relativePath && File::exists(public_path($relativePath))) {
            File::delete(public_path($relativePath));
        }
    }
}
