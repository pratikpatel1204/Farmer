<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <title><?php echo $__env->yieldContent('title'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('front/image/favicon.png')); ?>" sizes="32x32">
    <script>
        (function() {
            var html = document.documentElement;
            var m = document.cookie.match(/googtrans=\/en\/(\w+)/);
            var firstVisit = false;
            try {
                firstVisit = !localStorage.getItem('lang_chosen') && !m;
            } catch (e) {}

            if (firstVisit || (m && m[1] !== 'en')) {
                // Loader me kis bhasha ka message dikhe
                var lang = m ? m[1] : 'hi';
                html.setAttribute('data-loading-lang', lang);
                html.classList.add('translating');

                function reveal() {
                    html.classList.remove('translating');
                }

                new MutationObserver(function() {
                    if (/translated-(ltr|rtl)/.test(html.className)) {
                        setTimeout(reveal, 150);
                    }
                }).observe(html, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                // Safety: Google slow ya block ho to bhi page dikha do
                setTimeout(reveal, 2000);
            }
        })();
    </script>
    
    <script src="<?php echo e(asset('front/js/translate.js')); ?>" defer></script>

    
    <link rel="stylesheet" href="<?php echo e(asset('front/bootstrap/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/fontawesome/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/fonts.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/header.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/toastr.min.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <?php echo $__env->make('front.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <main class="content-wrapper">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    <?php echo $__env->make('front.layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="<?php echo e(asset('front/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('front/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('front/vendor/swiper/swiper-bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('front/js/toastr.min.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/layout/app.blade.php ENDPATH**/ ?>