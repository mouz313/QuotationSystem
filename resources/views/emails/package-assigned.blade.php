<x-mail::message>
# Package Assigned

Hello {{ $userName }},

A new package has been assigned to your account:

**{{ $packageName }}** — ${{ number_format($packagePrice, 2) }}

You now have access to all features included in this package.

<x-mail::button :url="url('/dashboard')">
View Dashboard
</x-mail::button>
</x-mail::message>
