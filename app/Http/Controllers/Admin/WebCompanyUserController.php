<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class WebCompanyUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'super_admin')->with('company')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(setting_int('pagination_per_page', 15))->withQueryString();
        $companies = Company::latest()->get();

        return view('admin.company-users.index', compact('users', 'companies'));
    }

    public function create()
    {
        $companies = Company::latest()->get();
        return view('admin.company-users.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => ['required', 'confirmed', Password::min(8)],
            'role'       => 'required|in:company_admin,staff',
            'company_id' => 'required|exists:companies,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        ActivityLog::log('created', $user, 'Company user created: ' . $user->name);

        return redirect('/admin/company-users')->with('success', 'Company user created.');
    }

    public function edit(User $user)
    {
        $companies = Company::latest()->get();
        return view('admin.company-users.edit', compact('user', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'password'   => ['nullable', 'confirmed', Password::min(8)],
            'role'       => 'required|in:company_admin,staff',
            'company_id' => 'required|exists:companies,id',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        ActivityLog::log('updated', $user, 'Company user updated: ' . $user->name);

        return redirect('/admin/company-users')->with('success', 'Company user updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::log('deleted', $user, 'Company user deleted: ' . $name);

        return redirect('/admin/company-users')->with('success', 'Company user deleted.');
    }
}
