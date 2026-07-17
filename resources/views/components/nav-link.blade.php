@props(['href', 'active' => null, 'icon' => null])

@php
    $isActive = $active
        ? request()->is($active)
        : request()->is(trim($href, '/'));
@endphp

<a href="{{ $href }}" class="sidebar-link {{ $isActive ? 'active' : '' }}">
    @if($icon)
        <x-icon :name="$icon" />
    @endif
    {{ $slot }}
</a>
