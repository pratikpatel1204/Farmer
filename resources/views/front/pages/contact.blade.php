@extends('front.layout.app')
@section('title', 'Contact Us - ' . config('app.name'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
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
@endpush
@php
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
@endphp

@section('content')
    <div class="inner-page">

        @include('front.partials.page-hero', [
            'title' => 'We are here to help',
            'text' =>
                'Questions about buying, selling or your account? Send us a message or call us. We reply within one working day.',
            'badge' => ['icon' => 'fa-phone', 'label' => 'Contact us'],
            'variant' => 'sun',
            'art' => 'fa-headset',
            'crumbs' => ['Contact Us' => null],
        ])

        <section class="page-section">
            <div class="container-xl">
                <div class="row g-4">

                    {{-- Contact details --}}
                    <div class="col-12 col-lg-5">
                        @if ($hasDetails)
                            <div class="contact-info-list">
                                @if ($contact->phone)
                                    <a href="{{ $contact->phone_link }}" class="contact-info">
                                        <i class="fa-solid fa-phone tint-green"></i>
                                        <div><strong>Call us</strong><span>{{ $contact->phone }}</span></div>
                                    </a>
                                @endif
                                @if ($whatsappLink)
                                    <a href="{{ $whatsappLink }}" class="contact-info" target="_blank" rel="noopener">
                                        <i class="fa-brands fa-whatsapp tint-lime"></i>
                                        <div><strong>WhatsApp</strong><span>{{ $contact->whatsapp_number }}</span></div>
                                    </a>
                                @endif
                                @if ($contact->email)
                                    <a href="{{ $contact->email_link }}" class="contact-info">
                                        <i class="fa-solid fa-envelope tint-yellow"></i>
                                        <div><strong>Email</strong><span>{{ $contact->email }}</span></div>
                                    </a>
                                @endif
                                @if ($contact->office_address)
                                    <div class="contact-info">
                                        <i class="fa-solid fa-location-dot tint-red"></i>
                                        <div><strong>Office</strong><span>{!! nl2br(e($contact->office_address)) !!}</span></div>
                                    </div>
                                @endif
                                @if ($contact->working_hours)
                                    <div class="contact-info">
                                        <i class="fa-solid fa-clock tint-brown"></i>
                                        <div><strong>Working hours</strong><span>{!! nl2br(e($contact->working_hours)) !!}</span></div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fa-solid fa-headset"></i>
                                <h3>Write to us</h3>
                                <p class="mb-0">Send us a message using the form and our team will get back to you.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Form --}}
                    <div class="col-12 col-lg-7">
                        <div class="farm-card">
                            <h2 class="farm-card-title"><i class="fa-solid fa-paper-plane"></i>Send us a message</h2>

                            {{-- JS band ho to bhi purana redirect wala flow chalega --}}
                            @if (session('contact_success'))
                                <div class="farm-alert farm-alert-success mb-3" role="status"><i
                                        class="fa-solid fa-circle-check"></i>{{ session('contact_success') }}</div>
                            @endif
                            @if (session('contact_error'))
                                <div class="farm-alert farm-alert-error mb-3" role="alert"><i
                                        class="fa-solid fa-triangle-exclamation"></i>{{ session('contact_error') }}</div>
                            @endif

                            <form action="{{ url('contact-us') }}" method="POST" class="farm-form" id="contactForm"
                                novalidate>
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="contactName" class="form-label">Full name *</label>
                                        <input type="text" id="contactName" name="name" value="{{ old('name') }}"
                                            class="form-control @error('name') is-invalid @enderror" placeholder="Your name"
                                            autocomplete="name" maxlength="100" required>
                                        <div class="invalid-feedback" data-error-for="name">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="contactMobile" class="form-label">Mobile number *</label>
                                        <input type="tel" id="contactMobile" name="mobile" value="{{ old('mobile') }}"
                                            class="form-control @error('mobile') is-invalid @enderror"
                                            placeholder="10 digit mobile" autocomplete="tel" inputmode="tel" maxlength="15"
                                            required>
                                        <div class="invalid-feedback" data-error-for="mobile">
                                            @error('mobile')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="contactEmail" class="form-label">Email</label>
                                        <input type="email" id="contactEmail" name="email" value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="you@example.com" autocomplete="email" maxlength="150">
                                        <div class="invalid-feedback" data-error-for="email">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="contactSubject" class="form-label">Subject *</label>
                                        <select id="contactSubject" name="subject"
                                            class="form-select @error('subject') is-invalid @enderror" required>
                                            <option value="" disabled @selected(!old('subject'))>Select a subject
                                            </option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject }}" @selected(old('subject') === $subject)>
                                                    {{ $subject }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" data-error-for="subject">
                                            @error('subject')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="contactMessage" class="form-label">Message *</label>
                                        <textarea id="contactMessage" name="message" rows="5" maxlength="2000"
                                            class="form-control @error('message') is-invalid @enderror" placeholder="How can we help you?" required>{{ old('message') }}</textarea>
                                        <div class="d-flex justify-content-between gap-2">
                                            <div class="invalid-feedback d-block" data-error-for="message">
                                                @error('message')
                                                    {{ $message }}
                                                @enderror
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

        {{-- Map + FAQ --}}
        <section class="page-section bg-white-section">
            <div class="container-xl">
                <div class="row g-4">
                    <div class="col-12 col-lg-5">
                        <h2 class="farm-card-title"><i class="fa-solid fa-map-location-dot"></i>Find us</h2>
                        {{-- Admin panel me map_embed_url bhara ho to asli map, warna placeholder --}}
                        <div class="map-box">
                            @if ($mapUrl)
                                <iframe src="{{ $mapUrl }}" title="KisanMandi office location" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                            @else
                                <div class="map-pin"><i class="fa-solid fa-location-dot"></i>KisanMandi Office</div>
                            @endif
                        </div>
                        @if ($contact->office_address)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($contact->office_address) }}"
                                class="btn btn-farm-outline mt-3" target="_blank" rel="noopener"><i
                                    class="fa-solid fa-diamond-turn-right me-1"></i>Get directions</a>
                        @endif
                    </div>
                    <div class="col-12 col-lg-7">
                        <h2 class="farm-card-title"><i class="fa-solid fa-circle-question"></i>Common questions</h2>
                        <div class="accordion farm-accordion" id="contactFaq">
                            @php
                                $faqs = [
                                    [
                                        'Is KisanMandi free to use?',
                                        'Yes. Registering, listing your produce and posting your requirements is completely free.',
                                    ],
                                    [
                                        'What is the difference between People Want and People Have?',
                                        'People Have shows produce that farmers are selling. People Want shows what buyers are looking for. Farmers can check People Want to find new buyers.',
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
                            @endphp
                            @foreach ($faqs as $i => [$question, $answer])
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}"
                                            aria-expanded="{{ $i ? 'false' : 'true' }}"
                                            aria-controls="faq{{ $i }}">{{ $question }}</button>
                                    </h3>
                                    <div id="faq{{ $i }}"
                                        class="accordion-collapse collapse {{ $i ? '' : 'show' }}"
                                        data-bs-parent="#contactFaq">
                                        <div class="accordion-body">{{ $answer }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
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
@endpush
