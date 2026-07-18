<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Http\Request;

class WebClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::where('user_id', $request->user()->id)
            ->withCount('quotations');

        if ($request->filled('search')) {
            $search = $request->search;
            $clients->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $clients->latest()->paginate(setting_int('pagination_per_page', 15))->withQueryString();

        $pendingPaymentClientIds = Payment::where('payments.status', 'pending')
            ->join('quotations', 'payments.quotation_id', '=', 'quotations.id')
            ->where('quotations.user_id', $request->user()->id)
            ->where('quotations.client_id', '>', 0)
            ->pluck('quotations.client_id')
            ->unique()
            ->toArray();

        return view('company.clients.index', compact('clients', 'pendingPaymentClientIds'));
    }

    public function create()
    {
        return view('company.clients.create');
    }

    public function show(Client $client)
    {
        $this->authorizeOwnership($client);

        $quotations = Quotation::where('client_id', $client->id)
            ->where('user_id', request()->user()->id)
            ->with(['currency', 'payments'])
            ->latest()
            ->get();

        $totalQuoted = $quotations->sum('grand_total');
        $totalPaid = $quotations->sum('paid_amount');
        $pendingPayments = Payment::whereHas('quotation', function ($q) use ($client) {
            $q->where('client_id', $client->id)->where('user_id', request()->user()->id);
        })->where('status', 'pending')->count();

        return view('company.clients.show', compact('client', 'quotations', 'totalQuoted', 'totalPaid', 'pendingPayments'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;
        if ($company && !$company->canAddClient()) {
            return back()->with('error', 'You have reached your client limit. Please upgrade your package.');
        }

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $client = Client::create(array_merge($validated, ['user_id' => $request->user()->id]));

        ActivityLog::log('client_created', $client, 'Client "' . $client->name . '" created');

        return redirect('/clients')->with('success', 'Client added.');
    }

    public function edit(Client $client)
    {
        $this->authorizeOwnership($client);
        return view('company.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeOwnership($client);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $client->update($validated);

        ActivityLog::log('client_updated', $client, 'Client "' . $client->name . '" updated');

        return redirect('/clients')->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        $this->authorizeOwnership($client);

        $hasPendingPayments = Payment::whereHas('quotation', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->where('status', 'pending')->exists();

        if ($hasPendingPayments) {
            return back()->with('error', 'Cannot delete this client. There are pending payments that need to be reviewed first.');
        }

        ActivityLog::log('client_deleted', $client, 'Client "' . $client->name . '" deleted');
        $client->delete();
        return redirect('/clients')->with('success', 'Client deleted.');
    }

    public function exportCsv(Request $request)
    {
        $clients = Client::where('user_id', $request->user()->id)->latest()->get();

        $filename = 'clients-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, ['Name', 'Email', 'Phone', 'Address', 'Created']);

        foreach ($clients as $c) {
            fputcsv($handle, [$c->name, $c->email, $c->phone ?? '', $c->address ?? '', $c->created_at->format('Y-m-d')]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function authorizeOwnership(Client $client): void
    {
        $this->authorize('update', $client);
    }
}
