<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quotation $quotation,
        public string $oldStatus,
        public string $newStatus,
        public ?string $notes,
        public string $changedByName,
        public string $recipientType,
    ) {}

    public function envelope(): Envelope
    {
        $label = match ($this->newStatus) {
            'accepted'         => 'Accepted',
            'declined'         => 'Declined',
            'change_requested' => 'Change Requested',
            'sent'             => 'Sent',
            'opened'           => 'Opened',
            default            => ucfirst($this->newStatus),
        };

        return new Envelope(
            subject: "Quotation {$this->quotation->quote_number} - {$label}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quotation-status',
            with: [
                'quoteNumber'  => $this->quotation->quote_number,
                'oldStatus'    => $this->oldStatus,
                'newStatus'    => $this->newStatus,
                'notes'        => $this->notes,
                'changedByName' => $this->changedByName,
                'recipientType' => $this->recipientType,
                'grandTotal'   => number_format($this->quotation->grand_total, 2),
                'currency'     => $this->quotation->currency_symbol,
                'clientName'   => $this->quotation->client->name,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
