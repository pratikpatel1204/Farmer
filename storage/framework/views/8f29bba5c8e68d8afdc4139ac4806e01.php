<?php $__env->startSection('title', 'Categories - ' . config('app.name')); ?>
<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="inner-page">

    <?php echo $__env->make('front.partials.page-hero', [
        'title' => 'Shop by category',
        'text' => 'From vegetables to tractors. Pick a category to see what farmers have and what buyers want.',
        'badge' => ['icon' => 'fa-table-cells-large', 'label' => 'Categories'],
        'variant' => 'soil',
        'art' => 'fa-table-cells-large',
        'crumbs' => ['Category' => null],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="page-section">
        <div class="container-xl">
            <?php if($categories->count()): ?>
                <div class="category-head">
                    <p class="category-head-text"><strong><?php echo e($categories->count()); ?></strong> categories &middot; tap any card to see products</p>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-lg-4">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $look = \App\Support\Market::categoryLook($category);
                            $image = \App\Support\Market::categoryImage($category);
                            $total = $category->have_count + $category->want_count;
                        ?>
                        <div class="col">
                            <a href="<?php echo e(url('category/' . \App\Support\Market::slugOf($category))); ?>" class="cat-card">
                                <div class="cat-card-media tint-<?php echo e($look['color']); ?> <?php echo e($image ? 'has-image' : 'no-image'); ?>">
                                    <?php if($image): ?>
                                        <img src="<?php echo e($image); ?>" alt="<?php echo e($category->name); ?>" loading="lazy"
                                             onerror="this.parentNode.classList.replace('has-image', 'no-image'); this.remove();">
                                    <?php endif; ?>
                                    
                                    <span class="cat-card-icon" aria-hidden="true"><i class="fa-solid <?php echo e($look['icon']); ?>"></i></span>
                                    <span class="cat-card-count"><?php echo e($total); ?> <?php echo e($total === 1 ? 'listing' : 'listings'); ?></span>
                                </div>
                                <div class="cat-card-body">
                                    <h2 class="cat-card-name"><?php echo e($category->name); ?></h2>
                                    <div class="cat-card-split">
                                        <span class="split-have"><i class="fa-solid fa-seedling"></i><?php echo e($category->have_count); ?> Have</span>
                                        <span class="split-want"><i class="fa-solid fa-cart-shopping"></i><?php echo e($category->want_count); ?> Want</span>
                                    </div>
                                    <span class="cat-card-cta">View products <i class="fa-solid fa-arrow-right"></i></span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <h3>No categories yet</h3>
                    <p>Categories will appear here once they are added.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php echo $__env->make('front.partials.cta-band', [
        'title' => 'Can\'t find your category?',
        'text' => 'Tell us what you grow or need. We add new categories every month.',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/category/index.blade.php ENDPATH**/ ?>