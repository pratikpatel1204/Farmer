<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogs = Blog::published()
            ->when($request->filled('q'), fn($q) => $q->where('title', 'like', '%' . $request->q . '%'))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        $recent = $this->recent();

        return view('front.blog.index', compact('blogs', 'recent'));
    }

    public function show(string $slug)
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        // Ek session me ek hi baar view count badhe
        $key = 'blog_viewed_' . $blog->id;
        if (!session()->has($key)) {
            $blog->increment('views');
            session()->put($key, true);
        }

        $date = $blog->published_at ?? $blog->created_at;

        $previous = Blog::published()
            ->where('id', '!=', $blog->id)
            ->whereRaw('COALESCE(published_at, created_at) < ?', [$date])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->first(['id', 'title', 'slug']);

        $next = Blog::published()
            ->where('id', '!=', $blog->id)
            ->whereRaw('COALESCE(published_at, created_at) > ?', [$date])
            ->orderByRaw('COALESCE(published_at, created_at) ASC')
            ->first(['id', 'title', 'slug']);

        $recent = $this->recent($blog->id);

        return view('front.blog.show', compact('blog', 'recent', 'previous', 'next'));
    }

    private function recent(?int $exceptId = null)
    {
        return Blog::published()
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->orderByDesc('published_at')
            ->take(5)
            ->get(['id', 'title', 'slug', 'image', 'published_at', 'created_at']);
    }
}
