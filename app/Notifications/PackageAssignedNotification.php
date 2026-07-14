<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $packageName, public float $packagePrice) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Package Assigned')
            ->markdown('emails.package-assigned', [
                'userName' => $notifiable->name,
                'packageName' => $this->packageName,
                'packagePrice' => $this->packagePrice,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
