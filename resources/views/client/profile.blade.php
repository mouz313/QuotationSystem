@extends('client.layouts.client')
@section('title', 'Profile')
@section('content')

<div class="fade-in">
    <x-page-header title="My Profile" subtitle="Manage your account settings" />
</div>

<div style="max-width:36rem;">
    <x-card style="margin-bottom:1.25rem;">
        <x-card-header>
            <div style="display:flex;align-items:center;gap:.5rem;">
                <div style="width:1.75rem;height:1.75rem;border-radius:.375rem;background:var(--brand-50);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:1rem;height:1rem;color:var(--brand-500);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">Personal Information</span>
            </div>
        </x-card-header>
        <div style="padding:1.25rem;">
            <form method="POST" action="/client/profile" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf @method('PUT')
                <x-form-input label="Full Name" name="name" :value="old('name', $clientUser->name)" :required="true" :error="$errors->first('name')" />
                <x-form-input label="Email" name="email" type="email" :value="old('email', $clientUser->email)" :required="true" :error="$errors->first('email')" />
                <x-form-input label="Phone" name="phone" :value="old('phone', $clientUser->phone)" :error="$errors->first('phone')" />
                <button type="submit" class="btn btn-brand">Update Profile</button>
            </form>
        </div>
    </x-card>

    <x-card>
        <x-card-header>
            <div style="display:flex;align-items:center;gap:.5rem;">
                <div style="width:1.75rem;height:1.75rem;border-radius:.375rem;background:var(--warning-50);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:1rem;height:1rem;color:var(--warning-600);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <span style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">Change Password</span>
            </div>
        </x-card-header>
        <div style="padding:1.25rem;">
            <form method="POST" action="/client/profile/password" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf @method('PUT')
                <x-form-input label="Current Password" name="current_password" type="password" :required="true" :error="$errors->first('current_password')" />
                <x-form-input label="New Password" name="password" type="password" :required="true" :error="$errors->first('password')" />
                <x-form-input label="Confirm New Password" name="password_confirmation" type="password" :required="true" />
                <button type="submit" class="btn btn-brand">Update Password</button>
            </form>
        </div>
    </x-card>
</div>

@endsection
