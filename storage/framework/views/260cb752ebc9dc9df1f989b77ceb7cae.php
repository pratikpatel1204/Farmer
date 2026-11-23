<?php $__env->startSection('title', config('app.name') . ' || Admin Products'); ?>
<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Products</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/admin/dashboard')); ?>"><i
                                    class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item active">Product Master</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-md-flex justify-content-between align-items-center">
                <ul class="nav nav-tabs card-header-tabs product-tabs mb-2 mb-md-0" id="productTabs" role="tablist">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" data-status="Pending">
                            Pending <span class="badge bg-warning ms-1" id="count-Pending"><?php echo e($counts['Pending']); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-status="Approved">
                            Approved <span class="badge bg-success ms-1"
                                id="count-Approved"><?php echo e($counts['Approved']); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-status="Rejected">
                            Rejected <span class="badge bg-danger ms-1" id="count-Rejected"><?php echo e($counts['Rejected']); ?></span>
                        </a>
                    </li>
                </ul>
                <div style="max-width: 300px; width: 100%;">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Search title, SKU, tags, seller...">
                </div>
            </div>

            <div class="card-body p-0 position-relative" style="min-height:220px">
                
                <div id="tableLoader" class="d-none align-items-center justify-content-center"
                    style="position:absolute;inset:0;background:rgba(255,255,255,.75);z-index:5;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="small text-muted mt-2">Loading...</div>
                    </div>
                </div>
                <div id="tableWrap"></div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            const urls = {
                table: "<?php echo e(route('admin.product.table')); ?>",
                approve: "<?php echo e(route('admin.product.approve')); ?>",
                reject: "<?php echo e(route('admin.product.reject')); ?>",
                delete: "<?php echo e(route('admin.product.delete')); ?>",
            };
            const csrf = "<?php echo e(csrf_token()); ?>";

            // Restore tab / search / page from URL (same tab opens after refresh)
            const params = new URLSearchParams(location.search);
            const STATUSES = ['Pending', 'Approved', 'Rejected'];

            let state = {
                status: STATUSES.includes(params.get('status')) ? params.get('status') : 'Pending',
                search: params.get('search') || '',
                page: parseInt(params.get('page')) || 1
            };
            let timer = null,
                xhr = null;

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });

            function syncUrl() {
                const p = new URLSearchParams();
                p.set('status', state.status);
                if (state.search) p.set('search', state.search);
                if (state.page > 1) p.set('page', state.page);
                history.replaceState(null, '', location.pathname + '?' + p.toString());
            }

            function showLoader() {
                $('#tableLoader').removeClass('d-none').addClass('d-flex');
            }

            function hideLoader() {
                $('#tableLoader').removeClass('d-flex').addClass('d-none');
            }

            function loadTable() {
                if (xhr) xhr.abort();
                showLoader();
                syncUrl();
                const req = xhr = $.get(urls.table, state, function(res) {
                    $('#tableWrap').html(res.html);
                    Object.keys(res.counts).forEach(k => $('#count-' + k).text(res.counts[k]));
                }).fail(function(e) {
                    if (e.statusText !== 'abort') {
                        $('#tableWrap').html(
                            '<div class="text-center text-danger p-5">Unable to load data. Please try again.</div>'
                        );
                    }
                }).always(function() {
                    if (req === xhr) hideLoader(); // only the latest request hides the loader
                });
            }

            // Tabs
            $('#productTabs').on('click', '.nav-link', function(e) {
                e.preventDefault();
                $('#productTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                state.status = $(this).data('status');
                state.page = 1;
                loadTable();
            });

            // Search (debounced)
            $('#searchInput').on('keyup', function() {
                clearTimeout(timer);
                const val = $(this).val();
                timer = setTimeout(() => {
                    state.search = val;
                    state.page = 1;
                    loadTable();
                }, 400);
            });

            // Pagination
            $('#tableWrap').on('click', '.pagination a', function(e) {
                e.preventDefault();
                const page = new URL(this.href).searchParams.get('page');
                state.page = parseInt(page) || 1;
                loadTable();
            });

            function post(url, data) {
                return $.ajax({
                        url,
                        type: 'POST',
                        data: {
                            ...data,
                            _token: csrf
                        }
                    })
                    .done(res => {
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                        loadTable();
                    })
                    .fail(x => {
                        let msg = 'Something went wrong. Please try again.';
                        if (x.responseJSON?.errors) {
                            msg = Object.values(x.responseJSON.errors)[0][0];
                        } else if (x.responseJSON?.message) {
                            msg = x.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    });
            }

            // Approve
            $('#tableWrap').on('click', '.btn-approve', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Approve this product?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Yes, approve it',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) post(urls.approve, {
                        id: id
                    });
                });
            });

            // Reject (reason is asked inside SweetAlert)
            $('#tableWrap').on('click', '.btn-reject', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Reject this product?',
                    input: 'textarea',
                    inputLabel: 'Rejection reason',
                    inputPlaceholder: 'Write the reason for rejection...',
                    inputAttributes: {
                        maxlength: 1000
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Reject',
                    cancelButtonText: 'Cancel',
                    inputValidator: value => {
                        if (!value || value.trim() === '') {
                            return 'Rejection reason is required.';
                        }
                    }
                }).then(result => {
                    if (result.isConfirmed) post(urls.reject, {
                        id: id,
                        rejection_reason: result.value.trim()
                    });
                });
            });

            // Delete
            $('#tableWrap').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This product will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) post(urls.delete, {
                        id: id
                    });
                });
            });

            // Initial: activate the tab from URL, fill the search box, then load the table
            $('#productTabs .nav-link').removeClass('active')
                .filter('[data-status="' + state.status + '"]').addClass('active');
            $('#searchInput').val(state.search);

            loadTable();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/product/list.blade.php ENDPATH**/ ?>