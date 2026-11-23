


<?php
    $pages = [
        'want' => [
            'url' => url('i-want'),
            'title' => 'I Want: what buyers need',
            'text' => 'Fresh requirements posted by buyers, traders and shops. If you have it, contact them directly.',
            'badge' => ['icon' => 'fa-cart-shopping', 'label' => 'I Want'],
            'art' => 'fa-basket-shopping',
            'crumb' => 'I Want',
        ],
        'have' => [
            'url' => url('i-have'),
            'title' => 'I Have: ready stock from farmers',
            'text' => 'Crops, seeds and more listed by farmers. Pick what you like and talk to the farmer.',
            'badge' => ['icon' => 'fa-seedling', 'label' => 'I Have'],
            'art' => 'fa-wheat-awn',
            'crumb' => 'I Have',
        ],
        'search' => [
            'url' => url('search'),
            'title' => request('q') ? 'Results for "' . \Illuminate\Support\Str::limit(request('q'), 40) . '"' : 'Search products',
            'text' => 'Results from I Have and I Want listings, plus matching member profiles.',
            'badge' => ['icon' => 'fa-magnifying-glass', 'label' => 'Search'],
            'art' => 'fa-magnifying-glass',
            'crumb' => 'Search',
        ],
    ];
    $page = $pages[$type ?? 'search'];
    $listUrl = fn (array $params) => $page['url'] . (($query = http_build_query(array_filter($params))) ? '?' . $query : '');
?>

<?php $__env->startSection('title', $page['crumb'] . ' - KisanMandi'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/search-profiles.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="inner-page">

    <?php echo $__env->make('front.partials.page-hero', [
        'title' => $page['title'],
        'text' => $page['text'],
        'badge' => $page['badge'],
        'art' => $page['art'],
        'variant' => $type ?? 'green',
        'crumbs' => $activeCategory
            ? [$page['crumb'] => $page['url'], $activeCategory->name => null]
            : [$page['crumb'] => null],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="page-section">
        <div class="container-xl">
            <div class="row g-4">

                
                <div class="col-lg-3 filter-col">
                    <aside class="offcanvas-lg offcanvas-start filter-sidebar" tabindex="-1" id="productFilters" aria-labelledby="productFiltersLabel">
                        <div class="offcanvas-header">
                            <h2 class="offcanvas-title fs-5" id="productFiltersLabel">Filters</h2>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#productFilters" aria-label="Close filters"></button>
                        </div>
                        <div class="offcanvas-body flex-column">

                            <div class="filter-box">
                                <h3 class="filter-title">Search</h3>
                                <form action="<?php echo e($page['url']); ?>" method="GET" class="filter-search" role="search">
                                    <?php if($activeCategory): ?><input type="hidden" name="category" value="<?php echo e($activeCategory->id); ?>"><?php endif; ?>
                                    <?php if(request('sort')): ?><input type="hidden" name="sort" value="<?php echo e(request('sort')); ?>"><?php endif; ?>
                                    <input type="search" name="q" value="<?php echo e(request('q')); ?>" class="form-control" placeholder="e.g. wheat, onion…" aria-label="Search products">
                                    <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                                </form>
                            </div>

                            <div class="filter-box">
                                <h3 class="filter-title">Categories</h3>
                                <ul class="filter-list">
                                    <li>
                                        <a href="<?php echo e($listUrl(['q' => request('q'), 'sort' => request('sort')])); ?>" class="<?php echo e($activeCategory ? '' : 'active'); ?>">
                                            <i class="fa-solid fa-border-all"></i>All categories
                                            <span class="count"><?php echo e($totalCount); ?></span>
                                        </a>
                                    </li>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $look = \App\Support\Market::categoryFor($category->name); ?>
                                        <li>
                                            <a href="<?php echo e($listUrl(['category' => $category->id, 'q' => request('q'), 'sort' => request('sort')])); ?>" class="<?php echo e($activeCategory?->id === $category->id ? 'active' : ''); ?>">
                                                <span class="category-dot dot-<?php echo e($look['color'] ?? 'green'); ?>"></span><?php echo e($category->name); ?>

                                                <span class="count"><?php echo e($category->products_count); ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>

                            <?php if($type === 'want'): ?>
                                <a href="<?php echo e(url('i-have')); ?>" class="switch-box switch-have">
                                    <strong><i class="fa-solid fa-seedling me-1"></i>Looking to buy?</strong>
                                    <span>See ready stock in I Have <i class="fa-solid fa-arrow-right ms-1"></i></span>
                                </a>
                            <?php elseif($type === 'have'): ?>
                                <a href="<?php echo e(url('i-want')); ?>" class="switch-box switch-want">
                                    <strong><i class="fa-solid fa-cart-shopping me-1"></i>Want to sell?</strong>
                                    <span>See what buyers need in I Want <i class="fa-solid fa-arrow-right ms-1"></i></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </aside>
                </div>

                
                <div class="col-lg-9">
                    <div class="listing-toolbar">
                        <p class="result-count">
                            <strong><?php echo e($products->total()); ?></strong>
                            <?php echo e($products->total() === 1 ? 'listing' : 'listings'); ?>

                            <?php if($activeCategory): ?> in <?php echo e($activeCategory->name); ?> <?php endif; ?>
                        </p>
                        <div class="toolbar-actions">
                            <button class="btn btn-filter d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#productFilters" aria-controls="productFilters">
                                <i class="fa-solid fa-sliders me-1"></i>Filters
                            </button>
                            <form action="<?php echo e($page['url']); ?>" method="GET" class="toolbar-sort">
                                <?php $__currentLoopData = ['category' => $activeCategory?->id, 'q' => request('q')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($value): ?><input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>"><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <label for="sortBy" class="visually-hidden">Sort by</label>
                                <select id="sortBy" name="sort" class="form-select" data-auto-submit>
                                    <option value="latest" <?php if(request('sort', 'latest') === 'latest'): echo 'selected'; endif; ?>>Newest first</option>
                                    <option value="oldest" <?php if(request('sort') === 'oldest'): echo 'selected'; endif; ?>>Oldest first</option>
                                    <option value="name" <?php if(request('sort') === 'name'): echo 'selected'; endif; ?>>Name A-Z</option>
                                </select>
                                <noscript><button type="submit" class="btn btn-filter ms-1">Go</button></noscript>
                            </form>
                        </div>
                    </div>

                    <?php if($products->count()): ?>
                        <?php echo $__env->make('front.partials.product-grid', ['products' => $products, 'columns' => 'row-cols-2 row-cols-md-3'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php elseif(isset($profiles) && $profiles->count()): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-user-check"></i>
                            <h3>No products found</h3>
                            <p>But we found matching profiles below.</p>
                            <a href="#profiles" class="btn btn-farm-outline">See profiles</a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-seedling"></i>
                            <h3>No listings found</h3>
                            <p>Try another category or search word. New listings are added every day.</p>
                            <a href="<?php echo e($page['url']); ?>" class="btn btn-farm-outline">Clear filters</a>
                        </div>
                    <?php endif; ?>

                    
                    <?php echo $__env->make('front.partials.profile-search-results', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </section>

    <?php if($type === 'want'): ?>
        <?php echo $__env->make('front.partials.cta-band', ['title' => 'Need something from farmers?', 'text' => 'Register as a Non Farmer and post your requirement for free.'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('front.partials.cta-band', ['title' => 'Have produce to sell?', 'text' => 'Register as a Farmer and list your crop for free. Buyers will call you.'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('front/js/pages.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/products/index.blade.php ENDPATH**/ ?>