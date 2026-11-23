
<?php $__env->startSection('title', config('app.name') . ' || Product Inquiries'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.inquiries.partials.styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="content">

        
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Product Inquiries</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('admin/dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Inquiries</li>
                        <li class="breadcrumb-item active" aria-current="page">Product Inquiry</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php echo $__env->make('admin.inquiries.partials.stats', ['counts' => $counts], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                <h5 class="mb-0">Product Inquiry List</h5>
                <span class="badge badge-soft-primary" id="inqResultBadge">Results : <?php echo e($inquiries->total()); ?></span>
            </div>

            <div class="card-body">
                
                <form id="inqFilterForm" class="row g-2 align-items-end mb-3" autocomplete="off">
                    <div class="col-xl-4 col-md-12">
                        <label class="form-label fs-12 mb-1" for="inqSearch">Search</label>
                        <div class="inq-search-box">
                            <i class="ti ti-search"></i>
                            <input type="text" id="inqSearch" name="search" class="form-control inq-filter"
                                placeholder="Customer, seller, product, email, phone, message..."
                                value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <label class="form-label fs-12 mb-1" for="inqStatus">Status</label>
                        <select id="inqStatus" name="status" class="form-select inq-filter">
                            <option value="">All Status</option>
                            <option value="0" <?php if(request('status') === '0'): echo 'selected'; endif; ?>>Pending</option>
                            <option value="1" <?php if(request('status') === '1'): echo 'selected'; endif; ?>>Contacted</option>
                            <option value="2" <?php if(request('status') === '2'): echo 'selected'; endif; ?>>Closed</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-4 col-6">
                        <label class="form-label fs-12 mb-1" for="inqDateFrom">From</label>
                        <input type="date" id="inqDateFrom" name="date_from" class="form-control inq-filter"
                            value="<?php echo e(request('date_from')); ?>">
                    </div>
                    <div class="col-xl-1 col-md-4 col-6">
                        <label class="form-label fs-12 mb-1" for="inqDateTo">To</label>
                        <input type="date" id="inqDateTo" name="date_to" class="form-control inq-filter"
                            value="<?php echo e(request('date_to')); ?>">
                    </div>
                    <div class="col-xl-2 col-md-6 col-6">
                        <label class="form-label fs-12 mb-1" for="inqPerPage">Show</label>
                        <select id="inqPerPage" name="per_page" class="form-select inq-filter">
                            <?php $__currentLoopData = [10, 25, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($size); ?>" <?php if((int) request('per_page', 10) === $size): echo 'selected'; endif; ?>><?php echo e($size); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-6 col-12">
                        <button type="button" id="inqResetBtn" class="btn btn-light border w-100">
                            <i class="ti ti-refresh me-1"></i>Reset
                        </button>
                    </div>
                </form>

                
                <div class="inq-table-wrap">
                    <div class="inq-loader d-none" id="inqLoader">
                        <div class="inq-spinner"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover inq-table" id="inqTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="inqTableBody">
                                <?php echo $__env->make('admin.inquiries.partials.product_rows', [
                                    'inquiries' => $inquiries,
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="inqPagination" class="mt-3">
                    <?php echo $__env->make('admin.inquiries.partials.pagination', ['inquiries' => $inquiries], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('admin.inquiries.partials.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('admin.inquiries.partials.scripts', [
        'listRoute' => route('admin.product.inquiries.list'),
        'statusRoute' => route('admin.product.inquiries.status.update'),
        'currentPage' => $inquiries->currentPage(),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/inquiries/product_list.blade.php ENDPATH**/ ?>