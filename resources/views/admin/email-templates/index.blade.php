@extends('layouts.admin')
@section('title', 'Email Templates')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Email Templates</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage notification email templates and placeholders</p>
        </div>
        <span style="font-size:.8125rem;color:var(--gray-400);">{{ count($templates) }} templates</span>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Template</th>
                    <th>Subject</th>
                    <th>Preview</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($templates as $key => $tpl)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <div class="avatar avatar-brand" style="font-size:.7rem;">
                                @if($key === 'welcome_company')
                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                @elseif($key === 'package_assigned')
                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                @elseif($key === 'quotation_notification')
                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @else
                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="cell-main">{{ $tpl['label'] }}</div>
                                <div class="cell-sub">{{ $key }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="color:var(--gray-600);font-size:.8125rem;">{{ $tpl['subject'] ?: '—' }}</span>
                    </td>
                    <td style="max-width:20rem;">
                        <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--gray-400);font-size:.8125rem;">{{ Str::limit(strip_tags($tpl['body'] ?: 'No content yet'), 80) }}</span>
                    </td>
                    <td>
                        @if($tpl['is_enabled'])
                            <span class="badge badge-active">Enabled</span>
                        @else
                            <span class="badge badge-inactive">Disabled</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="/admin/email-templates/{{ $key }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;padding:1rem;border-radius:var(--radius-lg);background:var(--gray-50);border:1px solid var(--gray-200);">
        <div style="display:flex;align-items:flex-start;gap:.5rem;font-size:.8125rem;color:var(--gray-500);">
            <svg style="width:1rem;height:1rem;margin-top:.125rem;flex-shrink:0;color:var(--gray-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <span style="font-weight:600;color:var(--gray-700);">Available placeholders:</span>
                <code style="margin:0 .25rem;padding:.125rem .375rem;background:#fff;border-radius:var(--radius-sm);border:1px solid var(--gray-200);font-size:.75rem;color:var(--brand-600);">{!! '{{company_name}}' !!}</code>
                <code style="margin:0 .25rem;padding:.125rem .375rem;background:#fff;border-radius:var(--radius-sm);border:1px solid var(--gray-200);font-size:.75rem;color:var(--brand-600);">{!! '{{client_name}}' !!}</code>
                <code style="margin:0 .25rem;padding:.125rem .375rem;background:#fff;border-radius:var(--radius-sm);border:1px solid var(--gray-200);font-size:.75rem;color:var(--brand-600);">{!! '{{quote_number}}' !!}</code>
                <code style="margin:0 .25rem;padding:.125rem .375rem;background:#fff;border-radius:var(--radius-sm);border:1px solid var(--gray-200);font-size:.75rem;color:var(--brand-600);">{!! '{{total}}' !!}</code>
            </div>
        </div>
    </div>
</div>
@endsection