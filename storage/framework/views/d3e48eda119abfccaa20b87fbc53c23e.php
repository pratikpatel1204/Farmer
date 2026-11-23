<?php $__env->startSection('title', $product->title . ' - ' . config('app.name')); ?>
<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/auth.css')); ?>">
<?php $__env->stopPush(); ?>
<?php
    $isWant = $type === 'want';
    $category = $product->categorys;
    $cat = \App\Support\Market::categoryFor($category->name ?? $product->category);
    $icon = $cat['icon'] ?? 'fa-seedling';
    $tint = $cat['color'] ?? 'green';
    $tags = collect(explode(',', (string) $product->product_tags))->map(fn ($t) => trim($t))->filter();

    $ownerName = $member->full_name ?? 'Member';
    $ownerRole = $member->role ?? ($isWant ? 'Non Farmer' : 'Farmer');
    $isFarmer = $ownerRole === 'Farmer';
    $location = collect([$member?->citys?->name, $member?->states?->name])->filter()->implode(', ') ?: ($member->area ?? null);
    $initials = collect(explode(' ', trim($ownerName)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

    $isVerified = ($profile->status ?? null) === 'Approved';
    $companyImages = \App\Support\Market::imageList($profile->company_images ?? null);
    $socialIcons = ['facebook' => 'fa-facebook-f', 'instagram' => 'fa-instagram', 'youtube' => 'fa-youtube', 'twitter' => 'fa-x-twitter', 'x' => 'fa-x-twitter', 'linkedin' => 'fa-linkedin-in', 'whatsapp' => 'fa-whatsapp'];

    $listUrl = url($isWant ? 'i-want' : 'i-have');
    $categoryUrl = $category ? $listUrl . '?category=' . $category->id : null;
    $profileUrl = !empty($member?->unique_id) ? url('profile/' . $member->unique_id) : null;
    // Enquiry: login ho to form wala modal, warna "pehle login karein" modal. Apni listing par button band.
    $authUser = auth()->user();
    $isOwner = $authUser && (int) $authUser->id === (int) $product->member_id;
    $enquiryModal = $authUser ? '#enquiryModal' : '#loginFirstModal';
    $enquiryLabel = $isWant ? 'Submit Request' : 'Contact seller';
    $partyName = $isWant ? 'buyer' : 'seller';
    $attributes = $attributes ?? collect();
?>

<?php $__env->startSection('content'); ?>
<div class="inner-page">
    <section class="product-detail">
        <div class="container-xl">

            <nav class="detail-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e($listUrl); ?>"><?php echo e($isWant ? 'I Want' : 'I Have'); ?></a></li>
                    <?php if($category): ?>
                        <li class="breadcrumb-item"><a href="<?php echo e($categoryUrl); ?>"><?php echo e($category->name); ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(\Illuminate\Support\Str::limit($product->title, 40)); ?></li>
                </ol>
            </nav>

            <div class="row g-4">
                
                <div class="col-12 col-lg-5">
                    <div class="gallery-main <?php echo e($images ? '' : 'thumb-' . $tint); ?>">
                        <?php if($images): ?>
                            <img id="galleryMain" src="<?php echo e(asset($images[0])); ?>" alt="<?php echo e($product->title); ?>">
                        <?php else: ?>
                            <i class="fa-solid <?php echo e($icon); ?> thumb-icon" aria-hidden="true"></i>
                        <?php endif; ?>
                        <span class="product-type <?php echo e($isWant ? 'type-want' : 'type-have'); ?>">
                            <i class="fa-solid <?php echo e($isWant ? 'fa-cart-shopping' : 'fa-seedling'); ?>"></i><?php echo e($isWant ? 'Wanted' : 'Available'); ?>

                        </span>
                    </div>

                    <?php if(count($images) > 1): ?>
                        <div class="gallery-thumbs" role="group" aria-label="Product photos">
                            <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" class="gallery-thumb <?php echo e($loop->first ? 'active' : ''); ?>" data-gallery-thumb="#galleryMain" data-src="<?php echo e(asset($img)); ?>" aria-label="Photo <?php echo e($loop->iteration); ?>" aria-pressed="<?php echo e($loop->first ? 'true' : 'false'); ?>">
                                    <img src="<?php echo e(asset($img)); ?>" alt="" loading="lazy">
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="col-12 col-md-7 col-lg-4">
                    <div class="farm-card detail-info">
                        <?php if($category): ?>
                            <a href="<?php echo e($categoryUrl); ?>" class="detail-category tint-<?php echo e($tint); ?>"><i class="fa-solid <?php echo e($icon); ?>"></i><?php echo e($category->name); ?></a>
                        <?php endif; ?>

                        <h1 class="detail-title"><?php echo e($product->title); ?></h1>

                        <div class="detail-meta">
                            <?php if($location): ?><span><i class="fa-solid fa-location-dot"></i><?php echo e($location); ?></span><?php endif; ?>
                            <?php if($product->created_at): ?><span><i class="fa-regular fa-calendar"></i>Posted <?php echo e(\Carbon\Carbon::parse($product->created_at)->format('d M Y')); ?></span><?php endif; ?>
                        </div>

                        <?php if($tags->count()): ?>
                            <div class="tag-list">
                                <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($tag); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                        <?php if($product->description): ?>
                            <p class="detail-lead"><?php echo e(\Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags(str_replace('</p>', ' ', $product->description)))), 180)); ?></p>
                        <?php endif; ?>

                        <div class="detail-actions">
                            <?php if($isOwner): ?>
                                <span class="btn btn-farm-outline flex-fill disabled" aria-disabled="true"><i class="fa-solid fa-user-check me-1"></i>This is your listing</span>
                            <?php else: ?>
                                <button type="button" class="btn btn-farm <?php echo e($isWant ? 'btn-farm-red' : ''); ?> flex-fill" data-bs-toggle="modal" data-bs-target="<?php echo e($enquiryModal); ?>">
                                    <i class="fa-solid <?php echo e($isWant ? 'fa-hand-holding-heart' : 'fa-phone'); ?> me-1"></i><?php echo e($enquiryLabel); ?>

                                </button>
                            <?php endif; ?>
                            <?php if($product->brochure): ?>
                                <a href="<?php echo e(asset($product->brochure)); ?>" class="btn btn-farm-outline flex-fill" target="_blank" rel="noopener" download>
                                    <i class="fa-solid fa-file-arrow-down me-1"></i>Brochure
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="detail-note">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Talk to the <?php echo e($isWant ? 'buyer' : 'seller'); ?> and check the goods before you pay. KisanMandi never asks for advance payment.</span>
                        </div>
                    </div>
                </div>

                
                <div class="col-12 col-md-5 col-lg-3">
                    <div class="farm-card seller-card">
                        <div class="profile-avatar <?php echo e($isFarmer ? '' : 'bg-brown'); ?>">
                            <?php if(!empty($member?->profile_image)): ?>
                                <img src="<?php echo e(asset($member->profile_image)); ?>" alt="<?php echo e($ownerName); ?>">
                            <?php else: ?>
                                <?php echo e($initials); ?>

                            <?php endif; ?>
                        </div>
                        <h2 class="seller-name">
                            <?php echo e($ownerName); ?>

                            <?php if($isVerified): ?><i class="fa-solid fa-circle-check" title="Verified"></i><?php endif; ?>
                        </h2>
                        <span class="role-badge <?php echo e($isFarmer ? 'badge-farmer' : 'badge-nonfarmer'); ?>">
                            <i class="fa-solid <?php echo e($isFarmer ? 'fa-seedling' : 'fa-store'); ?>"></i><?php echo e($ownerRole); ?>

                        </span>

                        <ul class="seller-facts">
                            <?php if($location): ?>
                                <li><i class="fa-solid fa-location-dot"></i><span>Location</span><strong><?php echo e($location); ?></strong></li>
                            <?php endif; ?>
                            <?php if(!empty($profile->establishment_year)): ?>
                                <li><i class="fa-solid fa-calendar-check"></i><span>Since</span><strong><?php echo e($profile->establishment_year); ?></strong></li>
                            <?php endif; ?>
                            <?php if(!empty($profile->website)): ?>
                                <li><i class="fa-solid fa-globe"></i><span>Website</span><a href="<?php echo e(\Illuminate\Support\Str::startsWith($profile->website, 'http') ? $profile->website : 'https://' . $profile->website); ?>" target="_blank" rel="noopener nofollow"><?php echo e(preg_replace('#^https?://(www\.)?#', '', $profile->website)); ?></a></li>
                            <?php endif; ?>
                        </ul>

                        <?php if($socials): ?>
                            <div class="social-links">
                                <?php $__currentLoopData = $socials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(is_string($link) && \Illuminate\Support\Str::startsWith($link, ['http://', 'https://'])): ?>
                                        <a href="<?php echo e($link); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php echo e(ucfirst($name)); ?>"><i class="fa-brands <?php echo e($socialIcons[strtolower($name)] ?? 'fa-link'); ?>"></i></a>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                        <?php if($profileUrl): ?>
                            <a href="<?php echo e($profileUrl); ?>" class="btn btn-profile">View full profile</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="farm-card mt-4">
                <ul class="nav detail-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tabDescBtn" data-bs-toggle="tab" data-bs-target="#tabDesc" type="button" role="tab" aria-controls="tabDesc" aria-selected="true">Description</button>
                    </li>
                    <?php if($attributes->count()): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tabSpecBtn" data-bs-toggle="tab" data-bs-target="#tabSpec" type="button" role="tab" aria-controls="tabSpec" aria-selected="false">Specification</button>
                        </li>
                    <?php endif; ?>
                    <?php if(!empty($profile->description) || $companyImages): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tabSellerBtn" data-bs-toggle="tab" data-bs-target="#tabSeller" type="button" role="tab" aria-controls="tabSeller" aria-selected="false">About the <?php echo e($isWant ? 'buyer' : 'seller'); ?></button>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active rich-text" id="tabDesc" role="tabpanel" aria-labelledby="tabDescBtn" tabindex="0">
                        
                        <?php if($product->description): ?>
                            <p><?php echo nl2br(e(trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $product->description)))))); ?></p>
                        <?php else: ?>
                            <p>No description added.</p>
                        <?php endif; ?>
                    </div>
                    <?php if($attributes->count()): ?>
                        <div class="tab-pane fade" id="tabSpec" role="tabpanel" aria-labelledby="tabSpecBtn" tabindex="0">
                            <table class="spec-table">
                                <tbody>
                                    <?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <th scope="row"><?php echo e($attr->label); ?></th>
                                            <td><?php echo e($attr->value); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($profile->description) || $companyImages): ?>
                        <div class="tab-pane fade rich-text" id="tabSeller" role="tabpanel" aria-labelledby="tabSellerBtn" tabindex="0">
                            <?php if(!empty($profile->description)): ?>
                                <?php echo \App\Support\Market::safeHtml($profile->description); ?>

                            <?php endif; ?>
                            <?php if($companyImages): ?>
                                <div class="company-gallery mt-3">
                                    <?php $__currentLoopData = $companyImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <img src="<?php echo e(asset($img)); ?>" alt="<?php echo e($ownerName); ?> photo <?php echo e($loop->iteration); ?>" loading="lazy">
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    
    <?php if($related->count()): ?>
        <section class="home-section product-section">
            <div class="container-xl">
                <div class="section-head">
                    <div>
                        <span class="section-eyebrow"><i class="fa-solid fa-layer-group"></i>More like this</span>
                        <h2 class="section-title">Related listings</h2>
                    </div>
                    <div class="section-actions">
                        <button class="slider-arrow" type="button" data-slider-prev="#relatedSlider" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="slider-arrow" type="button" data-slider-next="#relatedSlider" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div id="relatedSlider" class="auto-slider" data-autoplay="3500">
                    <div class="slider-track">
                        <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $itemCat = \App\Support\Market::categoryFor($item->categorys->name ?? $item->category); ?>
                            <div class="slider-item">
                                <?php echo $__env->make('front.partials.product-card', [
                                    'product' => $item,
                                    'type' => \App\Support\Market::typeOf($item),
                                    'icon' => $itemCat['icon'] ?? 'fa-seedling',
                                    'tint' => 'thumb-' . ($itemCat['color'] ?? 'green'),
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>
<?php echo $__env->make('front.products._enquiry-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('front/js/home.js')); ?>"></script>
    <script src="<?php echo e(asset('front/js/pages.js')); ?>"></script>
    <script src="<?php echo e(asset('front/js/enquiry.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/products/show.blade.php ENDPATH**/ ?>