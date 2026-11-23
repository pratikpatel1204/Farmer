<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PopupController extends Controller
{
    public function index()
    {
        return view('admin.popup.popup-list');
    }

    public function table(Request $request)
    {
        $popups = Popup::query()
            ->when($request->search, function ($q, $search) {
                $q->where('title', 'like', '%' . $search . '%');
            })
            ->latest('id')
            ->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json([
            'html' => view('admin.popup.popup-table', compact('popups'))->render(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required|string|max:150',
            'short_description' => 'required|string|max:150',
            'btn_url'           => 'required|url|max:255',
            'image'             => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'            => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['title', 'short_description', 'btn_url', 'status']);
        $data['image'] = $this->uploadImage($request->file('image'));

        Popup::create($data);

        return response()->json(['status' => true, 'message' => 'Popup added successfully']);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'                => 'required|exists:popup_master,id',
            'title'             => 'required|string|max:150',
            'short_description' => 'required|string|max:150',
            'btn_url'           => 'required|url|max:255',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'            => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $popup = Popup::findOrFail($request->id);
        $popup->title = $request->title;
        $popup->short_description = $request->short_description;
        $popup->btn_url = $request->btn_url;
        $popup->status = $request->status;

        if ($request->hasFile('image')) {
            $this->deleteImage($popup->image);
            $popup->image = $this->uploadImage($request->file('image'));
        }

        $popup->save();

        return response()->json(['status' => true, 'message' => 'Popup updated successfully']);
    }

    public function destroy($id)
    {
        $popup = Popup::find($id);

        if (!$popup) {
            return response()->json(['success' => false, 'message' => 'Popup not found']);
        }

        $this->deleteImage($popup->image);
        $popup->delete();

        return response()->json(['success' => true, 'message' => 'Popup deleted successfully']);
    }

    /* ---------- helpers ---------- */

    private function uploadImage($file): string
    {
        $filename = 'popup_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destination = public_path('uploads/popups');

        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return 'uploads/popups/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
