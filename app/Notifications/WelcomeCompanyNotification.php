<?php
namespace App\Notifications;

use App\Mail\CompanyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeCompanyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $companyName) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage();
        $mail->subject('Welcome to QuotationSystem');

        return $mail->markdown('emails.welcome', [
            'companyName' => $this->companyName,
            'userName' => $notifiable->name,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
