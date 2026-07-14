@extends('layouts.admin')
@section('title', 'Email Templates')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Email Templates</h1>
        <p class="text-sm text-gray-500">Manage notification email templates and placeholders</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        4 templates available
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500 bg-gray-50 border-b">
                <th class="px-4 py-3 font-medium">Template</th>
                <th class="px-4 py-3 font-medium">Subject</th>
                <th class="px-4 py-3 font-medium">Preview</th>
                <th class="px-4 py-3 font-medium">Status</th>
                <th class="px-4 py-3 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($templates as $key => $tpl)
            <tr class="border-t hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            @if($key === 'welcome_company')
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            @elseif($key === 'package_assigned')
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @elseif($key === 'quotation_notification')
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            @endif
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">{{ $tpl['label'] }}</div>
                            <div class="text-xs text-gray-400">{{ $key }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 text-gray-600">
                    {{ $tpl['subject'] ?: '—' }}
                </td>
                <td class="px-4 py-4 text-gray-400 max-w-xs">
                    <span class="truncate block">{{ Str::limit(strip_tags($tpl['body'] ?: 'No content yet'), 80) }}</span>
                </td>
                <td class="px-4 py-4">
                    @if($tpl['is_enabled'])
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Enabled
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            Disabled
                        </span>
                    @endif
                </td>
                <td class="px-4 py-4">
                    <a href="/admin/email-templates/{{ $key }}/edit"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4 bg-gray-50 rounded-xl p-4 border border-gray-200">
    <div class="flex items-start gap-2 text-sm text-gray-500">
        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <span class="font-medium text-gray-700">Available placeholders:</span>
            <code class="mx-1 px-1.5 py-0.5 bg-white rounded border text-xs text-indigo-600">{!! '{{company_name}}' !!}</code>
            <code class="mx-1 px-1.5 py-0.5 bg-white rounded border text-xs text-indigo-600">{!! '{{client_name}}' !!}</code>
            <code class="mx-1 px-1.5 py-0.5 bg-white rounded border text-xs text-indigo-600">{!! '{{quote_number}}' !!}</code>
            <code class="mx-1 px-1.5 py-0.5 bg-white rounded border text-xs text-indigo-600">{!! '{{total}}' !!}</code>
        </div>
    </div>
</div>
@endsection
