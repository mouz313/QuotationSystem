@extends('layouts.app')
@section('title', 'Team Users')
@section('content')

<x-page-header title="Team Users" subtitle="Manage your company team members">
    <x-slot name="actions">
        <a href="/company/users/create" class="btn btn-brand" style="font-size:.8125rem;">+ Add User</a>
    </x-slot>
</x-page-header>

<x-search-bar action="/company/users" placeholder="Search by name or email..." />

<div class="d-card fade-in" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="font-weight:600;">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'company_admin')
                            <span class="badge badge-sent">Admin</span>
                        @else
                            <span class="badge badge-draft">Staff</span>
                        @endif
                    </td>
                    <td style="color:var(--surface-500);">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:.25rem;">
                            <a href="/company/users/{{ $user->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="/company/users/{{ $user->id }}" onsubmit="return confirm('Remove this user?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon" title="Remove" style="color:var(--danger-600);">
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
                        <x-empty-state icon="client" title="{{ request('search') ? 'No users match your search.' : 'No team users.' }}" description="{{ request('search') ? 'Try a different search term.' : 'Add your first team member to get started.' }}" action="{{ !request('search') ? '/company/users/create' : '' }}" actionLabel="{{ !request('search') ? '+ Add User' : '' }}" />
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:1rem;">{{ $users->links() }}</div>
@endsection
