@extends('client.layouts.client')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')

@php
    $company = $quotation->user?->company;
    $brandColor = $company?->brand_color ?? '#4f46e5';
    $approvedPayments = $quotation->payments->where('status', 'approved');
    $totalPaid = $approvedPayments->sum('amount');
    $remaining = max(0, $quotation->grand_total - $totalPaid);
    $pStatus = $quotation->payment_status ?? 'unpaid';
@endphp

<style>
    .q-hero{background:linear-gradient(135deg, {{ $brandColor }}, {{ $brandColor }}cc);position:relative;overflow:hidden}
    .q-hero::before{content:'';position:absolute;top:-40%;right:-15%;width:400px;height:400px;background:rgba(255,255,255,0.06);border-radius:50%}
    .q-hero::after{content:'';position:absolute;bottom:-30%;left:-8%;width:280px;height:280px;background:rgba(255,255,255,0.04);border-radius:50%}
    @keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    .fade-in{animation:fadeUp .35s ease-out both}
    .pay-card{transition:all .15s}
    .pay-card:hover{background:rgba(99,102,241,.02)}
    .qs-card{background:var(--surface-0);border-radius:.75rem;border:1px solid var(--surface-200);box-shadow:0 1px 3px rgba(0,0,0,.04);overflow:hidden}
    .qs-header{display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:1rem 1.25rem;border-bottom:1px solid var(--surface-100)}
    .qs-icon{width:1.75rem;height:1.75rem;border-radius:.375rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .qs-body{padding:1.25rem}
    .qs-label{font-size:.625rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--surface-400)}
    .qs-value{font-size:.8125rem;font-weight:600;color:var(--surface-800)}
    .qs-muted{font-size:.8125rem;color:var(--surface-500)}
    .qs-row{display:flex;align-items:center;justify-content:space-between;padding:.625rem 0;font-size:.8125rem}
    .qs-stat{flex:1;text-align:center;padding:.75rem;border-radius:.5rem;border:1px solid var(--surface-100);background:var(--surface-50)}
</style>

{{-- Hero --}}
<div class="q-hero rounded-2xl p-6 sm:p-8 text-white mb-5 relative fade-in">
    <a href="/client/dashboard" style="display:inline-flex;align-items:center;gap:.25rem;color:rgba(255,255,255,.6);font-size:.75rem;font-weight:500;margin-bottom:1rem;transition:color .15s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.6)'">
        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
        Back to Dashboard
    </a>
    <div style="position:relative;z-10;display:flex;flex-direction:column;gap:1rem;" class="sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
                <h1 style="font-size:1.5rem;font-weight:800;letter-spacing:-.02em;" class="sm:text-2xl">{{ $quotation->quote_number }}</h1>
                <x-quotation-status-badge :status="$quotation->status" />
                @if($quotation->isMilestone())
                <span style="padding:.125rem .5rem;font-size:.625rem;font-weight:700;border-radius:9999px;background:rgba(255,255,255,.2);text-transform:uppercase;letter-spacing:.05em;">Milestone</span>
                @endif
            </div>
            <p style="color:rgba(255,255,255,.6);font-size:.8125rem;">{{ $company?->name ?? 'N/A' }} &middot; Issued {{ $quotation->issue_date->format('d M Y') }}</p>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <a href="/client/quotations/{{ $quotation->id }}/pdf" style="padding:.5rem 1rem;background:rgba(255,255,255,.15);backdrop-filter:blur(4px);color:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;border:1px solid rgba(255,255,255,.2);transition:background .15s;text-decoration:none;display:inline-flex;align-items:center;gap:.375rem;" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
            </a>
            @if(in_array($quotation->status, ['sent', 'opened']))
                <form method="POST" action="/client/quotations/{{ $quotation->id }}/accept" style="display:inline;">
                    @csrf
                    <button style="padding:.5rem 1rem;background:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;display:flex;align-items:center;gap:.375rem;transition:background .15s;color:{{ $brandColor }};" onmouseover="this.style.background='var(--surface-100)'" onmouseout="this.style.background='white'">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Accept
                    </button>
                </form>
                <button onclick="toggleForm('declineForm')" style="padding:.5rem 1rem;background:rgba(255,255,255,.15);backdrop-filter:blur(4px);color:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;border:1px solid rgba(255,255,255,.2);transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">Decline</button>
                <button onclick="toggleForm('changeForm')" style="padding:.5rem 1rem;background:rgba(255,255,255,.15);backdrop-filter:blur(4px);color:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;border:1px solid rgba(255,255,255,.2);transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">Request Change</button>
            @endif
            @if($quotation->status === 'change_requested')
                <form method="POST" action="/client/quotations/{{ $quotation->id }}/accept" style="display:inline;">
                    @csrf
                    <button style="padding:.5rem 1rem;background:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;transition:background .15s;color:{{ $brandColor }};" onmouseover="this.style.background='var(--surface-100)'" onmouseout="this.style.background='white'">Accept</button>
                </form>
                <button onclick="toggleForm('declineForm')" style="padding:.5rem 1rem;background:rgba(255,255,255,.15);backdrop-filter:blur(4px);color:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;border:1px solid rgba(255,255,255,.2);transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">Decline</button>
            @endif
        </div>
    </div>
