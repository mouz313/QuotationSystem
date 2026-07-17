@extends('layouts.admin')
@section('title', 'Create Package')
@section('content')
<div class="fade-in">
    <x-page-header title="Create Package" subtitle="Define a new subscription plan" back="/admin/packages" />

    <x-card class="max-w-2xl">
        <div style="padding:1.25rem;">
            <form method="POST" action="/admin/packages" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf
                <x-form-input label="Package Name" name="name" :value="old('name')" :required="true" />
                <x-form-textarea label="Description" name="description" :rows="2" :value="old('description')" />
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <x-form-input label="Price ($)" name="price" type="number" :value="old('price')" :required="true" />
                    <x-form-input label="Duration (days)" name="duration_days" type="number" :value="old('duration_days', 30)" :required="true" />
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
                    <x-form-input label="Max Users" name="max_users" type="number" :value="old('max_users', 1)" :required="true" />
                    <x-form-input label="Max Clients" name="max_clients" type="number" :value="old('max_clients', 10)" :required="true" />
                    <x-form-input label="Max Quotations" name="max_quotations" type="number" :value="old('max_quotations', 50)" :required="true" />
                </div>
                <div style="display:flex;gap:.5rem;padding-top:.5rem;">
                    <button type="submit" class="btn btn-brand">Create Package</button>
                    <a href="/admin/packages" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
