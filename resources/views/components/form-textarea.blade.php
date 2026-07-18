@props(['label', 'name', 'value' => null, 'placeholder' => '', 'rows' => 3, 'required' => false, 'error' => null])

<div>
    <label for="{{ $name }}" style="display:block;font-size:.8125rem;font-weight:600;color:var(--gray-700);margin-bottom:.375rem;">
        {{ $label }} @if($required)<span style="color:var(--red-500);">*</span>@endif
    </label>
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        style="width:100%;padding:.5rem .75rem;border:1px solid {{ $error ? 'var(--red-500)' : 'var(--gray-200)' }};border-radius:.5rem;font-size:.8125rem;color:var(--gray-800);background:#fff;outline:none;resize:vertical;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);"
    >{{ old($name, $value) }}</textarea>
    @if($error)
        <p style="font-size:.75rem;color:var(--red-600);margin-top:.25rem;">{{ $error }}</p>
    @endif
</div>
