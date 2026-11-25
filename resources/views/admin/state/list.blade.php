@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || State List')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">State List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">State</li>
                        <li class="breadcrumb-item active" aria-current="page">State List</li>
                    </ol>
                </nav>
            </div>
            <a href="#" data-bs-toggle="modal" data-bs-target="#addStateModal" class="btn btn-primary mt-2 mt-md-0">
                + Add State
            </a>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">State</h4>
                    </div>
                    <div class="card-body">
                        <div id="state_table_wrapper">
                            @include('admin.state.ajax_table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD STATE MODAL -->
    <div class="modal fade" id="addStateModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="addStateForm" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5>Add State</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>State Name</label>
                            <input type="text" name="name" class="form-control">
                            <div class="invalid-feedback">Please enter state name.</div>
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

    <!-- EDIT STATE MODAL -->
    <div class="modal fade" id="editStateModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editStateForm" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="edit_state_id">
                    <div class="modal-header">
                        <h5>Edit State</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>State Name</label>
                            <input type="text" name="name" id="edit_state_name" class="form-control">
                            <div class="invalid-feedback">Please enter state name.</div>
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
        let stateTable = null;

        function initDataTable() {
            if (stateTable) {
                stateTable.destroy();
                stateTable = null;
            }
            stateTable = $('#StatesTable').DataTable({
                pageLength: 10,
                ordering: true,
                searching: true,
                responsive: true,
                language: {
                    emptyTable: "No states found",
                    zeroRecords: "No matching states found"
                }
            });
        }

        function reloadStateTable() {
            $.ajax({
                url: "{{ route('admin.state.table') }}",
                type: "GET",
                success: function(res) {
                    $('#state_table_wrapper').html(res.html);
                    initDataTable();
                },
                error: function() {
                    toastr.error('Unable to load states');
                }
            });
        }

        $(document).ready(function() {
            initDataTable();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('#addStateModal').on('show.bs.modal', function() {
            $('#addStateForm')[0].reset();
            $('#addStateForm .is-invalid').removeClass('is-invalid');
        });

        $(document).on('click', '.editStateBtn', function() {
            $('#editStateForm .is-invalid').removeClass('is-invalid');
            $('#edit_state_id').val($(this).data('id'));
            $('#edit_state_name').val($(this).data('name'));
            $('#editStateModal').modal('show');
        });

        function validateStateForm($form) {
            let valid = true;
            $form.find('.is-invalid').removeClass('is-invalid');

            let name = $form.find('[name="name"]').val().trim();

            if (!name) {
                $form.find('[name="name"]').addClass('is-invalid');
                toastr.error('Please enter state name');
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

        // ADD STATE
        $('#addStateForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateStateForm($form)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.state.store') }}",
                type: "POST",
                data: $form.serialize(),
                success: function(res) {
                    toastr.success(res.message);
                    $('#addStateModal').modal('hide');
                    reloadStateTable();
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

        // UPDATE STATE
        $('#editStateForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateStateForm($form)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.state.update') }}",
                type: "POST",
                data: $form.serialize(),
                success: function(res) {
                    toastr.success(res.message);
                    $('#editStateModal').modal('hide');
                    reloadStateTable();
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

        // DELETE STATE
        $(document).on('click', '.deleteStateBtn', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "This state will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true);
                    $.ajax({
                        url: "{{ url('admin/state/state-delete') }}/" + id,
                        type: "POST",
                        data: { _method: "DELETE" },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                reloadStateTable();
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