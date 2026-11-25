
<?php $__env->startSection('title', config('app.name') . ' || Notifications'); ?>
<?php
    $nIcons = [
        'inquiry' => ['ti-message-question', 'bg-primary'],
        'contact' => ['ti-address-book', 'bg-info'],
        'user' => ['ti-user-plus', 'bg-success'],
        'product' => ['ti-package', 'bg-warning'],
        'profile' => ['ti-id-badge', 'bg-purple'],
        'survey' => ['ti-clipboard-list', 'bg-pink'],
        'bell' => ['ti-bell', 'bg-secondary'],
    ];
?>
<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Notifications</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </nav>
            </div>
            <?php if(auth()->user()->unreadNotifications()->exists()): ?>
                <form action="<?php echo e(route('admin.notifications.readAll')); ?>" method="POST" class="mb-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-checks me-1"></i>Mark all as read</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php [$ic, $bg] = $nIcons[$notification->data['icon'] ?? 'bell'] ?? $nIcons['bell']; ?>
                    <div class="d-flex align-items-start gap-3 p-3 border-bottom <?php echo e($notification->read_at ? '' : 'bg-light'); ?>">
                        <span class="avatar avatar-md rounded-circle <?php echo e($bg); ?> flex-shrink-0">
                            <i class="ti <?php echo e($ic); ?> fs-16 text-white"></i>
                        </span>
                        <a href="<?php echo e(route('admin.notifications.open', $notification->id)); ?>" class="flex-grow-1 text-body">
                            <p class="mb-1">
                                <span class="text-dark fw-semibold"><?php echo e($notification->data['title'] ?? 'Notification'); ?></span>
                                <?php if (! ($notification->read_at)): ?>
                                    <span class="badge bg-danger ms-1">New</span>
                                <?php endif; ?>
                            </p>
                            <p class="mb-1"><?php echo e($notification->data['message'] ?? ''); ?></p>
                            <span class="text-muted fs-12">
                                <?php echo e($notification->created_at->format('d M Y, h:i A')); ?> ¡¤ <?php echo e($notification->created_at->diffForHumans()); ?>

                            </span>
                        </a>
                        <form action="<?php echo e(route('admin.notifications.destroy', $notification->id)); ?>" method="POST"
                            onsubmit="return confirm('Delete this notification?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-icon btn-light" title="Delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-bell-off fs-24 d-block mb-2"></i>
                        No notifications yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3"><?php echo e($notifications->links('pagination::bootstrap-5')); ?></div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\Farmer\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>