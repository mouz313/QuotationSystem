@extends('layouts.admin')
@section('title', 'Edit Admin User')
@section('content')
<div class="fade-in">
    <x-page-header title="Edit Admin User" subtitle="Update {{ $user->name }}" back="/admin/users" />

    <form method="POST" action="/admin/users/{{ $user->id }}" style="max-width:36rem;">
        @csrf @method('PUT')
        <x-card>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <x-form-input label="Full Name" name="name" type="text" :required="true" :value="$user->name" />
                <x-form-input label="Email" name="email" type="email" :required="true" :value="$user->email" />
                <x-form-select label="Admin Role" name="admin_role_id" :value="$user->admin_role_id">
                    <option value="">Super Admin (full access)</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-input label="New Password (leave blank to keep current)" name="password" type="password" />
                <x-form-input label="Confirm Password" name="password_confirmation" type="password" />
            </div>
        </x-card>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Update User</button>
            <a href="/admin/users" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
