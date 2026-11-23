@extends('front.layout.app')
@section('title', 'Set new password - ' . config('app.name'))
@section('content')
@include('front.auth._assets')

<div class="inner-page">
    <section class="auth-section">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="auth-card">
                        @include('front.auth._side', [
                            'heading' => 'Almost done!',
                            'text' => 'Choose a new password you can remember. You will use it with your mobile number or Unique ID.',
                        ])

                        <div class="auth-form-wrap">
                            <span class="auth-badge"><i class="fa-solid fa-lock"></i></span>
                            <h2 class="auth-title">Set new password</h2>
                            <p class="auth-subtitle">Create a strong password for your account.</p>

                            <form id="passwordForm" class="farm-form" novalidate>
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-icon">
                                        <i class="fa-solid fa-lock"></i>
                                        <input type="password" class="form-control has-toggle" id="password" name="password"
                                            placeholder="New password" autocomplete="new-password">
                                        <button type="button" class="password-toggle" data-password-toggle="#password" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
                                    </div>
                                    <ul class="pass-rules">
                                        <li><i class="fa-solid fa-circle"></i>Minimum 6 characters</li>
                                    </ul>
                                    <div class="field-error" id="passwordError"></div>
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <div class="input-icon">
                                        <i class="fa-solid fa-lock"></i>
                                        <input type="password" class="form-control has-toggle" id="confirm_password"
                                            name="confirm_password" placeholder="Confirm password" autocomplete="new-password">
                                        <button type="button" class="password-toggle" data-password-toggle="#confirm_password" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
                                    </div>
                                    <div class="field-error" id="confirmError"></div>
                                </div>

                                <button type="submit" class="btn btn-farm w-100" id="saveBtn">
                                    <span id="saveText"><i class="fa-solid fa-check me-1"></i>Update Password</span>
                                    <span class="spinner-border spinner-border-sm" id="saveLoader" style="display:none;" role="status">
                                        <span class="visually-hidden">Saving...</span>
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
@endsection

@push('scripts')
    <script>
        $(function() {
            function setError(sel, errSel, msg) {
                $(sel).addClass('is-invalid');
                $(errSel).text(msg);
            }

            $('#password, #confirm_password').on('input', function() {
                $(this).removeClass('is-invalid');
                $(this.id === 'password' ? '#passwordError' : '#confirmError').text('');
            });

            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();
                $('#password, #confirm_password').removeClass('is-invalid');
                $('#passwordError, #confirmError').text('');

                var pass = $('#password').val();
                var conf = $('#confirm_password').val();
                var ok = true;

                if (pass.length < 6) {
                    setError('#password', '#passwordError', 'Password must be at least 6 characters.');
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

                $.post("{{ route('save.password') }}", {
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
                            if (r.errors.password) setError('#password', '#passwordError', r.errors.password[0]);
                            if (r.errors.confirm_password) setError('#confirm_password', '#confirmError', r.errors.confirm_password[0]);
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
@endpush
