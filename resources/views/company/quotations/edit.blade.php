@extends('layouts.app')
@section('title', 'Edit Quotation')
@section('content')

<x-page-header title="Edit Quotation" subtitle="{{ $quotation->quote_number }}" back="/quotations/{{ $quotation->id }}" />

<form method="POST" action="/quotations/{{ $quotation->id }}" enctype="multipart/form-data" class="fade-in">
    @csrf @method('PUT')

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start;">
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <x-card>
                <div class="d-card-header">
                    <h3>Items</h3>
                </div>
                <div style="padding:1.25rem;">
                    <div id="items-container" style="display:flex;flex-direction:column;gap:.75rem;">
                        @foreach($quotation->items as $i => $item)
                        <div class="item-row" style="padding:.75rem 1rem;background:var(--surface-50);border:1px solid var(--surface-100);border-radius:.75rem;">
                            <div style="display:grid;grid-template-columns:4fr 3fr 1fr 2fr 1fr 1fr;gap:.5rem;align-items:end;">
                                <div>
                                    <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Title *</label>
                                    <input type="text" name="items[{{ $i }}][item_title]" value="{{ $item->item_title }}" required style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Description</label>
                                    <input type="text" name="items[{{ $i }}][item_description]" value="{{ $item->item_description }}" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Qty</label>
                                    <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" min="1" required onchange="calcRow(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Price</label>
                                    <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" min="0" required onchange="calcRow(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Total</label>
                                    <div style="padding:.5rem .75rem;font-size:.8125rem;font-weight:700;color:var(--surface-700);" class="row-subtotal">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</div>
                                </div>
                                <div style="display:flex;align-items:end;justify-content:end;">
                                    <button type="button" onclick="this.closest('.item-row').remove(); recalc()" style="padding:.5rem;color:var(--surface-400);border-radius:.5rem;border:none;cursor:pointer;">
                                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="milestone-dates {{ $quotation->isMilestone() ? '' : 'hidden' }}" style="display:{{ $quotation->isMilestone() ? 'grid' : 'none' }};grid-template-columns:1fr 1fr;gap:.5rem;margin-top:.5rem;padding-top:.5rem;border-top:1px solid var(--surface-200);">
                                <div>
                                    <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Start Date *</label>
                                    <input type="date" name="items[{{ $i }}][start_date]" value="{{ $item->start_date?->format('Y-m-d') }}" {{ $quotation->isMilestone() ? 'required' : '' }} style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">End Date *</label>
                                    <input type="date" name="items[{{ $i }}][end_date]" value="{{ $item->end_date?->format('Y-m-d') }}" {{ $quotation->isMilestone() ? 'required' : '' }} style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addRow()" style="margin-top:.75rem;width:100%;padding:.75rem;border:2px dashed var(--surface-200);border-radius:.75rem;font-size:.8125rem;color:var(--surface-400);background:transparent;cursor:pointer;transition:all .15s;" onmouseover="this.style.color='var(--success-600)';this.style.borderColor='var(--success-400)'" onmouseout="this.style.color='var(--surface-400)';this.style.borderColor='var(--surface-200)'">+ Add Item</button>
                </div>
            </x-card>
        </div>

        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <x-card>
                <div class="d-card-header">
                    <h3>Details</h3>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.75rem;font-size:.8125rem;">
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Quotation Type</label>
                        <div style="display:flex;gap:.5rem;">
                            <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.5rem .75rem;border:2px solid {{ $quotation->type === 'simple' ? 'var(--brand-500)' : 'var(--surface-200)' }};border-radius:.5rem;cursor:pointer;font-weight:600;transition:all .15s;background:{{ $quotation->type === 'simple' ? 'var(--brand-50)' : 'transparent' }};color:{{ $quotation->type === 'simple' ? 'var(--brand-700)' : 'var(--surface-600)' }};" id="typeSimpleLabel">
                                <input type="radio" name="type" value="simple" {{ $quotation->type === 'simple' ? 'checked' : '' }} onchange="toggleType(this.value)" style="accent-color:var(--brand-600);"> Simple
                            </label>
                            <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.5rem .75rem;border:2px solid {{ $quotation->type === 'milestone' ? 'var(--brand-500)' : 'var(--surface-200)' }};border-radius:.5rem;cursor:pointer;font-weight:600;transition:all .15s;background:{{ $quotation->type === 'milestone' ? 'var(--brand-50)' : 'transparent' }};color:{{ $quotation->type === 'milestone' ? 'var(--brand-700)' : 'var(--surface-600)' }};" id="typeMilestoneLabel">
                                <input type="radio" name="type" value="milestone" {{ $quotation->type === 'milestone' ? 'checked' : '' }} onchange="toggleType(this.value)" style="accent-color:var(--brand-600);"> Milestone
                            </label>
                        </div>
                        <input type="hidden" name="type" id="typeHidden" value="{{ $quotation->type }}">
                    </div>
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Client</label>
                        <select name="client_id" required style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;background:var(--surface-0);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;">
                            @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ $quotation->client_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Currency</label>
                        <select name="currency_id" id="currencySelect" required style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;background:var(--surface-0);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;">
                            @foreach($currencies as $cur)
                            <option value="{{ $cur->id }}" data-symbol="{{ $cur->symbol }}" {{ $quotation->currency_id == $cur->id ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->symbol }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                        <div>
                            <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Issue Date</label>
                            <input type="date" name="issue_date" value="{{ $quotation->issue_date->format('Y-m-d') }}" required style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                        </div>
                        <div>
                            <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ $quotation->expiry_date?->format('Y-m-d') }}" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Tax</label>
                        <select name="tax_id" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;background:var(--surface-0);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;">
                            <option value="">No Tax</option>
                            @foreach($taxes as $t)
                            <option value="{{ $t->id }}" data-percentage="{{ $t->percentage }}" {{ $quotation->tax_id == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->percentage }}%)</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tax_percentage" id="taxPercentage" value="{{ $quotation->tax_percentage }}">
                    </div>
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Discount</label>
                        <input type="number" name="discount_amount" value="{{ $quotation->discount_amount }}" step="0.01" min="0" oninput="recalc()" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                    </div>
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Terms</label>
                        <textarea name="terms_conditions" rows="3" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;resize:vertical;">{{ $quotation->terms_conditions }}</textarea>
                    </div>
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Payment Instructions</label>
                        <textarea name="payment_instructions" rows="3" placeholder="Bank details for client payment..." style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;resize:vertical;">{{ $quotation->payment_instructions }}</textarea>
                    </div>
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Existing Attachments</label>
                        @forelse($quotation->attachments as $att)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem;background:var(--surface-50);border-radius:.5rem;font-size:.8125rem;margin-bottom:.375rem;">
                            <a href="/storage/quotation-attachments/{{ $att->filename }}" target="_blank" style="color:var(--brand-600);text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">{{ $att->original_name }}</a>
                            <label style="display:flex;align-items:center;gap:.25rem;margin-left:.5rem;font-size:.7rem;color:var(--danger-500);cursor:pointer;">
                                <input type="checkbox" name="remove_attachments[]" value="{{ $att->id }}" style="accent-color:var(--danger-500);border-radius:2px;">
                                Remove
                            </label>
                        </div>
                        @empty
                        <p style="font-size:.7rem;color:var(--surface-400);">No attachments yet.</p>
                        @endforelse
                    </div>
                    <div>
                        <label style="display:block;color:var(--surface-600);margin-bottom:.375rem;font-weight:600;">Add Attachments</label>
                        <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="file-input" style="font-size:.8125rem;">
                        <p style="font-size:.6rem;color:var(--surface-400);margin-top:.25rem;">PDF, Word, Excel, JPG, PNG. Max 10MB each. Max 5 files.</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="d-card-header">
                    <h3>Summary</h3>
                </div>
                <div style="padding:1.25rem;font-size:.8125rem;" id="summary">
                    @php
                        $gross = $quotation->items->sum(fn($i) => $i->quantity * $i->unit_price);
                        $taxAmt = $gross * ($quotation->tax_percentage / 100);
                        $grand = max(0, ($gross + $taxAmt) - $quotation->discount_amount);
                    @endphp
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--surface-500);">Subtotal:</span><span id="gross-total">{{ $quotation->currency_symbol }}{{ number_format($gross, 2) }}</span></div>
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--surface-500);">Tax:</span><span id="tax-amount-total">{{ $quotation->currency_symbol }}{{ number_format($taxAmt, 2) }}</span></div>
                    <div id="discountRow" class="{{ $quotation->discount_amount > 0 ? '' : 'hidden' }}" style="display:{{ $quotation->discount_amount > 0 ? 'flex' : 'none' }};justify-content:space-between;"><span style="color:var(--surface-500);">Discount:</span><span style="color:var(--danger-600);" id="discount-amount-display">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                    <div style="display:flex;justify-content:space-between;border-top:1px solid var(--surface-200);padding-top:.5rem;font-size:1.125rem;font-weight:800;"><span>Total:</span><span id="grand-total" style="color:var(--brand-600);">{{ $quotation->currency_symbol }}{{ number_format($grand, 2) }}</span></div>
                </div>
            </x-card>

            <button type="submit" class="btn btn-brand" style="width:100%;justify-content:center;padding:.75rem;font-size:.875rem;">
                @if($quotation->status === 'change_requested')
                    Amend & Re-send Quotation
                @else
                    Update Quotation
                @endif
            </button>
        </div>
    </div>
