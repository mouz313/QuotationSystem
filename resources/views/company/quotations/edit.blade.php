@extends('layouts.app')
@section('title', 'Edit Quotation')
@section('content')
<div class="mb-6">
    <a href="/quotations/{{ $quotation->id }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to Quotation</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Edit Quotation</h1>
    <p class="text-sm text-gray-500">{{ $quotation->quote_number }}</p>
</div>

<form method="POST" action="/quotations/{{ $quotation->id }}" class="grid grid-cols-3 gap-6">
    @csrf @method('PUT')

    <div class="col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Items</h2>
            <div id="items-container">
                @foreach($quotation->items as $i => $item)
                <div class="item-row bg-gray-50 rounded-xl p-3 mb-3 border">
                    <div class="grid grid-cols-12 gap-2 items-end">
                        <div class="col-span-4">
                            <label class="block text-xs text-gray-500 mb-1">Title *</label>
                            <input type="text" name="items[{{ $i }}][item_title]" value="{{ $item->item_title }}" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs text-gray-500 mb-1">Description</label>
                            <input type="text" name="items[{{ $i }}][item_description]" value="{{ $item->item_description }}" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs text-gray-500 mb-1">Qty</label>
                            <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" min="1" required onchange="calcRow(this)" class="w-full px-3 py-2 border rounded-lg text-sm outline-none text-right">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs text-gray-500 mb-1">Price</label>
                            <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" min="0" required onchange="calcRow(this)" class="w-full px-3 py-2 border rounded-lg text-sm outline-none text-right">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs text-gray-500 mb-1">Total</label>
                            <div class="px-3 py-2 text-sm font-semibold text-gray-700 row-subtotal">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</div>
                        </div>
                        <div class="col-span-1 flex items-end justify-end">
                            <button type="button" onclick="this.closest('.item-row').remove(); recalc()" class="p-2 text-red-400 hover:text-red-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addRow()" class="mt-2 w-full py-3 border-2 border-dashed border-gray-200 rounded-xl text-sm text-gray-400 hover:text-emerald-600 hover:border-emerald-400 transition-all">+ Add Item</button>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h2 class="font-bold text-gray-800 mb-4">Details</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <label class="block text-gray-600 mb-1">Client</label>
                    <select name="client_id" required class="w-full px-3 py-2 border rounded-lg outline-none">
                        @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ $quotation->client_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Currency</label>
                    <select name="currency_id" id="currencySelect" required class="w-full px-3 py-2 border rounded-lg outline-none">
                        @foreach($currencies as $cur)
                        <option value="{{ $cur->id }}" data-symbol="{{ $cur->symbol }}" {{ $quotation->currency_id == $cur->id ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->symbol }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Issue Date</label>
                    <input type="date" name="issue_date" value="{{ $quotation->issue_date->format('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ $quotation->expiry_date?->format('Y-m-d') }}" class="w-full px-3 py-2 border rounded-lg outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Tax</label>
                    <select name="tax_id" class="w-full px-3 py-2 border rounded-lg outline-none">
                        <option value="">No Tax</option>
                        @foreach($taxes as $t)
                        <option value="{{ $t->id }}" data-percentage="{{ $t->percentage }}" {{ $quotation->tax_id == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->percentage }}%)</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="tax_percentage" id="taxPercentage" value="{{ $quotation->tax_percentage }}">
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Discount</label>
                    <input type="number" name="discount_amount" value="{{ $quotation->discount_amount }}" step="0.01" min="0" oninput="recalc()" class="w-full px-3 py-2 border rounded-lg outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Terms</label>
                    <textarea name="terms_conditions" rows="3" class="w-full px-3 py-2 border rounded-lg outline-none text-sm">{{ $quotation->terms_conditions }}</textarea>
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Payment Instructions</label>
                    <textarea name="payment_instructions" rows="3" class="w-full px-3 py-2 border rounded-lg outline-none text-sm" placeholder="Bank details for client payment...">{{ $quotation->payment_instructions }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h2 class="font-bold text-gray-800 mb-3">Summary</h2>
            <div class="space-y-2 text-sm" id="summary">
                @php
                    $gross = $quotation->items->sum(fn($i) => $i->quantity * $i->unit_price);
                    $taxAmt = $gross * ($quotation->tax_percentage / 100);
                    $grand = max(0, ($gross + $taxAmt) - $quotation->discount_amount);
                @endphp
                <div class="flex justify-between"><span class="text-gray-500">Subtotal:</span><span id="gross-total">{{ $quotation->currency_symbol }}{{ number_format($gross, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tax:</span><span id="tax-amount-total">{{ $quotation->currency_symbol }}{{ number_format($taxAmt, 2) }}</span></div>
                <div id="discountRow" class="{{ $quotation->discount_amount > 0 ? '' : 'hidden' }} flex justify-between"><span class="text-gray-500">Discount:</span><span class="text-red-600" id="discount-amount-display">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                <div class="flex justify-between border-t pt-2 text-lg font-bold"><span>Total:</span><span id="grand-total" class="text-indigo-600">{{ $quotation->currency_symbol }}{{ number_format($grand, 2) }}</span></div>
            </div>
        </div>

        <button class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
            @if($quotation->status === 'change_requested')
                Amend & Re-send Quotation
            @else
                Update Quotation
            @endif
        </button>
    </div>
</form>

<script>
let rowIndex = {{ count($quotation->items) }};

function getSymbol() {
    const sel = document.getElementById('currencySelect');
    return sel?.options[sel.selectedIndex]?.dataset?.symbol || '$';
}
function fmt(val) { return getSymbol() + parseFloat(val).toFixed(2); }

function addRow() {
    const container = document.getElementById('items-container');
    const i = rowIndex++;
    container.insertAdjacentHTML('beforeend', `<div class="item-row bg-gray-50 rounded-xl p-3 mb-3 border">
        <div class="grid grid-cols-12 gap-2 items-end">
            <div class="col-span-4"><input type="text" name="items[${i}][item_title]" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" placeholder="Title"></div>
            <div class="col-span-3"><input type="text" name="items[${i}][item_description]" class="w-full px-3 py-2 border rounded-lg text-sm outline-none" placeholder="Description"></div>
            <div class="col-span-1"><input type="number" name="items[${i}][quantity]" min="1" value="1" required onchange="calcRow(this)" class="w-full px-3 py-2 border rounded-lg text-sm outline-none text-right"></div>
            <div class="col-span-2"><input type="number" name="items[${i}][unit_price]" step="0.01" min="0" value="0" required onchange="calcRow(this)" class="w-full px-3 py-2 border rounded-lg text-sm outline-none text-right"></div>
            <div class="col-span-1"><div class="px-3 py-2 text-sm font-semibold text-gray-700 row-subtotal">${fmt(0)}</div></div>
            <div class="col-span-1 flex items-end justify-end">
                <button type="button" onclick="this.closest('.item-row').remove(); recalc()" class="p-2 text-red-400 hover:text-red-600 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </div>
        </div>
    </div>`);
}

function calcRow(el) {
    const row = el.closest('.item-row');
    const qty = parseFloat(row.querySelector('input[name*="quantity"]').value) || 0;
    const price = parseFloat(row.querySelector('input[name*="unit_price"]').value) || 0;
    row.querySelector('.row-subtotal').textContent = fmt(qty * price);
    recalc();
}

function recalc() {
    let gross = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="quantity"]')?.value) || 0;
        const price = parseFloat(row.querySelector('input[name*="unit_price"]')?.value) || 0;
        gross += qty * price;
    });
    const taxPct = parseFloat(document.getElementById('taxPercentage').value) || 0;
    const discount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
    const taxAmt = gross * (taxPct / 100);
    const grand = Math.max(0, (gross + taxAmt) - discount);
    document.getElementById('gross-total').textContent = fmt(gross);
    document.getElementById('tax-amount-total').textContent = fmt(taxAmt);
    document.getElementById('grand-total').textContent = fmt(grand);
    const dr = document.getElementById('discountRow');
    if (discount > 0) { dr.classList.remove('hidden'); document.getElementById('discount-amount-display').textContent = '-' + fmt(discount); }
    else { dr.classList.add('hidden'); }
}

document.querySelector('select[name="tax_id"]')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('taxPercentage').value = opt?.dataset?.percentage || 0;
    recalc();
});
document.getElementById('currencySelect')?.addEventListener('change', recalc);
</script>
@endsection
