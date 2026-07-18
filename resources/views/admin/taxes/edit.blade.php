@extends('layouts.admin')
@section('title', 'Edit Tax')
@section('content')
<div class="fade-in">
    <x-page-header title="Edit Tax" subtitle="Update {{ $tax->name }}" back="/admin/taxes" />

    <form method="POST" action="/admin/taxes/{{ $tax->id }}" style="max-width:36rem;">
        @csrf @method('PUT')
        <x-card>
            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                <x-form-input label="Tax Name" name="name" :required="true" :error="$errors->first('name')" :value="old('name', $tax->name)" maxlength="255" />
                <x-form-input label="Percentage (%)" name="percentage" type="number" :required="true" :error="$errors->first('percentage')" :value="old('percentage', $tax->percentage)" min="0" max="100" step="0.01" />
                <div style="display:flex;align-items:center;gap:1.5rem;">
                    <x-form-toggle label="Default" name="is_default" :checked="old('is_default', $tax->is_default)" />
                    <x-form-toggle label="Active" name="is_active" :checked="old('is_active', $tax->is_active)" />
                </div>
            </div>
        </x-card>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Update Tax</button>
            <a href="/admin/taxes" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
