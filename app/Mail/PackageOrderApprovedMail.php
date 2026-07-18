<?php

namespace App\Mail;

use App\Models\Package;
use App\Models\PackageOrder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PackageOrderApprovedMail extends Mailable
{
    public function __construct(
        public PackageOrder $order,
        public Package $package,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Package Order #{$this->order->id} Has Been Approved",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.package-order-approved',
        );
    }
}
