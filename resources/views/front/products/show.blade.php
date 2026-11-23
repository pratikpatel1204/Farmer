@extends('front.layout.app')
@section('title', $product->title . ' - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/auth.css') }}">
@endpush
@php
    $isWant = $type === 'want';
    $category = $product->categorys;
    $cat = \App\Support\Market::categoryFor($category->name ?? $product->category);
    $icon = $cat['icon'] ?? 'fa-seedling';
    $tint = $cat['color'] ?? 'green';
    $tags = collect(explode(',', (string) $product->product_tags))->map(fn ($t) => trim($t))->filter();

    $ownerName = $member->full_name ?? 'Member';
    $ownerRole = $member->role ?? ($isWant ? 'Non Farmer' : 'Farmer');
    $isFarmer = $ownerRole === 'Farmer';
    $location = collect([$member?->citys?->name, $member?->states?->name])->filter()->implode(', ') ?: ($member->area ?? null);
    $initials = collect(explode(' ', trim($ownerName)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

    $isVerified = ($profile->status ?? null) === 'Approved';
    $companyImages = \App\Support\Market::imageList($profile->company_images ?? null);
    $socialIcons = ['facebook' => 'fa-facebook-f', 'instagram' => 'fa-instagram', 'youtube' => 'fa-youtube', 'twitter' => 'fa-x-twitter', 'x' => 'fa-x-twitter', 'linkedin' => 'fa-linkedin-in', 'whatsapp' => 'fa-whatsapp'];

    $listUrl = url($isWant ? 'i-want' : 'i-have');
    $categoryUrl = $category ? $listUrl . '?category=' . $category->id : null;
    $profileUrl = !empty($member?->unique_id) ? url('profile/' . $member->unique_id) : null;
    // Enquiry: login ho to form wala modal, warna "pehle login karein" modal. Apni listing par button band.
    $authUser = auth()->user();
    $isOwner = $authUser && (int) $authUser->id === (int) $product->member_id;
    $enquiryModal = $authUser ? '#enquiryModal' : '#loginFirstModal';
    $enquiryLabel = $isWant ? 'Submit Request' : 'Contact seller';
    $partyName = $isWant ? 'buyer' : 'seller';
    $attributes = $attributes ?? collect();
@endphp

@section('content')
<div class="inner-page">
    <section class="product-detail">
        <div class="container-xl">

            <nav class="detail-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ $listUrl }}">{{ $isWant ? 'I Want' : 'I Have' }}</a></li>
                    @if ($category)
                        <li class="breadcrumb-item"><a href="{{ $categoryUrl }}">{{ $category->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($product->title, 40) }}</li>
                </ol>
            </nav>

            <div class="row g-4">
                {{-- ================= Gallery ================= --}}
                <div class="col-12 col-lg-5">
                    <div class="gallery-main {{ $images ? '' : 'thumb-' . $tint }}">
                        @if ($images)
                            <img id="galleryMain" src="{{ asset($images[0]) }}" alt="{{ $product->title }}">
                        @else
                            <i class="fa-solid {{ $icon }} thumb-icon" aria-hidden="true"></i>
                        @endif
                        <span class="product-type {{ $isWant ? 'type-want' : 'type-have' }}">
                            <i class="fa-solid {{ $isWant ? 'fa-cart-shopping' : 'fa-seedling' }}"></i>{{ $isWant ? 'Wanted' : 'Available' }}
                        </span>
                    </div>

                    @if (count($images) > 1)
                        <div class="gallery-thumbs" role="group" aria-label="Product photos">
                            @foreach ($images as $img)
                                <button type="button" class="gallery-thumb {{ $loop->first ? 'active' : '' }}" data-gallery-thumb="#galleryMain" data-src="{{ asset($img) }}" aria-label="Photo {{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                                    <img src="{{ asset($img) }}" alt="" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ================= Info ================= --}}
                <div class="col-12 col-md-7 col-lg-4">
                    <div class="farm-card detail-info">
                        @if ($category)
                            <a href="{{ $categoryUrl }}" class="detail-category tint-{{ $tint }}"><i class="fa-solid {{ $icon }}"></i>{{ $category->name }}</a>
                        @endif

                        <h1 class="detail-title">{{ $product->title }}</h1>

                        <div class="detail-meta">
                            @if ($location)<span><i class="fa-solid fa-location-dot"></i>{{ $location }}</span>@endif
                            @if ($product->created_at)<span><i class="fa-regular fa-calendar"></i>Posted {{ \Carbon\Carbon::parse($product->created_at)->format('d M Y') }}</span>@endif
                        </div>

                        @if ($tags->count())
                            <div class="tag-list">
                                @foreach ($tags as $tag)<span>{{ $tag }}</span>@endforeach
                            </div>
                        @endif

                        @if ($product->description)
                            <p class="detail-lead">{{ \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags(str_replace('</p>', ' ', $product->description)))), 180) }}</p>
                        @endif

                        <div class="detail-actions">
                            @if ($isOwner)
                                <span class="btn btn-farm-outline flex-fill disabled" aria-disabled="true"><i class="fa-solid fa-user-check me-1"></i>This is your listing</span>
                            @else
                                <button type="button" class="btn btn-farm {{ $isWant ? 'btn-farm-red' : '' }} flex-fill" data-bs-toggle="modal" data-bs-target="{{ $enquiryModal }}">
                                    <i class="fa-solid {{ $isWant ? 'fa-hand-holding-heart' : 'fa-phone' }} me-1"></i>{{ $enquiryLabel }}
                                </button>
                            @endif
                            @if ($product->brochure)
                                <a href="{{ asset($product->brochure) }}" class="btn btn-farm-outline flex-fill" target="_blank" rel="noopener" download>
                                    <i class="fa-solid fa-file-arrow-down me-1"></i>Brochure
                                </a>
                            @endif
                        </div>

                        <div class="detail-note">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Talk to the {{ $isWant ? 'buyer' : 'seller' }} and check the goods before you pay. KisanMandi never asks for advance payment.</span>
                        </div>
                    </div>
                </div>

                {{-- ================= Seller card ================= --}}
                <div class="col-12 col-md-5 col-lg-3">
                    <div class="farm-card seller-card">
                        <div class="profile-avatar {{ $isFarmer ? '' : 'bg-brown' }}">
                            @if (!empty($member?->profile_image))
                                <img src="{{ asset($member->profile_image) }}" alt="{{ $ownerName }}">
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                        <h2 class="seller-name">
                            {{ $ownerName }}
                            @if ($isVerified)<i class="fa-solid fa-circle-check" title="Verified"></i>@endif
                        </h2>
                        <span class="role-badge {{ $isFarmer ? 'badge-farmer' : 'badge-nonfarmer' }}">
                            <i class="fa-solid {{ $isFarmer ? 'fa-seedling' : 'fa-store' }}"></i>{{ $ownerRole }}
                        </span>

                        <ul class="seller-facts">
                            @if ($location)
                                <li><i class="fa-solid fa-location-dot"></i><span>Location</span><strong>{{ $location }}</strong></li>
                            @endif
                            @if (!empty($profile->establishment_year))
                                <li><i class="fa-solid fa-calendar-check"></i><span>Since</span><strong>{{ $profile->establishment_year }}</strong></li>
                            @endif
                            @if (!empty($profile->website))
                                <li><i class="fa-solid fa-globe"></i><span>Website</span><a href="{{ \Illuminate\Support\Str::startsWith($profile->website, 'http') ? $profile->website : 'https://' . $profile->website }}" target="_blank" rel="noopener nofollow">{{ preg_replace('#^https?://(www\.)?#', '', $profile->website) }}</a></li>
                            @endif
                        </ul>

                        @if ($socials)
                            <div class="social-links">
                                @foreach ($socials as $name => $link)
                                    @if (is_string($link) && \Illuminate\Support\Str::startsWith($link, ['http://', 'https://']))
                                        <a href="{{ $link }}" target="_blank" rel="noopener nofollow" aria-label="{{ ucfirst($name) }}"><i class="fa-brands {{ $socialIcons[strtolower($name)] ?? 'fa-link' }}"></i></a>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if ($profileUrl)
                            <a href="{{ $profileUrl }}" class="btn btn-profile">View full profile</a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================= Description / About seller ================= --}}
            <div class="farm-card mt-4">
                <ul class="nav detail-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tabDescBtn" data-bs-toggle="tab" data-bs-target="#tabDesc" type="button" role="tab" aria-controls="tabDesc" aria-selected="true">Description</button>
                    </li>
                    @if ($attributes->count())
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tabSpecBtn" data-bs-toggle="tab" data-bs-target="#tabSpec" type="button" role="tab" aria-controls="tabSpec" aria-selected="false">Specification</button>
                        </li>
                    @endif
                    @if (!empty($profile->description) || $companyImages)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tabSellerBtn" data-bs-toggle="tab" data-bs-target="#tabSeller" type="button" role="tab" aria-controls="tabSeller" aria-selected="false">About the {{ $isWant ? 'buyer' : 'seller' }}</button>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active rich-text" id="tabDesc" role="tabpanel" aria-labelledby="tabDescBtn" tabindex="0">
                        {{-- Members khud likhte hain, isliye safe text (HTML tags hata kar). Sirf admin likhe to {!! $product->description !!} kar sakte hain. --}}
                        @if ($product->description)
                            <p>{!! nl2br(e(trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $product->description)))))) !!}</p>
                        @else
                            <p>No description added.</p>
                        @endif
                    </div>
                    @if ($attributes->count())
                        <div class="tab-pane fade" id="tabSpec" role="tabpanel" aria-labelledby="tabSpecBtn" tabindex="0">
                            <table class="spec-table">
                                <tbody>
                                    @foreach ($attributes as $attr)
                                        <tr>
                                            <th scope="row">{{ $attr->label }}</th>
                                            <td>{{ $attr->value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (!empty($profile->description) || $companyImages)
                        <div class="tab-pane fade rich-text" id="tabSeller" role="tabpanel" aria-labelledby="tabSellerBtn" tabindex="0">
                            @if (!empty($profile->description))
                                {!! \App\Support\Market::safeHtml($profile->description) !!}
                            @endif
                            @if ($companyImages)
                                <div class="company-gallery mt-3">
                                    @foreach ($companyImages as $img)
                                        <img src="{{ asset($img) }}" alt="{{ $ownerName }} photo {{ $loop->iteration }}" loading="lazy">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Related (home wala auto slider) ================= --}}
    @if ($related->count())
        <section class="home-section product-section">
            <div class="container-xl">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-layer-group"></i>More like this</span>
                        <h2 class="section-title">Related listings</h2>
                    </div>
                    <div class="section-actions">
                        <button class="slider-arrow" type="button" data-slider-prev="#relatedSlider" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="slider-arrow" type="button" data-slider-next="#relatedSlider" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div id="relatedSlider" class="auto-slider" data-autoplay="3500">
                    <div class="slider-track">
                        @foreach ($related as $item)
                            @php $itemCat = \App\Support\Market::categoryFor($item->categorys->name ?? $item->category); @endphp
                            <div class="slider-item">
                                @include('front.partials.product-card', [
                                    'product' => $item,
                                    'type' => \App\Support\Market::typeOf($item),
                                    'icon' => $itemCat['icon'] ?? 'fa-seedling',
                                    'tint' => 'thumb-' . ($itemCat['color'] ?? 'green'),
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
@include('front.products._enquiry-modal')
@endsection

@push('scripts')
    <script src="{{ asset('front/js/home.js') }}"></script>
    <script src="{{ asset('front/js/pages.js') }}"></script>
    <script src="{{ asset('front/js/enquiry.js') }}"></script>
@endpush
