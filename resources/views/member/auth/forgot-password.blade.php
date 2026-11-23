<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} || Forgot Password</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('seller/img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ asset('seller/css/bootstrap.min.css') }}" id="bootstrapLtr">
    <link rel="stylesheet" href="{{ asset('seller/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('seller/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('seller/css/toastr.min.css') }}">
    <style>
        .otp-boxes {
            display: flex;
            justify-content: center;
            gap: .5rem;
        }

        .otp-box {
            width: 46px;
            height: 54px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 700;
            border: 1.5px solid #d3def0;
            border-radius: .75rem;
            outline: 0;
        }

        .otp-box:focus {
            border-color: #0a6cf0;
            box-shadow: 0 0 0 .2rem rgba(10, 108, 240, .18);
        }

        .otp-box.is-invalid {
            border-color: #dc3545;
        }

        .fp-note {
            font-size: .85rem;
            color: #6b7a94;
        }

        .fp-rules {
            font-size: .78rem;
            color: #6b7a94;
            margin: .35rem 0 0;
            padding-left: 1rem;
        }

        @media (max-width: 400px) {
            .otp-box {
                width: 40px;
                height: 48px;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="auth-page">
        <div class="auth-left-orb auth-left-orb-1"></div>
        <div class="auth-left-orb auth-left-orb-2"></div>
        <div class="auth-left-orb auth-left-orb-3"></div>

        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <div class="auth-form-logo">
                        <img src="{{ asset('seller/img/logo.png') }}" alt="{{ config('app.name') }}">
                    </div>
                    <h2 class="auth-form-title">Forgot password?</h2>
                    <p class="auth-form-subtitle">Enter your registered email and we will send you an OTP</p>
                </div>
                @if (session('error'))
                    <div class="text-danger mb-3">{{ session('error') }}</div>
                @endif

                <form id="emailForm" novalidate>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group-zentic">
                            <span class="input-group-addon"><i class="icon-mail"></i></span>
                            <input type="email" class="form-control-zentic" id="email" name="email"
                                placeholder="you@example.com" autocomplete="email">
                        </div>
                        <div class="field-error text-danger" id="emailError"></div>
                    </div>

                    <button type="submit" class="btn-zentic btn-ai w-100 justify-content-center" id="sendBtn">
                        <span id="sendText"><i class="icon-send"></i> Send OTP</span>
                        <div class="spinner-border spinner-border-sm text-light ms-2" id="sendLoader"
                            style="display:none;" role="status"></div>
                    </button>
                </form>

                <div class="auth-divider"></div>
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-primary-c"><i class="icon-arrow-left"></i> Back to
                        login</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="otpModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="otpModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:1.25rem;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="otpModalTitle">Verify OTP</h5>
                </div>
                <div class="modal-body text-center">
                    <p class="fp-note mb-3">
                        Enter the 6-digit OTP sent to <b id="otpEmail"></b>
                    </p>

                    <div class="otp-boxes mb-2" id="otpBoxes">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" inputmode="numeric" maxlength="1" class="otp-box"
                                autocomplete="one-time-code" aria-label="OTP digit {{ $i + 1 }}">
                        @endfor
                    </div>
                    <div class="field-error text-danger" id="otpError" style="min-height:1.2rem;"></div>

                    <div class="fp-note mt-2">
                        Didn't receive the OTP?
                        <button type="button" class="btn btn-link p-0 align-baseline" id="resendBtn" disabled>
                            Resend OTP
                        </button>
                        <span id="resendTimer"></span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        id="cancelBtn">Cancel</button>
                    <button type="button" class="btn-zentic btn-ai" id="verifyBtn">
                        <span id="verifyText">Verify OTP</span>
                        <div class="spinner-border spinner-border-sm text-light ms-2" id="verifyLoader"
                            style="display:none;" role="status"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('seller/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('seller/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('seller/js/toastr.min.js') }}"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 4000,
            preventDuplicates: true
        };
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
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
                $.post("{{ route('front.check.forgot.password') }}", {
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
                $.post("{{ route('front.check.forgot.otp') }}", {
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
                $.post("{{ route('front.resend.forgot.otp') }}", {
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
</body>

</html>