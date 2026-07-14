@props(['href', 'active' => null, 'icon' => null])

@php
    $isActive = $active
        ? request()->is($active)
        : request()->is(trim($href, '/'));
@endphp

<a href="{{ $href }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ $isActive ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
    @if($icon)
        <x-icon :name="$icon" />
    @endif
    {{ $slot }}
</a>
