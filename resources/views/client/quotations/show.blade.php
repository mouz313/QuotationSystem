@extends('client.layouts.client')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="/client/dashboard" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to Dashboard</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $quotation->quote_number }}</h1>
    </div>
    <div class="flex gap-2">
        @if(in_array($quotation->status, ['sent', 'opened']))
            <form method="POST" action="/client/quotations/{{ $quotation->id }}/accept" class="inline">
                @csrf
                <button class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">Accept</button>
            </form>
            <button onclick="document.getElementById('declineForm').classList.toggle('hidden')" class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200">Decline</button>
            <button onclick="document.getElementById('changeForm').classList.toggle('hidden')" class="px-4 py-2 bg-purple-100 text-purple-700 text-sm font-semibold rounded-lg hover:bg-purple-200">Request Change</button>
        @endif
        @if($quotation->status === 'change_requested')
            <form method="POST" action="/client/quotations/{{ $quotation->id }}/accept" class="inline">
                @csrf
                <button class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">Accept</button>
            </form>
            <button onclick="document.getElementById('declineForm').classList.toggle('hidden')" class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200">Decline</button>
        @endif
    </div>
</div>

<div id="declineForm" class="hidden mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
    <form method="POST" action="/client/quotations/{{ $quotation->id }}/decline">
        @csrf
        <label class="block text-sm font-medium text-red-800 mb-1">Reason (optional)</label>
        <textarea name="reason" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm outline-none" placeholder="Why are you declining?"></textarea>
        <div class="flex gap-2 mt-2">
            <button class="px-4 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Confirm Decline</button>
            <button type="button" onclick="this.closest('#declineForm').classList.add('hidden')" class="px-4 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-lg">Cancel</button>
        </div>
    </form>
</div>

<div id="changeForm" class="hidden mb-4 bg-purple-50 border border-purple-200 rounded-xl p-4">
    <form method="POST" action="/client/quotations/{{ $quotation->id }}/request-change">
        @csrf
        <label class="block text-sm font-medium text-purple-800 mb-1">What changes do you need?</label>
        <textarea name="notes" rows="3" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" placeholder="Describe the changes you'd like..."></textarea>
        <div class="flex gap-2 mt-2">
            <button class="px-4 py-1.5 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700">Submit Request</button>
            <button type="button" onclick="this.closest('#changeForm').classList.add('hidden')" class="px-4 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-lg">Cancel</button>
        </div>
    </form>
</div>

