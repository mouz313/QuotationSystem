<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $clientUser = Auth::guard('client')->user();
        return view('client.profile', compact('clientUser'));
    }

    public function updateProfile(Request $request)
    {
        $clientUser = Auth::guard('client')->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:client_users,email,' . $clientUser->id,
            'phone' => 'nullable|string|max:255',
        ]);

        $clientUser->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password:client',
            'password'         => 'required|min:8|confirmed',
        ]);

        Auth::guard('client')->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->session()->regenerate();
        return back()->with('success', 'Password updated successfully.');
    }
}
