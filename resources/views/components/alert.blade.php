@props(['type' => 'success', 'dismissible' => false])

@if(session($type))
    @php
        $styles = match($type) {
            'success' => 'background:var(--emerald-50);border:1px solid var(--emerald-100);color:var(--emerald-600);',
            'error' => 'background:var(--red-50);border:1px solid var(--red-100);color:var(--red-600);',
            'warning' => 'background:var(--amber-50);border:1px solid var(--amber-100);color:var(--amber-600);',
            'info' => 'background:var(--blue-50);border:1px solid var(--blue-100);color:var(--blue-600);',
            default => 'background:var(--gray-50);border:1px solid var(--gray-100);color:var(--gray-600);',
        };
    @endphp
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        style="{{ $styles }} margin-bottom:1rem;padding:1rem;border-radius:.5rem;font-size:.875rem;display:flex;align-items:flex-start;gap:.75rem;"
    >
        @if($type === 'success')
            <x-icon name="check" style="width:1rem;height:1rem;margin-top:.125rem;flex-shrink:0;" />
        @elseif($type === 'error')
            <x-icon name="x" style="width:1rem;height:1rem;margin-top:.125rem;flex-shrink:0;" />
        @elseif($type === 'warning')
            <x-icon name="alert" style="width:1rem;height:1rem;margin-top:.125rem;flex-shrink:0;" />
        @elseif($type === 'info')
            <x-icon name="info" style="width:1rem;height:1rem;margin-top:.125rem;flex-shrink:0;" />
        @endif
        <span style="flex:1;">{{ session($type) }}</span>
        @if($dismissible)
            <button @click="show = false" style="flex-shrink:0;opacity:.5;cursor:pointer;background:none;border:none;color:inherit;padding:0;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.5'">
                <x-icon name="x" style="width:1rem;height:1rem;" />
            </button>
        @endif
    </div>
@endif

@if($type === 'error' && $errors->any())
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        style="background:var(--red-50);border:1px solid var(--red-100);color:var(--red-600);margin-bottom:1rem;padding:1rem;border-radius:.5rem;font-size:.875rem;display:flex;align-items:flex-start;gap:.75rem;"
    >
        <x-icon name="x" style="width:1rem;height:1rem;margin-top:.125rem;flex-shrink:0;" />
        <div style="flex:1;">
            <ul style="list-style-type:disc;padding-left:1.25rem;margin:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @if($dismissible)
            <button @click="show = false" style="flex-shrink:0;opacity:.5;cursor:pointer;background:none;border:none;color:inherit;padding:0;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.5'">
                <x-icon name="x" style="width:1rem;height:1rem;" />
            </button>
        @endif
    </div>
@endif
