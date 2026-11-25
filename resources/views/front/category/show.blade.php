@php
    $look = \App\Support\Market::categoryLook($category);
    $categoryUrl = url('category/' . \App\Support\Market::slugOf($category));
    // Sub category page par sab filter / sort / search usi sub category ke URL par
    $baseUrl = $subCategory ? \App\Support\Market::subCategoryUrl($category, $subCategory) : $categoryUrl;
    $pageName = $subCategory ? $subCategory->name : $category->name;
    $keep = array_filter(['q' => request('q'), 'sort' => request('sort')]);
    $tabUrl = fn($t) => $baseUrl . (($q = http_build_query(array_filter($keep + ['type' => $t]))) ? '?' . $q : '');
    // Category page + sub categories hain: pehle sub category cards, neeche bina sub category wale products
    $showSubCards = !$subCategory && $subCategories->isNotEmpty();
    $subQuery = $type ? '?type=' . $type : '';
@endphp
@extends('front.layout.app')
@section('title', $pageName . ($subCategory ? ' - ' . $category->name : '') . ' - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/category-sub.css') }}">
@endpush

@section('content')
    <div class="inner-page">

        @include('front.partials.page-hero', [
            'title' => $pageName,
            'text' => $subCategory
                ? 'Listings in ' . $subCategory->name . ' (' . $category->name . '): ready stock from farmers and requirements from buyers.'
                : 'Everything in ' . $category->name . ': ready stock from farmers and requirements from buyers.',
            'badge' => ['icon' => $look['icon'], 'label' => $subCategory ? $category->name : 'Category'],
            'art' => $look['icon'],
            'crumbs' => $subCategory
                ? ['Category' => url('category'), $category->name => $categoryUrl, $subCategory->name => null]
                : ['Category' => url('category'), $category->name => null],
        ])

        <section class="page-section">
            <div class="container-xl">
                <div class="row g-4">

                    {{-- Sidebar --}}
                    <div class="col-lg-3 filter-col">
                        <aside class="offcanvas-lg offcanvas-start filter-sidebar" tabindex="-1" id="categoryFilters"
                            aria-labelledby="categoryFiltersLabel">
                            <div class="offcanvas-header">
                                <h2 class="offcanvas-title fs-5" id="categoryFiltersLabel">Filters</h2>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                                    data-bs-target="#categoryFilters" aria-label="Close filters"></button>
                            </div>
                            <div class="offcanvas-body flex-column">
                                <div class="filter-box">
                                    <h3 class="filter-title">Search in {{ $pageName }}</h3>
                                    <form action="{{ $baseUrl }}" method="GET" class="filter-search" role="search">
                                        @if ($type)
                                            <input type="hidden" name="type" value="{{ $type }}">
                                        @endif
                                        <input type="search" name="q" value="{{ request('q') }}"
                                            class="form-control" placeholder="Search…"
                                            aria-label="Search in {{ $pageName }}">
                                        <button type="submit" aria-label="Search"><i
                                                class="fa-solid fa-magnifying-glass"></i></button>
                                    </form>
                                </div>

                                {{-- Sub category page: isi category ki baaki sub categories --}}
                                @if ($subCategory)
                                    <div class="filter-box">
                                        <h3 class="filter-title">{{ $category->name }}</h3>
                                        <ul class="filter-list">
                                            <li>
                                                <a href="{{ $categoryUrl . $subQuery }}">
                                                    <i class="fa-solid fa-arrow-left"></i>All sub categories
                                                </a>
                                            </li>
                                            @foreach ($subCategories as $sub)
                                                <li>
                                                    <a href="{{ \App\Support\Market::subCategoryUrl($category, $sub) . $subQuery }}"
                                                        class="{{ $sub->id === $subCategory->id ? 'active' : '' }}">
                                                        <span class="category-dot dot-{{ $look['color'] }}"></span>{{ $sub->name }}
                                                        <span class="count">{{ $sub->products_count }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="filter-box">
                                    <h3 class="filter-title">All categories</h3>
                                    <ul class="filter-list">
                                        @foreach ($categories as $item)
                                            <li>
                                                <a href="{{ url('category/' . \App\Support\Market::slugOf($item)) }}"
                                                    class="{{ $item->id === $category->id ? 'active' : '' }}">
                                                    <span
                                                        class="category-dot dot-{{ \App\Support\Market::categoryLook($item)['color'] }}"></span>{{ $item->name }}
                                                    <span class="count">{{ $item->products_count }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div>

                    {{-- Sub categories + products --}}
                    <div class="col-lg-9">

                        {{-- 1) Category page: pehle sub categories --}}
                        @if ($showSubCards)
                            <div class="sub-cat-section">
                                <div class="sub-cat-head">
                                    <h2 class="sub-cat-title"><i class="fa-solid fa-layer-group"></i>Sub categories in
                                        {{ $category->name }}</h2>
                                    <span class="sub-cat-total">{{ $subCategories->count() }}</span>
                                </div>
                                <div class="sub-cat-grid">
                                    @foreach ($subCategories as $sub)
                                        <a href="{{ \App\Support\Market::subCategoryUrl($category, $sub) . $subQuery }}"
                                            class="sub-cat-card tint-{{ $look['color'] }} {{ $sub->products_count ? '' : 'is-empty' }}">
                                            <span class="sub-cat-icon" aria-hidden="true"><i
                                                    class="fa-solid {{ $look['icon'] }}"></i></span>
                                            <span class="sub-cat-body">
                                                <span class="sub-cat-name">{{ $sub->name }}</span>
                                                <span class="sub-cat-count">
                                                    {{ $sub->products_count }}
                                                    {{ $sub->products_count === 1 ? 'listing' : 'listings' }}
                                                </span>
                                            </span>
                                            <i class="fa-solid fa-chevron-right sub-cat-arrow" aria-hidden="true"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 2) Products: sub category page = uske products, category page = bina sub category wale --}}
                        @if ($showSubCards)
                            <h2 class="sub-cat-title mb-3"><i class="fa-solid {{ $look['icon'] }}"></i>Other listings in
                                {{ $category->name }}</h2>
                        @endif

                        <div class="listing-toolbar">
                            <nav class="type-tabs" aria-label="Listing type">
                                <a href="{{ $tabUrl(null) }}" class="{{ $type ? '' : 'active' }}">All</a>
                                <a href="{{ $tabUrl('have') }}" class="{{ $type === 'have' ? 'active' : '' }}"><i
                                        class="fa-solid fa-seedling"></i>People Have</a>
                                <a href="{{ $tabUrl('want') }}" class="{{ $type === 'want' ? 'active' : '' }}"><i
                                        class="fa-solid fa-cart-shopping"></i>People Want</a>
                            </nav>
                            <div class="toolbar-actions">
                                <button class="btn btn-filter d-lg-none" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#categoryFilters" aria-controls="categoryFilters">
                                    <i class="fa-solid fa-sliders me-1"></i>Filters
                                </button>
                                <form action="{{ $baseUrl }}" method="GET" class="toolbar-sort">
                                    @foreach (['type' => $type, 'q' => request('q')] as $key => $value)
                                        @if ($value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endif
                                    @endforeach
                                    <label for="sortBy" class="visually-hidden">Sort by</label>
                                    <select id="sortBy" name="sort" class="form-select" data-auto-submit>
                                        <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest first</option>
                                        <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
                                        <option value="name" @selected(request('sort') === 'name')>Name A-Z</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <p class="result-count mb-3"><strong>{{ $products->total() }}</strong>
                            {{ $products->total() === 1 ? 'listing' : 'listings' }} found</p>

                        @if ($products->count())
                            @include('front.partials.product-grid', ['products' => $products])
                        @elseif ($showSubCards)
                            {{-- Sab products sub categories me hain: upar wale cards par click karo --}}
                            <div class="empty-state empty-state-sm">
                                <i class="fa-solid fa-layer-group"></i>
                                <h3>Pick a sub category</h3>
                                <p>{{ $type === 'want' ? 'Requirements' : 'Listings' }} in {{ $category->name }} are
                                    grouped in the sub categories above.</p>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fa-solid {{ $look['icon'] }}"></i>
                                <h3>Nothing here yet</h3>
                                <p>No {{ $type === 'want' ? 'requirements' : 'listings' }} in {{ $pageName }} right
                                    now. Be the first to post one.</p>
                                <a href="{{ url('register') }}" class="btn btn-farm">Post a listing</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('front/js/pages.js') }}"></script>
@endpush
