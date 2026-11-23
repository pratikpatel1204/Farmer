<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ config('app.name') . ' || Forgot Password' }}</title>
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
                <p>Smart management for your dream team. Secure, fast, and built for scale.</p>
                <ul class="auth-brand-features">
                    <li><i class="ti ti-device-analytics"></i> Real‑time insights</li>
                    <li><i class="ti ti-shield-lock"></i> Enterprise‑grade security</li>
                    <li><i class="ti ti-users"></i> Team collaboration tools</li>
                </ul>
            </div>
        </div>

        <!-- RIGHT – Forgot password form -->
        <div class="auth-form-wrapper">
            <div class="auth-form-header">
                <h2>Forgot password?</h2>
                <p>No worries — enter your email and we'll send you instructions to reset it.</p>
            </div>

            <form id="forgotPasswordForm">
                <div class="auth-field">
                    <label for="email">Email address</label>
                    <div class="auth-input-group" id="emailShell">
                        <span class="auth-input-icon"><i class="ti ti-mail"></i></span>
                        <input type="email" id="email" name="email" placeholder="you@company.com"
                            autocomplete="email">
                    </div>
                </div>

                <button type="submit" class="auth-btn" id="submitBtn">
                    <span class="btn-text">Submit</span>
                    <span class="spinner-border spinner-border-sm d-none btn-spinner" role="status"></span>
                </button>
            </form>

            <p class="auth-back">Return to <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>

    <script src="{{ asset('admin/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/js/feather.min.js') }}"></script>
    <script src="{{ asset('admin/js/script.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#forgotPasswordForm').on('submit', function(e) {
                e.preventDefault();

                let email = $('#email').val();
                if (!email) {
                    toastr.error('Please enter an email address.');
                    return;
                }

                let $btn = $('#submitBtn');
                $btn.find('.btn-text').addClass('d-none');
                $btn.find('.btn-spinner').removeClass('d-none');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('forgot.password.check') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        email: email
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            setTimeout(function() {
                                window.location.href = res.redirect_url;
                            }, 1000);
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error("Something went wrong.");
                    }
                    complete: function() {
                        $btn.find('.btn-text').removeClass('d-none');
                        $btn.find('.btn-spinner').addClass('d-none');
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>
