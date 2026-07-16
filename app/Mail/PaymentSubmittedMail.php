<?php

namespace App\Mail;

use App\Models\ClientUser;
use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quotation $quotation,
        public Payment $payment,
        public ClientUser $clientUser,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment Submitted - {$this->quotation->quote_number}",
        );
    }

    public function content(): Content
    {
        $this->quotation->load('user.company');
        $company = $this->quotation->user->company;

        return new Content(
            view: 'emails.payment-submitted',
            with: [
                'quoteNumber' => $this->quotation->quote_number,
                'amount'      => number_format($this->payment->amount, 2),
                'currency'    => $this->quotation->currency_symbol,
                'clientName'  => $this->clientUser->name,
                'clientEmail' => $this->clientUser->email,
                'notes'       => $this->payment->notes,
                'grandTotal'  => number_format($this->quotation->grand_total, 2),
                'company'     => $company,
                'brandColor'  => $company->brand_color ?? '#4f46e5',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
