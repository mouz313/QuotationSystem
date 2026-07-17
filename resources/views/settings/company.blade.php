@extends('layouts.app')
@section('title', 'Company Settings')
@section('content')

<div class="fade-in" style="max-width:48rem;">
    <x-page-header title="Company Settings" subtitle="Manage your company profile and details" />

    <x-card>
        <div class="tab-group" style="border-bottom:1px solid var(--surface-100);">
            <button type="button" role="tab" onclick="switchTab('general')" id="tab-general" class="tab-pill active">General</button>
            <button type="button" role="tab" onclick="switchTab('account')" id="tab-account" class="tab-pill">Account Details</button>
            <button type="button" role="tab" onclick="switchTab('subscription')" id="tab-subscription" class="tab-pill">Subscription</button>
            <button type="button" role="tab" onclick="switchTab('details')" id="tab-details" class="tab-pill">Details</button>
        </div>

        <div id="tab-content-general" class="tab-content" style="padding:1.5rem;">
            <form method="POST" action="/company/settings" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <x-form-input label="Company Name" name="name" :value="old('name', $company->name)" :required="true" :error="$errors->first('name')" />
                    <x-form-input label="Company Email" name="email" type="email" :value="old('email', $company->email)" :required="true" :error="$errors->first('email')" />
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <x-form-input label="Phone" name="phone" :value="old('phone', $company->phone)" />
                    <x-form-input label="Website" name="website" type="url" :value="old('website', $company->website)" placeholder="https://..." />
                </div>
                <x-form-textarea label="Address" name="address" :value="old('address', $company->address)" :rows="2" />

                <div>
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Company Logo</label>
                    <p style="font-size:.75rem;color:var(--surface-400);margin-bottom:.5rem;">Upload a logo to display in the sidebar. Recommended: 200x200px, max 2MB.</p>
                    <div style="display:flex;align-items:center;gap:1rem;">
                        @if($company->logo_url)
                            <div style="position:relative;">
                                <img src="{{ $company->logo_url }}" alt="Company logo" style="width:4rem;height:4rem;border-radius:.5rem;object-fit:cover;border:1px solid var(--surface-200);">
                                <label style="position:absolute;top:-.375rem;right:-.375rem;width:1.25rem;height:1.25rem;background:var(--danger-600);color:white;border-radius:9999px;display:flex;align-items:center;justify-content:center;cursor:pointer;" title="Remove logo">
                                    <input type="checkbox" name="remove_logo" value="1" class="hidden" onchange="if(this.checked){document.getElementById('currentLogo').style.display='none'}">
                                    <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </label>
                            </div>
                        @endif
                        <div style="flex:1;">
                            <input type="file" name="logo" accept="image/*" onchange="previewLogo(event)" style="width:100%;font-size:.8125rem;color:var(--surface-500);">
                            <div id="logoPreview" style="margin-top:.5rem;display:none;">
                                <img style="width:4rem;height:4rem;border-radius:.5rem;object-fit:cover;border:1px solid var(--surface-200);">
                            </div>
                            @if($company->logo_url)
                            <p style="font-size:.625rem;color:var(--surface-400);margin-top:.25rem;">Click the <span style="color:var(--danger-600);font-weight:700;">x</span> on the logo to remove it, or upload a new one to replace it.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <x-form-textarea label="Default Terms & Conditions" name="default_terms" :value="old('default_terms', $company->default_terms)" :rows="3" placeholder="Auto-filled when creating a new quotation" />

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Brand Color</label>
                        <p style="font-size:.75rem;color:var(--surface-400);margin-bottom:.5rem;">Used in PDF quotations</p>
                        <div style="display:flex;gap:.5rem;">
                            <input type="color" name="brand_color" value="{{ old('brand_color', $company->brand_color ?? '#4f46e5') }}" style="width:2.5rem;height:2.5rem;padding:2px;border:1px solid var(--surface-200);border-radius:.375rem;cursor:pointer;">
                            <input type="text" name="brand_color_hex" value="{{ old('brand_color', $company->brand_color ?? '#4f46e5') }}" oninput="document.querySelector('input[name=brand_color]').value=this.value" style="flex:1;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;outline:none;" placeholder="#4f46e5">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Brand Font</label>
                        <p style="font-size:.75rem;color:var(--surface-400);margin-bottom:.5rem;">Used in PDF quotations</p>
                        <x-form-select name="brand_font" :value="$company->brand_font ?? 'Helvetica'" :options="['Helvetica' => 'Helvetica', 'Arial' => 'Arial', 'Times' => 'Times', 'Courier' => 'Courier', 'DejaVu Sans' => 'DejaVu Sans', 'DejaVu Serif' => 'DejaVu Serif']" />
                    </div>
                </div>
                <button type="submit" class="btn btn-brand" style="align-self:flex-start;">Save Changes</button>
            </form>
        </div>

        <div id="tab-content-account" class="tab-content" style="padding:1.5rem;display:none;">
            <form method="POST" action="/company/settings" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf @method('PUT')
                <input type="hidden" name="name" value="{{ $company->name }}">
                <input type="hidden" name="email" value="{{ $company->email }}">
                <div>
                    <h3 style="font-size:1rem;font-weight:600;color:var(--surface-800);margin-bottom:.25rem;">Account / Bank Details</h3>
                    <p style="font-size:.75rem;color:var(--surface-400);margin-bottom:.75rem;">This information will be displayed on your quotations and PDFs for client payments.</p>
                </div>
                <div>
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Account Details</label>
                    <p style="font-size:.75rem;color:var(--surface-400);margin-bottom:.5rem;">Enter your bank name, account holder, account number, IBAN, SWIFT/BIC, or any other payment details you want clients to see.</p>
                    <textarea name="account_details" rows="8" style="width:100%;padding:.75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;font-family:monospace;line-height:1.625;resize:vertical;transition:border-color .15s,box-shadow .15s;focus:border-color:var(--brand-500);focus:box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);" placeholder="Bank Name: XYZ Bank&#10;Account Holder: Your Company LLC&#10;Account Number: 1234567890&#10;IBAN: PK1234567890123456789012&#10;SWIFT/BIC: XYZBPKKA&#10;Branch: Main Branch">{{ old('account_details', $company->account_details) }}</textarea>
                </div>
                <button type="submit" class="btn btn-brand" style="align-self:flex-start;">Save Account Details</button>
            </form>
        </div>

        <div id="tab-content-subscription" class="tab-content" style="padding:1.5rem;display:none;">
            @if($activePackage)
                @php $pkg = $activePackage->package; @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
                    <div>
                        <h2 style="font-size:1rem;font-weight:600;color:var(--surface-800);">Current Plan</h2>
                        <p style="font-size:.8125rem;color:var(--surface-500);">Your active subscription details</p>
                    </div>
                    <span class="badge badge-draft" style="font-size:.8125rem;font-weight:600;">{{ $pkg->name }}</span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                    <div style="font-size:.8125rem;">
                        <span style="color:var(--surface-500);">Price:</span>
                        <span style="margin-left:.5rem;font-weight:600;color:var(--surface-800);">${{ number_format($pkg->price, 2) }}/{{ $pkg->duration_days }}d</span>
                    </div>
                    <div style="font-size:.8125rem;">
                        <span style="color:var(--surface-500);">Status:</span>
                        <span style="margin-left:.5rem;"><span class="badge badge-active">{{ $activePackage->status }}</span></span>
                    </div>
                    <div style="font-size:.8125rem;">
                        <span style="color:var(--surface-500);">Started:</span>
                        <span style="margin-left:.5rem;font-weight:600;color:var(--surface-800);">{{ $activePackage->start_date->format('M d, Y') }}</span>
                    </div>
                    <div style="font-size:.8125rem;">
                        <span style="color:var(--surface-500);">Expires:</span>
                        <span style="margin-left:.5rem;font-weight:600;color:var(--surface-800);">{{ $activePackage->end_date->format('M d, Y') }}</span>
                    </div>
                </div>

                <div style="border-top:1px solid var(--surface-100);padding-top:1.5rem;">
                    <h3 style="font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:1rem;">Usage</h3>
                    <div style="display:flex;flex-direction:column;gap:1rem;">
                        @php
                            $usages = [
                                ['label' => 'Users', 'current' => $userCount, 'max' => $pkg->max_users],
                                ['label' => 'Clients', 'current' => $clientCount, 'max' => $pkg->max_clients],
                                ['label' => 'Quotations', 'current' => $quotationCount, 'max' => $pkg->max_quotations],
                            ];
                        @endphp
                        @foreach($usages as $u)
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:.25rem;">
                                <span style="font-size:.8125rem;color:var(--surface-500);">{{ $u['label'] }}</span>
                                <span style="font-size:.8125rem;font-weight:600;color:var(--surface-800);">{{ $u['current'] }} / {{ $u['max'] }}</span>
                            </div>
                            <div style="width:100%;height:.5rem;background:var(--surface-100);border-radius:9999px;">
                                <div style="height:.5rem;border-radius:9999px;background:var(--brand-600);transition:width .3s;" role="progressbar" style="width:{{ $u['max'] > 0 ? min(100, ($u['current'] / $u['max']) * 100) : 0 }}%;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div style="border-top:1px solid var(--surface-100);padding-top:1.5rem;margin-top:1.5rem;">
                    <h3 style="font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:1rem;">Available Packages</h3>
                    <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:1rem;">
                        @foreach($packages as $p)
                            <div style="padding:1rem;border:1px solid {{ $p->id === $pkg->id ? 'var(--brand-500)' : 'var(--surface-200)' }};border-radius:.5rem;{{ $p->id === $pkg->id ? 'background:var(--brand-50);box-shadow:0 0 0 3px oklch(0.55 0.17 275 / .1);' : '' }}">
                                <div style="font-size:.8125rem;font-weight:600;color:{{ $p->id === $pkg->id ? 'var(--brand-700)' : 'var(--surface-800)' }};">{{ $p->name }}</div>
                                <div style="font-size:.75rem;color:var(--surface-500);margin-top:.25rem;">${{ number_format($p->price, 2) }}/{{ $p->duration_days }}d</div>
                                <ul style="font-size:.75rem;color:var(--surface-500);margin-top:.5rem;display:flex;flex-direction:column;gap:.25rem;">
                                    <li>{{ $p->max_users }} users</li>
                                    <li>{{ $p->max_clients }} clients</li>
                                    <li>{{ $p->max_quotations }} quotations</li>
                                </ul>
                                @if($p->id !== $pkg->id)
                                    <div style="margin-top:.5rem;font-size:.75rem;color:var(--surface-400);">Contact admin to switch</div>
                                @else
                                    <div style="margin-top:.5rem;font-size:.75rem;color:var(--brand-600);font-weight:600;">Current plan</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <h2 style="font-size:1rem;font-weight:600;color:var(--surface-800);margin-bottom:.25rem;">Subscription</h2>
                <p style="font-size:.8125rem;color:var(--surface-500);margin-bottom:1rem;">Your current plan and usage</p>
                <p style="color:var(--surface-500);font-size:.8125rem;">No active subscription. Contact admin to assign a package.</p>
            @endif
        </div>

        <div id="tab-content-details" class="tab-content" style="padding:1.5rem;display:none;">
            <h2 style="font-size:1rem;font-weight:600;color:var(--surface-800);margin-bottom:.25rem;">Company Details</h2>
            <p style="font-size:.8125rem;color:var(--surface-500);margin-bottom:1rem;">Read-only information</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:.8125rem;">
                <div>
                    <span style="color:var(--surface-500);">Status:</span>
                    <span style="margin-left:.5rem;"><span class="badge badge-{{ $company->status }}">{{ ucfirst($company->status) }}</span></span>
                </div>
                <div>
                    <span style="color:var(--surface-500);">Registered:</span>
                    <span style="margin-left:.5rem;font-weight:600;color:var(--surface-800);">{{ $company->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </x-card>
</div>

<script>
function previewLogo(e) {
    const file = e.target.files[0];
    if (!file) return;
    const preview = document.getElementById('logoPreview');
    preview.style.display = 'block';
    preview.querySelector('img').src = URL.createObjectURL(file);
}

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-pill').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-content-' + tab).style.display = 'block';
    document.getElementById('tab-' + tab).classList.add('active');
}
</script>

@endsection
