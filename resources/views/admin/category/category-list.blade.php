@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Category List')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Category List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-smart-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">Catalog</li>
                        <li class="breadcrumb-item active">Category List</li>
                    </ol>
                </nav>
            </div>
            <a href="#" data-bs-toggle="modal" data-bs-target="#addCategoryModal"
                class="btn btn-primary mt-2 mt-md-0">
                + Add Category
            </a>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">Categories</h4>
                        <div style="max-width: 300px; width: 100%;">
                            <input type="text" id="categorySearch" class="form-control"
                                placeholder="Search by category name...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="category_table_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD CATEGORY MODAL -->
    <div class="modal fade" id="addCategoryModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="addCategoryForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5>Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter category name">
                            <div class="invalid-feedback">Please enter category name.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control category-image-input"
                                accept="image/png, image/jpeg, image/webp">
                            <div class="invalid-feedback">Please select a valid image (JPG, PNG, WEBP, max 2 MB).</div>
                            <img src="" alt="Preview" class="img-thumbnail mt-2 d-none category-preview"
                                style="max-height: 120px;">
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

    <!-- EDIT CATEGORY MODAL -->
    <div class="modal fade" id="editCategoryModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editCategoryForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="edit_category_id">
                    <div class="modal-header">
                        <h5>Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" id="edit_category_name" class="form-control"
                                placeholder="Enter category name">
                            <div class="invalid-feedback">Please enter category name.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image <small class="text-muted">(leave empty to keep
                                    current)</small></label>
                            <input type="file" name="image" class="form-control category-image-input"
                                accept="image/png, image/jpeg, image/webp">
                            <div class="invalid-feedback">Please select a valid image (JPG, PNG, WEBP, max 2 MB).</div>
                            <img src="" alt="Preview" id="edit_category_preview"
                                class="img-thumbnail mt-2 category-preview" style="max-height: 120px;">
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

        function reloadCategoryTable(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: "{{ route('admin.category.table') }}",
                type: "GET",
                data: {
                    page: page,
                    search: search
                },
                success: function(res) {
                    $('#category_table_wrapper').html(res.html);
                },
                error: function() {
                    toastr.error('Unable to load categories');
                }
            });
        }

        $(document).ready(function() {
            reloadCategoryTable(1, '');
        });

        // SEARCH (debounced)
        $(document).on('keyup', '#categorySearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                reloadCategoryTable(1, val);
            }, 400);
        });

        // PAGINATION
        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            reloadCategoryTable($(this).data('page'), currentSearch);
        });

        // IMAGE PREVIEW + client-side check
        $(document).on('change', '.category-image-input', function() {
            let $input = $(this);
            let $preview = $input.closest('.mb-3').find('.category-preview');
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
        $('#addCategoryModal').on('show.bs.modal', function() {
            $('#addCategoryForm')[0].reset();
            $('#addCategoryForm .is-invalid').removeClass('is-invalid');
            $('#addCategoryForm .category-preview').addClass('d-none').attr('src', '');
        });

        // EDIT button
        $(document).on('click', '.editCategoryBtn', function() {
            let $form = $('#editCategoryForm');
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
            $('#edit_category_id').val($(this).data('id'));
            $('#edit_category_name').val($(this).data('name'));
            $('#edit_category_preview').attr('src', $(this).data('image'));
            $('#editCategoryModal').modal('show');
        });

        function validateCategoryForm($form, imageRequired) {
            let valid = true;
            $form.find('.is-invalid').removeClass('is-invalid');

            let name = $form.find('[name="name"]').val().trim();
            let $image = $form.find('[name="image"]');

            if (!name) {
                $form.find('[name="name"]').addClass('is-invalid');
                toastr.error('Please enter category name');
                valid = false;
            }

            if (imageRequired && !$image[0].files.length) {
                $image.addClass('is-invalid');
                toastr.error('Please select category image');
                valid = false;
            }

            return valid;
        }

        function toggleBtnLoader($btn, show) {
            $btn.prop('disabled', show);
            $btn.find('.spinner-border').toggleClass('d-none', !show);
        }

        function handle422(xhr, $form) {
            if (xhr.status === 422) {
                $.each(xhr.responseJSON.errors, function(k, v) {
                    toastr.error(v[0]);
                    $form.find('[name="' + k + '"]').addClass('is-invalid');
                });
            } else {
                toastr.error('Something went wrong. Please try again.');
            }
        }

        // ADD CATEGORY
        $('#addCategoryForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateCategoryForm($form, true)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.category.store') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(res) {
                    toastr.success(res.message);
                    $('#addCategoryModal').modal('hide');
                    reloadCategoryTable(1, currentSearch);
                },
                error: function(xhr) {
                    handle422(xhr, $form);
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // UPDATE CATEGORY
        $('#editCategoryForm').submit(function(e) {
            e.preventDefault();
            let $form = $(this);
            let $btn = $form.find('.btn-save');

            if (!validateCategoryForm($form, false)) {
                return;
            }

            toggleBtnLoader($btn, true);

            $.ajax({
                url: "{{ route('admin.category.update') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(res) {
                    toastr.success(res.message);
                    $('#editCategoryModal').modal('hide');
                    reloadCategoryTable(currentPage, currentSearch);
                },
                error: function(xhr) {
                    handle422(xhr, $form);
                },
                complete: function() {
                    toggleBtnLoader($btn, false);
                }
            });
        });

        // DELETE CATEGORY
        $(document).on('click', '.deleteCategoryBtn', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "This category will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true);
                    $.ajax({
                        url: "{{ url('admin/category/category-delete') }}/" + id,
                        type: "POST",
                        data: { _method: "DELETE" },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                reloadCategoryTable(currentPage, currentSearch);
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