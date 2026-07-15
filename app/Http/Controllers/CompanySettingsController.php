<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Package;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
    public function edit()
    {
        $company = Auth::user()->company;
        if (!$company) abort(404);

        $company->load('companyPackages.package');
        $activePackage = $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first();

        $userCount = $company->userCount();
        $clientCount = $company->clientCount();
        $quotationCount = $company->quotationCount();
        $packages = Package::where('is_active', true)->orderBy('price')->get();

        return view('settings.company', compact('company', 'activePackage', 'userCount', 'clientCount', 'quotationCount', 'packages'));
    }

    public function update(Request $request)
    {
        $company = Auth::user()->company;
        if (!$company) abort(404);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:companies,email,' . $company->id,
            'phone'         => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'website'       => 'nullable|url',
            'default_terms' => 'nullable|string',
            'brand_color'   => 'nullable|string|max:7',
            'brand_font'    => 'nullable|string|max:50',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($validated['logo']);
        }

        unset($validated['brand_color_hex']);

        $company->update($validated);

        return back()->with('success', 'Company settings updated.');
    }
}
