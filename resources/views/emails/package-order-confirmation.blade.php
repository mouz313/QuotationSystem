<x-mail::message>
# Order Confirmed

Hello,

Your package order has been confirmed.

**Order #{{ $order->id }}**
**Package:** {{ $package->name }}
**Amount:** {{ $package->currency_symbol }}{{ number_format($order->amount, 2) }}

Your order is pending approval. You will be notified once it is reviewed by an administrator.

<x-mail::button :url="url('/company/settings')">
View Subscription
</x-mail::button>

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
