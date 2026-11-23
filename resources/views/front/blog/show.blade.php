@extends('front.layout.app')
@section('title', ($blog->meta_title ?: $blog->title) . ' - ' . config('app.name'))
@push('meta')
    <meta name="description" content="{{ $blog->meta_description ?: $blog->excerpt }}">
    <meta property="og:title" content="{{ $blog->meta_title ?: $blog->title }}">
    <meta property="og:description" content="{{ $blog->meta_description ?: $blog->excerpt }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $blog->url }}">
    @if ($blog->image)
        <meta property="og:image" content="{{ asset($blog->image) }}">
    @endif
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
@endpush

@section('content')
    <div class="inner-page">
        <section class="post-header">
            <div class="container-xl">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <nav class="detail-breadcrumb" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('blog') }}">Blog</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ \Illuminate\Support\Str::limit($blog->title, 40) }}</li>
                            </ol>
                        </nav>

                        <article class="farm-card p-0 overflow-hidden">
                            <div class="post-body pb-0">
                                <span class="section-eyebrow"><i class="fa-solid fa-newspaper"></i>Blog</span>
                                <h1 class="post-title">{{ $blog->title }}</h1>
                                <div class="post-meta">
                                    <span><i class="fa-regular fa-calendar"></i>{{ $blog->display_date }}</span>
                                    <span><i class="fa-regular fa-clock"></i>{{ $blog->read_minutes }} min read</span>
                                    <span><i
                                            class="fa-solid fa-user-pen"></i>{{ $blog->author_name ?: 'KisanMandi Team' }}</span>
                                    <span><i class="fa-regular fa-eye"></i>{{ number_format($blog->views) }} views</span>
                                </div>
                            </div>

                            <div class="px-3 px-md-4">
                                <div class="post-cover">
                                    @if (!empty($blog->image))
                                        <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}">
                                    @else
                                        <i class="fa-solid fa-seedling thumb-icon" aria-hidden="true"></i>
                                    @endif
                                </div>
                            </div>

                            <div class="post-body pt-0">
                                {{-- Blog admin likhta hai (editor se HTML), isliye {!! !!} --}}
                                <div class="rich-text">{!! $blog->description ?? '' !!}</div>
                            </div>
                        </article>

                        @if ($previous || $next)
                            <nav class="post-nav" aria-label="More articles">
                                @if ($previous)
                                    <a href="{{ $previous->url }}" class="prev"><small><i
                                                class="fa-solid fa-arrow-left me-1"></i>Previous</small><strong>{{ $previous->title }}</strong></a>
                                @else
                                    <span></span>
                                @endif
                                @if ($next)
                                    <a href="{{ $next->url }}" class="next"><small>Next<i
                                                class="fa-solid fa-arrow-right ms-1"></i></small><strong>{{ $next->title }}</strong></a>
                                @endif
                            </nav>
                        @endif
                    </div>

                    <aside class="col-12 col-lg-4 pt-lg-5">
                        @include('front.blog._sidebar', ['recent' => $recent])
                    </aside>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('front/js/pages.js') }}"></script>
@endpush
