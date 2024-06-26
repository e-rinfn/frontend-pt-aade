<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PeminjamanController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Middleware 'auth' digunakan untuk memastikan pengguna harus login
Route::middleware(['auth'])->group(function () {
    // Menggunakan resource route untuk CRUD barang
    Route::resource('barangs', BarangController::class);
    Route::resource('peminjaman', PeminjamanController::class);

    // Menampilkan halaman utama
    Route::get('/halaman-utama', [BarangController::class, 'halamanUtama'])->name('barangs.halaman-utama');

    // Menambahkan data barang melalui create.blade.php
    Route::get('/create', [BarangController::class, 'create'])->name('barangs.create');

    // Route untuk update menggunakan PUT method
    Route::put('/barangs/{id}', [BarangController::class, 'update'])->name('barangs.update');

    // Route untuk delete menggunakan DELETE method
    // Route::delete('/barangs/{id}', [BarangController::class, 'destroy'])->name('barangs.destroy');

    Route::get('/pinjam', [BarangController::class, 'halamanPinjam'])->name('barangs.pinjam');
    Route::get('/pengembalian', [PeminjamanController::class, 'index'])->name('barangs.pengembalian');
    Route::get('/laporan', [BarangController::class, 'halamanLaporan'])->name('barangs.laporan');
});
