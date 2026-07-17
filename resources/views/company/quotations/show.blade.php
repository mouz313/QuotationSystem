@extends('layouts.app')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')

<x-page-header back="/quotations" title="{{ $quotation->quote_number }}" subtitle="Issued {{ $quotation->issue_date->format('M d, Y') }} · Expires {{ $quotation->expiry_date?->format('M d, Y') ?? 'N/A' }}@if($quotation->invoice_number) · Invoice: <span style="font-weight:700;">{{ $quotation->invoice_number }}</span>@endif">
    <x-slot name="actions">
        <x-status-badge :status="$quotation->status">{{ ucfirst($quotation->status) }}</x-status-badge>
        @if($quotation->isMilestone())
            <span style="padding:.2rem .6rem;font-size:.65rem;font-weight:700;border-radius:999px;background:oklch(0.95 0.04 300);color:oklch(0.50 0.16 300);text-transform:uppercase;letter-spacing:.04em;">Milestone</span>
        @endif
    </x-slot>
</x-page-header>

<div style="display:flex;gap:.375rem;flex-wrap:wrap;margin-bottom:1.25rem;" class="fade-in">
    @if(in_array($quotation->status, ['draft', 'change_requested']))
        <a href="/quotations/{{ $quotation->id }}/edit" class="btn" style="background:{{ $quotation->status === 'change_requested' ? 'oklch(0.50 0.16 300)' : 'var(--warning-600)' }};color:white;font-size:.8125rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            {{ $quotation->status === 'change_requested' ? 'Amend Quotation' : 'Edit' }}
        </a>
    @endif
    <a href="/quotations/{{ $quotation->id }}/preview" target="_blank" class="btn btn-ghost" style="border:1px solid var(--surface-200);font-size:.8125rem;">Preview</a>
    <a href="/quotations/{{ $quotation->id }}/pdf" class="btn btn-brand" style="font-size:.8125rem;">PDF</a>
    @if(in_array($quotation->status, ['draft', 'change_requested']))
    <form method="POST" action="/quotations/{{ $quotation->id }}/send-email" style="display:inline;" onsubmit="return confirm('Send this quotation to {{ $quotation->client->email }}?')">
        @csrf
        <button class="btn" style="background:var(--success-600);color:white;font-size:.8125rem;">Send to Client</button>
    </form>
    @endif
    @if(in_array($quotation->status, ['sent', 'opened', 'accepted']) && $quotation->payment_status !== 'paid' && $quotation->client->email)
    <form method="POST" action="/quotations/{{ $quotation->id }}/send-reminder" style="display:inline;">
        @csrf
        <button class="btn" style="background:var(--warning-600);color:white;font-size:.8125rem;">Send Payment Reminder</button>
    </form>
    @endif
    <form method="POST" action="/quotations/{{ $quotation->id }}/clone" style="display:inline;">
        @csrf
        <button class="btn btn-ghost" style="border:1px solid var(--surface-200);font-size:.8125rem;">Clone</button>
    </form>
    @if($quotation->status === 'draft')
        <form method="POST" action="/quotations/{{ $quotation->id }}/status" style="display:inline;">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="sent">
            <button class="btn" style="background:var(--info-600);color:white;font-size:.8125rem;">Mark Sent</button>
        </form>
    @endif
    <form method="POST" action="/quotations/{{ $quotation->id }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete {{ $quotation->quote_number }}? This cannot be undone.')">
        @csrf @method('DELETE')
        <button class="btn btn-icon" title="Delete" style="color:var(--danger-600);border:1px solid var(--danger-200);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </form>
</div>

