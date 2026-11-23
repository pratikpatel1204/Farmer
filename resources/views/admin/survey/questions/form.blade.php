{{-- resources/views/admin/survey/questions/form.blade.php : Add / Edit question (CKEditor) --}}
@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || ' . ($question->exists ? 'Edit Question' : 'Add Question'))

@php
    $maxOptions = \App\Models\SurveyQuestion::MAX_OPTIONS;
    $answered = $question->exists ? $question->answers()->count() : 0;
@endphp

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ $question->exists ? 'Edit Question' : 'Add Question' }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey.questions.index', $survey) }}">{{ \Illuminate\Support\Str::limit($survey->name, 30) }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $question->exists ? 'Edit' : 'Add' }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="mb-2">
                    <a href="{{ route('admin.survey.questions.index', $survey) }}" class="btn btn-light d-flex align-items-center">
                        <i class="ti ti-arrow-left me-2"></i>Back to Questions
                    </a>
                </div>
            </div>
        </div>

        @include('admin.survey._alerts')

        @if ($answered)
            <div class="alert alert-warning d-flex align-items-start" role="alert">
                <i class="ti ti-alert-triangle fs-5 me-2"></i>
                <div>{{ $answered }} {{ \Illuminate\Support\Str::plural('member', $answered) }} already answered this question. If you change the meaning of an option, old answers will count for the new text.</div>
            </div>
        @endif

        <form method="POST"
            action="{{ $question->exists ? route('admin.survey.questions.update', [$survey, $question]) : route('admin.survey.questions.store', $survey) }}"
            id="surveyQuestionForm">
            @csrf
            @if ($question->exists)
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Question</h5>
                        </div>
                        <div class="card-body">
                            <label class="form-label" for="question">Question Text <span class="text-danger">*</span></label>
                            <div class="@error('question') is-invalid border border-danger rounded @enderror">
                                <textarea id="question" name="question" rows="5" class="form-control">{{ old('question', $question->question) }}</textarea>
                            </div>
                            @error('question')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">Options</h5>
                            <span class="fs-12 text-muted">Up to {{ $maxOptions }} options. Leave extra boxes empty, they will not be shown.</span>
                        </div>
                        <div class="card-body">
                            @error('options')
                                <div class="alert alert-danger py-2">{{ $message }}</div>
                            @enderror
                            <div class="row g-3">
                                @for ($i = 1; $i <= $maxOptions; $i++)
                                    <div class="col-md-6">
                                        <label class="form-label" for="option_{{ $i }}">
                                            Option {{ chr(64 + $i) }} @if ($i <= 2)<span class="text-danger">*</span>@endif
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text fw-semibold">{{ chr(64 + $i) }}</span>
                                            <input type="text" id="option_{{ $i }}" name="option_{{ $i }}" maxlength="255"
                                                class="form-control @error('option_' . $i) is-invalid @enderror"
                                                value="{{ old('option_' . $i, $question->{'option_' . $i}) }}"
                                                placeholder="{{ $i <= 2 ? 'Required' : 'Optional' }}">
                                            @error('option_' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <p class="fs-12 text-muted mb-0 mt-3"><i class="ti ti-info-circle me-1"></i>This is a survey, so there is no correct answer. Members just pick the option that fits them.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="sort_order">Sort Order</label>
                                <input type="number" id="sort_order" name="sort_order" min="0" max="9999"
                                    class="form-control @error('sort_order') is-invalid @enderror"
                                    value="{{ old('sort_order', $question->sort_order) }}">
                                <small class="text-muted">Smaller number comes first. The first 2-3 questions are shown on the home page.</small>
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label" for="status">Status</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="1" @selected((string) old('status', $question->status) === '1')>Show</option>
                                    <option value="0" @selected((string) old('status', $question->status) === '0')>Hide</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body d-grid gap-2">
                            <button type="submit" name="save_action" value="save" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>{{ $question->exists ? 'Update Question' : 'Save Question' }}
                            </button>
                            @unless ($question->exists)
                                <button type="submit" name="save_action" value="save_new" class="btn btn-outline-primary">
                                    <i class="ti ti-plus me-1"></i>Save & Add Another
                                </button>
                            @endunless
                            <a href="{{ route('admin.survey.questions.index', $survey) }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- CKEditor 5 (Classic). Layout me pehle se CKEditor ho to ye script line hata dein. --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        (function () {
            if (typeof ClassicEditor === 'undefined') return;
            ClassicEditor.create(document.querySelector('#question'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo']
            }).then(function (editor) {
                // Submit se pehle editor ka HTML textarea me
                document.getElementById('surveyQuestionForm').addEventListener('submit', function () {
                    editor.updateSourceElement();
                });
            }).catch(function (error) {
                console.error(error);
            });
        })();
    </script>
    <style>
        .ck-editor__editable_inline { min-height: 180px; }
    </style>
@endsection
