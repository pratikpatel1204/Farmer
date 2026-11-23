<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ config('app.name') . ' || Reset Password' }}</title>
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

        <!-- RIGHT – Reset password form -->
        <div class="auth-form-wrapper">
            <div class="auth-form-header">
                <h2>Reset password</h2>
                <p>Your new password must be different from previously used passwords.</p>
            </div>

            <form id="resetPasswordForm">
                <div class="auth-field">
                    <label for="password">Password</label>
                    <div class="auth-input-group" id="passwordShell">
                        <span class="auth-input-icon"><i class="ti ti-lock"></i></span>
                        <input type="password" id="password" placeholder="Enter new password"
                            autocomplete="new-password">
                        <span class="auth-toggle-password ti ti-eye-off" role="button" tabindex="0"></span>
                    </div>
                    <span class="auth-hint" id="passwordInfo"></span>
                </div>

                <div class="auth-field">
                    <label for="confirm_password">Confirm password</label>
                    <div class="auth-input-group" id="confirmShell">
                        <span class="auth-input-icon"><i class="ti ti-lock"></i></span>
                        <input type="password" id="confirm_password" placeholder="Re-enter new password"
                            autocomplete="new-password">
                        <span class="auth-toggle-passwords ti ti-eye-off" role="button" tabindex="0"></span>
                    </div>
                    <span class="auth-hint" id="matchMessage"></span>
                </div>

                <button type="submit" id="resetBtn" class="auth-btn">
                    <span class="btn-text">Submit</span>
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

            // Confirm password match check
            $("#password, #confirm_password").on("keyup", function() {
                let pass = $("#password").val();
                let cpass = $("#confirm_password").val();

                if (pass.length < 1 || cpass.length < 1) {
                    $("#matchMessage").html("").removeClass("text-success text-danger");
                    return;
                }

                if (pass === cpass) {
                    $("#matchMessage").html("✔ Passwords match").removeClass("text-danger").addClass(
                        "text-success");
                } else {
                    $("#matchMessage").html("✖ Passwords do not match").removeClass("text-success")
                        .addClass("text-danger");
                }
            });

            // Password strength check
            $("#password").on("keyup", function() {
                let pass = $(this).val();
                let $info = $("#passwordInfo").removeClass("text-danger text-success text-warning");

                if (pass.length === 0) {
                    $info.html("");
                } else if (pass.length < 6) {
                    $info.html("Too weak").addClass("text-danger");
                } else if (pass.match(/[A-Z]/) && pass.match(/[0-9]/) && pass.match(/[^a-zA-Z0-9]/)) {
                    $info.html("Strong password").addClass("text-success");
                } else {
                    $info.html("Medium strength").addClass("text-warning");
                }
            });

            // Show / hide main password
            $(".auth-toggle-password").on("click", function() {
                let input = $("#password");
                let isPassword = input.attr("type") === "password";
                input.attr("type", isPassword ? "text" : "password");
                $(this).toggleClass("ti-eye-off ti-eye");
            });

            // Show / hide confirm password
            $(".auth-toggle-passwords").on("click", function() {
                let input = $("#confirm_password");
                let isPassword = input.attr("type") === "password";
                input.attr("type", isPassword ? "text" : "password");
                $(this).toggleClass("ti-eye-off ti-eye");
            });

            // AJAX form submit + button loader
            $("#resetPasswordForm").on("submit", function(e) {
                e.preventDefault();

                let password = $("#password").val();
                let confirm_password = $("#confirm_password").val();

                if (password !== confirm_password) {
                    toastr.error("Passwords do not match!");
                    return;
                }

                let $btn = $("#resetBtn");
                $btn.find(".btn-text").addClass("d-none");
                $btn.find(".btn-spinner").removeClass("d-none");
                $btn.prop("disabled", true);

                $.ajax({
                    url: "{{ route('reset.password.submit') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        password: password,
                        confirm_password: confirm_password,
                        email: '{{ $user->email }}',
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = response.redirect_url;
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error("Something went wrong. Try again.");
                    },
                    complete: function() {
                        $btn.find(".btn-text").removeClass("d-none");
                        $btn.find(".btn-spinner").addClass("d-none");
                        $btn.prop("disabled", false);
                    }
                });
            });

        });
    </script>
</body>

</html>
