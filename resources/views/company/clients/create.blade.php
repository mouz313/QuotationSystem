@extends('layouts.app')
@section('title', 'Add Client')
@section('content')

<x-page-header title="Add Client" subtitle="Create a new client" back="/clients" />

<x-card class="fade-in" style="max-width:40rem;">
    <div style="padding:1.5rem;">
        <form method="POST" action="/clients" style="display:flex;flex-direction:column;gap:1rem;">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <x-form-input label="Name" name="name" required placeholder="Full name" :error="$errors->first('name')" />
                <x-form-input label="Email" name="email" type="email" required placeholder="email@example.com" :error="$errors->first('email')" />
            </div>
            <x-form-input label="Phone" name="phone" placeholder="Phone number" :error="$errors->first('phone')" />
            <x-form-textarea label="Address" name="address" rows="2" placeholder="Full address" :error="$errors->first('address')" />
            <div style="display:flex;gap:.5rem;padding-top:.5rem;">
                <button type="submit" class="btn btn-brand">Save Client</button>
                <a href="/clients" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
            </div>
        </form>
    </div>
</x-card>
@endsection
