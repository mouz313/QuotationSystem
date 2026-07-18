<x-mail::message>
# Package Expiry Warning

Hello,

Your **{{ $package->name }}** package for **{{ $company->name }}** will expire in **{{ $daysLeft }} day(s)**.

To avoid service interruption, please renew your subscription or contact your administrator.

**Package Details:**
- Package: {{ $package->name }}
- Expires: {{ now()->addDays($daysLeft)->format('d M Y') }}

<x-mail::button :url="url('/company/settings')">
View Subscription
</x-mail::button>

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
