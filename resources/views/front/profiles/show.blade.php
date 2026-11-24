@extends('front.layout.app')
@section('title', $details['name'] .' - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
@endpush
@php
    $isFarmer = $details['role'] === 'Farmer';
    $initials = collect(explode(' ', trim($details['name'])))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $location = collect([$details['area'], $details['city'], $details['state']])->filter()->implode(', ');
    $place = collect([$details['city'], $details['state']])->filter()->implode(', ');
    $linkIcons = [
        'website'   => ['fa-solid fa-globe', 'Website'],
        'facebook'  => ['fa-brands fa-facebook-f', 'Facebook'],
        'instagram' => ['fa-brands fa-instagram', 'Instagram'],
        'youtube'   => ['fa-brands fa-youtube', 'YouTube'],
        'linkedin'  => ['fa-brands fa-linkedin-in', 'LinkedIn'],
    ];
    // Login nahi hai to login page; login ke baad aapka enquiry / chat route yahan laga sakte hain
    $contactUrl = auth()->check() ? url('contact-us') : url('login');
    $profileUrl = url('profile/' . $details['unique_id']);
    $fallbackIcons = [['fa-carrot', 'thumb-red'], ['fa-wheat-awn', 'thumb-yellow'], ['fa-seedling', 'thumb-lime'], ['fa-apple-whole', 'thumb-red']];
    $cardData = function ($product, $index) use ($fallbackIcons) {
        $cat = \App\Support\Market::categoryFor(data_get($product, 'categorys.name'));
        return $cat ? [$cat['icon'], 'thumb-' . $cat['color']] : $fallbackIcons[$index % count($fallbackIcons)];
    };
@endphp

@section('content')
<div class="inner-page">

    {{-- ================= Profile header ================= --}}
    <div class="page-hero-wrap">
        <div class="container-xl">
            <section class="profile-hero {{ $isFarmer ? 'is-farmer' : 'is-nonfarmer' }}">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>

                <div class="profile-hero-main">
                    <div class="profile-hero-avatar">
                        @if ($details['image'])
                            <img src="{{ asset($details['image']) }}" alt="{{ $details['name'] }}">
                        @else
                            {{ $initials }}
                        @endif
                    </div>

                    <div class="profile-hero-info">
                        <h1 class="profile-hero-name">
                            {{ $details['name'] }}
                            <i class="fa-solid fa-circle-check verified" title="Verified profile" aria-label="Verified profile"></i>
                        </h1>
                        <div class="profile-hero-meta">
                            <span class="role-badge {{ $isFarmer ? 'badge-farmer' : 'badge-nonfarmer' }}">
                                <i class="fa-solid {{ $isFarmer ? 'fa-seedling' : 'fa-store' }}"></i>{{ $details['role'] }}
                            </span>
                            @if ($place)<span><i class="fa-solid fa-location-dot"></i>{{ $place }}</span>@endif
                            @if ($details['since'])<span><i class="fa-regular fa-calendar"></i>Member since {{ $details['since'] }}</span>@endif
                            <span><i class="fa-solid fa-id-badge"></i>{{ $details['unique_id'] }}</span>
                        </div>
                    </div>

                    <div class="profile-hero-actions">
                        <a href="{{ $contactUrl }}" class="btn btn-farm btn-farm-yellow">
                            <i class="fa-solid fa-comments me-1"></i>{{ auth()->check() ? 'Contact' : 'Login to contact' }}
                        </a>
                        <button type="button" class="btn btn-light-glass" data-copy-link="{{ $profileUrl }}" title="Copy link">
                            <i class="fa-solid fa-share-nodes me-1"></i>Share
                        </button>
                    </div>
                </div>

                <div class="profile-hero-stats">
                    <div><strong>{{ $products->count() }}</strong><span>Listings</span></div>
                    <div><strong>{{ $haveProducts->count() }}</strong><span>People Have</span></div>
                    <div><strong>{{ $wantProducts->count() }}</strong><span>People Want</span></div>
                    <div><strong>{{ $details['established'] ?: '-' }}</strong><span>Since</span></div>
                </div>
            </section>
        </div>
    </div>

    {{-- ================= Details ================= --}}
    <section class="page-section">
        <div class="container-xl">
            <div class="row g-4">
                <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                    <div class="farm-card">
                        <h2 class="farm-card-title"><i class="fa-solid fa-user"></i>About {{ $details['name'] }}</h2>
                        @if ($details['description'])
                            <div class="rich-text">{!! $details['description'] !!}</div>
                        @else
                            <p class="text-body-secondary mb-0">This member has not added a description yet.</p>
                        @endif
                    </div>

                    @if ($details['photos'])
                        <div class="farm-card">
                            <h2 class="farm-card-title"><i class="fa-solid fa-images"></i>Photos</h2>
                            <div class="profile-gallery">
                                @foreach ($details['photos'] as $photo)
                                    <a href="{{ asset($photo) }}" target="_blank" rel="noopener" aria-label="Open photo {{ $loop->iteration }}">
                                        <img src="{{ asset($photo) }}" alt="{{ $details['name'] }} photo {{ $loop->iteration }}" loading="lazy">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($details['video'])
                        <div class="farm-card">
                            <h2 class="farm-card-title"><i class="fa-solid fa-video"></i>Video</h2>
                            <video class="profile-video" src="{{ asset($details['video']) }}" controls preload="metadata" playsinline></video>
                        </div>
                    @endif
                </div>

                <aside class="col-12 col-lg-4 d-flex flex-column gap-4">
                    <div class="farm-card">
                        <h2 class="farm-card-title"><i class="fa-solid fa-briefcase"></i>{{ $isFarmer ? 'Farm details' : 'Business details' }}</h2>
                        <ul class="seller-facts m-0">
                            @if ($details['established'])
                                <li><i class="fa-solid fa-calendar-check"></i><span>Established</span><strong>{{ $details['established'] }}</strong></li>
                            @endif
                            @if ($details['workers'])
                                <li><i class="fa-solid fa-people-group"></i><span>Workers</span><strong>{{ number_format((float) $details['workers']) }}</strong></li>
                            @endif
                            @if ((float) $details['land'] > 0)
                                <li><i class="fa-solid fa-ruler-combined"></i><span>Area</span><strong>{{ rtrim(rtrim(number_format((float) $details['land'], 2), '0'), '.') }} sq. yard</strong></li>
                            @endif
                            @if ($location)
                                <li><i class="fa-solid fa-location-dot"></i><span>Location</span><strong class="text-wrap text-end">{{ $location }}</strong></li>
                            @endif
                            @if ($details['pincode'])
                                <li><i class="fa-solid fa-map-pin"></i><span>Pincode</span><strong>{{ $details['pincode'] }}</strong></li>
                            @endif
                            <li><i class="fa-solid fa-shield-halved"></i><span>Status</span><strong class="text-success">Verified</strong></li>
                        </ul>
                    </div>

                    @if ($details['links'])
                        <div class="farm-card">
                            <h2 class="farm-card-title"><i class="fa-solid fa-link"></i>Find online</h2>
                            <div class="profile-links">
                                @foreach ($details['links'] as $key => $url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener nofollow" class="profile-link link-{{ $key }}">
                                        <i class="{{ $linkIcons[$key][0] }}"></i>{{ $linkIcons[$key][1] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="detail-note m-0">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Always check the goods before you pay. KisanMandi never asks for advance payment.</span>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- ================= Saare products (home wala card) ================= --}}
    <section class="page-section bg-white-section" id="member-products">
        <div class="container-xl">
            <div class="section-head">
                <div>
                    <span class="section-eyebrow"><i class="fa-solid fa-box-open"></i>Listings</span>
                    <h2 class="section-title">Products by {{ $details['name'] }}</h2>
                </div>
                @if ($haveProducts->count() && $wantProducts->count())
                    <nav class="type-tabs" role="tablist" aria-label="Listing type">
                        <a href="#tabAll" class="active" data-bs-toggle="tab" role="tab" aria-controls="tabAll" aria-selected="true">All ({{ $products->count() }})</a>
                        <a href="#tabHave" data-bs-toggle="tab" role="tab" aria-controls="tabHave" aria-selected="false"><i class="fa-solid fa-seedling"></i>People Have ({{ $haveProducts->count() }})</a>
                        <a href="#tabWant" data-bs-toggle="tab" role="tab" aria-controls="tabWant" aria-selected="false"><i class="fa-solid fa-cart-shopping"></i>People Want ({{ $wantProducts->count() }})</a>
                    </nav>
                @endif
            </div>

            @if ($products->count())
                <div class="tab-content">
                    @foreach (['tabAll' => $products, 'tabHave' => $haveProducts, 'tabWant' => $wantProducts] as $paneId => $list)
                        @continue($paneId !== 'tabAll' && !($haveProducts->count() && $wantProducts->count()))
                        <div class="tab-pane fade {{ $paneId === 'tabAll' ? 'show active' : '' }}" id="{{ $paneId }}" role="tabpanel">
                            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-sm-3 product-grid">
                                @foreach ($list as $product)
                                    @php [$icon, $tint] = $cardData($product, $loop->index); @endphp
                                    <div class="col">
                                        @include('front.partials.product-card', ['product' => $product, 'type' => \App\Support\Market::typeOf($product), 'icon' => $icon, 'tint' => $tint])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-box-open"></i>
                    <h3>No listings yet</h3>
                    <p>{{ $details['name'] }} has not posted any products yet.</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('front/js/pages.js') }}"></script>
@endpush
