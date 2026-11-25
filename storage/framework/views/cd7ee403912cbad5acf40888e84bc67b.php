<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th width="50">#</th>
                <th>Title</th>
                <th>Description</th>
                <th width="110">Default</th>
                <th width="120">Status</th>
                <th width="140">Created</th>
                <th width="110">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration + ($notifications->currentPage() - 1) * $notifications->perPage()); ?></td>
                    <td>
                        <?php echo e($notification->title); ?>

                        <?php if($notification->is_default): ?>
                            <span class="badge bg-warning text-dark ms-1"><i class="ti ti-pin"></i> Pinned</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo e(\Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags($notification->description))), 70)); ?>

                        
                        <template class="notification-html"><?php echo $notification->description; ?></template>
                    </td>
                    <td>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input notificationDefaultToggle" type="checkbox" role="switch"
                                data-id="<?php echo e($notification->id); ?>" <?php if($notification->is_default): echo 'checked'; endif; ?>
                                title="Pin on top for members">
                        </div>
                    </td>
                    <td>
                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                            <input class="form-check-input notificationStatusToggle" type="checkbox" role="switch"
                                data-id="<?php echo e($notification->id); ?>" <?php if($notification->status === 'Active'): echo 'checked'; endif; ?>>
                            <span class="badge <?php echo e($notification->status === 'Active' ? 'bg-success' : 'bg-danger'); ?>">
                                <?php echo e($notification->status); ?>

                            </span>
                        </div>
                    </td>
                    <td><?php echo e($notification->created_at?->format('d M Y, h:i A')); ?></td>
                    <td>
                        <button class="btn btn-sm btn-info editNotificationBtn" data-id="<?php echo e($notification->id); ?>"
                            data-title="<?php echo e($notification->title); ?>" data-status="<?php echo e($notification->status); ?>"
                            data-default="<?php echo e($notification->is_default ? 1 : 0); ?>">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteNotificationBtn" data-id="<?php echo e($notification->id); ?>">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center">No notifications found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($notifications->hasPages()): ?>
    <div class="d-md-flex justify-content-between align-items-center mt-3">
        <div class="text-muted mb-2 mb-md-0">
            Showing <?php echo e($notifications->firstItem() ?? 0); ?> to <?php echo e($notifications->lastItem() ?? 0); ?> of
            <?php echo e($notifications->total()); ?> entries
        </div>
        <nav>
            <ul class="pagination mb-0">
                <?php if($notifications->onFirstPage()): ?>
                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                <?php else: ?>
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#" data-page="<?php echo e($notifications->currentPage() - 1); ?>">&laquo;</a>
                    </li>
                <?php endif; ?>

                <?php $__currentLoopData = range(1, $notifications->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $notifications->currentPage()): ?>
                        <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                    <?php elseif($page == 1 || $page == $notifications->lastPage() || abs($page - $notifications->currentPage()) <= 2): ?>
                        <li class="page-item">
                            <a class="page-link paginateLink" href="#" data-page="<?php echo e($page); ?>"><?php echo e($page); ?></a>
                        </li>
                    <?php elseif($page == $notifications->currentPage() - 3 || $page == $notifications->currentPage() + 3): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($notifications->hasMorePages()): ?>
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#" data-page="<?php echo e($notifications->currentPage() + 1); ?>">&raquo;</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\Farmer\resources\views/admin/notification_master/table.blade.php ENDPATH**/ ?>