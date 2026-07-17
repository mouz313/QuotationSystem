@extends('layouts.admin')
@section('title', 'Edit Company User')
@section('content')
<div class="fade-in">
    <x-page-header title="Edit Company User" subtitle="Update {{ $user->name }}" back="/admin/company-users" />

    <form method="POST" action="/admin/company-users/{{ $user->id }}" class="max-w-xl">
        @csrf @method('PUT')
        <div class="d-card">
            <div class="d-card-body" style="display:flex;flex-direction:column;gap:1rem;">
                <x-form-input label="Full Name" name="name" required :value="$user->name" />
                <x-form-input label="Email" name="email" type="email" required :value="$user->email" />
                <x-form-select name="company_id" label="Company" required placeholder="Select company..."
                    :value="$user->company_id"
                    :options="$companies->pluck('name', 'id')->toArray()" />
                <x-form-select name="role" label="Role" required placeholder="Select role..."
                    :value="$user->role"
                    :options="['company_admin' => 'Company Admin', 'staff' => 'Staff']" />
                <x-form-input label="New Password (leave blank to keep current)" name="password" type="password" />
                <x-form-input label="Confirm Password" name="password_confirmation" type="password" />
            </div>
        </div>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Update User</button>
            <a href="/admin/company-users" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
