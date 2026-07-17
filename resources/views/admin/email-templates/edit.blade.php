@extends('layouts.admin')
@section('title', 'Edit ' . $label)
@section('content')
<div class="fade-in">
    <x-page-header title="Edit: {{ $label }}" subtitle="Customize the email subject and body for this template" back="/admin/email-templates">
        <x:slot name="actions">
            <div style="display:flex;align-items:center;gap:.5rem;color:var(--surface-400);font-size:.8125rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span style="font-family:monospace;font-size:.75rem;">{{ $template }}</span>
            </div>
        </x:slot>
    </x-page-header>

    <form method="POST" action="/admin/email-templates/{{ $template }}">
        @csrf @method('PUT')
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
            {{-- Main Form --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                <div class="d-card">
                    <div class="d-card-body" style="display:flex;flex-direction:column;gap:1.25rem;">
                        {{-- Toggle --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:1rem;border-bottom:1px solid var(--surface-100);">
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="width:2.25rem;height:2.25rem;border-radius:.5rem;background:var(--surface-50);display:flex;align-items:center;justify-content:center;">
                                    <svg style="width:1rem;height:1rem;color:var(--surface-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <div>
                                    <div style="font-size:.8125rem;font-weight:600;color:var(--surface-700);">Template Visibility</div>
                                    <div style="font-size:.7rem;color:var(--surface-400);">Enable or disable this email template</div>
                                </div>
                            </div>
                            <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;">
                                <input type="checkbox" name="is_enabled" value="1" {{ ($data['is_enabled'] ?? '1') === '1' ? 'checked' : '' }} style="position:absolute;opacity:0;width:0;height:0;">
                                <div style="width:2.75rem;height:1.5rem;background:var(--surface-200);border-radius:999px;transition:background .2s;" id="toggleBg"></div>
                            </label>
                        </div>

                        {{-- Subject --}}
                        <x-form-input label="Email Subject" name="subject" :value="$data['subject'] ?? ''" required
                            placeholder="e.g. Welcome to {{company_name}}"
                            :error="$errors->first('subject')" />

                        {{-- Body --}}
                        <x-form-textarea label="Email Body (HTML)" name="body" :value="$data['body'] ?? ''" rows="16" required
                            placeholder="<h1>Welcome!</h1><p>Hi {{client_name}},</p>..."
                            :error="$errors->first('body')" />
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <button type="submit" class="btn btn-brand">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Template
                    </button>
                    <a href="/admin/email-templates" class="btn btn-ghost" style="border:1px solid var(--surface-200);">
                        Cancel
                    </a>
                </div>
            </div>

            {{-- Sidebar: Placeholders --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                <div class="d-card">
                    <div class="d-card-body">
                        <h3 style="display:flex;align-items:center;gap:.5rem;font-size:.8125rem;font-weight:700;color:var(--surface-800);margin-bottom:1rem;">
                            <svg style="width:1rem;height:1rem;color:var(--brand-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Placeholders
                        </h3>
                        <p style="font-size:.7rem;color:var(--surface-400);margin-bottom:1rem;">Click to copy. These are replaced with real values when the email is sent.</p>
                        <div style="display:flex;flex-direction:column;gap:.5rem;">
                            <button type="button" onclick="navigator.clipboard.writeText('{{company_name}}')"
                                style="width:100%;display:flex;align-items:center;gap:.75rem;padding:.625rem .75rem;background:var(--surface-50);border-radius:.5rem;text-align:left;transition:background .15s;border:none;cursor:pointer;font-size:.8125rem;"
                                onmouseover="this.style.background='var(--brand-50)'" onmouseout="this.style.background='var(--surface-50)'">
                                <code style="font-size:.75rem;font-family:monospace;font-weight:600;color:var(--brand-600);">{!! '{{company_name}}' !!}</code>
                                <svg style="width:.875rem;height:.875rem;margin-left:auto;color:var(--surface-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                            <button type="button" onclick="navigator.clipboard.writeText('{{client_name}}')"
                                style="width:100%;display:flex;align-items:center;gap:.75rem;padding:.625rem .75rem;background:var(--surface-50);border-radius:.5rem;text-align:left;transition:background .15s;border:none;cursor:pointer;font-size:.8125rem;"
                                onmouseover="this.style.background='var(--brand-50)'" onmouseout="this.style.background='var(--surface-50)'">
                                <code style="font-size:.75rem;font-family:monospace;font-weight:600;color:var(--brand-600);">{!! '{{client_name}}' !!}</code>
                                <svg style="width:.875rem;height:.875rem;margin-left:auto;color:var(--surface-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                            <button type="button" onclick="navigator.clipboard.writeText('{{quote_number}}')"
                                style="width:100%;display:flex;align-items:center;gap:.75rem;padding:.625rem .75rem;background:var(--surface-50);border-radius:.5rem;text-align:left;transition:background .15s;border:none;cursor:pointer;font-size:.8125rem;"
                                onmouseover="this.style.background='var(--brand-50)'" onmouseout="this.style.background='var(--surface-50)'">
                                <code style="font-size:.75rem;font-family:monospace;font-weight:600;color:var(--brand-600);">{!! '{{quote_number}}' !!}</code>
                                <svg style="width:.875rem;height:.875rem;margin-left:auto;color:var(--surface-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                            <button type="button" onclick="navigator.clipboard.writeText('{{total}}')"
                                style="width:100%;display:flex;align-items:center;gap:.75rem;padding:.625rem .75rem;background:var(--surface-50);border-radius:.5rem;text-align:left;transition:background .15s;border:none;cursor:pointer;font-size:.8125rem;"
                                onmouseover="this.style.background='var(--brand-50)'" onmouseout="this.style.background='var(--surface-50)'">
                                <code style="font-size:.75rem;font-family:monospace;font-weight:600;color:var(--brand-600);">{!! '{{total}}' !!}</code>
                                <svg style="width:.875rem;height:.875rem;margin-left:auto;color:var(--surface-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div style="background:var(--warning-50);border:1px solid var(--warning-100);border-radius:.75rem;padding:1rem;">
                    <div style="display:flex;align-items:flex-start;gap:.5rem;">
                        <svg style="width:1rem;height:1rem;margin-top:.125rem;flex-shrink:0;color:var(--warning-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        <div style="font-size:.7rem;color:var(--warning-700);">
                            <p style="font-weight:700;margin-bottom:.25rem;">HTML Content</p>
                            <p>You can use full HTML tags like <code style="background:var(--warning-100);padding:0 .25rem;border-radius:.25rem;">&lt;h1&gt;</code>, <code style="background:var(--warning-100);padding:0 .25rem;border-radius:.25rem;">&lt;p&gt;</code>, <code style="background:var(--warning-100);padding:0 .25rem;border-radius:.25rem;">&lt;a&gt;</code> in the body field.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