</div>

{{-- Decline Form --}}
<div id="declineForm" class="hidden mb-5 fade-in" style="background:var(--danger-50);border:1px solid var(--danger-100);border-radius:.75rem;padding:1.25rem;">
    <form method="POST" action="/client/quotations/{{ $quotation->id }}/decline">
        @csrf
        <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--danger-800);margin-bottom:.5rem;">Reason (optional)</label>
        <textarea name="reason" rows="2" style="width:100%;padding:.625rem .75rem;border:1px solid var(--danger-100);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;resize:vertical;" placeholder="Why are you declining?"></textarea>
        <div style="display:flex;gap:.5rem;margin-top:.75rem;">
            <button style="padding:.5rem 1rem;background:var(--danger-600);color:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;transition:background .15s;" onmouseover="this.style.background='var(--danger-700)'" onmouseout="this.style.background='var(--danger-600)'">Confirm Decline</button>
            <button type="button" onclick="toggleForm('declineForm')" style="padding:.5rem 1rem;background:var(--surface-0);color:var(--surface-600);font-size:.8125rem;font-weight:500;border-radius:.5rem;border:1px solid var(--surface-200);transition:background .15s;" onmouseover="this.style.background='var(--surface-50)'" onmouseout="this.style.background='var(--surface-0)'">Cancel</button>
        </div>
    </form>
</div>

{{-- Change Request Form --}}
<div id="changeForm" class="hidden mb-5 fade-in" style="background:var(--brand-50);border:1px solid var(--brand-200);border-radius:.75rem;padding:1.25rem;">
    <form method="POST" action="/client/quotations/{{ $quotation->id }}/request-change">
        @csrf
        <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--brand-800);margin-bottom:.5rem;">What changes do you need?</label>
        <textarea name="notes" rows="3" required style="width:100%;padding:.625rem .75rem;border:1px solid var(--brand-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;resize:vertical;" placeholder="Describe the changes you'd like..."></textarea>
        <div style="display:flex;gap:.5rem;margin-top:.75rem;">
            <button style="padding:.5rem 1rem;background:var(--brand-600);color:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;transition:background .15s;" onmouseover="this.style.background='var(--brand-700)'" onmouseout="this.style.background='var(--brand-600)'">Submit Request</button>
            <button type="button" onclick="toggleForm('changeForm')" style="padding:.5rem 1rem;background:var(--surface-0);color:var(--surface-600);font-size:.8125rem;font-weight:500;border-radius:.5rem;border:1px solid var(--surface-200);transition:background .15s;" onmouseover="this.style.background='var(--surface-50)'" onmouseout="this.style.background='var(--surface-0)'">Cancel</button>
        </div>
    </form>
</div>

