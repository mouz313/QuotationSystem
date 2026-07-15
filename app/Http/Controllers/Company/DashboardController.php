<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Item;
use App\Models\Quotation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'clients'     => Client::where('user_id', $user->id)->count(),
            'items'       => Item::where('user_id', $user->id)->count(),
            'quotations'  => Quotation::where('user_id', $user->id)->count(),
            'revenue'     => Quotation::where('user_id', $user->id)->where('status', 'accepted')->sum('grand_total'),
        ];

        $recentQuotations = Quotation::where('user_id', $user->id)
            ->with('client')
            ->latest()
            ->limit(5)
            ->get();

        $chartData = Quotation::where('user_id', $user->id)
            ->whereYear('issue_date', now()->year)
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

        return view('company.dashboard', compact('stats', 'recentQuotations', 'months', 'counts', 'revenues'));
    }
}
