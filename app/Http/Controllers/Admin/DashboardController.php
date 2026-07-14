<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Package;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
            'total_users'       => User::where('role', '!=', 'super_admin')->count(),
            'conversion_rate'   => 0,
        ];

        if ($stats['total_quotations'] > 0) {
            $stats['conversion_rate'] = round(($stats['accepted_quotes'] / $stats['total_quotations']) * 100, 1);
        }

        $recentCompanies = Company::withCount('users')
            ->with('companyPackages.package')
            ->latest()->limit(10)->get();

        // Chart data: Monthly revenue last 12 months
        $monthlyRevenue = Quotation::where('status', 'accepted')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(grand_total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $revenueLabels = [];
        $revenueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $revenueLabels[] = now()->subMonths($i)->format('M Y');
            $revenueData[] = round($monthlyRevenue[$key] ?? 0, 2);
        }

        // Chart data: Quotations by status
        $statusCounts = Quotation::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Chart data: Top companies by quotation value
        $topCompanies = Quotation::where('quotations.status', 'accepted')
            ->join('users', 'quotations.user_id', '=', 'users.id')
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->selectRaw('companies.name, SUM(quotations.grand_total) as total')
            ->groupBy('companies.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Chart data: New companies growth
        $companyGrowth = Company::where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $growthLabels = [];
        $growthData = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $growthLabels[] = now()->subMonths($i)->format('M Y');
            $growthData[] = $companyGrowth[$key] ?? 0;
        }

        $recentActivity = ActivityLog::with('user')->latest()->limit(10)->get();

        return view('admin.dashboard', compact(
            'stats', 'recentCompanies',
            'revenueLabels', 'revenueData',
            'statusCounts', 'topCompanies',
            'growthLabels', 'growthData',
            'recentActivity'
        ));
    }
}
