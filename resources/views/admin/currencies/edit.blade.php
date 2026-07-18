@extends('layouts.admin')
@section('title', 'Edit Currency')
@section('content')
<div class="fade-in">
    <x-page-header title="Edit Currency" subtitle="Update {{ $currency->name }} ({{ $currency->code }})" back="/admin/currencies" />

    <form method="POST" action="/admin/currencies/{{ $currency->id }}" style="max-width:36rem;">
        @csrf @method('PUT')
        <x-card>
            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                <x-form-input label="Currency Code" name="code" :required="true" :error="$errors->first('code')" :value="old('code', $currency->code)" maxlength="10" style="text-transform:uppercase;" />
                <x-form-input label="Name" name="name" :required="true" :error="$errors->first('name')" :value="old('name', $currency->name)" maxlength="255" />
                <x-form-input label="Symbol" name="symbol" :required="true" :error="$errors->first('symbol')" :value="old('symbol', $currency->symbol)" maxlength="10" />
                <div style="display:flex;align-items:center;gap:1.5rem;">
                    <x-form-toggle label="Default" name="is_default" :checked="old('is_default', $currency->is_default)" />
                    <x-form-toggle label="Active" name="is_active" :checked="old('is_active', $currency->is_active)" />
                </div>
            </div>
        </x-card>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Update Currency</button>
            <a href="/admin/currencies" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
