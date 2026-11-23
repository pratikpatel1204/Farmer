@extends('front.layout.app')
@section('title', 'About Us - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
@endpush
@section('content')
    <div class="inner-page">
        @include('front.partials.page-hero', [
            'title' => 'Connecting farmers and buyers directly',
            'text' =>
                'KisanMandi is an open marketplace where farmers list what they grow and buyers post what they need. No middlemen, fair prices.',
            'badge' => ['icon' => 'fa-circle-info', 'label' => 'About us'],
            'art' => 'fa-handshake',
            'crumbs' => ['About' => null],
        ])

        {{-- ================= Our story ================= --}}
        <section class="page-section">
            <div class="container-xl">
                <div class="row g-4 align-items-center">
                    <div class="col-12 col-lg-6">
                        <span class="section-eyebrow"><i class="fa-solid fa-seedling"></i>Our story</span>
                        <h2 class="section-title">From the field to your doorstep</h2>
                        <div class="rich-text mt-3">
                            <p>Most farmers sell their crop through many hands before it reaches the buyer. Every hand takes
                                a cut, and the farmer gets the smallest share.</p>
                            <p>KisanMandi was started to fix this. Farmers can show their produce to buyers across India,
                                and traders, shops and families can tell farmers exactly what they need.</p>
                        </div>
                        <ul class="about-points">
                            <li><i class="fa-solid fa-circle-check"></i>Free listing for farmers and buyers</li>
                            <li><i class="fa-solid fa-circle-check"></i>Talk directly and agree on your own price</li>
                            <li><i class="fa-solid fa-circle-check"></i>Verified profiles with a blue tick</li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="about-collage">
                            <div class="collage-tile tile-green tall">
                                <i class="fa-solid fa-wheat-awn" aria-hidden="true"></i>
                                <small>For farmers</small>
                                Sell your harvest at a fair price
                            </div>
                            <div class="collage-tile tile-sun">
                                <i class="fa-solid fa-basket-shopping" aria-hidden="true"></i>
                                <small>For buyers</small>
                                Fresh stock, straight from the farm
                            </div>
                            <div class="collage-tile tile-tomato">
                                <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                                <small>For everyone</small>
                                Faster deals, less waste
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= How it works ================= --}}
        <section class="page-section bg-white-section">
            <div class="container-xl">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-route"></i>How it works</span>
                        <h2 class="section-title">Simple steps for both sides</h2>
                        <p class="section-subtitle">Whether you have something to sell or something to buy, it takes only a
                            few minutes.</p>
                    </div>
                </div>

                <div class="row g-3 g-lg-4">
                    <div class="col-12 col-md-6">
                        <div class="steps-card steps-have">
                            <h3><i class="fa-solid fa-seedling"></i>I Have (for farmers)</h3>
                            <ol class="step-list">
                                <li><strong>Register as a Farmer</strong><span>Create your free profile with your village
                                        and city.</span></li>
                                <li><strong>List your produce</strong><span>Add photos, quantity and a short description of
                                        your crop.</span></li>
                                <li><strong>Get calls from buyers</strong><span>Buyers contact you directly. You decide the
                                        price.</span></li>
                            </ol>
                            <a href="{{ url('i-have') }}" class="btn btn-farm mt-3">See I Have listings</a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="steps-card steps-want">
                            <h3><i class="fa-solid fa-cart-shopping"></i>I Want (for buyers)</h3>
                            <ol class="step-list">
                                <li><strong>Register as a Non Farmer</strong><span>Traders, shops, factories and families
                                        are all welcome.</span></li>
                                <li><strong>Post your requirement</strong><span>Tell farmers what you need, how much and
                                        where.</span></li>
                                <li><strong>Farmers reach out</strong><span>Compare offers and buy directly from the
                                        farm.</span></li>
                            </ol>
                            <a href="{{ url('i-want') }}" class="btn btn-farm btn-farm-red mt-3">See I Want listings</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= Values ================= --}}
        <section class="page-section">
            <div class="container-xl">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-heart"></i>What we believe</span>
                        <h2 class="section-title">Built on trust</h2>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                    <div class="col">
                        <div class="value-card"><i class="fa-solid fa-handshake tint-green"></i>
                            <h3>Direct deals</h3>
                            <p>Buyer and farmer talk to each other. No agent in the middle.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="value-card"><i class="fa-solid fa-shield-halved tint-yellow"></i>
                            <h3>Verified members</h3>
                            <p>Profiles are checked by our team before they get the blue tick.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="value-card"><i class="fa-solid fa-indian-rupee-sign tint-red"></i>
                            <h3>Fair prices</h3>
                            <p>Open listings help everyone see the real market rate.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="value-card"><i class="fa-solid fa-headset tint-brown"></i>
                            <h3>Real support</h3>
                            <p>Our team helps you in Hindi and English, on call or email.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @include('front.partials.cta-band', [
            'title' => 'Ready to join KisanMandi?',
            'text' => 'Register free as a Farmer or a Non Farmer and start today.',
        ])
    </div>
@endsection
