
<?php
    $variant = $variant ?? 'green';
    $crumbs = $crumbs ?? [];
?>
<div class="page-hero-wrap">
    <div class="container-fluid">
        <section class="page-hero hero-<?php echo e($variant); ?>">
            <?php if(!empty($art)): ?>
                <i class="fa-solid <?php echo e($art); ?> page-hero-art" aria-hidden="true"></i>
            <?php endif; ?>

            <div class="page-hero-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                        <?php $__currentLoopData = $crumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($link && !$loop->last): ?>
                                <li class="breadcrumb-item"><a href="<?php echo e($link); ?>"><?php echo e($label); ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e($label); ?></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ol>
                </nav>

                <?php if(!empty($badge)): ?>
                    <span class="page-hero-badge"><i class="fa-solid <?php echo e($badge['icon']); ?>"></i><?php echo e($badge['label']); ?></span>
                <?php endif; ?>

                <h1 class="page-hero-title"><?php echo e($title); ?></h1>

                <?php if(!empty($text)): ?>
                    <p class="page-hero-text"><?php echo e($text); ?></p>
                <?php endif; ?>

                <?php if(!empty($actions)): ?>
                    <div class="page-hero-actions"><?php echo $actions; ?></div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/partials/page-hero.blade.php ENDPATH**/ ?>