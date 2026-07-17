<?php

namespace App\Mail;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quotation $quotation,
        public string $senderName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment Reminder - {$this->quotation->quote_number}",
        );
    }

    public function content(): Content
    {
        $this->quotation->load('user.company');
        $company = $this->quotation->user->company;

        $remaining = $this->quotation->grand_total - ($this->quotation->paid_amount ?? 0);

        return new Content(
            view: 'emails.payment-reminder',
            with: [
                'quoteNumber' => $this->quotation->quote_number,
                'clientName'  => $this->quotation->client->name,
                'grandTotal'  => number_format($this->quotation->grand_total, 2),
                'totalPaid'   => number_format($this->quotation->paid_amount ?? 0, 2),
                'remaining'   => number_format($remaining, 2),
                'currency'    => $this->quotation->currency_symbol,
                'company'     => $company,
                'brandColor'  => $company->brand_color ?? '#4f46e5',
                'senderName'  => $this->senderName,
            ],
        );
    }

    public function attachments(): array
    {
        $this->quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);
        $company = $this->quotation->user->company;
        $pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation', 'company'));
        $pdf->setOption('isRemoteEnabled', true);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromRaw($pdf->output())
                ->as($this->quotation->quote_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
