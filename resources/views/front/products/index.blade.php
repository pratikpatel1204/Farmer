{{-- resources/views/front/products/index.blade.php
     I Want ($type = 'want'), I Have ($type = 'have') aur Search ($type = null) teeno isi view se.
     Variables: $products (paginator), $type, $profiles (sirf search page), $categories (categories table + products_count),
                $activeCategory (chuni hui category ya null), $totalCount --}}
@extends('front.layout.app')

@php
    $pages = [
        'want' => [
            'url' => url('i-want'),
            'title' => 'I Want: what buyers need',
            'text' => 'Fresh requirements posted by buyers, traders and shops. If you have it, contact them directly.',
            'badge' => ['icon' => 'fa-cart-shopping', 'label' => 'I Want'],
            'art' => 'fa-basket-shopping',
            'crumb' => 'I Want',
        ],
        'have' => [
            'url' => url('i-have'),
            'title' => 'I Have: ready stock from farmers',
            'text' => 'Crops, seeds and more listed by farmers. Pick what you like and talk to the farmer.',
            'badge' => ['icon' => 'fa-seedling', 'label' => 'I Have'],
            'art' => 'fa-wheat-awn',
            'crumb' => 'I Have',
        ],
        'search' => [
            'url' => url('search'),
            'title' => request('q') ? 'Results for "' . \Illuminate\Support\Str::limit(request('q'), 40) . '"' : 'Search products',
            'text' => 'Results from I Have and I Want listings, plus matching member profiles.',
            'badge' => ['icon' => 'fa-magnifying-glass', 'label' => 'Search'],
            'art' => 'fa-magnifying-glass',
            'crumb' => 'Search',
        ],
    ];
    $page = $pages[$type ?? 'search'];
    $listUrl = fn (array $params) => $page['url'] . (($query = http_build_query(array_filter($params))) ? '?' . $query : '');
@endphp

@section('title', $page['crumb'] . ' - KisanMandi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/search-profiles.css') }}">
@endpush

@section('content')
<div class="inner-page">

    @include('front.partials.page-hero', [
        'title' => $page['title'],
        'text' => $page['text'],
        'badge' => $page['badge'],
        'art' => $page['art'],
        'variant' => $type ?? 'green',
        'crumbs' => $activeCategory
            ? [$page['crumb'] => $page['url'], $activeCategory->name => null]
            : [$page['crumb'] => null],
    ])

    <section class="page-section">
        <div class="container-xl">
            <div class="row g-4">

                {{-- ================= Sidebar filter: desktop par left, mobile par drawer ================= --}}
                <div class="col-lg-3 filter-col">
                    <aside class="offcanvas-lg offcanvas-start filter-sidebar" tabindex="-1" id="productFilters" aria-labelledby="productFiltersLabel">
                        <div class="offcanvas-header">
                            <h2 class="offcanvas-title fs-5" id="productFiltersLabel">Filters</h2>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#productFilters" aria-label="Close filters"></button>
                        </div>
                        <div class="offcanvas-body flex-column">

                            <div class="filter-box">
                                <h3 class="filter-title">Search</h3>
                                <form action="{{ $page['url'] }}" method="GET" class="filter-search" role="search">
                                    @if ($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory->id }}">@endif
                                    @if (request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="e.g. wheat, onion…" aria-label="Search products">
                                    <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                                </form>
                            </div>

                            <div class="filter-box">
                                <h3 class="filter-title">Categories</h3>
                                <ul class="filter-list">
                                    <li>
                                        <a href="{{ $listUrl(['q' => request('q'), 'sort' => request('sort')]) }}" class="{{ $activeCategory ? '' : 'active' }}">
                                            <i class="fa-solid fa-border-all"></i>All categories
                                            <span class="count">{{ $totalCount }}</span>
                                        </a>
                                    </li>
                                    @foreach ($categories as $category)
                                        @php $look = \App\Support\Market::categoryFor($category->name); @endphp
                                        <li>
                                            <a href="{{ $listUrl(['category' => $category->id, 'q' => request('q'), 'sort' => request('sort')]) }}" class="{{ $activeCategory?->id === $category->id ? 'active' : '' }}">
                                                <span class="category-dot dot-{{ $look['color'] ?? 'green' }}"></span>{{ $category->name }}
                                                <span class="count">{{ $category->products_count }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if ($type === 'want')
                                <a href="{{ url('i-have') }}" class="switch-box switch-have">
                                    <strong><i class="fa-solid fa-seedling me-1"></i>Looking to buy?</strong>
                                    <span>See ready stock in I Have <i class="fa-solid fa-arrow-right ms-1"></i></span>
                                </a>
                            @elseif ($type === 'have')
                                <a href="{{ url('i-want') }}" class="switch-box switch-want">
                                    <strong><i class="fa-solid fa-cart-shopping me-1"></i>Want to sell?</strong>
                                    <span>See what buyers need in I Want <i class="fa-solid fa-arrow-right ms-1"></i></span>
                                </a>
                            @endif
                        </div>
                    </aside>
                </div>

                {{-- ================= Products ================= --}}
                <div class="col-lg-9">
                    <div class="listing-toolbar">
                        <p class="result-count">
                            <strong>{{ $products->total() }}</strong>
                            {{ $products->total() === 1 ? 'listing' : 'listings' }}
                            @if ($activeCategory) in {{ $activeCategory->name }} @endif
                        </p>
                        <div class="toolbar-actions">
                            <button class="btn btn-filter d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#productFilters" aria-controls="productFilters">
                                <i class="fa-solid fa-sliders me-1"></i>Filters
                            </button>
                            <form action="{{ $page['url'] }}" method="GET" class="toolbar-sort">
                                @foreach (['category' => $activeCategory?->id, 'q' => request('q')] as $key => $value)
                                    @if ($value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
                                @endforeach
                                <label for="sortBy" class="visually-hidden">Sort by</label>
                                <select id="sortBy" name="sort" class="form-select" data-auto-submit>
                                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest first</option>
                                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
                                    <option value="name" @selected(request('sort') === 'name')>Name A-Z</option>
                                </select>
                                <noscript><button type="submit" class="btn btn-filter ms-1">Go</button></noscript>
                            </form>
                        </div>
                    </div>

                    @if ($products->count())
                        @include('front.partials.product-grid', ['products' => $products, 'columns' => 'row-cols-2 row-cols-md-3'])
                    @elseif (isset($profiles) && $profiles->count())
                        <div class="empty-state">
                            <i class="fa-solid fa-user-check"></i>
                            <h3>No products found</h3>
                            <p>But we found matching profiles below.</p>
                            <a href="#profiles" class="btn btn-farm-outline">See profiles</a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa-solid fa-seedling"></i>
                            <h3>No listings found</h3>
                            <p>Try another category or search word. New listings are added every day.</p>
                            <a href="{{ $page['url'] }}" class="btn btn-farm-outline">Clear filters</a>
                        </div>
                    @endif

                    {{-- NAYA: profile name se milte hue profiles (sirf header search page par) --}}
                    @include('front.partials.profile-search-results')
                </div>
            </div>
        </div>
    </section>

    @if ($type === 'want')
        @include('front.partials.cta-band', ['title' => 'Need something from farmers?', 'text' => 'Register as a Non Farmer and post your requirement for free.'])
    @else
        @include('front.partials.cta-band', ['title' => 'Have produce to sell?', 'text' => 'Register as a Farmer and list your crop for free. Buyers will call you.'])
    @endif
</div>
@endsection

@push('scripts')
    <script src="{{ asset('front/js/pages.js') }}"></script>
@endpush
