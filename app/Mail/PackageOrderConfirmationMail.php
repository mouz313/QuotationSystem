<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Package;
use App\Models\PackageOrder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PackageOrderConfirmationMail extends Mailable
{
    public function __construct(
        public PackageOrder $order,
        public Package $package,
        public Company $company,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Package Order #{$this->order->id} Confirmed",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.package-order-confirmation',
        );
    }
}
