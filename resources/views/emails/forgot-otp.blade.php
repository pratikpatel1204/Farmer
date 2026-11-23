<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $appName }} - Password reset OTP</title>
</head>
<body style="margin:0; padding:0; background-color:#eef5e9; font-family:Arial, Helvetica, sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

    {{-- Hidden preheader (inbox preview text) --}}
    <div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#eef5e9; opacity:0;">
        Your {{ $appName }} password reset OTP is {{ $otp }}. Valid for {{ $minutes }} minutes.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eef5e9;">
        <tr>
            <td align="center" style="padding:32px 12px;">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; background-color:#ffffff; border-radius:14px; overflow:hidden; border:1px solid #d9e8d0;">

                    {{-- Top colour strip --}}
                    <tr>
                        <td style="height:6px; line-height:6px; font-size:0; background-color:#8cc63f;">&nbsp;</td>
                    </tr>

                    {{-- Header --}}
                    <tr>
                        <td align="center" bgcolor="#1d7a3a" style="background-color:#1d7a3a; padding:28px 24px 26px;">
                            @if(!empty($logoUrl))
                                <img src="{{ $logoUrl }}" alt="{{ $appName }}" height="48" style="display:block; height:48px; width:auto; max-width:200px; border:0; margin:0 auto 12px;">
                            @endif
                            <div style="font-size:22px; line-height:28px; font-weight:bold; color:#ffffff; letter-spacing:0.3px;">
                                {{ $appName }}
                            </div>
                            <div style="font-size:13px; line-height:20px; color:#d6eec3; margin-top:4px;">
                                Fresh from farmers, straight to you
                            </div>
                        </td>
                    </tr>

                    {{-- Icon + title --}}
                    <tr>
                        <td align="center" style="padding:34px 32px 8px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" width="64" height="64" style="width:64px; height:64px; border-radius:32px; background-color:#fff4dc; font-size:30px; line-height:64px; text-align:center;">
                                        &#128274;
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin:18px 0 0; font-size:24px; line-height:32px; color:#135c2a; font-weight:bold;">
                                Reset your password
                            </h1>
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding:14px 40px 0; font-size:15px; line-height:24px; color:#3d4a3f;">
                            <p style="margin:0 0 12px;">Hello <strong style="color:#135c2a;">{{ $user->full_name }}</strong>,</p>
                            <p style="margin:0;">
                                We received a request to reset the password of your {{ $appName }} account.
                                Use the OTP below to continue.
                            </p>
                        </td>
                    </tr>

                    {{-- OTP box --}}
                    <tr>
                        <td align="center" style="padding:26px 40px 8px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:360px;">
                                <tr>
                                    <td align="center" style="background-color:#f3f9ee; border:2px dashed #8cc63f; border-radius:12px; padding:20px 12px;">
                                        <div style="font-size:12px; line-height:16px; color:#5b6b5e; text-transform:uppercase; letter-spacing:2px; font-weight:bold;">
                                            Your OTP
                                        </div>
                                        <div style="font-family:'Courier New', Courier, monospace; font-size:38px; line-height:48px; font-weight:bold; color:#135c2a; letter-spacing:10px; margin-top:6px;">
                                            {{ $otp }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Validity --}}
                    <tr>
                        <td align="center" style="padding:10px 40px 0;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#fff4dc; border-radius:20px; padding:8px 18px; font-size:13px; line-height:18px; color:#8a5a00; font-weight:bold;">
                                        &#9200;&nbsp; Valid for {{ $minutes }} minutes only
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Warning --}}
                    <tr>
                        <td style="padding:26px 40px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#fffaf0; border-left:4px solid #f5b324; border-radius:6px; padding:14px 16px; font-size:14px; line-height:22px; color:#5c4a1f;">
                                        <strong style="color:#8a5a00;">Do not share this OTP with anyone.</strong><br>
                                        {{ $appName }} team will never ask for your OTP on call, SMS or WhatsApp.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Ignore note --}}
                    <tr>
                        <td style="padding:20px 40px 34px; font-size:14px; line-height:22px; color:#5b6b5e;">
                            If you did not request a password reset, you can safely ignore this email.
                            Your password will stay the same.
                            <p style="margin:18px 0 0; color:#3d4a3f;">
                                Thank you,<br>
                                <strong style="color:#1d7a3a;">Team {{ $appName }}</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" bgcolor="#135c2a" style="background-color:#135c2a; padding:18px 24px; font-size:12px; line-height:18px; color:#cfe6c2;">
                            This is an automated email, please do not reply.<br>
                            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
