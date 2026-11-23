<?php $__env->startSection('title', 'Home Page - ' . config('app.name')); ?>
<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
<?php $__env->stopPush(); ?>
<?php
    $thumbIcons = [
        ['fa-carrot', 'thumb-red'],
        ['fa-wheat-awn', 'thumb-yellow'],
        ['fa-seedling', 'thumb-lime'],
        ['fa-apple-whole', 'thumb-red'],
        ['fa-tractor', 'thumb-brown'],
        ['fa-lemon', 'thumb-yellow'],
    ];
?>

<?php $__env->startSection('content'); ?>
    <div class="home-page">
        <?php
            $slideColors = ['slide-green', 'slide-sun', 'slide-soil'];
            $banners = $banners ?? collect();
        ?>
        
        <?php echo $__env->make('front.partials.hero-section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        
        <section class="home-section product-section section-want">
            <div class="container-fluid">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-cart-shopping"></i>I Want</span>
                        <h2 class="section-title">Buyers are looking for</h2>
                        <p class="section-subtitle">Latest requirements from buyers. If you have it, get in touch.</p>
                    </div>
                    <div class="section-actions">
                        <button class="slider-arrow" type="button" data-slider-prev="#wantSlider" aria-label="Previous"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <button class="slider-arrow" type="button" data-slider-next="#wantSlider" aria-label="Next"><i
                                class="fa-solid fa-chevron-right"></i></button>
                        <a href="<?php echo e(url('i-want')); ?>" class="btn btn-view-all">View all</a>
                    </div>
                </div>

                <?php if($wantProducts->count()): ?>
                    <div id="wantSlider" class="auto-slider" data-autoplay="3000">
                        <div class="slider-track">
                            <?php $__currentLoopData = $wantProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php [$icon, $tint] = $thumbIcons[$loop->index % count($thumbIcons)]; ?>
                                <div class="slider-item">
                                    <?php echo $__env->make('front.partials.product-card', [
                                        'product' => $product,
                                        'type' => 'want',
                                        'icon' => $icon,
                                        'tint' => $tint,
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-note">No requirements posted yet.</div>
                <?php endif; ?>
            </div>
        </section>

        
        <section class="home-section product-section section-have">
            <div class="container-fluid">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-seedling"></i>I Have</span>
                        <h2 class="section-title">Ready stock from farmers</h2>
                        <p class="section-subtitle">Straight from the farm. Like something? Talk to the farmer.</p>
                    </div>
                    <div class="section-actions">
                        <button class="slider-arrow" type="button" data-slider-prev="#haveSlider"
                            aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="slider-arrow" type="button" data-slider-next="#haveSlider" aria-label="Next"><i
                                class="fa-solid fa-chevron-right"></i></button>
                        <a href="<?php echo e(url('i-have')); ?>" class="btn btn-view-all">View all</a>
                    </div>
                </div>

                <?php if($haveProducts->count()): ?>
                    <div id="haveSlider" class="auto-slider" data-autoplay="3500">
                        <div class="slider-track">
                            <?php $__currentLoopData = $haveProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php [$icon, $tint] = $thumbIcons[($loop->index + 2) % count($thumbIcons)]; ?>
                                <div class="slider-item">
                                    <?php echo $__env->make('front.partials.product-card', [
                                        'product' => $product,
                                        'type' => 'have',
                                        'icon' => $icon,
                                        'tint' => $tint,
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-note">No products listed yet.</div>
                <?php endif; ?>
            </div>
        </section>

        
        <section class="home-section profile-section">
            <div class="container-fluid">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-users"></i>Our members</span>
                        <h2 class="section-title">Farmers and buyers in one place</h2>
                        <p class="section-subtitle">Browse Farmer and Non Farmer profiles and connect directly.</p>
                    </div>
                    <div class="section-actions flex-wrap">
                        <div class="profile-filter" data-profile-filter="#profileSlider" role="group"
                            aria-label="Profile filter">
                            <button type="button" class="btn active" data-role="all" aria-pressed="true">All</button>
                            <button type="button" class="btn" data-role="farmer"
                                aria-pressed="false">Farmer</button>
                            <button type="button" class="btn" data-role="nonfarmer" aria-pressed="false">Non
                                Farmer</button>
                        </div>
                        <button class="slider-arrow" type="button" data-slider-prev="#profileSlider"
                            aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="slider-arrow" type="button" data-slider-next="#profileSlider"
                            aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>

                <?php if($profiles->count()): ?>
                    <div id="profileSlider" class="auto-slider" data-autoplay="4000">
                        <div class="slider-track">
                            <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isFarmer = $member->role === 'Farmer';
                                    $roleKey = $isFarmer ? 'farmer' : 'nonfarmer';
                                    $initials = collect(explode(' ', trim($member->full_name)))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                        ->implode('');
                                ?>
                                <div class="slider-item" data-role="<?php echo e($roleKey); ?>">
                                    <div class="profile-card role-<?php echo e($roleKey); ?>">
                                        <div class="profile-avatar">
                                            <?php if($member->profile_image): ?>
                                                <img src="<?php echo e(asset($member->profile_image)); ?>"
                                                    alt="<?php echo e($member->full_name); ?>" loading="lazy">
                                            <?php else: ?>
                                                <?php echo e($initials); ?>

                                            <?php endif; ?>
                                        </div>
                                        <?php if(optional($member->profile)->status === 'Approved'): ?>
                                            <span class="verified-tick" title="Verified">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </span>
                                        <?php endif; ?>
                                        <h3 class="profile-name"><?php echo e($member->full_name); ?></h3>
                                        <span class="role-badge">
                                            <i class="fa-solid <?php echo e($isFarmer ? 'fa-seedling' : 'fa-store'); ?>"></i>
                                            <?php echo e($member->role); ?>

                                        </span>
                                        <?php
                                            $city = $member->citys->name ?? null;
                                            $state = $member->states->name ?? null;
                                        ?>
                                        <p class="profile-location">
                                            <i class="fa-solid fa-location-dot me-1"></i>
                                            <?php echo e(collect([$city, $state])->filter()->implode(', ') ?:'India'); ?>

                                        </p>
                                        <?php if($member->area): ?>
                                            <div class="profile-area">
                                                <i class="fa-solid fa-map-pin"></i>
                                                <span><?php echo e($member->area); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <a href="<?php echo e(url('profile/' . $member->unique_id)); ?>" class="btn btn-profile">
                                            <i class="fa-solid fa-user me-2"></i>View profile
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-note">No profiles yet.</div>
                <?php endif; ?>
            </div>
        </section>

        
        <section class="home-section blog-section">
            <div class="container-fluid">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-newspaper"></i>Blog</span>
                        <h2 class="section-title">Useful farming tips</h2>
                        <p class="section-subtitle">Simple articles on weather, mandi prices and new techniques.</p>
                    </div>
                    <div class="section-actions">
                        <a href="<?php echo e(url('blog')); ?>" class="btn btn-view-all">All posts</a>
                    </div>
                </div>

                <?php if($blogs->count()): ?>
                    <div class="row g-3 g-lg-4">
                        <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $blogUrl = url('blog/' . ($blog->slug ?? $blog->id)); ?>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <article class="blog-card">
                                    <a href="<?php echo e($blogUrl); ?>" class="blog-thumb">
                                        <?php if(!empty($blog->image)): ?>
                                            <img src="<?php echo e(asset($blog->image)); ?>" alt="<?php echo e($blog->title ?? ''); ?>"
                                                loading="lazy">
                                        <?php else: ?>
                                            <i class="fa-solid fa-leaf thumb-icon" aria-hidden="true"></i>
                                        <?php endif; ?>
                                        <span
                                            class="blog-date"><?php echo e(\Carbon\Carbon::parse($blog->created_at ?? now())->format('d M Y')); ?></span>
                                    </a>
                                    <div class="blog-body">
                                        <h3 class="blog-title"><a
                                                href="<?php echo e($blogUrl); ?>"><?php echo e($blog->title ?? ''); ?></a></h3>
                                        <p class="blog-excerpt">
                                            <?php echo e(\Illuminate\Support\Str::limit(strip_tags(preg_replace('#</(p|h[1-6]|li|div)>#i', ' ', $blog->description ?? '')), 120)); ?>

                                        </p>
                                        <a href="<?php echo e($blogUrl); ?>" class="blog-more">Read more <i
                                                class="fa-solid fa-arrow-right ms-1"></i></a>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="empty-note">New blog posts are coming soon.</div>
                <?php endif; ?>
            </div>
        </section>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('front/js/home.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/home.blade.php ENDPATH**/ ?>