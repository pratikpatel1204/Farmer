@extends('front.layout.app')
@section('title', 'Verify email - ' . config('app.name'))
@section('content')
@include('front.auth._assets')

<div class="inner-page">
    <section class="auth-section">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="auth-card">
                        @include('front.auth._side', [
                            'heading' => 'One last step',
                            'text' => 'Verify your email so we can keep your account safe and send you important updates.',
                        ])

                        <div class="auth-form-wrap text-center">
                            <span class="auth-badge mx-auto"><i class="fa-solid fa-envelope-circle-check"></i></span>
                            <h2 class="auth-title">Verify your email</h2>
                            <p class="auth-subtitle">Enter the 6-digit OTP sent to <b>{{ $email }}</b></p>

                            <div class="otp-boxes mb-2" id="otpBoxes">
                                @for ($i = 0; $i < 6; $i++)
                                    <input type="text" inputmode="numeric" maxlength="1" class="otp-box"
                                        autocomplete="one-time-code" aria-label="OTP digit {{ $i + 1 }}">
                                @endfor
                            </div>
                            <div class="field-error mb-3" id="otpError"></div>

                            <button type="button" class="btn btn-farm w-100 mt-2" id="verifyBtn">
                                <span id="verifyText"><i class="fa-solid fa-check me-1"></i>Verify &amp; Login</span>
                                <span class="spinner-border spinner-border-sm" id="verifyLoader" style="display:none;" role="status">
                                    <span class="visually-hidden">Checking...</span>
                                </span>
                            </button>

                            <div class="otp-note mt-3">
                                Didn't receive the OTP?
                                <button type="button" class="link-btn" id="resendBtn" disabled>Resend OTP</button>
                                <span id="resendTimer"></span>
                            </div>

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
            var verifyUrl = "{{ route('email.verify.submit', $encryptedUserId) }}";
            var resendUrl = "{{ route('email.verify.resend', $encryptedUserId) }}";
            var $boxes = $('.otp-box'),
                timer = null;
            $boxes.first().focus();

            function startTimer(sec) {
                clearInterval(timer);
                var left = sec;
                $('#resendBtn').prop('disabled', true);
                $('#resendTimer').text('(' + left + 's)');
                timer = setInterval(function() {
                    left--;
                    if (left <= 0) {
                        clearInterval(timer);
                        $('#resendTimer').text('');
                        $('#resendBtn').prop('disabled', false);
                    } else $('#resendTimer').text('(' + left + 's)');
                }, 1000);
            }
            startTimer(30);

            function setLoading(on) {
                $('#verifyBtn').prop('disabled', on);
                $('#verifyText').toggle(!on);
                $('#verifyLoader').toggle(on);
            }

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
                var t = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                if (!t) return;
                e.preventDefault();
                $boxes.each(function(i) {
                    this.value = t[i] || '';
                });
                $boxes.eq(Math.min(t.length, 5)).focus();
            });

            $('#verifyBtn').on('click', function() {
                var otp = $boxes.map(function() {
                    return this.value;
                }).get().join('');
                if (otp.length !== 6) {
                    $boxes.addClass('is-invalid');
                    $('#otpError').text('Please enter the 6-digit OTP.');
                    return;
                }
                setLoading(true);
                $.post(verifyUrl, {
                        otp: otp
                    })
                    .done(function(res) {
                        toastr.success(res.message);
                        window.location.href = res.redirectUrl;
                    })
                    .fail(function(xhr) {
                        $boxes.addClass('is-invalid');
                        $('#otpError').text((xhr.responseJSON && xhr.responseJSON.message) || 'Invalid OTP.');
                        setLoading(false);
                    });
            });

            $('#resendBtn').on('click', function() {
                $(this).prop('disabled', true);
                $.post(resendUrl)
                    .done(function(res) {
                        toastr.success(res.message);
                        $boxes.val('');
                        $boxes.first().focus();
                        startTimer(30);
                    })
                    .fail(function(xhr) {
                        toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Could not resend OTP.');
                        startTimer(30);
                    });
            });
        });
    </script>
@endpush
