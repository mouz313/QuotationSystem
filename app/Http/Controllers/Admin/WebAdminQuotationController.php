<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,accepted,declined',
        ]);

        $quotation->update(['status' => $validated['status']]);
        ActivityLog::log('status_changed', $quotation, 'Changed status of ' . $quotation->quote_number . ' to ' . $validated['status']);

        event(new \App\Events\QuotationStatusChanged($quotation));

        return back()->with('success', 'Quotation status updated to ' . $validated['status'] . '.');
    }

    public function destroy(Quotation $quotation)
    {
        $quoteNumber = $quotation->quote_number;
        $quotation->delete();
        ActivityLog::log('deleted', null, 'Deleted quotation ' . $quoteNumber);

        return redirect('/admin/quotations')->with('success', 'Quotation deleted.');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.quotations.pdf', compact('quotation'));
        $pdf->setOption('isRemoteEnabled', true);
        return $pdf->download($quotation->quote_number . '.pdf');
    }
}
