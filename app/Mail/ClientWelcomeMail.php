<?php

namespace App\Mail;

use App\Models\ClientUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClientUser $clientUser,
        public string $tempPassword,
        public ?string $quoteNumber = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name') . ' - Your Client Portal',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-welcome',
        );
    }
}
