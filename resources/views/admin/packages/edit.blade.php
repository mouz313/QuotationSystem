@extends('layouts.admin')
@section('title', 'Edit Package')
@section('content')
<div class="fade-in">
    <x-page-header title="Edit Package: {{ $package->name }}" subtitle="Update subscription plan details" back="/admin/packages" />

    <x-card class="max-w-2xl">
        <div style="padding:1.25rem;">
            <form method="POST" action="/admin/packages/{{ $package->id }}" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf @method('PUT')
                <x-form-input label="Package Name" name="name" :value="old('name', $package->name)" :required="true" />
                <x-form-textarea label="Description" name="description" :rows="2" :value="old('description', $package->description)" />
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <x-form-input label="Price ($)" name="price" type="number" :value="old('price', $package->price)" :required="true" />
                    <x-form-input label="Duration (days)" name="duration_days" type="number" :value="old('duration_days', $package->duration_days)" :required="true" />
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
                    <x-form-input label="Max Users" name="max_users" type="number" :value="old('max_users', $package->max_users)" :required="true" />
                    <x-form-input label="Max Clients" name="max_clients" type="number" :value="old('max_clients', $package->max_clients)" :required="true" />
                    <x-form-input label="Max Quotations" name="max_quotations" type="number" :value="old('max_quotations', $package->max_quotations)" :required="true" />
                </div>
                <div>
                    <label style="display:flex;align-items:center;gap:.5rem;font-size:.8125rem;color:var(--gray-700);">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                            style="border-radius:3px;border:1px solid var(--gray-300);accent-color:var(--brand-600);">
                        Active
                    </label>
                </div>
                <div style="display:flex;gap:.5rem;padding-top:.5rem;">
                    <button type="submit" class="btn btn-brand">Update Package</button>
                    <a href="/admin/packages" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
