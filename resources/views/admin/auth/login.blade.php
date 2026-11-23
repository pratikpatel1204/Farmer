<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') . ' || Login' }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('admin/img/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/tabler-icons/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body class="auth-body">
    <main class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('admin/img/logo.png') }}" alt="{{ config('app.name') }}">
        </div>
        <div class="auth-form-header">
            <h2>Welcome back</h2>
            <p>Sign in with your mobile number to continue.</p>
        </div>
        <form id="adminLoginForm" novalidate>
            @csrf
            <div class="auth-field">
                <label for="emailInput">Email address</label>
                <div class="auth-input-group" id="emailShell">
                    <span class="auth-input-icon"><i class="ti ti-mail"></i></span>
                    <input id="emailInput" type="email" name="email" placeholder="you@example.com"
                        autocomplete="username">
                </div>
                <span class="auth-error error-email"></span>
            </div>
            <div class="auth-field">
                <label for="passwordInput">Password</label>
                <div class="auth-input-group" id="passwordShell">
                    <span class="auth-input-icon"><i class="ti ti-lock"></i></span>
                    <input id="passwordInput" type="password" name="password" placeholder="Enter your password"
                        autocomplete="current-password">
                    <button type="button" class="auth-toggle-password" aria-label="Show password">
                        <i class="ti ti-eye-off"></i>
                    </button>
                </div>
                <span class="auth-error error-password"></span>
            </div>
            <label class="auth-remember">
                <input type="checkbox" id="remember_me" name="remember">
                <span>Remember me</span>
            </label>
            <button type="submit" class="auth-btn" id="loginBtn">
                <span class="auth-spinner" aria-hidden="true"></span>
                <span class="btn-text">Sign in</span>
            </button>
        </form>
    </main>
    <script src="{{ asset('admin/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 3000
            };

            // Mobile: sirf digits allow
            $("#emailInput").on('input', function() {
                clearError('email');
            });

            $("#passwordInput").on('input', function() {
                clearError('password');
            });

            // Password show / hide
            $(".auth-toggle-password").on('click', function() {
                const input = $("#passwordInput");
                const show = input.attr('type') === 'password';
                input.attr('type', show ? 'text' : 'password');
                $(this).find('i').toggleClass('ti-eye-off', !show).toggleClass('ti-eye', show);
            });

            function setError(field, msg) {
                $(".error-" + field).text(msg);
                $("#" + field + "Shell").addClass("is-invalid");
            }

            function clearError(field) {
                $(".error-" + field).text("");
                $("#" + field + "Shell").removeClass("is-invalid");
            }

            function setLoading(state) {
                $("#loginBtn").prop("disabled", state).toggleClass("is-loading", state);
                $("#loginBtn .btn-text").text(state ? "Signing in..." : "Sign in");
            }

            $("#adminLoginForm").on('submit', function(e) {
                e.preventDefault();

                clearError('email');
                clearError('password');

                const email = $("#emailInput").val().trim();
                const password = $("#passwordInput").val();
                let hasError = false;

                if (email === "") {
                    setError('email', "Email is required");
                    hasError = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    setError('email', "Enter a valid email address");
                    hasError = true;
                }

                if (password.trim() === "") {
                    setError('password', "Password is required");
                    hasError = true;
                }

                setLoading(true);

                $.ajax({
                    url: "{{ route('admin.login.submit') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === true) {
                            toastr.success("Login successful! Redirecting...");
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('admin.dashboard') }}";
                            }, 600);
                        } else {
                            toastr.error(response.message ?? "Something went wrong!");
                            setLoading(false);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.email) setError('email', errors.email[0]);
                            if (errors.password) setError('password', errors.password[0]);
                            toastr.error((errors.email || errors.password)[0]);
                        } else if (xhr.status === 401) {
                            toastr.error(xhr.responseJSON?.message ??
                                "Invalid email or password.");
                        } else if (xhr.status === 419) {
                            toastr.error("Session expired. Please refresh the page.");
                        } else if (xhr.status === 429) {
                            toastr.error("Too many attempts. Please try again later.");
                        } else {
                            toastr.error("Login failed, please try again.");
                        }
                        setLoading(false);
                    }
                });
            });

        });
    </script>

</body>

</html>
