<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Package;
use Illuminate\Http\Request;

class WebPackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('companyPackages')->latest()->get();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
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

        Package::create($validated);
        $package = Package::latest()->first();
        ActivityLog::log('created', $package, 'Created package ' . $package->name);

        return redirect('/admin/packages')->with('success', 'Package created.');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
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
        ActivityLog::log('updated', $package, 'Updated package ' . $package->name);

        return redirect('/admin/packages')->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        if ($package->price == 0) {
            return back()->with('error', 'The Free package cannot be deleted. You can edit it instead.');
        }

        if ($package->companyPackages()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete a package with active subscriptions.');
        }

        $package->delete();
        ActivityLog::log('deleted', $package, 'Deleted package ' . $package->name);
        return redirect('/admin/packages')->with('success', 'Package deleted.');
    }
}
