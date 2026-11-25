
<?php $__env->startSection('title', config('app.name') . ' || Notifications'); ?>
<?php
    // Icon => [Zentic icon class, notif-icon bg class]. Bell wale partial jaisa hi.
    $nMap = [
        'inquiry' => ['icon-message-square', 'bg-primary'],
        'contact' => ['icon-mail', 'bg-info'],
        'user' => ['icon-user-plus', 'bg-success'],
        'product' => ['icon-package', 'bg-warning'],
        'profile' => ['icon-circle-user', 'bg-primary'],
        'survey' => ['icon-clipboard-list', 'bg-info'],
        'bell' => ['icon-bell', 'bg-primary'],
    ];

    $tabs = ['all' => 'All', 'unread' => 'Unread', 'requests' => 'Enquiries', 'system' => 'Updates'];

    // Current page ke notifications ko Today / Yesterday / Earlier me baanto
    $groups = $notifications->getCollection()->groupBy(function ($n) {
        if ($n->created_at->isToday()) {
            return 'Today';
        }
        if ($n->created_at->isYesterday()) {
            return 'Yesterday';
        }
        return 'Earlier';
    });
?>
<?php $__env->startSection('content'); ?>
    <main class="zentic-content km-notif-page">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div>
                <h1 class="page-title mb-1">Notifications</h1>
                <p class="text-muted mb-0">
                    <?php echo e($stats['unread'] ? $stats['unread'] . ' unread ' . \Illuminate\Support\Str::plural('notification', $stats['unread']) : 'You are all caught up'); ?>

                </p>
            </div>
            <?php if($stats['unread']): ?>
                <form action="<?php echo e(route('member.notifications.readAll')); ?>" method="POST" class="m-0">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-zentic"><i class="icon-circle-check"></i> Mark All Read</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <?php if($announcements->isNotEmpty()): ?>
                    <div class="zentic-card mb-4">
                        <div class="zentic-card-body">
                            <p class="fw-semibold mb-3"><i class="icon-bell me-1"></i> Announcements</p>
                            <div class="km-ann-list">
                                <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="km-ann <?php echo e($a->is_default ? 'km-ann-pinned' : ''); ?>">
                                        <button class="km-ann-head <?php echo e($a->is_default ? '' : 'collapsed'); ?>" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#ann<?php echo e($a->id); ?>"
                                            aria-expanded="<?php echo e($a->is_default ? 'true' : 'false'); ?>">
                                            <span class="km-ann-title">
                                                <?php if($a->is_default): ?>
                                                    <span class="km-ann-badge">Pinned</span>
                                                <?php endif; ?>
                                                <?php echo e($a->title); ?>

                                            </span>
                                            <span class="km-ann-date"><?php echo e($a->created_at->format('d M Y')); ?></span>
                                        </button>
                                        <div id="ann<?php echo e($a->id); ?>" class="collapse <?php echo e($a->is_default ? 'show' : ''); ?>">
                                            <div class="km-ann-body"><?php echo $a->description; ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="zentic-card">
                    <div class="zentic-card-body">
                        <div class="notif-tabs mb-3">
                            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('member.notification', $key === 'all' ? [] : ['tab' => $key])); ?>"
                                    class="notif-tab <?php echo e($tab === $key ? 'active' : ''); ?>"><?php echo e($label); ?></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <p class="km-notif-day"><?php echo e($day); ?></p>
                            <div class="notif-list">
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php [$ic, $bg] = $nMap[$n->data['icon'] ?? 'bell'] ?? $nMap['bell']; ?>
                                    <div class="notif-item <?php echo e($n->read_at ? '' : 'unread'); ?>">
                                        <div class="notif-icon <?php echo e($bg); ?>"><i class="<?php echo e($ic); ?>"></i></div>
                                        <a href="<?php echo e(route('member.notifications.open', $n->id)); ?>" class="notif-content">
                                            <p class="notif-title"><?php echo e($n->data['title'] ?? 'Notification'); ?></p>
                                            <p class="notif-desc"><?php echo e($n->data['message'] ?? ''); ?></p>
                                            <span class="notif-time">
                                                <?php echo e($n->created_at->isToday() ? $n->created_at->diffForHumans() : $n->created_at->format('d M Y, h:i A')); ?>

                                            </span>
                                        </a>
                                        <form action="<?php echo e(route('member.notifications.destroy', $n->id)); ?>" method="POST" class="m-0"
                                            onsubmit="return confirm('Delete this notification?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="km-notif-del" title="Delete" aria-label="Delete">
                                                <i class="icon-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="km-notif-empty py-5">
                                <i class="icon-bell d-block mb-2" style="font-size:28px;"></i>
                                <?php echo e($tab === 'all' ? 'No notifications yet. Enquiries and approvals will show here.' : 'Nothing here right now.'); ?>

                            </div>
                        <?php endif; ?>

                        <?php if($notifications->hasPages()): ?>
                            <div class="mt-3"><?php echo e($notifications->links('pagination::bootstrap-5')); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="zentic-card mb-4">
                    <div class="zentic-card-body">
                        <p class="fw-semibold mb-3">Summary</p>
                        <div class="km-notif-stats">
                            <div><span>Unread</span><strong><?php echo e($stats['unread']); ?></strong></div>
                            <div><span>Today</span><strong><?php echo e($stats['today']); ?></strong></div>
                            <div><span>This Week</span><strong><?php echo e($stats['week']); ?></strong></div>
                            <div><span>Total</span><strong><?php echo e($stats['total']); ?></strong></div>
                        </div>
                    </div>
                </div>

                <div class="zentic-card">
                    <div class="zentic-card-body">
                        <p class="fw-semibold mb-2">Clean up</p>
                        <p class="text-muted small mb-3">Delete all notifications you have already read.</p>
                        <form action="<?php echo e(route('member.notifications.clearRead')); ?>" method="POST" class="m-0"
                            onsubmit="return confirm('Delete all read notifications?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn-zentic w-100">Clear read notifications</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('member.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\Farmer\resources\views/member/notifications/index.blade.php ENDPATH**/ ?>