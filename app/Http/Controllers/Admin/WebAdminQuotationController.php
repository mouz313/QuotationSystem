<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;

class WebAdminQuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with(['client', 'currency', 'user.company']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('company_id')) {
            $query->whereHas('user', fn ($uq) => $uq->where('company_id', $request->company_id));
        }
        if ($request->filled('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
        }

        $quotations = $query->latest()->paginate(20)->withQueryString();
        $companies = \App\Models\Company::all();

        return view('admin.quotations.index', compact('quotations', 'companies'));
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);
        return view('admin.quotations.show', compact('quotation'));
    }
}
