@extends('layouts.admin')
@section('title', 'Create Page')
@section('content')
<div class="fade-in">
    <x-page-header title="Create Page" back="/admin/pages" />

    <form method="POST" action="/admin/pages" class="max-w-3xl">
        @csrf
        <div class="d-card">
            <div class="d-card-body" style="display:flex;flex-direction:column;gap:1rem;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <x-form-input label="Title" name="title" required
                        oninput="document.getElementById('slugPreview').textContent = '/' + this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'')" />
                    <div>
                        <x-form-input label="Slug (auto-generated)" name="slug" value="{{ old('slug') }}"
                            placeholder="auto-from-title" />
                        <div style="font-size:.7rem;color:var(--gray-400);margin-top:.25rem;">Preview: <span id="slugPreview" style="font-family:monospace;">/</span></div>
                    </div>
                </div>
                <x-form-input label="Meta Description" name="meta_description" value="{{ old('meta_description') }}"
                    placeholder="SEO description" />
                <x-form-textarea label="Content (HTML)" name="content" value="{{ old('content') }}" rows="16" />
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', '1') ? 'checked' : '' }} style="position:absolute;opacity:0;width:0;height:0;">
                        <div style="width:2.75rem;height:1.5rem;background:var(--gray-200);border-radius:999px;transition:background .2s;"></div>
                    </label>
                    <span style="font-size:.8125rem;color:var(--gray-700);">Published</span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn btn-brand">Create Page</button>
            <a href="/admin/pages" class="btn btn-ghost" style="border:1px solid var(--gray-200);">Cancel</a>
        </div>
    </form>
</div>
@endsection
