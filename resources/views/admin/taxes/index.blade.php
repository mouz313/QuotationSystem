@extends('layouts.admin')
@section('title', 'Taxes')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Taxes</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage tax rates for quotations</p>
        </div>
        <a href="/admin/taxes/create" class="btn btn-brand">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Tax
        </a>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Tax Name</th>
                    <th>Percentage</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($taxes as $tax)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <div class="avatar avatar-sm" style="background:var(--amber-50);color:var(--amber-600);font-size:.75rem;font-weight:800;">%</div>
                            <div>
                                <div class="cell-main">{{ $tax->name }}</div>
                                @if($tax->is_default)
                                    <div class="cell-sub">Default rate</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem;min-width:12rem;">
                            <span style="font-weight:700;color:var(--gray-900);min-width:3rem;">{{ $tax->percentage }}%</span>
                            <div class="progress-bar" style="flex:1;max-width:8rem;">
                                <div class="fill amber" style="width:{{ min($tax->percentage * 4, 100) }}%;"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($tax->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:.25rem;justify-content:flex-end;">
                            <a href="/admin/taxes/{{ $tax->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="/admin/taxes/{{ $tax->id }}" onsubmit="return confirm('Delete this tax?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-icon" title="Delete" style="color:var(--red-500);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <h3>No taxes yet</h3>
                            <p>Add your first tax rate to get started.</p>
                            <a href="/admin/taxes/create" class="btn btn-brand btn-sm" style="margin-top:.75rem;">New Tax</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
