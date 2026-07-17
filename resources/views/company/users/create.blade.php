@extends('layouts.app')
@section('title', 'Add Team User')
@section('content')

<x-page-header title="Add Team User" subtitle="Invite a new team member" back="/company/users" />

<x-card class="fade-in" style="max-width:40rem;">
    <div style="padding:1.5rem;">
        <form method="POST" action="/company/users" style="display:flex;flex-direction:column;gap:1rem;">
            @csrf
            <x-form-input label="Full Name" name="name" required placeholder="Full name" :error="$errors->first('name')" />
            <x-form-input label="Email" name="email" type="email" required placeholder="email@example.com" :error="$errors->first('email')" />
            <x-form-input label="Password" name="password" type="password" required placeholder="Minimum 8 characters" :error="$errors->first('password')" />
            <x-form-select label="Role" name="role" required :error="$errors->first('role')">
                <option value="staff">Staff</option>
                <option value="company_admin">Company Admin</option>
            </x-form-select>
            <div style="display:flex;gap:.5rem;padding-top:.5rem;">
                <button type="submit" class="btn btn-brand">Add User</button>
                <a href="/company/users" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
            </div>
        </form>
    </div>
</x-card>
@endsection