<div class="grid-2-1" style="align-items:start;">
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="d-card fade-in">
            <div style="padding:1.5rem;">
                @php $company = $quotation->user->company; @endphp
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;padding-bottom:1.25rem;border-bottom:1px solid var(--surface-100);">
                    <div style="display:flex;align-items:center;gap:.875rem;">
                        <div style="width:2.75rem;height:2.75rem;border-radius:.625rem;background:var(--brand-50);color:var(--brand-600);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.875rem;flex-shrink:0;">
                            {{ $company ? substr($company->name, 0, 2) : 'NA' }}
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--surface-900);">{{ $company?->name ?? 'N/A' }}</div>
                            @if($company && $company->email)<div style="font-size:.7rem;color:var(--surface-500);">{{ $company->email }}</div>@endif
                            @if($company && $company->phone)<div style="font-size:.7rem;color:var(--surface-500);">{{ $company->phone }}</div>@endif
                        </div>
                    </div>
                    <div style="font-size:.7rem;color:var(--surface-400);text-align:right;">
                        Currency: <span style="font-weight:700;color:var(--surface-700);">{{ $quotation->currency?->code ?? 'N/A' }}</span>
                    </div>
                </div>
                <div style="margin-bottom:1.5rem;">
                    <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--surface-400);margin-bottom:.375rem;">Client</div>
                    <div style="font-weight:600;color:var(--surface-900);">{{ $quotation->client->name }}</div>
                    <div style="font-size:.8125rem;color:var(--surface-500);">{{ $quotation->client->email }}</div>
                    @if($quotation->client->phone)<div style="font-size:.8125rem;color:var(--surface-500);">{{ $quotation->client->phone }}</div>@endif
                </div>
                <table class="d-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Description</th>
                            @if($quotation->isMilestone())
                            <th>Start</th>
                            <th>End</th>
                            @endif
                            <th style="text-align:right;">Qty</th>
                            <th style="text-align:right;">Price</th>
                            <th style="text-align:right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                        <tr>
                            <td style="font-weight:600;color:var(--surface-900);">{{ $item->item_title }}</td>
                            <td style="color:var(--surface-500);">{{ $item->item_description ?? '-' }}</td>
                            @if($quotation->isMilestone())
                            <td style="color:var(--surface-700);font-size:.75rem;">{{ $item->start_date?->format('d M Y') ?? '-' }}</td>
                            <td style="color:var(--surface-700);font-size:.75rem;">{{ $item->end_date?->format('d M Y') ?? '-' }}</td>
                            @endif
                            <td style="text-align:right;color:var(--surface-700);">{{ $item->quantity }}</td>
                            <td style="text-align:right;color:var(--surface-700);">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align:right;font-weight:600;color:var(--surface-900);">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--surface-100);">
                    <div style="max-width:15rem;margin-left:auto;display:flex;flex-direction:column;gap:.375rem;font-size:.8125rem;">
                        <div style="display:flex;justify-content:space-between;color:var(--surface-500);"><span>Subtotal</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
                        @if($quotation->discount_amount > 0)
                            <div style="display:flex;justify-content:space-between;color:var(--danger-500);"><span>Discount</span><span>-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                        @endif
                        @if($quotation->tax_percentage > 0)
                            <div style="display:flex;justify-content:space-between;color:var(--surface-500);">
                                <span>Tax @if($quotation->tax)({{ $quotation->tax->name }}) @endif({{ $quotation->tax_percentage }}%)</span>
                                <span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span>
                            </div>
                        @endif
                        <div style="display:flex;justify-content:space-between;padding-top:.5rem;border-top:2px solid var(--surface-200);font-size:1rem;font-weight:800;color:var(--surface-900);">
                            <span>Grand Total</span>
                            <span style="color:var(--brand-600);">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
                @if($quotation->terms_conditions)
                    <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--surface-100);">
                        <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--surface-400);margin-bottom:.5rem;">Terms & Conditions</div>
                        <div style="font-size:.8125rem;color:var(--surface-600);line-height:1.6;">{{ $quotation->terms_conditions }}</div>
                    </div>
                @endif

                @if($quotation->attachments->count() > 0)
                    <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--surface-100);">
                        <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--surface-400);margin-bottom:.5rem;">Attachments</div>
                        <div style="display:flex;flex-direction:column;gap:.375rem;">
                            @foreach($quotation->attachments as $att)
                            <div style="display:flex;align-items:center;gap:.5rem;padding:.625rem;background:var(--surface-50);border-radius:.5rem;">
                                <svg style="width:1rem;height:1rem;color:var(--surface-400);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <a href="/storage/quotation-attachments/{{ $att->filename }}" target="_blank" style="font-size:.8125rem;color:var(--brand-600);text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $att->original_name }}</a>
                                <span style="font-size:.6rem;color:var(--surface-400);margin-left:auto;flex-shrink:0;">{{ round($att->size / 1024) }}KB</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($quotation->isMilestone())
        @php $progress = $quotation->milestone_progress; @endphp
        <div class="d-card fade-in">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--brand-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <h3>Milestone Progress</h3>
                </div>
                <span style="font-size:.7rem;color:var(--surface-500);font-weight:600;">{{ $progress['completed'] }}/{{ $progress['total'] }} completed</span>
            </div>
            <div style="padding:1.25rem;">
                <div style="width:100%;background:var(--surface-100);border-radius:999px;height:.625rem;margin-bottom:1.25rem;">
                    <div style="background:var(--brand-600);height:.625rem;border-radius:999px;transition:width .5s ease;width:{{ $progress['percent'] }}%;"></div>
                </div>
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    @foreach($quotation->items()->orderBy('sort_order')->get() as $item)
                    @php
                        $itemPaid = $item->paid_amount;
                        $itemRemaining = max(0, $item->subtotal - $itemPaid);
                        $itemFullyPaid = $itemPaid >= $item->subtotal;
                        $itemPercent = $item->subtotal > 0 ? min(100, round(($itemPaid / $item->subtotal) * 100)) : 0;
                    @endphp
                    <div style="padding:1rem;border-radius:.75rem;border:1px solid {{ $itemFullyPaid ? 'var(--success-100)' : 'var(--surface-200)' }};background:{{ $itemFullyPaid ? 'var(--success-50)' : 'var(--surface-50)' }};">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                            <div style="display:flex;align-items:center;gap:.5rem;">
                                @if($itemFullyPaid)
                                    <svg style="width:1rem;height:1rem;color:var(--success-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <div style="width:1rem;height:1rem;border-radius:999px;border:2px solid var(--surface-300);"></div>
                                @endif
                                <span style="font-size:.8125rem;font-weight:700;color:{{ $itemFullyPaid ? 'var(--success-700)' : 'var(--surface-800)' }};">{{ $item->item_title }}</span>
                            </div>
                            <span style="font-size:.7rem;color:{{ $itemFullyPaid ? 'var(--success-600)' : 'var(--surface-500)' }};font-weight:600;">{{ $itemFullyPaid ? 'Paid' : $itemPercent . '%' }}</span>
                        </div>
                        @if($item->start_date && $item->end_date)
                        <div style="display:flex;align-items:center;gap:.75rem;font-size:.7rem;color:var(--surface-500);margin-bottom:.5rem;">
                            <span>{{ $item->start_date->format('d M Y') }} - {{ $item->end_date->format('d M Y') }}</span>
                            <span style="color:var(--surface-300);">|</span>
                            <span>{{ $item->duration_days }} days</span>
                        </div>
                        @endif
                        <div style="width:100%;background:white;border-radius:999px;height:.375rem;margin-bottom:.5rem;">
                            <div style="height:.375rem;border-radius:999px;transition:width .5s ease;background:{{ $itemFullyPaid ? 'var(--success-500)' : 'var(--brand-400)' }};width:{{ $itemPercent }}%;"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.7rem;">
                            <span style="color:var(--surface-500);">Paid: {{ $quotation->currency_symbol }}{{ number_format($itemPaid, 2) }}</span>
                            <span style="color:{{ $itemFullyPaid ? 'var(--success-600)' : 'var(--surface-700)' }};">Total: {{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</span>
                        </div>
                        @if($itemRemaining > 0)
                        <div style="font-size:.7rem;color:var(--danger-500);margin-top:.25rem;">Remaining: {{ $quotation->currency_symbol }}{{ number_format($itemRemaining, 2) }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="d-card fade-in">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--surface-400);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3>Activity Timeline</h3>
                </div>
            </div>
            <div style="padding:1.25rem;">
                @forelse($quotation->activityLogs as $log)
                    <div class="activity-item">
                        <div class="activity-dot" style="background:{{ \App\Models\ActivityLog::getActionColor($log->action) }};"></div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                                <span class="badge" style="font-size:.6rem;white-space:nowrap;{{ str_contains(\App\Models\ActivityLog::getActionColor($log->action), 'var(--') ? '' : 'background:var(--surface-100);color:var(--surface-600);' }}">{{ $log->action }}</span>
                                <span style="font-size:.8125rem;color:var(--surface-600);flex:1;">{{ $log->description }}</span>
                            </div>
                            <div style="display:flex;gap:.5rem;margin-top:.125rem;font-size:.65rem;color:var(--surface-400);">
                                <span style="white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</span>
                                @if($log->user)<span>· {{ $log->user->name }}</span>@endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align:center;color:var(--surface-400);padding:1rem 0;font-size:.8125rem;">No activity recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem;">
        @php
            $pStatus = $quotation->payment_status ?? 'unpaid';
            $totalPaid = $quotation->payments->where('status', 'approved')->sum('amount');
            $remaining = max(0, $quotation->grand_total - $totalPaid);
        @endphp
        <div class="d-card fade-in">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--surface-400);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <h3>Payment Overview</h3>
                </div>
            </div>
            <div style="padding:1.25rem;font-size:.8125rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.625rem;">
                    <span style="color:var(--surface-500);">Status</span>
                    @if($pStatus === 'paid')
                        <span class="badge badge-accepted">Paid</span>
                    @elseif($pStatus === 'partial')
                        <span class="badge badge-opened">Partial</span>
                    @else
                        <span class="badge badge-declined">Unpaid</span>
                    @endif
                </div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--surface-500);">Grand Total</span><span style="font-weight:600;color:var(--surface-900);">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span></div>
                @if($totalPaid > 0)
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--surface-500);">Total Paid</span><span style="font-weight:600;color:var(--success-600);">{{ $quotation->currency_symbol }}{{ number_format($totalPaid, 2) }}</span></div>
                @endif
                @if($remaining > 0)
                <div style="display:flex;justify-content:space-between;padding-top:.625rem;border-top:1px solid var(--surface-100);margin-top:.625rem;">
                    <span style="font-weight:700;color:var(--surface-700);">Remaining</span>
                    <span style="font-weight:800;color:var(--danger-600);">{{ $quotation->currency_symbol }}{{ number_format($remaining, 2) }}</span>
                </div>
                @elseif($totalPaid > 0)
                <div style="display:flex;justify-content:center;padding-top:.625rem;border-top:1px solid var(--surface-100);margin-top:.625rem;">
                    <span style="display:flex;align-items:center;gap:.375rem;color:var(--success-600);font-weight:700;">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Fully Paid
                    </span>
                </div>
                @endif
            </div>
        </div>

        @if($quotation->status !== 'draft')
        <div class="d-card fade-in">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--surface-400);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <h3>Payment Actions</h3>
                </div>
            </div>
            <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.75rem;">
                <form method="POST" action="/quotations/{{ $quotation->id }}/payment">
                    @csrf @method('PATCH')
                    <select name="payment_status" onchange="togglePaidAmount(this)" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;background:var(--surface-0);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;">
                        <option value="unpaid" {{ $pStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ $pStatus === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ $pStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    <div id="paidAmountField" class="{{ $pStatus === 'partial' ? '' : 'hidden' }}" style="margin-top:.5rem;">
                        <input type="number" name="paid_amount" step="0.01" min="0" value="{{ $quotation->paid_amount ?? 0 }}" placeholder="Amount paid" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                    </div>
                    <button class="btn btn-brand" style="width:100%;margin-top:.5rem;justify-content:center;">Update Status</button>
                </form>
                <form method="POST" action="/quotations/{{ $quotation->id }}/payment-instructions" style="padding-top:.875rem;border-top:1px solid var(--surface-100);">
                    @csrf @method('PATCH')
                    <label style="display:block;font-size:.7rem;font-weight:700;color:var(--surface-500);margin-bottom:.375rem;">Bank Details (visible to client)</label>
                    <textarea name="payment_instructions" rows="3" placeholder="Bank Name&#10;Account Number&#10;Reference: {{ $quotation->quote_number }}" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;resize:vertical;">{{ $quotation->payment_instructions }}</textarea>
                    <button class="btn btn-sm" style="background:var(--surface-600);color:white;margin-top:.375rem;">Save Instructions</button>
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
        <div class="fade-in" style="background:var(--warning-50);border:1px solid var(--warning-100);border-radius:.75rem;padding:1.25rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--warning-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 style="font-size:.8125rem;font-weight:700;color:var(--warning-700);">Pending Approvals ({{ $pendingPayments->count() }})</h3>
                </div>
                <label style="display:flex;align-items:center;gap:.375rem;font-size:.7rem;color:var(--warning-600);cursor:pointer;">
                    <input type="checkbox" id="selectAllPayments" onchange="toggleAllPayments(this)" style="accent-color:var(--warning-600);">
                    Select all
                </label>
            </div>
            <form id="bulkPaymentForm" style="display:flex;flex-direction:column;gap:.75rem;">
                @csrf
                @foreach($pendingPayments as $p)
                <div style="background:white;border-radius:.5rem;padding:1rem;border:1px solid var(--warning-100);">
                    <div style="display:flex;align-items:flex-start;gap:.75rem;">
                        <input type="checkbox" name="payment_ids[]" value="{{ $p->id }}" onchange="updateBulkActions()" style="margin-top:.25rem;accent-color:var(--warning-600);">
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                                <span style="font-size:1rem;font-weight:800;color:var(--surface-900);">{{ $quotation->currency_symbol }}{{ number_format($p->amount, 2) }}</span>
                                <span style="font-size:.7rem;color:var(--surface-400);">{{ $p->created_at->format('d M Y g:i A') }}</span>
                            </div>
                            <div style="font-size:.7rem;color:var(--surface-500);margin-bottom:.5rem;">by {{ $p->clientUser->name }}</div>
                            @if($p->quotationItem)
                            <div style="font-size:.7rem;color:var(--brand-600);margin-bottom:.5rem;font-weight:700;">Milestone: {{ $p->quotationItem->item_title }}</div>
                            @endif
                            @if($p->notes)<p style="font-size:.8125rem;color:var(--surface-600);margin-bottom:.75rem;background:var(--surface-50);padding:.625rem;border-radius:.5rem;">{{ $p->notes }}</p>@endif
                            <div style="display:flex;align-items:center;gap:.5rem;">
                                @if($p->proof)
                                <a href="/storage/{{ $p->proof }}" target="_blank" style="display:inline-flex;align-items:center;gap:.25rem;padding:.375rem .75rem;border:1px solid var(--surface-200);font-size:.7rem;font-weight:600;color:var(--surface-700);border-radius:.5rem;text-decoration:none;" onmouseover="this.style.background='var(--surface-50)'" onmouseout="this.style.background='transparent'">View Proof</a>
                                @endif
                                <form method="POST" action="/quotations/{{ $quotation->id }}/payments/{{ $p->id }}/approve" style="display:inline;">
                                    @csrf
                                    <button style="padding:.375rem .75rem;background:var(--success-600);color:white;font-size:.7rem;font-weight:700;border-radius:.5rem;border:none;cursor:pointer;">Approve</button>
                                </form>
                                <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" style="padding:.375rem .75rem;background:var(--danger-600);color:white;font-size:.7rem;font-weight:700;border-radius:.5rem;border:none;cursor:pointer;">Reject</button>
                                <form method="POST" action="/quotations/{{ $quotation->id }}/payments/{{ $p->id }}/reject" class="hidden">
                                    @csrf
                                    <div style="display:flex;gap:.375rem;">
                                        <input type="text" name="rejection_reason" placeholder="Reason" style="width:7rem;padding:.375rem .5rem;border:1px solid var(--surface-200);border-radius:.25rem;font-size:.7rem;outline:none;">
                                        <button style="padding:.375rem .5rem;background:var(--danger-700);color:white;font-size:.7rem;font-weight:700;border-radius:.25rem;border:none;cursor:pointer;">Confirm</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div id="bulkActions" class="hidden" style="padding-top:.75rem;border-top:1px solid var(--warning-200);display:flex;align-items:center;gap:.75rem;">
                    <button type="submit" form="bulkApproveForm" style="display:inline-flex;align-items:center;gap:.375rem;padding:.5rem 1rem;background:var(--success-600);color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;border:none;cursor:pointer;">Approve Selected</button>
                    <button type="button" onclick="document.getElementById('bulkRejectModal').classList.remove('hidden')" style="display:inline-flex;align-items:center;gap:.375rem;padding:.5rem 1rem;background:var(--danger-600);color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;border:none;cursor:pointer;">Reject Selected</button>
                    <span id="selectedCount" style="font-size:.7rem;color:var(--warning-600);font-weight:700;"></span>
                </div>
            </form>
            <form id="bulkApproveForm" method="POST" action="/quotations/{{ $quotation->id }}/payments/bulk-approve" class="hidden">
                @csrf
                <div id="bulkApproveInputs"></div>
            </form>
        </div>

        <div id="bulkRejectModal" class="hidden fade-in" style="background:var(--danger-50);border:1px solid var(--danger-100);border-radius:.75rem;padding:1.25rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <svg style="width:1.125rem;height:1.125rem;color:var(--danger-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <h3 style="font-size:.8125rem;font-weight:700;color:var(--danger-700);">Reject Selected Payments</h3>
            </div>
            <form method="POST" action="/quotations/{{ $quotation->id }}/payments/bulk-reject">
                @csrf
                <div id="bulkRejectInputs"></div>
                <div style="margin-bottom:.75rem;">
                    <label style="display:block;font-size:.7rem;font-weight:700;color:var(--danger-600);margin-bottom:.25rem;">Rejection Reason (optional)</label>
                    <textarea name="rejection_reason" rows="2" placeholder="Why are these payments being rejected?" style="width:100%;padding:.5rem .75rem;border:1px solid var(--danger-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;resize:vertical;background:white;"></textarea>
                </div>
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" style="padding:.5rem 1rem;background:var(--danger-600);color:white;font-size:.8125rem;font-weight:700;border-radius:.5rem;border:none;cursor:pointer;">Confirm Reject</button>
                    <button type="button" onclick="document.getElementById('bulkRejectModal').classList.add('hidden')" style="padding:.5rem 1rem;background:white;border:1px solid var(--surface-200);color:var(--surface-600);font-size:.8125rem;font-weight:600;border-radius:.5rem;cursor:pointer;">Cancel</button>
                </div>
            </form>
        </div>
        @endif

        @php $reviewedPayments = $quotation->payments->whereIn('status', ['approved', 'rejected']); @endphp
        @if($reviewedPayments->count() > 0)
        <div class="d-card fade-in">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--surface-400);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <h3>Payment History</h3>
                </div>
            </div>
            <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                @foreach($reviewedPayments as $p)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem;background:var(--surface-50);border-radius:.5rem;font-size:.8125rem;">
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span style="font-weight:700;color:var(--surface-900);">{{ $quotation->currency_symbol }}{{ number_format($p->amount, 2) }}</span>
                        <span style="font-size:.7rem;color:var(--surface-500);">{{ $p->clientUser->name }}</span>
                        @if($p->quotationItem)<span style="font-size:.7rem;color:var(--brand-600);font-weight:700;">({{ $p->quotationItem->item_title }})</span>@endif
                        @if($p->proof)<a href="/storage/{{ $p->proof }}" target="_blank" style="color:var(--brand-600);text-decoration:none;font-size:.7rem;">Proof</a>@endif
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        @if($p->reviewer)<span style="font-size:.7rem;color:var(--surface-400);">{{ $p->reviewer->name }}</span>@endif
                        <span class="badge {{ $p->status === 'approved' ? 'badge-accepted' : 'badge-declined' }}">{{ ucfirst($p->status) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($quotation->status === 'change_requested')
        <div class="fade-in" style="background:oklch(0.95 0.04 300);border:1px solid oklch(0.88 0.06 300);border-radius:.75rem;padding:1.25rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <svg style="width:1.125rem;height:1.125rem;color:oklch(0.50 0.16 300);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <h3 style="font-size:.8125rem;font-weight:700;color:oklch(0.45 0.14 300);">Change Request</h3>
            </div>
            @php $changeLog = $quotation->statusLogs->firstWhere('to_status', 'change_requested'); @endphp
            @if($changeLog && $changeLog->notes)
                <div style="padding:.75rem;background:white;border-radius:.5rem;font-size:.8125rem;color:var(--surface-700);margin-bottom:.75rem;border:1px solid oklch(0.90 0.04 300);">{{ $changeLog->notes }}</div>
            @else
                <p style="font-size:.8125rem;color:var(--surface-600);margin-bottom:.75rem;">Client has requested changes to this quotation.</p>
            @endif
            <a href="/quotations/{{ $quotation->id }}/edit" style="display:inline-flex;align-items:center;gap:.375rem;padding:.5rem 1rem;background:oklch(0.50 0.16 300);color:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;text-decoration:none;">Amend Quotation</a>
        </div>
        @endif

        <div class="d-card fade-in">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--surface-400);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3>Status History</h3>
                </div>
            </div>
            <div style="padding:1.25rem;">
                @forelse($quotation->statusLogs as $log)
                <div style="display:flex;gap:.625rem;padding:.625rem 0;">
                    <div style="display:flex;flex-direction:column;align-items:center;">
                        <div style="width:.5rem;height:.5rem;border-radius:999px;margin-top:.375rem;flex-shrink:0;
                            @switch($log->to_status)
                                @case('draft') background:var(--surface-400); @break
                                @case('sent') background:var(--info-500); @break
                                @case('opened') background:var(--warning-500); @break
                                @case('change_requested') background:oklch(0.50 0.16 300); @break
                                @case('accepted') background:var(--success-500); @break
                                @case('declined') background:var(--danger-500); @break
                                @default background:var(--surface-400);
                            @endswitch">
                        </div>
                        @if(!$loop->last)<div style="width:1px;height:100%;background:var(--surface-200);margin-left:.125rem;margin-top:.25rem;"></div>@endif
                    </div>
                    <div>
                        <p style="font-size:.8125rem;font-weight:600;color:var(--surface-800);">{{ ucfirst(str_replace('_', ' ', $log->to_status)) }}</p>
                        <p style="font-size:.65rem;color:var(--surface-400);">{{ $log->created_at->format('d M Y g:i A') }}</p>
                        @if($log->notes)<p style="font-size:.65rem;color:var(--surface-500);margin-top:.125rem;">{{ $log->notes }}</p>@endif
                    </div>
                </div>
                @empty
                <p style="text-align:center;color:var(--surface-400);padding:1rem 0;font-size:.8125rem;">No history yet.</p>
                @endforelse
            </div>
        </div>

        @if($quotation->revisions->count() > 0)
        <div class="d-card fade-in">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--surface-400);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <h3>Revision History</h3>
                </div>
            </div>
            <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                @foreach($quotation->revisions as $rev)
                <div style="padding:.75rem;background:var(--surface-50);border-radius:.5rem;font-size:.8125rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-weight:600;color:var(--surface-700);">Revision {{ $loop->iteration }}</span>
                        <span style="font-size:.65rem;color:var(--surface-400);">{{ $rev->created_at->format('d M Y') }}</span>
                    </div>
                    <p style="font-size:.7rem;color:var(--surface-500);margin-top:.125rem;">Total: {{ $quotation->currency_symbol }}{{ number_format($rev->grand_total, 2) }}</p>
                    @if($rev->notes)<p style="font-size:.7rem;color:var(--surface-500);margin-top:.125rem;">{{ $rev->notes }}</p>@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="d-card fade-in">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:var(--surface-400);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <h3>Internal Notes</h3>
                </div>
            </div>
            <div style="padding:1.25rem;">
                <div style="display:flex;flex-direction:column;gap:.625rem;max-height:12rem;overflow-y:auto;margin-bottom:.875rem;">
                    @forelse($quotation->notes as $note)
                        <div style="font-size:.8125rem;padding:.75rem;background:var(--surface-50);border-radius:.5rem;">
                            <p style="color:var(--surface-700);">{{ $note->note }}</p>
                            <p style="font-size:.65rem;color:var(--surface-400);margin-top:.375rem;">{{ $note->user->name }} · {{ $note->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p style="text-align:center;color:var(--surface-400);padding:1rem 0;font-size:.8125rem;">No notes yet.</p>
                    @endforelse
                </div>
                <form method="POST" action="/quotations/{{ $quotation->id }}/notes" style="display:flex;gap:.5rem;">
                    @csrf
                    <input type="text" name="note" placeholder="Add a note..." required style="flex:1;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;transition:border-color .15s;focus:border-color:var(--brand-500);">
                    <button class="btn btn-brand" style="white-space:nowrap;">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePaidAmount(sel) {
    document.getElementById('paidAmountField').classList.toggle('hidden', sel.value !== 'partial');
}

function toggleAllPayments(master) {
    document.querySelectorAll('input[name="payment_ids[]"]').forEach(cb => {
        cb.checked = master.checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checked = document.querySelectorAll('input[name="payment_ids[]"]:checked');
    const bulkActions = document.getElementById('bulkActions');
    const countEl = document.getElementById('selectedCount');
    const ids = Array.from(checked).map(cb => cb.value);

    if (ids.length > 0) {
        bulkActions.classList.remove('hidden');
        countEl.textContent = ids.length + ' selected';

        const approveContainer = document.getElementById('bulkApproveInputs');
        const rejectContainer = document.getElementById('bulkRejectInputs');
        approveContainer.innerHTML = '';
        rejectContainer.innerHTML = '';
        ids.forEach(id => {
            approveContainer.innerHTML += '<input type="hidden" name="payment_ids[]" value="' + id + '">';
            rejectContainer.innerHTML += '<input type="hidden" name="payment_ids[]" value="' + id + '">';
        });
    } else {
        bulkActions.classList.add('hidden');
    }

    const all = document.querySelectorAll('input[name="payment_ids[]"]');
    const selectAll = document.getElementById('selectAllPayments');
    if (selectAll) {
        selectAll.checked = all.length > 0 && checked.length === all.length;
    }
}
</script>
@endsection
