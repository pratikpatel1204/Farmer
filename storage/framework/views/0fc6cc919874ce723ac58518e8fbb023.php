
<?php if(isset($profiles) && $profiles->count()): ?>
    <div class="search-profiles" id="profiles">
        <div class="search-profiles-head">
            <h2><i class="fa-solid fa-users"></i>Profiles</h2>
            <span class="search-profiles-count"><?php echo e($profiles->count()); ?> <?php echo e($profiles->count() === 1 ? 'profile' : 'profiles'); ?> found</span>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3">
            <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isFarmer = $member->role === 'Farmer';
                    $initial = mb_strtoupper(mb_substr(trim((string) $member->full_name), 0, 1)) ?: '?';
                    $place = collect([$member->citys->name ?? null, $member->states->name ?? null])->filter()->implode(', ');
                ?>
                <div class="col">
                    <div class="sp-card <?php echo e($isFarmer ? 'is-farmer' : 'is-nonfarmer'); ?>">
                        <div class="sp-avatar-wrap">
                            <div class="sp-avatar">
                                <?php if($member->profile_image): ?>
                                    <img src="<?php echo e(asset($member->profile_image)); ?>" alt="<?php echo e($member->full_name); ?>" loading="lazy">
                                <?php else: ?>
                                    <?php echo e($initial); ?>

                                <?php endif; ?>
                            </div>
                            <?php if(optional($member->profile)->status === 'Approved'): ?>
                                <span class="sp-tick" title="Verified"><i class="fa-solid fa-check"></i></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="sp-name" title="<?php echo e($member->full_name); ?>"><?php echo e($member->full_name); ?></h3>

                        <span class="sp-role">
                            <i class="fa-solid <?php echo e($isFarmer ? 'fa-seedling' : 'fa-store'); ?>"></i><?php echo e($isFarmer ? 'Farmer' : 'Non Farmer'); ?>

                        </span>

                        <p class="sp-location">
                            <i class="fa-solid fa-location-dot"></i><?php echo e($place ?: 'India'); ?>

                        </p>

                        <div class="sp-area">
                            <i class="fa-solid fa-map-pin"></i>
                            <span><?php echo e($member->area ?: '-'); ?></span>
                        </div>

                        <a href="<?php echo e(url('profile/' . $member->unique_id)); ?>" class="sp-btn">
                            <i class="fa-solid fa-user"></i>View profile
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/partials/profile-search-results.blade.php ENDPATH**/ ?>