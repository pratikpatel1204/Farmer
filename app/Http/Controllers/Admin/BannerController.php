<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banner.banner-list');
    }

    public function table(Request $request)
    {
        $banners = Banner::query()
            ->when($request->search, function ($q, $search) {
                $q->where('title', 'like', '%' . $search . '%');
            })
            ->latest('id')
            ->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json([
            'html' => view('admin.banner.banner-table', compact('banners'))->render(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:100',
            'description' => 'required|string|max:150',
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['title', 'description', 'status']);
        $data['image'] = $this->uploadImage($request->file('image'));

        Banner::create($data);

        return response()->json(['status' => true, 'message' => 'Banner added successfully']);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'          => 'required|exists:banners,id',
            'title'       => 'required|string|max:100',
            'description' => 'required|string|max:150',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $banner = Banner::findOrFail($request->id);
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->status = $request->status;

        if ($request->hasFile('image')) {
            $this->deleteImage($banner->image);
            $banner->image = $this->uploadImage($request->file('image'));
        }

        $banner->save();

        return response()->json(['status' => true, 'message' => 'Banner updated successfully']);
    }

    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['success' => false, 'message' => 'Banner not found']);
        }

        $this->deleteImage($banner->image);
        $banner->delete();

        return response()->json(['success' => true, 'message' => 'Banner deleted successfully']);
    }

    /* ---------- helpers ---------- */

    private function uploadImage($file): string
    {
        $filename = 'banners_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
        $destination = public_path('uploads/banners');

        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return 'uploads/banners/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
