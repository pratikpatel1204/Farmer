
<?php if($authUser && !$isOwner): ?>
    <?php
        $defaultMessage = $isWant
            ? "Hi, I can supply \"{$product->title}\". Please let me know the quantity you need and your expected price."
            : "Hi, I am interested in \"{$product->title}\". Please share the price, available quantity and delivery details.";
    ?>
    <div class="modal fade farm-modal" id="enquiryModal" tabindex="-1" aria-labelledby="enquiryTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="enquiryTitle"><i class="fa-solid <?php echo e($isWant ? 'fa-hand-holding-heart' : 'fa-phone'); ?> me-2"></i><?php echo e($enquiryLabel); ?></h5>
                        <p class="enquiry-for">Enquiry for <strong><?php echo e($product->title); ?></strong> &middot; to <?php echo e($ownerName); ?></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    
                    <div class="enquiry-done" data-enquiry-done hidden>
                        <div class="success-icon"><i class="fa-solid fa-check"></i></div>
                        <h5 class="success-title">Enquiry sent!</h5>
                        <p class="success-sub" data-enquiry-done-text>The <?php echo e($partyName); ?> will contact you soon.</p>
                        <button type="button" class="btn btn-farm w-100" data-bs-dismiss="modal">Done</button>
                    </div>

                    <form class="farm-form" data-enquiry-form action="<?php echo e(route('products.enquiry', $product->slug)); ?>" method="POST" novalidate>
                        <?php echo csrf_field(); ?>
                        <div class="farm-alert farm-alert-error mb-3" data-enquiry-alert role="alert" hidden>
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="enqName" class="form-label">Your name <span class="req">*</span></label>
                                <div class="input-icon">
                                    <i class="fa-solid fa-user"></i>
                                    <input type="text" id="enqName" name="full_name" class="form-control" value="<?php echo e($authUser->full_name ?? $authUser->name ?? ''); ?>" autocomplete="name">
                                </div>
                                <div class="field-error" data-error-for="full_name"></div>
                            </div>
                            <div class="col-sm-6">
                                <label for="enqPhone" class="form-label">Mobile number <span class="req">*</span></label>
                                <div class="input-icon">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="text" id="enqPhone" name="phone" class="form-control" value="<?php echo e($authUser->mobile ?? ''); ?>" maxlength="10" inputmode="numeric" autocomplete="tel">
                                </div>
                                <div class="field-error" data-error-for="phone"></div>
                            </div>
                            <div class="col-sm-6">
                                <label for="enqEmail" class="form-label">Email <small>(optional)</small></label>
                                <div class="input-icon">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" id="enqEmail" name="email" class="form-control" value="<?php echo e($authUser->email ?? ''); ?>" autocomplete="email">
                                </div>
                                <div class="field-error" data-error-for="email"></div>
                            </div>
                            <div class="col-12">
                                <label for="enqMessage" class="form-label">Message <span class="req">*</span></label>
                                <textarea id="enqMessage" name="message" class="form-control" rows="4" maxlength="1000"><?php echo e($defaultMessage); ?></textarea>
                                <div class="field-error" data-error-for="message"></div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-farm <?php echo e($isWant ? 'btn-farm-red' : ''); ?> w-100" data-enquiry-submit>
                                    <span data-enquiry-text><i class="fa-solid fa-paper-plane me-1"></i>Send enquiry</span>
                                    <span class="spinner-border spinner-border-sm" data-enquiry-loader role="status" hidden>
                                        <span class="visually-hidden">Sending...</span>
                                    </span>
                                </button>
                                <p class="enquiry-privacy"><i class="fa-solid fa-lock"></i>Your details are shared only with this <?php echo e($partyName); ?>.</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php elseif(!$authUser): ?>
    <div class="modal fade farm-modal" id="loginFirstModal" tabindex="-1" aria-labelledby="loginFirstTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm-wide">
            <div class="modal-content">
                <div class="modal-body text-center pt-4">
                    <button type="button" class="btn-close modal-corner-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="login-first-icon"><i class="fa-solid fa-user-lock"></i></div>
                    <h5 class="success-title" id="loginFirstTitle">Please log in first</h5>
                    <p class="success-sub">You need to log in to send an enquiry to the <?php echo e($partyName); ?>. After logging in, you will come back to this listing.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('login', ['redirect' => '/product/' . $product->slug])); ?>" class="btn btn-farm"><i class="fa-solid fa-right-to-bracket me-1"></i>Log in</a>
                        <a href="<?php echo e(route('register')); ?>" class="btn btn-farm-outline"><i class="fa-solid fa-user-plus me-1"></i>Create a free account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/products/_enquiry-modal.blade.php ENDPATH**/ ?>