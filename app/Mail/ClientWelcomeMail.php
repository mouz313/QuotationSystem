<?php

namespace App\Mail;

use App\Models\ClientUser;
use App\Models\Company;
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
        public ?Company $company = null,
    ) {}

    public function envelope(): Envelope
    {
        $companyName = $this->company->name ?? config('app.name');
        return new Envelope(
            subject: 'Welcome to ' . $companyName . ' - Your Client Portal',
        );
    }

    public function content(): Content
    {
        $brandColor = $this->company->brand_color ?? '#4f46e5';

        return new Content(
            view: 'emails.client-welcome',
            with: [
                'clientUser' => $this->clientUser,
                'tempPassword' => $this->tempPassword,
                'quoteNumber' => $this->quoteNumber,
                'company' => $this->company,
                'brandColor' => $brandColor,
            ],
        );
    }
}
