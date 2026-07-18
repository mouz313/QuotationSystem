@extends('layouts.admin')
@section('title', 'Pages')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Pages</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage static frontend pages</p>
        </div>
        <a href="/admin/pages/create" class="btn btn-brand">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Page
        </a>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pages as $page)
                <tr>
                    <td>
                        <div class="cell-main">{{ $page->title }}</div>
                    </td>
                    <td>
                        <span style="font-family:'SF Mono','Fira Code',monospace;font-size:.75rem;color:var(--gray-500);">/{{ $page->slug }}</span>
                    </td>
                    <td>
                        @if($page->is_published)
                            <span class="badge badge-active">Published</span>
                        @else
                            <span class="badge badge-draft">Draft</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:.75rem;color:var(--gray-400);">{{ $page->updated_at->diffForHumans() }}</span>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:.25rem;justify-content:flex-end;">
                            <a href="/admin/pages/{{ $page->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="/admin/pages/{{ $page->id }}" onsubmit="return confirm('Delete this page?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-icon" title="Delete" style="color:var(--red-500);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                            <h3>No pages yet</h3>
                            <p>Create your first static page to get started.</p>
                            <a href="/admin/pages/create" class="btn btn-brand btn-sm" style="margin-top:.75rem;">New Page</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
