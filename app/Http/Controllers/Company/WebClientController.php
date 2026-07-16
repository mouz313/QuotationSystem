<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
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

        $clients = $clients->latest()->paginate(15)->withQueryString();

        return view('company.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('company.clients.create');
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
        $this->authorizeOwnership($client);
    }
}
