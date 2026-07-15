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

        $quotations = Quotation::whereHas('user', function ($q) use ($companyIds) {
                $q->whereIn('company_id', $companyIds);
            })
            ->with(['client', 'currency', 'user.company'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total'    => Quotation::whereHas('user', fn($q) => $q->whereIn('company_id', $companyIds))->count(),
            'accepted' => Quotation::whereHas('user', fn($q) => $q->whereIn('company_id', $companyIds))->where('status', 'accepted')->count(),
            'pending'  => Quotation::whereHas('user', fn($q) => $q->whereIn('company_id', $companyIds))->whereIn('status', ['sent', 'opened'])->count(),
            'declined' => Quotation::whereHas('user', fn($q) => $q->whereIn('company_id', $companyIds))->where('status', 'declined')->count(),
        ];

        return view('client.dashboard', compact('quotations', 'stats'));
    }
}
