<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $clientUser = $request->user('client');
        $companyIds = $clientUser->companies()->pluck('companies.id');

        $baseQuery = fn() => Quotation::whereHas('user', fn($q) => $q->whereIn('company_id', $companyIds));

        $stats = [
            'total'      => $baseQuery()->count(),
            'accepted'   => $baseQuery()->where('status', 'accepted')->count(),
            'pending'    => $baseQuery()->whereIn('status', ['sent', 'opened'])->count(),
            'declined'   => $baseQuery()->where('status', 'declined')->count(),
            'change_req' => $baseQuery()->where('status', 'change_requested')->count(),
        ];

        $currencyTotals = (clone $baseQuery())
            ->join('currencies', 'quotations.currency_id', '=', 'currencies.id')
            ->selectRaw('currencies.code as code, currencies.symbol as symbol, SUM(quotations.grand_total) as total_value, SUM(quotations.paid_amount) as paid_amount')
            ->groupBy('currencies.code', 'currencies.symbol')
            ->get();

        $quotations = $baseQuery()
            ->with(['client', 'currency', 'user.company', 'payments'])
            ->latest()
            ->paginate(12);

        $recentQuotations = (clone $baseQuery())
            ->with(['client', 'currency', 'user.company'])
            ->latest()
            ->take(5)
            ->get();

        $actionRequired = (clone $baseQuery())
            ->with(['user.company', 'currency'])
            ->whereIn('status', ['sent', 'opened', 'change_requested'])
            ->latest()
            ->take(5)
            ->get();

        return view('client.dashboard', compact('quotations', 'stats', 'currencyTotals', 'recentQuotations', 'actionRequired'));
    }
}
