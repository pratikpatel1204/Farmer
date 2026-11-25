<?php $__env->startSection('title', config('app.name') . ' || Notification Master'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* CKEditor ke link/balloon popups modal ke upar dikhne chahiye */
        .ck.ck-balloon-panel { z-index: 1065 !important; }
        #notificationForm .ck-editor__editable { min-height: 220px; }
        #notificationForm .ck-editor.is-invalid .ck-editor__editable { border-color: #dc3545 !important; }
    </style>

    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Notification Master</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Master Setting</li>
                        <li class="breadcrumb-item active">Notification Master</li>
                    </ol>
                </nav>
            </div>
            <button type="button" id="addNotificationBtn" class="btn btn-primary mt-2 mt-md-0">
                + Add Notification
            </button>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <div class="mb-2 mb-md-0">
                            <h4 class="card-title mb-1">Notifications for all members</h4>
                            <small class="text-muted">
                                The <b>Default</b> notification stays pinned on top for members until you make another
                                one default. Only <b>Active</b> notifications are shown to members.
                            </small>
                        </div>
                        <div style="max-width: 300px; width: 100%;">
                            <input type="text" id="notificationSearch" class="form-control" placeholder="Search by title...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="notification_table_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD / EDIT MODAL (ek hi modal, ek hi CKEditor) -->
    
    <div class="modal fade" id="notificationModal" data-bs-focus="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="notificationForm" novalidate>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" id="notification_id">
                    <div class="modal-header">
                        <h5 id="notificationModalTitle">Add Notification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="notification_title" class="form-control"
                                    maxlength="200" placeholder="Enter notification title">
                                <div class="invalid-feedback">Please enter notification title.</div>
                            </div>
                            <div class="col-md-2 col-6 mb-3">
                                <label class="form-label d-block">Default</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_default"
                                        value="1" id="notification_default">
                                    <label class="form-check-label" for="notification_default">Pin on top</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="notification_status" class="form-select">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-12 mb-1">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="notification_description"></textarea>
                                <div class="text-danger small mt-1 d-none" id="descriptionError">
                                    Please enter notification description.
                                </div>
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

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        let currentPage = 1;
        let currentSearch = '';
        let searchTimer = null;
        let editor = null;
        const $form = $('#notificationForm');

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
        });

        ClassicEditor.create(document.querySelector('#notification_description'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
                'blockQuote', 'insertTable', '|', 'undo', 'redo'
            ]
        }).then(e => {
            editor = e;
            editor.model.document.on('change:data', () => toggleDescError(false));
        }).catch(err => console.error(err));

        function toggleDescError(show) {
            $('#descriptionError').toggleClass('d-none', !show);
            $form.find('.ck-editor').toggleClass('is-invalid', show);
        }

        function reloadNotificationTable(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.get("<?php echo e(route('admin.notification-master.table')); ?>", { page: page, search: search })
                .done(res => $('#notification_table_wrapper').html(res.html))
                .fail(() => toastr.error('Unable to load notifications'));
        }

        $(document).ready(() => reloadNotificationTable(1, ''));

        // SEARCH (debounced)
        $(document).on('keyup', '#notificationSearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => reloadNotificationTable(1, val), 400);
        });

        // PAGINATION
        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            reloadNotificationTable($(this).data('page'), currentSearch);
        });

        function resetForm() {
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
            toggleDescError(false);
            $('#notification_id').val('');
            if (editor) editor.setData('');
        }

        // ADD
        $('#addNotificationBtn').on('click', function() {
            resetForm();
            $('#notificationModalTitle').text('Add Notification');
            $form.find('.btn-text').text('Save');
            $('#notificationModal').modal('show');
        });

        // EDIT
        $(document).on('click', '.editNotificationBtn', function() {
            resetForm();
            let $row = $(this).closest('tr');
            $('#notificationModalTitle').text('Edit Notification');
            $form.find('.btn-text').text('Update');
            $('#notification_id').val($(this).data('id'));
            $('#notification_title').val($(this).attr('data-title'));
            $('#notification_status').val($(this).attr('data-status'));
            $('#notification_default').prop('checked', $(this).attr('data-default') === '1');
            if (editor) editor.setData($row.find('.notification-html').html());
            $('#notificationModal').modal('show');
        });

        function validateForm() {
            let valid = true;
            $form.find('.is-invalid').removeClass('is-invalid');
            toggleDescError(false);

            if (!$('#notification_title').val().trim()) {
                $('#notification_title').addClass('is-invalid');
                toastr.error('Please enter notification title');
                valid = false;
            }

            let text = editor ? $('<div>').html(editor.getData()).text().replace(/ /g, ' ').trim() : '';
            if (!text) {
                toggleDescError(true);
                toastr.error('Please enter notification description');
                valid = false;
            }

            return valid;
        }

        function toggleBtnLoader($btn, show) {
            $btn.prop('disabled', show);
            $btn.find('.spinner-border').toggleClass('d-none', !show);
        }

        // SAVE (add ya update, id se decide)
        $form.on('submit', function(e) {
            e.preventDefault();
            if (!validateForm()) return;

            let $btn = $form.find('.btn-save');
            let isEdit = !!$('#notification_id').val();
            let data = new FormData(this);
            data.set('description', editor.getData());

            toggleBtnLoader($btn, true);

            $.ajax({
                url: isEdit ? "<?php echo e(route('admin.notification-master.update')); ?>" : "<?php echo e(route('admin.notification-master.store')); ?>",
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                success: function(res) {
                    toastr.success(res.message);
                    $('#notificationModal').modal('hide');
                    reloadNotificationTable(isEdit ? currentPage : 1, currentSearch);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(k, v) {
                            toastr.error(v[0]);
                            k === 'description' ? toggleDescError(true) : $form.find('[name="' + k + '"]').addClass('is-invalid');
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },
                complete: () => toggleBtnLoader($btn, false)
            });
        });

        // DEFAULT / STATUS toggles (table me)
        function toggleRequest($input, url) {
            $input.prop('disabled', true);
            $.post(url)
                .done(res => {
                    toastr.success(res.message);
                    reloadNotificationTable(currentPage, currentSearch);
                })
                .fail(() => {
                    toastr.error('Something went wrong.');
                    $input.prop('checked', !$input.prop('checked')).prop('disabled', false);
                });
        }

        $(document).on('change', '.notificationDefaultToggle', function() {
            toggleRequest($(this), "<?php echo e(url('admin/notification-master')); ?>/" + $(this).data('id') + "/default");
        });

        $(document).on('change', '.notificationStatusToggle', function() {
            toggleRequest($(this), "<?php echo e(url('admin/notification-master')); ?>/" + $(this).data('id') + "/status");
        });

        // DELETE
        $(document).on('click', '.deleteNotificationBtn', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "This notification will be removed for all members!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (!result.isConfirmed) return;
                $btn.prop('disabled', true);
                $.ajax({
                    url: "<?php echo e(url('admin/notification-master')); ?>/" + id,
                    type: "POST",
                    data: { _method: "DELETE" },
                    success: function(res) {
                        toastr.success(res.message);
                        reloadNotificationTable(currentPage, currentSearch);
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

<?php echo $__env->make('admin.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\Farmer\resources\views/admin/notification_master/list.blade.php ENDPATH**/ ?>