</form>

<script>
let rowIndex = {{ count($quotation->items) }};

function toggleType(value) {
    document.getElementById('typeHidden').value = value;
    const isMilestone = value === 'milestone';
    document.querySelectorAll('.milestone-dates').forEach(el => {
        el.style.display = isMilestone ? 'grid' : 'none';
        el.querySelectorAll('input').forEach(input => {
            if (isMilestone) input.setAttribute('required', 'required');
            else input.removeAttribute('required');
        });
    });
    document.getElementById('typeSimpleLabel').style.cssText = `flex:1;display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.5rem .75rem;border:2px solid ${!isMilestone ? 'var(--brand-500)' : 'var(--surface-200)'};border-radius:.5rem;cursor:pointer;font-weight:600;transition:all .15s;background:${!isMilestone ? 'var(--brand-50)' : 'transparent'};color:${!isMilestone ? 'var(--brand-700)' : 'var(--surface-600)'};`;
    document.getElementById('typeMilestoneLabel').style.cssText = `flex:1;display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.5rem .75rem;border:2px solid ${isMilestone ? 'var(--brand-500)' : 'var(--surface-200)'};border-radius:.5rem;cursor:pointer;font-weight:600;transition:all .15s;background:${isMilestone ? 'var(--brand-50)' : 'transparent'};color:${isMilestone ? 'var(--brand-700)' : 'var(--surface-600)'};`;
}

