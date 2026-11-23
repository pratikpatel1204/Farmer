<?php $__env->startSection('title', ($blog->meta_title ?: $blog->title) . ' - ' . config('app.name')); ?>
<?php $__env->startPush('meta'); ?>
    <meta name="description" content="<?php echo e($blog->meta_description ?: $blog->excerpt); ?>">
    <meta property="og:title" content="<?php echo e($blog->meta_title ?: $blog->title); ?>">
    <meta property="og:description" content="<?php echo e($blog->meta_description ?: $blog->excerpt); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo e($blog->url); ?>">
    <?php if($blog->image): ?>
        <meta property="og:image" content="<?php echo e(asset($blog->image)); ?>">
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="inner-page">
        <section class="post-header">
            <div class="container-xl">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <nav class="detail-breadcrumb" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo e(url('blog')); ?>">Blog</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <?php echo e(\Illuminate\Support\Str::limit($blog->title, 40)); ?></li>
                            </ol>
                        </nav>

                        <article class="farm-card p-0 overflow-hidden">
                            <div class="post-body pb-0">
                                <span class="section-eyebrow"><i class="fa-solid fa-newspaper"></i>Blog</span>
                                <h1 class="post-title"><?php echo e($blog->title); ?></h1>
                                <div class="post-meta">
                                    <span><i class="fa-regular fa-calendar"></i><?php echo e($blog->display_date); ?></span>
                                    <span><i class="fa-regular fa-clock"></i><?php echo e($blog->read_minutes); ?> min read</span>
                                    <span><i
                                            class="fa-solid fa-user-pen"></i><?php echo e($blog->author_name ?: 'KisanMandi Team'); ?></span>
                                    <span><i class="fa-regular fa-eye"></i><?php echo e(number_format($blog->views)); ?> views</span>
                                </div>
                            </div>

                            <div class="px-3 px-md-4">
                                <div class="post-cover">
                                    <?php if(!empty($blog->image)): ?>
                                        <img src="<?php echo e(asset($blog->image)); ?>" alt="<?php echo e($blog->title); ?>">
                                    <?php else: ?>
                                        <i class="fa-solid fa-seedling thumb-icon" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="post-body pt-0">
                                
                                <div class="rich-text"><?php echo $blog->description ?? ''; ?></div>
                            </div>
                        </article>

                        <?php if($previous || $next): ?>
                            <nav class="post-nav" aria-label="More articles">
                                <?php if($previous): ?>
                                    <a href="<?php echo e($previous->url); ?>" class="prev"><small><i
                                                class="fa-solid fa-arrow-left me-1"></i>Previous</small><strong><?php echo e($previous->title); ?></strong></a>
                                <?php else: ?>
                                    <span></span>
                                <?php endif; ?>
                                <?php if($next): ?>
                                    <a href="<?php echo e($next->url); ?>" class="next"><small>Next<i
                                                class="fa-solid fa-arrow-right ms-1"></i></small><strong><?php echo e($next->title); ?></strong></a>
                                <?php endif; ?>
                            </nav>
                        <?php endif; ?>
                    </div>

                    <aside class="col-12 col-lg-4 pt-lg-5">
                        <?php echo $__env->make('front.blog._sidebar', ['recent' => $recent], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </aside>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('front/js/pages.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/blog/show.blade.php ENDPATH**/ ?>