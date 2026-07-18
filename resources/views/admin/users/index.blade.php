@extends('layouts.admin')
@section('title', 'Admin Users')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Admin Users</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage admin panel access</p>
        </div>
        <a href="/admin/users/create" class="btn btn-brand">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Admin User
        </a>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <div class="avatar" style="background:linear-gradient(135deg,var(--brand-500),var(--purple-500));color:#fff;font-size:.6rem;">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                            <div>
                                <div class="cell-main">{{ $u->name }}</div>
                                <div class="cell-sub">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($u->adminRole)
                            <span class="badge badge-sent">{{ $u->adminRole->name }}</span>
                        @else
                            <span class="badge badge-active">Super Admin</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:.75rem;color:var(--gray-400);">{{ $u->created_at->diffForHumans() }}</span>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:.25rem;justify-content:flex-end;">
                            <a href="/admin/users/{{ $u->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($u->id !== auth()->id())
                                <form method="POST" action="/admin/users/{{ $u->id }}" onsubmit="return confirm('Delete this admin user?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-icon" title="Delete" style="color:var(--red-500);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <h3>No admin users</h3>
                            <p>Create your first admin user to get started.</p>
                            <a href="/admin/users/create" class="btn btn-brand btn-sm" style="margin-top:.75rem;">New Admin User</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
