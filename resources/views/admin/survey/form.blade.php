{{-- resources/views/admin/survey/form.blade.php : Add / Edit survey --}}
@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || ' . ($survey->exists ? 'Edit Survey' : 'Add Survey'))

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ $survey->exists ? 'Edit Survey' : 'Add Survey' }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $survey->exists ? 'Edit' : 'Add' }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="mb-2">
                    <a href="{{ route('admin.survey.index') }}" class="btn btn-light d-flex align-items-center">
                        <i class="ti ti-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        @include('admin.survey._alerts')

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Survey Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ $survey->exists ? route('admin.survey.update', $survey) : route('admin.survey.store') }}">
                            @csrf
                            @if ($survey->exists)
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label class="form-label" for="name">Survey Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" maxlength="255"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $survey->name) }}" placeholder="e.g. Crop Price Survey - Rabi 2026">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">Short Description <span class="text-muted fs-12">(optional, shown on the survey page)</span></label>
                                <textarea id="description" name="description" rows="3" maxlength="1000"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Tell members why you are running this survey">{{ old('description', $survey->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="0" @selected((string) old('status', $survey->status) === '0')>Inactive</option>
                                    <option value="1" @selected((string) old('status', $survey->status) === '1')>Active</option>
                                </select>
                                <small class="text-muted d-block mt-1">Choosing Active will make all other surveys inactive.</small>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <a href="{{ route('admin.survey.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>{{ $survey->exists ? 'Update Survey' : 'Save & Add Questions' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
