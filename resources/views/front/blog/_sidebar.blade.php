<div class="filter-box">
    <h3 class="filter-title">Search articles</h3>
    <form action="{{ url('blog') }}" method="GET" class="filter-search" role="search">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="e.g. soil, monsoon…" aria-label="Search articles">
        <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
</div>

@if ($recent->count())
    <div class="filter-box">
        <h3 class="filter-title">Recent posts</h3>
        <ul class="recent-posts">
            @foreach ($recent as $post)
                <li>
                    <a href="{{ url('blog/' . ($post->slug ?? $post->id)) }}">
                        <span class="recent-thumb">
                            @if (!empty($post->image))
                                <img src="{{ asset($post->image) }}" alt="" loading="lazy">
                            @else
                                <i class="fa-solid fa-leaf"></i>
                            @endif
                        </span>
                        <span>
                            <strong>{{ $post->title }}</strong>
                            <small>{{ \Carbon\Carbon::parse($post->created_at ?? now())->format('d M Y') }}</small>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="side-cta">
    <h3>Grow with KisanMandi</h3>
    <p>Sell your produce or find what you need, free of cost.</p>
    <a href="{{ url('register') }}" class="btn btn-farm btn-farm-yellow w-100">Register free</a>
</div>
