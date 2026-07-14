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

        return view('company.dashboard', compact('stats', 'recentQuotations'));
    }
}
