
<?php $__env->startPush('styles'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/toastr.min.css')); ?>">
    <?php if(!empty($select2)): ?>
        <link rel="stylesheet" href="<?php echo e(asset('front/vendor/select2/select2.min.css')); ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/auth.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('front/js/pages.js')); ?>"></script>
    <script src="<?php echo e(asset('front/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('front/js/toastr.min.js')); ?>"></script>
    <?php if(!empty($select2)): ?>
        <script src="<?php echo e(asset('front/vendor/select2/select2.min.js')); ?>"></script>
    <?php endif; ?>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 4000,
            preventDuplicates: true
        };
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/auth/_assets.blade.php ENDPATH**/ ?>