function getSymbol() {
    const sel = document.getElementById('currencySelect');
    return sel?.options[sel.selectedIndex]?.dataset?.symbol || '$';
}
function fmt(val) { return getSymbol() + parseFloat(val).toFixed(2); }

function addRow() {
    const container = document.getElementById('items-container');
    const i = rowIndex++;
    const isMilestone = document.getElementById('typeHidden').value === 'milestone';
    container.insertAdjacentHTML('beforeend', `<div class="item-row" style="padding:.75rem 1rem;background:var(--surface-50);border:1px solid var(--surface-100);border-radius:.75rem;">
        <div style="display:grid;grid-template-columns:4fr 3fr 1fr 2fr 1fr 1fr;gap:.5rem;align-items:end;">
            <div><input type="text" name="items[${i}][item_title]" required style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;" placeholder="Title"></div>
            <div><input type="text" name="items[${i}][item_description]" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;" placeholder="Description"></div>
            <div><input type="number" name="items[${i}][quantity]" min="1" value="1" required onchange="calcRow(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;"></div>
            <div><input type="number" name="items[${i}][unit_price]" step="0.01" min="0" value="0" required onchange="calcRow(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;"></div>
            <div><div style="padding:.5rem .75rem;font-size:.8125rem;font-weight:700;color:var(--surface-700);" class="row-subtotal">${fmt(0)}</div></div>
            <div style="display:flex;align-items:end;justify-content:end;">
                <button type="button" onclick="this.closest('.item-row').remove(); recalc()" style="padding:.5rem;color:var(--surface-400);border-radius:.5rem;border:none;cursor:pointer;"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </div>
        </div>
        <div class="milestone-dates" style="display:${isMilestone ? 'grid' : 'none'};grid-template-columns:1fr 1fr;gap:.5rem;margin-top:.5rem;padding-top:.5rem;border-top:1px solid var(--surface-200);">
            <div>
                <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Start Date *</label>
                <input type="date" name="items[${i}][start_date]" ${isMilestone ? 'required' : ''} style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">End Date *</label>
                <input type="date" name="items[${i}][end_date]" ${isMilestone ? 'required' : ''} style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
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
    if (discount > 0) { dr.style.display = 'flex'; document.getElementById('discount-amount-display').textContent = '-' + fmt(discount); }
    else { dr.style.display = 'none'; }
}

document.querySelector('select[name="tax_id"]')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('taxPercentage').value = opt?.dataset?.percentage || 0;
    recalc();
});
document.getElementById('currencySelect')?.addEventListener('change', recalc);
</script>
@endsection
