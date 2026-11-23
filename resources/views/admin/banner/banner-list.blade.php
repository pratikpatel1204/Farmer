@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Banner List')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Banner List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-smart-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">Catalog</li>
                        <li class="breadcrumb-item active">Banner List</li>
                    </ol>
                </nav>
            </div>
            <a href="#" data-bs-toggle="modal" data-bs-target="#addBannerModal" class="btn btn-primary mt-2 mt-md-0">
                + Add Banner
            </a>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">Banners</h4>
                        <div style="max-width: 300px; width: 100%;">
                            <input type="text" id="bannerSearch" class="form-control" placeholder="Search by title...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="banner_table_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD BANNER MODAL -->
    <div class="modal fade" id="addBannerModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addBannerForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5>Add Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" maxlength="100"
                                    placeholder="Enter banner title">
                                <div class="invalid-feedback">Please enter banner title.</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea name="description" class="form-control banner-desc" rows="2" maxlength="150"
                                    placeholder="Write a short description (max 150 characters)"></textarea>
                                <small class="text-muted"><span class="desc-count">0</span>/150</small>
                                <div class="invalid-feedback">Please enter short description.</div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control banner-image-input"
                                    accept="image/png, image/jpeg, image/webp">
                                <div class="invalid-feedback">Please select a valid image (JPG, PNG, WEBP, max 2 MB).</div>
                                <img src="" alt="Preview" class="img-thumbnail mt-2 d-none banner-preview"
                                    style="max-height: 120px;">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
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

    <!-- EDIT BANNER MODAL -->
    <div class="modal fade" id="editBannerModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editBannerForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="edit_banner_id">
                    <div class="modal-header">
                        <h5>Edit Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" id="edit_banner_title" class="form-control"
                                    maxlength="100" placeholder="Enter banner title">
                                <div class="invalid-feedback">Please enter banner title.</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea name="description" id="edit_banner_description" class="form-control banner-desc" rows="2"
                                    maxlength="150" placeholder="Write a short description (max 150 characters)"></textarea>
                                <small class="text-muted"><span class="desc-count">0</span>/150</small>
                                <div class="invalid-feedback">Please enter short description.</div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Image <small class="text-muted">(leave empty to keep
                                        current)</small></label>
                                <input type="file" name="image" class="form-control banner-image-input"
                                    accept="image/png, image/jpeg, image/webp">
                                <div class="invalid-feedback">Please select a valid image (JPG, PNG, WEBP, max 2 MB).</div>
                                <img src="" alt="Preview" id="edit_banner_preview"
                                    class="img-thumbnail mt-2 banner-preview" style="max-height: 120px;">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_banner_status" class="form-select">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
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

        function reloadBannerTable(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: "{{ route('admin.banner.table') }}",
                type: "GET",
                data: {
                    page: page,
                    search: search
                },
                success: function(res) {
                    $('#banner_table_wrapper').html(res.html);
                },
                error: function() {
                    toastr.error('Unable to load banners');
                }
            });
        }

        $(document).ready(function() {
            reloadBannerTable(1, '');
        });

        // SEARCH (debounced)
        $(document).on('keyup', '#bannerSearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                reloadBannerTable(1, val);
            }, 400);
        });

        // PAGINATION
        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            reloadBannerTable($(this).data('page'), currentSearch);
        });

        // DESCRIPTION CHARACTER COUNT
        $(document).on('input', '.banner-desc', function() {
            $(this).closest('.mb-3').find('.desc-count').text(this.value.length);
        });

        // IMAGE PREVIEW + client-side check
        $(document).on('change', '.banner-image-input', function() {
            let $input = $(this);
            let $preview = $input.closest('.mb-3').find('.banner-preview');
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
        $('#addBannerModal').on('show.bs.modal', function() {
            $('#addBannerForm')[0].reset();
            $('#addBannerForm .is-invalid').removeClass('is-invalid');
            $('#addBannerForm .desc-count').text(0);
            $('#addBannerForm .banner-preview').addClass('d-none').attr('src', '');
        });

        // EDIT button
        $(document).on('click', '.editBannerBtn', function() {
            let $form = $('#editBannerForm');
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');

            let desc = $(this).data('description') || '';
            $('#edit_banner_id').val($(this).data('id'));
            $('#edit_banner_title').val($(this).data('title'));
            $('#edit_banner_description').val(desc);
            $form.find('.desc-count').text(desc.length);
            $('#edit_banner_status').val($(this).data('status'));
            $('#edit_banner_preview').attr('src', $(this).data('image'));
            $('#editBannerModal').modal('show');
        });

        function validateBannerForm($form, imageRequired) {
            let valid = true;
            $form.find('.is-invalid').removeClass('is-invalid');

            let title = $form.find('[name="title"]').val().trim();
            let desc = $form.find('[name="description"]').val().trim();
            let $image = $form.find('[name="image"]');

            if (!title) {
                $form.find('[name="title"]').addClass('is-invalid');
                toastr.error('Please enter banner title');
                valid = false;
            }

            if (!desc) {
                $form.find('[name="description"]').addClass('is-invalid');
                toastr.error('Please enter short description');
                valid = false;
            }

            if (imageRequired && !$image[0].files.length) {
                $image.addClass('is-invalid');
                toastr.error('Please select banner image');
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

        // ADD BANNER
        $('#addBannerForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateBannerForm($form, true)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.banner.store') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(res) {
                    toastr.success(res.message);
                    $('#addBannerModal').modal('hide');
                    reloadBannerTable(1, currentSearch);
                },
                error: function(xhr) {
                    handleError(xhr, $form);
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // UPDATE BANNER
        $('#editBannerForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateBannerForm($form, false)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.banner.update') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(res) {
                    toastr.success(res.message);
                    $('#editBannerModal').modal('hide');
                    reloadBannerTable(currentPage, currentSearch);
                },
                error: function(xhr) {
                    handleError(xhr, $form);
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // DELETE BANNER
        $(document).on('click', '.deleteBannerBtn', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "This banner will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true);
                    $.ajax({
                        url: "{{ url('admin/banner/banner-delete') }}/" + id,
                        type: "DELETE",
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                reloadBannerTable(currentPage, currentSearch);
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
