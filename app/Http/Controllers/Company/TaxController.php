<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:taxes',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $tax = Tax::create($validated);

        return response()->json($tax, 201);
    }

    public function index(Request $request)
    {
        $taxes = Tax::active()
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->get();

        return response()->json($taxes);
    }
}
