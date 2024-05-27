<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
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

        // Kirim permintaan login ke API
        $response = Http::post('http://localhost:8001/api/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Memeriksa response dari API
        if ($response->successful()){
            // Login berhasil dilakukan
            return redirect()->route('home');
        }

        // Jika login gagal, kembali ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}