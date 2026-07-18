<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Quotation;
use App\Models\QuotationStatusLog;
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

        $quotations = $query->latest()->paginate(setting_int('pagination_per_page', 20))->withQueryString();
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

        $oldStatus = $quotation->status;
        $quotation->update(['status' => $validated['status']]);

        QuotationStatusLog::create([
            'quotation_id'    => $quotation->id,
            'from_status'     => $oldStatus,
            'to_status'       => $validated['status'],
            'changed_by_type' => get_class($request->user()),
            'changed_by_id'   => $request->user()->id,
            'notes'           => 'Changed by admin',
        ]);

        ActivityLog::log('status_changed', $quotation, 'Admin changed status of ' . $quotation->quote_number . ' from ' . $oldStatus . ' to ' . $validated['status']);

        Notification::create([
            'user_id' => $quotation->user_id,
            'type'    => 'status_changed',
            'message' => "Admin changed {$quotation->quote_number} from " . str_replace('_', ' ', $oldStatus) . " to " . str_replace('_', ' ', $validated['status']) . ".",
            'url'     => "/quotations/{$quotation->id}",
        ]);

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
        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company', 'attachments']);
        $company = $quotation->user->company;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.quotations.pdf', compact('quotation', 'company'));
        $pdf->setOption('isRemoteEnabled', true);
        return $pdf->download($quotation->quote_number . '.pdf');
    }
}
