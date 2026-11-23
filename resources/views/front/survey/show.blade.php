{{-- resources/views/front/survey/show.blade.php
     Variables (Front\SurveyController@show):
       $survey     Active survey ya null
       $questions  survey ke Show wale question (sort_order se)
       $response   login member ka submission ya null
       $answers    [question_id => option_no] (pehle diye hue answer) --}}
@extends('front.layout.app')

@section('title', ($survey->name ?? 'Survey') . ' - KisanMandi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/survey.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('front/js/survey.js') }}"></script>
@endpush

@php
    $isGuest = ! auth('web')->check();
    $canEdit = $response && $response->canEdit();
    // Galat submit ke baad (validation error) form khula rahe
    $openForm = ! $response || ($canEdit && $errors->any());
    $loginUrl = route('login', ['redirect' => '/survey']);
@endphp

@section('content')
<div class="inner-page survey-page">

    @include('front.partials.page-hero', [
        'title' => $survey->name ?? 'Survey',
        'text' => $survey
            ? ($survey->description ?: 'Your answers help us make KisanMandi better for farmers and buyers. There are no right or wrong answers.')
            : 'There is no survey running right now. Please check again later.',
        'badge' => ['icon' => 'fa-clipboard-question', 'label' => 'Survey'],
        'variant' => 'sun',
        'art' => 'fa-square-poll-vertical',
        'crumbs' => ['Survey' => null],
    ])

    <section class="page-section">
        <div class="container-xl">
            <div class="survey-wrap">

                @if (session('survey_success') && ! $response)
                    <div class="farm-alert farm-alert-success mb-3" role="status"><i class="fa-solid fa-circle-check"></i>{{ session('survey_success') }}</div>
                @endif
                @if (session('survey_error'))
                    <div class="farm-alert farm-alert-error mb-3" role="alert"><i class="fa-solid fa-triangle-exclamation"></i>{{ session('survey_error') }}</div>
                @endif

                @if (! $survey || $questions->isEmpty())
                    {{-- Koi Active survey nahi --}}
                    <div class="empty-state">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <h3>No survey right now</h3>
                        <p>There is no active survey at the moment. Please check again later.</p>
                        <a href="{{ url('/') }}" class="btn btn-farm"><i class="fa-solid fa-house me-1"></i>Back to home</a>
                    </div>
                @else

                    @if ($isGuest)
                        {{-- Guest: pehle login --}}
                        <div class="survey-login mb-4">
                            <i class="fa-solid fa-user-lock survey-login-icon" aria-hidden="true"></i>
                            <div class="survey-login-body">
                                <h2>Please log in to take this survey</h2>
                                <p>You can read all the questions below. To answer and submit, please log in to your KisanMandi account.</p>
                            </div>
                            <div class="survey-login-actions">
                                <a href="{{ $loginUrl }}" class="btn btn-farm"><i class="fa-solid fa-right-to-bracket me-1"></i>Log in</a>
                                <a href="{{ url('register') }}" class="btn btn-farm-outline">Register</a>
                            </div>
                        </div>
                    @endif

                    @if ($response)
                        {{-- Submit ho chuka: sirf Thank you (koi score / result nahi) --}}
                        <div class="survey-thanks mb-4">
                            <span class="survey-thanks-icon"><i class="fa-solid fa-hands-clapping"></i></span>
                            <h2>Thank you for taking part!</h2>
                            <p>{{ session('survey_success') === 'Thank you! Your answers have been updated.' ? 'Your answers have been updated.' : 'Your answers have been saved.' }} They help us make KisanMandi better for everyone.</p>

                            @if ($canEdit)
                                <div class="survey-edit-note">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>You can change your answers until <strong>{{ $response->locked_at->format('d M Y, h:i A') }}</strong> ({{ $response->locked_at->diffForHumans() }}). After that your answers will be locked.</span>
                                </div>
                                <div></div>
                                <button type="button" class="btn btn-farm-outline mt-3" data-survey-edit aria-controls="surveyForm"
                                    aria-expanded="{{ $openForm ? 'true' : 'false' }}" @if ($openForm) hidden @endif>
                                    <i class="fa-solid fa-pen me-1"></i>Change my answers
                                </button>
                            @else
                                <div class="survey-edit-note is-locked">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>Your answers are locked. Each survey can be taken only once.</span>
                                </div>
                            @endif

                            <div class="mt-3">
                                <a href="{{ url('/') }}" class="survey-home-link"><i class="fa-solid fa-arrow-left me-1"></i>Back to home</a>
                            </div>
                        </div>
                    @endif

                    @if ($isGuest || ! $response || $canEdit)
                        <form method="POST" action="{{ route('survey.submit') }}" id="surveyForm" class="survey-form" novalidate
                            data-survey-form @if (! $openForm && ! $isGuest) hidden @endif>
                            @csrf
                            <input type="hidden" name="survey_id" value="{{ $survey->id }}">

                            <div class="survey-count mb-3">
                                <span><i class="fa-solid fa-list-check me-1"></i>{{ $questions->count() }} {{ \Illuminate\Support\Str::plural('question', $questions->count()) }}</span>
                                @unless ($isGuest)
                                    <span>Pick one option for each question</span>
                                @endunless
                            </div>

                            @foreach ($questions as $question)
                                @php
                                    $selected = (int) old('answers.' . $question->id, $answers[$question->id] ?? 0);
                                    $errorKey = 'answers.' . $question->id;
                                @endphp
                                <div class="survey-question @error($errorKey) is-missing @enderror" id="question-{{ $question->id }}"
                                    data-survey-question role="radiogroup" aria-labelledby="question-title-{{ $question->id }}">
                                    <div class="survey-q-head">
                                        <span class="survey-q-no">Q{{ $loop->iteration }}</span>
                                        <div class="survey-q-text rich-text" id="question-title-{{ $question->id }}">{!! $question->question_html !!}</div>
                                    </div>

                                    <div class="survey-options">
                                        @foreach ($question->options as $no => $text)
                                            <label class="survey-option {{ $selected === $no ? 'is-checked' : '' }} {{ $isGuest ? 'is-disabled' : '' }}">
                                                <input type="radio" class="survey-option-input" name="answers[{{ $question->id }}]"
                                                    value="{{ $no }}" @checked($selected === $no) @disabled($isGuest)>
                                                <span class="survey-option-letter">{{ chr(64 + $loop->iteration) }}</span>
                                                <span class="survey-option-text">{{ $text }}</span>
                                                <i class="fa-solid fa-circle-check survey-option-tick" aria-hidden="true"></i>
                                            </label>
                                        @endforeach
                                    </div>

                                    <p class="survey-q-error" data-survey-error @unless ($errors->has($errorKey)) hidden @endunless>
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first($errorKey) ?: 'Please select an answer.' }}
                                    </p>
                                </div>
                            @endforeach

                            @if ($isGuest)
                                <div class="survey-submit-bar">
                                    <span class="survey-progress-text">Log in to answer these questions.</span>
                                    <a href="{{ $loginUrl }}" class="btn btn-farm"><i class="fa-solid fa-right-to-bracket me-1"></i>Log in to answer</a>
                                </div>
                            @else
                                <div class="survey-submit-bar">
                                    <div class="survey-progress">
                                        <span class="survey-progress-text" data-survey-progress-text>0 of {{ $questions->count() }} answered</span>
                                        <span class="survey-progress-track"><span class="survey-progress-fill" data-survey-progress-fill></span></span>
                                    </div>
                                    <button type="submit" class="btn btn-farm" data-survey-submit>
                                        @if ($response)
                                            <i class="fa-solid fa-floppy-disk me-1"></i>Update answers
                                        @else
                                            <i class="fa-solid fa-paper-plane me-1"></i>Submit survey
                                        @endif
                                    </button>
                                </div>
                                @unless ($response)
                                    <p class="survey-footnote"><i class="fa-solid fa-circle-info me-1"></i>After you submit, you can change your answers for 24 hours. After that they are locked.</p>
                                @endunless
                            @endif
                        </form>
                    @endif
                @endif

            </div>
        </div>
    </section>
</div>
@endsection
