<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailForgotOtp extends Mailable
{
    use Queueable, SerializesModels;

    /** OTP validity in minutes (keep same as AuthMemberController::OTP_MINUTES) */
    public int $minutes = 10;

    public function __construct(public User $user, public string $otp)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Password reset OTP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.forgot-otp',
            with: [
                'user'    => $this->user,
                'otp'     => $this->otp,
                'minutes' => $this->minutes,
                'appName' => config('app.name'),
                'logoUrl' => asset('front/image/logo.png'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
