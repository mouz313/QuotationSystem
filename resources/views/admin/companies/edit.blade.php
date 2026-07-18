@extends('layouts.admin')
@section('title', 'Edit ' . $company->name)
@section('content')
<div class="fade-in">
    <x-page-header title="Edit Company" subtitle="{{ $company->name }}" back="/admin/companies/{{ $company->id }}" />

    <form method="POST" action="/admin/companies/{{ $company->id }}" style="max-width:40rem;">
        @csrf
        @method('PUT')
        <x-card class="mb-4">
            <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1rem;">
                <x-form-input label="Company Name" name="name" :value="$company->name" required maxlength="255" :error="$errors->first('name')" />
                <x-form-input label="Email" name="email" type="email" :value="$company->email" required maxlength="255" :error="$errors->first('email')" />
                <x-form-input label="Phone" name="phone" :value="$company->phone" maxlength="50" :error="$errors->first('phone')" />
                <x-form-input label="Website" name="website" :value="$company->website" maxlength="255" placeholder="https://example.com" :error="$errors->first('website')" />
                <x-form-textarea label="Address" name="address" :value="$company->address" rows="3" maxlength="500" :error="$errors->first('address')" />
            </div>
        </x-card>

        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Update Company</button>
            <a href="/admin/companies/{{ $company->id }}" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection