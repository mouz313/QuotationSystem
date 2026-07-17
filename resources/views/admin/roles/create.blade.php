@extends('layouts.admin')
@section('title', 'Create Role')
@section('content')
<div class="fade-in">
    <x-page-header title="Create Role" back="/admin/roles" />

    <form method="POST" action="/admin/roles" style="max-width:42rem;">
        @csrf
        <x-card>
            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                <x-form-input label="Role Name" name="name" type="text" :required="true" value="{{ old('name') }}" />

                <div>
                    <label style="font-size:.8125rem;font-weight:600;color:var(--surface-700);display:block;margin-bottom:.75rem;">Permissions</label>
                    <div style="display:flex;flex-direction:column;gap:.75rem;">
                        @foreach(\App\Models\AdminRole::allPermissions() as $key => $label)
                            <x-form-toggle label="{{ $label }}" name="permissions[]" value="{{ $key }}" checked="{{ in_array($key, old('permissions', [])) ? 'checked' : '' }}" />
                        @endforeach
                    </div>
                    @error('permissions')
                        <p style="color:var(--danger-600);font-size:.75rem;margin-top:.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-card>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Create Role</button>
            <a href="/admin/roles" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
