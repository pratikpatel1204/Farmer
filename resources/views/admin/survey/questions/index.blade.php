{{-- resources/views/admin/survey/questions/index.blade.php : ek survey ke sare questions --}}
@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Survey Questions')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ $survey->name }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Questions</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <div class="mb-2">
                    <a href="{{ route('admin.survey.index') }}" class="btn btn-light d-flex align-items-center">
                        <i class="ti ti-arrow-left me-2"></i>Surveys
                    </a>
                </div>
                <div class="mb-2">
                    <a href="{{ route('admin.survey.results', $survey) }}" class="btn btn-outline-primary d-flex align-items-center">
                        <i class="ti ti-chart-bar me-2"></i>Results
                    </a>
                </div>
                <div class="mb-2">
                    <a href="{{ route('admin.survey.questions.create', $survey) }}" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-circle-plus me-2"></i>Add Question
                    </a>
                </div>
            </div>
        </div>

        @include('admin.survey._alerts')

        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-lg {{ $survey->is_active ? 'bg-success' : 'bg-secondary' }} rounded-circle flex-shrink-0 me-3">
                        <i class="ti ti-clipboard-list fs-4"></i>
                    </span>
                    <div>
                        <h5 class="mb-1">{{ $survey->name }}</h5>
                        <p class="mb-0 text-muted">
                            {{ $questions->count() }} {{ \Illuminate\Support\Str::plural('question', $questions->count()) }}
                            @if ($survey->is_active)
                                · <span class="text-success fw-medium">Active on the website</span>
                            @else
                                · <span class="text-danger fw-medium">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.survey.status', $survey) }}">
                    @csrf
                    @if ($survey->is_active)
                        <button type="submit" class="btn btn-outline-danger"><i class="ti ti-player-pause me-1"></i>Make Inactive</button>
                    @else
                        <button type="submit" class="btn btn-success"><i class="ti ti-player-play me-1"></i>Make Active</button>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Questions</h5>
            </div>
            <div class="card-body">
                @forelse ($questions as $question)
                    <div class="border rounded p-3 mb-3 {{ $question->status ? '' : 'bg-light' }}">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                <span class="avatar avatar-sm bg-primary rounded-circle flex-shrink-0">{{ $loop->iteration }}</span>
                                <div class="flex-grow-1">
                                    <div class="survey-admin-question mb-2">{!! $question->question_html !!}</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($question->options as $no => $text)
                                            <span class="badge bg-light text-dark border fw-normal fs-13 px-2 py-1">
                                                <strong class="me-1">{{ chr(64 + $no) }}.</strong>{{ $text }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="fs-12 text-muted mt-2">
                                        Order: {{ $question->sort_order }}
                                        · {{ $question->answers_count }} {{ \Illuminate\Support\Str::plural('answer', $question->answers_count) }}
                                        @unless ($question->status)
                                            · <span class="text-danger">Hidden on website</span>
                                        @endunless
                                    </div>
                                </div>
                            </div>
                            <div class="action-icon d-inline-flex align-items-center flex-shrink-0">
                                <a href="{{ route('admin.survey.questions.edit', [$survey, $question]) }}" class="me-2" title="Edit"><i class="ti ti-edit"></i></a>
                                <form method="POST" action="{{ route('admin.survey.questions.destroy', [$survey, $question]) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this question? Member answers for this question will also be deleted.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 text-danger" title="Delete"><i class="ti ti-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-help-circle fs-1 d-block mb-2"></i>
                        No questions yet. Click "Add Question" to add the first one.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .survey-admin-question p:last-child { margin-bottom: 0; }
        .survey-admin-question img { max-width: 100%; height: auto; }
    </style>
@endsection
