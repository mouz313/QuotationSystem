@extends('layouts.app')
@section('title', 'Edit Team User')
@section('content')

<x-page-header title="Edit Team User" subtitle="Update team member details" back="/company/users" />

<x-card class="fade-in" style="max-width:40rem;">
    <div style="padding:1.5rem;">
        <form method="POST" action="/company/users/{{ $user->id }}" style="display:flex;flex-direction:column;gap:1rem;">
            @csrf @method('PUT')
            <x-form-input label="Full Name" name="name" required placeholder="Full name" :value="$user->name" :error="$errors->first('name')" />
            <x-form-input label="Email" name="email" type="email" required placeholder="email@example.com" :value="$user->email" :error="$errors->first('email')" />
            <x-form-input label="New Password" name="password" type="password" placeholder="Leave blank to keep current" :error="$errors->first('password')" help="Minimum 8 characters" />
            <x-form-select label="Role" name="role" required :value="$user->role" :error="$errors->first('role')">
                <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="company_admin" {{ old('role', $user->role) === 'company_admin' ? 'selected' : '' }}>Company Admin</option>
            </x-form-select>
            <div style="display:flex;gap:.5rem;padding-top:.5rem;">
                <button type="submit" class="btn btn-brand">Update User</button>
                <a href="/company/users" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
            </div>
        </form>
    </div>
</x-card>
@endsection
