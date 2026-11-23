@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Level List')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Level List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-smart-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item active">Level List</li>
                    </ol>
                </nav>
            </div>
            <a href="#" data-bs-toggle="modal" data-bs-target="#addLevelModal" class="btn btn-primary mt-2 mt-md-0">
                + Add Level
            </a>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">Levels</h4>
                        <div style="max-width: 300px; width: 100%;">
                            <input type="text" id="levelSearch" class="form-control"
                                placeholder="Search by level or amount...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="level_table_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD LEVEL MODAL -->
    <div class="modal fade" id="addLevelModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="addLevelForm" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5>Add Level</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Level</label>
                            <input type="number" name="level" class="form-control" min="1" step="1"
                                placeholder="Enter level number (e.g. 1)">
                            <div class="invalid-feedback">Please enter a valid level number.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="amount" class="form-control" min="0" step="0.01"
                                    placeholder="Enter amount">
                                <div class="invalid-feedback">Please enter a valid amount.</div>
                            </div>
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

    <!-- EDIT LEVEL MODAL -->
    <div class="modal fade" id="editLevelModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editLevelForm" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="edit_level_id">
                    <div class="modal-header">
                        <h5>Edit Level</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Level</label>
                            <input type="number" name="level" id="edit_level_level" class="form-control" min="1"
                                step="1" placeholder="Enter level number (e.g. 1)">
                            <div class="invalid-feedback">Please enter a valid level number.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="amount" id="edit_level_amount" class="form-control"
                                    min="0" step="0.01" placeholder="Enter amount">
                                <div class="invalid-feedback">Please enter a valid amount.</div>
                            </div>
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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        function reloadLevelTable(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: "{{ route('admin.level.table') }}",
                type: "GET",
                data: {
                    page: page,
                    search: search
                },
                success: function(res) {
                    $('#level_table_wrapper').html(res.html);
                },
                error: function() {
                    toastr.error('Unable to load levels');
                }
            });
        }

        $(document).ready(function() {
            reloadLevelTable(1, '');
        });

        // SEARCH (debounced)
        $(document).on('keyup', '#levelSearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                reloadLevelTable(1, val);
            }, 400);
        });

        // PAGINATION
        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            reloadLevelTable($(this).data('page'), currentSearch);
        });

        // ADD modal reset
        $('#addLevelModal').on('show.bs.modal', function() {
            $('#addLevelForm')[0].reset();
            $('#addLevelForm .is-invalid').removeClass('is-invalid');
        });

        // EDIT button
        $(document).on('click', '.editLevelBtn', function() {
            $('#editLevelForm .is-invalid').removeClass('is-invalid');
            $('#edit_level_id').val($(this).data('id'));
            $('#edit_level_level').val($(this).data('level'));
            $('#edit_level_amount').val($(this).data('amount'));
            $('#editLevelModal').modal('show');
        });

        function validateLevelForm($form) {
            let valid = true;
            $form.find('.is-invalid').removeClass('is-invalid');

            let level = $form.find('[name="level"]').val().trim();
            let amount = $form.find('[name="amount"]').val().trim();

            if (!/^[1-9]\d*$/.test(level)) {
                $form.find('[name="level"]').addClass('is-invalid');
                toastr.error('Please enter a valid level number');
                valid = false;
            }

            if (amount === '' || isNaN(amount) || parseFloat(amount) < 0) {
                $form.find('[name="amount"]').addClass('is-invalid');
                toastr.error('Please enter a valid amount');
                valid = false;
            }

            return valid;
        }

        function toggleBtnLoader($btn, show) {
            $btn.prop('disabled', show);
            $btn.find('.spinner-border').toggleClass('d-none', !show);
        }

        function handleError(xhr, $form) {
            if (xhr.status === 422) {
                $.each(xhr.responseJSON.errors, function(k, v) {
                    toastr.error(v[0]);
                    $form.find('[name="' + k + '"]').addClass('is-invalid');
                });
            } else {
                toastr.error('Something went wrong. Please try again.');
            }
        }

        // ADD LEVEL
        $('#addLevelForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateLevelForm($form)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.level.store') }}",
                type: "POST",
                data: $form.serialize(),
                success: function(res) {
                    toastr.success(res.message);
                    $('#addLevelModal').modal('hide');
                    reloadLevelTable(1, currentSearch);
                },
                error: function(xhr) {
                    handleError(xhr, $form);
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // UPDATE LEVEL
        $('#editLevelForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateLevelForm($form)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.level.update') }}",
                type: "POST",
                data: $form.serialize(),
                success: function(res) {
                    toastr.success(res.message);
                    $('#editLevelModal').modal('hide');
                    reloadLevelTable(currentPage, currentSearch);
                },
                error: function(xhr) {
                    handleError(xhr, $form);
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // DELETE LEVEL
        $(document).on('click', '.deleteLevelBtn', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "This level will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true);
                    $.ajax({
                        url: "{{ url('admin/level/level-delete') }}/" + id,
                        type: "DELETE",
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                reloadLevelTable(currentPage, currentSearch);
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
