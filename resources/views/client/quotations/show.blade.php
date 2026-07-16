@extends('client.layouts.client')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')

@php
    $company = $quotation->user?->company;
    $brandColor = $company?->brand_color ?? '#4f46e5';
    $totalPaid = $quotation->payments->where('status', 'approved')->sum('amount');
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
</style>

{{-- Hero --}}
<div class="q-hero rounded-2xl p-6 sm:p-8 text-white mb-5 relative fade-in">
    <a href="/client/dashboard" class="inline-flex items-center gap-1 text-white/60 hover:text-white text-xs font-medium mb-4 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
        Back to Dashboard
    </a>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">{{ $quotation->quote_number }}</h1>
                <x-quotation-status-badge :status="$quotation->status" />
                @if($quotation->isMilestone())
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-white/20 uppercase tracking-wider">Milestone</span>
                @endif
            </div>
            <p class="text-white/60 text-sm">{{ $company?->name ?? 'N/A' }} &middot; Issued {{ $quotation->issue_date->format('d M Y') }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if(in_array($quotation->status, ['sent', 'opened']))
                <form method="POST" action="/client/quotations/{{ $quotation->id }}/accept" class="inline">
                    @csrf
                    <button class="px-4 py-2 bg-white text-sm font-semibold rounded-lg hover:bg-gray-100 transition flex items-center gap-1.5" style="color:{{ $brandColor }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Accept
                    </button>
                </form>
                <button onclick="toggleForm('declineForm')" class="px-4 py-2 bg-white/15 backdrop-blur text-white text-sm font-semibold rounded-lg hover:bg-white/25 transition border border-white/20">Decline</button>
                <button onclick="toggleForm('changeForm')" class="px-4 py-2 bg-white/15 backdrop-blur text-white text-sm font-semibold rounded-lg hover:bg-white/25 transition border border-white/20">Request Change</button>
            @endif
            @if($quotation->status === 'change_requested')
                <form method="POST" action="/client/quotations/{{ $quotation->id }}/accept" class="inline">
                    @csrf
                    <button class="px-4 py-2 bg-white text-sm font-semibold rounded-lg hover:bg-gray-100 transition" style="color:{{ $brandColor }}">Accept</button>
                </form>
                <button onclick="toggleForm('declineForm')" class="px-4 py-2 bg-white/15 backdrop-blur text-white text-sm font-semibold rounded-lg hover:bg-white/25 transition border border-white/20">Decline</button>
            @endif
        </div>
    </div>
</div>

{{-- Decline Form --}}
<div id="declineForm" class="hidden mb-5 bg-red-50 border border-red-200 rounded-xl p-5 fade-in">
    <form method="POST" action="/client/quotations/{{ $quotation->id }}/decline">
        @csrf
        <label class="block text-sm font-semibold text-red-800 mb-2">Reason (optional)</label>
        <textarea name="reason" rows="2" class="w-full px-4 py-2.5 border border-red-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-400 bg-white" placeholder="Why are you declining?"></textarea>
        <div class="flex gap-2 mt-3">
            <button class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">Confirm Decline</button>
            <button type="button" onclick="toggleForm('declineForm')" class="px-4 py-2 bg-white text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition border border-gray-200">Cancel</button>
        </div>
    </form>
</div>

{{-- Change Request Form --}}
<div id="changeForm" class="hidden mb-5 bg-violet-50 border border-violet-200 rounded-xl p-5 fade-in">
    <form method="POST" action="/client/quotations/{{ $quotation->id }}/request-change">
        @csrf
        <label class="block text-sm font-semibold text-violet-800 mb-2">What changes do you need?</label>
        <textarea name="notes" rows="3" required class="w-full px-4 py-2.5 border border-violet-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-violet-400 bg-white" placeholder="Describe the changes you'd like..."></textarea>
        <div class="flex gap-2 mt-3">
            <button class="px-4 py-2 bg-violet-600 text-white text-sm font-semibold rounded-lg hover:bg-violet-700 transition">Submit Request</button>
            <button type="button" onclick="toggleForm('changeForm')" class="px-4 py-2 bg-white text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition border border-gray-200">Cancel</button>
        </div>
    </form>
</div>

{{-- Status Timeline --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5 fade-in" style="animation-delay:.05s">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-800">Status Timeline</h3>
    </div>
    @php
        $steps = ['draft' => 'Draft', 'sent' => 'Sent', 'opened' => 'Opened', 'accepted' => 'Accepted', 'declined' => 'Declined', 'change_requested' => 'Changes'];
        $stepOrder = ['draft','sent','opened','change_requested','accepted','declined'];
        $currentIdx = array_search($quotation->status, $stepOrder);
    @endphp
    <div class="flex gap-0 overflow-x-auto pb-1">
        @foreach($stepOrder as $idx => $step)
            @php
                $isActive = $idx <= $currentIdx && $quotation->status !== 'declined';
                $isCurrent = $step === $quotation->status;
                $logEntry = $quotation->statusLogs->where('to_status', $step)->first();
            @endphp
            <div class="flex-1 min-w-[80px] flex flex-col items-center relative">
                @if($idx > 0)
                <div class="absolute top-3 right-1/2 w-full h-0.5 {{ $isActive ? 'bg-indigo-400' : 'bg-gray-200' }}"></div>
                @endif
                <div class="relative z-10 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] font-bold
                    {{ $isCurrent ? 'bg-indigo-600 ring-4 ring-indigo-100 scale-110' : ($isActive ? 'bg-indigo-400' : 'bg-gray-300') }}">
                    @if($isActive && !$isCurrent)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    @else
                        {{ $idx + 1 }}
                    @endif
                </div>
                <span class="text-[10px] font-semibold mt-1.5 text-center {{ $isCurrent ? 'text-indigo-700' : ($isActive ? 'text-gray-600' : 'text-gray-400') }}">{{ $steps[$step] }}</span>
                @if($logEntry)
                    <span class="text-[9px] text-gray-400 mt-0.5">{{ $logEntry->created_at->format('d M') }}</span>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left Column --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Items Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden fade-in" style="animation-delay:.1s">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-800">{{ $quotation->isMilestone() ? 'Milestones' : 'Line Items' }}</h2>
                </div>
                <span class="text-[11px] text-gray-400">{{ $quotation->items()->count() }} {{ $quotation->isMilestone() ? 'milestones' : 'items' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-2.5 text-left font-semibold">#</th>
                            <th class="px-5 py-2.5 text-left font-semibold">Item</th>
                            <th class="px-5 py-2.5 text-left font-semibold hidden sm:table-cell">Description</th>
                            @if($quotation->isMilestone())
                            <th class="px-5 py-2.5 text-left font-semibold">Start</th>
                            <th class="px-5 py-2.5 text-left font-semibold">End</th>
                            @endif
                            <th class="px-5 py-2.5 text-right font-semibold">Qty</th>
                            <th class="px-5 py-2.5 text-right font-semibold">Price</th>
                            <th class="px-5 py-2.5 text-right font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ $item->item_title }}</td>
                            <td class="px-5 py-3 text-gray-500 text-xs hidden sm:table-cell">{{ $item->item_description ?? '-' }}</td>
                            @if($quotation->isMilestone())
                            <td class="px-5 py-3 text-xs text-gray-500">{{ $item->start_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-500">{{ $item->end_date?->format('d M Y') ?? '-' }}</td>
                            @endif
                            <td class="px-5 py-3 text-right text-gray-600">{{ $item->quantity }}</td>
                            <td class="px-5 py-3 text-right text-gray-600">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-800">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Totals --}}
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                <div class="flex justify-end">
                    <div class="w-64 space-y-1.5 text-sm">
                        <div class="flex justify-between"><span class="text-gray-400">Subtotal</span><span class="font-medium text-gray-700">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
                        @if($quotation->discount_amount > 0)
                        <div class="flex justify-between"><span class="text-gray-400">Discount</span><span class="text-red-500 font-medium">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                        @endif
                        @if($quotation->tax_percentage > 0)
                        <div class="flex justify-between"><span class="text-gray-400">Tax ({{ $quotation->tax_percentage }}%)</span><span class="font-medium text-gray-700">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between border-t border-gray-200 pt-2">
                            <span class="font-bold text-gray-800">Grand Total</span>
                            <span class="font-bold text-lg" style="color:{{ $brandColor }}">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Milestone Progress --}}
        @if($quotation->isMilestone())
        @php $progress = $quotation->milestone_progress; @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 fade-in" style="animation-delay:.15s">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-800">Milestone Progress</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $progress['percent'] == 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }}">{{ $progress['percent'] }}%</span>
                    <span class="text-[11px] text-gray-400">{{ $progress['completed'] }}/{{ $progress['total'] }}</span>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 mb-5">
                <div class="h-2.5 rounded-full transition-all duration-700 ease-out" style="width:{{ $progress['percent'] }}%;background:{{ $brandColor }}"></div>
            </div>
            <div class="space-y-2.5">
                @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                @php
                    $itemPaid = $item->paid_amount;
                    $itemRemaining = max(0, $item->subtotal - $itemPaid);
                    $itemFullyPaid = $itemPaid >= $item->subtotal;
                    $itemPercent = $item->subtotal > 0 ? min(100, round(($itemPaid / $item->subtotal) * 100)) : 0;
                @endphp
                <div class="p-3.5 rounded-lg border transition-all {{ $itemFullyPaid ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $itemFullyPaid ? 'bg-emerald-500 text-white' : 'bg-gray-200' }}">
                                @if($itemFullyPaid)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <span class="text-[9px] font-bold text-gray-500">{{ $loop->iteration }}</span>
                                @endif
                            </div>
                            <span class="text-sm font-semibold {{ $itemFullyPaid ? 'text-emerald-800' : 'text-gray-800' }}">{{ $item->item_title }}</span>
                        </div>
                        @if($itemFullyPaid)
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 uppercase">Paid</span>
                        @endif
                    </div>
                    @if($item->start_date && $item->end_date)
                    <div class="flex items-center gap-1.5 text-[11px] text-gray-400 mb-2 ml-7">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $item->start_date->format('d M') }} — {{ $item->end_date->format('d M') }} &middot; {{ $item->duration_days }}d
                    </div>
                    @endif
                    <div class="ml-7">
                        <div class="w-full bg-gray-100 rounded-full h-1.5 mb-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-500 {{ $itemFullyPaid ? 'bg-emerald-500' : 'bg-indigo-400' }}" style="width:{{ $itemPercent }}%"></div>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Paid: <strong class="{{ $itemFullyPaid ? 'text-emerald-600' : 'text-gray-600' }}">{{ $quotation->currency_symbol }}{{ number_format($itemPaid, 2) }}</strong></span>
                            <span class="text-gray-400">Total: <strong class="text-gray-600">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</strong></span>
                        </div>
                        @if($itemRemaining > 0)
                        <div class="text-[10px] text-red-500 font-medium mt-1">Remaining: {{ $quotation->currency_symbol }}{{ number_format($itemRemaining, 2) }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Payment Section --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden fade-in" style="animation-delay:.2s">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-800">Payment</h2>
            </div>
            <div class="p-5">
                {{-- Payment stat cards --}}
                <div class="grid grid-cols-3 gap-2.5 mb-5">
                    <div class="text-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                        <p class="text-[9px] uppercase tracking-wider font-bold text-gray-400 mb-1">Status</p>
                        @php
                            $pBadge = match($pStatus) {
                                'paid' => 'text-emerald-600',
                                'partial' => 'text-amber-600',
                                default => 'text-red-500',
                            };
                        @endphp
                        <p class="font-bold text-sm {{ $pBadge }}">{{ ucfirst($pStatus) }}</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                        <p class="text-[9px] uppercase tracking-wider font-bold text-gray-400 mb-1">Paid</p>
                        <p class="font-bold text-sm text-emerald-600">{{ $quotation->currency_symbol }}{{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <div class="text-center p-3 rounded-lg {{ $remaining > 0 ? 'bg-red-50 border border-red-100' : 'bg-emerald-50 border border-emerald-100' }}">
                        <p class="text-[9px] uppercase tracking-wider font-bold text-gray-400 mb-1">{{ $remaining > 0 ? 'Remaining' : 'Status' }}</p>
                        @if($remaining > 0)
                            <p class="font-bold text-sm text-red-500">{{ $quotation->currency_symbol }}{{ number_format($remaining, 2) }}</p>
                        @else
                            <p class="font-bold text-sm text-emerald-600">Fully Paid</p>
                        @endif
                    </div>
                </div>

                @if($totalPaid > 0 && $quotation->paid_at)
                    <p class="text-[11px] text-gray-400 mb-4">Last payment: {{ $quotation->paid_at->format('d M Y') }}</p>
                @endif

                @if($quotation->payment_instructions)
                <div class="p-4 rounded-lg bg-gray-50 border border-gray-100 mb-5">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Payment Instructions</p>
                    <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed">{{ $quotation->payment_instructions }}</p>
                </div>
                @endif

                {{-- Submit Payment --}}
                @if(in_array($quotation->status, ['accepted', 'sent', 'opened']) && $pStatus !== 'paid')
                <button onclick="toggleForm('paymentForm')" class="w-full py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Submit Payment
                </button>

                <div id="paymentForm" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 fade-in">
                    <form method="POST" action="/client/quotations/{{ $quotation->id }}/submit-payment" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        @if($quotation->isMilestone())
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Select Milestone</label>
                            <select name="quotation_item_id" required onchange="updateMilestoneAmount(this)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
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
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Amount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-semibold text-sm">{{ $quotation->currency_symbol }}</span>
                                <input type="number" name="amount" step="0.01" min="0.01" required class="w-full pl-7 pr-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Payment Proof</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-lg p-3 text-center hover:border-indigo-300 transition bg-white">
                                <input type="file" name="proof" accept="image/*,.pdf" class="w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, or PDF. Max 5MB.</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Notes</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white" placeholder="Any details about this payment..."></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition">Submit for Verification</button>
                            <button type="button" onclick="toggleForm('paymentForm')" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- My Payments --}}
                @php $myPayments = $quotation->payments->where('client_user_id', auth('client')->id()); @endphp
                @if($myPayments->count() > 0)
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2.5">My Payment Submissions</h3>
                    <div class="space-y-1.5">
                        @foreach($myPayments as $p)
                        <div class="pay-card flex items-center justify-between p-3 rounded-lg">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ $p->status === 'approved' ? 'bg-emerald-100 text-emerald-600' : ($p->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') }}">
                                    @if($p->status === 'approved')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    @elseif($p->status === 'rejected')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-gray-800">{{ $quotation->currency_symbol }}{{ number_format($p->amount, 2) }}</span>
                                        @if($p->quotationItem)<span class="text-[9px] font-medium text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">{{ $p->quotationItem->item_title }}</span>@endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] text-gray-400">{{ $p->created_at->format('d M Y g:i A') }}</span>
                                        @if($p->proof)<a href="/storage/{{ $p->proof }}" target="_blank" class="text-[10px] text-indigo-600 hover:underline">View Proof</a>@endif
                                    </div>
                                    @if($p->notes)<p class="text-[10px] text-gray-500 mt-0.5">{{ $p->notes }}</p>@endif
                                </div>
                            </div>
                            @php
                                $pBadgeClass = match($p->status) {
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-amber-100 text-amber-700',
                                };
                            @endphp
                            <span class="px-2 py-0.5 text-[9px] font-bold rounded-full {{ $pBadgeClass }} uppercase tracking-wider">{{ $p->status }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Terms --}}
        @if($quotation->terms_conditions)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 fade-in" style="animation-delay:.25s">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Terms & Conditions</h3>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $quotation->terms_conditions }}</p>
        </div>
        @endif
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-5">

        {{-- Revisions --}}
        @if($quotation->revisions->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 fade-in" style="animation-delay:.15s">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Revisions</h3>
                <span class="ml-auto text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $quotation->revisions->count() }}</span>
            </div>
            <div class="space-y-1.5">
                @foreach($quotation->revisions as $rev)
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[11px] font-bold text-gray-700">Revision {{ $loop->iteration }}</span>
                        <span class="text-[10px] text-gray-400">{{ $rev->created_at->format('d M Y') }}</span>
                    </div>
                    <p class="text-[11px] text-gray-500">Total: <strong>{{ $quotation->currency_symbol }}{{ number_format($rev->grand_total, 2) }}</strong></p>
                    @if($rev->notes)<p class="text-[10px] text-gray-400 mt-1 leading-relaxed">{{ $rev->notes }}</p>@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Details --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 fade-in" style="animation-delay:.2s">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Details</h3>
            </div>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">Client</span><span class="font-medium text-gray-700">{{ $quotation->client->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Currency</span><span class="font-medium text-gray-700">{{ $quotation->currency?->code ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Issued</span><span class="font-medium text-gray-700">{{ $quotation->issue_date->format('d M Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Expiry</span><span class="font-medium text-gray-700">{{ $quotation->expiry_date?->format('d M Y') ?? 'N/A' }}</span></div>
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
