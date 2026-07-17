@props(['status' => null, 'variant' => null, 'class' => ''])

@php
    $badgeClass = $class;
    if ($status && !$variant) {
        $badgeClass .= ' badge-' . $status;
    }
@endphp

<span class="badge {{ $badgeClass }}" {{ $attributes->merge() }}>{{ $slot }}</span>
