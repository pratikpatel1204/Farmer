@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Create Permission')
@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Create Permission</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Permissions</li>
                        <li class="breadcrumb-item active" aria-current="page">Create Permission</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Create Permission</h5>
                    </div>
                    <div class="card-body">
                        <form id="permissionForm">
                            @csrf

                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Enter permission name" required>
                                    <span class="text-danger error-name"></span>
                                </div>

                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Group Name <span class="text-danger">*</span></label>
                                    <select name="group_name" id="group_name" class="form-select select2" required>
                                        <option value="">-- Select Group --</option>
                                        @foreach ($permissionGroups as $group)
                                            <option value="{{ $group }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-group_name"></span>
                                </div>

                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Guard <span class="text-danger">*</span></label>
                                    <select name="guard_name" id="guard_name" class="form-select" required>
                                        @foreach ($guards as $guard)
                                            <option value="{{ $guard }}"
                                                {{ old('guard_name', 'admin') == $guard ? 'selected' : '' }}>
                                                {{ $guard === 'admin' ? 'Admin (admin panel)' : 'Web (members / users)' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-guard_name"></span>
                                </div>
                            </div>

                            <div class="text-end">
                                @can('View Permissions')
                                    <a href="{{ route('admin.permissions.list') }}" class="btn btn-secondary">Cancel</a>
                                @endcan
                                <button type="submit" id="saveBtn" class="btn btn-primary">
                                    <span class="btn-text">Create Permission</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            function resetBtn() {
                $("#saveBtn").attr("disabled", false);
                $("#saveBtn .btn-text").removeClass('d-none');
                $("#saveBtn .spinner-border").addClass('d-none');
            }

            $("#permissionForm").on('submit', function(e) {
                e.preventDefault();

                $(".text-danger[class*='error-']").text("");
                $("#saveBtn").attr("disabled", true);
                $("#saveBtn .btn-text").addClass('d-none');
                $("#saveBtn .spinner-border").removeClass('d-none');

                $.ajax({
                    url: "{{ route('admin.permissions.store') }}",
                    type: "POST",
                    data: $(this).serialize(),

                    success: function(res) {
                        toastr.success(res.message ?? "Permission created successfully");

                        // Guard ko reset ke baad wapas wahi rakhne ke liye pehle save karte hain
                        let guard = $("#guard_name").val();
                        $("#permissionForm")[0].reset();
                        $("#group_name").val('').trigger('change');
                        $("#guard_name").val(guard);

                        resetBtn();
                    },

                    error: function(xhr) {
                        resetBtn();

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;

                            ['name', 'group_name', 'guard_name'].forEach(function(field) {
                                if (errors[field]) {
                                    $('.error-' + field).text(errors[field][0]);
                                    toastr.error(errors[field][0]);
                                }
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                    }
                });
            });
        });
    </script>
@endsection
