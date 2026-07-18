<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companies = Company::with('activePackage.package')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(setting_int('pagination_per_page', 15));

        return response()->json([
            'status' => 'success',
            'data'   => $companies,
        ]);
    }

    public function show(Company $company): JsonResponse
    {
        $company->load(['users', 'activePackage.package', 'quotations' => fn ($q) => $q->latest()->limit(setting_int('dashboard_limit', 10))]);

        return response()->json([
            'status' => 'success',
            'data'   => $company,
        ]);
    }

    public function updateStatus(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,blocked',
        ]);

        $company->update(['status' => $validated['status']]);

        return response()->json([
            'status'  => 'success',
            'message' => "Company status updated to {$validated['status']}.",
            'data'    => $company->only('id', 'name', 'status'),
        ]);
    }

    public function assignPackage(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'start_date' => 'required|date',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        CompanyPackage::where('company_id', $company->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $companyPackage = CompanyPackage::create([
            'company_id'  => $company->id,
            'package_id'  => $package->id,
            'start_date'  => $validated['start_date'],
            'end_date'    => now()->addDays($package->duration_days)->toDateString(),
            'status'      => 'active',
        ]);

        $company->update(['status' => 'active']);

        return response()->json([
            'status'  => 'success',
            'message' => "Package '{$package->name}' assigned to '{$company->name}'.",
            'data'    => $companyPackage->load('package'),
        ], 201);
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Company deleted.',
        ]);
    }
}
