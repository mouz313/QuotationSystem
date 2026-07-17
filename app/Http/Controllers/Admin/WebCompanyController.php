<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
        ActivityLog::log('status_changed', $company, 'Changed status of ' . $company->name . ' to ' . $validated['status']);

        $companyAdmin = $company->users()->where('role', 'company_admin')->first();
        if ($companyAdmin) {
            Notification::create([
                'user_id' => $companyAdmin->id,
                'type'    => 'company_status_changed',
                'message' => "Your company status has been changed to {$validated['status']} by admin.",
                'url'     => '/company/settings',
            ]);
        }

        event(new \App\Events\CompanyStatusChanged($company));

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
        ActivityLog::log('package_assigned', $company, 'Assigned package to ' . $company->name);
        event(new \App\Events\PackageAssigned($company, $package));

        $companyAdmin = $company->users()->where('role', 'company_admin')->first();
        if ($companyAdmin) {
            Notification::create([
                'user_id' => $companyAdmin->id,
                'type'    => 'package_assigned',
                'message' => "Package '{$package->name}' has been assigned to your company by admin.",
                'url'     => '/company/settings',
            ]);

            $companyAdmin->notify(
                new \App\Notifications\PackageAssignedNotification($package->name, $package->price)
            );
        }

        return back()->with('success', "Package '{$package->name}' assigned to '{$company->name}'.");
    }

    public function create()
    {
        $packages = Package::where('is_active', true)->get();
        return view('admin.companies.create', compact('packages'));
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:companies,email,' . $company->id,
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|string|max:255',
        ]);

        $company->update($validated);
        ActivityLog::log('updated', $company, 'Updated company ' . $company->name);

        return redirect('/admin/companies/' . $company->id)->with('success', 'Company updated.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:companies,email',
            'phone'    => 'nullable|string|max:50',
            'address'  => 'nullable|string|max:500',
            'admin_name'  => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $company = Company::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phone'   => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status'  => 'active',
        ]);

        User::create([
            'name'     => $validated['admin_name'],
            'email'    => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'company_id' => $company->id,
            'role'     => 'company_admin',
        ]);

        $adminUser = User::where('company_id', $company->id)->where('role', 'company_admin')->first();
        $adminUser->notify(new \App\Notifications\WelcomeCompanyNotification($company->name));

        ActivityLog::log('created', $company, 'Created company ' . $company->name);

        return redirect('/admin/companies')->with('success', 'Company created with admin user.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        ActivityLog::log('deleted', $company, 'Deleted company ' . $company->name);
        return redirect('/admin/companies')->with('success', 'Company deleted.');
    }
}
