<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class WebAdminRoleController extends Controller
{
    public function index()
    {
        $roles = AdminRole::withCount('users')->latest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:admin_roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $role = AdminRole::create([
            'name'        => $validated['name'],
            'permissions' => $validated['permissions'] ?? [],
            'is_default'  => false,
        ]);

        ActivityLog::log('created', $role, 'Created role ' . $role->name);

        return redirect('/admin/roles')->with('success', 'Role created.');
    }

    public function edit(AdminRole $role)
    {
        if ($role->is_default) {
            return back()->with('error', 'The default role cannot be edited.');
        }

        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, AdminRole $role)
    {
        if ($role->is_default) {
            return back()->with('error', 'The default role cannot be edited.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:admin_roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $role->update([
            'name'        => $validated['name'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        ActivityLog::log('updated', $role, 'Updated role ' . $role->name);

        return redirect('/admin/roles')->with('success', 'Role updated.');
    }

    public function destroy(AdminRole $role)
    {
        if ($role->is_default) {
            return back()->with('error', 'The default role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a role that has users assigned to it.');
        }

        $name = $role->name;
        $role->delete();
        ActivityLog::log('deleted', null, 'Deleted role ' . $name);

        return redirect('/admin/roles')->with('success', 'Role deleted.');
    }
}
