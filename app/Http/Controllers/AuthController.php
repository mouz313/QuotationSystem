<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => ['required', 'confirmed', Password::min(8)],
            'company_name' => 'required|string|max:255',
        ]);

        $result = DB::transaction(function () use ($validated) {
            // Create company
            $company = Company::create([
                'name'   => $validated['company_name'],
                'email'  => $validated['email'],
                'status' => 'active',
            ]);

            // Create admin user linked to company
            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'company_id' => $company->id,
                'role'       => 'company_admin',
            ]);

            // Find or create the free package
            $freePackage = Package::where('price', 0)->where('is_active', true)->first();
            if (!$freePackage) {
                $freePackage = Package::create([
                    'name'           => 'Free',
                    'description'    => 'Free plan with basic features to get you started.',
                    'price'          => 0,
                    'duration_days'  => 30,
                    'max_users'      => 1,
                    'max_clients'    => 3,
                    'max_quotations' => 10,
                    'is_active'      => true,
                ]);
            }

            // Assign free package to company
            CompanyPackage::create([
                'company_id'  => $company->id,
                'package_id'  => $freePackage->id,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays($freePackage->duration_days)->toDateString(),
                'status'      => 'active',
            ]);

            return ['company' => $company, 'user' => $user];
        });

        // Log activity
        ActivityLog::log('company_registered', $result['company'],
            $result['company']->name . ' registered via self-signup');

        // Login the user
        Auth::login($result['user']);

        // Send welcome email (non-blocking)
        try {
            Mail::to($result['user']->email)->send(
                new \App\Mail\CompanyWelcomeMail($result['user'], $result['company'])
            );
        } catch (\Exception $e) {
            \Log::warning('Welcome email failed: ' . $e->getMessage());
        }

        return redirect('/dashboard');
    }

    public function login(Request $request)
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ── API Auth (unchanged) ──

    public function apiRegister(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => ['required', 'confirmed', Password::min(8)],
            'company_id' => 'required|exists:companies,id',
            'role'       => 'required|in:company_admin,staff',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        if ($company->isBlocked()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot register for a blocked company.',
            ], 403);
        }

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'company_id' => $validated['company_id'],
            'role'       => $validated['role'],
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful.',
            'data'    => [
                'user'  => $user->only('id', 'name', 'email', 'role', 'company_id'),
                'token' => $token,
            ],
        ], 201);
    }

    public function apiLogin(Request $request)
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

    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('company');

        return response()->json([
            'status' => 'success',
            'data'   => $user->only('id', 'name', 'email', 'role', 'company_id', 'company'),
        ]);
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }
}
