@extends('front.layout.app')
@section('title', 'Home Page - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
@endpush
@php
    $thumbIcons = [
        ['fa-carrot', 'thumb-red'],
        ['fa-wheat-awn', 'thumb-yellow'],
        ['fa-seedling', 'thumb-lime'],
        ['fa-apple-whole', 'thumb-red'],
        ['fa-tractor', 'thumb-brown'],
        ['fa-lemon', 'thumb-yellow'],
    ];
@endphp

@section('content')
    <div class="home-page">
        @php
            $slideColors = ['slide-green', 'slide-sun', 'slide-soil'];
            $banners = $banners ?? collect();
        @endphp
        
        @include('front.partials.hero-section')
        
        {{-- ================= 2) I WANT products (auto scroll) ================= --}}
        <section class="home-section product-section section-want">
            <div class="container-fluid">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-cart-shopping"></i>I Want</span>
                        <h2 class="section-title">Buyers are looking for</h2>
                        <p class="section-subtitle">Latest requirements from buyers. If you have it, get in touch.</p>
                    </div>
                    <div class="section-actions">
                        <button class="slider-arrow" type="button" data-slider-prev="#wantSlider" aria-label="Previous"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <button class="slider-arrow" type="button" data-slider-next="#wantSlider" aria-label="Next"><i
                                class="fa-solid fa-chevron-right"></i></button>
                        <a href="{{ url('i-want') }}" class="btn btn-view-all">View all</a>
                    </div>
                </div>

                @if ($wantProducts->count())
                    <div id="wantSlider" class="auto-slider" data-autoplay="3000">
                        <div class="slider-track">
                            @foreach ($wantProducts as $product)
                                @php [$icon, $tint] = $thumbIcons[$loop->index % count($thumbIcons)]; @endphp
                                <div class="slider-item">
                                    @include('front.partials.product-card', [
                                        'product' => $product,
                                        'type' => 'want',
                                        'icon' => $icon,
                                        'tint' => $tint,
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="empty-note">No requirements posted yet.</div>
                @endif
            </div>
        </section>

        {{-- ================= 3) I HAVE products (auto scroll) ================= --}}
        <section class="home-section product-section section-have">
            <div class="container-fluid">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-seedling"></i>I Have</span>
                        <h2 class="section-title">Ready stock from farmers</h2>
                        <p class="section-subtitle">Straight from the farm. Like something? Talk to the farmer.</p>
                    </div>
                    <div class="section-actions">
                        <button class="slider-arrow" type="button" data-slider-prev="#haveSlider"
                            aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="slider-arrow" type="button" data-slider-next="#haveSlider" aria-label="Next"><i
                                class="fa-solid fa-chevron-right"></i></button>
                        <a href="{{ url('i-have') }}" class="btn btn-view-all">View all</a>
                    </div>
                </div>

                @if ($haveProducts->count())
                    <div id="haveSlider" class="auto-slider" data-autoplay="3500">
                        <div class="slider-track">
                            @foreach ($haveProducts as $product)
                                @php [$icon, $tint] = $thumbIcons[($loop->index + 2) % count($thumbIcons)]; @endphp
                                <div class="slider-item">
                                    @include('front.partials.product-card', [
                                        'product' => $product,
                                        'type' => 'have',
                                        'icon' => $icon,
                                        'tint' => $tint,
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="empty-note">No products listed yet.</div>
                @endif
            </div>
        </section>

        {{-- ================= 4) PROFILES: Farmer + Non Farmer ek sath ================= --}}
        <section class="home-section profile-section">
            <div class="container-fluid">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-users"></i>Our members</span>
                        <h2 class="section-title">Farmers and buyers in one place</h2>
                        <p class="section-subtitle">Browse Farmer and Non Farmer profiles and connect directly.</p>
                    </div>
                    <div class="section-actions flex-wrap">
                        <div class="profile-filter" data-profile-filter="#profileSlider" role="group"
                            aria-label="Profile filter">
                            <button type="button" class="btn active" data-role="all" aria-pressed="true">All</button>
                            <button type="button" class="btn" data-role="farmer"
                                aria-pressed="false">Farmer</button>
                            <button type="button" class="btn" data-role="nonfarmer" aria-pressed="false">Non
                                Farmer</button>
                        </div>
                        <button class="slider-arrow" type="button" data-slider-prev="#profileSlider"
                            aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="slider-arrow" type="button" data-slider-next="#profileSlider"
                            aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>

                @if ($profiles->count())
                    <div id="profileSlider" class="auto-slider" data-autoplay="4000">
                        <div class="slider-track">
                            @foreach ($profiles as $member)
                                @php
                                    $isFarmer = $member->role === 'Farmer';
                                    $roleKey = $isFarmer ? 'farmer' : 'nonfarmer';
                                    $initials = collect(explode(' ', trim($member->full_name)))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                        ->implode('');
                                @endphp
                                <div class="slider-item" data-role="{{ $roleKey }}">
                                    <div class="profile-card role-{{ $roleKey }}">
                                        <div class="profile-avatar">
                                            @if ($member->profile_image)
                                                <img src="{{ asset($member->profile_image) }}"
                                                    alt="{{ $member->full_name }}" loading="lazy">
                                            @else
                                                {{ $initials }}
                                            @endif
                                        </div>
                                        @if (optional($member->profile)->status === 'Approved')
                                            <span class="verified-tick" title="Verified">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </span>
                                        @endif
                                        <h3 class="profile-name">{{ $member->full_name }}</h3>
                                        <span class="role-badge">
                                            <i class="fa-solid {{ $isFarmer ? 'fa-seedling' : 'fa-store' }}"></i>
                                            {{ $member->role }}
                                        </span>
                                        @php
                                            $city = $member->citys->name ?? null;
                                            $state = $member->states->name ?? null;
                                        @endphp
                                        <p class="profile-location">
                                            <i class="fa-solid fa-location-dot me-1"></i>
                                            {{ collect([$city, $state])->filter()->implode(', ') ?:'India' }}
                                        </p>
                                        @if ($member->area)
                                            <div class="profile-area">
                                                <i class="fa-solid fa-map-pin"></i>
                                                <span>{{ $member->area }}</span>
                                            </div>
                                        @endif
                                        <a href="{{ url('profile/' . $member->unique_id) }}" class="btn btn-profile">
                                            <i class="fa-solid fa-user me-2"></i>View profile
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="empty-note">No profiles yet.</div>
                @endif
            </div>
        </section>

        {{-- ================= 5) BLOG ================= --}}
        <section class="home-section blog-section">
            <div class="container-fluid">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-newspaper"></i>Blog</span>
                        <h2 class="section-title">Useful farming tips</h2>
                        <p class="section-subtitle">Simple articles on weather, mandi prices and new techniques.</p>
                    </div>
                    <div class="section-actions">
                        <a href="{{ url('blog') }}" class="btn btn-view-all">All posts</a>
                    </div>
                </div>

                @if ($blogs->count())
                    <div class="row g-3 g-lg-4">
                        @foreach ($blogs as $blog)
                            @php $blogUrl = url('blog/' . ($blog->slug ?? $blog->id)); @endphp
                            <div class="col-12 col-sm-6 col-lg-3">
                                <article class="blog-card">
                                    <a href="{{ $blogUrl }}" class="blog-thumb">
                                        @if (!empty($blog->image))
                                            <img src="{{ asset($blog->image) }}" alt="{{ $blog->title ?? '' }}"
                                                loading="lazy">
                                        @else
                                            <i class="fa-solid fa-leaf thumb-icon" aria-hidden="true"></i>
                                        @endif
                                        <span
                                            class="blog-date">{{ \Carbon\Carbon::parse($blog->created_at ?? now())->format('d M Y') }}</span>
                                    </a>
                                    <div class="blog-body">
                                        <h3 class="blog-title"><a
                                                href="{{ $blogUrl }}">{{ $blog->title ?? '' }}</a></h3>
                                        <p class="blog-excerpt">
                                            {{ \Illuminate\Support\Str::limit(strip_tags(preg_replace('#</(p|h[1-6]|li|div)>#i', ' ', $blog->description ?? '')), 120) }}
                                        </p>
                                        <a href="{{ $blogUrl }}" class="blog-more">Read more <i
                                                class="fa-solid fa-arrow-right ms-1"></i></a>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-note">New blog posts are coming soon.</div>
                @endif
            </div>
        </section>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('front/js/home.js') }}"></script>
@endpush
