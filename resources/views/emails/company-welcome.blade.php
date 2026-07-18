<x-mail::message>
# Welcome, {{ $userName }}!

Your company **{{ $companyName }}** has been created on QuotationSystem.

You've been assigned a **Free plan** to get you started. You can log in using the button below to start creating quotations for your clients.

<x-mail::button :url="url('/login')">
Login to Dashboard
</x-mail::button>

**Login Email:** {{ $email }}<br>
**Password:** Use the password you set during registration.

If you have any questions, feel free to reach out.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
