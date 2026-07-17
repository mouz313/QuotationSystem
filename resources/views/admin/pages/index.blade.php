@extends('layouts.admin')
@section('title', 'Pages')
@section('content')
<div class="fade-in">
    <x-page-header title="Pages" subtitle="Manage static frontend pages">
        <x:slot name="actions">
            <a href="/admin/pages/create" class="btn btn-brand">+ New Page</a>
        </x:slot>
    </x-page-header>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pages as $page)
                <tr>
                    <td style="font-weight:600;">{{ $page->title }}</td>
                    <td style="font-family:monospace;font-size:.75rem;color:var(--surface-500);">/{{ $page->slug }}</td>
                    <td>
                        @if($page->is_published)
                            <span class="badge badge-active">Published</span>
                        @else
                            <span class="badge badge-draft">Draft</span>
                        @endif
                    </td>
                    <td style="color:var(--surface-500);font-size:.75rem;">{{ $page->updated_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:.25rem;">
                            <a href="/admin/pages/{{ $page->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="/admin/pages/{{ $page->id }}" onsubmit="return confirm('Delete this page?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon" title="Delete" style="color:var(--danger-600);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">
                    <x-empty-state title="No pages yet" description="Create your first static page to get started." icon="info" action="/admin/pages/create" actionLabel="+ New Page" />
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
