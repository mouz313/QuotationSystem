@extends('layouts.app')
@section('title', 'Clients')
@section('content')

<x-page-header title="Clients" subtitle="Manage your client list">
    <x-slot name="actions">
        <a href="/clients/export" class="btn btn-ghost" style="border:1px solid var(--surface-200);font-size:.8125rem;">Export CSV</a>
        <a href="/clients/create" class="btn btn-brand" style="font-size:.8125rem;">+ New Client</a>
    </x-slot>
</x-page-header>

<x-search-bar action="/clients" placeholder="Search by name, email, or phone..." />

<div class="d-card" style="overflow:hidden;">
    <div class="overflow-x-auto">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Quotations</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr>
                    <td style="font-weight:600;"><a href="/clients/{{ $client->id }}" style="color:var(--brand-600);text-decoration:none;">{{ $client->name }}</a></td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->phone ?? '-' }}</td>
                    <td>{{ $client->quotations_count }}</td>
                    <td>
                        <div style="display:flex;gap:.25rem;">
                            <a href="/clients/{{ $client->id }}" class="btn btn-ghost btn-icon" title="View" style="color:var(--brand-600);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="/clients/{{ $client->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if(in_array($client->id, $pendingPaymentClientIds ?? []))
                                <span class="btn btn-icon" style="color:var(--surface-400);cursor:not-allowed;" title="Cannot delete: pending payments">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </span>
                            @else
                                <form method="POST" action="/clients/{{ $client->id }}" onsubmit="return confirm('Delete this client?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon" title="Delete" style="color:var(--danger-600);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state icon="client" title="{{ request('search') ? 'No clients match your search.' : 'No clients yet.' }}" description="{{ request('search') ? 'Try a different search term.' : 'Add your first client to get started.' }}" action="{{ !request('search') ? '/clients/create' : '' }}" actionLabel="{{ !request('search') ? '+ Add Client' : '' }}" />
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:1rem;">{{ $clients->links() }}</div>
@endsection
