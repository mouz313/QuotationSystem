<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $token,
        public string $email = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reset Your Password');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-password-reset',
            with: [
                'userName' => $this->userName,
                'resetUrl' => url('/client/reset-password/' . $this->token . '?' . http_build_query(['email' => $this->email])),
            ],
        );
    }
}
