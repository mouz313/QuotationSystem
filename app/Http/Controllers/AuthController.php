<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'staff',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful.',
            'data'    => [
                'user'  => $user->only('id', 'name', 'email', 'role'),
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = Auth::user();

        if ($user->company && $user->company->isBlocked()) {
            Auth::logout();
            return response()->json([
                'status'  => 'error',
                'message' => 'Your company has been blocked. Contact support.',
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful.',
            'data'    => [
                'user'  => $user->only('id', 'name', 'email', 'role', 'company_id'),
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('company');

        return response()->json([
            'status' => 'success',
            'data'   => $user->only('id', 'name', 'email', 'role', 'company_id', 'company'),
        ]);
    }

    // ── Web Auth ──

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function webLogin(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($validated)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->company && $user->company->isBlocked()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your company has been blocked.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        ActivityLog::log('login', null, $user->name . ' logged in');

        $redirect = $user->isSuperAdmin() ? '/admin/dashboard' : '/dashboard';
        return redirect()->intended($redirect);
    }

    public function webRegister(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'staff',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function webLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
