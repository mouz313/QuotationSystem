@extends('layouts.app')
@section('title', 'Edit Item')
@section('content')

<x-page-header title="Edit Item: {{ $item->title }}" subtitle="Update item information" back="/items" />

<x-card class="fade-in" style="max-width:40rem;">
    <div style="padding:1.5rem;">
        <form method="POST" action="/items/{{ $item->id }}" style="display:flex;flex-direction:column;gap:1rem;">
            @csrf @method('PUT')
            <x-form-input label="Title" name="title" required placeholder="Service or product title" :value="$item->title" :error="$errors->first('title')" />
            <x-form-textarea label="Description" name="description" rows="2" placeholder="Brief description" :value="$item->description" :error="$errors->first('description')" />
            <x-form-input label="Unit Price" name="unit_price" type="number" step="0.01" min="0" required placeholder="0.00" :value="$item->unit_price" :error="$errors->first('unit_price')" />
            <div style="display:flex;gap:.5rem;padding-top:.5rem;">
                <button type="submit" class="btn btn-brand">Update Item</button>
                <a href="/items" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
            </div>
        </form>
    </div>
</x-card>
@endsection
