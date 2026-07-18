@extends('layouts.admin')
@section('title', 'Roles & Permissions')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Roles & Permissions</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage admin roles and their permissions</p>
        </div>
        <a href="/admin/roles/create" class="btn btn-brand">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Role
        </a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(22rem,1fr));gap:1rem;">
        @forelse($roles as $role)
            <div class="d-card" style="padding:1.5rem;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;">
                    <div style="display:flex;align-items:center;gap:.625rem;">
                        <div class="avatar" style="background:linear-gradient(135deg,var(--brand-100),var(--purple-100));color:var(--brand-700);">
                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--gray-900);">{{ $role->name }}</div>
                            <div style="font-size:.75rem;color:var(--gray-400);">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</div>
                        </div>
                    </div>
                    @if($role->is_default)
                        <span class="badge badge-sent">Default</span>
                    @endif
                </div>

                <div style="margin-bottom:1rem;">
                    <div style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.375rem;">Permissions</div>
                    <div style="display:flex;flex-wrap:wrap;gap:.25rem;">
                        @forelse($role->permissions ?? [] as $perm)
                            <span style="padding:.15rem .5rem;border-radius:999px;font-size:.6875rem;font-weight:500;background:var(--gray-100);color:var(--gray-600);">{{ $perm }}</span>
                        @empty
                            <span style="font-size:.75rem;color:var(--gray-400);">No permissions</span>
                        @endforelse
                    </div>
                </div>

                @if(!$role->is_default)
                    <div style="display:flex;gap:.375rem;padding-top:.75rem;border-top:1px solid var(--gray-100);">
                        <a href="/admin/roles/{{ $role->id }}/edit" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;">
                            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        @if($role->users_count === 0)
                            <form method="POST" action="/admin/roles/{{ $role->id }}" onsubmit="return confirm('Delete this role?')" style="flex:1;">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="width:100%;justify-content:center;background:var(--red-50);color:var(--red-600);border:1px solid var(--red-100);">
                                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div style="grid-column:1/-1;">
                <div class="d-card">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <h3>No roles created</h3>
                        <p>Create your first role to get started.</p>
                        <a href="/admin/roles/create" class="btn btn-brand btn-sm" style="margin-top:.75rem;">New Role</a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection