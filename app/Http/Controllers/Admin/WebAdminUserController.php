<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class WebAdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'super_admin')->with('adminRole')->latest()->get();
        $roles = AdminRole::latest()->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = AdminRole::latest()->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users',
            'password'       => ['required', 'confirmed', Password::min(8)],
            'admin_role_id'  => 'nullable|exists:admin_roles,id',
        ]);

        $validated['role'] = 'super_admin';
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect('/admin/users')->with('success', 'Admin user created.');
    }

    public function edit(User $user)
    {
        $roles = AdminRole::latest()->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'admin_role_id'  => 'nullable|exists:admin_roles,id',
            'password'       => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect('/admin/users')->with('success', 'Admin user updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect('/admin/users')->with('success', 'Admin user deleted.');
    }
}
