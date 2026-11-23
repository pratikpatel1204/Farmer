<?php
    $statCards = [
        ['status' => '', 'key' => 'total', 'label' => 'Total Inquiries', 'icon' => 'ti ti-inbox', 'color' => 'primary'],
        [
            'status' => '0',
            'key' => 'pending',
            'label' => 'Pending',
            'icon' => 'ti ti-clock-hour-4',
            'color' => 'warning',
        ],
        [
            'status' => '1',
            'key' => 'contacted',
            'label' => 'Contacted',
            'icon' => 'ti ti-phone-check',
            'color' => 'success',
        ],
        ['status' => '2', 'key' => 'closed', 'label' => 'Closed', 'icon' => 'ti ti-circle-x', 'color' => 'danger'],
    ];
    $activeStatus = (string) request('status', '');
?>

<div class="row">
    <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-xl-3 col-sm-6 d-flex">
            <div class="card flex-fill inq-stat-card inq-c-<?php echo e($card['color']); ?> <?php echo e($activeStatus === $card['status'] ? 'active' : ''); ?>"
                data-status="<?php echo e($card['status']); ?>" role="button">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="fs-13 fw-medium text-muted mb-1"><?php echo e($card['label']); ?></p>
                        <h3 class="mb-0" data-count="<?php echo e($card['key']); ?>"><?php echo e((int) ($counts[$card['key']] ?? 0)); ?></h3>
                    </div>
                    <span class="inq-stat-icon"><i class="<?php echo e($card['icon']); ?>"></i></span>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/inquiries/partials/stats.blade.php ENDPATH**/ ?>