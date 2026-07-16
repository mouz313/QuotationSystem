@extends('layouts.app')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/quotations" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $quotation->quote_number }}</h1>
                    <x-quotation-status-badge :status="$quotation->status" class="text-xs" />
                    @if($quotation->isMilestone())
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-violet-100 text-violet-700">Milestone</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-0.5">Issued {{ $quotation->issue_date->format('M d, Y') }} · Expires {{ $quotation->expiry_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    <div class="flex gap-1.5 mt-4 flex-wrap">
        @if(in_array($quotation->status, ['draft', 'change_requested']))
            <a href="/quotations/{{ $quotation->id }}/edit" class="inline-flex items-center gap-1.5 px-3.5 py-2 {{ $quotation->status === 'change_requested' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-amber-600 hover:bg-amber-700' }} text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                @if($quotation->status === 'change_requested') Amend Quotation @else Edit @endif
            </a>
        @endif
        <a href="/quotations/{{ $quotation->id }}/preview" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Preview
        </a>
        <a href="/quotations/{{ $quotation->id }}/pdf" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            PDF
        </a>
        <form method="POST" action="/quotations/{{ $quotation->id }}/send-email" class="inline">
            @csrf
            <button class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Send to Client
            </button>
        </form>
        <form method="POST" action="/quotations/{{ $quotation->id }}/clone" class="inline">
            @csrf
            <button class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Clone
            </button>
        </form>
        @if($quotation->status === 'draft')
            <form method="POST" action="/quotations/{{ $quotation->id }}/status" class="inline">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="sent">
                <button class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Mark Sent
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-6">
                @php $company = $quotation->user->company; @endphp
                <div class="flex items-start justify-between mb-6 pb-5 border-b border-gray-100">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 font-bold text-lg flex-shrink-0">
                            {{ $company ? substr($company->name, 0, 2) : 'NA' }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $company?->name ?? 'N/A' }}</div>
                            @if($company && $company->email)<div class="text-xs text-gray-500">{{ $company->email }}</div>@endif
                            @if($company && $company->phone)<div class="text-xs text-gray-500">{{ $company->phone }}</div>@endif
                        </div>
                    </div>
                    <div class="text-right text-xs text-gray-400">
                        <div>Currency: <span class="font-medium text-gray-700">{{ $quotation->currency?->code ?? 'N/A' }}</span></div>
                    </div>
                </div>
                <div class="mb-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Client</div>
                    <div class="font-medium text-gray-900">{{ $quotation->client->name }}</div>
                    <div class="text-sm text-gray-500">{{ $quotation->client->email }}</div>
                    @if($quotation->client->phone)<div class="text-sm text-gray-500">{{ $quotation->client->phone }}</div>@endif
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-3 font-semibold">Item</th>
                            <th class="pb-3 font-semibold">Description</th>
                            @if($quotation->isMilestone())
                            <th class="pb-3 font-semibold">Start</th>
                            <th class="pb-3 font-semibold">End</th>
                            @endif
                            <th class="pb-3 font-semibold text-right">Qty</th>
                            <th class="pb-3 font-semibold text-right">Price</th>
                            <th class="pb-3 font-semibold text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3.5 font-medium text-gray-900">{{ $item->item_title }}</td>
                            <td class="py-3.5 text-gray-500">{{ $item->item_description ?? '-' }}</td>
                            @if($quotation->isMilestone())
                            <td class="py-3.5 text-gray-700 text-xs">{{ $item->start_date?->format('d M Y') ?? '-' }}</td>
                            <td class="py-3.5 text-gray-700 text-xs">{{ $item->end_date?->format('d M Y') ?? '-' }}</td>
                            @endif
                            <td class="py-3.5 text-right text-gray-700">{{ $item->quantity }}</td>
                            <td class="py-3.5 text-right text-gray-700">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3.5 text-right font-medium text-gray-900">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="max-w-[240px] ml-auto space-y-1.5 text-sm">
                        <div class="flex justify-between text-gray-500"><span>Subtotal</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
                        @if($quotation->discount_amount > 0)
                            <div class="flex justify-between text-red-500"><span>Discount</span><span>-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                        @endif
                        @if($quotation->tax_percentage > 0)
                            <div class="flex justify-between text-gray-500">
                                <span>Tax @if($quotation->tax)({{ $quotation->tax->name }}) @endif({{ $quotation->tax_percentage }}%)</span>
                                <span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-gray-200 text-base font-bold text-gray-900">
                            <span>Grand Total</span>
                            <span class="text-indigo-600">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
                @if($quotation->terms_conditions)
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Terms & Conditions</div>
                        <div class="text-sm text-gray-600 leading-relaxed">{{ $quotation->terms_conditions }}</div>
                    </div>
                @endif
            </div>
        </div>

        @if($quotation->isMilestone())
        @php $progress = $quotation->milestone_progress; @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4.5 h-4.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-800">Milestone Progress</h3>
                </div>
                <span class="text-xs font-medium text-gray-500">{{ $progress['completed'] }}/{{ $progress['total'] }} completed</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 mb-5">
                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress['percent'] }}%"></div>
            </div>
            <div class="space-y-3">
                @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                @php
                    $itemPaid = $item->paid_amount;
                    $itemRemaining = max(0, $item->subtotal - $itemPaid);
                    $itemFullyPaid = $itemPaid >= $item->subtotal;
                    $itemPercent = $item->subtotal > 0 ? min(100, round(($itemPaid / $item->subtotal) * 100)) : 0;
                @endphp
                <div class="p-4 rounded-xl border {{ $itemFullyPaid ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-gray-50' }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            @if($itemFullyPaid)
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <div class="w-4 h-4 rounded-full border-2 border-gray-300"></div>
                            @endif
                            <span class="text-sm font-semibold {{ $itemFullyPaid ? 'text-emerald-800' : 'text-gray-800' }}">{{ $item->item_title }}</span>
                        </div>
                        <span class="text-xs {{ $itemFullyPaid ? 'text-emerald-600' : 'text-gray-500' }}">{{ $itemFullyPaid ? 'Paid' : $itemPercent . '%' }}</span>
                    </div>
                    @if($item->start_date && $item->end_date)
                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">
                        <span>{{ $item->start_date->format('d M Y') }} - {{ $item->end_date->format('d M Y') }}</span>
                        <span class="text-gray-300">|</span>
                        <span>{{ $item->duration_days }} days</span>
                    </div>
                    @endif
                    <div class="w-full bg-white rounded-full h-1.5 mb-2">
                        <div class="h-1.5 rounded-full transition-all {{ $itemFullyPaid ? 'bg-emerald-500' : 'bg-indigo-400' }}" style="width: {{ $itemPercent }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Paid: {{ $quotation->currency_symbol }}{{ number_format($itemPaid, 2) }}</span>
                        <span class="{{ $itemFullyPaid ? 'text-emerald-600' : 'text-gray-700' }}">Total: {{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</span>
                    </div>
                    @if($itemRemaining > 0)
                    <div class="text-xs text-red-500 mt-1">Remaining: {{ $quotation->currency_symbol }}{{ number_format($itemRemaining, 2) }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-semibold text-gray-800">Activity Timeline</h3>
            </div>
            <div class="space-y-0 divide-y divide-gray-50">
                @forelse($quotation->activityLogs as $log)
                    <div class="flex items-center gap-3 py-2.5 text-sm">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full whitespace-nowrap {{ \App\Models\ActivityLog::getActionColor($log->action) }}">{{ $log->action }}</span>
                        <span class="text-gray-600 flex-1">{{ $log->description }}</span>
                        <span class="text-gray-400 text-xs whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                        <span class="text-gray-400 text-xs whitespace-nowrap">{{ $log->user?->name }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No activity recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-5">
        @php
            $pStatus = $quotation->payment_status ?? 'unpaid';
            $totalPaid = $quotation->payments->where('status', 'approved')->sum('amount');
            $remaining = max(0, $quotation->grand_total - $totalPaid);
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <h3 class="text-sm font-semibold text-gray-800">Payment Overview</h3>
            </div>
            <div class="space-y-2.5 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Status</span>
                    @if($pStatus === 'paid')
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Paid</span>
                    @elseif($pStatus === 'partial')
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Partial</span>
                    @else
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Unpaid</span>
                    @endif
                </div>
                <div class="flex justify-between"><span class="text-gray-500">Grand Total</span><span class="font-medium text-gray-900">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span></div>
                @if($totalPaid > 0)
                <div class="flex justify-between"><span class="text-gray-500">Total Paid</span><span class="font-medium text-emerald-600">{{ $quotation->currency_symbol }}{{ number_format($totalPaid, 2) }}</span></div>
                @endif
                @if($remaining > 0)
                <div class="flex justify-between pt-2.5 border-t border-gray-100">
                    <span class="font-semibold text-gray-700">Remaining</span>
                    <span class="font-bold text-red-600">{{ $quotation->currency_symbol }}{{ number_format($remaining, 2) }}</span>
                </div>
                @elseif($totalPaid > 0)
                <div class="flex justify-center pt-2.5 border-t border-gray-100">
                    <span class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Fully Paid
                    </span>
                </div>
                @endif
            </div>
        </div>

        @if($quotation->status !== 'draft')
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <h3 class="text-sm font-semibold text-gray-800">Payment Actions</h3>
            </div>
            <div class="space-y-3">
                <form method="POST" action="/quotations/{{ $quotation->id }}/payment">
                    @csrf @method('PATCH')
                    <select name="payment_status" onchange="togglePaidAmount(this)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="unpaid" {{ $pStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ $pStatus === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ $pStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    <div id="paidAmountField" class="{{ $pStatus === 'partial' ? '' : 'hidden' }} mt-2">
                        <input type="number" name="paid_amount" step="0.01" min="0" value="{{ $quotation->paid_amount ?? 0 }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Amount paid">
                    </div>
                    <button class="w-full mt-2 px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">Update Status</button>
                </form>
                <form method="POST" action="/quotations/{{ $quotation->id }}/payment-instructions" class="pt-3.5 border-t border-gray-100">
                    @csrf @method('PATCH')
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Bank Details (visible to client)</label>
                    <textarea name="payment_instructions" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Bank Name&#10;Account Number&#10;Reference: {{ $quotation->quote_number }}">{{ $quotation->payment_instructions }}</textarea>
                    <button class="mt-1.5 px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors">Save Instructions</button>
                </form>
            </div>
            <script>
            function togglePaidAmount(sel) {
                document.getElementById('paidAmountField').classList.toggle('hidden', sel.value !== 'partial');
            }
            </script>
        </div>
        @endif

        @php $pendingPayments = $quotation->payments->where('status', 'pending'); @endphp
        @if($pendingPayments->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-semibold text-amber-800">Pending Approvals ({{ $pendingPayments->count() }})</h3>
            </div>
            <div class="space-y-3">
                @foreach($pendingPayments as $p)
                <div class="bg-white rounded-lg p-4 border border-amber-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-base font-bold text-gray-900">{{ $quotation->currency_symbol }}{{ number_format($p->amount, 2) }}</span>
                        <span class="text-xs text-gray-400">{{ $p->created_at->format('d M Y g:i A') }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mb-2">by {{ $p->clientUser->name }}</div>
                    @if($p->quotationItem)
                    <div class="text-xs text-indigo-600 mb-2 font-medium">Milestone: {{ $p->quotationItem->item_title }}</div>
                    @endif
                    @if($p->notes)<p class="text-sm text-gray-600 mb-3 bg-gray-50 p-2.5 rounded-lg">{{ $p->notes }}</p>@endif
                    <div class="flex items-center gap-2">
                        @if($p->proof)
                        <a href="/storage/{{ $p->proof }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 border border-gray-200 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View Proof
                        </a>
                        @endif
                        <form method="POST" action="/quotations/{{ $quotation->id }}/payments/{{ $p->id }}/approve" class="inline">
                            @csrf
                            <button class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">Approve</button>
                        </form>
                        <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">Reject</button>
                        <form method="POST" action="/quotations/{{ $quotation->id }}/payments/{{ $p->id }}/reject" class="hidden">
                            @csrf
                            <div class="flex gap-1.5">
                                <input type="text" name="rejection_reason" placeholder="Reason" class="w-28 px-2 py-1.5 border border-gray-200 rounded text-xs outline-none">
                                <button class="px-2 py-1.5 bg-red-700 text-white text-xs font-medium rounded">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @php $reviewedPayments = $quotation->payments->whereIn('status', ['approved', 'rejected']); @endphp
        @if($reviewedPayments->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <h3 class="text-sm font-semibold text-gray-800">Payment History</h3>
            </div>
            <div class="space-y-2">
                @foreach($reviewedPayments as $p)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-gray-900">{{ $quotation->currency_symbol }}{{ number_format($p->amount, 2) }}</span>
                        <span class="text-xs text-gray-500">{{ $p->clientUser->name }}</span>
                        @if($p->quotationItem)<span class="text-xs text-indigo-600 font-medium">({{ $p->quotationItem->item_title }})</span>@endif
                        @if($p->proof)<a href="/storage/{{ $p->proof }}" target="_blank" class="text-indigo-600 hover:underline text-xs">Proof</a>@endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if($p->reviewer)<span class="text-xs text-gray-400">{{ $p->reviewer->name }}</span>@endif
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $p->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($p->status) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($quotation->status === 'change_requested')
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4.5 h-4.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <h3 class="text-sm font-semibold text-purple-800">Change Request</h3>
            </div>
            @php $changeLog = $quotation->statusLogs->firstWhere('to_status', 'change_requested'); @endphp
            @if($changeLog && $changeLog->notes)
                <div class="p-3 bg-white rounded-lg text-sm text-gray-700 mb-3 border border-purple-100">{{ $changeLog->notes }}</div>
            @else
                <p class="text-sm text-gray-600 mb-3">Client has requested changes to this quotation.</p>
            @endif
            <a href="/quotations/{{ $quotation->id }}/edit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Amend Quotation
            </a>
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-semibold text-gray-800">Status History</h3>
            </div>
            <div class="space-y-0">
                @forelse($quotation->statusLogs as $log)
                <div class="flex gap-2.5 py-2.5">
                    <div class="flex flex-col items-center">
                        <div class="w-2 h-2 rounded-full mt-1.5
                            @switch($log->to_status)
                                @case('draft') bg-gray-400 @break
                                @case('sent') bg-blue-500 @break
                                @case('opened') bg-amber-500 @break
                                @case('change_requested') bg-purple-500 @break
                                @case('accepted') bg-emerald-500 @break
                                @case('declined') bg-red-500 @break
                                @default bg-gray-400
                            @endswitch">
                        </div>
                        @if(!$loop->last)<div class="w-px h-full bg-gray-200 ml-0.5 mt-1"></div>@endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $log->to_status)) }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->format('d M Y g:i A') }}</p>
                        @if($log->notes)<p class="text-xs text-gray-500 mt-0.5">{{ $log->notes }}</p>@endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 py-4 text-center">No history yet.</p>
                @endforelse
            </div>
        </div>

        @if($quotation->revisions->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <h3 class="text-sm font-semibold text-gray-800">Revision History</h3>
            </div>
            <div class="space-y-2">
                @foreach($quotation->revisions as $rev)
                <div class="p-3 bg-gray-50 rounded-lg text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Revision {{ $loop->iteration }}</span>
                        <span class="text-xs text-gray-400">{{ $rev->created_at->format('d M Y') }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">Total: {{ $quotation->currency_symbol }}{{ number_format($rev->grand_total, 2) }}</p>
                    @if($rev->notes)<p class="text-xs text-gray-500 mt-0.5">{{ $rev->notes }}</p>@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <h3 class="text-sm font-semibold text-gray-800">Internal Notes</h3>
            </div>
            <div class="space-y-2.5 mb-3.5 max-h-48 overflow-y-auto">
                @forelse($quotation->notes as $note)
                    <div class="text-sm p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-700">{{ $note->note }}</p>
                        <p class="text-xs text-gray-400 mt-1.5">{{ $note->user->name }} · {{ $note->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No notes yet.</p>
                @endforelse
            </div>
            <form method="POST" action="/quotations/{{ $quotation->id }}/notes" class="flex gap-2">
                @csrf
                <input type="text" name="note" placeholder="Add a note..." required
                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">Add</button>
            </form>
        </div>
    </div>
</div>
@endsection
