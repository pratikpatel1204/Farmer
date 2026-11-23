<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} || Set New Password</title>
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
                    <h2 class="auth-form-title">Set new password</h2>
                    <p class="auth-form-subtitle">Create a strong password for your account</p>
                </div>
                <form id="passwordForm" novalidate>
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group-zentic">
                            <span class="input-group-addon"><i class="icon-lock"></i></span>
                            <input type="password" class="form-control-zentic" id="password" name="password"
                                placeholder="New password" autocomplete="new-password">
                            <span class="input-group-addon input-right" style="cursor:pointer;" data-toggle="password"
                                data-target="#password">
                                <i class="icon-eye"></i>
                            </span>
                        </div>
                        <ul class="fp-rules">
                            <li>Minimum 6 characters</li>
                            <li>1 capital letter, 1 number and 1 special character</li>
                        </ul>
                        <div class="field-error text-danger" id="passwordError"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group-zentic">
                            <span class="input-group-addon"><i class="icon-lock"></i></span>
                            <input type="password" class="form-control-zentic" id="confirm_password"
                                name="confirm_password" placeholder="Confirm password" autocomplete="new-password">
                            <span class="input-group-addon input-right" style="cursor:pointer;" data-toggle="password"
                                data-target="#confirm_password">
                                <i class="icon-eye"></i>
                            </span>
                        </div>
                        <div class="field-error text-danger" id="confirmError"></div>
                    </div>

                    <button type="submit" class="btn-zentic btn-ai w-100 justify-content-center" id="saveBtn">
                        <span id="saveText"><i class="icon-check"></i> Update Password</span>
                        <div class="spinner-border spinner-border-sm text-light ms-2" id="saveLoader"
                            style="display:none;" role="status"></div>
                    </button>
                </form>
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
            var rule = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{6,}$/;

            $('[data-toggle="password"]').on('click', function() {
                var $input = $($(this).data('target'));
                var show = $input.attr('type') === 'password';
                $input.attr('type', show ? 'text' : 'password');
                $(this).find('i').toggleClass('icon-eye', !show).toggleClass('icon-eye-off', show);
            });

            function setError(sel, errSel, msg) {
                $(sel).addClass('is-invalid');
                $(errSel).text(msg);
            }

            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();
                $('#password, #confirm_password').removeClass('is-invalid');
                $('#passwordError, #confirmError').text('');

                var pass = $('#password').val();
                var conf = $('#confirm_password').val();
                var ok = true;

                if (!rule.test(pass)) {
                    setError('#password', '#passwordError',
                        'Password must have 1 capital letter, 1 number and 1 special character (min 6).'
                    );
                    ok = false;
                }
                if (pass !== conf) {
                    setError('#confirm_password', '#confirmError', 'Confirm password does not match.');
                    ok = false;
                }
                if (!ok) return;

                $('#saveBtn').prop('disabled', true);
                $('#saveText').hide();
                $('#saveLoader').show();

                $.post("{{ route('front.save.password') }}", {
                        password: pass,
                        confirm_password: conf
                    })
                    .done(function(res) {
                        toastr.success(res.message);
                        setTimeout(function() {
                            window.location.href = res.redirectUrl;
                        }, 1200);
                    })
                    .fail(function(xhr) {
                        var r = xhr.responseJSON || {};
                        if (r.errors) {
                            if (r.errors.password) setError('#password', '#passwordError', r.errors
                                .password[0]);
                            if (r.errors.confirm_password) setError('#confirm_password',
                                '#confirmError', r.errors.confirm_password[0]);
                        } else {
                            toastr.error(r.message || 'Something went wrong.');
                        }
                        if (r.redirectUrl) setTimeout(function() {
                            window.location.href = r.redirectUrl;
                        }, 1500);
                        $('#saveBtn').prop('disabled', false);
                        $('#saveText').show();
                        $('#saveLoader').hide();
                    });
            });
        });
    </script>
</body>

</html>