<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BarangController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }


    // Menampilkan daftar barang
    public function index()
    {
         // Mengambil data barang dari API eksternal
        $response = Http::get('http://localhost:8001/api/barangs');

        if ($response->successful()) {
            $barangs = $response->json();
            return view('barangs.index', compact('barangs'));
        } else {
            return view('barangs.index')->with('error', 'Gagal mengambil data barang dari API.');
        }
    }

    // Menampilkan form untuk membuat barang baru
    public function create()
    {
        return view('barangs.create');
    }

    // Menyimpan barang baru
    public function store(Request $request)
    {   
        // Mendapatkan token dari session
        $token = session('api_token');
        
        // Kirim permintaan ke API untuk menyimpan barang baru
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('http://localhost:8001/api/barangs', [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price
        ]);

        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            // Alihkan ke halaman index jika berhasil
            return redirect()->route('barangs.index');
        } else {
            dd('error');
            // Kembalikan dengan pesan error jika gagal
            return back()->withErrors([
                'error' => 'Gagal menyimpan barang. Silakan coba lagi.',
            ]);
        }
    }

    // Menampilkan detail barang
    public function show($id)
    {
        // Mengambil data barang dari API eksternal
        $response = Http::get('http://localhost:8001/api/barangs/'.$id);
        if ($response->successful()) {
            $barang = $response->json();
            return view('barangs.show', compact('barang'));
        } else {
            return view('barangs.index')->with('error', 'Gagal mengambil data barang dari API.');
        }
    }

    // Menampilkan form untuk mengedit barang
    public function edit($id)
    {
        // Mengambil data barang dari API eksternal
        $response = Http::get('http://localhost:8001/api/barangs/'.$id);
        if ($response->successful()) {
            $barang = $response->json();
            return view('barangs.edit', compact('barang'));
        } else {
            return view('barangs.index')->with('error', 'Gagal mengambil data barang dari API.');
        }
    }

    // Mengupdate barang
    public function update(Request $request, $id)
    {
        // Validasi request
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|integer',
            'description' => 'required',
        ]);

        // Mendapatkan token dari session
        $token = session('api_token');

        // Kirim permintaan ke API untuk mengupdate barang
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('http://localhost:8001/api/barangs/' . $id, $validated);

        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            // Alihkan ke halaman index jika berhasil
            return redirect()->route('barangs.index')->with('success', 'Barang berhasil diperbarui.');
        } else {
            // Ambil pesan error dari respons API jika ada
            $error_message = $response->json()['error'] ?? 'Gagal memperbarui barang. Silakan coba lagi.';

            // Kembalikan dengan pesan error
            return back()->withErrors([
                'error' => $error_message,
            ]);
        }
    }

    // Menghapus barang
    public function destroy($id)
    {
        // Mendapatkan token dari session
        $token = session('api_token');

        // Kirim permintaan ke API untuk menghapus barang
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('http://localhost:8001/api/barangs/' . $id);

        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            // Alihkan ke halaman index jika berhasil
            return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus.');
        } else {
            // Ambil pesan error dari respons API jika ada
            $error_message = $response->json()['error'] ?? 'Gagal menghapus barang. Silakan coba lagi.';

            // Kembalikan dengan pesan error
            return redirect()->route('barangs.index')->withErrors([
                'error' => $error_message,
            ]);
        }
    }

    public function halamanUtama()
    {
        return view('barangs.halaman-utama');
    }


    // Tambah pinjam barang
    public function pinjam(Request $request, $id)
{
    // Validasi request
    $validated = $request->validate([
        'jumlah_pinjam' => 'required|integer|min:1',
    ]);

    // Mendapatkan token dari session
    $token = session('api_token');

    // Mengambil data barang dari API eksternal
    $response = Http::get('http://localhost:8001/api/barangs/' . $id);
    if (!$response->successful()) {
        return back()->withErrors([
            'error' => 'Gagal mengambil data barang. Silakan coba lagi.',
        ]);
    }

    // Mendapatkan data barang
    $barang = $response->json();

    // Periksa apakah stok cukup
    if ($barang['stok'] < $validated['jumlah_pinjam']) {
        return back()->withErrors([
            'error' => 'Stok barang tidak mencukupi.',
        ]);
    }

    // Kurangi stok barang
    $barang['stok'] -= $validated['jumlah_pinjam'];

    // Kirim permintaan ke API untuk mengupdate stok barang
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->put('http://localhost:8001/api/barangs/' . $id, [
        'stok' => $barang['stok'],
    ]);

    // Periksa apakah permintaan berhasil
    if ($response->successful()) {
        // Alihkan ke halaman index jika berhasil
        return redirect()->route('barangs.index')->with('success', 'Barang berhasil dipinjam.');
    } else {
        // Ambil pesan error dari respons API jika ada
        $error_message = $response->json()['error'] ?? 'Gagal meminjam barang. Silakan coba lagi.';

        // Kembalikan dengan pesan error
        return back()->withErrors([
            'error' => $error_message,
        ]);
    }
}


}