
<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="ti ti-circle-check fs-5 me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="ti ti-alert-triangle fs-5 me-2"></i><?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/survey/_alerts.blade.php ENDPATH**/ ?>