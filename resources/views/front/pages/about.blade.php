@extends('front.layout.app')
@section('title', 'About Us - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/about.css') }}">
@endpush
@php
    $brand = config('app.name');

    // Farmer <-> Business: farmers ko kya kya mil sakta hai
    $farmNeeds = [
        'Seeds', 'Saplings', 'Fertilizers', 'Pesticides', 'Manure', 'Machinery', 'Farming tools',
        'Solar energy', 'Drip irrigation', 'Biogas', 'Modern farming technologies', 'Equipment repair',
        'Soil testing', 'Crop consultancy', 'Insurance', 'Farm loans', 'Transportation', 'Warehousing',
        'Cold storage', 'Processing', 'Packaging',
    ];

    // Who can join
    $members = [
        ['icon' => 'fa-wheat-awn', 'label' => 'Farmers'],
        ['icon' => 'fa-people-group', 'label' => 'FPOs'],
        ['icon' => 'fa-industry', 'label' => 'Manufacturers'],
        ['icon' => 'fa-scale-balanced', 'label' => 'Traders'],
        ['icon' => 'fa-boxes-stacked', 'label' => 'Wholesalers'],
        ['icon' => 'fa-store', 'label' => 'Retailers'],
        ['icon' => 'fa-screwdriver-wrench', 'label' => 'Agricultural service providers'],
        ['icon' => 'fa-microchip', 'label' => 'Technology companies'],
        ['icon' => 'fa-lightbulb', 'label' => 'Entrepreneurs'],
        ['icon' => 'fa-user-tie', 'label' => 'Professionals'],
        ['icon' => 'fa-house-chimney', 'label' => 'Families'],
        ['icon' => 'fa-basket-shopping', 'label' => 'Consumers'],
    ];
