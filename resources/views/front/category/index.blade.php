@extends('front.layout.app')
@section('title', 'Categories - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
@endpush

@section('content')
<div class="inner-page">

    @include('front.partials.page-hero', [
        'title' => 'Shop by category',
        'text' => 'From vegetables to tractors. Pick a category to see what farmers have and what buyers want.',
        'badge' => ['icon' => 'fa-table-cells-large', 'label' => 'Categories'],
        'variant' => 'soil',
        'art' => 'fa-table-cells-large',
        'crumbs' => ['Category' => null],
    ])

    <section class="page-section">
        <div class="container-xl">
            @if ($categories->count())
                <div class="category-head">
                    <p class="category-head-text"><strong>{{ $categories->count() }}</strong> categories &middot; tap any card to see products</p>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-lg-4">
                    @foreach ($categories as $category)
                        @php
                            $look = \App\Support\Market::categoryLook($category);
                            $image = \App\Support\Market::categoryImage($category);
                            $total = $category->have_count + $category->want_count;
                        @endphp
                        <div class="col">
                            <a href="{{ url('category/' . \App\Support\Market::slugOf($category)) }}" class="cat-card">
                                <div class="cat-card-media tint-{{ $look['color'] }} {{ $image ? 'has-image' : 'no-image' }}">
                                    @if ($image)
                                        <img src="{{ $image }}" alt="{{ $category->name }}" loading="lazy"
                                             onerror="this.parentNode.classList.replace('has-image', 'no-image'); this.remove();">
                                    @endif
                                    {{-- Image na ho (ya load na ho) to icon dikhega --}}
                                    <span class="cat-card-icon" aria-hidden="true"><i class="fa-solid {{ $look['icon'] }}"></i></span>
                                    <span class="cat-card-count">{{ $total }} {{ $total === 1 ? 'listing' : 'listings' }}</span>
                                </div>
                                <div class="cat-card-body">
                                    <h2 class="cat-card-name">{{ $category->name }}</h2>
                                    <div class="cat-card-split">
                                        <span class="split-have"><i class="fa-solid fa-seedling"></i>{{ $category->have_count }} Have</span>
                                        <span class="split-want"><i class="fa-solid fa-cart-shopping"></i>{{ $category->want_count }} Want</span>
                                    </div>
                                    <span class="cat-card-cta">View products <i class="fa-solid fa-arrow-right"></i></span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <h3>No categories yet</h3>
                    <p>Categories will appear here once they are added.</p>
                </div>
            @endif
        </div>
    </section>

    @include('front.partials.cta-band', [
        'title' => 'Can\'t find your category?',
        'text' => 'Tell us what you grow or need. We add new categories every month.',
    ])
</div>
@endsection
