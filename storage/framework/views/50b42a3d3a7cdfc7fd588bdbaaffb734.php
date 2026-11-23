<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('member/img/favicon.png')); ?>">
    <script>
        (function() {
            var html = document.documentElement;
            var m = document.cookie.match(/googtrans=\/en\/(\w+)/);
            var firstVisit = false;
            try {
                firstVisit = !localStorage.getItem('lang_chosen') && !m;
            } catch (e) {}

            if (firstVisit || (m && m[1] !== 'en')) {
                html.setAttribute('data-loading-lang', m ? m[1] : 'hi');
                html.classList.add('translating');

                function reveal() {
                    html.classList.remove('translating');
                }

                new MutationObserver(function() {
                    if (/translated-(ltr|rtl)/.test(html.className)) setTimeout(reveal, 150);
                }).observe(html, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                setTimeout(reveal, 2000);
            }
        })();
    </script>
    <script src="<?php echo e(asset('member/js/translate.js')); ?>" defer></script>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?php echo e(asset('member/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('member/css/dataTables.bootstrap5.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('member/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('member/css/icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('member/css/toastr.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('member/css/notifications.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('member/plugins/select2/css/select2.min.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <div id="page-loader" class="notranslate" translate="no" aria-hidden="true">
        <div class="loader-ring">
            <img src="<?php echo e(asset('member/img/favicon.png')); ?>" alt="">
        </div>
        <div class="loader-text"></div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php echo $__env->make('member.layout.Sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="zentic-main" id="zenticMain">
        <?php echo $__env->make('member.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
        <footer class="zentic-footer">
            <span>&copy; <?php echo e(date('Y')); ?> <strong>RGM Mart</strong>. All rights reserved.</span>
        </footer>
        <button class="back-to-top visible" id="backToTop" aria-label="Back to top">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </button>
    </div>
    <script src="<?php echo e(asset('member/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('member/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('member/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('member/js/dataTables.bootstrap5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('member/js/toastr.min.js')); ?>"></script>
    <script src="<?php echo e(asset('member/js/custom.js')); ?>"></script>
    <script src="<?php echo e(asset('member/plugins/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('member/plugins/ckeditor/ckeditor.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/member/layout/main-layout.blade.php ENDPATH**/ ?>