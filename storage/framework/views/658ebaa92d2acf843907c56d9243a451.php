
<?php $__env->startSection('title', config('app.name') . ' || Product List'); ?>
<?php $__env->startSection('content'); ?>
    <style>
        .product-table-box {
            position: relative;
            min-height: 200px;
        }

        .product-table-loader {
            position: absolute;
            inset: 0;
            z-index: 5;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .6);
            backdrop-filter: blur(1px);
            border-radius: 12px;
        }

        @media (prefers-color-scheme: dark) {
            .product-table-loader {
                background: rgba(0, 0, 0, .35);
            }
        }

        .product-table-box.is-loading .product-table-loader {
            display: flex;
        }

        .product-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
        }

        .product-search {
            position: relative;
            min-width: 260px;
            flex: 1;
            max-width: 380px;
        }

        .product-search .icon-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            opacity: .55;
            pointer-events: none;
        }

        .product-search input {
            padding-left: 36px;
            padding-right: 34px;
        }

        .product-search .search-clear {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: .6;
            display: none;
        }

        .product-search .search-clear:hover {
            opacity: 1;
        }

        .product-pagination .page-btn[disabled] {
            opacity: .45;
            pointer-events: none;
        }

        .type-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .type-filter-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            opacity: .85;
        }

        .type-filter-select {
            position: relative;
            min-width: 150px;
        }

        .type-filter-select select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 100%;
            height: 40px;
            padding: 0 38px 0 14px;
            font-size: 14px;
            font-weight: 500;
            color: inherit;
            background-color: transparent;
            border: 1px solid var(--border-color, #dfe3e8);
            border-radius: 10px;
            cursor: pointer;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .type-filter-select select:hover {
            border-color: #22c55e;
        }

        .type-filter-select select:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, .15);
        }

        /* dropdown options dark theme me bhi readable rahe */
        .type-filter-select select option {
            color: #1f2937;
            background: #fff;
        }

        /* custom arrow */
        .type-filter-select::after {
            content: "";
            position: absolute;
            right: 14px;
            top: 50%;
            width: 8px;
            height: 8px;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: translateY(-70%) rotate(45deg);
            opacity: .6;
            pointer-events: none;
        }

        /* search input ki height bhi same rakho taaki line me barabar dikhe */
        .product-search input {
            height: 40px;
        }

        @media (max-width: 576px) {

            .type-filter,
            .type-filter-select {
                width: 100%;
            }

            .product-search {
                max-width: 100%;
            }
        }
    </style>

    <main class="zentic-content">
        <div class="zentic-card mb-4">
            <div class="zentic-card-header">
                <h1 class="card-title mb-0">Product List</h1>
                <a href="<?php echo e(route('member.product.create')); ?>" class="btn-zentic btn-primary">
                    <i class="icon-plus"></i>Create Product
                </a>
            </div>
            <div class="zentic-card-body">
                <div class="settings-tabs mb-3">
                    <button class="settings-tab <?php echo e($status == 'All' ? 'active' : ''); ?>" data-status="All">
                        <i class="icon-gauge"></i> All
                    </button>
                    <button class="settings-tab <?php echo e($status == 'Pending' ? 'active' : ''); ?>" data-status="Pending">
                        <i class="icon-bot"></i> Pending
                    </button>
                    <button class="settings-tab <?php echo e($status == 'Approved' ? 'active' : ''); ?>" data-status="Approved">
                        <i class="icon-shield-half"></i> Approved
                    </button>
                    <button class="settings-tab <?php echo e($status == 'Rejected' ? 'active' : ''); ?>" data-status="Rejected">
                        <i class="icon-credit-card"></i> Rejected
                    </button>
                </div>

                <!-- Search + Type filter -->
                <div class="product-filter-bar mb-3">
                    <div class="product-search">
                        <i class="icon-search"></i>
                        <input type="text" id="productSearch" class="form-control-zentic"
                            placeholder="Search title, SKU, tag, category..." value="<?php echo e($search); ?>"
                            autocomplete="off">
                        <i class="icon-x search-clear" id="searchClear" title="Clear"></i>
                    </div>
                    <div class="type-filter">
                        <label class="type-filter-label" for="typeFilter">
                            <i class="icon-filter"></i> Type
                        </label>
                        <div class="type-filter-select">
                            <select id="typeFilter">
                                <option value="all" <?php echo e(strtolower($type) == 'all' ? 'selected' : ''); ?>>All</option>
                                <option value="have" <?php echo e(strtolower($type) == 'have' ? 'selected' : ''); ?>>I Have</option>
                                <option value="want" <?php echo e(strtolower($type) == 'want' ? 'selected' : ''); ?>>I Want</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="product-table-box is-loading" id="productTableBox">
                    <div class="product-table-loader">
                        <div class="text-center">
                            <div class="zentic-spinner zentic-spinner-md mx-auto" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="gradient-text fw-semibold">Loading...</div>
                        </div>
                    </div>
                    <div class="zentic-table-wrapper" id="ProductTableContainer"></div>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        $(function() {
            const $box = $('#productTableBox');
            const $container = $('#ProductTableContainer');
            const $search = $('#productSearch');
            const $clear = $('#searchClear');

            let currentPage = parseInt(new URL(window.location).searchParams.get('page') || '1', 10) || 1;
            let activeXhr = null;
            let searchTimer = null;

            function getFilters() {
                return {
                    status: $('.settings-tab.active').data('status') || 'All',
                    type: $('#typeFilter').val() || 'all',
                    search: $.trim($search.val())
                };
            }

            function toggleClear() {
                $clear.toggle($search.val().length > 0);
            }

            function updateUrl() {
                if (!history.replaceState) return;
                const f = getFilters();
                const url = new URL(window.location);
                url.searchParams.set('status', f.status);
                url.searchParams.set('type', f.type);
                f.search ? url.searchParams.set('search', f.search) : url.searchParams.delete('search');
                currentPage > 1 ? url.searchParams.set('page', currentPage) : url.searchParams.delete('page');
                history.replaceState({}, '', url);
            }

            function loadTable(page) {
                currentPage = page || 1;

                // purani request cancel karo (fast typing / tab switching)
                if (activeXhr) activeXhr.abort();

                $box.addClass('is-loading');

                const req = $.ajax({
                    url: "<?php echo e(route('member.product.list')); ?>",
                    type: 'GET',
                    data: $.extend(getFilters(), {
                        page: currentPage
                    }),
                    success: function(html) {
                        $container.html(html);
                    },
                    error: function(xhr, status) {
                        if (status === 'abort') return;
                        $container.html(
                            '<p class="text-center text-danger py-4">Something went wrong. Please try again.</p>'
                        );
                    },
                    complete: function() {
                        // sirf latest request hi loader hataye
                        if (req === activeXhr) {
                            $box.removeClass('is-loading');
                            activeXhr = null;
                        }
                    }
                });
                activeXhr = req;
                updateUrl();
            }

            // initial load
            toggleClear();
            loadTable(currentPage);

            // Status tabs (unchanged behaviour)
            $('.settings-tab').on('click', function() {
                $('.settings-tab').removeClass('active');
                $(this).addClass('active');
                loadTable(1);
            });

            // Type filter
            $('#typeFilter').on('change', function() {
                loadTable(1);
            });

            // Search (debounced)
            $search.on('input', function() {
                toggleClear();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    loadTable(1);
                }, 400);
            });

            $search.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimer);
                    loadTable(1);
                }
            });

            $clear.on('click', function() {
                $search.val('').trigger('focus');
                toggleClear();
                clearTimeout(searchTimer);
                loadTable(1);
            });

            // Pagination (links come from table partial)
            $container.on('click', '.page-btn', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'), 10);
                if (!page || page === currentPage) return;
                loadTable(page);
                $('html, body').animate({
                    scrollTop: $box.offset().top - 100
                }, 200);
            });

            // Delete
            $container.on('click', '.delete-product', function() {
                if (!confirm('Are you sure you want to delete this product?')) return;

                const $btn = $(this);
                const productId = $btn.data('id');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "<?php echo e(route('member.product.delete')); ?>",
                    type: 'POST',
                    data: {
                        id: productId,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // page ka last row delete hua to pichle page pe jao
                            const rowsOnPage = $container.find('tbody tr').length;
                            loadTable(rowsOnPage <= 1 && currentPage > 1 ? currentPage - 1 :
                                currentPage);
                        } else {
                            toastr.error(response.message || 'An error occurred.');
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        toastr.error((xhr.responseJSON && xhr.responseJSON.message) ||
                            'An error occurred. Please try again.');
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('click', '.js-product-complete', function () {
            const $btn = $(this);
            const completed = String($btn.data('completed')) === '1';
            const question = completed
                ? 'Mark this product as completed? It will be hidden from the website.'
                : 'Reopen this product? It will be visible on the website again.';
     
            if (!confirm(question)) {
                return;
            }
     
            $btn.prop('disabled', true);
     
            $.ajax({
                url: "<?php echo e(route('member.product.complete')); ?>",
                type: 'POST',
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    id: $btn.data('id'),
                    completed: completed ? 1 : 0
                },
                success: function (res) {
                    alert(res.message);
                    // Page par table reload ka function ho to wahi, warna poora page reload
                    if (typeof loadProducts === 'function') {
                        loadProducts();
                    } else {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Something went wrong. Please try again.');
                    $btn.prop('disabled', false);
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('member.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/member/products/index.blade.php ENDPATH**/ ?>