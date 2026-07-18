@extends('layouts.admin')
@section('title', 'Currencies')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Currencies</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage available currencies for quotations</p>
        </div>
        <a href="/admin/currencies/create" class="btn btn-brand">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Currency
        </a>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Currency</th>
                    <th>Symbol</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($currencies as $cur)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <div class="avatar avatar-sm" style="background:var(--emerald-50);color:var(--emerald-600);font-size:.7rem;font-weight:800;">{{ $cur->code }}</div>
                            <div>
                                <div class="cell-main">{{ $cur->name }}</div>
                                <div class="cell-sub">{{ $cur->code }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:1.125rem;font-weight:700;color:var(--gray-800);">{{ $cur->symbol }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:.375rem;">
                            @if($cur->is_default)
                                <span class="badge badge-sent">Default</span>
                            @endif
                            @if($cur->is_active)
                                <span class="badge badge-active">Active</span>
                            @else
                                <span class="badge badge-inactive">Inactive</span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:.25rem;justify-content:flex-end;">
                            <a href="/admin/currencies/{{ $cur->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="/admin/currencies/{{ $cur->id }}" onsubmit="return confirm('Delete this currency?')">
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
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3>No currencies yet</h3>
                            <p>Add your first currency to get started.</p>
                            <a href="/admin/currencies/create" class="btn btn-brand btn-sm" style="margin-top:.75rem;">New Currency</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
