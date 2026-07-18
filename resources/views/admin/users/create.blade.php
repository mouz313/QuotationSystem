@extends('layouts.admin')
@section('title', 'Create Admin User')
@section('content')
<div class="fade-in">
    <x-page-header title="Create Admin User" back="/admin/users" />

    <form method="POST" action="/admin/users" style="max-width:36rem;">
        @csrf
        <x-card>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <x-form-input label="Full Name" name="name" type="text" :required="true" value="{{ old('name') }}" />
                <x-form-input label="Email" name="email" type="email" :required="true" value="{{ old('email') }}" />
                <x-form-select label="Admin Role" name="admin_role_id" :value="old('admin_role_id')">
                    <option value="">Super Admin (full access)</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-input label="Password" name="password" type="password" :required="true" />
                <x-form-input label="Confirm Password" name="password_confirmation" type="password" :required="true" />
            </div>
        </x-card>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Create User</button>
            <a href="/admin/users" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
