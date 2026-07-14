<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Package;
use Illuminate\Http\Request;

class WebCompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::with('companyPackages.package')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['users', 'companyPackages.package']);
        $packages = Package::where('is_active', true)->get();

        return view('admin.companies.show', compact('company', 'packages'));
    }

    public function updateStatus(Request $request, Company $company)
    {
        $validated = $request->validate(['status' => 'required|in:active,inactive,blocked']);
        $company->update(['status' => $validated['status']]);

        return back()->with('success', "Company status updated to {$validated['status']}.");
    }

    public function assignPackage(Request $request, Company $company)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'start_date' => 'required|date',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        \App\Models\CompanyPackage::where('company_id', $company->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        \App\Models\CompanyPackage::create([
            'company_id'  => $company->id,
            'package_id'  => $package->id,
            'start_date'  => $validated['start_date'],
            'end_date'    => now()->addDays($package->duration_days)->toDateString(),
            'status'      => 'active',
        ]);

        $company->update(['status' => 'active']);

        return back()->with('success', "Package '{$package->name}' assigned to '{$company->name}'.");
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect('/admin/companies')->with('success', 'Company deleted.');
    }
}
