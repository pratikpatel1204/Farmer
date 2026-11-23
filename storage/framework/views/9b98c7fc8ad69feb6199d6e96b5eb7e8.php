<?php $__env->startSection('title', 'Blog - ' . config('app.name')); ?>
<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
<?php $__env->stopPush(); ?>

<?php
    // Pehle page par (bina search) sabse naya post bada dikhega
    $showFeatured = $blogs->onFirstPage() && !request('q') && $blogs->count() > 2;
    $featured = $showFeatured ? $blogs->first() : null;
?>

<?php $__env->startSection('content'); ?>
<div class="inner-page">

    <?php echo $__env->make('front.partials.page-hero', [
        'title' => request('q') ? 'Articles about "' . \Illuminate\Support\Str::limit(request('q'), 40) . '"' : 'Farming tips & market news',
        'text' => 'Easy reads on weather, crop care, mandi prices and new farming techniques.',
        'badge' => ['icon' => 'fa-newspaper', 'label' => 'Blog'],
        'variant' => 'have',
        'art' => 'fa-book-open',
        'crumbs' => ['Blog' => null],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="page-section">
        <div class="container-xl">
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <?php if($blogs->count()): ?>
                        <?php if($featured): ?>
                            <?php $featuredUrl = url('blog/' . ($featured->slug ?? $featured->id)); ?>
                            <article class="blog-featured">
                                <a href="<?php echo e($featuredUrl); ?>" class="blog-thumb tint-1">
                                    <?php if(!empty($featured->image)): ?>
                                        <img src="<?php echo e(asset($featured->image)); ?>" alt="<?php echo e($featured->title); ?>">
                                    <?php else: ?>
                                        <i class="fa-solid fa-seedling thumb-icon" aria-hidden="true"></i>
                                    <?php endif; ?>
                                    <span class="blog-date"><?php echo e(\Carbon\Carbon::parse($featured->created_at ?? now())->format('d M Y')); ?></span>
                                </a>
                                <div class="blog-body">
                                    <span class="featured-label"><i class="fa-solid fa-star"></i>Latest</span>
                                    <h2 class="blog-title"><a href="<?php echo e($featuredUrl); ?>"><?php echo e($featured->title); ?></a></h2>
                                    <p class="blog-excerpt"><?php echo e(\Illuminate\Support\Str::limit(strip_tags(preg_replace('#</(p|h[1-6]|li|div)>#i', ' ', $featured->description ?? '')), 180)); ?></p>
                                    <a href="<?php echo e($featuredUrl); ?>" class="blog-more">Read article <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                </div>
                            </article>
                        <?php endif; ?>

                        <div class="row row-cols-1 row-cols-sm-2 g-3 blog-grid">
                            <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($featured && $loop->first) continue; ?>
                                <div class="col">
                                    <?php echo $__env->make('front.partials.blog-card', ['blog' => $blog, 'index' => $loop->index], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php if($blogs->hasPages()): ?>
                            <div class="farm-pagination"><?php echo e($blogs->links('pagination::bootstrap-5')); ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-newspaper"></i>
                            <h3><?php echo e(request('q') ? 'No articles found' : 'New articles are coming soon'); ?></h3>
                            <p><?php echo e(request('q') ? 'Try a different search word.' : 'We are writing useful farming tips for you. Please check back soon.'); ?></p>
                            <?php if(request('q')): ?><a href="<?php echo e(url('blog')); ?>" class="btn btn-farm-outline">See all articles</a><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="col-12 col-lg-4">
                    <?php echo $__env->make('front.blog._sidebar', ['recent' => $recent], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </aside>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/blog/index.blade.php ENDPATH**/ ?>