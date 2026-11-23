{{-- resources/views/front/partials/blog-card.blade.php  Variables: $blog, $index (rang ke liye) --}}
@php
    $blogUrl = url('blog/' . ($blog->slug ?? $blog->id));
    $tint = 'tint-' . ((($index ?? 0) % 4) + 1);
    $blogIcons = ['fa-leaf', 'fa-cloud-sun-rain', 'fa-chart-line', 'fa-tractor'];
@endphp
<article class="blog-card">
    <a href="{{ $blogUrl }}" class="blog-thumb {{ $tint }}">
        @if (!empty($blog->image))
            <img src="{{ asset($blog->image) }}" alt="{{ $blog->title ?? '' }}" loading="lazy">
        @else
            <i class="fa-solid {{ $blogIcons[($index ?? 0) % 4] }} thumb-icon" aria-hidden="true"></i>
        @endif
        <span class="blog-date">{{ \Carbon\Carbon::parse($blog->created_at ?? now())->format('d M Y') }}</span>
    </a>
    <div class="blog-body">
        <h3 class="blog-title"><a href="{{ $blogUrl }}">{{ $blog->title ?? '' }}</a></h3>
        <p class="blog-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags(preg_replace('#</(p|h[1-6]|li|div)>#i', ' ', $blog->description ?? '')), 120) }}</p>
        <a href="{{ $blogUrl }}" class="blog-more">Read more <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
</article>
