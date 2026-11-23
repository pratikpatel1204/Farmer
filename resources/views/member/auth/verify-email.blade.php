<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} || Verify Email</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('seller/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('seller/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('seller/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('seller/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('seller/css/toastr.min.css') }}">
    <style>
        .otp-boxes{display:flex;justify-content:center;gap:.5rem}
        .otp-box{width:46px;height:54px;text-align:center;font-size:1.4rem;font-weight:700;border:1.5px solid #d3def0;border-radius:.75rem;outline:0}
        .otp-box:focus{border-color:#0a6cf0;box-shadow:0 0 0 .2rem rgba(10,108,240,.18)}
        .otp-box.is-invalid{border-color:#dc3545}
        @media (max-width:400px){.otp-box{width:40px;height:48px}}
    </style>
</head>
<body>
<div class="auth-page">
    <div class="auth-right">
        <div class="auth-form-container text-center">
            <div class="auth-form-header">
                <div class="auth-form-logo"><img src="{{ asset('seller/img/logo.png') }}" alt="{{ config('app.name') }}"></div>
                <h2 class="auth-form-title">Verify your email</h2>
                <p class="auth-form-subtitle">Enter the 6-digit OTP sent to <b>{{ $email }}</b></p>
            </div>

            <div class="otp-boxes mb-2" id="otpBoxes">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" inputmode="numeric" maxlength="1" class="otp-box" autocomplete="one-time-code">
                @endfor
            </div>
            <div class="text-danger mb-3" id="otpError" style="min-height:1.2rem;"></div>

            <button type="button" class="btn-zentic btn-ai w-100 justify-content-center" id="verifyBtn">
                <span id="verifyText">Verify & Login</span>
                <div class="spinner-border spinner-border-sm text-light ms-2" id="verifyLoader" style="display:none;"></div>
            </button>

            <div class="mt-3" style="font-size:.85rem">
                Didn't receive the OTP?
                <button type="button" class="btn btn-link p-0 align-baseline" id="resendBtn" disabled>Resend OTP</button>
                <span id="resendTimer"></span>
            </div>

            <div class="auth-divider"></div>
            <a href="{{ route('login') }}" class="text-primary-c"><i class="icon-arrow-left"></i> Back to login</a>
        </div>
    </div>
</div>

<script src="{{ asset('seller/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('seller/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('seller/js/toastr.min.js') }}"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $(function () {
        var verifyUrl = "{{ route('email.verify.submit', $encryptedUserId) }}";
        var resendUrl = "{{ route('email.verify.resend', $encryptedUserId) }}";
        var $boxes = $('.otp-box'), timer = null;
        $boxes.first().focus();

        function startTimer(sec) {
            clearInterval(timer);
            var left = sec;
            $('#resendBtn').prop('disabled', true);
            $('#resendTimer').text('(' + left + 's)');
            timer = setInterval(function () {
                left--;
                if (left <= 0) { clearInterval(timer); $('#resendTimer').text(''); $('#resendBtn').prop('disabled', false); }
                else $('#resendTimer').text('(' + left + 's)');
            }, 1000);
        }
        startTimer(30);

        function setLoading(on) {
            $('#verifyBtn').prop('disabled', on);
            $('#verifyText').toggle(!on);
            $('#verifyLoader').toggle(on);
        }

        $boxes.on('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 1);
            $boxes.removeClass('is-invalid'); $('#otpError').text('');
            if (this.value) $(this).next('.otp-box').focus();
        });
        $boxes.on('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value) $(this).prev('.otp-box').focus();
            if (e.key === 'Enter') $('#verifyBtn').click();
        });
        $boxes.on('paste', function (e) {
            var t = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            if (!t) return;
            e.preventDefault();
            $boxes.each(function (i) { this.value = t[i] || ''; });
            $boxes.eq(Math.min(t.length, 5)).focus();
        });

        $('#verifyBtn').on('click', function () {
            var otp = $boxes.map(function () { return this.value; }).get().join('');
            if (otp.length !== 6) {
                $boxes.addClass('is-invalid'); $('#otpError').text('Please enter the 6-digit OTP.'); return;
            }
            setLoading(true);
            $.post(verifyUrl, { otp: otp })
                .done(function (res) {
                    toastr.success(res.message);
                    window.location.href = res.redirectUrl;
                })
                .fail(function (xhr) {
                    $boxes.addClass('is-invalid');
                    $('#otpError').text((xhr.responseJSON && xhr.responseJSON.message) || 'Invalid OTP.');
                    setLoading(false);
                });
        });

        $('#resendBtn').on('click', function () {
            $(this).prop('disabled', true);
            $.post(resendUrl)
                .done(function (res) { toastr.success(res.message); $boxes.val(''); $boxes.first().focus(); startTimer(30); })
                .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Could not resend OTP.'); startTimer(30); });
        });
    });
</script>
</body>
</html>