<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ClientUser;
use App\Models\Company;
use Illuminate\Http\Request;

class WebClientUserController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientUser::with('companies')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->whereHas('companies', function ($q) use ($request) {
                $q->where('companies.id', $request->company_id);
            });
        }

        $clientUsers = $query->paginate(setting_int('pagination_per_page', 15))->withQueryString();
        $companies = Company::latest()->get();

        return view('admin.client-users.index', compact('clientUsers', 'companies'));
    }

    public function show(ClientUser $clientUser)
    {
        $clientUser->load(['companies', 'clients']);

        return view('admin.client-users.show', compact('clientUser'));
    }

    public function updateStatus(Request $request, ClientUser $clientUser)
    {
        $clientUser->update(['is_active' => !$clientUser->is_active]);

        $status = $clientUser->is_active ? 'activated' : 'deactivated';
        ActivityLog::log('status_changed', $clientUser, "Client user {$status}: " . $clientUser->name);

        return back()->with('success', "Client user {$status}.");
    }

    public function destroy(ClientUser $clientUser)
    {
        $name = $clientUser->name;
        $clientUser->delete();

        ActivityLog::log('deleted', $clientUser, 'Client user deleted: ' . $name);

        return redirect('/admin/client-users')->with('success', 'Client user deleted.');
    }
}
