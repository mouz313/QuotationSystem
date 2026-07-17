@extends('layouts.app')
@section('title', 'Create Quotation')
@section('content')

<div style="max-width:80rem;margin:0 auto;">
    <x-page-header title="Create a New Quotation" subtitle="Fill in the details below and see a live preview as you go" back="/quotations" />

    <form method="POST" action="/quotations" id="quoteForm" enctype="multipart/form-data">
        @csrf
        <div style="display:grid;grid-template-columns:1fr;gap:1.5rem;" class="fade-in">
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
                <div style="display:flex;flex-direction:column;gap:1.5rem;">
                    <x-card>
                        <div class="d-card-header" style="background:var(--brand-50);">
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <svg style="width:1.125rem;height:1.125rem;color:var(--brand-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <h3>Quotation Details</h3>
                                    <p style="font-size:.65rem;color:var(--surface-400);margin-top:.125rem;">Client, currency, and dates</p>
                                </div>
                            </div>
                        </div>
                        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
                            <div>
                                <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Client <span style="color:var(--danger-500);">*</span></label>
                                <select name="client_id" required style="width:100%;padding:.625rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;background:var(--surface-0);transition:box-shadow .15s;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                    <option value="">Select a client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }} ({{ $client->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                                <div>
                                    <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Currency <span style="color:var(--danger-500);">*</span></label>
                                    <select name="currency_id" id="currencySelect" required style="width:100%;padding:.625rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;background:var(--surface-0);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                        <option value="">Select currency</option>
                                        @foreach($currencies as $cur)
                                            <option value="{{ $cur->id }}" data-symbol="{{ $cur->symbol }}" {{ old('currency_id', $currencies->where('is_default')->first()?->id) == $cur->id ? 'selected' : '' }}>
                                                {{ $cur->symbol }} {{ $cur->code }} — {{ $cur->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Issue Date <span style="color:var(--danger-500);">*</span></label>
                                    <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" required style="width:100%;padding:.625rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                                <div>
                                    <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Expiry Date</label>
                                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" style="width:100%;padding:.625rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                </div>
                            </div>

                            <div style="padding-top:1.25rem;border-top:1px solid var(--surface-100);">
                                <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.5rem;">Quotation Type <span style="color:var(--danger-500);">*</span></label>
                                <div style="display:flex;gap:.75rem;">
                                    <label style="flex:1;display:flex;align-items:center;gap:.625rem;padding:.75rem 1rem;border:2px solid {{ old('type', 'simple') === 'simple' ? 'var(--brand-500)' : 'var(--surface-200)' }};border-radius:.75rem;cursor:pointer;background:{{ old('type', 'simple') === 'simple' ? 'var(--brand-50)' : 'transparent' }};color:{{ old('type', 'simple') === 'simple' ? 'var(--brand-700)' : 'var(--surface-600)' }};transition:all .15s;" id="typeSimpleLabel">
                                        <input type="radio" name="type" value="simple" {{ old('type', 'simple') === 'simple' ? 'checked' : '' }} onchange="toggleType(this.value)" style="accent-color:var(--brand-600);">
                                        <div>
                                            <div style="font-size:.8125rem;font-weight:700;">Simple</div>
                                            <div style="font-size:.65rem;opacity:.7;">Standard line items</div>
                                        </div>
                                    </label>
                                    <label style="flex:1;display:flex;align-items:center;gap:.625rem;padding:.75rem 1rem;border:2px solid {{ old('type') === 'milestone' ? 'var(--brand-500)' : 'var(--surface-200)' }};border-radius:.75rem;cursor:pointer;background:{{ old('type') === 'milestone' ? 'var(--brand-50)' : 'transparent' }};color:{{ old('type') === 'milestone' ? 'var(--brand-700)' : 'var(--surface-600)' }};transition:all .15s;" id="typeMilestoneLabel">
                                        <input type="radio" name="type" value="milestone" {{ old('type') === 'milestone' ? 'checked' : '' }} onchange="toggleType(this.value)" style="accent-color:var(--brand-600);">
                                        <div>
                                            <div style="font-size:.8125rem;font-weight:700;">Milestone</div>
                                            <div style="font-size:.65rem;opacity:.7;">Date-based payment milestones</div>
                                        </div>
                                    </label>
                                </div>
                                <input type="hidden" name="type" id="typeHidden" value="{{ old('type', 'simple') }}">
                            </div>
                        </div>
                    </x-card>

                    <x-card>
                        <div class="d-card-header" style="background:var(--success-50);">
                            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                                <div style="display:flex;align-items:center;gap:.75rem;">
                                    <svg style="width:1.125rem;height:1.125rem;color:var(--success-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <div>
                                        <h3 id="itemsSectionTitle">Line Items</h3>
                                        <p style="font-size:.65rem;color:var(--success-500);margin-top:.125rem;" id="itemsSectionDesc">Products or services to include</p>
                                    </div>
                                </div>
                                <button type="button" onclick="addRow()" style="padding:.375rem .75rem;font-size:.7rem;background:white;color:var(--success-700);font-weight:700;border-radius:.5rem;border:none;cursor:pointer;">+ Add Item</button>
                            </div>
                        </div>
                        <div style="padding:1.5rem;">
                            @if($items->isNotEmpty())
                            <div style="margin-bottom:1rem;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                                    <label style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);">Your Saved Items</label>
                                    <span style="font-size:.65rem;color:var(--surface-400);">{{ $items->count() }} items</span>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
                                    @foreach($items as $item)
                                    <button type="button" onclick='addFromSavedItem(@json(['title' => $item->title, 'description' => $item->description, 'price' => $item->unit_price]))' style="text-align:left;padding:.75rem;background:white;border:1px solid var(--surface-200);border-radius:.75rem;cursor:pointer;transition:all .15s;" onmouseover="this.style.borderColor='var(--success-400)';this.style.background='var(--success-50)'" onmouseout="this.style.borderColor='var(--surface-200)';this.style.background='white'">
                                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;">
                                            <div style="min-width:0;">
                                                <div style="font-size:.8125rem;font-weight:600;color:var(--surface-800);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->title }}</div>
                                                @if($item->description)
                                                <div style="font-size:.65rem;color:var(--surface-400);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:.125rem;">{{ $item->description }}</div>
                                                @endif
                                            </div>
                                            <div style="font-size:.8125rem;font-weight:700;color:var(--success-600);white-space:nowrap;">${{ number_format($item->unit_price, 2) }}</div>
                                        </div>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div style="display:grid;grid-template-columns:4fr 3fr 1fr 2fr 1fr 1fr;gap:.5rem;margin-bottom:.5rem;padding:0 .25rem;" class="hidden md:grid">
                                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-400);">Item</div>
                                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-400);">Description</div>
                                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-400);text-align:right;">Qty</div>
                                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-400);text-align:right;">Price</div>
                                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-400);text-align:right;">Total</div>
                                <div></div>
                            </div>

                            <div id="items-container" style="display:flex;flex-direction:column;gap:.5rem;">
                                <div class="item-row" style="padding:.75rem 1rem;background:var(--surface-50);border:1px solid var(--surface-100);border-radius:.75rem;transition:all .15s;">
                                    <div style="display:grid;grid-template-columns:12fr;gap:.5rem;align-items:end;">
                                        <div>
                                            <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Title *</label>
                                            <input type="text" name="items[0][item_title]" required style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);" placeholder="Item title">
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Description</label>
                                            <input type="text" name="items[0][item_description]" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);" placeholder="Description">
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Qty *</label>
                                            <input type="number" name="items[0][quantity]" min="1" value="1" required onchange="calcRow(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Price *</label>
                                            <input type="number" name="items[0][unit_price]" step="0.01" min="0" value="0" required onchange="calcRow(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Total</label>
                                            <div style="padding:.5rem .75rem;font-size:.8125rem;font-weight:700;color:var(--surface-700);" class="row-subtotal">{{ $defaultCurrency?->symbol ?? '$' }}0.00</div>
                                        </div>
                                        <div style="display:flex;align-items:end;justify-content:end;">
                                            <button type="button" onclick="this.closest('.item-row').remove(); recalc()" style="padding:.5rem;color:var(--surface-400);border-radius:.5rem;border:none;cursor:pointer;transition:color .15s,background .15s;" onmouseover="this.style.color='var(--danger-600)';this.style.background='var(--danger-50)'" onmouseout="this.style.color='var(--surface-400)';this.style.background='transparent'">
                                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="milestone-dates hidden" style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-top:.5rem;padding-top:.5rem;border-top:1px solid var(--surface-200);">
                                        <div>
                                            <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Start Date *</label>
                                            <input type="date" name="items[0][start_date]" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">End Date *</label>
                                            <input type="date" name="items[0][end_date]" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addRow()" style="width:100%;margin-top:.75rem;padding:.75rem;border:2px dashed var(--surface-200);border-radius:.75rem;font-size:.8125rem;color:var(--surface-400);background:transparent;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:.5rem;" onmouseover="this.style.color='var(--success-600)';this.style.borderColor='var(--success-400)';this.style.background='var(--success-50)'" onmouseout="this.style.color='var(--surface-400)';this.style.borderColor='var(--surface-200)';this.style.background='transparent'">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Add Another Item
                            </button>
                        </div>
                    </x-card>

                    <x-card>
                        <div class="d-card-header" style="background:var(--warning-50);">
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <svg style="width:1.125rem;height:1.125rem;color:var(--warning-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <div>
                                    <h3>Terms & Conditions</h3>
                                    <p style="font-size:.65rem;color:var(--warning-500);margin-top:.125rem;">Optional terms for this quotation</p>
                                </div>
                            </div>
                        </div>
                        <div style="padding:1.5rem;">
                            <textarea name="terms_conditions" rows="4" placeholder="Payment due within 14 days..." style="width:100%;padding:.75rem;border:1px solid var(--surface-200);border-radius:.75rem;font-size:.8125rem;color:var(--surface-800);outline:none;resize:vertical;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">{{ old('terms_conditions', $defaultTerms) }}</textarea>
                        </div>
                        <div style="padding:0 1.5rem 1.5rem;">
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Payment Instructions</label>
                            <textarea name="payment_instructions" rows="3" placeholder="Payment due within 14 days...&#10;Reference: Quote #" style="width:100%;padding:.75rem;border:1px solid var(--surface-200);border-radius:.75rem;font-size:.8125rem;color:var(--surface-800);outline:none;resize:vertical;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">{{ old('payment_instructions') }}</textarea>
                        </div>
                    </x-card>

                    <x-card>
                        <div class="d-card-header" style="background:var(--info-50);">
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <svg style="width:1.125rem;height:1.125rem;color:var(--info-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <div>
                                    <h3>Attachments</h3>
                                    <p style="font-size:.65rem;color:var(--info-500);margin-top:.125rem;">Optional files to include (PDF, Word, Excel, Images)</p>
                                </div>
                            </div>
                        </div>
                        <div style="padding:1.5rem;">
                            <div style="border:2px dashed var(--surface-200);border-radius:.75rem;padding:1.5rem;text-align:center;transition:all .15s;" onmouseover="this.style.borderColor='var(--info-400)';this.style.background='var(--info-50)'" onmouseout="this.style.borderColor='var(--surface-200)';this.style.background='transparent'">
                                <svg style="width:2.5rem;height:2.5rem;color:var(--surface-300);margin:0 auto .75rem;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p style="font-size:.8125rem;color:var(--surface-600);font-weight:600;margin-bottom:.25rem;">Drop files here or click to browse</p>
                                <p style="font-size:.65rem;color:var(--surface-400);margin-bottom:.75rem;">Max 5 files, 10MB each. PDF, Word, Excel, JPG, PNG.</p>
                                <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="file-input" style="font-size:.8125rem;">
                            </div>
                        </div>
                    </x-card>
                </div>

                <div style="display:flex;flex-direction:column;gap:1.5rem;">
                    <div class="d-card fade-in" style="position:sticky;top:4rem;">
                        <div class="d-card-header" style="background:var(--surface-900);color:white;">
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <h3 style="color:white;">Summary</h3>
                            </div>
                        </div>
                        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1.25rem;">
                            <div>
                                <label style="display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.5rem;">Tax</label>
                                <div style="display:flex;flex-direction:column;gap:.5rem;">
                                    <select name="tax_id" id="taxSelect" onchange="onTaxChange()" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;background:var(--surface-0);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                        <option value="">No tax</option>
                                        @foreach($taxes as $tax)
                                            <option value="{{ $tax->id }}" data-percentage="{{ $tax->percentage }}" {{ old('tax_id') == $tax->id ? 'selected' : '' }}>
                                                {{ $tax->name }} ({{ $tax->percentage }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="tax_id" id="taxIdHidden" value="{{ old('tax_id') }}">
                                    <div style="display:flex;align-items:center;gap:.5rem;">
                                        <input type="number" name="tax_percentage" id="taxPercentage" step="0.01" min="0" max="100" value="{{ old('tax_percentage', 0) }}" onchange="recalc()" style="width:5rem;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);" placeholder="%">
                                        <span style="font-size:.7rem;color:var(--surface-400);">%</span>
                                        <span id="tax-amount" style="margin-left:auto;font-size:.8125rem;font-weight:600;color:var(--surface-700);">$0.00</span>
                                    </div>
                                </div>
                                <button type="button" onclick="toggleAddTax()" id="addTaxToggle" style="margin-top:.5rem;font-size:.7rem;color:var(--brand-600);font-weight:600;display:flex;align-items:center;gap:.25rem;border:none;cursor:pointer;background:transparent;transition:color .15s;">
                                    <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Add New Tax
                                </button>
                                <div id="addTaxForm" class="hidden" style="margin-top:.5rem;padding:.75rem;background:var(--surface-50);border-radius:.75rem;border:1px solid var(--surface-200);display:flex;flex-direction:column;gap:.5rem;">
                                    <input type="text" id="newTaxName" placeholder="Tax name" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                                    <div style="display:flex;gap:.5rem;">
                                        <input type="number" id="newTaxPercent" placeholder="%" min="0" max="100" step="0.01" style="flex:1;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                                        <button type="button" onclick="saveNewTax()" class="btn btn-brand" style="font-size:.8125rem;">Save</button>
                                    </div>
                                    <div id="newTaxError" class="hidden" style="font-size:.7rem;color:var(--danger-600);"></div>
                                </div>
                            </div>

                            <div>
                                <label style="display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.5rem;">Discount</label>
                                <div style="position:relative;">
                                    <span style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);font-size:.8125rem;color:var(--surface-400);font-weight:600;">$</span>
                                    <input type="number" name="discount_amount" step="0.01" min="0" value="{{ old('discount_amount', 0) }}" onchange="recalc()" style="width:100%;padding:.625rem .75rem .625rem 1.75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);">
                                </div>
                            </div>

                            <div style="border-top:1px solid var(--surface-100);padding-top:1rem;display:flex;flex-direction:column;gap:.5rem;">
                                <div style="display:flex;justify-content:space-between;font-size:.8125rem;">
                                    <span style="color:var(--surface-500);">Subtotal</span>
                                    <span id="gross-total" style="font-weight:600;color:var(--surface-700);">$0.00</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;font-size:.8125rem;">
                                    <span style="color:var(--surface-500);">Tax</span>
                                    <span id="tax-amount-total" style="font-weight:600;color:var(--surface-700);">$0.00</span>
                                </div>
                                @php $hasDiscount = old('discount_amount', 0) > 0; @endphp
                                <div id="discountRow" class="{{ $hasDiscount ? '' : 'hidden' }}" style="display:flex;justify-content:space-between;font-size:.8125rem;">
                                    <span style="color:var(--surface-500);">Discount</span>
                                    <span id="discount-amount-display" style="font-weight:600;color:var(--danger-500);">-$0.00</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;padding-top:.75rem;border-top:2px solid var(--surface-900);">
                                    <span style="font-weight:800;color:var(--surface-900);">Grand Total</span>
                                    <span id="grand-total" style="font-size:1.25rem;font-weight:800;color:var(--brand-600);">$0.00</span>
                                </div>
                            </div>

                            <div style="border-top:1px solid var(--surface-100);padding-top:1.25rem;display:flex;flex-direction:column;gap:.75rem;">
                                <button type="submit" class="btn btn-brand" style="width:100%;justify-content:center;padding:.75rem;font-size:.875rem;">
                                    Create Quotation
                                </button>
                                <a href="/quotations" style="display:block;width:100%;text-align:center;padding:.75rem;border:1px solid var(--surface-200);color:var(--surface-600);font-weight:600;border-radius:.5rem;text-decoration:none;font-size:.8125rem;transition:background .15s;" onmouseover="this.style.background='var(--surface-50)'" onmouseout="this.style.background='transparent'">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let rowIndex = 1;

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
    document.getElementById('typeSimpleLabel').style.cssText = `flex:1;display:flex;align-items:center;gap:.625rem;padding:.75rem 1rem;border:2px solid ${!isMilestone ? 'var(--brand-500)' : 'var(--surface-200)'};border-radius:.75rem;cursor:pointer;background:${!isMilestone ? 'var(--brand-50)' : 'transparent'};color:${!isMilestone ? 'var(--brand-700)' : 'var(--surface-600)'};transition:all .15s;`;
    document.getElementById('typeMilestoneLabel').style.cssText = `flex:1;display:flex;align-items:center;gap:.625rem;padding:.75rem 1rem;border:2px solid ${isMilestone ? 'var(--brand-500)' : 'var(--surface-200)'};border-radius:.75rem;cursor:pointer;background:${isMilestone ? 'var(--brand-50)' : 'transparent'};color:${isMilestone ? 'var(--brand-700)' : 'var(--surface-600)'};transition:all .15s;`;
    document.getElementById('itemsSectionTitle').textContent = isMilestone ? 'Milestones' : 'Line Items';
    document.getElementById('itemsSectionDesc').textContent = isMilestone ? 'Define milestones with date ranges and payments' : 'Products or services to include';
}

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
    const isMilestone = document.getElementById('typeHidden').value === 'milestone';
    const html = `<div class="item-row" style="padding:.75rem 1rem;background:var(--surface-50);border:1px solid var(--surface-100);border-radius:.75rem;transition:all .15s;">
        <div style="display:grid;grid-template-columns:12fr;gap:.5rem;align-items:end;">
            <div>
                <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Title *</label>
                <input type="text" name="items[${i}][item_title]" required style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;" placeholder="Item title">
            </div>
            <div>
                <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Description</label>
                <input type="text" name="items[${i}][item_description]" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;" placeholder="Description">
            </div>
            <div>
                <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Qty *</label>
                <input type="number" name="items[${i}][quantity]" min="1" value="1" required onchange="calcRow(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;">
            </div>
            <div>
                <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Price *</label>
                <input type="number" name="items[${i}][unit_price]" step="0.01" min="0" value="0" required onchange="calcRow(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;text-align:right;">
            </div>
            <div>
                <label style="display:block;font-size:.65rem;color:var(--surface-500);margin-bottom:.25rem;">Total</label>
                <div style="padding:.5rem .75rem;font-size:.8125rem;font-weight:700;color:var(--surface-700);" class="row-subtotal">${fmt(0)}</div>
            </div>
            <div style="display:flex;align-items:end;justify-content:end;">
                <button type="button" onclick="this.closest('.item-row').remove(); recalc()" style="padding:.5rem;color:var(--surface-400);border-radius:.5rem;border:none;cursor:pointer;">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
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
        discountRow.style.display = 'flex';
        document.getElementById('discount-amount-display').textContent = '-' + fmt(discount);
    } else {
        discountRow.style.display = 'none';
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
