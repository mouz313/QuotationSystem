@extends('layouts.admin')
@section('title', 'System Settings')
@section('content')
<div class="fade-in">
    <x-page-header title="System Settings" subtitle="Configure your platform appearance and integrations" />

    @php
        $tabs = [
            'general' => 'General',
            'social'  => 'Social Media',
            'pusher'  => 'Pusher / Notifications',
            'email'   => 'Email / SMTP',
        ];
        $active = request('tab', 'general');
    @endphp

    <div class="d-card" style="overflow:hidden;">
        {{-- Tab Headers --}}
        <div style="display:flex;border-bottom:1px solid var(--surface-200);">
            @foreach($tabs as $key => $label)
                <a href="?tab={{ $key }}"
                   style="padding:.75rem 1.5rem;font-size:.8125rem;font-weight:600;transition:all .15s;text-decoration:none;{{ $active === $key ? 'color:var(--brand-600);border-bottom:2px solid var(--brand-600);background:var(--brand-50);' : 'color:var(--surface-500);' }}"
                   onmouseover="this.style.color='var(--surface-700)'"
                   onmouseout="this.style.color='{{ $active === $key ? "var(--brand-600)" : "var(--surface-500)" }}'">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="d-card-body">

            {{-- ═══════════════ GENERAL TAB ═══════════════ --}}
            @if($active === 'general')
                <form method="POST" action="/admin/settings/general" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1.5rem;">
                    @csrf @method('PUT')
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                        <x-form-input label="App Name" name="app_name" :value="$settings['general']['app_name'] ?? 'QuotationSystem'" required />
                        <x-form-input label="Page Title" name="app_title" :value="$settings['general']['app_title'] ?? 'SaaS Quotation System'" required />
                    </div>
                    <x-form-input label="App Description" name="app_description" :value="$settings['general']['app_description'] ?? ''"
                        placeholder="Short description of your platform" />
                    <x-form-textarea label="Footer Text" name="footer_text" :value="$settings['general']['footer_text'] ?? ''" rows="2"
                        placeholder="© 2026 Your Company. All rights reserved." />
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                        <div>
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Logo</label>
                            @if(!empty($settings['general']['logo']))
                                <div style="margin-bottom:.5rem;"><img src="/storage/{{ $settings['general']['logo'] }}" style="height:2.5rem;"></div>
                            @endif
                            <input type="file" name="logo" accept="image/*"
                                style="width:100%;font-size:.8125rem;color:var(--surface-500);">
                            <p style="font-size:.7rem;color:var(--surface-400);margin-top:.25rem;">PNG, JPG, or SVG. Max 2MB.</p>
                        </div>
                        <div>
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Favicon</label>
                            @if(!empty($settings['general']['favicon']))
                                <div style="margin-bottom:.5rem;"><img src="/storage/{{ $settings['general']['favicon'] }}" style="height:2rem;"></div>
                            @endif
                            <input type="file" name="favicon" accept="image/*"
                                style="width:100%;font-size:.8125rem;color:var(--surface-500);">
                            <p style="font-size:.7rem;color:var(--surface-400);margin-top:.25rem;">PNG, ICO, or SVG. Max 1MB.</p>
                        </div>
                    </div>
                    <button class="btn btn-brand" style="align-self:flex-start;">Save General Settings</button>
                </form>

            {{-- ═══════════════ SOCIAL TAB ═══════════════ --}}
            @elseif($active === 'social')
                <form method="POST" action="/admin/settings/social" style="display:flex;flex-direction:column;gap:1.25rem;">
                    @csrf @method('PUT')
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                        <div>
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">
                                <span style="color:var(--info-600);">Facebook</span> URL
                            </label>
                            <input type="url" name="facebook" value="{{ $settings['social']['facebook'] ?? '' }}"
                                placeholder="https://facebook.com/yourpage"
                                style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">
                                <span style="color:var(--info-500);">Twitter / X</span> URL
                            </label>
                            <input type="url" name="twitter" value="{{ $settings['social']['twitter'] ?? '' }}"
                                placeholder="https://twitter.com/yourhandle"
                                style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">
                                <span style="color:var(--brand-600);">LinkedIn</span> URL
                            </label>
                            <input type="url" name="linkedin" value="{{ $settings['social']['linkedin'] ?? '' }}"
                                placeholder="https://linkedin.com/company/..."
                                style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">
                                <span style="color:var(--danger-500);">Instagram</span> URL
                            </label>
                            <input type="url" name="instagram" value="{{ $settings['social']['instagram'] ?? '' }}"
                                placeholder="https://instagram.com/yourhandle"
                                style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">
                                <span style="color:var(--danger-600);">YouTube</span> URL
                            </label>
                            <input type="url" name="youtube" value="{{ $settings['social']['youtube'] ?? '' }}"
                                placeholder="https://youtube.com/@yourchannel"
                                style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">
                                <span style="color:var(--surface-800);">GitHub</span> URL
                            </label>
                            <input type="url" name="github" value="{{ $settings['social']['github'] ?? '' }}"
                                placeholder="https://github.com/yourorg"
                                style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                        </div>
                    </div>
                    <button class="btn btn-brand" style="align-self:flex-start;">Save Social Links</button>
                </form>

            {{-- ═══════════════ PUSHER TAB ═══════════════ --}}
            @elseif($active === 'pusher')
                <form method="POST" action="/admin/settings/pusher" style="display:flex;flex-direction:column;gap:1.25rem;">
                    @csrf @method('PUT')
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;">
                            <input type="checkbox" name="pusher_enabled" value="1"
                                {{ ($settings['pusher']['pusher_enabled'] ?? '') === '1' ? 'checked' : '' }}
                                style="position:absolute;opacity:0;width:0;height:0;">
                            <div style="width:2.75rem;height:1.5rem;background:var(--surface-200);border-radius:999px;transition:background .2s;"></div>
                        </label>
                        <span style="font-size:.8125rem;font-weight:600;color:var(--surface-700);">Enable Pusher Real-time Notifications</span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                        <x-form-input label="App ID" name="pusher_app_id" :value="$settings['pusher']['pusher_app_id'] ?? ''" />
                        <x-form-input label="App Key" name="pusher_app_key" :value="$settings['pusher']['pusher_app_key'] ?? ''" />
                        <x-form-input label="App Secret" name="pusher_app_secret" type="password" :value="$settings['pusher']['pusher_app_secret'] ?? ''" />
                        <x-form-select name="pusher_app_cluster" label="Cluster"
                            :value="$settings['pusher']['pusher_app_cluster'] ?? ''"
                            placeholder="Select cluster"
                            :options="['us1' => 'us1', 'us2' => 'us2', 'eu1' => 'eu1', 'eu2' => 'eu2', 'ap1' => 'ap1', 'ap2' => 'ap2', 'au1' => 'au1', 'sa1' => 'sa1', 'ca1' => 'ca1', 'jp1' => 'jp1']" />
                    </div>
                    <button class="btn btn-brand" style="align-self:flex-start;">Save Pusher Settings</button>
                </form>

            {{-- ═══════════════ EMAIL TAB ═══════════════ --}}
            @elseif($active === 'email')
                <form method="POST" action="/admin/settings/email" style="display:flex;flex-direction:column;gap:1.25rem;">
                    @csrf @method('PUT')
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                        <x-form-select name="mail_driver" label="Mail Driver"
                            :value="$settings['email']['mail_driver'] ?? 'smtp'"
                            :options="['smtp' => 'SMTP', 'sendmail' => 'SENDMAIL', 'mailgun' => 'MAILGUN', 'ses' => 'SES', 'log' => 'LOG']" />
                        <x-form-select name="mail_encryption" label="Encryption"
                            :value="$settings['email']['mail_encryption'] ?? 'tls'"
                            :options="['tls' => 'TLS', 'ssl' => 'SSL']" />
                        <x-form-input label="SMTP Host" name="mail_host" :value="$settings['email']['mail_host'] ?? ''"
                            placeholder="smtp.mailgun.org" />
                        <x-form-input label="SMTP Port" name="mail_port" type="number" :value="$settings['email']['mail_port'] ?? '587'" />
                        <x-form-input label="Username" name="mail_username" :value="$settings['email']['mail_username'] ?? ''" />
                        <x-form-input label="Password" name="mail_password" type="password" :value="$settings['email']['mail_password'] ?? ''" />
                        <x-form-input label="From Address" name="mail_from_address" type="email" :value="$settings['email']['mail_from_address'] ?? ''"
                            placeholder="noreply@yourdomain.com" />
                        <x-form-input label="From Name" name="mail_from_name" :value="$settings['email']['mail_from_name'] ?? ''"
                            placeholder="QuotationSystem" />
                    </div>
                    <button class="btn btn-brand" style="align-self:flex-start;">Save Email Settings</button>
                </form>

                <hr style="margin:2rem 0;border:none;border-top:1px solid var(--surface-200);">

                <div style="background:var(--surface-50);border-radius:.75rem;padding:1.5rem;border:1px solid var(--surface-200);">
                    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
                        <svg style="width:1.25rem;height:1.25rem;color:var(--surface-600);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <h3 style="font-size:1rem;font-weight:700;color:var(--surface-800);">Send Test Email</h3>
                    </div>
                    <p style="font-size:.8125rem;color:var(--surface-500);margin-bottom:1rem;">Verify your email settings by sending a test message. Save your settings first before testing.</p>
                    <form method="POST" action="/admin/settings/email/test" style="display:flex;gap:.75rem;align-items:flex-end;">
                        @csrf
                        <div style="flex:1;">
                            <x-form-input label="Recipient Email" name="test_email" type="email" placeholder="you@example.com" required />
                        </div>
                        <button class="btn btn-brand" style="white-space:nowrap;padding-bottom:.625rem;">
                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Send Test
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
