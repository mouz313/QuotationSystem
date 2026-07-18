<x-mail::message>
# Order Approved

Hello,

Your package order has been approved and your subscription is now active.

**Order #{{ $order->id }}**
**Package:** {{ $package->name }}
**Amount:** {{ $package->currency_symbol }}{{ number_format($order->amount, 2) }}
**Duration:** {{ $package->duration_days }} days

<x-mail::button :url="url('/company/settings')">
View Dashboard
</x-mail::button>

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
