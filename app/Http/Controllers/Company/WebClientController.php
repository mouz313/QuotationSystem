<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class WebClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::where('user_id', $request->user()->id)
            ->withCount('quotations')
            ->latest()
            ->paginate(15);

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

        Client::create(array_merge($validated, ['user_id' => $request->user()->id]));

        return redirect('/clients')->with('success', 'Client added.');
    }

    public function edit(Client $client)
    {
        if ($client->user_id !== request()->user()->id) abort(403);
        return view('company.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== $request->user()->id) abort(403);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect('/clients')->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        if ($client->user_id !== request()->user()->id) abort(403);
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
}
