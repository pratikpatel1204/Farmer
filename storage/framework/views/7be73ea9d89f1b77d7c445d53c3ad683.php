<?php $__env->startSection('title', $details['name'] .' - ' . config('app.name')); ?>
<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
<?php $__env->stopPush(); ?>
<?php
    $isFarmer = $details['role'] === 'Farmer';
    $initials = collect(explode(' ', trim($details['name'])))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $location = collect([$details['area'], $details['city'], $details['state']])->filter()->implode(', ');
    $place = collect([$details['city'], $details['state']])->filter()->implode(', ');
    $linkIcons = [
        'website'   => ['fa-solid fa-globe', 'Website'],
        'facebook'  => ['fa-brands fa-facebook-f', 'Facebook'],
        'instagram' => ['fa-brands fa-instagram', 'Instagram'],
        'youtube'   => ['fa-brands fa-youtube', 'YouTube'],
        'linkedin'  => ['fa-brands fa-linkedin-in', 'LinkedIn'],
    ];
    // Login nahi hai to login page; login ke baad aapka enquiry / chat route yahan laga sakte hain
    $contactUrl = auth()->check() ? url('contact-us') : url('login');
    $profileUrl = url('profile/' . $details['unique_id']);
    $fallbackIcons = [['fa-carrot', 'thumb-red'], ['fa-wheat-awn', 'thumb-yellow'], ['fa-seedling', 'thumb-lime'], ['fa-apple-whole', 'thumb-red']];
    $cardData = function ($product, $index) use ($fallbackIcons) {
        $cat = \App\Support\Market::categoryFor(data_get($product, 'categorys.name'));
        return $cat ? [$cat['icon'], 'thumb-' . $cat['color']] : $fallbackIcons[$index % count($fallbackIcons)];
    };
?>

