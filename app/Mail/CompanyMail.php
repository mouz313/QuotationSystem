<?php
namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $templateKey,
        public array $replacements = [],
    ) {}

    public function envelope(): Envelope
    {
        $template = Setting::getGroup('email_template_' . $this->templateKey);
        $subject = $template['subject'] ?? 'Notification';

        foreach ($this->replacements as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
        }

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $template = Setting::getGroup('email_template_' . $this->templateKey);
        $body = $template['body'] ?? '';

        foreach ($this->replacements as $key => $value) {
            $body = str_replace('{' . $key . '}', $value, $body);
        }

        return new Content(htmlString: $body);
    }

    public function attachments(): array
    {
        return [];
    }
}
