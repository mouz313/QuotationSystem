<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class WebCurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::latest()->get();
        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'       => 'required|string|max:10|unique:currencies',
            'name'       => 'required|string|max:255',
            'symbol'     => 'required|string|max:10',
            'is_default' => 'sometimes|boolean',
        ]);

        $validated['is_default'] = $validated['is_default'] ?? false;

        if ($validated['is_default']) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        Currency::create($validated);

        return redirect('/admin/currencies')->with('success', 'Currency created.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code'       => 'sometimes|string|max:10|unique:currencies,code,' . $currency->id,
            'name'       => 'sometimes|string|max:255',
            'symbol'     => 'sometimes|string|max:10',
            'is_default' => 'sometimes|boolean',
            'is_active'  => 'sometimes|boolean',
        ]);

        if (isset($validated['is_default']) && $validated['is_default']) {
            Currency::where('is_default', true)->where('id', '!=', $currency->id)->update(['is_default' => false]);
        }

        $currency->update($validated);

        return redirect('/admin/currencies')->with('success', 'Currency updated.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->quotations()->exists()) {
            return back()->with('error', 'Cannot delete a currency that is used in quotations.');
        }

        $currency->delete();
        return redirect('/admin/currencies')->with('success', 'Currency deleted.');
    }
}
