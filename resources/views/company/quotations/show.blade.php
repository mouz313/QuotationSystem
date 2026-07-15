@extends('layouts.app')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $quotation->quote_number }}</h1>
        <p class="text-sm text-gray-500">Issued {{ $quotation->issue_date->format('M d, Y') }}</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="/quotations/{{ $quotation->id }}/preview" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50" target="_blank">Preview</a>
        <a href="/quotations/{{ $quotation->id }}/pdf" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Download PDF</a>
        <form method="POST" action="/quotations/{{ $quotation->id }}/send-email" class="inline">
            @csrf
            <button class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Send to Client</button>
        </form>
        <form method="POST" action="/quotations/{{ $quotation->id }}/clone" class="inline">
            @csrf
            <button class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">Clone</button>
        </form>
        @if($quotation->status === 'draft')
            <form method="POST" action="/quotations/{{ $quotation->id }}/status" class="inline">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="sent">
                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Mark as Sent</button>
            </form>
        @endif
        <a href="/quotations" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            @php $company = $quotation->user->company; @endphp
            <div class="flex items-start justify-between mb-6 pb-4 border-b">
                <div class="flex items-center gap-3">
                    @if($company && $company->logo_url)
                        <img src="{{ $company->logo_url }}" alt="Company logo" class="w-12 h-12 rounded-lg object-cover border">
                    @endif
                    <div>
                        @if($company)<div class="font-semibold text-gray-800">{{ $company->name }}</div>@endif
                        @if($company && $company->email)<div class="text-xs text-gray-500">{{ $company->email }}</div>@endif
                        @if($company && $company->phone)<div class="text-xs text-gray-500">{{ $company->phone }}</div>@endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="mb-2">
                        @if($quotation->status === 'draft')<span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Draft</span>
                        @elseif($quotation->status === 'sent')<span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-700">Sent</span>
                        @elseif($quotation->status === 'accepted')<span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">Accepted</span>
                        @else<span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-700">Declined</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">Expiry: {{ $quotation->expiry_date?->format('M d, Y') ?? 'N/A' }}</div>
                    @if($quotation->currency)
                        <div class="text-sm text-gray-500 mt-1">Currency: <span class="font-medium">{{ $quotation->currency->symbol }} {{ $quotation->currency->code }}</span></div>
                    @endif
                </div>
            </div>
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-500 mb-1">Client</h3>
                <div class="font-medium">{{ $quotation->client->name }}</div>
                <div class="text-sm text-gray-600">{{ $quotation->client->email }}</div>
                @if($quotation->client->phone)<div class="text-sm text-gray-600">{{ $quotation->client->phone }}</div>@endif
            </div>

            <table class="w-full text-sm mb-6">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Item</th><th class="pb-2">Description</th><th class="pb-2 text-right">Qty</th><th class="pb-2 text-right">Price</th><th class="pb-2 text-right">Subtotal</th>
                </tr></thead>
                <tbody>
                @foreach($quotation->items as $item)
                    <tr class="border-b">
                        <td class="py-3 font-medium">{{ $item->item_title }}</td>
                        <td class="py-3 text-gray-600">{{ $item->item_description ?? '-' }}</td>
                        <td class="py-3 text-right">{{ $item->quantity }}</td>
                        <td class="py-3 text-right">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3 text-right font-medium">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="flex justify-end">
                <div class="w-64 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal:</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
                    @if($quotation->discount_amount > 0)
                        <div class="flex justify-between"><span class="text-gray-500">Discount:</span><span class="text-red-600">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                    @endif
                    @if($quotation->tax_percentage > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tax @if($quotation->tax)({{ $quotation->tax->name }}) @endif({{ $quotation->tax_percentage }}%):</span>
                            <span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between border-t pt-2 text-lg font-bold"><span>Grand Total:</span><span class="text-indigo-600">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span></div>
                </div>
            </div>

            @if($quotation->terms_conditions)
                <div class="mt-6 pt-4 border-t">
                    <h4 class="text-sm font-semibold text-gray-500 mb-1">Terms & Conditions</h4>
                    <p class="text-sm text-gray-600">{{ $quotation->terms_conditions }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Activity Timeline</h3>
            @forelse($quotation->activityLogs as $log)
                <div class="flex gap-3 py-2 border-b last:border-0 text-sm">
                    <span class="px-2 py-0.5 text-xs rounded-full {{ \App\Models\ActivityLog::getActionColor($log->action) }}">{{ $log->action }}</span>
                    <span class="text-gray-600 flex-1">{{ $log->description }}</span>
                    <span class="text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</span>
                    <span class="text-gray-400 text-xs">{{ $log->user?->name }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">No activity recorded yet.</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Payment</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Status:</span>
                    @if(($quotation->payment_status ?? 'unpaid') === 'paid')
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Paid</span>
                    @elseif(($quotation->payment_status ?? 'unpaid') === 'partial')
                        <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700">Partial</span>
                    @else
                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">Unpaid</span>
                    @endif
                </div>
                @if($quotation->paid_amount)
                <div class="flex justify-between">
                    <span class="text-gray-500">Paid:</span>
                    <span class="font-medium">{{ $quotation->currency_symbol }}{{ number_format($quotation->paid_amount, 2) }}</span>
                </div>
                @endif
            </div>
            @if($quotation->status !== 'draft')
            <form method="POST" action="/quotations/{{ $quotation->id }}/payment" class="mt-3 space-y-2">
                @csrf @method('PATCH')
                <select name="payment_status" onchange="togglePaidAmount(this)" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                    <option value="unpaid" {{ ($quotation->payment_status ?? 'unpaid') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ ($quotation->payment_status ?? 'unpaid') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ ($quotation->payment_status ?? 'unpaid') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
                <div id="paidAmountField" class="{{ ($quotation->payment_status ?? 'unpaid') === 'partial' ? '' : 'hidden' }}">
                    <input type="number" name="paid_amount" step="0.01" min="0" value="{{ $quotation->paid_amount ?? 0 }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm outline-none" placeholder="Amount paid">
                </div>
                <button class="w-full px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Update Payment</button>
            </form>
            <script>
            function togglePaidAmount(sel) {
                document.getElementById('paidAmountField').classList.toggle('hidden', sel.value !== 'partial');
            }
            </script>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Notes</h3>
            <div class="space-y-3 mb-3 max-h-48 overflow-y-auto">
                @forelse($quotation->notes as $note)
                    <div class="text-sm p-2 bg-gray-50 rounded">
                        <p class="text-gray-700">{{ $note->note }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $note->user->name }} · {{ $note->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No notes yet.</p>
                @endforelse
            </div>
            <form method="POST" action="/quotations/{{ $quotation->id }}/notes" class="flex gap-2">
                @csrf
                <input type="text" name="note" placeholder="Add a note..." required
                    class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none">
                <button class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Add</button>
            </form>
        </div>
    </div>
</div>
@endsection
