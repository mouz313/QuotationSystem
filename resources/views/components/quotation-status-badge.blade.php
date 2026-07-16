@props(['status'])

@php
    $badge = match($status) {
        'draft' => 'bg-gray-100 text-gray-600',
        'sent' => 'bg-blue-100 text-blue-700',
        'opened' => 'bg-amber-100 text-amber-700',
        'change_requested' => 'bg-purple-100 text-purple-700',
        'accepted' => 'bg-emerald-100 text-emerald-700',
        'declined' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-600',
    };
    $label = ucfirst(str_replace('_', ' ', $status));
@endphp
<span {{ $attributes->merge(['class' => "px-2 py-1 text-xs rounded-full font-semibold $badge"]) }}>{{ $label }}</span>
