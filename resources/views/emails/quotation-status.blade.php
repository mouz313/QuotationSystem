<x-mail::message>
# Quotation Status Update

Hello {{ $userName }},

Quotation **{{ $quoteNumber }}** has been updated to **{{ ucfirst($status) }}**.

Grand Total: ${{ $grandTotal }}

<x-mail::button :url="url('/quotations/' . $quotation->id ?? '/dashboard')">
View Quotation
</x-mail::button>
</x-mail::message>
