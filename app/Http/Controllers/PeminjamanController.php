<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PeminjamanController extends Controller
{



    // Menampilkan daftar barang
    public function index()
    {
        // Mendapatkan token dari session
        $token = session('api_token');
        
        // Mengirim permintaan ke API untuk mendapatkan data barang
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('http://localhost:8001/api/peminjaman');

        // Memeriksa apakah permintaan berhasil
        if ($response->successful()) {
            // Mengambil data barang dari respons API
            $peminjaman = $response->json();

            // Mengirim data barang ke view
            return view('barangs.pengembalian', ['peminjaman' => $peminjaman]);
        } else {
            return back()->withErrors([
                'error' => 'Gagal mendapatkan data peminjaman. Silakan coba lagi.',
            ]);
        }
    }


    public function laporan()
    {
        // Mendapatkan token dari session
        $token = session('api_token');
        
        // Mengirim permintaan ke API untuk mendapatkan data barang
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('http://localhost:8001/api/peminjaman');

        // Memeriksa apakah permintaan berhasil
        if ($response->successful()) {
            // Mengambil data barang dari respons API
            $peminjaman = $response->json();

            // Mengirim data barang ke view
            return view('barangs.laporan', ['peminjaman' => $peminjaman]);
        } else {
            return back()->withErrors([
                'error' => 'Gagal mendapatkan data peminjaman. Silakan coba lagi.',
            ]);
        }
    }

    public function store(Request $request)
    {
        // Validasi data input
        $validator = Validator::make($request->all(), [
            'no_hp' => 'required|string|max:255',
            'nama_peminjam' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'status' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Mendapatkan token dari session
        $token = session('api_token');
        
        // Kirim permintaan ke API untuk menyimpan barang baru
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('http://localhost:8001/api/peminjaman', [
            'nama_peminjam' => $request->nama_peminjam,
            'no_hp' => $request->no_hp,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => $request->status,
        ]);

        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            // Alihkan ke halaman index jika berhasil
            return redirect()->route('barangs.pinjam')->with('success', 'Peminjaman berhasil ditambahkan ke daftar.');
        } else {
            // Kembalikan dengan pesan error jika gagal
            return back()->withErrors([
                'error' => 'Gagal menyimpan barang. Silakan coba lagi.',
            ]);
        }
    }

    public function edit($id)
{
    // Mendapatkan token dari session
    $token = session('api_token');
    
    // Mengirim permintaan ke API untuk mendapatkan data peminjaman berdasarkan ID
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->get('http://localhost:8001/api/peminjaman/' . $id);

    // Periksa apakah permintaan berhasil
    if ($response->successful()) {
        // Mengambil data peminjaman dari respons API
        $peminjaman = $response->json();

        // Tampilkan halaman edit dengan data peminjaman yang akan diubah
        return view('barangs.edit', ['peminjaman' => $peminjaman]);
    } else {
        // Kembalikan dengan pesan error jika gagal
        return back()->withErrors([
            'error' => 'Gagal mendapatkan data peminjaman. Silakan coba lagi.',
        ]);
    }
}

public function update(Request $request, $id)
{
    // Validasi data input
    $validator = Validator::make($request->all(), [
        'no_hp' => 'required|string|max:255',
        'nama_peminjam' => 'required|string|max:255',
        'nama_barang' => 'required|string|max:255',
        'jumlah' => 'required|integer|min:1',
        'tanggal_pinjam' => 'required|date',
        'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
        'status' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Mendapatkan token dari session
    $token = session('api_token');
    
    // Kirim permintaan ke API untuk mengupdate data peminjaman
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->put('http://localhost:8001/api/peminjaman/' . $id, [
        'nama_peminjam' => $request->nama_peminjam,
        'no_hp' => $request->no_hp,
        'nama_barang' => $request->nama_barang,
        'jumlah' => $request->jumlah,
        'tanggal_pinjam' => $request->tanggal_pinjam,
        'tanggal_kembali' => $request->tanggal_kembali,
        'status' => $request->status,
    ]);

    // Periksa apakah permintaan berhasil
    if ($response->successful()) {
        // Alihkan ke halaman index jika berhasil
        return redirect()->route('barangs.pinjam')->with('success', 'Data peminjaman berhasil diupdate.');
    } else {
        // Kembalikan dengan pesan error jika gagal
        return back()->withErrors([
            'error' => 'Gagal mengupdate data peminjaman. Silakan coba lagi.',
        ]);
    }
}


    public function destroy($id)
    {
        // Mendapatkan token dari session
        $token = session('api_token');
        
        // Kirim permintaan ke API untuk menghapus data peminjaman
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('http://localhost:8001/api/peminjaman/' . $id);
    
        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            // Alihkan kembali ke halaman index dengan pesan sukses
            return redirect()->route('barangs.pinjam')->with('success', 'Data peminjaman berhasil dihapus.');
        } else {
            // Kembalikan dengan pesan error jika gagal
            return back()->withErrors([
                'error' => 'Gagal menghapus data peminjaman. Silakan coba lagi.',
            ]);
        }
    }
}
