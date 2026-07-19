@props(['label' => null, 'name', 'options' => [], 'value' => null, 'placeholder' => '', 'required' => false, 'error' => null])

<div>
    @if($label)
    <label for="{{ $name }}" style="display:block;font-size:.8125rem;font-weight:600;color:var(--gray-700);margin-bottom:.375rem;">
        {{ $label }} @if($required)<span style="color:var(--red-500);">*</span>@endif
    </label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if($required) required @endif
        style="width:100%;padding:.5rem .75rem;border:1px solid {{ $error ? 'var(--red-500)' : 'var(--gray-200)' }};border-radius:.5rem;font-size:.8125rem;color:var(--gray-800);background:#fff;outline:none;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;"
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $key => $label)
            <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
        {{ $slot }}
    </select>
    @if($error)
        <p style="font-size:.75rem;color:var(--red-600);margin-top:.25rem;">{{ $error }}</p>
    @endif
</div>
