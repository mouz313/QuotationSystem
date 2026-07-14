<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(): JsonResponse
    {
        $packages = Package::withCount('companyPackages')->latest()->get();

        return response()->json([
            'status' => 'success',
            'data'   => $packages,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:packages',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'duration_days'   => 'required|integer|min:1',
            'max_users'       => 'required|integer|min:1',
            'max_clients'     => 'required|integer|min:1',
            'max_quotations'  => 'required|integer|min:1',
        ]);

        $package = Package::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Package created.',
            'data'    => $package,
        ], 201);
    }

    public function show(Package $package): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $package->loadCount('companyPackages'),
        ]);
    }

    public function update(Request $request, Package $package): JsonResponse
    {
        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255|unique:packages,name,' . $package->id,
            'description'     => 'nullable|string',
            'price'           => 'sometimes|numeric|min:0',
            'duration_days'   => 'sometimes|integer|min:1',
            'max_users'       => 'sometimes|integer|min:1',
            'max_clients'     => 'sometimes|integer|min:1',
            'max_quotations'  => 'sometimes|integer|min:1',
            'is_active'       => 'sometimes|boolean',
        ]);

        $package->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Package updated.',
            'data'    => $package,
        ]);
    }

    public function destroy(Package $package): JsonResponse
    {
        if ($package->companyPackages()->where('status', 'active')->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete a package with active subscriptions.',
            ], 409);
        }

        $package->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Package deleted.',
        ]);
    }
}
