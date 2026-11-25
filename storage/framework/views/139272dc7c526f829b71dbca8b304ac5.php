<?php $__env->startSection('title', config('app.name') . ' || Sub Category List'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Sub Category List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Master Setting</li>
                        <li class="breadcrumb-item active">Sub Category List</li>
                    </ol>
                </nav>
            </div>
            <a href="#" data-bs-toggle="modal" data-bs-target="#addSubCategoryModal" class="btn btn-primary mt-2 mt-md-0">
                + Add Sub Category
            </a>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center gap-2">
                        <h4 class="card-title mb-2 mb-md-0">Sub Categories</h4>
                        <div class="d-flex flex-column flex-sm-row gap-2" style="max-width: 560px; width: 100%;">
                            
                            <select id="subCategoryFilter" class="form-select">
                                <option value="">All Categories (<?php echo e($categories->sum('sub_categories_count')); ?>)</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" data-name="<?php echo e($category->name); ?>" <?php if(request('category_id') == $category->id): echo 'selected'; endif; ?>>
                                        <?php echo e($category->name); ?> (<?php echo e($category->sub_categories_count); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <input type="text" id="subCategorySearch" class="form-control"
                                placeholder="Search by sub category or category...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sub_category_table_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = ['add' => 'Add', 'edit' => 'Edit']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="<?php echo e($mode); ?>SubCategoryModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="<?php echo e($mode); ?>SubCategoryForm" novalidate>
                        <?php echo csrf_field(); ?>
                        <?php if($mode === 'edit'): ?>
                            <input type="hidden" name="id" id="edit_sub_id">
                        <?php endif; ?>
                        <div class="modal-header">
                            <h5><?php echo e($label); ?> Sub Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Category</label>
                                <select name="category_id" id="<?php echo e($mode); ?>_sub_category"
                                    class="form-select category-select2" data-placeholder="Search & select category">
                                    <option value="">Select Category</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="invalid-feedback">Please select category.</div>
                            </div>
                            <div class="mb-3">
                                <label>Sub Category Name</label>
                                <input type="text" name="name" id="<?php echo e($mode); ?>_sub_name" class="form-control">
                                <div class="invalid-feedback">Please enter sub category name.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-save">
                                <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                <span class="btn-text"><?php echo e($mode === 'add' ? 'Save' : 'Update'); ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <style>
        /* Select2 (category) par bhi validation ka laal border */
        select.is-invalid + .select2-container .select2-selection {
            border-color: #dc3545 !important;
        }
    </style>

    <script>
        let currentPage = 1;
        let currentSearch = '';
        let searchTimer = null;

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
        });

        // Filter dropdown ke counts update karo (add/delete ke baad)
        function refreshCounts(counts, total) {
            $('#subCategoryFilter option').each(function() {
                let id = $(this).val();
                if (!id) {
                    $(this).text('All Categories (' + total + ')');
                } else {
                    $(this).text($(this).data('name') + ' (' + (counts[id] || 0) + ')');
                }
            });
        }

        function reloadSubCategoryTable(page = 1, search = currentSearch) {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: "<?php echo e(route('admin.sub-category.table')); ?>",
                type: "GET",
                data: { page: page, search: search, category_id: $('#subCategoryFilter').val() },
                success: function(res) {
                    $('#sub_category_table_wrapper').html(res.html);
                    refreshCounts(res.counts, res.total);
                },
                error: function() {
                    toastr.error('Unable to load sub categories');
                }
            });
        }

        $(document).ready(function() {
            reloadSubCategoryTable(1, '');
        });

        $(document).on('keyup', '#subCategorySearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() { reloadSubCategoryTable(1, val); }, 400);
        });

        $(document).on('change', '#subCategoryFilter', function() {
            reloadSubCategoryTable(1, currentSearch);
        });

        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            reloadSubCategoryTable($(this).data('page'), currentSearch);
        });

        // Add / Edit modal ka Category dropdown: Select2 + search.
        // dropdownParent = modal, warna Bootstrap modal search box par focus nahi lene deta.
        // DOM ready par: select2.min.js layout me page ke neeche load hota hai (is script ke baad).
        $(function() {
            $('.category-select2').each(function() {
                const $select = $(this);
                $select.select2({
                    width: '100%',
                    placeholder: $select.data('placeholder'),
                    allowClear: true,
                    dropdownParent: $select.closest('.modal')
                });
            });
        });

        // Category chun li to error hatao
        $(document).on('change', '.category-select2', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
            }
        });

        // Modal khulte hi category ka search box khul jaye (jab category pehle se select na ho)
        $('#addSubCategoryModal, #editSubCategoryModal').on('shown.bs.modal', function() {
            const $select = $(this).find('.category-select2');
            if (!$select.val()) {
                $select.select2('open');
            } else {
                $(this).find('[name="name"]').trigger('focus');
            }
        });

        // Add modal: filter me jo category selected hai wahi pehle se select ho
        $('#addSubCategoryModal').on('show.bs.modal', function() {
            $('#addSubCategoryForm')[0].reset();
            $('#addSubCategoryForm .is-invalid').removeClass('is-invalid');
            $('#add_sub_category').val($('#subCategoryFilter').val()).trigger('change');
        });

        $(document).on('click', '.editSubCategoryBtn', function() {
            $('#editSubCategoryForm .is-invalid').removeClass('is-invalid');
            $('#edit_sub_id').val($(this).data('id'));
            $('#edit_sub_category').val($(this).data('category')).trigger('change');
            $('#edit_sub_name').val($(this).data('name'));
            $('#editSubCategoryModal').modal('show');
        });

        function validateForm($form) {
            let valid = true;
            $form.find('.is-invalid').removeClass('is-invalid');

            if (!$form.find('[name="category_id"]').val()) {
                $form.find('[name="category_id"]').addClass('is-invalid');
                toastr.error('Please select category');
                valid = false;
            }
            if (!$form.find('[name="name"]').val().trim()) {
                $form.find('[name="name"]').addClass('is-invalid');
                toastr.error('Please enter sub category name');
                valid = false;
            }
            return valid;
        }

        function toggleBtnLoader($btn, show) {
            $btn.prop('disabled', show);
            $btn.find('.spinner-border').toggleClass('d-none', !show);
        }

        function submitForm($form, url, $modal, page) {
            let $btn = $form.find('.btn-save');
            if (!validateForm($form)) return;

            toggleBtnLoader($btn, true);

            $.ajax({
                url: url,
                type: "POST",
                data: $form.serialize(),
                success: function(res) {
                    toastr.success(res.message);
                    $modal.modal('hide');
                    reloadSubCategoryTable(page(), currentSearch);
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
        }

        $('#addSubCategoryForm').submit(function(e) {
            e.preventDefault();
            submitForm($(this), "<?php echo e(route('admin.sub-category.store')); ?>", $('#addSubCategoryModal'), () => 1);
        });

        $('#editSubCategoryForm').submit(function(e) {
            e.preventDefault();
            submitForm($(this), "<?php echo e(route('admin.sub-category.update')); ?>", $('#editSubCategoryModal'), () => currentPage);
        });

        $(document).on('click', '.deleteSubCategoryBtn', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "This sub category will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (!result.isConfirmed) return;
                $btn.prop('disabled', true);
                $.ajax({
                    url: "<?php echo e(url('admin/sub-category/delete')); ?>/" + id,
                    type: "DELETE",
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            reloadSubCategoryTable(currentPage, currentSearch);
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
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\Farmer\resources\views/admin/sub_category/list.blade.php ENDPATH**/ ?>