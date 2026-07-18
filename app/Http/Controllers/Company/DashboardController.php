<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Item;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function activityLog(Request $request)
    {
        $user = $request->user();
        $companyIds = User::where('company_id', $user->company_id)->pluck('id');

        $query = ActivityLog::with('user')
            ->whereIn('user_id', $companyIds)
            ->where('subject_type', Quotation::class)
            ->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(setting_int('pagination_activity', 25))->withQueryString();

        return view('company.activity-log', compact('logs'));
    }

    public function reports(Request $request)
    {
        $user = $request->user();
        $companyIds = User::where('company_id', $user->company_id)->pluck('id');
        $period = $request->get('period', 'all_time');

        $baseQuery = Quotation::whereIn('user_id', $companyIds);
        $baseQuery = $this->applyPeriodFilter($baseQuery, $period);

        $stats = [
            'total_quotations' => (clone $baseQuery)->count(),
            'accepted_count'   => (clone $baseQuery)->where('status', 'accepted')->count(),
        ];

        $conversionRate = $stats['total_quotations'] > 0
            ? round(($stats['accepted_count'] / $stats['total_quotations']) * 100, 1)
            : 0;

        $revenueByCurrency = (clone $baseQuery)
            ->where('status', 'accepted')
            ->join('currencies', 'quotations.currency_id', '=', 'currencies.id')
            ->selectRaw('currencies.symbol as currency_symbol, currencies.code as currency_code, SUM(quotations.grand_total) as total')
            ->groupBy('currencies.id', 'currencies.symbol', 'currencies.code')
            ->get();

        $totalRevenue = $revenueByCurrency->sum('total');

        $year = now()->year;
        $chartData = Quotation::whereIn('user_id', $companyIds)
            ->whereYear('issue_date', $year)
            ->selectRaw("DATE_FORMAT(issue_date, '%m') as month, COUNT(*) as total, SUM(grand_total) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $months = [];
        $counts = [];
        $revenues = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = str_pad($m, 2, '0', STR_PAD_LEFT);
            $months[] = date('M', mktime(0, 0, 0, $m, 1));
            $counts[] = (int) ($chartData[$key]->total ?? 0);
            $revenues[] = (float) ($chartData[$key]->revenue ?? 0);
        }

        return view('company.reports', compact(
            'stats', 'conversionRate', 'revenueByCurrency', 'totalRevenue',
            'months', 'counts', 'revenues', 'period'
        ));
    }

    public function exportReport(Request $request)
    {
        $user = $request->user();
        $companyIds = User::where('company_id', $user->company_id)->pluck('id');

        $query = Quotation::with(['client', 'currency', 'user'])
            ->whereIn('user_id', $companyIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
        }

        $quotations = $query->latest()->get();

        $data = $quotations->map(fn ($q) => [
            'Quote #'  => $q->quote_number,
            'Client'   => $q->client->name ?? 'N/A',
            'User'     => $q->user->name ?? 'N/A',
            'Date'     => $q->issue_date->format('M d, Y'),
            'Currency' => $q->currency?->code ?? 'USD',
            'Total'    => $q->grand_total,
            'Status'   => $q->status,
        ])->toArray();

        if (empty($data)) {
            return back()->with('error', 'No data to export.');
        }

        $headers = array_keys($data[0]);
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
            'Content-Disposition' => 'attachment; filename="quotations-report.csv"',
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $company = $user->company;
        $hasPackage = $company && $company->activePackage() !== null;
        $period = $request->get('period', 'this_year');

        $baseQuery = Quotation::where('user_id', $user->id);
        $baseQuery = $this->applyPeriodFilter($baseQuery, $period);

        $stats = [
            'clients'     => Client::where('user_id', $user->id)->count(),
            'items'       => Item::where('user_id', $user->id)->count(),
            'quotations'  => (clone $baseQuery)->count(),
            'revenue'     => (clone $baseQuery)->where('status', 'accepted')->sum('grand_total'),
        ];

        $revenueByCurrency = (clone $baseQuery)
            ->where('status', 'accepted')
            ->join('currencies', 'quotations.currency_id', '=', 'currencies.id')
            ->selectRaw('currencies.symbol as currency_symbol, currencies.code as currency_code, SUM(quotations.grand_total) as total')
            ->groupBy('currencies.id', 'currencies.symbol', 'currencies.code')
            ->get();

        $recentQuotations = Quotation::where('user_id', $user->id)
            ->with(['client', 'currency'])
            ->latest()
            ->limit(5)
            ->get();

        $companyIds = User::where('company_id', $user->company_id)->pluck('id');
        $year = $period === 'all_time'
            ? (Quotation::whereIn('user_id', $companyIds)->min('issue_date')?->year ?? now()->year)
            : now()->year;
        $chartData = Quotation::where('user_id', $user->id)
            ->whereYear('issue_date', $year)
            ->selectRaw("DATE_FORMAT(issue_date, '%m') as month, COUNT(*) as total, SUM(grand_total) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $months = [];
        $counts = [];
        $revenues = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = str_pad($m, 2, '0', STR_PAD_LEFT);
            $months[] = date('M', mktime(0, 0, 0, $m, 1));
            $counts[] = (int) ($chartData[$key]->total ?? 0);
            $revenues[] = (float) ($chartData[$key]->revenue ?? 0);
        }

        $statusBreakdown = Quotation::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('company.dashboard', compact(
            'stats', 'recentQuotations', 'months', 'counts', 'revenues',
            'revenueByCurrency', 'company', 'hasPackage', 'period', 'statusBreakdown'
        ));
    }

    private function applyPeriodFilter($query, string $period)
    {
        return match ($period) {
            'this_month' => $query->whereMonth('issue_date', now()->month)->whereYear('issue_date', now()->year),
            'this_quarter' => $query->whereBetween('issue_date', [
                now()->startOfQuarter(), now()->endOfQuarter()
            ]),
            'this_year' => $query->whereYear('issue_date', now()->year),
            default => $query,
        };
    }
}
