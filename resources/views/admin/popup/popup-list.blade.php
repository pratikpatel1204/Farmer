@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Popup List')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Popup List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-smart-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">Catalog</li>
                        <li class="breadcrumb-item active">Popup List</li>
                    </ol>
                </nav>
            </div>
            <a href="#" data-bs-toggle="modal" data-bs-target="#addPopupModal" class="btn btn-primary mt-2 mt-md-0">
                + Add Popup
            </a>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">Popups</h4>
                        <div style="max-width: 300px; width: 100%;">
                            <input type="text" id="popupSearch" class="form-control" placeholder="Search by title...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="popup_table_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD POPUP MODAL -->
    <div class="modal fade" id="addPopupModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addPopupForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5>Add Popup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" maxlength="150"
                                    placeholder="Enter popup title">
                                <div class="invalid-feedback">Please enter popup title.</div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Button URL</label>
                                <input type="text" name="btn_url" class="form-control" maxlength="255"
                                    placeholder="https://example.com/register">
                                <div class="invalid-feedback">Please enter a valid URL (starting with http:// or https://).
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control popup-image-input"
                                    accept="image/png, image/jpeg, image/webp">
                                <div class="invalid-feedback">Please select a valid image (JPG, PNG, WEBP, max 2 MB).</div>
                                <img src="" alt="Preview" class="img-thumbnail mt-2 d-none popup-preview"
                                    style="max-height: 120px;">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea name="short_description" class="form-control popup-desc" rows="2" maxlength="150"
                                    placeholder="Write a short description (max 150 characters)"></textarea>
                                <small class="text-muted"><span class="desc-count">0</span>/150</small>
                                <div class="invalid-feedback">Please enter short description.</div>
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

    <!-- EDIT POPUP MODAL -->
    <div class="modal fade" id="editPopupModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editPopupForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="edit_popup_id">
                    <div class="modal-header">
                        <h5>Edit Popup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" id="edit_popup_title" class="form-control"
                                    maxlength="150" placeholder="Enter popup title">
                                <div class="invalid-feedback">Please enter popup title.</div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Button URL</label>
                                <input type="text" name="btn_url" id="edit_popup_btn_url" class="form-control"
                                    maxlength="255" placeholder="https://example.com/register">
                                <div class="invalid-feedback">Please enter a valid URL (starting with http:// or https://).
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Image <small class="text-muted">(leave empty to keep
                                        current)</small></label>
                                <input type="file" name="image" class="form-control popup-image-input"
                                    accept="image/png, image/jpeg, image/webp">
                                <div class="invalid-feedback">Please select a valid image (JPG, PNG, WEBP, max 2 MB).</div>
                                <img src="" alt="Preview" id="edit_popup_preview"
                                    class="img-thumbnail mt-2 popup-preview" style="max-height: 120px;">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_popup_status" class="form-select">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea name="short_description" id="edit_popup_description" class="form-control popup-desc" rows="2"
                                    maxlength="150" placeholder="Write a short description (max 150 characters)"></textarea>
                                <small class="text-muted"><span class="desc-count">0</span>/150</small>
                                <div class="invalid-feedback">Please enter short description.</div>
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
        const MAX_IMAGE_SIZE = 2 * 1024 * 1024; // 2 MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        function reloadPopupTable(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: "{{ route('admin.popup.table') }}",
                type: "GET",
                data: {
                    page: page,
                    search: search
                },
                success: function(res) {
                    $('#popup_table_wrapper').html(res.html);
                },
                error: function() {
                    toastr.error('Unable to load popups');
                }
            });
        }

        $(document).ready(function() {
            reloadPopupTable(1, '');
        });

        // SEARCH (debounced)
        $(document).on('keyup', '#popupSearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                reloadPopupTable(1, val);
            }, 400);
        });

        // PAGINATION
        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            reloadPopupTable($(this).data('page'), currentSearch);
        });

        // DESCRIPTION CHARACTER COUNT
        $(document).on('input', '.popup-desc', function() {
            $(this).closest('.mb-3').find('.desc-count').text(this.value.length);
        });

        // IMAGE PREVIEW + client-side check
        $(document).on('change', '.popup-image-input', function() {
            let $input = $(this);
            let $preview = $input.closest('.mb-3').find('.popup-preview');
            let file = this.files[0];

            $input.removeClass('is-invalid');
            if (!file) {
                return;
            }

            if (!ALLOWED_TYPES.includes(file.type) || file.size > MAX_IMAGE_SIZE) {
                $input.addClass('is-invalid').val('');
                toastr.error('Image must be JPG, PNG or WEBP and under 2 MB');
                return;
            }

            let reader = new FileReader();
            reader.onload = function(e) {
                $preview.attr('src', e.target.result).removeClass('d-none');
            };
            reader.readAsDataURL(file);
        });

        // ADD modal reset
        $('#addPopupModal').on('show.bs.modal', function() {
            $('#addPopupForm')[0].reset();
            $('#addPopupForm .is-invalid').removeClass('is-invalid');
            $('#addPopupForm .desc-count').text(0);
            $('#addPopupForm .popup-preview').addClass('d-none').attr('src', '');
        });

        // EDIT button
        $(document).on('click', '.editPopupBtn', function() {
            let $form = $('#editPopupForm');
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');

            let desc = $(this).attr('data-description') || '';
            $('#edit_popup_id').val($(this).data('id'));
            $('#edit_popup_title').val($(this).attr('data-title'));
            $('#edit_popup_btn_url').val($(this).attr('data-btn-url'));
            $('#edit_popup_description').val(desc);
            $form.find('.desc-count').text(desc.length);
            $('#edit_popup_status').val($(this).attr('data-status'));
            $('#edit_popup_preview').attr('src', $(this).attr('data-image'));
            $('#editPopupModal').modal('show');
        });

        function validatePopupForm($form, imageRequired) {
            let valid = true;
            $form.find('.is-invalid').removeClass('is-invalid');

            let title = $form.find('[name="title"]').val().trim();
            let url = $form.find('[name="btn_url"]').val().trim();
            let desc = $form.find('[name="short_description"]').val().trim();
            let $image = $form.find('[name="image"]');

            if (!title) {
                $form.find('[name="title"]').addClass('is-invalid');
                toastr.error('Please enter popup title');
                valid = false;
            }

            if (!/^https?:\/\/\S+\.\S+/i.test(url)) {
                $form.find('[name="btn_url"]').addClass('is-invalid');
                toastr.error('Please enter a valid button URL');
                valid = false;
            }

            if (!desc) {
                $form.find('[name="short_description"]').addClass('is-invalid');
                toastr.error('Please enter short description');
                valid = false;
            }

            if (imageRequired && !$image[0].files.length) {
                $image.addClass('is-invalid');
                toastr.error('Please select popup image');
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

        // ADD POPUP
        $('#addPopupForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validatePopupForm($form, true)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.popup.store') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(res) {
                    toastr.success(res.message);
                    $('#addPopupModal').modal('hide');
                    reloadPopupTable(1, currentSearch);
                },
                error: function(xhr) {
                    handleError(xhr, $form);
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // UPDATE POPUP
        $('#editPopupForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validatePopupForm($form, false)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.popup.update') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(res) {
                    toastr.success(res.message);
                    $('#editPopupModal').modal('hide');
                    reloadPopupTable(currentPage, currentSearch);
                },
                error: function(xhr) {
                    handleError(xhr, $form);
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // DELETE POPUP
        $(document).on('click', '.deletePopupBtn', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "This popup will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true);
                    $.ajax({
                        url: "{{ url('admin/popup/popup-delete') }}/" + id,
                        type: "DELETE",
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                reloadPopupTable(currentPage, currentSearch);
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
