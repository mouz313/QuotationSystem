<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReviewedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quotation $quotation,
        public Payment $payment,
        public string $reviewerName,
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->payment->status === 'approved' ? 'Approved' : 'Rejected';
        return new Envelope(
            subject: "Payment {$status} - {$this->quotation->quote_number}",
        );
    }

    public function content(): Content
    {
        $this->quotation->load('user.company');
        $company = $this->quotation->user->company;
        $status = $this->payment->status === 'approved' ? 'Approved' : 'Rejected';

        return new Content(
            view: 'emails.payment-reviewed',
            with: [
                'quoteNumber'  => $this->quotation->quote_number,
                'amount'       => number_format($this->payment->amount, 2),
                'currency'     => $this->quotation->currency_symbol,
                'paymentStatus' => $status,
                'reviewerName' => $this->reviewerName,
                'reviewerNotes' => $this->payment->notes,
                'grandTotal'   => number_format($this->quotation->grand_total, 2),
                'totalPaid'    => number_format($this->quotation->paid_amount ?? 0, 2),
                'company'      => $company,
                'brandColor'   => $company->brand_color ?? '#4f46e5',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
