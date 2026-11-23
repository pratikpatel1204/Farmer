@extends('front.layout.app')
@section('title', 'Forgot password - ' . config('app.name'))
@section('content')
@include('front.auth._assets')
<div class="inner-page">
    <section class="auth-section">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="auth-card">
                        @include('front.auth._side', [
                            'heading' => 'Forgot your password?',
                            'text' => 'No problem. We will send a 6-digit OTP to your registered email so you can set a new one.',
                        ])

                        <div class="auth-form-wrap">
                            <span class="auth-badge"><i class="fa-solid fa-key"></i></span>
                            <h2 class="auth-title">Forgot password?</h2>
                            <p class="auth-subtitle">Enter your registered email and we will send you an OTP.</p>

                            @if (session('error'))
                                <div class="farm-alert farm-alert-error mb-3" role="alert"><i class="fa-solid fa-circle-exclamation"></i>{{ session('error') }}</div>
                            @endif

                            <form id="emailForm" class="farm-form" novalidate>
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-icon">
                                        <i class="fa-solid fa-envelope"></i>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="you@example.com" autocomplete="email">
                                    </div>
                                    <div class="field-error" id="emailError"></div>
                                </div>

                                <button type="submit" class="btn btn-farm w-100" id="sendBtn">
                                    <span id="sendText"><i class="fa-solid fa-paper-plane me-1"></i>Send OTP</span>
                                    <span class="spinner-border spinner-border-sm" id="sendLoader" style="display:none;" role="status">
                                        <span class="visually-hidden">Sending...</span>
                                    </span>
                                </button>
                            </form>

                            <p class="auth-switch">
                                <a href="{{ route('login') }}" class="auth-link auth-back"><i class="fa-solid fa-arrow-left"></i>Back to login</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- OTP modal --}}
<div class="modal fade farm-modal" id="otpModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="otpModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpModalTitle"><i class="fa-solid fa-shield-halved me-2"></i>Verify OTP</h5>
            </div>
            <div class="modal-body text-center">
                <p class="otp-note mb-3">Enter the 6-digit OTP sent to <b id="otpEmail"></b></p>

                <div class="otp-boxes mb-2" id="otpBoxes">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-box"
                            autocomplete="one-time-code" aria-label="OTP digit {{ $i + 1 }}">
                    @endfor
                </div>
                <div class="field-error" id="otpError"></div>

                <div class="otp-note mt-3">
                    Didn't receive the OTP?
                    <button type="button" class="link-btn" id="resendBtn" disabled>Resend OTP</button>
                    <span id="resendTimer"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-farm-outline" data-bs-dismiss="modal" id="cancelBtn">Cancel</button>
                <button type="button" class="btn btn-farm" id="verifyBtn">
                    <span id="verifyText"><i class="fa-solid fa-check me-1"></i>Verify OTP</span>
                    <span class="spinner-border spinner-border-sm" id="verifyLoader" style="display:none;" role="status">
                        <span class="visually-hidden">Checking...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var token = null;
            var timer = null;
            var modalEl = document.getElementById('otpModal');
            var modal = new bootstrap.Modal(modalEl);
            var $boxes = $('.otp-box');

            function setBtn(textSel, loaderSel, btnSel, loading) {
                $(btnSel).prop('disabled', loading);
                loading ? $(textSel).hide() : $(textSel).show();
                loading ? $(loaderSel).show() : $(loaderSel).hide();
            }

            function startTimer(seconds) {
                clearInterval(timer);
                var left = seconds;
                $('#resendBtn').prop('disabled', true);
                $('#resendTimer').text('(' + left + 's)');
                timer = setInterval(function() {
                    left--;
                    if (left <= 0) {
                        clearInterval(timer);
                        $('#resendTimer').text('');
                        $('#resendBtn').prop('disabled', false);
                    } else {
                        $('#resendTimer').text('(' + left + 's)');
                    }
                }, 1000);
            }

            function clearOtp() {
                $boxes.val('').removeClass('is-invalid');
                $('#otpError').text('');
                $boxes.first().focus();
            }

            $('#email').on('input', function() {
                $(this).removeClass('is-invalid');
                $('#emailError').text('');
            });

            // ---- Step 1: email -> send OTP ----
            $('#emailForm').on('submit', function(e) {
                e.preventDefault();
                var email = $('#email').val().trim();
                $('#emailError').text('');
                $('#email').removeClass('is-invalid');

                if (!email) {
                    $('#email').addClass('is-invalid');
                    $('#emailError').text('Please enter your email.');
                    return;
                }

                setBtn('#sendText', '#sendLoader', '#sendBtn', true);
                $.post("{{ route('check.forgot.password') }}", {
                        email: email
                    })
                    .done(function(res) {
                        token = res.token;
                        $('#otpEmail').text(res.email);
                        toastr.success(res.message);
                        modal.show();
                        clearOtp();
                        startTimer(30);
                    })
                    .fail(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Something went wrong. Please try again.';
                        $('#email').addClass('is-invalid');
                        $('#emailError').text(msg);
                    })
                    .always(function() {
                        setBtn('#sendText', '#sendLoader', '#sendBtn', false);
                    });
            });

            modalEl.addEventListener('shown.bs.modal', function() {
                $boxes.first().focus();
            });

            // ---- OTP boxes behaviour ----
            $boxes.on('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 1);
                $boxes.removeClass('is-invalid');
                $('#otpError').text('');
                if (this.value) $(this).next('.otp-box').focus();
            });
            $boxes.on('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value) $(this).prev('.otp-box').focus();
                if (e.key === 'Enter') $('#verifyBtn').click();
            });
            $boxes.on('paste', function(e) {
                var text = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(
                    /\D/g, '').slice(0, 6);
                if (!text) return;
                e.preventDefault();
                $boxes.each(function(i) {
                    this.value = text[i] || '';
                });
                $boxes.eq(Math.min(text.length, 5)).focus();
            });

            function getOtp() {
                return $boxes.map(function() {
                    return this.value;
                }).get().join('');
            }

            // ---- Step 2: verify OTP ----
            $('#verifyBtn').on('click', function() {
                var otp = getOtp();
                if (otp.length !== 6) {
                    $boxes.addClass('is-invalid');
                    $('#otpError').text('Please enter the 6-digit OTP.');
                    return;
                }

                setBtn('#verifyText', '#verifyLoader', '#verifyBtn', true);
                $.post("{{ route('check.forgot.otp') }}", {
                        otp: otp,
                        token: token
                    })
                    .done(function(res) {
                        toastr.success(res.message);
                        window.location.href = res.redirectUrl;
                    })
                    .fail(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Invalid OTP. Please try again.';
                        $boxes.addClass('is-invalid');
                        $('#otpError').text(msg);
                        setBtn('#verifyText', '#verifyLoader', '#verifyBtn', false);
                    });
            });

            // ---- Resend OTP ----
            $('#resendBtn').on('click', function() {
                $(this).prop('disabled', true);
                $.post("{{ route('resend.forgot.otp') }}", {
                        token: token
                    })
                    .done(function(res) {
                        toastr.success(res.message);
                        clearOtp();
                        startTimer(30);
                    })
                    .fail(function(xhr) {
                        toastr.error((xhr.responseJSON && xhr.responseJSON.message) ||
                            'Could not resend OTP.');
                        startTimer(30);
                    });
            });

            // ---- Cancel: reset ----
            $('#cancelBtn').on('click', function() {
                clearInterval(timer);
                token = null;
                clearOtp();
            });
        });
    </script>
@endpush
