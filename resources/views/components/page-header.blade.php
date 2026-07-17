@props(['title', 'subtitle' => null, 'back' => null])

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        @if($back)
        <a href="{{ $back }}" class="btn btn-ghost btn-sm" style="padding:.35rem;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        @endif
        <div>
            <h1 style="font-size:1.375rem;font-weight:800;color:var(--surface-900);">{{ $title }}</h1>
            @if($subtitle)
                <p style="font-size:.8125rem;color:var(--surface-500);margin-top:.125rem;">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    @if(isset($actions) && $actions->isNotEmpty())
    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
        {{ $actions }}
    </div>
    @endif
</div>
