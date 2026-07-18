<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PackageExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public Package $package,
        public int $daysLeft,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your {$this->package->name} package expires in {$this->daysLeft} day(s)",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.package-expiry-reminder',
        );
    }
}
