<?php $__env->startSection('title', 'Contact Us - ' . config('app.name')); ?>
<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front/css/home.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front/css/pages.css')); ?>">
    <style>
        .btn-farm .btn-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 6px;
            vertical-align: -3px;
            border: 2px solid rgba(255, 255, 255, .45);
            border-top-color: #fff;
            border-radius: 50%;
            animation: contact-spin .7s linear infinite;
        }

        @keyframes contact-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-farm[disabled] {
            opacity: .8;
            cursor: not-allowed;
        }

        .contact-char-count {
            font-size: 12px;
            color: #6B7280;
            text-align: right;
            margin-top: 4px;
        }

        .contact-char-count.over {
            color: #dc3545;
        }

        .farm-form .invalid-feedback:empty {
            display: none !important;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php
    $whatsappLink = $contact->whatsapp_link
        ? $contact->whatsapp_link .
            ($contact->whatsapp_message ? '?text=' . rawurlencode($contact->whatsapp_message) : '')
        : null;

    $mapUrl = trim((string) $contact->map_embed_url);
    $mapUrl = \Illuminate\Support\Str::startsWith($mapUrl, [
        'https://www.google.com/maps/embed',
        'https://maps.google.com/maps',
    ])
        ? $mapUrl
        : null;

    $hasDetails =
        $contact->phone ||
        $contact->whatsapp_number ||
        $contact->email ||
        $contact->office_address ||
        $contact->working_hours;

    $subjects = [
        'General question',
        'Selling my produce',
        'Buying / requirement',
        'Account or login help',
        'Report a problem',
    ];
?>

<?php $__env->startSection('content'); ?>
    <div class="inner-page">

        <?php echo $__env->make('front.partials.page-hero', [
            'title' => 'We are here to help',
            'text' =>
                'Questions about buying, selling or your account? Send us a message or call us. We reply within one working day.',
            'badge' => ['icon' => 'fa-phone', 'label' => 'Contact us'],
            'variant' => 'sun',
            'art' => 'fa-headset',
            'crumbs' => ['Contact Us' => null],
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <section class="page-section">
            <div class="container-xl">
                <div class="row g-4">

                    
                    <div class="col-12 col-lg-5">
                        <?php if($hasDetails): ?>
                            <div class="contact-info-list">
                                <?php if($contact->phone): ?>
                                    <a href="<?php echo e($contact->phone_link); ?>" class="contact-info">
                                        <i class="fa-solid fa-phone tint-green"></i>
                                        <div><strong>Call us</strong><span><?php echo e($contact->phone); ?></span></div>
                                    </a>
                                <?php endif; ?>
                                <?php if($whatsappLink): ?>
                                    <a href="<?php echo e($whatsappLink); ?>" class="contact-info" target="_blank" rel="noopener">
                                        <i class="fa-brands fa-whatsapp tint-lime"></i>
                                        <div><strong>WhatsApp</strong><span><?php echo e($contact->whatsapp_number); ?></span></div>
                                    </a>
                                <?php endif; ?>
                                <?php if($contact->email): ?>
                                    <a href="<?php echo e($contact->email_link); ?>" class="contact-info">
                                        <i class="fa-solid fa-envelope tint-yellow"></i>
                                        <div><strong>Email</strong><span><?php echo e($contact->email); ?></span></div>
                                    </a>
                                <?php endif; ?>
                                <?php if($contact->office_address): ?>
                                    <div class="contact-info">
                                        <i class="fa-solid fa-location-dot tint-red"></i>
                                        <div><strong>Office</strong><span><?php echo nl2br(e($contact->office_address)); ?></span></div>
                                    </div>
                                <?php endif; ?>
                                <?php if($contact->working_hours): ?>
                                    <div class="contact-info">
                                        <i class="fa-solid fa-clock tint-brown"></i>
                                        <div><strong>Working hours</strong><span><?php echo nl2br(e($contact->working_hours)); ?></span></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa-solid fa-headset"></i>
                                <h3>Write to us</h3>
                                <p class="mb-0">Send us a message using the form and our team will get back to you.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="col-12 col-lg-7">
                        <div class="farm-card">
                            <h2 class="farm-card-title"><i class="fa-solid fa-paper-plane"></i>Send us a message</h2>

                            
                            <?php if(session('contact_success')): ?>
                                <div class="farm-alert farm-alert-success mb-3" role="status"><i
                                        class="fa-solid fa-circle-check"></i><?php echo e(session('contact_success')); ?></div>
                            <?php endif; ?>
                            <?php if(session('contact_error')): ?>
                                <div class="farm-alert farm-alert-error mb-3" role="alert"><i
                                        class="fa-solid fa-triangle-exclamation"></i><?php echo e(session('contact_error')); ?></div>
                            <?php endif; ?>

                            <form action="<?php echo e(url('contact-us')); ?>" method="POST" class="farm-form" id="contactForm"
                                novalidate>
                                <?php echo csrf_field(); ?>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="contactName" class="form-label">Full name *</label>
                                        <input type="text" id="contactName" name="name" value="<?php echo e(old('name')); ?>"
                                            class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Your name"
                                            autocomplete="name" maxlength="100" required>
                                        <div class="invalid-feedback" data-error-for="name">
                                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <?php echo e($message); ?>

                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="contactMobile" class="form-label">Mobile number *</label>
                                        <input type="tel" id="contactMobile" name="mobile" value="<?php echo e(old('mobile')); ?>"
                                            class="form-control <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            placeholder="10 digit mobile" autocomplete="tel" inputmode="tel" maxlength="15"
                                            required>
                                        <div class="invalid-feedback" data-error-for="mobile">
                                            <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <?php echo e($message); ?>

                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="contactEmail" class="form-label">Email</label>
                                        <input type="email" id="contactEmail" name="email" value="<?php echo e(old('email')); ?>"
                                            class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            placeholder="you@example.com" autocomplete="email" maxlength="150">
                                        <div class="invalid-feedback" data-error-for="email">
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <?php echo e($message); ?>

                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="contactSubject" class="form-label">Subject *</label>
                                        <select id="contactSubject" name="subject"
                                            class="form-select <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="" disabled <?php if(!old('subject')): echo 'selected'; endif; ?>>Select a subject
                                            </option>
                                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($subject); ?>" <?php if(old('subject') === $subject): echo 'selected'; endif; ?>>
                                                    <?php echo e($subject); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback" data-error-for="subject">
                                            <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <?php echo e($message); ?>

                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="contactMessage" class="form-label">Message *</label>
                                        <textarea id="contactMessage" name="message" rows="5" maxlength="2000"
                                            class="form-control <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="How can we help you?" required><?php echo e(old('message')); ?></textarea>
                                        <div class="d-flex justify-content-between gap-2">
                                            <div class="invalid-feedback d-block" data-error-for="message">
                                                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <?php echo e($message); ?>

                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                            <div class="contact-char-count ms-auto" id="contactCharCount">0 / 2000</div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex flex-wrap align-items-center gap-3">
                                        <button type="submit" class="btn btn-farm" id="contactSubmitBtn">
                                            <span class="btn-label"><i class="fa-solid fa-paper-plane me-1"></i>Send
                                                message</span>
                                        </button>
                                        <span class="form-text m-0">We never share your number with anyone.</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="page-section bg-white-section">
            <div class="container-xl">
                <div class="row g-4">
                    <div class="col-12 col-lg-5">
                        <h2 class="farm-card-title"><i class="fa-solid fa-map-location-dot"></i>Find us</h2>
                        
                        <div class="map-box">
                            <?php if($mapUrl): ?>
                                <iframe src="<?php echo e($mapUrl); ?>" title="KisanMandi office location" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                            <?php else: ?>
                                <div class="map-pin"><i class="fa-solid fa-location-dot"></i>KisanMandi Office</div>
                            <?php endif; ?>
                        </div>
                        <?php if($contact->office_address): ?>
                            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo e(rawurlencode($contact->office_address)); ?>"
                                class="btn btn-farm-outline mt-3" target="_blank" rel="noopener"><i
                                    class="fa-solid fa-diamond-turn-right me-1"></i>Get directions</a>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-lg-7">
                        <h2 class="farm-card-title"><i class="fa-solid fa-circle-question"></i>Common questions</h2>
                        <div class="accordion farm-accordion" id="contactFaq">
                            <?php
                                $faqs = [
                                    [
                                        'Is KisanMandi free to use?',
                                        'Yes. Registering, listing your produce and posting your requirements is completely free.',
                                    ],
                                    [
                                        'What is the difference between I Want and I Have?',
                                        'I Have shows produce that farmers are selling. I Want shows what buyers are looking for. Farmers can check I Want to find new buyers.',
                                    ],
                                    [
                                        'How do I get the blue verified tick?',
                                        'Complete your profile with correct details. Our team checks it and approves it, usually within 2 working days.',
                                    ],
                                    [
                                        'Does KisanMandi take any commission?',
                                        'No. You deal directly with the buyer or the farmer and agree on the price yourselves.',
                                    ],
                                ];
                            ?>
                            <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => [$question, $answer]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button <?php echo e($i ? 'collapsed' : ''); ?>" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq<?php echo e($i); ?>"
                                            aria-expanded="<?php echo e($i ? 'false' : 'true'); ?>"
                                            aria-controls="faq<?php echo e($i); ?>"><?php echo e($question); ?></button>
                                    </h3>
                                    <div id="faq<?php echo e($i); ?>"
                                        class="accordion-collapse collapse <?php echo e($i ? '' : 'show'); ?>"
                                        data-bs-parent="#contactFaq">
                                        <div class="accordion-body"><?php echo e($answer); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        (function() {
            const form = document.getElementById('contactForm');
            if (!form) return;

            const btn = document.getElementById('contactSubmitBtn');
            const btnHtml = btn.innerHTML;
            const msgBox = document.getElementById('contactMessage');
            const counter = document.getElementById('contactCharCount');
            const MAX_MSG = 2000;

            if (window.toastr) {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000
                };
            }

            function notify(type, message) {
                if (window.toastr) {
                    toastr[type](message);
                } else {
                    alert(message);
                }
            }

            /* ---------- Validation rules (server ke rules jaise hi) ---------- */
            const rules = {
                name(v) {
                    if (!v) return 'Please enter your full name.';
                    if (v.length < 2) return 'Name must be at least 2 characters.';
                    if (v.length > 100) return 'Name must not be more than 100 characters.';
                    return '';
                },
                mobile(v) {
                    if (!v) return 'Please enter your mobile number.';
                    if (!/^[0-9+\s-]{10,15}$/.test(v)) return 'Please enter a valid mobile number.';
                    return '';
                },
                email(v) {
                    if (!v) return '';
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) return 'Please enter a valid email address.';
                    if (v.length > 150) return 'Email must not be more than 150 characters.';
                    return '';
                },
                subject(v) {
                    return v ? '' : 'Please select a subject.';
                },
                message(v) {
                    if (!v) return 'Please write your message.';
                    if (v.length < 10) return 'Message must be at least 10 characters.';
                    if (v.length > MAX_MSG) return 'Message must not be more than ' + MAX_MSG + ' characters.';
                    return '';
                }
            };

            function field(name) {
                return form.elements[name];
            }

            function setError(name, message) {
                const input = field(name);
                const box = form.querySelector('[data-error-for="' + name + '"]');
                if (input) input.classList.toggle('is-invalid', !!message);
                if (box) box.textContent = message || '';
            }

            function validateField(name) {
                const input = field(name);
                if (!input) return true;
                const error = rules[name](input.value.trim());
                setError(name, error);
                if (!error && input.value.trim()) input.classList.add('is-valid');
                else input.classList.remove('is-valid');
                return !error;
            }

            function validateAll() {
                let firstInvalid = null;
                Object.keys(rules).forEach(function(name) {
                    if (!validateField(name) && !firstInvalid) firstInvalid = field(name);
                });
                if (firstInvalid) firstInvalid.focus();
                return !firstInvalid;
            }

            // Field chhodne par check, aur error dikhne ke baad type karte hi re-check
            Object.keys(rules).forEach(function(name) {
                const input = field(name);
                if (!input) return;
                input.addEventListener('blur', () => validateField(name));
                input.addEventListener(input.tagName === 'SELECT' ? 'change' : 'input', function() {
                    if (input.classList.contains('is-invalid') || input.classList.contains(
                        'is-valid')) {
                        validateField(name);
                    }
                });
            });

            // Mobile me sirf allowed characters
            field('mobile').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9+\s-]/g, '');
            });

            /* ---------- Message counter ---------- */
            function updateCounter() {
                const len = msgBox.value.length;
                counter.textContent = len + ' / ' + MAX_MSG;
                counter.classList.toggle('over', len > MAX_MSG);
            }
            msgBox.addEventListener('input', updateCounter);
            updateCounter();

            /* ---------- Button loader ---------- */
            function setLoading(loading) {
                btn.disabled = loading;
                btn.innerHTML = loading ?
                    '<span class="btn-spinner" aria-hidden="true"></span>Sending...' :
                    btnHtml;
            }

            /* ---------- AJAX submit ---------- */
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (btn.disabled) return;

                if (!validateAll()) {
                    notify('error', 'Please correct the highlighted fields.');
                    return;
                }

                setLoading(true);

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(form)
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.status === 422 && data.errors) {
                        let firstInvalid = null;
                        Object.keys(data.errors).forEach(function(name) {
                            setError(name, data.errors[name][0]);
                            if (!firstInvalid) firstInvalid = field(name);
                        });
                        if (firstInvalid) firstInvalid.focus();
                        notify('error', data.message || 'Please correct the highlighted fields.');
                        return;
                    }

                    if (res.status === 419) {
                        notify('error', 'Your session has expired. Please refresh the page and try again.');
                        return;
                    }

                    if (res.status === 429) {
                        notify('warning', 'Too many attempts. Please wait a minute and try again.');
                        return;
                    }

                    if (!res.ok || !data.success) {
                        notify('error', data.message ||
                            'Sorry, your message could not be sent. Please call or email us.');
                        return;
                    }

                    notify('success', data.message);
                    form.reset();
                    form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove(
                        'is-valid', 'is-invalid'));
                    form.querySelectorAll('[data-error-for]').forEach(el => el.textContent = '');
                    updateCounter();
                } catch (err) {
                    notify('error', 'Network error. Please check your internet connection and try again.');
                } finally {
                    setLoading(false);
                }
            });
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/pages/contact.blade.php ENDPATH**/ ?>