<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // ── Web Methods ──

    public function index(Request $request)
    {
        $users = User::where('company_id', $request->user()->company_id)
            ->latest()
            ->paginate(15);

        return view('company.users.index', compact('users'));
    }

    public function create()
    {
        return view('company.users.create');
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;
        if ($company && !$company->canAddUser()) {
            return back()->with('error', 'You have reached your user limit. Please upgrade your package.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role'     => 'required|in:company_admin,staff',
        ]);

        User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'company_id' => $request->user()->company_id,
            'role'       => $validated['role'],
        ]);

        return redirect('/company/users')->with('success', 'User added to team.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->company_id !== $request->user()->company_id) abort(403);
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Cannot remove yourself.');
        }

        $user->delete();
        return redirect('/company/users')->with('success', 'User removed.');
    }

    // ── API Methods ──

    public function apiIndex(Request $request): JsonResponse
    {
        $users = User::where('company_id', $request->user()->company_id)
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15);

        return response()->json(['status' => 'success', 'data' => $users]);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role'     => 'required|in:company_admin,staff',
        ]);

        if ($company && $company->activePackage()) {
            $currentCount = User::where('company_id', $company->id)->count();
            if ($currentCount >= $company->package->max_users) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "User limit reached ({$company->package->max_users}).",
                ], 403);
            }
        }

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'company_id' => $request->user()->company_id,
            'role'       => $validated['role'],
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'User created.',
            'data'    => $user->only('id', 'name', 'email', 'role'),
        ], 201);
    }

    public function apiShow(Request $request, User $user): JsonResponse
    {
        if ($user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => 'error', 'message' => 'Access denied.'], 403);
        }
        return response()->json(['status' => 'success', 'data' => $user->only('id', 'name', 'email', 'role', 'created_at')]);
    }

    public function apiUpdate(Request $request, User $user): JsonResponse
    {
        if ($user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => 'error', 'message' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:company_admin,staff',
        ]);

        $user->update($validated);
        return response()->json(['status' => 'success', 'message' => 'User updated.', 'data' => $user->only('id', 'name', 'email', 'role')]);
    }

    public function apiDestroy(Request $request, User $user): JsonResponse
    {
        if ($user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => 'error', 'message' => 'Access denied.'], 403);
        }
        if ($user->id === $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete yourself.'], 409);
        }
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'User removed.']);
    }
}
