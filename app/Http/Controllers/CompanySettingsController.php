<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySettingsController extends Controller
{
    public function edit()
    {
        $company = Auth::user()->company;
        if (!$company) abort(404);

        $company->load('companyPackages.package');
        $activePackage = $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first();

        return view('settings.company', compact('company', 'activePackage'));
    }

    public function update(Request $request)
    {
        $company = Auth::user()->company;
        if (!$company) abort(404);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:companies,email,' . $company->id,
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
        ]);

        $company->update($validated);

        return back()->with('success', 'Company settings updated.');
    }
}
