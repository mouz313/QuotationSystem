@extends('layouts.admin')
@section('title', 'Create Company')
@section('content')
<div class="fade-in">
    <x-page-header title="Create Company" subtitle="Add a new company with an admin user" back="/admin/companies" />

    <form method="POST" action="/admin/companies" style="max-width:40rem;">
        @csrf
        <x-card class="mb-4">
            <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);">Company Details</h3>
                <x-form-input label="Company Name" name="name" placeholder="Acme Corp" required maxlength="255" :error="$errors->first('name')" />
                <x-form-input label="Email" name="email" type="email" placeholder="info@acmecorp.com" required maxlength="255" :error="$errors->first('email')" />
                <x-form-input label="Phone" name="phone" placeholder="+1 234 567 890" maxlength="50" :error="$errors->first('phone')" />
                <x-form-textarea label="Address" name="address" placeholder="123 Main St, City, Country" rows="3" maxlength="500" :error="$errors->first('address')" />

                <hr style="border:none;border-top:1px solid var(--gray-100);margin:.25rem 0;">
                <h3 style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);">Admin User</h3>
                <x-form-input label="Admin Name" name="admin_name" placeholder="John Doe" required maxlength="255" :error="$errors->first('admin_name')" />
                <x-form-input label="Admin Email" name="admin_email" type="email" placeholder="admin@acmecorp.com" required maxlength="255" :error="$errors->first('admin_email')" />
                <x-form-input label="Admin Password" name="admin_password" type="password" required :error="$errors->first('admin_password')" />
                <x-form-input label="Confirm Password" name="admin_password_confirmation" type="password" required />
            </div>
        </x-card>

        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Create Company</button>
            <a href="/admin/companies" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection