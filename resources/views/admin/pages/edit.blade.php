@extends('layouts.admin')
@section('title', 'Edit Page')
@section('content')
<div class="fade-in">
    <x-page-header title="Edit Page" subtitle="{{ $page->title }}" back="/admin/pages" />

    <form method="POST" action="/admin/pages/{{ $page->id }}" class="max-w-3xl">
        @csrf @method('PUT')
        <div class="d-card">
            <div class="d-card-body" style="display:flex;flex-direction:column;gap:1rem;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <x-form-input label="Title" name="title" :value="$page->title" required />
                    <x-form-input label="Slug" name="slug" :value="$page->slug" />
                </div>
                <x-form-input label="Meta Description" name="meta_description" :value="$page->meta_description" />
                <x-form-textarea label="Content (HTML)" name="content" :value="$page->content" rows="16" />
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }} style="position:absolute;opacity:0;width:0;height:0;">
                        <div style="width:2.75rem;height:1.5rem;background:var(--surface-200);border-radius:999px;transition:background .2s;"></div>
                    </label>
                    <span style="font-size:.8125rem;color:var(--surface-700);">Published</span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Update Page</button>
            <a href="/admin/pages" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
