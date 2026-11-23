

<?php $__env->startSection('title', $title . ' - ' . config('app.name')); ?>

<?php $__env->startPush('styles'); ?>
    
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="inner-page">

    <?php echo $__env->make('front.partials.page-hero', [
        'title'  => $title,
        'text'   => $text,
        'badge'  => ['icon' => $icon, 'label' => 'Legal'],
        'art'    => $icon,
        'crumbs' => [$title => null],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="page-section">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-9">
                    <div class="bg-white border rounded-4 shadow-sm p-3 p-md-4 p-lg-5">

                        <?php if($updatedAt): ?>
                            <p class="text-muted small mb-4">
                                <i class="fa-solid fa-calendar-check me-1"></i>
                                Last updated: <?php echo e($updatedAt->format('d M Y')); ?>

                            </p>
                        <?php endif; ?>

                        <?php if($content): ?>
                            <div class="rich-text policy-content" style="line-height:1.75">
                                <?php echo $content; ?>

                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">This page will be updated soon.</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/pages/policy.blade.php ENDPATH**/ ?>