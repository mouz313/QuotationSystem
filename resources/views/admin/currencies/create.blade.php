@extends('layouts.admin')
@section('title', 'Create Currency')
@section('content')
<div class="fade-in">
    <x-page-header title="Create Currency" subtitle="Add a new currency for quotations" back="/admin/currencies" />

    <form method="POST" action="/admin/currencies" style="max-width:36rem;">
        @csrf
        <x-card>
            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                <x-form-input label="Currency Code" name="code" placeholder="USD" :required="true" :error="$errors->first('code')" :value="old('code')" maxlength="10" style="text-transform:uppercase;" help="ISO 4217 code, e.g. USD, EUR, GBP" />
                <x-form-input label="Name" name="name" placeholder="US Dollar" :required="true" :error="$errors->first('name')" :value="old('name')" maxlength="255" />
                <x-form-input label="Symbol" name="symbol" placeholder="$" :required="true" :error="$errors->first('symbol')" :value="old('symbol')" maxlength="10" />
                <x-form-toggle label="Set as default currency" name="is_default" :checked="old('is_default')" />
            </div>
        </x-card>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Create Currency</button>
            <a href="/admin/currencies" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
