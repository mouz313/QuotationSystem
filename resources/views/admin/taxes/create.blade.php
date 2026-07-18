@extends('layouts.admin')
@section('title', 'Create Tax')
@section('content')
<div class="fade-in">
    <x-page-header title="Create Tax" subtitle="Add a new tax rate for quotations" back="/admin/taxes" />

    <form method="POST" action="/admin/taxes" style="max-width:36rem;">
        @csrf
        <x-card>
            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                <x-form-input label="Tax Name" name="name" placeholder="VAT" :required="true" :error="$errors->first('name')" :value="old('name')" maxlength="255" />
                <x-form-input label="Percentage (%)" name="percentage" type="number" placeholder="10" :required="true" :error="$errors->first('percentage')" :value="old('percentage')" min="0" max="100" step="0.01" />
                <x-form-toggle label="Set as default tax" name="is_default" :checked="old('is_default')" />
            </div>
        </x-card>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Create Tax</button>
            <a href="/admin/taxes" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
