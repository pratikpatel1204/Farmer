@extends('front.layout.app')
@section('title', 'Login - ' . config('app.name'))
@section('content')
@include('front.auth._assets')
<div class="inner-page">
    <section class="auth-section">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="auth-card">
                        @include('front.auth._side', [
                            'heading' => 'Welcome back!',
                            'text' => 'Log in to manage your listings, see new requirements and talk to buyers.',
                        ])
                        <div class="auth-form-wrap">
                            <h2 class="auth-title">Login</h2>
                            <p class="auth-subtitle">Sign in with your mobile number or Unique ID.</p>
                            <form id="loginForm" class="farm-form" novalidate>
                                <div class="mb-3">
                                    <label for="login" class="form-label">Mobile Number / Unique ID</label>
                                    <div class="input-icon">
                                        <i class="fa-solid fa-user"></i>
                                        <input type="text" class="form-control" id="login" name="login"
                                            placeholder="e.g. 9876543210 or FRM123456" autocomplete="username">
                                    </div>
                                    <div class="field-error" id="loginError"></div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <label for="password" class="form-label">Password</label>
                                        <a href="{{ route('forgot.password') }}" class="auth-link small">Forgot password?</a>
                                    </div>
                                    <div class="input-icon">
                                        <i class="fa-solid fa-lock"></i>
                                        <input type="password" class="form-control has-toggle" id="password" name="password"
                                            placeholder="Your password" autocomplete="current-password">
                                        <button type="button" class="password-toggle" data-password-toggle="#password" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
                                    </div>
                                    <div class="field-error" id="passwordError"></div>
                                </div>
                                <button type="submit" class="btn btn-farm w-100" id="signupButton">
                                    <span id="btnText"><i class="fa-solid fa-right-to-bracket me-1"></i>Log In</span>
                                    <span class="spinner-border spinner-border-sm" id="loader" style="display: none;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </button>
                            </form>
                            <p class="auth-switch">Don't have an account? <a href="{{ route('register') }}" class="auth-link">Create a free account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

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
                            if (response.redirectUrl) window.location.href = response.redirectUrl;
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

                            if (response && response.errors) {
                                $.each(response.errors, function(key, value) {
                                    var message = value[0];

                                    if (key === 'login') {
                                        setFieldError($('#login'), $('#loginError'), message);
                                    } else if (key === 'password') {
                                        setFieldError($('#password'), $('#passwordError'), message);
                                    } else {
                                        // Not tied to a known field on this form -> show as toastr
                                        toastr.error(message);
                                    }
                                });
                            } else if (response && response.message) {
                                toastr.error(response.message);
                            } else {
                                toastr.error("Please check the highlighted fields and try again.");
                            }
                        } else if (xhr.status === 419) {
                            toastr.error("Your session expired. Please refresh the page and try again.");
                        } else if (xhr.status === 429) {
                            toastr.error("Too many attempts. Please try again later.");
                        } else {
                            toastr.error("An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });
        });
    </script>
@endpush
