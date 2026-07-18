<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Company $company,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to QuotationSystem - ' . $this->company->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-welcome',
            with: [
                'userName'    => $this->user->name,
                'companyName' => $this->company->name,
                'email'       => $this->user->email,
            ],
        );
    }
}
