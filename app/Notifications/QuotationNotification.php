<?php
namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Quotation $quotation, public string $newStatus) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Quotation ' . $this->quotation->quote_number . ' Status Updated')
            ->markdown('emails.quotation-status', [
                'userName' => $notifiable->name,
                'quoteNumber' => $this->quotation->quote_number,
                'status' => $this->newStatus,
                'grandTotal' => number_format($this->quotation->grand_total, 2),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
