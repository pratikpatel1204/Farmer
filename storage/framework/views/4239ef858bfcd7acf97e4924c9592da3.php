<?php $__env->startSection('title', ($survey->name ?? 'Survey') . ' - KisanMandi'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/survey.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('front/js/survey.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php
    $isGuest = ! auth('web')->check();
    $canEdit = $response && $response->canEdit();
    // Galat submit ke baad (validation error) form khula rahe
    $openForm = ! $response || ($canEdit && $errors->any());
    $loginUrl = route('login', ['redirect' => '/survey']);
?>

<?php $__env->startSection('content'); ?>
<div class="inner-page survey-page">

    <?php echo $__env->make('front.partials.page-hero', [
        'title' => $survey->name ?? 'Survey',
        'text' => $survey
            ? ($survey->description ?: 'Your answers help us make KisanMandi better for farmers and buyers. There are no right or wrong answers.')
            : 'There is no survey running right now. Please check again later.',
        'badge' => ['icon' => 'fa-clipboard-question', 'label' => 'Survey'],
        'variant' => 'sun',
        'art' => 'fa-square-poll-vertical',
        'crumbs' => ['Survey' => null],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="page-section">
        <div class="container-xl">
            <div class="survey-wrap">

                <?php if(session('survey_success') && ! $response): ?>
                    <div class="farm-alert farm-alert-success mb-3" role="status"><i class="fa-solid fa-circle-check"></i><?php echo e(session('survey_success')); ?></div>
                <?php endif; ?>
                <?php if(session('survey_error')): ?>
                    <div class="farm-alert farm-alert-error mb-3" role="alert"><i class="fa-solid fa-triangle-exclamation"></i><?php echo e(session('survey_error')); ?></div>
                <?php endif; ?>

                <?php if(! $survey || $questions->isEmpty()): ?>
                    
                    <div class="empty-state">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <h3>No survey right now</h3>
                        <p>There is no active survey at the moment. Please check again later.</p>
                        <a href="<?php echo e(url('/')); ?>" class="btn btn-farm"><i class="fa-solid fa-house me-1"></i>Back to home</a>
                    </div>
                <?php else: ?>

                    <?php if($isGuest): ?>
                        
                        <div class="survey-login mb-4">
                            <i class="fa-solid fa-user-lock survey-login-icon" aria-hidden="true"></i>
                            <div class="survey-login-body">
                                <h2>Please log in to take this survey</h2>
                                <p>You can read all the questions below. To answer and submit, please log in to your KisanMandi account.</p>
                            </div>
                            <div class="survey-login-actions">
                                <a href="<?php echo e($loginUrl); ?>" class="btn btn-farm"><i class="fa-solid fa-right-to-bracket me-1"></i>Log in</a>
                                <a href="<?php echo e(url('register')); ?>" class="btn btn-farm-outline">Register</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($response): ?>
                        
                        <div class="survey-thanks mb-4">
                            <span class="survey-thanks-icon"><i class="fa-solid fa-hands-clapping"></i></span>
                            <h2>Thank you for taking part!</h2>
                            <p><?php echo e(session('survey_success') === 'Thank you! Your answers have been updated.' ? 'Your answers have been updated.' : 'Your answers have been saved.'); ?> They help us make KisanMandi better for everyone.</p>

                            <?php if($canEdit): ?>
                                <div class="survey-edit-note">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>You can change your answers until <strong><?php echo e($response->locked_at->format('d M Y, h:i A')); ?></strong> (<?php echo e($response->locked_at->diffForHumans()); ?>). After that your answers will be locked.</span>
                                </div>
                                <div></div>
                                <button type="button" class="btn btn-farm-outline mt-3" data-survey-edit aria-controls="surveyForm"
                                    aria-expanded="<?php echo e($openForm ? 'true' : 'false'); ?>" <?php if($openForm): ?> hidden <?php endif; ?>>
                                    <i class="fa-solid fa-pen me-1"></i>Change my answers
                                </button>
                            <?php else: ?>
                                <div class="survey-edit-note is-locked">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>Your answers are locked. Each survey can be taken only once.</span>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <a href="<?php echo e(url('/')); ?>" class="survey-home-link"><i class="fa-solid fa-arrow-left me-1"></i>Back to home</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($isGuest || ! $response || $canEdit): ?>
                        <form method="POST" action="<?php echo e(route('survey.submit')); ?>" id="surveyForm" class="survey-form" novalidate
                            data-survey-form <?php if(! $openForm && ! $isGuest): ?> hidden <?php endif; ?>>
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="survey_id" value="<?php echo e($survey->id); ?>">

                            <div class="survey-count mb-3">
                                <span><i class="fa-solid fa-list-check me-1"></i><?php echo e($questions->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('question', $questions->count())); ?></span>
                                <?php if (! ($isGuest)): ?>
                                    <span>Pick one option for each question</span>
                                <?php endif; ?>
                            </div>

                            <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $selected = (int) old('answers.' . $question->id, $answers[$question->id] ?? 0);
                                    $errorKey = 'answers.' . $question->id;
                                ?>
                                <div class="survey-question <?php $__errorArgs = [$errorKey];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-missing <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="question-<?php echo e($question->id); ?>"
                                    data-survey-question role="radiogroup" aria-labelledby="question-title-<?php echo e($question->id); ?>">
                                    <div class="survey-q-head">
                                        <span class="survey-q-no">Q<?php echo e($loop->iteration); ?></span>
                                        <div class="survey-q-text rich-text" id="question-title-<?php echo e($question->id); ?>"><?php echo $question->question_html; ?></div>
                                    </div>

                                    <div class="survey-options">
                                        <?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $no => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="survey-option <?php echo e($selected === $no ? 'is-checked' : ''); ?> <?php echo e($isGuest ? 'is-disabled' : ''); ?>">
                                                <input type="radio" class="survey-option-input" name="answers[<?php echo e($question->id); ?>]"
                                                    value="<?php echo e($no); ?>" <?php if($selected === $no): echo 'checked'; endif; ?> <?php if($isGuest): echo 'disabled'; endif; ?>>
                                                <span class="survey-option-letter"><?php echo e(chr(64 + $loop->iteration)); ?></span>
                                                <span class="survey-option-text"><?php echo e($text); ?></span>
                                                <i class="fa-solid fa-circle-check survey-option-tick" aria-hidden="true"></i>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>

                                    <p class="survey-q-error" data-survey-error <?php if (! ($errors->has($errorKey))): ?> hidden <?php endif; ?>>
                                        <i class="fa-solid fa-circle-exclamation me-1"></i><?php echo e($errors->first($errorKey) ?: 'Please select an answer.'); ?>

                                    </p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php if($isGuest): ?>
                                <div class="survey-submit-bar">
                                    <span class="survey-progress-text">Log in to answer these questions.</span>
                                    <a href="<?php echo e($loginUrl); ?>" class="btn btn-farm"><i class="fa-solid fa-right-to-bracket me-1"></i>Log in to answer</a>
                                </div>
                            <?php else: ?>
                                <div class="survey-submit-bar">
                                    <div class="survey-progress">
                                        <span class="survey-progress-text" data-survey-progress-text>0 of <?php echo e($questions->count()); ?> answered</span>
                                        <span class="survey-progress-track"><span class="survey-progress-fill" data-survey-progress-fill></span></span>
                                    </div>
                                    <button type="submit" class="btn btn-farm" data-survey-submit>
                                        <?php if($response): ?>
                                            <i class="fa-solid fa-floppy-disk me-1"></i>Update answers
                                        <?php else: ?>
                                            <i class="fa-solid fa-paper-plane me-1"></i>Submit survey
                                        <?php endif; ?>
                                    </button>
                                </div>
                                <?php if (! ($response)): ?>
                                    <p class="survey-footnote"><i class="fa-solid fa-circle-info me-1"></i>After you submit, you can change your answers for 24 hours. After that they are locked.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/survey/show.blade.php ENDPATH**/ ?>