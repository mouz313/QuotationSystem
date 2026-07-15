<x-mail::message>
# Quotation from {{ config('app.name') }}

Dear {{ $clientName }},

Please find attached quotation **{{ $quoteNumber }}** for your review.

**Grand Total: {{ $currency }}{{ $grandTotal }}**

<x-mail::button :url="url('/')">
Visit Our Website
</x-mail::button>

Thank you for your business!
</x-mail::message>