<div class="grid grid-cols-3 gap-6">
    {{-- Quotation Details --}}
    <div class="col-span-2 bg-white rounded-xl shadow-sm border p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Quotation Details</h2>
                <p class="text-sm text-gray-500">From: {{ $quotation->user?->company?->name ?? 'N/A' }}</p>
            </div>
            @php
                $badge = match($quotation->status) {
                    'draft' => 'bg-gray-100 text-gray-600',
                    'sent' => 'bg-blue-100 text-blue-700',
                    'opened' => 'bg-amber-100 text-amber-700',
                    'change_requested' => 'bg-purple-100 text-purple-700',
                    'accepted' => 'bg-emerald-100 text-emerald-700',
                    'declined' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $quotation->status)) }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
            <div><span class="text-gray-500">Issue Date:</span> <span class="font-medium">{{ $quotation->issue_date->format('d M Y') }}</span></div>
            <div><span class="text-gray-500">Expiry Date:</span> <span class="font-medium">{{ $quotation->expiry_date?->format('d M Y') ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">Client:</span> <span class="font-medium">{{ $quotation->client->name }}</span></div>
            <div><span class="text-gray-500">Currency:</span> <span class="font-medium">{{ $quotation->currency?->code ?? 'N/A' }}</span></div>
        </div>

        <table class="w-full text-sm mb-4">
            <thead><tr class="border-b text-gray-500 text-xs uppercase"><th class="pb-2 text-left">Item</th><th class="pb-2 text-left">Description</th><th class="pb-2 text-right">Qty</th><th class="pb-2 text-right">Price</th><th class="pb-2 text-right">Total</th></tr></thead>
            <tbody class="divide-y">
                @foreach($quotation->items as $item)
                <tr><td class="py-2 font-medium">{{ $item->item_title }}</td><td class="py-2 text-gray-500">{{ $item->item_description }}</td><td class="py-2 text-right">{{ $item->quantity }}</td><td class="py-2 text-right">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td><td class="py-2 text-right font-medium">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t pt-3 space-y-1 text-sm text-right">
            <div class="flex justify-between"><span class="text-gray-500">Subtotal:</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
            @if($quotation->discount_amount > 0)<div class="flex justify-between"><span class="text-gray-500">Discount:</span><span class="text-red-600">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>@endif
            <div class="flex justify-between"><span class="text-gray-500">Tax ({{ $quotation->tax_percentage }}%):</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span></div>
            <div class="flex justify-between border-t pt-2 text-lg font-bold"><span>Grand Total:</span><span class="text-indigo-600">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span></div>
        </div>

        @if($quotation->terms_conditions)
            <div class="mt-6 p-4 bg-gray-50 rounded-lg text-sm text-gray-600"><strong class="text-gray-800">Terms & Conditions:</strong><br>{{ $quotation->terms_conditions }}</div>
        @endif
    </div>

    {{-- Payment Section --}}
    <div class="col-span-2 bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Payment</h2>
        <div class="grid grid-cols-2 gap-6 text-sm">
            <div>
                <p class="text-gray-500 mb-1">Payment Status</p>
                @php
                    $pBadge = match($quotation->payment_status ?? 'unpaid') {
                        'paid' => 'bg-emerald-100 text-emerald-700',
                        'partial' => 'bg-amber-100 text-amber-700',
                        default => 'bg-red-100 text-red-700',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pBadge }}">{{ ucfirst($quotation->payment_status ?? 'Unpaid') }}</span>
                @php $totalPaid = $quotation->payments->where('status', 'approved')->sum('amount'); @endphp
                @if($totalPaid > 0)
                    <p class="mt-2 text-gray-700">Paid: <strong>{{ $quotation->currency_symbol }}{{ number_format($totalPaid, 2) }}</strong></p>
                @endif
                @php $remaining = max(0, $quotation->grand_total - $totalPaid); @endphp
                @if($remaining > 0)
                    <p class="text-red-600 font-semibold mt-1">Remaining: {{ $quotation->currency_symbol }}{{ number_format($remaining, 2) }}</p>
                @elseif($totalPaid > 0)
                    <p class="text-emerald-600 font-semibold mt-1">Fully Paid</p>
                @endif
                @if($quotation->paid_at)
                    <p class="text-gray-400 text-xs">Last payment on: {{ $quotation->paid_at->format('d M Y') }}</p>
                @endif
            </div>
            @if($quotation->payment_instructions)
            <div>
                <p class="text-gray-500 mb-1">Payment Instructions</p>
                <div class="p-3 bg-gray-50 rounded-lg text-gray-700 whitespace-pre-wrap">{{ $quotation->payment_instructions }}</div>
            </div>
            @endif
        </div>

        @if(in_array($quotation->status, ['accepted', 'sent', 'opened']) && ($quotation->payment_status ?? 'unpaid') !== 'paid')
        <div class="mt-4 pt-4 border-t">
            <button onclick="document.getElementById('paymentForm').classList.toggle('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                Submit Payment
            </button>
            <div id="paymentForm" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                <form method="POST" action="/client/quotations/{{ $quotation->id }}/submit-payment" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Proof (receipt/screenshot)</label>
                        <input type="file" name="proof" accept="image/*,.pdf" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, or PDF. Max 5MB.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm outline-none" placeholder="Any details about this payment..."></textarea>
                    </div>
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">Submit for Verification</button>
                </form>
            </div>
        </div>
        @endif

        @php $myPayments = $quotation->payments->where('client_user_id', auth('client')->id()); @endphp
        @if($myPayments->count() > 0)
        <div class="mt-4 pt-4 border-t">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">My Payment Submissions</h3>
            <div class="space-y-2">
                @foreach($myPayments as $p)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm">
                    <div>
                        <span class="font-medium">{{ $quotation->currency_symbol }}{{ number_format($p->amount, 2) }}</span>
                        @if($p->proof)<a href="/storage/{{ $p->proof }}" target="_blank" class="text-indigo-600 hover:underline ml-2 text-xs">View Proof</a>@endif
                        @if($p->notes)<p class="text-xs text-gray-500 mt-0.5">{{ $p->notes }}</p>@endif
                    </div>
                    @php
                        $pBadge = match($p->status) {
                            'approved' => 'bg-emerald-100 text-emerald-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $pBadge }}">{{ ucfirst($p->status) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        {{-- Status Timeline --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Status Timeline</h3>
            <div class="space-y-3">
                @forelse($quotation->statusLogs as $log)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 rounded-full mt-1.5
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
                        @if(!$loop->last)<div class="w-0.5 h-full bg-gray-200 mt-1"></div>@endif
                    </div>
                    <div class="pb-3">
                        <p class="text-sm font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $log->to_status)) }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->format('d M Y g:i A') }}</p>
                        @if($log->notes)<p class="text-xs text-gray-500 mt-0.5">{{ $log->notes }}</p>@endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No status history yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Revisions --}}
        @if($quotation->revisions->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Revisions</h3>
            <div class="space-y-2">
                @foreach($quotation->revisions as $rev)
                <div class="text-sm p-3 bg-gray-50 rounded-lg">
                    <p class="font-medium text-gray-700">Revision {{ $loop->iteration }}</p>
                    <p class="text-xs text-gray-400">{{ $rev->created_at->format('d M Y g:i A') }}</p>
                    <p class="text-xs text-gray-500">Total: {{ $quotation->currency_symbol }}{{ number_format($rev->grand_total, 2) }}</p>
                    @if($rev->notes)<p class="text-xs text-gray-500 mt-1">{{ $rev->notes }}</p>@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
