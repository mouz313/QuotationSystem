@extends('layouts.app')
@section('title', 'Available Packages')

@section('content')
<div style="max-width:56rem;margin:0 auto;">
    <div style="margin-bottom:2rem;">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--surface-900);">Packages</h1>
        <p style="font-size:.875rem;color:var(--surface-500);margin-top:.25rem;">Choose a package that best fits your company's needs.</p>
    </div>

    @if($currentPackage)
    <div style="padding:1rem 1.25rem;background:var(--brand-50);border:1px solid var(--brand-200);border-radius:.75rem;margin-bottom:1.5rem;">
        <p style="font-size:.8125rem;font-weight:700;color:var(--brand-800);">Current Package: {{ $currentPackage->package->name }}</p>
        <p style="font-size:.75rem;color:var(--brand-600);margin-top:.125rem;">Expires: {{ $currentPackage->end_date->format('d M Y') }}</p>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(20rem,1fr));gap:1.5rem;">
        @foreach($packages as $pkg)
        <div style="background:var(--surface-0);border:1px solid var(--surface-200);border-radius:1rem;padding:1.75rem;display:flex;flex-direction:column;{{ $currentPackage && $currentPackage->package_id === $pkg->id ? 'border-color:var(--brand-400);box-shadow:0 0 0 2px var(--brand-100);' : '' }}">
            <h3 style="font-size:1.125rem;font-weight:800;color:var(--surface-900);margin-bottom:.25rem;">{{ $pkg->name }}</h3>
            <p style="font-size:.8125rem;color:var(--surface-500);margin-bottom:1rem;flex-grow:1;">{{ $pkg->description }}</p>
            <div style="margin-bottom:1.25rem;">
                <span style="font-size:2rem;font-weight:900;color:var(--surface-900);">{{ $pkg->currency_symbol }}{{ number_format($pkg->price, 2) }}</span>
                <span style="font-size:.8125rem;color:var(--surface-400);">/ {{ $pkg->duration_days }} days</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:.5rem;margin-bottom:1.25rem;font-size:.8125rem;color:var(--surface-600);">
                <div style="display:flex;align-items:center;gap:.5rem;"><svg style="width:1rem;height:1rem;color:var(--success-500);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>{{ $pkg->max_users }} user(s)</div>
                <div style="display:flex;align-items:center;gap:.5rem;"><svg style="width:1rem;height:1rem;color:var(--success-500);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>{{ $pkg->max_clients }} client(s)</div>
                <div style="display:flex;align-items:center;gap:.5rem;"><svg style="width:1rem;height:1rem;color:var(--success-500);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>{{ $pkg->max_quotations }} quotation(s)</div>
            </div>
            @if($currentPackage && $currentPackage->package_id === $pkg->id)
                <div style="padding:.625rem;background:var(--brand-50);color:var(--brand-700);font-size:.8125rem;font-weight:700;border-radius:.5rem;text-align:center;">Current Package</div>
            @elseif($pkg->price == 0)
                <div style="padding:.625rem;background:var(--surface-100);color:var(--surface-500);font-size:.8125rem;font-weight:700;border-radius:.5rem;text-align:center;">Free Package</div>
            @else
                <form method="POST" action="/packages/{{ $pkg->id }}/purchase" style="margin-top:auto;">
                    @csrf
                    <button type="submit" style="width:100%;padding:.625rem;background:var(--brand-600);color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;transition:background .15s;" onmouseover="this.style.background='var(--brand-700)'" onmouseout="this.style.background='var(--brand-600)'">Purchase Now</button>
                </form>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
