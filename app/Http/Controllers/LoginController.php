<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi inputan
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Logika login sederhana (tanpa database)
        if ($request->email === 'admin@example.com' && $request->password === 'password') {
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}