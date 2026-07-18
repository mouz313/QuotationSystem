@props(['title' => null, 'subtitle' => null, 'padding' => true, 'class' => ''])

<div class="d-card {{ $class }}">
    @if($title)
    <div class="d-card-header">
        <div>
            <h3>{{ $title }}</h3>
            @if($subtitle)
                <p style="font-size:.7rem;color:var(--gray-400);margin-top:.125rem;">{{ $subtitle }}</p>
            @endif
        </div>
        @if(isset($actions) && $actions->isNotEmpty())
            <div style="display:flex;align-items:center;gap:.5rem;">{{ $actions }}</div>
        @endif
    </div>
    @endif
    <div @unless($padding) style="padding:0;" @endunless class="{{ $title ? '' : '' }}">
        {{ $slot }}
    </div>
</div>
