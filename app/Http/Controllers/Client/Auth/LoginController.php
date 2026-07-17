<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('client.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $clientUser = ClientUser::where('email', $credentials['email'])->first();

        if (!$clientUser || !Hash::check($credentials['password'], $clientUser->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        if (!$clientUser->is_active) {
            return back()->withErrors(['email' => 'Your account has been deactivated. Contact the company for assistance.'])->onlyInput('email');
        }

        Auth::guard('client')->login($clientUser, $request->filled('remember'));
        $clientUser->update(['last_login_at' => now()]);
        $request->session()->regenerate();
        return redirect()->intended('/client/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/client/login');
    }
}
