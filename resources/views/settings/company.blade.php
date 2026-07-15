@extends('layouts.app')
@section('title', 'Company Settings')
@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Company Settings</h1>
        <p class="text-sm text-gray-500">Manage your company profile and details</p>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex" role="tablist">
                <button type="button" role="tab" onclick="switchTab('general')" id="tab-general" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600">General</button>
                <button type="button" role="tab" onclick="switchTab('subscription')" id="tab-subscription" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Subscription</button>
                <button type="button" role="tab" onclick="switchTab('details')" id="tab-details" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Details</button>
            </nav>
        </div>

        <div id="tab-content-general" class="tab-content p-6">
            <form method="POST" action="/company/settings" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                        <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" name="website" value="{{ old('website', $company->website) }}"
                            placeholder="https://..."
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('address', $company->address) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Logo</label>
                    <p class="text-xs text-gray-400 mb-2">Upload a logo to display in the sidebar. Recommended: 200x200px, max 2MB.</p>
                    <div class="flex items-center gap-4">
                        @if($company->logo_url)
                            <img src="{{ $company->logo_url }}" alt="Company logo" class="w-16 h-16 rounded-lg object-cover border">
                        @endif
                        <div class="flex-1">
                            <input type="file" name="logo" accept="image/*" onchange="previewLogo(event)"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <div id="logoPreview" class="mt-2 hidden">
                                <img class="w-16 h-16 rounded-lg object-cover border">
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Terms & Conditions</label>
                    <p class="text-xs text-gray-400 mb-1">Auto-filled when creating a new quotation</p>
                    <textarea name="default_terms" rows="3"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('default_terms', $company->default_terms) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand Color</label>
                        <p class="text-xs text-gray-400 mb-1">Used in PDF quotations</p>
                        <div class="flex gap-2">
                            <input type="color" name="brand_color" value="{{ old('brand_color', $company->brand_color ?? '#4f46e5') }}"
                                class="w-10 h-10 p-0.5 border rounded cursor-pointer">
                            <input type="text" name="brand_color_hex" value="{{ old('brand_color', $company->brand_color ?? '#4f46e5') }}"
                                oninput="document.querySelector('input[name=brand_color]').value=this.value"
                                class="flex-1 px-3 py-2 border rounded-lg text-sm outline-none" placeholder="#4f46e5">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand Font</label>
                        <p class="text-xs text-gray-400 mb-1">Used in PDF quotations</p>
                        <select name="brand_font" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                            @foreach(['Helvetica', 'Arial', 'Times', 'Courier', 'DejaVu Sans', 'DejaVu Serif'] as $font)
                                <option value="{{ $font }}" {{ ($company->brand_font ?? 'Helvetica') === $font ? 'selected' : '' }}>{{ $font }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Save Changes</button>
            </form>
        </div>

        <div id="tab-content-subscription" class="tab-content p-6 hidden">
            @if($activePackage)
                @php $pkg = $activePackage->package; @endphp
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold">Current Plan</h2>
                        <p class="text-sm text-gray-500">Your active subscription details</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold bg-indigo-100 text-indigo-700 rounded-lg">{{ $pkg->name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="text-sm">
                        <span class="text-gray-500">Price:</span>
                        <span class="ml-2 font-medium">${{ number_format($pkg->price, 2) }}/{{ $pkg->duration_days }}d</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-gray-500">Status:</span>
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">{{ $activePackage->status }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-gray-500">Started:</span>
                        <span class="ml-2 font-medium">{{ $activePackage->start_date->format('M d, Y') }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-gray-500">Expires:</span>
                        <span class="ml-2 font-medium">{{ $activePackage->end_date->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Usage</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">Users</span>
                                <span class="font-medium">{{ $userCount }} / {{ $pkg->max_users }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pkg->max_users > 0 ? min(100, ($userCount / $pkg->max_users) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">Clients</span>
                                <span class="font-medium">{{ $clientCount }} / {{ $pkg->max_clients }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pkg->max_clients > 0 ? min(100, ($clientCount / $pkg->max_clients) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">Quotations</span>
                                <span class="font-medium">{{ $quotationCount }} / {{ $pkg->max_quotations }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pkg->max_quotations > 0 ? min(100, ($quotationCount / $pkg->max_quotations) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mt-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Available Packages</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($packages as $p)
                            <div class="border rounded-lg p-4 {{ $p->id === $pkg->id ? 'border-indigo-500 ring-2 ring-indigo-200 bg-indigo-50' : 'border-gray-200' }}">
                                <div class="text-sm font-semibold {{ $p->id === $pkg->id ? 'text-indigo-700' : 'text-gray-800' }}">{{ $p->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">${{ number_format($p->price, 2) }}/{{ $p->duration_days }}d</div>
                                <ul class="text-xs text-gray-500 mt-2 space-y-1">
                                    <li>{{ $p->max_users }} users</li>
                                    <li>{{ $p->max_clients }} clients</li>
                                    <li>{{ $p->max_quotations }} quotations</li>
                                </ul>
                                @if($p->id !== $pkg->id)
                                    <div class="mt-2 text-xs text-gray-400">Contact admin to switch</div>
                                @else
                                    <div class="mt-2 text-xs text-indigo-600 font-medium">Current plan</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <h2 class="text-lg font-semibold mb-1">Subscription</h2>
                <div class="text-sm text-gray-500 mb-4">Your current plan and usage</div>
                <p class="text-gray-500 text-sm">No active subscription. Contact admin to assign a package.</p>
            @endif
        </div>

        <div id="tab-content-details" class="tab-content p-6 hidden">
            <h2 class="text-lg font-semibold mb-1">Company Details</h2>
            <div class="text-sm text-gray-500 mb-4">Read-only information</div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Status:</span>
                    @if($company->status === 'active')
                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                    @elseif($company->status === 'blocked')
                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Blocked</span>
                    @else
                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Inactive</span>
                    @endif
                </div>
                <div><span class="text-gray-500">Registered:</span> <span class="ml-2">{{ $company->created_at->format('M d, Y') }}</span></div>
            </div>
        </div>
    </div>
</div>

<script>
function previewLogo(e) {
    const file = e.target.files[0];
    if (!file) return;
    const preview = document.getElementById('logoPreview');
    preview.classList.remove('hidden');
    preview.querySelector('img').src = URL.createObjectURL(file);
}

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-indigo-600', 'text-indigo-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('tab-content-' + tab).classList.remove('hidden');
    const btn = document.getElementById('tab-' + tab);
    btn.classList.remove('border-transparent', 'text-gray-500');
    btn.classList.add('border-indigo-600', 'text-indigo-600');
}
</script>
@endsection