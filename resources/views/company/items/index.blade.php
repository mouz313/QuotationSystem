@extends('layouts.app')
@section('title', 'Items')
@section('content')

<x-page-header title="Items" subtitle="Service/product catalog">
    <x-slot name="actions">
        <a href="/items/export" class="btn btn-ghost" style="border:1px solid var(--surface-200);font-size:.8125rem;">Export CSV</a>
        <a href="/items/create" class="btn btn-brand" style="font-size:.8125rem;">+ New Item</a>
    </x-slot>
</x-page-header>

<x-search-bar action="/items" placeholder="Search by title or description..." />

<div class="d-card fade-in" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Unit Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td style="font-weight:600;">{{ $item->title }}</td>
                    <td style="max-width:20rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->description ?? '-' }}</td>
                    <td style="font-weight:600;">{{ $defaultCurrency?->symbol ?? '$' }}{{ number_format($item->unit_price, 2) }}</td>
                    <td>
                        <div style="display:flex;gap:.25rem;">
                            <a href="/items/{{ $item->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="/items/{{ $item->id }}" onsubmit="return confirm('Delete this item?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon" title="Delete" style="color:var(--danger-600);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <x-empty-state icon="item" title="{{ request('search') ? 'No items match your search.' : 'No items yet.' }}" description="{{ request('search') ? 'Try a different search term.' : 'Add your first item to get started.' }}" action="{{ !request('search') ? '/items/create' : '' }}" actionLabel="{{ !request('search') ? '+ Add Item' : '' }}" />
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:1rem;">{{ $items->links() }}</div>
@endsection
