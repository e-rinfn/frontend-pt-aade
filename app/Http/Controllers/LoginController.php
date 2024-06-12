<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

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

         // Mengirim permintaan ke API untuk memverifikasi kredensial
        $response = Http::post('http://localhost:8001/api/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Memeriksa respons dari API
        if ($response->successful()) {
             // Login berhasil, ambil token
            $token = $response->json()['token'];
             // Hapus token sebelumnya (jika ada) dan simpan token baru dalam session
             $request->session()->invalidate(); // Hapus session yang ada
             $request->session()->regenerateToken(); // Regenerasi token session baru
             $request->session()->put('api_token', $token); // Simpan token baru
             // Alihkan ke halaman home
            return redirect()->route('barangs.index');
        }

        // Login gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}