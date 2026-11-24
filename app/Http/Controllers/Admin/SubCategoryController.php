<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Sub Category Master (admin): sirf name, image nahi.
 * Har category ki kitni sub categories hain, wo filter dropdown me count ke saath dikhta hai.
 */
class SubCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('subCategories')->orderBy('name')->get();

        return view('admin.sub_category.list', compact('categories'));
    }

    // AJAX: sirf table HTML (search + category filter + pagination)
    public function table(Request $request)
    {
        $query = SubCategory::with('category')->orderBy('id', 'DESC');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $subCategories = $query->paginate(10)->withQueryString();

        // Dropdown ke counts refresh karne ke liye
        $counts = SubCategory::selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return response()->json([
            'html'   => view('admin.sub_category.ajax_table', compact('subCategories'))->render(),
            'counts' => $counts,
            'total'  => SubCategory::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255|unique:sub_categories,name,NULL,id,category_id,' . (int) $request->category_id,
        ], [
            'name.unique' => 'This sub category already exists in the selected category.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        SubCategory::create([
            'category_id' => $request->category_id,
            'name'        => trim($request->name),
        ]);

        return response()->json(['success' => true, 'message' => 'Sub category created successfully']);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'          => 'required|exists:sub_categories,id',
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255|unique:sub_categories,name,' . (int) $request->id . ',id,category_id,' . (int) $request->category_id,
        ], [
            'name.unique' => 'This sub category already exists in the selected category.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        SubCategory::findOrFail($request->id)->update([
            'category_id' => $request->category_id,
            'name'        => trim($request->name),
        ]);

        return response()->json(['success' => true, 'message' => 'Sub category updated successfully']);
    }

    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);

        if (!$subCategory) {
            return response()->json(['success' => false, 'message' => 'Sub category not found'], 404);
        }

        $subCategory->delete();

        return response()->json(['success' => true, 'message' => 'Sub category deleted successfully']);
    }
}