{{-- Status Timeline --}}
<div class="qs-card p-5 mb-5 fade-in" style="animation-delay:.05s;">
    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;">
        <div class="qs-icon" style="background:var(--brand-50);">
            <svg style="width:1rem;height:1rem;color:var(--brand-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="qs-value">Status Timeline</h3>
    </div>
    @php
        $steps = ['draft' => 'Draft', 'sent' => 'Sent', 'opened' => 'Opened', 'accepted' => 'Accepted', 'declined' => 'Declined', 'change_requested' => 'Changes'];
        $stepOrder = ['draft','sent','opened','change_requested','accepted','declined'];
        $currentIdx = array_search($quotation->status, $stepOrder);
    @endphp
    <div style="display:flex;gap:0;overflow-x:auto;padding-bottom:.25rem;">
        @foreach($stepOrder as $idx => $step)
            @php
                $isActive = $idx <= $currentIdx && $quotation->status !== 'declined';
                $isCurrent = $step === $quotation->status;
                $logEntry = $quotation->statusLogs->where('to_status', $step)->first();
            @endphp
            <div style="flex:1;min-width:80px;display:flex;flex-direction:column;align-items:center;position:relative;">
                @if($idx > 0)
                <div style="position:absolute;top:.75rem;right:50%;width:100%;height:2px;background:{{ $isActive ? 'var(--brand-400)' : 'var(--surface-200)' }};"></div>
                @endif
                <div style="position:relative;z-index:10;width:1.5rem;height:1.5rem;border-radius:9999px;display:flex;align-items:center;justify-content:center;color:white;font-size:.625rem;font-weight:700;background:{{ $isCurrent ? 'var(--brand-600)' : ($isActive ? 'var(--brand-400)' : 'var(--surface-300)') }};{{ $isCurrent ? 'box-shadow:0 0 0 4px var(--brand-100);transform:scale(1.1);' : '' }}">
                    @if($isActive && !$isCurrent)
                        <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    @else
                        {{ $idx + 1 }}
                    @endif
                </div>
                <span style="font-size:.625rem;font-weight:600;margin-top:.375rem;text-align:center;color:{{ $isCurrent ? 'var(--brand-700)' : ($isActive ? 'var(--surface-600)' : 'var(--surface-400)') }};">{{ $steps[$step] }}</span>
                @if($logEntry)
                    <span style="font-size:.5625rem;color:var(--surface-400);margin-top:.125rem;">{{ $logEntry->created_at->format('d M') }}</span>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr;gap:1.25rem;" class="lg:grid-cols-3">

    {{-- Left Column --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;" class="lg:col-span-2">

        {{-- Items Table --}}
        <div class="qs-card fade-in" style="animation-delay:.1s;">
            <div class="qs-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="qs-icon" style="background:var(--success-50);">
                        <svg style="width:1rem;height:1rem;color:var(--success-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <span class="qs-value">{{ $quotation->isMilestone() ? 'Milestones' : 'Line Items' }}</span>
                </div>
                <span class="qs-label">{{ $quotation->items()->count() }} {{ $quotation->isMilestone() ? 'milestones' : 'items' }}</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="d-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th class="hidden sm:table-cell">Description</th>
                            @if($quotation->isMilestone())
                            <th>Start</th>
                            <th>End</th>
                            @endif
                            <th style="text-align:right;">Qty</th>
                            <th style="text-align:right;">Price</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                        <tr>
                            <td style="color:var(--surface-400);font-size:.75rem;">{{ $loop->iteration }}</td>
                            <td style="font-weight:600;color:var(--surface-800);">{{ $item->item_title }}</td>
                            <td style="color:var(--surface-500);font-size:.75rem;" class="hidden sm:table-cell">{{ $item->item_description ?? '-' }}</td>
                            @if($quotation->isMilestone())
                            <td style="font-size:.75rem;color:var(--surface-500);">{{ $item->start_date?->format('d M Y') ?? '-' }}</td>
                            <td style="font-size:.75rem;color:var(--surface-500);">{{ $item->end_date?->format('d M Y') ?? '-' }}</td>
                            @endif
                            <td style="text-align:right;color:var(--surface-600);">{{ $item->quantity }}</td>
                            <td style="text-align:right;color:var(--surface-600);">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align:right;font-weight:700;color:var(--surface-800);">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Totals --}}
            <div style="padding:1rem 1.25rem;background:var(--surface-50);border-top:1px solid var(--surface-100);">
                <div style="display:flex;justify-content:flex-end;">
                    <div style="width:16rem;display:flex;flex-direction:column;gap:.375rem;font-size:.8125rem;">
                        <div class="qs-row"><span style="color:var(--surface-400);">Subtotal</span><span style="font-weight:500;color:var(--surface-700);">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
                        @if($quotation->discount_amount > 0)
                        <div class="qs-row"><span style="color:var(--surface-400);">Discount</span><span style="color:var(--danger-500);font-weight:500;">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                        @endif
                        @if($quotation->tax_percentage > 0)
                        <div class="qs-row"><span style="color:var(--surface-400);">Tax ({{ $quotation->tax_percentage }}%)</span><span style="font-weight:500;color:var(--surface-700);">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span></div>
                        @endif
                        <div class="qs-row" style="border-top:1px solid var(--surface-200);padding-top:.5rem;">
                            <span style="font-weight:700;color:var(--surface-800);">Grand Total</span>
                            <span style="font-weight:700;font-size:1.125rem;color:{{ $brandColor }}">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Milestone Progress --}}
        @if($quotation->isMilestone())
        @php $progress = $quotation->milestone_progress; @endphp
        <div class="qs-card p-5 fade-in" style="animation-delay:.15s;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="qs-icon" style="background:var(--brand-50);">
                        <svg style="width:1rem;height:1rem;color:var(--brand-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="qs-value">Milestone Progress</span>
                </div>
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <span style="font-size:.625rem;font-weight:700;padding:.125rem .5rem;border-radius:9999px;background:{{ $progress['percent'] == 100 ? 'var(--success-100)' : 'var(--brand-100)' }};color:{{ $progress['percent'] == 100 ? 'var(--success-700)' : 'var(--brand-700)' }};">{{ $progress['percent'] }}%</span>
                    <span class="qs-label">{{ $progress['completed'] }}/{{ $progress['total'] }}</span>
                </div>
            </div>
            <div style="width:100%;height:.625rem;background:var(--surface-100);border-radius:9999px;margin-bottom:1.25rem;">
                <div style="height:.625rem;border-radius:9999px;transition:width .7s ease-out;width:{{ $progress['percent'] }}%;background:{{ $brandColor }};"></div>
            </div>
            <div style="display:flex;flex-direction:column;gap:.625rem;">
                @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                @php
                    $itemPaid = $item->paid_amount;
                    $itemRemaining = max(0, $item->subtotal - $itemPaid);
                    $itemFullyPaid = $itemPaid >= $item->subtotal;
                    $itemPercent = $item->subtotal > 0 ? min(100, round(($itemPaid / $item->subtotal) * 100)) : 0;
                @endphp
                <div style="padding:.875rem;border-radius:.5rem;border:1px solid {{ $itemFullyPaid ? 'var(--success-100)' : 'var(--surface-200)' }};background:{{ $itemFullyPaid ? 'var(--success-50)' : 'var(--surface-0)' }};transition:all .15s;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <div style="width:1.25rem;height:1.25rem;border-radius:9999px;display:flex;align-items:center;justify-content:center;background:{{ $itemFullyPaid ? 'var(--success-500)' : 'var(--surface-200)' }};color:{{ $itemFullyPaid ? 'white' : 'var(--surface-500)' }};">
                                @if($itemFullyPaid)
                                    <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <span style="font-size:.5625rem;font-weight:700;">{{ $loop->iteration }}</span>
                                @endif
                            </div>
                            <span style="font-size:.8125rem;font-weight:600;color:{{ $itemFullyPaid ? 'var(--success-800)' : 'var(--surface-800)' }};">{{ $item->item_title }}</span>
                        </div>
                        @if($itemFullyPaid)
                            <span style="font-size:.5625rem;font-weight:700;padding:.125rem .5rem;border-radius:9999px;background:var(--success-100);color:var(--success-700);text-transform:uppercase;">Paid</span>
                        @endif
                    </div>
                    @if($item->start_date && $item->end_date)
                    <div style="display:flex;align-items:center;gap:.375rem;font-size:.6875rem;color:var(--surface-400);margin-bottom:.5rem;margin-left:1.75rem;">
                        <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $item->start_date->format('d M') }} — {{ $item->end_date->format('d M') }} &middot; {{ $item->duration_days }}d
                    </div>
                    @endif
                    <div style="margin-left:1.75rem;">
                        <div style="width:100%;height:.375rem;background:var(--surface-100);border-radius:9999px;margin-bottom:.375rem;">
                            <div style="height:.375rem;border-radius:9999px;transition:width .5s;background:{{ $itemFullyPaid ? 'var(--success-500)' : 'var(--brand-400)' }};width:{{ $itemPercent }}%;"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.6875rem;">
                            <span style="color:var(--surface-400);">Paid: <strong style="color:{{ $itemFullyPaid ? 'var(--success-600)' : 'var(--surface-600)' }};">{{ $quotation->currency_symbol }}{{ number_format($itemPaid, 2) }}</strong></span>
                            <span style="color:var(--surface-400);">Total: <strong style="color:var(--surface-600);">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</strong></span>
                        </div>
                        @if($itemRemaining > 0)
                        <div style="font-size:.625rem;color:var(--danger-500);font-weight:500;margin-top:.25rem;">Remaining: {{ $quotation->currency_symbol }}{{ number_format($itemRemaining, 2) }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Payment Section --}}
        <div class="qs-card fade-in" style="animation-delay:.2s;">
            <div class="qs-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="qs-icon" style="background:var(--warning-50);">
                        <svg style="width:1rem;height:1rem;color:var(--warning-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="qs-value">Payment</span>
                </div>
            </div>
            <div class="qs-body">
                {{-- Payment stat cards --}}
                <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:.625rem;margin-bottom:1.25rem;">
                    <div class="qs-stat">
                        <p class="qs-label" style="margin-bottom:.25rem;">Status</p>
                        @php
                            $pBadgeColor = match($pStatus) {
                                'paid' => 'var(--success-600)',
                                'partial' => 'var(--warning-600)',
                                default => 'var(--danger-500)',
                            };
                        @endphp
                        <p style="font-weight:700;font-size:.8125rem;color:{{ $pBadgeColor }};">{{ ucfirst($pStatus) }}</p>
                    </div>
                    <div class="qs-stat" style="background:var(--success-50);border-color:var(--success-100);">
                        <p class="qs-label" style="margin-bottom:.25rem;">Paid</p>
                        <p style="font-weight:700;font-size:.8125rem;color:var(--success-600);">{{ $quotation->currency_symbol }}{{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <div class="qs-stat" style="background:{{ $remaining > 0 ? 'var(--danger-50)' : 'var(--success-50)' }};border-color:{{ $remaining > 0 ? 'var(--danger-100)' : 'var(--success-100)' }};">
                        <p class="qs-label" style="margin-bottom:.25rem;">{{ $remaining > 0 ? 'Remaining' : 'Status' }}</p>
                        @if($remaining > 0)
                            <p style="font-weight:700;font-size:.8125rem;color:var(--danger-500);">{{ $quotation->currency_symbol }}{{ number_format($remaining, 2) }}</p>
                        @else
                            <p style="font-weight:700;font-size:.8125rem;color:var(--success-600);">Fully Paid</p>
                        @endif
                    </div>
                </div>

                @if($totalPaid > 0 && $quotation->paid_at)
                    <p style="font-size:.6875rem;color:var(--surface-400);margin-bottom:1rem;">Last payment: {{ $quotation->paid_at->format('d M Y') }}</p>
                @endif

                @if($quotation->payment_instructions)
                <div style="padding:1rem;border-radius:.5rem;background:var(--surface-50);border:1px solid var(--surface-100);margin-bottom:1.25rem;">
                    <p class="qs-label" style="margin-bottom:.375rem;">Payment Instructions</p>
                    <p style="font-size:.8125rem;color:var(--surface-600);white-space:pre-wrap;line-height:1.625;">{{ $quotation->payment_instructions }}</p>
                </div>
                @endif

                {{-- Submit Payment --}}
                @if(in_array($quotation->status, ['accepted', 'sent', 'opened']) && $pStatus !== 'paid')
                <div style="display:flex;gap:.5rem;margin-bottom:.5rem;">
                    @if(config('services.stripe.key'))
                    <form method="POST" action="/client/quotations/{{ $quotation->id }}/pay-stripe" style="flex:1;">
                        @csrf
                        <button type="submit" style="width:100%;padding:.625rem;background:#635BFF;color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;transition:opacity .15s;display:flex;align-items:center;justify-content:center;gap:.5rem;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                            <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-7.076-2.19L3.37 21.83C5.278 22.966 8.292 24 12.03 24c2.62 0 4.72-.642 6.215-1.866 1.648-1.35 2.477-3.277 2.477-5.738 0-4.17-2.508-5.879-6.746-7.246z"/></svg>
                            Pay with Stripe
                        </button>
                    </form>
                    @endif
                    @if(config('services.paypal.client_id'))
                    <form method="POST" action="/client/quotations/{{ $quotation->id }}/pay-paypal" style="flex:1;">
                        @csrf
                        <button type="submit" style="width:100%;padding:.625rem;background:#0070BA;color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;transition:opacity .15s;display:flex;align-items:center;justify-content:center;gap:.5rem;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                            <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797H9.602c-.536 0-.99.394-1.077.926L7.076 21.337z"/></svg>
                            Pay with PayPal
                        </button>
                    </form>
                    @endif
                </div>
                @if(config('services.stripe.key') || config('services.paypal.client_id'))
                <div style="text-align:center;margin-bottom:1rem;font-size:.6875rem;color:var(--surface-400);">— or pay manually below —</div>
                @endif

                <button onclick="toggleForm('paymentForm')" style="width:100%;padding:.625rem;background:var(--success-600);color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;transition:background .15s;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;align-items:center;justify-content:center;gap:.5rem;" onmouseover="this.style.background='var(--success-700)'" onmouseout="this.style.background='var(--success-600)'">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Submit Payment
                </button>

                <div id="paymentForm" class="hidden mt-4 fade-in" style="padding:1rem;background:var(--surface-50);border-radius:.5rem;border:1px solid var(--surface-200);">
                    <form method="POST" action="/client/quotations/{{ $quotation->id }}/submit-payment" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:.75rem;">
                        @csrf
                        @if($quotation->isMilestone())
                        <div>
                            <label class="qs-label" style="display:block;margin-bottom:.25rem;">Select Milestone</label>
                            <select name="quotation_item_id" required onchange="updateMilestoneAmount(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                                <option value="">Choose a milestone...</option>
                                @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                                    @php $rem = max(0, $item->subtotal - $item->paid_amount); @endphp
                                    @if($rem > 0)
                                    <option value="{{ $item->id }}" data-remaining="{{ number_format($rem, 2) }}">{{ $item->item_title }} — {{ $quotation->currency_symbol }}{{ number_format($rem, 2) }} remaining</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="quotation_item_id" value="">
                        @endif
                        <div>
                            <label class="qs-label" style="display:block;margin-bottom:.25rem;">Amount</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--surface-400);font-weight:600;font-size:.8125rem;">{{ $quotation->currency_symbol }}</span>
                                <input type="number" name="amount" step="0.01" min="0.01" required style="width:100%;padding:.5rem .75rem;padding-left:1.75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="qs-label" style="display:block;margin-bottom:.25rem;">Payment Method</label>
                            <select name="payment_method" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                                <option value="">Select method...</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="online">Online Payment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="qs-label" style="display:block;margin-bottom:.25rem;">Payment Proof</label>
                            <div style="border:2px dashed var(--surface-200);border-radius:.5rem;padding:.75rem;text-align:center;transition:border-color .15s;background:var(--surface-0);" onmouseover="this.style.borderColor='var(--brand-300)'" onmouseout="this.style.borderColor='var(--surface-200)'">
                                <input type="file" name="proof" accept="image/*,.pdf" style="width:100%;font-size:.8125rem;color:var(--surface-500);">
                                <p style="font-size:.625rem;color:var(--surface-400);margin-top:.25rem;">JPG, PNG, or PDF. Max 5MB.</p>
                            </div>
                        </div>
                        <div>
                            <label class="qs-label" style="display:block;margin-bottom:.25rem;">Notes</label>
                            <textarea name="notes" rows="2" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;resize:vertical;" placeholder="Any details about this payment..."></textarea>
                        </div>
                        <div style="display:flex;gap:.5rem;">
                            <button type="submit" style="flex:1;padding:.5rem;background:var(--success-600);color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;transition:background .15s;" onmouseover="this.style.background='var(--success-700)'" onmouseout="this.style.background='var(--success-600)'">Submit for Verification</button>
                            <button type="button" onclick="toggleForm('paymentForm')" style="padding:.5rem 1rem;background:var(--surface-0);border:1px solid var(--surface-200);color:var(--surface-600);font-size:.8125rem;font-weight:500;border-radius:.5rem;transition:background .15s;" onmouseover="this.style.background='var(--surface-50)'" onmouseout="this.style.background='var(--surface-0)'">Cancel</button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- My Payments --}}
                @php $myPayments = $quotation->payments->where('client_user_id', auth('client')->id()); @endphp
                @if($myPayments->count() > 0)
                <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--surface-100);">
                    <h3 class="qs-label" style="margin-bottom:.625rem;">My Payment Submissions</h3>
                    <div style="display:flex;flex-direction:column;gap:.375rem;">
                        @foreach($myPayments as $p)
                        <div class="pay-card" style="display:flex;align-items:center;justify-content:space-between;padding:.75rem;border-radius:.5rem;">
                            <div style="display:flex;align-items:center;gap:.625rem;">
                                <div style="width:1.75rem;height:1.75rem;border-radius:.375rem;display:flex;align-items:center;justify-content:center;background:{{ $p->status === 'approved' ? 'var(--success-100)' : ($p->status === 'rejected' ? 'var(--danger-100)' : 'var(--warning-100)') }};color:{{ $p->status === 'approved' ? 'var(--success-600)' : ($p->status === 'rejected' ? 'var(--danger-600)' : 'var(--warning-600)') }};">
                                    @if($p->status === 'approved')
                                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    @elseif($p->status === 'rejected')
                                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                    @else
                                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div style="display:flex;align-items:center;gap:.5rem;">
                                        <span style="font-weight:700;font-size:.8125rem;color:var(--surface-800);">{{ $quotation->currency_symbol }}{{ number_format($p->amount, 2) }}</span>
                                        @if($p->quotationItem)<span style="font-size:.5625rem;font-weight:500;color:var(--brand-600);background:var(--brand-50);padding:.125rem .375rem;border-radius:.25rem;">{{ $p->quotationItem->item_title }}</span>@endif
                                    </div>
                                    <div style="display:flex;align-items:center;gap:.5rem;margin-top:.125rem;">
                                        <span style="font-size:.625rem;color:var(--surface-400);">{{ $p->created_at->format('d M Y g:i A') }}</span>
                                        @if($p->proof)<a href="/storage/{{ $p->proof }}" target="_blank" class="btn btn-ghost btn-icon" title="View Proof" style="color:var(--brand-600);padding:.125rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>@endif
                                    </div>
                                    @if($p->notes)<p style="font-size:.625rem;color:var(--surface-500);margin-top:.125rem;">{{ $p->notes }}</p>@endif
                                </div>
                            </div>
                            @php
                                $pBadgeStyle = match($p->status) {
                                    'approved' => 'background:var(--success-100);color:var(--success-700)',
                                    'rejected' => 'background:var(--danger-100);color:var(--danger-700)',
                                    default => 'background:var(--warning-100);color:var(--warning-700)',
                                };
                            @endphp
                            <span style="padding:.125rem .5rem;font-size:.5625rem;font-weight:700;border-radius:9999px;text-transform:uppercase;letter-spacing:.05em;{{ $pBadgeStyle }}">{{ $p->status }}</span>
                        </div>
                        @endforeach
                    </div>
                    @if($approvedPayments->count() > 0)
                    <a href="/client/quotations/{{ $quotation->id }}/receipt" style="margin-top:.75rem;width:100%;display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.625rem 1rem;background:var(--brand-600);color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;text-decoration:none;transition:background .15s;box-shadow:0 1px 3px rgba(0,0,0,.08);" onmouseover="this.style.background='var(--brand-700)'" onmouseout="this.style.background='var(--brand-600)'">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download Payment Receipt
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Terms --}}
        @if($quotation->terms_conditions)
        <div class="qs-card p-5 fade-in" style="animation-delay:.25s;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <div class="qs-icon" style="background:var(--surface-100);">
                    <svg style="width:1rem;height:1rem;color:var(--surface-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">Terms & Conditions</h3>
            </div>
            <p style="font-size:.8125rem;color:var(--surface-600);line-height:1.625;white-space:pre-wrap;">{{ $quotation->terms_conditions }}</p>
        </div>
        @endif

        @if($quotation->attachments->count() > 0)
        <div class="qs-card p-5 fade-in" style="animation-delay:.28s;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <div class="qs-icon" style="background:var(--info-50);">
                    <svg style="width:1rem;height:1rem;color:var(--info-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                </div>
                <h3 style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">Attachments</h3>
                <span style="margin-left:auto;font-size:.625rem;font-weight:700;color:var(--surface-400);background:var(--surface-100);padding:.125rem .5rem;border-radius:9999px;">{{ $quotation->attachments->count() }}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:.375rem;">
                @foreach($quotation->attachments as $att)
                <a href="/storage/quotation-attachments/{{ $att->filename }}" target="_blank" style="display:flex;align-items:center;gap:.625rem;padding:.75rem;border-radius:.5rem;border:1px solid var(--surface-100);text-decoration:none;transition:border-color .15s,background .15s;" onmouseover="this.style.borderColor='var(--info-300)';this.style.background='var(--info-50)'" onmouseout="this.style.borderColor='var(--surface-100)';this.style.background='transparent'">
                    <div style="width:2rem;height:2rem;border-radius:.375rem;background:var(--info-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:1rem;height:1rem;color:var(--info-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:.8125rem;font-weight:500;color:var(--surface-700);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $att->original_name }}</p>
                        <p style="font-size:.625rem;color:var(--surface-400);">{{ round($att->size / 1024) }}KB</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Right Sidebar --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Revisions --}}
        @if($quotation->revisions->count() > 0)
        <div class="qs-card p-5 fade-in" style="animation-delay:.15s;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <div class="qs-icon" style="background:var(--info-50);">
                    <svg style="width:1rem;height:1rem;color:var(--info-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <h3 style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">Revisions</h3>
                <span style="margin-left:auto;font-size:.625rem;font-weight:700;color:var(--surface-400);background:var(--surface-100);padding:.125rem .5rem;border-radius:9999px;">{{ $quotation->revisions->count() }}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:.375rem;">
                @foreach($quotation->revisions as $rev)
                <div style="padding:.75rem;background:var(--surface-50);border-radius:.5rem;border:1px solid var(--surface-100);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.25rem;">
                        <span style="font-size:.6875rem;font-weight:700;color:var(--surface-700);">Revision {{ $loop->iteration }}</span>
                        <span style="font-size:.625rem;color:var(--surface-400);">{{ $rev->created_at->format('d M Y') }}</span>
                    </div>
                    <p style="font-size:.6875rem;color:var(--surface-500);">Total: <strong>{{ $quotation->currency_symbol }}{{ number_format($rev->grand_total, 2) }}</strong></p>
                    @if($rev->notes)<p style="font-size:.625rem;color:var(--surface-400);margin-top:.25rem;line-height:1.5;">{{ $rev->notes }}</p>@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Details --}}
        <div class="qs-card p-5 fade-in" style="animation-delay:.2s;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <div class="qs-icon" style="background:var(--brand-50);">
                    <svg style="width:1rem;height:1rem;color:var(--brand-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">Details</h3>
            </div>
            <div style="display:flex;flex-direction:column;gap:.625rem;font-size:.8125rem;">
                <div class="qs-row"><span style="color:var(--surface-400);">Client</span><span style="font-weight:500;color:var(--surface-700);">{{ $quotation->client->name }}</span></div>
                <div class="qs-row"><span style="color:var(--surface-400);">Currency</span><span style="font-weight:500;color:var(--surface-700);">{{ $quotation->currency?->code ?? 'N/A' }}</span></div>
                <div class="qs-row"><span style="color:var(--surface-400);">Issued</span><span style="font-weight:500;color:var(--surface-700);">{{ $quotation->issue_date->format('d M Y') }}</span></div>
                <div class="qs-row"><span style="color:var(--surface-400);">Expiry</span><span style="font-weight:500;color:var(--surface-700);">{{ $quotation->expiry_date?->format('d M Y') ?? 'N/A' }}</span></div>
                @if($quotation->invoice_number)
                <div class="qs-row"><span style="color:var(--surface-400);">Invoice #</span><span style="font-weight:700;color:var(--surface-800);">{{ $quotation->invoice_number }}</span></div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleForm(id) {
    document.getElementById(id).classList.toggle('hidden');
}
function updateMilestoneAmount(select) {
    const opt = select.options[select.selectedIndex];
    const remaining = opt?.dataset?.remaining;
    if (remaining) {
        const amountInput = document.querySelector('input[name="amount"]');
        amountInput.placeholder = remaining;
        amountInput.max = parseFloat(remaining.replace(/,/g, ''));
    }
}
</script>
@endsection
