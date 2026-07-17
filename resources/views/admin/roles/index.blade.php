@extends('layouts.admin')
@section('title', 'Roles & Permissions')
@section('content')
<div class="fade-in">
    <x-page-header title="Roles & Permissions" subtitle="Manage admin roles and their permissions">
        <x-slot name="actions">
            <a href="/admin/roles/create" class="btn btn-brand btn-sm">+ New Role</a>
        </x-slot>
    </x-page-header>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th>Default</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($roles as $role)
                <tr>
                    <td style="font-weight:600;">{{ $role->name }}</td>
                    <td>
                        <span class="badge badge-sent">{{ count($role->permissions ?? []) }} permissions</span>
                    </td>
                    <td>{{ $role->users_count }}</td>
                    <td>
                        @if($role->is_default)
                            <span class="badge badge-active">Default</span>
                        @else
                            <span class="badge badge-inactive">No</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.25rem;">
                            @if(!$role->is_default)
                                <a href="/admin/roles/{{ $role->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if($role->users_count === 0)
                                    <form method="POST" action="/admin/roles/{{ $role->id }}" onsubmit="return confirm('Delete this role?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-icon" title="Delete" style="color:var(--danger-600);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state icon="client" title="No roles" description="Create your first role to get started." action="/admin/roles/create" actionLabel="+ New Role" />
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
