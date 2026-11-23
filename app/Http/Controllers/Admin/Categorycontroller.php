<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.category.category-list');
    }

    public function table(Request $request)
    {
        $categories = Category::query()
            ->when($request->search, function ($q, $search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->latest('id')
            ->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json([
            'html' => view('admin.category.category-table', compact('categories'))->render(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255|unique:categories,name',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $data = ['name' => $request->name];
        $data['image'] = $this->uploadImage($request->file('image'));

        Category::create($data);

        return response()->json(['status' => true, 'message' => 'Category added successfully']);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required|exists:categories,id',
            'name'  => 'required|string|max:255|unique:categories,name,' . $request->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $category = Category::findOrFail($request->id);
        $category->name = $request->name;

        if ($request->hasFile('image')) {
            $this->deleteImage($category->image);
            $category->image = $this->uploadImage($request->file('image'));
        }

        $category->save();

        return response()->json(['status' => true, 'message' => 'Category updated successfully']);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found']);
        }

        $this->deleteImage($category->image);
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }

    /* ---------- helpers ---------- */

    private function uploadImage($file): string
    {
        $filename = 'categories_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
        $destination = public_path('uploads/categories');

        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return 'uploads/categories/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
