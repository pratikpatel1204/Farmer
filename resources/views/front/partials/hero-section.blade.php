@php
    $heroSurvey = $heroSurvey ?? \App\Models\Survey::current();
    $heroQuestions = $heroSurvey ? $heroSurvey->visibleQuestions()->limit(3)->get() : collect();
    $heroQuestionTotal = $heroSurvey && $heroQuestions->count() ? $heroSurvey->visibleQuestions()->count() : 0;
    $heroSurveyDone =
        $heroQuestionTotal &&
        auth('web')->check() &&
        \App\Models\SurveyResponse::where('survey_id', $heroSurvey->id)->where('user_id', auth('web')->id())->exists();
@endphp

@if ($heroQuestionTotal)
    @push('styles')
        <link rel="stylesheet" href="{{ asset('front/css/survey.css') }}">
    @endpush
    @push('scripts')
        <script src="{{ asset('front/js/survey.js') }}"></script>
    @endpush
@endif

<section class="hero-section">
    <div class="container-fluid">
        <div class="row g-3 hero-row">

            {{-- Slider (banners table se) --}}
            <div class="col-12 col-md-6">
                <div id="heroSlider" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="4500">

                    @if ($banners->count())
                        @if ($banners->count() > 1)
                            <div class="carousel-indicators">
                                @foreach ($banners as $banner)
                                    <button type="button" data-bs-target="#heroSlider"
                                        data-bs-slide-to="{{ $loop->index }}"
                                        class="{{ $loop->first ? 'active' : '' }}"
                                        @if ($loop->first) aria-current="true" @endif
                                        aria-label="Slide {{ $loop->iteration }}"></button>
                                @endforeach
                            </div>
                        @endif

                        <div class="carousel-inner">
                            @foreach ($banners as $banner)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="hero-slide has-image {{ $slideColors[$loop->index % count($slideColors)] }}"
                                        @if ($banner->image) style="background-image: url('{{ asset($banner->image) }}');" @endif>
                                        <div class="hero-slide-content">
                                            <span class="hero-badge"><i class="fa-solid fa-leaf"></i>Direct from
                                                farmers</span>
                                            <h2 class="hero-title">{{ $banner->title }}</h2>
                                            @if ($banner->description)
                                                <p class="hero-text">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($banner->description), 140) }}
                                                </p>
                                            @endif
                                            <a href="{{ url('i-have') }}" class="btn btn-hero">
                                                Browse crops <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Koi Active banner nahi hai to ye default slide dikhegi --}}
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="hero-slide slide-green">
                                    <i class="fa-solid fa-wheat-awn hero-art" aria-hidden="true"></i>
                                    <div class="hero-slide-content">
                                        <span class="hero-badge">
                                            <i class="fa-solid fa-leaf"></i>Direct from farmers
                                        </span>
                                        <h2 class="hero-title">Fresh crops, fair prices</h2>
                                        <p class="hero-text">Buy and sell farm produce without any middlemen.</p>
                                        <a href="{{ url('i-have') }}" class="btn btn-hero">
                                            Browse crops <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($banners->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroSlider"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="col-12 col-md-6">
                @if ($heroQuestionTotal)
                    {{-- Active survey ke question (jitne height me aa jaayein, 2-3) --}}
                    <div class="hero-survey">
                        <i class="fa-solid fa-square-poll-vertical hero-survey-art" aria-hidden="true"></i>

                        <div class="hero-survey-head">
                            <span class="hero-survey-label"><i class="fa-solid fa-clipboard-question"></i>Quick
                                survey</span>
                            <h3 class="hero-survey-title">{{ $heroSurvey->name }}</h3>
                        </div>

                        <ol class="hero-survey-list" data-survey-fit>
                            @foreach ($heroQuestions as $q)
                                <li class="hero-survey-item" data-survey-item>
                                    <a href="{{ route('survey.show') }}#question-{{ $q->id }}" target="_blank"
                                        rel="noopener" class="hero-survey-link">
                                        <span class="hero-survey-no">{{ $loop->iteration }}</span>
                                        <span class="hero-survey-body">
                                            <span
                                                class="hero-survey-q">{{ \Illuminate\Support\Str::limit($q->question_text, 120) }}</span>
                                            <span class="hero-survey-options">
                                                @foreach (array_slice($q->options, 0, 4, true) as $text)
                                                    <span
                                                        class="hero-survey-option">{{ \Illuminate\Support\Str::limit($text, 28) }}</span>
                                                @endforeach
                                                @if (count($q->options) > 4)
                                                    <span
                                                        class="hero-survey-option is-more">+{{ count($q->options) - 4 }}
                                                        more</span>
                                                @endif
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ol>

                        <div class="hero-survey-foot">
                            <span class="hero-survey-meta">
                                @if ($heroSurveyDone)
                                    <i class="fa-solid fa-circle-check"></i>You have taken this survey
                                @else
                                    <i class="fa-regular fa-clock"></i>{{ $heroQuestionTotal }}
                                    {{ \Illuminate\Support\Str::plural('question', $heroQuestionTotal) }} · takes 1-2
                                    minutes
                                @endif
                            </span>
                            <a href="{{ route('survey.show') }}" target="_blank" rel="noopener" class="btn btn-survey">
                                View all <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                            </a>
                        </div>
                    </div>
                @else
                    {{-- Koi Active survey nahi: purane static banner --}}
                    <div class="hero-banners">
                        <a href="{{ url('i-want') }}" class="hero-banner banner-want">
                            <span class="hero-banner-label"><i class="fa-solid fa-cart-shopping"></i>People Want</span>
                            <h3 class="hero-banner-title">Want to buy? Tell us your need</h3>
                            <p class="hero-banner-text">Post your requirement and the right farmers will reach you.</p>
                            <span class="hero-banner-link">View all <i class="fa-solid fa-arrow-right ms-1"></i></span>
                            <i class="fa-solid fa-basket-shopping hero-banner-art" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('i-have') }}" class="hero-banner banner-have">
                            <span class="hero-banner-label"><i class="fa-solid fa-seedling"></i>People Have</span>
                            <h3 class="hero-banner-title">Want to sell? List your crop</h3>
                            <p class="hero-banner-text">Add photos, set your price and find buyers.</p>
                            <span class="hero-banner-link">View all <i
                                    class="fa-solid fa-arrow-right ms-1"></i></span>
                            <i class="fa-solid fa-wheat-awn hero-banner-art" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="trust-strip">
            <div class="trust-item">
                <i class="fa-solid fa-earth-asia"></i>
                <div>
                    <strong>Pan India Business</strong>
                    <span>High Growth Potential</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-handshake"></i>
                <div>
                    <strong>Direct Deals</strong>
                    <span>No Middleman</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-boxes-stacked"></i>
                <div>
                    <strong>Wide Range of Products</strong>
                    <span>One Stop Solution</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <div>
                    <strong>User Friendly Technology</strong>
                    <span>Unlimited Upload at Zero Cost</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-user-check"></i>
                <div>
                    <strong>Verified Profiles</strong>
                    <span>KYC Checked Members</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-headset"></i>
                <div>
                    <strong>Help</strong>
                    <span>Multilingual Support</span>
                </div>
            </div>
        </div>
    </div>
</section>