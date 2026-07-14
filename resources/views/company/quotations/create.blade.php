@extends('layouts.app')
@section('title', 'Create Quotation')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create Quotation</h1>
    <p class="text-sm text-gray-500">Fill in the details to generate a new quotation</p>
</div>
<form method="POST" action="/quotations" id="quoteForm" class="space-y-6">
    @csrf
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Quotation Details</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client *</label>
                <select name="client_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">Select a client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }} ({{ $client->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                <select name="currency_id" id="currencySelect" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">Select currency</option>
                    @foreach($currencies as $cur)
                        <option value="{{ $cur->id }}" data-symbol="{{ $cur->symbol }}" {{ old('currency_id', $currencies->where('is_default')->first()?->id) == $cur->id ? 'selected' : '' }}>
                            {{ $cur->symbol }} {{ $cur->code }} — {{ $cur->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date *</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Line Items</h2>
            <button type="button" onclick="addRow()" class="px-3 py-1 text-xs bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200">+ Add Item</button>
        </div>
        <div id="items-container" class="space-y-3">
            <div class="item-row grid grid-cols-12 gap-2 items-end">
                <div class="col-span-4">
                    <label class="block text-xs text-gray-500 mb-1">Title *</label>
                    <input type="text" name="items[0][item_title]" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="col-span-3">
                    <label class="block text-xs text-gray-500 mb-1">Description</label>
                    <input type="text" name="items[0][item_description]" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="col-span-1">
                    <label class="block text-xs text-gray-500 mb-1">Qty *</label>
                    <input type="number" name="items[0][quantity]" min="1" value="1" required onchange="calcRow(this)" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Unit Price *</label>
                    <input type="number" name="items[0][unit_price]" step="0.01" min="0" value="0" required onchange="calcRow(this)" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="col-span-1">
                    <label class="block text-xs text-gray-500 mb-1">Subtotal</label>
                    <div class="px-3 py-2 text-sm text-gray-600 row-subtotal">$0.00</div>
                </div>
                <div class="col-span-1">
                    <button type="button" onclick="this.closest('.item-row').remove(); recalc()" class="px-2 py-2 text-red-500 hover:text-red-700 text-sm">&times;</button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Terms & Conditions</label>
                <textarea name="terms_conditions" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Payment due within 14 days...">{{ old('terms_conditions') }}</textarea>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal:</span><span id="gross-total" class="font-medium">$0.00</span></div>

                {{-- Tax selector --}}
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500 w-20">Tax:</span>
                        <select name="tax_id" id="taxSelect" onchange="onTaxChange()" class="flex-1 px-2 py-1 border rounded text-sm outline-none">
                            <option value="">No tax</option>
                            @foreach($taxes as $tax)
                                <option value="{{ $tax->id }}" data-percentage="{{ $tax->percentage }}" {{ old('tax_id') == $tax->id ? 'selected' : '' }}>
                                    {{ $tax->name }} ({{ $tax->percentage }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="tax_id" id="taxIdHidden" value="{{ old('tax_id') }}">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500 w-20">Tax (%):</span>
                        <input type="number" name="tax_percentage" id="taxPercentage" step="0.01" min="0" max="100" value="{{ old('tax_percentage', 0) }}" onchange="recalc()" class="w-20 px-2 py-1 border rounded text-sm outline-none">
                        <span id="tax-amount" class="text-gray-600">$0.00</span>
                    </div>
                    {{-- Realtime add tax --}}
                    <div class="pt-1">
                        <button type="button" onclick="toggleAddTax()" id="addTaxToggle" class="text-xs text-indigo-600 hover:text-indigo-800 underline">+ Can't find your tax? Add it here</button>
                        <div id="addTaxForm" class="hidden mt-2 p-3 bg-gray-50 rounded-lg border space-y-2">
                            <div class="flex gap-2">
                                <input type="text" id="newTaxName" placeholder="Tax name" class="flex-1 px-2 py-1 border rounded text-sm outline-none">
                                <input type="number" id="newTaxPercent" placeholder="%" min="0" max="100" step="0.01" class="w-20 px-2 py-1 border rounded text-sm outline-none">
                                <button type="button" onclick="saveNewTax()" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">Save</button>
                            </div>
                            <div id="newTaxError" class="text-xs text-red-500 hidden"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Discount ($):</span>
                    <input type="number" name="discount_amount" step="0.01" min="0" value="{{ old('discount_amount', 0) }}" onchange="recalc()" class="w-24 px-2 py-1 border rounded text-sm outline-none">
                </div>
                <div class="border-t pt-2 flex justify-between"><span class="font-semibold">Grand Total:</span><span id="grand-total" class="text-xl font-bold text-indigo-600">$0.00</span></div>
            </div>
        </div>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Create Quotation</button>
        <a href="/quotations" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>

<script>
let rowIndex = 1;

function getSymbol() {
    const sel = document.getElementById('currencySelect');
    const opt = sel.options[sel.selectedIndex];
    return opt?.dataset?.symbol || '$';
}

function fmt(val) {
    return getSymbol() + parseFloat(val).toFixed(2);
}

function addRow() {
    const container = document.getElementById('items-container');
    const html = `<div class="item-row grid grid-cols-12 gap-2 items-end">
        <div class="col-span-4"><input type="text" name="items[${rowIndex}][item_title]" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Item title"></div>
        <div class="col-span-3"><input type="text" name="items[${rowIndex}][item_description]" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Description"></div>
        <div class="col-span-1"><input type="number" name="items[${rowIndex}][quantity]" min="1" value="1" required onchange="calcRow(this)" class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
        <div class="col-span-2"><input type="number" name="items[${rowIndex}][unit_price]" step="0.01" min="0" value="0" required onchange="calcRow(this)" class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
        <div class="col-span-1"><div class="px-3 py-2 text-sm text-gray-600 row-subtotal">${fmt(0)}</div></div>
        <div class="col-span-1"><button type="button" onclick="this.closest('.item-row').remove(); recalc()" class="px-2 py-2 text-red-500 hover:text-red-700 text-sm">&times;</button></div>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    rowIndex++;
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
    document.getElementById('tax-amount').textContent = fmt(taxAmt);
    document.getElementById('grand-total').textContent = fmt(grand);
}

function onTaxChange() {
    const sel = document.getElementById('taxSelect');
    const opt = sel.options[sel.selectedIndex];
    const pct = opt?.dataset?.percentage || 0;
    document.getElementById('taxPercentage').value = pct;
    document.getElementById('taxIdHidden').value = sel.value;
    recalc();
}

function toggleAddTax() {
    const form = document.getElementById('addTaxForm');
    form.classList.toggle('hidden');
}

function saveNewTax() {
    const name = document.getElementById('newTaxName').value.trim();
    const pct = document.getElementById('newTaxPercent').value;
    const errEl = document.getElementById('newTaxError');

    if (!name || !pct) {
        errEl.textContent = 'Name and percentage are required.';
        errEl.classList.remove('hidden');
        return;
    }

    errEl.classList.add('hidden');

    fetch('/api/v1/taxes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ name: name, percentage: pct }),
    })
    .then(res => {
        if (!res.ok) return res.json().then(d => { throw d; });
        return res.json();
    })
    .then(data => {
        const sel = document.getElementById('taxSelect');
        const opt = new Option(data.name + ' (' + data.percentage + '%)', data.id, true, true);
        opt.dataset.percentage = data.percentage;
        sel.add(opt);

        document.getElementById('taxIdHidden').value = data.id;
        document.getElementById('taxPercentage').value = data.percentage;
        document.getElementById('newTaxName').value = '';
        document.getElementById('newTaxPercent').value = '';
        document.getElementById('addTaxForm').classList.add('hidden');
        recalc();
    })
    .catch(err => {
        const msg = err.errors?.name?.[0] || err.message || 'Failed to create tax.';
        errEl.textContent = msg;
        errEl.classList.remove('hidden');
    });
}

document.getElementById('currencySelect').addEventListener('change', recalc);
</script>
@endsection
