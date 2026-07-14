<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies'   => Company::count(),
            'active_companies'  => Company::where('status', 'active')->count(),
            'total_quotations'  => Quotation::count(),
            'accepted_quotes'   => Quotation::where('status', 'accepted')->count(),
            'total_revenue'     => Quotation::where('status', 'accepted')->sum('grand_total'),
            'monthly_revenue'   => Quotation::where('status', 'accepted')->whereMonth('created_at', now()->month)->sum('grand_total'),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function export(Request $request, string $type)
    {
        $format = $request->query('format', 'csv');

        return match($type) {
            'companies'    => $this->exportCompanies($format),
            'quotations'   => $this->exportQuotations($request, $format),
            'revenue'      => $this->exportRevenue($request, $format),
            default        => abort(404),
        };
    }

    private function exportCompanies(string $format)
    {
        $companies = Company::with(['users', 'companyPackages.package'])->get();

        $data = $companies->map(fn ($c) => [
            'Name'       => $c->name,
            'Email'      => $c->email,
            'Status'     => $c->status,
            'Users'      => $c->users->count(),
            'Package'    => $c->activePackage()?->package?->name ?? 'None',
            'Created'    => $c->created_at->format('M d, Y'),
        ])->toArray();

        return $this->download($data, 'companies-report', $format);
    }

    private function exportQuotations(Request $request, string $format)
    {
        $query = Quotation::with(['client', 'currency', 'user.company']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('from_date')) $query->whereDate('issue_date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('issue_date', '<=', $request->to_date);

        $quotations = $query->latest()->get();

        $data = $quotations->map(fn ($q) => [
            'Quote #'     => $q->quote_number,
            'Company'     => $q->user->company?->name ?? 'N/A',
            'Client'      => $q->client->name,
            'Date'        => $q->issue_date->format('M d, Y'),
            'Currency'    => $q->currency?->code ?? 'USD',
            'Total'       => $q->grand_total,
            'Status'      => $q->status,
        ])->toArray();

        return $this->download($data, 'quotations-report', $format);
    }

    private function exportRevenue(Request $request, string $format)
    {
        $query = Quotation::where('status', 'accepted');

        if ($request->filled('from_date')) $query->whereDate('created_at', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('created_at', '<=', $request->to_date);

        $revenue = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(grand_total) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = $revenue->map(fn ($r) => [
            'Month'       => $r->month,
            'Quotations'  => $r->count,
            'Revenue'     => number_format($r->total, 2),
        ])->toArray();

        return $this->download($data, 'revenue-report', $format);
    }

    private function download(array $data, string $filename, string $format)
    {
        if (empty($data)) {
            return back()->with('error', 'No data to export.');
        }

        $headers = array_keys($data[0]);

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf-table', compact('data', 'headers', 'filename'));
            return $pdf->download($filename . '.pdf');
        }

        // CSV export
        $callback = function () use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($data as $row) {
                fputcsv($handle, array_values($row));
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }
}
