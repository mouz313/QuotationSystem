@props(['icon' => 'info', 'title' => 'No data found', 'description' => '', 'action' => null, 'actionLabel' => null])

<div style="text-align:center;padding:3rem 1.5rem;">
    <div style="width:3.5rem;height:3.5rem;margin:0 auto;border-radius:.625rem;background:var(--gray-100);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
        <svg style="width:1.75rem;height:1.75rem;color:var(--gray-400);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            @if($icon === 'quote')
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            @elseif($icon === 'client')
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            @elseif($icon === 'item')
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            @else
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            @endif
        </svg>
    </div>
    <p style="font-size:.875rem;font-weight:600;color:var(--gray-600);">{{ $title }}</p>
    @if($description)
        <p style="font-size:.75rem;color:var(--gray-400);margin-top:.25rem;">{{ $description }}</p>
    @endif
    @if($action && $actionLabel)
        <a href="{{ $action }}" style="display:inline-flex;align-items:center;gap:.375rem;margin-top:1rem;padding:.5rem 1rem;font-size:.8125rem;font-weight:600;color:white;background:var(--brand-600);border-radius:.5rem;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='var(--brand-700)'" onmouseout="this.style.background='var(--brand-600)'">
            {{ $actionLabel }}
        </a>
    @endif
    {{ $slot }}
</div>
