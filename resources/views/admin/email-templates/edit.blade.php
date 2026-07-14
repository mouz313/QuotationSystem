@extends('layouts.admin')
@section('title', 'Edit ' . $label)
@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="/admin/email-templates" class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit: {{ $label }}</h1>
            <p class="text-sm text-gray-500">Customize the email subject and body for this template</p>
        </div>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <span class="font-mono text-xs">{{ $template }}</span>
    </div>
</div>

<form method="POST" action="/admin/email-templates/{{ $template }}">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Form --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow p-6 space-y-5">
                {{-- Toggle --}}
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-700">Template Visibility</div>
                            <div class="text-xs text-gray-400">Enable or disable this email template</div>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_enabled" value="1" {{ ($data['is_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                {{-- Subject --}}
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        Email Subject
                    </label>
                    <input type="text" name="subject" value="{{ old('subject', $data['subject'] ?? '') }}" required
                        placeholder="e.g. Welcome to {{company_name}}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition">
                    @error('subject')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Body --}}
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Email Body (HTML)
                    </label>
                    <textarea name="body" rows="16" required
                        placeholder="<h1>Welcome!</h1><p>Hi {{client_name}},</p>..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none font-mono text-sm leading-relaxed transition">{{ old('body', $data['body'] ?? '') }}</textarea>
                    @error('body')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Template
                </button>
                <a href="/admin/email-templates"
                   class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                    Cancel
                </a>
            </div>
        </div>

        {{-- Sidebar: Placeholders --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-4">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Placeholders
                </h3>
                <p class="text-xs text-gray-400 mb-4">Click to copy. These are replaced with real values when the email is sent.</p>
                <div class="space-y-2">
                    <button type="button" onclick="navigator.clipboard.writeText('{{company_name}}')"
                        class="w-full flex items-center gap-3 px-3 py-2.5 bg-gray-50 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition-colors group text-left">
                        <code class="text-xs font-mono font-semibold text-indigo-600">{!! '{{company_name}}' !!}</code>
                        <svg class="w-3.5 h-3.5 ml-auto text-gray-300 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                    <button type="button" onclick="navigator.clipboard.writeText('{{client_name}}')"
                        class="w-full flex items-center gap-3 px-3 py-2.5 bg-gray-50 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition-colors group text-left">
                        <code class="text-xs font-mono font-semibold text-indigo-600">{!! '{{client_name}}' !!}</code>
                        <svg class="w-3.5 h-3.5 ml-auto text-gray-300 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                    <button type="button" onclick="navigator.clipboard.writeText('{{quote_number}}')"
                        class="w-full flex items-center gap-3 px-3 py-2.5 bg-gray-50 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition-colors group text-left">
                        <code class="text-xs font-mono font-semibold text-indigo-600">{!! '{{quote_number}}' !!}</code>
                        <svg class="w-3.5 h-3.5 ml-auto text-gray-300 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                    <button type="button" onclick="navigator.clipboard.writeText('{{total}}')"
                        class="w-full flex items-center gap-3 px-3 py-2.5 bg-gray-50 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition-colors group text-left">
                        <code class="text-xs font-mono font-semibold text-indigo-600">{!! '{{total}}' !!}</code>
                        <svg class="w-3.5 h-3.5 ml-auto text-gray-300 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <div class="text-xs text-amber-700">
                        <p class="font-semibold mb-1">HTML Content</p>
                        <p>You can use full HTML tags like <code class="bg-amber-100 px-1 rounded">&lt;h1&gt;</code>, <code class="bg-amber-100 px-1 rounded">&lt;p&gt;</code>, <code class="bg-amber-100 px-1 rounded">&lt;a&gt;</code> in the body field.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