@endphp
@section('content')
    <div class="inner-page about-page">
        @include('front.partials.page-hero', [
            'title' => 'Much More Than an E-Commerce Portal!',
            'text' => 'Connecting Farmers, Businesses & Consumers – For Mutual Growth',
            'badge' => ['icon' => 'fa-circle-info', 'label' => 'About us'],
            'art' => 'fa-handshake',
            'crumbs' => ['About' => null],
        ])

        {{-- ================= Intro ================= --}}
        <section class="page-section">
            <div class="container-xl">
                <div class="row g-4 g-lg-5 align-items-center">
                    <div class="col-12 col-lg-6">
                        <span class="section-eyebrow"><i class="fa-solid fa-seedling"></i>Who we are</span>
                        <h2 class="section-title">Connecting Farmers, Businesses &amp; Consumers</h2>
                        <p class="about-lead">
                            <strong>{{ $brand }}</strong> is a nationwide digital networking platform that directly
                            connects Farmers, Agricultural Businesses and Consumers, creating opportunities to exchange
                            products, services, resources, knowledge and business opportunities.
                        </p>
                        <blockquote class="about-philosophy">
                            <i class="fa-solid fa-quote-left" aria-hidden="true"></i>
                            <span class="about-philosophy-label">Our philosophy is simple</span>
                            Connect people directly so they can help each other, save costs, generate business and grow
                            together.
                        </blockquote>
                    </div>

                    {{-- Farmer / Business / Consumer: teen taraf connection --}}
                    <div class="col-12 col-lg-6">
                        <div class="connect-map" aria-hidden="true">
                            <svg class="connect-lines" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <line x1="50" y1="18" x2="18" y2="80" />
                                <line x1="50" y1="18" x2="82" y2="80" />
                                <line x1="18" y1="80" x2="82" y2="80" />
                            </svg>
                            <div class="connect-node node-farmer">
                                <span><i class="fa-solid fa-wheat-awn"></i></span>Farmers
                            </div>
                            <div class="connect-node node-business">
                                <span><i class="fa-solid fa-industry"></i></span>Businesses
                            </div>
                            <div class="connect-node node-consumer">
                                <span><i class="fa-solid fa-users"></i></span>Consumers
                            </div>
                            <div class="connect-center">
                                <strong>{{ $brand }}</strong>
                                <small>Mutual Growth</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= 3 connections ================= --}}
        <section class="page-section bg-white-section">
            <div class="container-xl">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-diagram-project"></i>How we connect</span>
                        <h2 class="section-title">Three connections, one network</h2>
                    </div>
                </div>

                {{-- 1) Farmer <-> Business --}}
                <article class="connect-block block-green">
                    <div class="connect-block-head">
                        <span class="connect-no">01</span>
                        <div>
                            <h3 class="connect-pair">Farmer <i class="fa-solid fa-arrows-left-right"></i> Business</h3>
                            <p class="connect-sub">Connecting Farmers with Companies &amp; Businesses</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-12 col-lg-7">
                            <p>Farmers can directly connect with manufacturers, dealers, suppliers and service providers
                                to access everything they need for agriculture:</p>
                            <ul class="need-chips">
                                @foreach ($farmNeeds as $need)
                                    <li>{{ $need }}</li>
                                @endforeach
                                <li class="chip-more">and other agricultural services</li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-5">
                            <p>Similarly, companies and industries can directly connect with farmers to procure
                                agricultural produce and raw materials.</p>
                            <ul class="example-list">
                                <li><i class="fa-solid fa-bottle-droplet"></i>A <strong>ketchup manufacturer</strong> can
                                    find tomato growers</li>
                                <li><i class="fa-solid fa-wheat-awn"></i>A <strong>flour mill</strong> can connect with
                                    wheat farmers</li>
                                <li><i class="fa-solid fa-apple-whole"></i>A <strong>food processing company</strong> can
                                    source fruits, vegetables and other ingredients directly from producers</li>
                            </ul>
                        </div>
                    </div>
                    <p class="connect-result"><strong><i class="fa-solid fa-circle-check"></i>Result:</strong> Direct
                        connections between agricultural suppliers, farmers and industries – creating opportunities for
                        both sides.</p>
                </article>

                {{-- 2) Farmer <-> Farmer --}}
                <article class="connect-block block-sun">
                    <div class="connect-block-head">
                        <span class="connect-no">02</span>
                        <div>
                            <h3 class="connect-pair">Farmer <i class="fa-solid fa-arrows-left-right"></i> Farmer</h3>
                            <p class="connect-sub">Farmers Helping Farmers</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <p>Farmers can search and connect directly with other farmers in nearby villages, districts or
                                anywhere across India to support each other in farming and related activities.</p>
                            <p>They can buy, sell, rent, exchange or share seeds, saplings, manure, crops, machinery,
                                tractors, tools, labour, land, water, storage and other agricultural resources.</p>
                        </div>
                        <div class="col-12 col-lg-6">
                            <ul class="example-list">
                                <li><i class="fa-solid fa-tractor"></i>Rent another farmer’s tractor</li>
                                <li><i class="fa-solid fa-map-location-dot"></i>Lease available farmland</li>
                                <li><i class="fa-solid fa-droplet"></i>Access water from a nearby farm through mutually
                                    agreed arrangements</li>
                                <li><i class="fa-solid fa-right-left"></i>Exchange produce and resources instead of making
                                    cash payments</li>
                            </ul>
                        </div>
                    </div>
                    <p>Farmers can also share farming techniques, practical experiences, innovative ideas, cost-saving
                        methods, market information and knowledge about cultivation, irrigation, soil health, pest
                        management, harvesting, storage and marketing.</p>
                    <p class="connect-result"><strong><i class="fa-solid fa-circle-check"></i>Result:</strong> Reduced
                        farming costs, better utilisation of resources, knowledge sharing and stronger farmer-to-farmer
                        cooperation.</p>
                </article>

                {{-- 3) Farmer <-> Consumer --}}
                <article class="connect-block block-tomato">
                    <div class="connect-block-head">
                        <span class="connect-no">03</span>
                        <div>
                            <h3 class="connect-pair">Farmer <i class="fa-solid fa-arrows-left-right"></i> Consumer</h3>
                            <p class="connect-sub">Bringing Farmers Closer to the General Public</p>
                        </div>
                    </div>
                    <p>Farmers can directly connect with households, families, residential societies and consumers to
                        sell fresh vegetables, fruits, grains, pulses, dairy products, spices, honey and other agricultural
                        produce.</p>
                    <p class="connect-highlight">But this relationship goes beyond buying and selling!</p>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="mini-card">
                                <h4><i class="fa-solid fa-handshake-angle"></i>Consumers help farmers</h4>
                                <p>By providing business leads, connecting them with potential buyers, sharing professional
                                    expertise, technical knowledge, marketing skills and business opportunities.</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mini-card">
                                <h4><i class="fa-solid fa-children"></i>Consumers visit farms</h4>
                                <p>Introduce children to farming, learn agricultural practices, organise community visits
                                    and events, participate in educational and social programmes, and develop meaningful
                                    relationships with farming communities.</p>
                            </div>
                        </div>
                    </div>
                    <p class="connect-result"><strong><i class="fa-solid fa-circle-check"></i>Result:</strong> Direct
                        farm-to-consumer connections, new business opportunities, agricultural awareness and mutual
                        support.</p>
                </article>
            </div>
        </section>

        {{-- ================= Who can join ================= --}}
        <section class="page-section">
            <div class="container-xl">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-user-plus"></i>Who can join?</span>
                        <h2 class="section-title">Everyone is Welcome!</h2>
                    </div>
                </div>
                <ul class="join-grid">
                    @foreach ($members as $member)
                        <li><i class="fa-solid {{ $member['icon'] }}"></i>{{ $member['label'] }}</li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- ================= How it works ================= --}}
        <section class="page-section bg-white-section">
            <div class="container-xl">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-route"></i>How does it work?</span>
                        <h2 class="section-title">List, post, connect</h2>
                    </div>
                </div>
                <div class="row g-3 g-lg-4">
                    <div class="col-12 col-md-4">
                        <div class="work-card work-have">
                            <span class="work-step">1</span>
                            <h3><i class="fa-solid fa-seedling"></i>People Have</h3>
                            <p>List your available products, produce, machinery, land, services, expertise and resources.</p>
                            <a href="{{ url('i-have') }}" class="work-link">See People Have <i
                                    class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="work-card work-want">
                            <span class="work-step">2</span>
                            <h3><i class="fa-solid fa-cart-shopping"></i>People Want</h3>
                            <p>Post your requirements, specifications, quantity, location and other details.</p>
                            <a href="{{ url('i-want') }}" class="work-link">See People Want <i
                                    class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="work-card work-connect">
                            <span class="work-step">3</span>
                            <h3><i class="fa-solid fa-handshake"></i>Connect</h3>
                            <p>Discover relevant members, connect directly, discuss requirements and establish mutually
                                beneficial relationships.</p>
                            <a href="{{ url('register') }}" class="work-link">Join free <i
                                    class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= Vision ================= --}}
        <section class="page-section">
            <div class="container-xl">
                <div class="vision-band">
                    <i class="fa-solid fa-eye vision-art" aria-hidden="true"></i>
                    <span class="vision-label">Our Vision</span>
                    <p class="vision-text">To build India’s largest connected agricultural ecosystem where Farmers,
                        Businesses and Consumers can find each other, help each other and create opportunities together.</p>
                    <div class="vision-brand">
                        <strong>{{ $brand }}</strong>
                        <ul class="vision-words">
                            <li>Connect.</li>
                            <li>Collaborate.</li>
                            <li>Trade.</li>
                            <li>Share.</li>
                            <li>Grow Together!</li>
                        </ul>
                        <p>One Network. Endless Possibilities. Mutual Growth.</p>
                    </div>
                </div>
            </div>
        </section>

        @include('front.partials.cta-band', [
            'title' => 'Ready to join ' . $brand . '?',
            'text' => 'Farmers, businesses and consumers – everyone is welcome. Register free and start connecting today.',
        ])
    </div>
@endsection
