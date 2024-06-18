<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }


    // Menampilkan daftar barang
    public function index()
    {
        // Mendapatkan token dari session
        $token = session('api_token');
        
        // Mengirim permintaan ke API untuk mendapatkan data barang
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('http://localhost:8001/api/barangs');

        // Memeriksa apakah permintaan berhasil
        if ($response->successful()) {
            // Mengambil data barang dari respons API
            $barangs = $response->json();

            // Mengirim data barang ke view
            return view('barangs.index', ['barangs' => $barangs]);
        } else {
            return back()->withErrors([
                'error' => 'Gagal mendapatkan data barang. Silakan coba lagi.',
            ]);
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
        // Validasi data input
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'required|integer|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Mendapatkan token dari session
        $token = session('api_token');

        // Proses unggah gambar
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            // Mengubah nama file gambar sesuai dengan nama_barang dan timestamp
            $fileName = time() . '-' . $request->file('gambar')->getClientOriginalName();
            $gambarPath = $request->file('gambar')->storeAs('images', $fileName, 'public');
        }

        // Kirim permintaan ke API untuk menyimpan barang baru
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('http://localhost:8001/api/barangs', [
            'nama_barang' => $request->nama_barang,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'gambar' => $gambarPath,
        ]);

        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            // Alihkan ke halaman index jika berhasil
            return redirect()->route('barangs.index')->with('success', 'Barang berhasil ditambahkan.');
        } else {
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





    
public function halamanPinjam(){
    return view('barangs.pinjam');
}

public function halamanPengembalian(){
    return view('barangs.pengembalian');
}

public function halamanLaporan(){
    return view('barangs.laporan');
}
}