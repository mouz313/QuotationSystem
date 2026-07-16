<?php

namespace App\Mail;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendQuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quotation $quotation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Quotation: ' . $this->quotation->quote_number,
        );
    }

    public function content(): Content
    {
        $this->quotation->load('user.company');
        $company = $this->quotation->user->company;

        return new Content(
            view: 'emails.send-quotation',
            with: [
                'quoteNumber' => $this->quotation->quote_number,
                'clientName'  => $this->quotation->client->name,
                'grandTotal'  => number_format($this->quotation->grand_total, 2),
                'currency'    => $this->quotation->currency_symbol,
                'company'     => $company,
                'brandColor'  => $company->brand_color ?? '#4f46e5',
            ],
        );
    }

    public function attachments(): array
    {
        $quotation = $this->quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);

        $pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation'));
        $pdf->setOption('isRemoteEnabled', true);

        return [
            Attachment::fromData(fn () => $pdf->output(), $this->quotation->quote_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
