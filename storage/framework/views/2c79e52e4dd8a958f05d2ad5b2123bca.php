<?php $__env->startSection('title', $category->name . ' - ' . config('app.name')); ?>
<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
<?php $__env->stopPush(); ?>
<?php
    $look = \App\Support\Market::categoryLook($category);
    $baseUrl = url('category/' . \App\Support\Market::slugOf($category));
    $keep = array_filter(['q' => request('q'), 'sort' => request('sort')]);
    $tabUrl = fn($t) => $baseUrl . (($q = http_build_query(array_filter($keep + ['type' => $t]))) ? '?' . $q : '');
?>

<?php $__env->startSection('content'); ?>
    <div class="inner-page">

        <?php echo $__env->make('front.partials.page-hero', [
            'title' => $category->name,
            'text' =>
                'Everything in ' . $category->name . ': ready stock from farmers and requirements from buyers.',
            'badge' => ['icon' => $look['icon'], 'label' => 'Category'],
            'art' => $look['icon'],
            'crumbs' => ['Category' => url('category'), $category->name => null],
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <section class="page-section">
            <div class="container-xl">
                <div class="row g-4">

                    
                    <div class="col-lg-3 filter-col">
                        <aside class="offcanvas-lg offcanvas-start filter-sidebar" tabindex="-1" id="categoryFilters"
                            aria-labelledby="categoryFiltersLabel">
                            <div class="offcanvas-header">
                                <h2 class="offcanvas-title fs-5" id="categoryFiltersLabel">Filters</h2>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                                    data-bs-target="#categoryFilters" aria-label="Close filters"></button>
                            </div>
                            <div class="offcanvas-body flex-column">
                                <div class="filter-box">
                                    <h3 class="filter-title">Search in <?php echo e($category->name); ?></h3>
                                    <form action="<?php echo e($baseUrl); ?>" method="GET" class="filter-search" role="search">
                                        <?php if($type): ?>
                                            <input type="hidden" name="type" value="<?php echo e($type); ?>">
                                        <?php endif; ?>
                                        <input type="search" name="q" value="<?php echo e(request('q')); ?>"
                                            class="form-control" placeholder="Search…"
                                            aria-label="Search in <?php echo e($category->name); ?>">
                                        <button type="submit" aria-label="Search"><i
                                                class="fa-solid fa-magnifying-glass"></i></button>
                                    </form>
                                </div>

                                <div class="filter-box">
                                    <h3 class="filter-title">All categories</h3>
                                    <ul class="filter-list">
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <a href="<?php echo e(url('category/' . \App\Support\Market::slugOf($item))); ?>"
                                                    class="<?php echo e($item->id === $category->id ? 'active' : ''); ?>">
                                                    <span
                                                        class="category-dot dot-<?php echo e(\App\Support\Market::categoryLook($item)['color']); ?>"></span><?php echo e($item->name); ?>

                                                    <span class="count"><?php echo e($item->products_count); ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div>

                    
                    <div class="col-lg-9">
                        <div class="listing-toolbar">
                            <nav class="type-tabs" aria-label="Listing type">
                                <a href="<?php echo e($tabUrl(null)); ?>" class="<?php echo e($type ? '' : 'active'); ?>">All</a>
                                <a href="<?php echo e($tabUrl('have')); ?>" class="<?php echo e($type === 'have' ? 'active' : ''); ?>"><i
                                        class="fa-solid fa-seedling"></i>I Have</a>
                                <a href="<?php echo e($tabUrl('want')); ?>" class="<?php echo e($type === 'want' ? 'active' : ''); ?>"><i
                                        class="fa-solid fa-cart-shopping"></i>I Want</a>
                            </nav>
                            <div class="toolbar-actions">
                                <button class="btn btn-filter d-lg-none" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#categoryFilters" aria-controls="categoryFilters">
                                    <i class="fa-solid fa-sliders me-1"></i>Filters
                                </button>
                                <form action="<?php echo e($baseUrl); ?>" method="GET" class="toolbar-sort">
                                    <?php $__currentLoopData = ['type' => $type, 'q' => request('q')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($value): ?>
                                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <label for="sortBy" class="visually-hidden">Sort by</label>
                                    <select id="sortBy" name="sort" class="form-select" data-auto-submit>
                                        <option value="latest" <?php if(request('sort', 'latest') === 'latest'): echo 'selected'; endif; ?>>Newest first</option>
                                        <option value="oldest" <?php if(request('sort') === 'oldest'): echo 'selected'; endif; ?>>Oldest first</option>
                                        <option value="name" <?php if(request('sort') === 'name'): echo 'selected'; endif; ?>>Name A-Z</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <p class="result-count mb-3"><strong><?php echo e($products->total()); ?></strong>
                            <?php echo e($products->total() === 1 ? 'listing' : 'listings'); ?> found</p>

                        <?php if($products->count()): ?>
                            <?php echo $__env->make('front.partials.product-grid', ['products' => $products], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa-solid <?php echo e($look['icon']); ?>"></i>
                                <h3>Nothing here yet</h3>
                                <p>No <?php echo e($type === 'want' ? 'requirements' : 'listings'); ?> in <?php echo e($category->name); ?> right
                                    now. Be the first to post one.</p>
                                <a href="<?php echo e(url('register')); ?>" class="btn btn-farm">Post a listing</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('front/js/pages.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/category/show.blade.php ENDPATH**/ ?>