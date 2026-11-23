<?php
    $heroSurvey = $heroSurvey ?? \App\Models\Survey::current();
    $heroQuestions = $heroSurvey ? $heroSurvey->visibleQuestions()->limit(3)->get() : collect();
    $heroQuestionTotal = $heroSurvey && $heroQuestions->count() ? $heroSurvey->visibleQuestions()->count() : 0;
    $heroSurveyDone =
        $heroQuestionTotal &&
        auth('web')->check() &&
        \App\Models\SurveyResponse::where('survey_id', $heroSurvey->id)->where('user_id', auth('web')->id())->exists();
?>

<?php if($heroQuestionTotal): ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('front/css/survey.css')); ?>">
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('front/js/survey.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<section class="hero-section">
    <div class="container-fluid">
        <div class="row g-3 hero-row">

            
            <div class="col-12 col-md-6">
                <div id="heroSlider" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="4500">

                    <?php if($banners->count()): ?>
                        <?php if($banners->count() > 1): ?>
                            <div class="carousel-indicators">
                                <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button type="button" data-bs-target="#heroSlider"
                                        data-bs-slide-to="<?php echo e($loop->index); ?>"
                                        class="<?php echo e($loop->first ? 'active' : ''); ?>"
                                        <?php if($loop->first): ?> aria-current="true" <?php endif; ?>
                                        aria-label="Slide <?php echo e($loop->iteration); ?>"></button>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                        <div class="carousel-inner">
                            <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="carousel-item <?php echo e($loop->first ? 'active' : ''); ?>">
                                    <div class="hero-slide has-image <?php echo e($slideColors[$loop->index % count($slideColors)]); ?>"
                                        <?php if($banner->image): ?> style="background-image: url('<?php echo e(asset($banner->image)); ?>');" <?php endif; ?>>
                                        <div class="hero-slide-content">
                                            <span class="hero-badge"><i class="fa-solid fa-leaf"></i>Direct from
                                                farmers</span>
                                            <h2 class="hero-title"><?php echo e($banner->title); ?></h2>
                                            <?php if($banner->description): ?>
                                                <p class="hero-text">
                                                    <?php echo e(\Illuminate\Support\Str::limit(strip_tags($banner->description), 140)); ?>

                                                </p>
                                            <?php endif; ?>
                                            <a href="<?php echo e(url('i-have')); ?>" class="btn btn-hero">
                                                Browse crops <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="hero-slide slide-green">
                                    <i class="fa-solid fa-wheat-awn hero-art" aria-hidden="true"></i>
                                    <div class="hero-slide-content">
                                        <span class="hero-badge">
                                            <i class="fa-solid fa-leaf"></i>Direct from farmers
                                        </span>
                                        <h2 class="hero-title">Fresh crops, fair prices</h2>
                                        <p class="hero-text">Buy and sell farm produce without any middlemen.</p>
                                        <a href="<?php echo e(url('i-have')); ?>" class="btn btn-hero">
                                            Browse crops <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($banners->count() > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroSlider"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <?php if($heroQuestionTotal): ?>
                    
                    <div class="hero-survey">
                        <i class="fa-solid fa-square-poll-vertical hero-survey-art" aria-hidden="true"></i>

                        <div class="hero-survey-head">
                            <span class="hero-survey-label"><i class="fa-solid fa-clipboard-question"></i>Quick
                                survey</span>
                            <h3 class="hero-survey-title"><?php echo e($heroSurvey->name); ?></h3>
                        </div>

                        <ol class="hero-survey-list" data-survey-fit>
                            <?php $__currentLoopData = $heroQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="hero-survey-item" data-survey-item>
                                    <a href="<?php echo e(route('survey.show')); ?>#question-<?php echo e($q->id); ?>" target="_blank"
                                        rel="noopener" class="hero-survey-link">
                                        <span class="hero-survey-no"><?php echo e($loop->iteration); ?></span>
                                        <span class="hero-survey-body">
                                            <span
                                                class="hero-survey-q"><?php echo e(\Illuminate\Support\Str::limit($q->question_text, 120)); ?></span>
                                            <span class="hero-survey-options">
                                                <?php $__currentLoopData = array_slice($q->options, 0, 4, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span
                                                        class="hero-survey-option"><?php echo e(\Illuminate\Support\Str::limit($text, 28)); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(count($q->options) > 4): ?>
                                                    <span
                                                        class="hero-survey-option is-more">+<?php echo e(count($q->options) - 4); ?>

                                                        more</span>
                                                <?php endif; ?>
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ol>

                        <div class="hero-survey-foot">
                            <span class="hero-survey-meta">
                                <?php if($heroSurveyDone): ?>
                                    <i class="fa-solid fa-circle-check"></i>You have taken this survey
                                <?php else: ?>
                                    <i class="fa-regular fa-clock"></i><?php echo e($heroQuestionTotal); ?>

                                    <?php echo e(\Illuminate\Support\Str::plural('question', $heroQuestionTotal)); ?> · takes 1-2
                                    minutes
                                <?php endif; ?>
                            </span>
                            <a href="<?php echo e(route('survey.show')); ?>" target="_blank" rel="noopener" class="btn btn-survey">
                                View all <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    
                    <div class="hero-banners">
                        <a href="<?php echo e(url('i-want')); ?>" class="hero-banner banner-want">
                            <span class="hero-banner-label"><i class="fa-solid fa-cart-shopping"></i>I Want</span>
                            <h3 class="hero-banner-title">Want to buy? Tell us your need</h3>
                            <p class="hero-banner-text">Post your requirement and the right farmers will reach you.</p>
                            <span class="hero-banner-link">View all <i class="fa-solid fa-arrow-right ms-1"></i></span>
                            <i class="fa-solid fa-basket-shopping hero-banner-art" aria-hidden="true"></i>
                        </a>
                        <a href="<?php echo e(url('i-have')); ?>" class="hero-banner banner-have">
                            <span class="hero-banner-label"><i class="fa-solid fa-seedling"></i>I Have</span>
                            <h3 class="hero-banner-title">Want to sell? List your crop</h3>
                            <p class="hero-banner-text">Add photos, set your price and find buyers.</p>
                            <span class="hero-banner-link">View all <i
                                    class="fa-solid fa-arrow-right ms-1"></i></span>
                            <i class="fa-solid fa-wheat-awn hero-banner-art" aria-hidden="true"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="trust-strip">
            <div class="trust-item">
                <i class="fa-solid fa-earth-asia"></i>
                <div>
                    <strong>Pan India Business</strong>
                    <span>High Growth Potential</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-handshake"></i>
                <div>
                    <strong>Direct Deals</strong>
                    <span>No Middleman</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-boxes-stacked"></i>
                <div>
                    <strong>Wide Range of Products</strong>
                    <span>One Stop Solution</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <div>
                    <strong>User Friendly Technology</strong>
                    <span>Unlimited Upload at Zero Cost</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-user-check"></i>
                <div>
                    <strong>Verified Profiles</strong>
                    <span>KYC Checked Members</span>
                </div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-headset"></i>
                <div>
                    <strong>Help</strong>
                    <span>Multilingual Support</span>
                </div>
            </div>
        </div>
    </div>
</section><?php /**PATH D:\xampp\htdocs\Farmer\resources\views/front/partials/hero-section.blade.php ENDPATH**/ ?>