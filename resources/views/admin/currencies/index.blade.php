@extends('layouts.admin')
@section('title', 'Currencies')
@section('content')
<div class="fade-in">
    <x-page-header title="Currencies" subtitle="Manage available currencies for quotations">
        <x-slot name="actions">
            <a href="/admin/currencies/create" class="btn btn-brand">+ New Currency</a>
        </x-slot>
    </x-page-header>

    <x-card :padding="false" style="overflow:hidden;">
        <table class="d-table">
            <thead><tr>
                <th>Code</th>
                <th>Name</th>
                <th>Symbol</th>
                <th>Default</th>
                <th>Status</th>
                <th>Actions</th>
            </tr></thead>
            <tbody>
            @forelse($currencies as $cur)
                <tr>
                    <td><span style="font-family:monospace;font-weight:600;">{{ $cur->code }}</span></td>
                    <td>{{ $cur->name }}</td>
                    <td style="font-size:1.125rem;">{{ $cur->symbol }}</td>
                    <td>
                        @if($cur->is_default)
                            <x-status-badge status="info">Default</x-status-badge>
                        @else
                            <span style="color:var(--surface-400);">-</span>
                        @endif
                    </td>
                    <td>
                        @if($cur->is_active)
                            <x-status-badge status="success">Active</x-status-badge>
                        @else
                            <x-status-badge>Inactive</x-status-badge>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.25rem;">
                            <a href="/admin/currencies/{{ $cur->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="/admin/currencies/{{ $cur->id }}" onsubmit="return confirm('Delete this currency?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon" title="Delete" style="color:var(--danger-600);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">
                    <x-empty-state icon="info" title="No currencies created yet" description="Add your first currency to get started." action="/admin/currencies/create" actionLabel="+ New Currency" />
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </x-card>
</div>
@endsection