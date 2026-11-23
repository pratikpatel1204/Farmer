@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || City List')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">City List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-smart-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">Location</li>
                        <li class="breadcrumb-item active">City List</li>
                    </ol>
                </nav>
            </div>
            <a href="#" data-bs-toggle="modal" data-bs-target="#addCityModal" class="btn btn-primary mt-2 mt-md-0">
                + Add City
            </a>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">Cities</h4>
                        <div style="max-width: 300px; width: 100%;">
                            <input type="text" id="citySearch" class="form-control"
                                placeholder="Search by city or state name...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="city_table_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD CITY MODAL -->
    <div class="modal fade" id="addCityModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="addCityForm" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5>Add City</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>State</label>
                            <select name="state_id" class="form-select">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select state.</div>
                        </div>
                        <div class="mb-3">
                            <label>City Name</label>
                            <input type="text" name="name" class="form-control">
                            <div class="invalid-feedback">Please enter city name.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-save">
                            <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                            <span class="btn-text">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT CITY MODAL -->
    <div class="modal fade" id="editCityModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editCityForm" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="edit_city_id">
                    <div class="modal-header">
                        <h5>Edit City</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>State</label>
                            <select name="state_id" id="edit_city_state" class="form-select">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select state.</div>
                        </div>
                        <div class="mb-3">
                            <label>City Name</label>
                            <input type="text" name="name" id="edit_city_name" class="form-control">
                            <div class="invalid-feedback">Please enter city name.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-save">
                            <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                            <span class="btn-text">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentSearch = '';
        let searchTimer = null;

        function reloadCityTable(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: "{{ route('admin.city.table') }}",
                type: "GET",
                data: {
                    page: page,
                    search: search
                },
                success: function(res) {
                    $('#city_table_wrapper').html(res.html);
                },
                error: function() {
                    toastr.error('Unable to load cities');
                }
            });
        }

        $(document).ready(function() {
            reloadCityTable(1, '');
        });

        // SEARCH (debounced)
        $(document).on('keyup', '#citySearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                reloadCityTable(1, val);
            }, 400);
        });

        // PAGINATION CLICK
        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            let page = $(this).data('page');
            reloadCityTable(page, currentSearch);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('#addCityModal').on('show.bs.modal', function() {
            $('#addCityForm')[0].reset();
            $('#addCityForm .is-invalid').removeClass('is-invalid');
        });

        $(document).on('click', '.editCityBtn', function() {
            $('#editCityForm .is-invalid').removeClass('is-invalid');
            $('#edit_city_id').val($(this).data('id'));
            $('#edit_city_state').val($(this).data('state'));
            $('#edit_city_name').val($(this).data('name'));
            $('#editCityModal').modal('show');
        });

        function validateCityForm($form) {
            let valid = true;
            $form.find('.is-invalid').removeClass('is-invalid');

            let stateId = $form.find('[name="state_id"]').val();
            let name = $form.find('[name="name"]').val().trim();

            if (!stateId) {
                $form.find('[name="state_id"]').addClass('is-invalid');
                toastr.error('Please select state');
                valid = false;
            }

            if (!name) {
                $form.find('[name="name"]').addClass('is-invalid');
                toastr.error('Please enter city name');
                valid = false;
            }

            return valid;
        }

        function toggleBtnLoader($btn, show) {
            if (show) {
                $btn.prop('disabled', true);
                $btn.find('.spinner-border').removeClass('d-none');
            } else {
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
            }
        }

        // ADD CITY
        $('#addCityForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateCityForm($form)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.city.store') }}",
                type: "POST",
                data: $form.serialize(),
                success: function(res) {
                    toastr.success(res.message);
                    $('#addCityModal').modal('hide');
                    reloadCityTable(1, currentSearch);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(k, v) {
                            toastr.error(v[0]);
                            $form.find('[name="' + k + '"]').addClass('is-invalid');
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // UPDATE CITY
        $('#editCityForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateCityForm($form)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.city.update') }}",
                type: "POST",
                data: $form.serialize(),
                success: function(res) {
                    toastr.success(res.message);
                    $('#editCityModal').modal('hide');
                    reloadCityTable(currentPage, currentSearch);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(k, v) {
                            toastr.error(v[0]);
                            $form.find('[name="' + k + '"]').addClass('is-invalid');
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // DELETE CITY
        $(document).on('click', '.deleteCityBtn', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "This city will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true);
                    $.ajax({
                        url: "{{ url('admin/city/city-delete') }}/" + id,
                        type: "DELETE",
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                reloadCityTable(currentPage, currentSearch);
                            } else {
                                toastr.error(res.message);
                                $btn.prop('disabled', false);
                            }
                        },
                        error: function() {
                            toastr.error('Something went wrong.');
                            $btn.prop('disabled', false);
                        }
                    });
                }
            });
        });
    </script>
@endsection