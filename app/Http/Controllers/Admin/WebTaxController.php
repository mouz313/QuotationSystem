<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tax;
use Illuminate\Http\Request;

class WebTaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::latest()->get();
        return view('admin.taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('admin.taxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:taxes',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_default' => 'sometimes|boolean',
        ]);

        $validated['is_default'] = $validated['is_default'] ?? false;

        if ($validated['is_default']) {
            Tax::where('is_default', true)->update(['is_default' => false]);
        }

        Tax::create($validated);
        ActivityLog::log('created', Tax::latest()->first(), 'Created tax ' . $validated['name']);

        return redirect('/admin/taxes')->with('success', 'Tax created.');
    }

    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name'       => 'sometimes|string|max:255|unique:taxes,name,' . $tax->id,
            'percentage' => 'sometimes|numeric|min:0|max:100',
            'is_default' => 'sometimes|boolean',
            'is_active'  => 'sometimes|boolean',
        ]);

        if (isset($validated['is_default']) && $validated['is_default']) {
            Tax::where('is_default', true)->where('id', '!=', $tax->id)->update(['is_default' => false]);
        }

        $tax->update($validated);
        ActivityLog::log('updated', $tax, 'Updated tax ' . $tax->name);

        return redirect('/admin/taxes')->with('success', 'Tax updated.');
    }

    public function destroy(Tax $tax)
    {
        if ($tax->quotations()->exists()) {
            return back()->with('error', 'Cannot delete a tax that is used in quotations.');
        }

        $tax->delete();
        ActivityLog::log('deleted', $tax, 'Deleted tax ' . $tax->name);
        return redirect('/admin/taxes')->with('success', 'Tax deleted.');
    }
}
