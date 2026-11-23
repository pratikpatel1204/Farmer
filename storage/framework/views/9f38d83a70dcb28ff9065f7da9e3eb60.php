<div class="table-responsive">
    <table class="table table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Action</th>
                <th>Product</th>
                <th>Seller</th>
                <th class="text-center">Attributes</th>
                <th>Created</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($products->firstItem() + $i); ?></td>

                    
                    <td>
                        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <a href="<?php echo e(route('admin.product.view', $p->id)); ?>" class="btn btn-primary btn-sm"
                                title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            <?php if($p->status !== 'Approved'): ?>
                                <button type="button" class="btn btn-success btn-approve btn-sm"
                                    data-id="<?php echo e($p->id); ?>" title="Approve">
                                    <i class="ti ti-check"></i>
                                </button>
                            <?php endif; ?>
                            <?php if($p->status !== 'Rejected'): ?>
                                <button type="button" class="btn btn-warning btn-reject btn-sm"
                                    data-id="<?php echo e($p->id); ?>" title="Reject">
                                    <i class="ti ti-x"></i>
                                </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-danger btn-delete btn-sm"
                                data-id="<?php echo e($p->id); ?>" title="Delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>

                    
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="<?php echo e(route('admin.product.view', $p->id)); ?>" class="avatar avatar-md me-2">
                                <img src="<?php echo e(asset($p->main_image)); ?>" class="rounded" alt="<?php echo e($p->title); ?>">
                            </a>
                            <h6 class="fw-medium mb-0">
                                <a href="<?php echo e(route('admin.product.view', $p->id)); ?>"><?php echo e($p->title); ?></a>
                            </h6>
                        </div>
                    </td>

                    
                    <td>
                        <?php if($p->member): ?>
                            <div class="fw-medium"><?php echo e($p->member->full_name); ?></div>
                            <small class="text-muted"><?php echo e($p->member->unique_id); ?></small>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    
                    <td class="text-center">
                        <span class="badge bg-info"><?php echo e($p->product_attrs_count ?? 0); ?></span>
                    </td>

                    <td><?php echo e($p->created_at?->format('d M Y, h:i A')); ?></td>

                    
                    <td>
                        <?php if($p->status === 'Approved'): ?>
                            <span class="badge badge-soft-success">Approved</span>
                        <?php elseif($p->status === 'Rejected'): ?>
                            <span class="badge badge-soft-danger" title="<?php echo e($p->rejection_reason); ?>">Rejected</span>
                            <?php if($p->rejection_reason): ?>
                                <div class="text-muted small mt-1" style="max-width:200px;white-space:normal">
                                    <?php echo e($p->rejection_reason); ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge badge-soft-warning">Pending</span>
                        <?php endif; ?>
                        <?php if($p->completed_at): ?>
                            <span class="badge bg-info">Completed</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted p-5">
                        No <?php echo e(strtolower($status)); ?> products found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($products->hasPages()): ?>
    <div class="d-flex align-items-center justify-content-between flex-wrap p-3">
        <small class="text-muted">
            Showing <?php echo e($products->firstItem()); ?> - <?php echo e($products->lastItem()); ?> of <?php echo e($products->total()); ?>

        </small>
        <?php echo e($products->links('pagination::bootstrap-5')); ?>

    </div>
<?php endif; ?>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/product/table.blade.php ENDPATH**/ ?>