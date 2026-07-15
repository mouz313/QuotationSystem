@extends('layouts.admin')
@section('title', 'System Settings')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
    <p class="text-sm text-gray-500">Configure your platform appearance and integrations</p>
</div>

@php
    $tabs = [
        'general' => 'General',
        'social'  => 'Social Media',
        'pusher'  => 'Pusher / Notifications',
        'email'   => 'Email / SMTP',
    ];
    $active = request('tab', 'general');
@endphp

<div class="bg-white rounded-xl shadow overflow-hidden">
    {{-- Tab Headers --}}
    <div class="flex border-b">
        @foreach($tabs as $key => $label)
            <a href="?tab={{ $key }}"
               class="px-6 py-3 text-sm font-medium transition {{ $active === $key ? 'text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="p-6">

        {{-- ═══════════════ GENERAL TAB ═══════════════ --}}
        @if($active === 'general')
            <form method="POST" action="/admin/settings/general" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">App Name</label>
                        <input type="text" name="app_name" value="{{ $settings['general']['app_name'] ?? 'QuotationSystem' }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Page Title</label>
                        <input type="text" name="app_title" value="{{ $settings['general']['app_title'] ?? 'SaaS Quotation System' }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">App Description</label>
                    <input type="text" name="app_description" value="{{ $settings['general']['app_description'] ?? '' }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Short description of your platform">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text</label>
                    <textarea name="footer_text" rows="2"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="© 2026 Your Company. All rights reserved.">{{ $settings['general']['footer_text'] ?? '' }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        @if(!empty($settings['general']['logo']))
                            <div class="mb-2"><img src="/storage/{{ $settings['general']['logo'] }}" class="h-10"></div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, or SVG. Max 2MB.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                        @if(!empty($settings['general']['favicon']))
                            <div class="mb-2"><img src="/storage/{{ $settings['general']['favicon'] }}" class="h-8"></div>
                        @endif
                        <input type="file" name="favicon" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-400 mt-1">PNG, ICO, or SVG. Max 1MB.</p>
                    </div>
                </div>
                <button class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Save General Settings</button>
            </form>

        {{-- ═══════════════ SOCIAL TAB ═══════════════ --}}
        @elseif($active === 'social')
            <form method="POST" action="/admin/settings/social" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="text-blue-600">Facebook</span> URL
                        </label>
                        <input type="url" name="facebook" value="{{ $settings['social']['facebook'] ?? '' }}"
                            placeholder="https://facebook.com/yourpage"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="text-sky-500">Twitter / X</span> URL
                        </label>
                        <input type="url" name="twitter" value="{{ $settings['social']['twitter'] ?? '' }}"
                            placeholder="https://twitter.com/yourhandle"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="text-blue-700">LinkedIn</span> URL
                        </label>
                        <input type="url" name="linkedin" value="{{ $settings['social']['linkedin'] ?? '' }}"
                            placeholder="https://linkedin.com/company/..."
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="text-pink-600">Instagram</span> URL
                        </label>
                        <input type="url" name="instagram" value="{{ $settings['social']['instagram'] ?? '' }}"
                            placeholder="https://instagram.com/yourhandle"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="text-red-600">YouTube</span> URL
                        </label>
                        <input type="url" name="youtube" value="{{ $settings['social']['youtube'] ?? '' }}"
                            placeholder="https://youtube.com/@yourchannel"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="text-gray-800">GitHub</span> URL
                        </label>
                        <input type="url" name="github" value="{{ $settings['social']['github'] ?? '' }}"
                            placeholder="https://github.com/yourorg"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <button class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Save Social Links</button>
            </form>

        {{-- ═══════════════ PUSHER TAB ═══════════════ --}}
        @elseif($active === 'pusher')
            <form method="POST" action="/admin/settings/pusher" class="space-y-5">
                @csrf @method('PUT')
                <div class="flex items-center gap-3 mb-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="pusher_enabled" value="1"
                            {{ ($settings['pusher']['pusher_enabled'] ?? '') === '1' ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                    <span class="text-sm font-medium text-gray-700">Enable Pusher Real-time Notifications</span>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">App ID</label>
                        <input type="text" name="pusher_app_id" value="{{ $settings['pusher']['pusher_app_id'] ?? '' }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">App Key</label>
                        <input type="text" name="pusher_app_key" value="{{ $settings['pusher']['pusher_app_key'] ?? '' }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">App Secret</label>
                        <input type="password" name="pusher_app_secret" value="{{ $settings['pusher']['pusher_app_secret'] ?? '' }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cluster</label>
                        <select name="pusher_app_cluster"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Select cluster</option>
                            @foreach(['us1','us2','eu1','eu2','ap1','ap2','au1','sa1','ca1','jp1'] as $cluster)
                                <option value="{{ $cluster }}" {{ ($settings['pusher']['pusher_app_cluster'] ?? '') === $cluster ? 'selected' : '' }}>{{ $cluster }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Save Pusher Settings</button>
            </form>

        {{-- ═══════════════ EMAIL TAB ═══════════════ --}}
        @elseif($active === 'email')
            <form method="POST" action="/admin/settings/email" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mail Driver</label>
                        <select name="mail_driver"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            @foreach(['smtp','sendmail','mailgun','ses','log'] as $driver)
                                <option value="{{ $driver }}" {{ ($settings['email']['mail_driver'] ?? 'smtp') === $driver ? 'selected' : '' }}>{{ strtoupper($driver) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                        <select name="mail_encryption"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="tls" {{ ($settings['email']['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['email']['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                        <input type="text" name="mail_host" value="{{ $settings['email']['mail_host'] ?? '' }}"
                            placeholder="smtp.mailgun.org"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                        <input type="number" name="mail_port" value="{{ $settings['email']['mail_port'] ?? '587' }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="mail_username" value="{{ $settings['email']['mail_username'] ?? '' }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="mail_password" value="{{ $settings['email']['mail_password'] ?? '' }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                        <input type="email" name="mail_from_address" value="{{ $settings['email']['mail_from_address'] ?? '' }}"
                            placeholder="noreply@yourdomain.com"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                        <input type="text" name="mail_from_name" value="{{ $settings['email']['mail_from_name'] ?? '' }}"
                            placeholder="QuotationSystem"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <button class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Save Email Settings</button>
            </form>

            <hr class="my-8 border-gray-200">

            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <h3 class="text-base font-semibold text-gray-800">Send Test Email</h3>
                </div>
                <p class="text-sm text-gray-500 mb-4">Verify your email settings by sending a test message. Save your settings first before testing.</p>
                <form method="POST" action="/admin/settings/email/test" class="flex gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Email</label>
                        <input type="email" name="test_email" placeholder="you@example.com" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <button class="px-5 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 font-medium flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Send Test
                    </button>
                </form>
            </div>
        @endif

    </div>
</div>
@endsection
