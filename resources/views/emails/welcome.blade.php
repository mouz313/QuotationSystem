<x-mail::message>
# Welcome, {{ $userName }}!

Your company **{{ $companyName }}** has been created on QuotationSystem.

You can now log in and start creating quotations for your clients.

<x-mail::button :url="url('/login')">
Login to Dashboard
</x-mail::button>
</x-mail::message>
