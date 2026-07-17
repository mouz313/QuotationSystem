@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Profile Settings')
@section('content')

<div class="fade-in">
    <x-page-header title="Profile Settings" subtitle="Manage your personal account information" />
</div>

@php
    $tabs = [
        'profile'  => 'Profile',
        'details'  => 'Account Details',
        'password' => 'Change Password',
    ];
    $active = request('tab', 'profile');
@endphp

<x-card>
    <div class="tab-group">
        @foreach($tabs as $key => $label)
            <a href="?tab={{ $key }}" class="tab-pill {{ $active === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div style="padding:1.5rem;">

        @if($active === 'profile')
            <form method="POST" action="/settings/profile" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <x-form-input label="Full Name" name="name" :value="old('name', $user->name)" :required="true" :error="$errors->first('name')" />
                    <x-form-input label="Email Address" name="email" type="email" :value="old('email', $user->email)" :required="true" :error="$errors->first('email')" />
                </div>
                <button type="submit" class="btn btn-brand">Save Changes</button>
            </form>

        @elseif($active === 'details')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:.8125rem;">
                <div>
                    <span style="color:var(--surface-500);">Role:</span>
                    <span style="margin-left:.5rem;"><span class="badge badge-draft">{{ $user->role }}</span></span>
                </div>
                <div>
                    <span style="color:var(--surface-500);">Member since:</span>
                    <span style="margin-left:.5rem;font-weight:600;color:var(--surface-800);">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                @if($user->company)
                <div>
                    <span style="color:var(--surface-500);">Company:</span>
                    <span style="margin-left:.5rem;font-weight:600;color:var(--surface-800);">{{ $user->company->name }}</span>
                </div>
                <div>
                    <span style="color:var(--surface-500);">Company Status:</span>
                    <span style="margin-left:.5rem;">
                        <span class="badge badge-{{ $user->company->status }}">{{ ucfirst($user->company->status) }}</span>
                    </span>
                </div>
                @endif
            </div>

        @elseif($active === 'password')
            <form method="POST" action="/settings/password" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf @method('PUT')
                <x-form-input label="Current Password" name="current_password" type="password" :required="true" :error="$errors->first('current_password')" />
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <x-form-input label="New Password" name="password" type="password" :required="true" :error="$errors->first('password')" />
                    <x-form-input label="Confirm Password" name="password_confirmation" type="password" :required="true" />
                </div>
                <button type="submit" class="btn btn-brand">Update Password</button>
            </form>
        @endif

    </div>
</x-card>

@endsection
