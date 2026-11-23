<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogs = Blog::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status === 'published');
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        $blog = new Blog([
            'author_name' => 'KisanMandi Team',
            'status'      => true,
        ]);

        return view('admin.blog.form', compact('blog'));
    }

    public function store(Request $request)
    {
        return $this->save($request, new Blog());
    }

    public function edit(Blog $blog)
    {
        return view('admin.blog.form', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        return $this->save($request, $blog);
    }

    public function destroy(Blog $blog)
    {
        $blog->delete(); // soft delete, the image file is kept

        return response()->json([
            'status'  => true,
            'message' => 'Blog deleted successfully.',
        ]);
    }

    public function toggleStatus(Blog $blog)
    {
        $blog->status = !$blog->status;
        if ($blog->status && !$blog->published_at) {
            $blog->published_at = now();
        }
        $blog->save();

        return response()->json([
            'status'  => true,
            'message' => $blog->status ? 'Blog published.' : 'Blog moved to draft.',
        ]);
    }

    /* ------------------------------------------------------------------ */

    private function save(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(), [
            'title'             => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['required', 'string'],
            'author_name'       => ['nullable', 'string', 'max:100'],
            'meta_title'        => ['nullable', 'string', 'max:255'],
            'meta_description'  => ['nullable', 'string', 'max:500'],
            'published_at'      => ['nullable', 'date'],
            'status'            => ['required', 'in:0,1'],
            'is_featured'       => ['nullable', 'in:0,1'],
            'image'             => [$blog->exists ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'title.required'       => 'Please enter the blog title.',
            'description.required' => 'Please enter the blog description.',
            'image.required'       => 'Please upload a blog image.',
            'image.image'          => 'The file must be an image.',
            'image.mimes'          => 'The image must be a JPG, JPEG, PNG, or WEBP file.',
            'image.max'            => 'The image size must not exceed 2 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $isNew = !$blog->exists;

        $blog->title             = $request->title;
        $blog->short_description = $request->short_description;
        $blog->description       = $request->description;
        $blog->author_name       = $request->author_name ?: 'KisanMandi Team';
        $blog->meta_title        = $request->meta_title;
        $blog->meta_description  = $request->meta_description;
        $blog->status            = (bool) $request->status;
        $blog->is_featured       = $request->boolean('is_featured');
        $blog->published_at      = $request->published_at
            ?: ($blog->published_at ?? ($blog->status ? now() : null));

        // Slug is auto-generated from the title on create only.
        // On edit it stays unchanged so existing URLs keep working.
        if ($isNew) {
            $blog->slug       = Blog::makeUniqueSlug($request->title);
            $blog->created_by = Auth::id();
        }

        if ($request->hasFile('image')) {
            if ($blog->image && file_exists(public_path($blog->image))) {
                @unlink(public_path($blog->image));
            }

            $uploadPath = public_path('uploads/blog');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file     = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $blog->image = 'uploads/blog/' . $fileName;
        }

        $blog->save();

        return response()->json([
            'status'   => true,
            'message'  => $isNew ? 'Blog created successfully.' : 'Blog updated successfully.',
            'redirect' => route('admin.blog.index'),
        ]);
    }
}