<?php $__env->startSection('content'); ?>
<div class="inner-page">

    
    <div class="page-hero-wrap">
        <div class="container-xl">
            <section class="profile-hero <?php echo e($isFarmer ? 'is-farmer' : 'is-nonfarmer'); ?>">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>

                <div class="profile-hero-main">
                    <div class="profile-hero-avatar">
                        <?php if($details['image']): ?>
                            <img src="<?php echo e(asset($details['image'])); ?>" alt="<?php echo e($details['name']); ?>">
                        <?php else: ?>
                            <?php echo e($initials); ?>

                        <?php endif; ?>
                    </div>

                    <div class="profile-hero-info">
                        <h1 class="profile-hero-name">
                            <?php echo e($details['name']); ?>

                            <i class="fa-solid fa-circle-check verified" title="Verified profile" aria-label="Verified profile"></i>
                        </h1>
                        <div class="profile-hero-meta">
                            <span class="role-badge <?php echo e($isFarmer ? 'badge-farmer' : 'badge-nonfarmer'); ?>">
                                <i class="fa-solid <?php echo e($isFarmer ? 'fa-seedling' : 'fa-store'); ?>"></i><?php echo e($details['role']); ?>

                            </span>
                            <?php if($place): ?><span><i class="fa-solid fa-location-dot"></i><?php echo e($place); ?></span><?php endif; ?>
                            <?php if($details['since']): ?><span><i class="fa-regular fa-calendar"></i>Member since <?php echo e($details['since']); ?></span><?php endif; ?>
                            <span><i class="fa-solid fa-id-badge"></i><?php echo e($details['unique_id']); ?></span>
                        </div>
                    </div>

                    <div class="profile-hero-actions">
                        <a href="<?php echo e($contactUrl); ?>" class="btn btn-farm btn-farm-yellow">
                            <i class="fa-solid fa-comments me-1"></i><?php echo e(auth()->check() ? 'Contact' : 'Login to contact'); ?>

                        </a>
                        <button type="button" class="btn btn-light-glass" data-copy-link="<?php echo e($profileUrl); ?>" title="Copy link">
                            <i class="fa-solid fa-share-nodes me-1"></i>Share
                        </button>
                    </div>
                </div>

                <div class="profile-hero-stats">
                    <div><strong><?php echo e($products->count()); ?></strong><span>Listings</span></div>
                    <div><strong><?php echo e($haveProducts->count()); ?></strong><span>I Have</span></div>
                    <div><strong><?php echo e($wantProducts->count()); ?></strong><span>I Want</span></div>
                    <div><strong><?php echo e($details['established'] ?: '-'); ?></strong><span>Since</span></div>
                </div>
            </section>
        </div>
    </div>

    
    <section class="page-section">
        <div class="container-xl">
            <div class="row g-4">
                <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                    <div class="farm-card">
                        <h2 class="farm-card-title"><i class="fa-solid fa-user"></i>About <?php echo e($details['name']); ?></h2>
                        <?php if($details['description']): ?>
                            <div class="rich-text"><?php echo $details['description']; ?></div>
                        <?php else: ?>
                            <p class="text-body-secondary mb-0">This member has not added a description yet.</p>
                        <?php endif; ?>
                    </div>

                    <?php if($details['photos']): ?>
                        <div class="farm-card">
                            <h2 class="farm-card-title"><i class="fa-solid fa-images"></i>Photos</h2>
                            <div class="profile-gallery">
                                <?php $__currentLoopData = $details['photos']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(asset($photo)); ?>" target="_blank" rel="noopener" aria-label="Open photo <?php echo e($loop->iteration); ?>">
                                        <img src="<?php echo e(asset($photo)); ?>" alt="<?php echo e($details['name']); ?> photo <?php echo e($loop->iteration); ?>" loading="lazy">
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($details['video']): ?>
                        <div class="farm-card">
                            <h2 class="farm-card-title"><i class="fa-solid fa-video"></i>Video</h2>
                            <video class="profile-video" src="<?php echo e(asset($details['video'])); ?>" controls preload="metadata" playsinline></video>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="col-12 col-lg-4 d-flex flex-column gap-4">
                    <div class="farm-card">
                        <h2 class="farm-card-title"><i class="fa-solid fa-briefcase"></i><?php echo e($isFarmer ? 'Farm details' : 'Business details'); ?></h2>
                        <ul class="seller-facts m-0">
                            <?php if($details['established']): ?>
                                <li><i class="fa-solid fa-calendar-check"></i><span>Established</span><strong><?php echo e($details['established']); ?></strong></li>
                            <?php endif; ?>
                            <?php if($details['workers']): ?>
                                <li><i class="fa-solid fa-people-group"></i><span>Workers</span><strong><?php echo e(number_format((float) $details['workers'])); ?></strong></li>
                            <?php endif; ?>
                            <?php if((float) $details['land'] > 0): ?>
                                <li><i class="fa-solid fa-ruler-combined"></i><span>Area</span><strong><?php echo e(rtrim(rtrim(number_format((float) $details['land'], 2), '0'), '.')); ?> sq. yard</strong></li>
                            <?php endif; ?>
                            <?php if($location): ?>
                                <li><i class="fa-solid fa-location-dot"></i><span>Location</span><strong class="text-wrap text-end"><?php echo e($location); ?></strong></li>
                            <?php endif; ?>
                            <?php if($details['pincode']): ?>
                                <li><i class="fa-solid fa-map-pin"></i><span>Pincode</span><strong><?php echo e($details['pincode']); ?></strong></li>
                            <?php endif; ?>
                            <li><i class="fa-solid fa-shield-halved"></i><span>Status</span><strong class="text-success">Verified</strong></li>
                        </ul>
                    </div>

                    <?php if($details['links']): ?>
                        <div class="farm-card">
                            <h2 class="farm-card-title"><i class="fa-solid fa-link"></i>Find online</h2>
                            <div class="profile-links">
                                <?php $__currentLoopData = $details['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($url); ?>" target="_blank" rel="noopener nofollow" class="profile-link link-<?php echo e($key); ?>">
                                        <i class="<?php echo e($linkIcons[$key][0]); ?>"></i><?php echo e($linkIcons[$key][1]); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="detail-note m-0">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Always check the goods before you pay. KisanMandi never asks for advance payment.</span>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    
    <section class="page-section bg-white-section" id="member-products">
        <div class="container-xl">
            <div class="section-head">
                <div>
                    <span class="section-eyebrow"><i class="fa-solid fa-box-open"></i>Listings</span>
                    <h2 class="section-title">Products by <?php echo e($details['name']); ?></h2>
                </div>
                <?php if($haveProducts->count() && $wantProducts->count()): ?>
                    <nav class="type-tabs" role="tablist" aria-label="Listing type">
                        <a href="#tabAll" class="active" data-bs-toggle="tab" role="tab" aria-controls="tabAll" aria-selected="true">All (<?php echo e($products->count()); ?>)</a>
                        <a href="#tabHave" data-bs-toggle="tab" role="tab" aria-controls="tabHave" aria-selected="false"><i class="fa-solid fa-seedling"></i>I Have (<?php echo e($haveProducts->count()); ?>)</a>
                        <a href="#tabWant" data-bs-toggle="tab" role="tab" aria-controls="tabWant" aria-selected="false"><i class="fa-solid fa-cart-shopping"></i>I Want (<?php echo e($wantProducts->count()); ?>)</a>
                    </nav>
                <?php endif; ?>
            </div>

            <?php if($products->count()): ?>
                <div class="tab-content">
                    <?php $__currentLoopData = ['tabAll' => $products, 'tabHave' => $haveProducts, 'tabWant' => $wantProducts]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paneId => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($paneId !== 'tabAll' && !($haveProducts->count() && $wantProducts->count())) continue; ?>
                        <div class="tab-pane fade <?php echo e($paneId === 'tabAll' ? 'show active' : ''); ?>" id="<?php echo e($paneId); ?>" role="tabpanel">
                            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-sm-3 product-grid">
                                <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php [$icon, $tint] = $cardData($product, $loop->index); ?>
                                    <div class="col">
                                        <?php echo $__env->make('front.partials.product-card', ['product' => $product, 'type' => \App\Support\Market::typeOf($product), 'icon' => $icon, 'tint' => $tint], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-box-open"></i>
                    <h3>No listings yet</h3>
                    <p><?php echo e($details['name']); ?> has not posted any products yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('front/js/pages.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/profiles/show.blade.php ENDPATH**/ ?>