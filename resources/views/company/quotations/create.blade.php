@extends('layouts.app')
@section('title', 'Create Quotation')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 text-center lg:text-left">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            New Quotation
        </div>
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Create a New Quotation</h1>
        <p class="text-gray-500 mt-1">Fill in the details below and see a live preview as you go</p>
    </div>

    <form method="POST" action="/quotations" id="quoteForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- SECTION: Quotation Details --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Quotation Details</h2>
                                <p class="text-indigo-200 text-xs">Client, currency, and dates</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Client <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <select name="client_id" required class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white text-sm transition-shadow hover:shadow-sm">
                                        <option value="">Select a client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }} ({{ $client->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Currency <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <select name="currency_id" id="currencySelect" required class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white text-sm transition-shadow hover:shadow-sm">
                                        <option value="">Select currency</option>
                                        @foreach($currencies as $cur)
                                            <option value="{{ $cur->id }}" data-symbol="{{ $cur->symbol }}" {{ old('currency_id', $currencies->where('is_default')->first()?->id) == $cur->id ? 'selected' : '' }}>
                                                {{ $cur->symbol }} {{ $cur->code }} — {{ $cur->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Issue Date <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" required class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition-shadow hover:shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date</label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition-shadow hover:shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION: Line Items --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-white">Line Items</h2>
                                    <p class="text-emerald-200 text-xs">Products or services to include</p>
                                </div>
                            </div>
                            <div>
                                <button type="button" onclick="addRow()" class="px-3 py-1.5 text-xs bg-white text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-all">
                                    + Add Item
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($items->isNotEmpty())
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Your Saved Items</label>
                                <span class="text-xs text-gray-400">{{ $items->count() }} items</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($items as $item)
                                <button type="button" onclick='addFromSavedItem(@json(['title' => $item->title, 'description' => $item->description, 'price' => $item->unit_price]))'
                                    class="text-left p-3 bg-white border border-gray-200 rounded-xl hover:border-emerald-400 hover:shadow-sm hover:bg-emerald-50/50 transition-all group">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-800 group-hover:text-emerald-700 truncate">{{ $item->title }}</div>
                                            @if($item->description)
                                            <div class="text-xs text-gray-400 truncate mt-0.5">{{ $item->description }}</div>
                                            @endif
                                        </div>
                                        <div class="text-sm font-semibold text-emerald-600 whitespace-nowrap">${{ number_format($item->unit_price, 2) }}</div>
                                    </div>
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Items header --}}
                        <div class="hidden md:grid grid-cols-12 gap-2 mb-2 px-1">
                            <div class="col-span-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Item</div>
                            <div class="col-span-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Description</div>
                            <div class="col-span-1 text-xs font-medium text-gray-400 uppercase tracking-wider text-right">Qty</div>
                            <div class="col-span-2 text-xs font-medium text-gray-400 uppercase tracking-wider text-right">Price</div>
                            <div class="col-span-1 text-xs font-medium text-gray-400 uppercase tracking-wider text-right">Total</div>
                            <div class="col-span-1"></div>
                        </div>

                        <div id="items-container" class="space-y-2">
                            <div class="item-row bg-gray-50 rounded-xl p-3 md:p-4 border border-gray-100 transition-all hover:border-gray-200">
                                <div class="grid grid-cols-12 gap-2 items-end">
                                    <div class="col-span-12 md:col-span-4">
                                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Title *</label>
                                        <input type="text" name="items[0][item_title]" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Item title">
                                    </div>
                                    <div class="col-span-8 md:col-span-3">
                                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Description</label>
                                        <input type="text" name="items[0][item_description]" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Description">
                                    </div>
                                    <div class="col-span-2 md:col-span-1">
                                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Qty *</label>
                                        <input type="number" name="items[0][quantity]" min="1" value="1" required onchange="calcRow(this)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-right">
                                    </div>
                                    <div class="col-span-2 md:col-span-2">
                                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Price *</label>
                                        <input type="number" name="items[0][unit_price]" step="0.01" min="0" value="0" required onchange="calcRow(this)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-right">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Total</label>
                                        <div class="px-3 py-2 text-sm font-semibold text-gray-700 row-subtotal">{{ $defaultCurrency?->symbol ?? '$' }}0.00</div>
                                    </div>
                                    <div class="col-span-1 flex items-end justify-end">
                                        <button type="button" onclick="this.closest('.item-row').remove(); recalc()" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="addRow()" class="mt-3 w-full py-3 border-2 border-dashed border-gray-200 rounded-xl text-sm text-gray-400 hover:text-emerald-600 hover:border-emerald-400 hover:bg-emerald-50/50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Another Item
                        </button>
                    </div>
                </div>

                {{-- SECTION: Terms --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Terms & Conditions</h2>
                                <p class="text-amber-200 text-xs">Optional terms for this quotation</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <textarea name="terms_conditions" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition-shadow hover:shadow-sm" placeholder="Payment due within 14 days...">{{ old('terms_conditions', $defaultTerms) }}</textarea>
                    </div>
                    <div class="px-6 pb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Instructions</label>
                        <textarea name="payment_instructions" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="Bank: ...&#10;Account: ...&#10;Reference: Quote #">{{ old('payment_instructions') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Summary & Actions --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden lg:sticky lg:top-6">
                    <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h2 class="text-lg font-semibold text-white">Summary</h2>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        {{-- Tax --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tax</label>
                            <div class="space-y-2">
                                <select name="tax_id" id="taxSelect" onchange="onTaxChange()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">No tax</option>
                                    @foreach($taxes as $tax)
                                        <option value="{{ $tax->id }}" data-percentage="{{ $tax->percentage }}" {{ old('tax_id') == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->name }} ({{ $tax->percentage }}%)
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="tax_id" id="taxIdHidden" value="{{ old('tax_id') }}">
                                <div class="flex items-center gap-2">
                                    <input type="number" name="tax_percentage" id="taxPercentage" step="0.01" min="0" max="100" value="{{ old('tax_percentage', 0) }}" onchange="recalc()" class="w-20 px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500 text-right" placeholder="%">
                                    <span class="text-xs text-gray-400">%</span>
                                    <span id="tax-amount" class="ml-auto text-sm font-medium text-gray-700">$0.00</span>
                                </div>
                            </div>
                            <button type="button" onclick="toggleAddTax()" id="addTaxToggle" class="mt-2 text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Add New Tax
                            </button>
                            <div id="addTaxForm" class="hidden mt-2 p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                                <input type="text" id="newTaxName" placeholder="Tax name" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none">
                                <div class="flex gap-2">
                                    <input type="number" id="newTaxPercent" placeholder="%" min="0" max="100" step="0.01" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none">
                                    <button type="button" onclick="saveNewTax()" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">Save</button>
                                </div>
                                <div id="newTaxError" class="text-xs text-red-500 hidden"></div>
                            </div>
                        </div>

                        {{-- Discount --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Discount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">$</span>
                                <input type="number" name="discount_amount" step="0.01" min="0" value="{{ old('discount_amount', 0) }}" onchange="recalc()" class="w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500 text-right">
                            </div>
                        </div>

                        {{-- Totals --}}
                        <div class="border-t border-gray-100 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span id="gross-total" class="font-medium text-gray-700">$0.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax</span>
                                <span id="tax-amount-total" class="font-medium text-gray-700">$0.00</span>
                            </div>
                            @php $hasDiscount = old('discount_amount', 0) > 0; @endphp
                            <div class="flex justify-between text-sm {{ $hasDiscount ? '' : 'hidden' }}" id="discountRow">
                                <span class="text-gray-500">Discount</span>
                                <span id="discount-amount-display" class="font-medium text-red-500">-$0.00</span>
                            </div>
                            <div class="border-t-2 border-gray-800 pt-3 flex justify-between">
                                <span class="font-bold text-gray-800">Grand Total</span>
                                <span id="grand-total" class="text-xl font-bold text-indigo-600">$0.00</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-gray-100 pt-5 space-y-3">
                            <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Create Quotation
                            </button>
                            <a href="/quotations" class="block w-full py-3 text-center border border-gray-200 text-gray-600 font-medium rounded-xl hover:bg-gray-50 transition-all">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let rowIndex = 1;

function addFromSavedItem(data) {
    addRow();
    const rows = document.querySelectorAll('.item-row');
    const lastRow = rows[rows.length - 1];
    lastRow.querySelector('input[name*="item_title"]').value = data.title;
    lastRow.querySelector('input[name*="item_description"]').value = data.description || '';
    lastRow.querySelector('input[name*="unit_price"]').value = data.price;
    calcRow(lastRow.querySelector('input[name*="quantity"]'));
}

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
    const i = rowIndex;
    const html = `<div class="item-row bg-gray-50 rounded-xl p-3 md:p-4 border border-gray-100 transition-all hover:border-gray-200">
        <div class="grid grid-cols-12 gap-2 items-end">
            <div class="col-span-12 md:col-span-4">
                <label class="block text-xs text-gray-500 mb-1 md:hidden">Title *</label>
                <input type="text" name="items[${i}][item_title]" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Item title">
            </div>
            <div class="col-span-8 md:col-span-3">
                <label class="block text-xs text-gray-500 mb-1 md:hidden">Description</label>
                <input type="text" name="items[${i}][item_description]" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Description">
            </div>
            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs text-gray-500 mb-1 md:hidden">Qty *</label>
                <input type="number" name="items[${i}][quantity]" min="1" value="1" required onchange="calcRow(this)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-right">
            </div>
            <div class="col-span-2 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1 md:hidden">Price *</label>
                <input type="number" name="items[${i}][unit_price]" step="0.01" min="0" value="0" required onchange="calcRow(this)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-right">
            </div>
            <div class="col-span-1">
                <label class="block text-xs text-gray-500 mb-1 md:hidden">Total</label>
                <div class="px-3 py-2 text-sm font-semibold text-gray-700 row-subtotal">${fmt(0)}</div>
            </div>
            <div class="col-span-1 flex items-end justify-end">
                <button type="button" onclick="this.closest('.item-row').remove(); recalc()" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    rowIndex++;
    container.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
    document.getElementById('tax-amount-total').textContent = fmt(taxAmt);
    document.getElementById('grand-total').textContent = fmt(grand);

    const discountRow = document.getElementById('discountRow');
    if (discount > 0) {
        discountRow.classList.remove('hidden');
        document.getElementById('discount-amount-display').textContent = '-' + fmt(discount);
    } else {
        discountRow.classList.add('hidden');
    }
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
