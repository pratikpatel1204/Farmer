{{-- resources/views/admin/survey/results.blade.php : sirf admin ko result dikhta hai (member ko nahi) --}}
@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Survey Results')

@php
    $totalResponses = $responses->total();
    $colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-info', 'bg-danger', 'bg-secondary'];
@endphp

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Survey Results</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($survey->name, 40) }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <div class="mb-2">
                    <a href="{{ route('admin.survey.questions.index', $survey) }}" class="btn btn-light d-flex align-items-center">
                        <i class="ti ti-list-details me-2"></i>Questions
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <span class="avatar avatar-lg bg-primary rounded-circle flex-shrink-0 me-3"><i class="ti ti-users fs-4"></i></span>
                        <div>
                            <p class="mb-1 text-muted">Total Responses</p>
                            <h4 class="mb-0">{{ number_format($totalResponses) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <span class="avatar avatar-lg bg-info rounded-circle flex-shrink-0 me-3"><i class="ti ti-help-circle fs-4"></i></span>
                        <div>
                            <p class="mb-1 text-muted">Questions</p>
                            <h4 class="mb-0">{{ $questions->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <span class="avatar avatar-lg {{ $survey->is_active ? 'bg-success' : 'bg-secondary' }} rounded-circle flex-shrink-0 me-3"><i class="ti ti-toggle-right fs-4"></i></span>
                        <div>
                            <p class="mb-1 text-muted">Status</p>
                            <h4 class="mb-0">{{ $survey->is_active ? 'Active' : 'Inactive' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Answers by Question</h5>
            </div>
            <div class="card-body">
                @forelse ($questions as $question)
                    @php
                        $qCounts = $counts[$question->id] ?? collect();
                        $qTotal = $qCounts->sum();
                    @endphp
                    <div class="mb-4 pb-3 {{ $loop->last ? '' : 'border-bottom' }}">
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <span class="avatar avatar-xs bg-primary rounded-circle flex-shrink-0">{{ $loop->iteration }}</span>
                            <div class="fw-medium">{{ \Illuminate\Support\Str::limit($question->question_text, 200) }}</div>
                        </div>
                        @foreach ($question->options as $no => $text)
                            @php
                                $count = (int) ($qCounts[$no] ?? 0);
                                $percent = $qTotal ? round($count * 100 / $qTotal) : 0;
                            @endphp
                            <div class="mb-2">
                                <div class="d-flex justify-content-between fs-13 mb-1">
                                    <span><strong>{{ chr(64 + $no) }}.</strong> {{ $text }}</span>
                                    <span class="text-muted">{{ $count }} ({{ $percent }}%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $colors[($no - 1) % count($colors)] }}" role="progressbar" style="width: {{ $percent }}%;"
                                        aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
                        <div class="fs-12 text-muted">{{ $qTotal }} {{ \Illuminate\Support\Str::plural('answer', $qTotal) }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No questions in this survey.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Members who took this survey</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Member</th>
                                <th>Mobile</th>
                                <th>Submitted On</th>
                                <th>Last Changed</th>
                                <th>Edit Window</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($responses as $response)
                                <tr>
                                    <td>{{ $responses->firstItem() + $loop->index }}</td>
                                    <td>
                                        <h6 class="fw-medium mb-0">{{ $response->user->full_name ?? $response->user->name ?? 'Deleted member' }}</h6>
                                        @if ($response->user?->unique_id)
                                            <span class="fs-12 text-muted">{{ $response->user->unique_id }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $response->user->mobile ?? '-' }}</td>
                                    <td>{{ $response->submitted_at?->format('d M Y, h:i A') }}</td>
                                    <td>{{ $response->updated_at?->format('d M Y, h:i A') }}</td>
                                    <td>
                                        @if ($response->canEdit())
                                            <span class="badge badge-warning badge-xs">Open till {{ $response->locked_at->format('d M, h:i A') }}</span>
                                        @else
                                            <span class="badge badge-success badge-xs">Locked</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No member has taken this survey yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($responses->hasPages())
                <div class="card-footer">{{ $responses->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
@endsection
