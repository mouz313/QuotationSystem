@props(['action' => null, 'placeholder' => 'Search...', 'value' => null, 'name' => 'search'])

<form method="GET" action="{{ $action }}" style="display:flex;gap:.5rem;margin-bottom:1rem;">
    <input
        type="text"
        name="{{ $name }}"
        value="{{ request($name, $value) }}"
        placeholder="{{ $placeholder }}"
        style="flex:1;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;transition:border-color .15s;focus:border-color:var(--brand-500);"
    >
    {{ $slot }}
    <button type="submit" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Search</button>
    @if(request($name))
        <a href="{{ strtok(request()->url(), '?') }}" class="btn btn-ghost" style="color:var(--surface-500);font-size:.8125rem;">Clear</a>
    @endif
</form>
