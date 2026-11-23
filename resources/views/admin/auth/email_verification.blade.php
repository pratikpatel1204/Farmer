<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ config('app.name') . ' || Email Verification' }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('admin/img/apple-touch-icon.png') }}">

    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/icons/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/tabler-icons/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body class="auth-body">
    <div id="global-loader" style="display: none;">
        <div class="page-loader"></div>
    </div>

    <div class="auth-container">

        <!-- LEFT – Branding -->
        <div class="auth-brand">
            <div class="auth-brand-content">
                <div class="auth-brand-logo">
                    <img src="{{ asset('admin/img/logo.png') }}" alt="{{ config('app.name') }}">
                </div>
                <h1>{{ config('app.name') }}</h1>
                <p>One more step to keep your account safe and sound.</p>
                <ul class="auth-brand-features">
                    <li><i class="ti ti-device-analytics"></i> Real‑time insights</li>
                    <li><i class="ti ti-shield-lock"></i> Enterprise‑grade security</li>
                    <li><i class="ti ti-users"></i> Team collaboration tools</li>
                </ul>
            </div>
        </div>

        <!-- RIGHT – OTP form -->
        <div class="auth-form-wrapper">
            <div class="auth-form-header text-center">
                <h2>Email OTP verification</h2>
                <p>Please enter the OTP received to confirm your account ownership. A code has been sent to
                    {{ substr($user->email, 0, 5) . '****' . strstr($user->email, '@') }}</p>
            </div>

            <form id="otpForm" class="digit-group">
                <div class="auth-otp-group">
                    <input type="text" class="auth-otp-box" id="digit-1" data-next="digit-2" maxlength="1"
                        inputmode="numeric">
                    <input type="text" class="auth-otp-box" id="digit-2" data-next="digit-3"
                        data-previous="digit-1" maxlength="1" inputmode="numeric">
                    <input type="text" class="auth-otp-box" id="digit-3" data-next="digit-4"
                        data-previous="digit-2" maxlength="1" inputmode="numeric">
                    <input type="text" class="auth-otp-box" id="digit-4" data-next="digit-5"
                        data-previous="digit-3" maxlength="1" inputmode="numeric">
                    <input type="text" class="auth-otp-box" id="digit-5" data-next="digit-6"
                        data-previous="digit-4" maxlength="1" inputmode="numeric">
                    <input type="text" class="auth-otp-box" id="digit-6" data-previous="digit-5" maxlength="1"
                        inputmode="numeric">
                </div>

                <p class="auth-resend">Didn't get the OTP? <a href="javascript:void(0);" id="resendOtp">Resend OTP</a>
                </p>

                <button type="submit" class="auth-btn" id="verifyBtn">
                    <span class="btn-text">Verify &amp; proceed</span>
                    <span class="spinner-border spinner-border-sm d-none btn-spinner" role="status"></span>
                </button>
            </form>
        </div>
    </div>

    <script src="{{ asset('admin/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/js/feather.min.js') }}"></script>
    <script src="{{ asset('admin/js/script.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.auth-otp-box').on('keyup', function(e) {
                let input = $(this);
                let value = input.val();

                if (value.length === 1) {
                    let nextId = input.data('next');
                    if (nextId) {
                        $('#' + nextId).focus();
                    }
                }

                if (e.key === "Backspace") {
                    let prevId = input.data('previous');
                    if (prevId) {
                        $('#' + prevId).focus();
                    }
                }
            });

            // OTP Form Submission via AJAX
            $('#otpForm').on('submit', function(e) {
                e.preventDefault();

                let otp = '';
                $('.digit-group input').each(function() {
                    otp += $(this).val();
                });

                let $btn = $('#verifyBtn');
                $btn.find('.btn-text').addClass('d-none');
                $btn.find('.btn-spinner').removeClass('d-none');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('verify.otp') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        email: '{{ $user->email }}',
                        otp: otp
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = response.redirect_url;
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong. Please try again.');
                    },
                    complete: function() {
                        $btn.find('.btn-text').removeClass('d-none');
                        $btn.find('.btn-spinner').addClass('d-none');
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Resend OTP via AJAX
            $('#resendOtp').on('click', function() {
                let $link = $(this);
                let originalText = $link.text();
                $link.addClass('disabled').text('Sending...');

                $.ajax({
                    url: "{{ route('resend.otp') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        email: '{{ $user->email }}'
                    },
                    success: function(response) {
                        $link.removeClass('disabled').text(originalText);

                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        $link.removeClass('disabled').text(originalText);
                        toastr.error('Failed to resend OTP. Please try again.');
                    }
                });
            });

        });
    </script>
</body>

</html>
