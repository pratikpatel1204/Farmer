<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductAdminController extends Controller
{
    private array $statuses = ['Pending', 'Approved', 'Rejected'];

    private function counts(): array
    {
        $c = Product::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            'Pending'  => (int) ($c['Pending'] ?? 0),
            'Approved' => (int) ($c['Approved'] ?? 0),
            'Rejected' => (int) ($c['Rejected'] ?? 0),
        ];
    }

    // Main page (tabs + empty container, table is loaded via AJAX)
    public function admin_product_list()
    {
        return view('admin.product.list', ['counts' => $this->counts()]);
    }

    // AJAX: renders the table blade and returns it as JSON
    public function admin_product_table(Request $request)
    {
        $status = in_array($request->status, $this->statuses) ? $request->status : 'Pending';
        $search = trim((string) $request->search);

        $products = Product::withCount('ProductAttrs')
            ->where('status', $status)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('product_tags', 'like', "%{$search}%")
                        ->orWhereHas('member', function ($m) use ($search) {
                            $m->where('full_name', 'like', "%{$search}%")
                                ->orWhere('unique_id', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withPath(route('admin.product.table'))
            ->appends(['status' => $status, 'search' => $search]);

        return response()->json([
            'html'   => view('admin.product.table', compact('products', 'status'))->render(),
            'counts' => $this->counts(),
        ]);
    }

    public function admin_product_approve(Request $request)
    {
        $request->validate(['id' => 'required|exists:products,id']);

        // Model load kiya (pehle query update tha), taaki member ko notification ja sake
        $product = Product::findOrFail($request->id);
        $product->status           = 'Approved';
        $product->rejection_reason = null;
        $product->save();

        // Member ko "Product Approved"
        Notify::productStatus($product, 'Approved');

        return response()->json(['success' => true, 'message' => 'Product approved successfully.']);
    }

    public function admin_product_reject(Request $request)
    {
        $request->validate([
            'id'               => 'required|exists:products,id',
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'Rejection reason is required.',
        ]);

        $product = Product::findOrFail($request->id);
        $product->status           = 'Rejected';
        $product->rejection_reason = trim($request->rejection_reason);
        $product->save();

        // Member ko "Product Rejected" + reason
        Notify::productStatus($product, 'Rejected', $product->rejection_reason);

        return response()->json(['success' => true, 'message' => 'Product rejected successfully.']);
    }

    public function admin_product_delete(Request $request)
    {
        $request->validate(['id' => 'required|exists:products,id']);

        $product = Product::findOrFail($request->id);

        // delete files
        foreach (array_filter([$product->main_image, $product->brochure]) as $file) {
            File::delete(public_path($file));
        }
        foreach (($product->sub_images ?? []) as $file) {
            File::delete(public_path($file));
        }

        $product->attributesList()->delete();
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
    }

    public function admin_view_product($id)
    {
        $product = Product::with('attributesList')->findOrFail($id);

        return view('admin.product.view', compact('product'));
    }
}