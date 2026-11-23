<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') . ' || Login' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('member/img/favicon.png') }}">
    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('member/css/bootstrap.min.css') }}" id="bootstrapLtr">
    <link rel="stylesheet" href="{{ asset('member/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('member/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('member/css/toastr.min.css') }}">
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
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('member/img/logo.png') }}" alt="{{ config('app.name') }}">
                        </a>
                    </div>
                    <h2 class="auth-form-title">Welcome back</h2>
                    <p class="auth-form-subtitle">Sign in to your {{ config('app.name') }} dashboard</p>
                </div>
                <form id="loginForm" novalidate>
                    <div class="form-group">
                        <label for="login" class="form-label">Mobile Number / Unique ID</label>
                        <div class="input-group-zentic">
                            <span class="input-group-addon"><i class="icon-user"></i></span>
                            <input type="text" class="form-control-zentic" id="login" name="login">
                        </div>
                        <div class="field-error text-danger" id="loginError"></div>
                    </div>

                    <div class="form-group">
                        <div class="d-flex justify-content-between mb-1">
                            <label for="password" class="form-label mb-0">Password</label>
                            <a href="{{ route('forgot.password') }}" class="text-xs text-primary-c">Forgot
                                password?</a>
                        </div>
                        <div class="input-group-zentic">
                            <span class="input-group-addon"><i class="icon-lock"></i></span>
                            <input type="password" class="form-control-zentic" id="password" name="password"
                                placeholder="Your password">
                            <span class="input-group-addon input-right" style="cursor: pointer;"
                                onclick="togglePassword()">
                                <i class="icon-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>
                        <div class="field-error text-danger" id="passwordError"></div>
                    </div>

                    <button type="submit" class="btn-zentic btn-ai w-100 justify-content-center" id="signupButton">
                        <span id="btnText"><i class="icon-log-in"></i> Log In</span>
                        <div class="spinner-border spinner-border-sm text-light ms-2" id="loader"
                            style="display: none;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </form>
                <div class="auth-divider"></div>
                <div class="auth-form-header mb-3">
                    <h2 class="auth-form-subtitle">Don't have an account?</h2>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-3">
                    <a href="{{ route('home') }}" class="btn-zentic btn-outline">
                        <i class="icon-arrow-left"></i>Back
                    </a>
                    <a href="{{ route('register') }}" class="btn-zentic btn-ai">
                        <i class="icon-user-plus login-choice__icon"></i>Create account
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('member/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('member/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('member/js/toastr.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 4000,
                preventDuplicates: true
            };

            const uniqueId = sessionStorage.getItem('login_unique_id');
            const password = sessionStorage.getItem('login_password');
            if (uniqueId && password) {
                $('#login').val(uniqueId);
                $('#password').val(password);
                sessionStorage.removeItem('login_unique_id');
                sessionStorage.removeItem('login_password');
            }

            function setFieldError($input, $errorBox, message) {
                $input.addClass('is-invalid');
                $errorBox.text(message).addClass('show');
            }

            function clearFieldError($input, $errorBox) {
                $input.removeClass('is-invalid');
                $errorBox.text('').removeClass('show');
            }

            function clearAllErrors() {
                clearFieldError($('#login'), $('#loginError'));
                clearFieldError($('#password'), $('#passwordError'));
            }

            function setLoading(isLoading) {
                var $btn = $('#signupButton');
                if (isLoading) {
                    $btn.prop('disabled', true);
                    $('#btnText').hide();
                    $('#loader').show();
                } else {
                    $btn.prop('disabled', false);
                    $('#btnText').show();
                    $('#loader').hide();
                }
            }

            // Clear individual field error as user types
            $('#login').on('input', function() {
                clearFieldError($(this), $('#loginError'));
            });
            $('#password').on('input', function() {
                clearFieldError($(this), $('#passwordError'));
            });

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                clearAllErrors();

                var login = $('#login').val().trim();
                var password = $('#password').val().trim();
                var isValid = true;

                if (!login) {
                    setFieldError($('#login'), $('#loginError'),
                        'Please enter your Mobile Number or Unique ID.');
                    isValid = false;
                }

                if (!password) {
                    setFieldError($('#password'), $('#passwordError'), 'Please enter your password.');
                    isValid = false;
                } else if (password.length < 6) {
                    setFieldError($('#password'), $('#passwordError'),
                        'Password must be at least 6 characters long.');
                    isValid = false;
                }

                if (!isValid) {
                    return;
                }

                setLoading(true);

                $.ajax({
                    url: "{{ route('check.login') }}",
                    method: "POST",
                    data: {
                        login: $('#login').val(),
                        password: $('#password').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        setLoading(false);

                        if (response.status === 1) {
                            toastr.success(response.message);
                            window.location.href = response.redirectUrl;
                        } else if (response.status === 0) {
                            toastr.warning(response.message);
                            window.location.href = response.redirectUrl;
                        } else if (response.status === 2) {
                            toastr.warning(response.message);
                            setTimeout(function() {
                                window.location.href = response.redirectUrl;
                            }, 5000);
                        } else {
                            toastr.error(response.message ||
                                "An error occurred. Please try again.");
                        }
                    },
                    error: function(xhr) {
                        setLoading(false);

                        if (xhr.status === 422) {
                            var response = xhr.responseJSON;
                            var fieldKeys = ['login', 'password'];

                            if (response && response.errors) {
                                $.each(response.errors, function(key, value) {
                                    var message = value[0];

                                    if (key === 'login') {
                                        setFieldError($('#login'), $('#loginError'),
                                            message);
                                    } else if (key === 'password') {
                                        setFieldError($('#password'), $(
                                            '#passwordError'), message);
                                    } else {
                                        // Not tied to a known field on this form -> show as toastr
                                        toastr.error(message);
                                    }
                                });
                            } else if (response && response.message) {
                                toastr.error(response.message);
                            } else {
                                toastr.error(
                                    "Please check the highlighted fields and try again.");
                            }
                        } else if (xhr.status === 429) {
                            toastr.error("Too many attempts. Please try again later.");
                        } else {
                            toastr.error("An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });
        });

        function togglePassword() {
            const passwordField = document.getElementById("password");
            const toggleIcon = document.getElementById("togglePasswordIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("icon-eye");
                toggleIcon.classList.add("icon-eye-off");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("icon-eye-off");
                toggleIcon.classList.add("icon-eye");
            }
        }
    </script>
</body>

</html>
