@extends('front.layout.app')
@section('title', 'Blog - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
@endpush

@php
    // Pehle page par (bina search) sabse naya post bada dikhega
    $showFeatured = $blogs->onFirstPage() && !request('q') && $blogs->count() > 2;
    $featured = $showFeatured ? $blogs->first() : null;
@endphp

@section('content')
<div class="inner-page">

    @include('front.partials.page-hero', [
        'title' => request('q') ? 'Articles about "' . \Illuminate\Support\Str::limit(request('q'), 40) . '"' : 'Farming tips & market news',
        'text' => 'Easy reads on weather, crop care, mandi prices and new farming techniques.',
        'badge' => ['icon' => 'fa-newspaper', 'label' => 'Blog'],
        'variant' => 'have',
        'art' => 'fa-book-open',
        'crumbs' => ['Blog' => null],
    ])

    <section class="page-section">
        <div class="container-xl">
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    @if ($blogs->count())
                        @if ($featured)
                            @php $featuredUrl = url('blog/' . ($featured->slug ?? $featured->id)); @endphp
                            <article class="blog-featured">
                                <a href="{{ $featuredUrl }}" class="blog-thumb tint-1">
                                    @if (!empty($featured->image))
                                        <img src="{{ asset($featured->image) }}" alt="{{ $featured->title }}">
                                    @else
                                        <i class="fa-solid fa-seedling thumb-icon" aria-hidden="true"></i>
                                    @endif
                                    <span class="blog-date">{{ \Carbon\Carbon::parse($featured->created_at ?? now())->format('d M Y') }}</span>
                                </a>
                                <div class="blog-body">
                                    <span class="featured-label"><i class="fa-solid fa-star"></i>Latest</span>
                                    <h2 class="blog-title"><a href="{{ $featuredUrl }}">{{ $featured->title }}</a></h2>
                                    <p class="blog-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags(preg_replace('#</(p|h[1-6]|li|div)>#i', ' ', $featured->description ?? '')), 180) }}</p>
                                    <a href="{{ $featuredUrl }}" class="blog-more">Read article <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                </div>
                            </article>
                        @endif

                        <div class="row row-cols-1 row-cols-sm-2 g-3 blog-grid">
                            @foreach ($blogs as $blog)
                                @continue($featured && $loop->first)
                                <div class="col">
                                    @include('front.partials.blog-card', ['blog' => $blog, 'index' => $loop->index])
                                </div>
                            @endforeach
                        </div>

                        @if ($blogs->hasPages())
                            <div class="farm-pagination">{{ $blogs->links('pagination::bootstrap-5') }}</div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fa-solid fa-newspaper"></i>
                            <h3>{{ request('q') ? 'No articles found' : 'New articles are coming soon' }}</h3>
                            <p>{{ request('q') ? 'Try a different search word.' : 'We are writing useful farming tips for you. Please check back soon.' }}</p>
                            @if (request('q'))<a href="{{ url('blog') }}" class="btn btn-farm-outline">See all articles</a>@endif
                        </div>
                    @endif
                </div>

                <aside class="col-12 col-lg-4">
                    @include('front.blog._sidebar', ['recent' => $recent])
                </aside>
            </div>
        </div>
    </section>
</div>
@endsection
