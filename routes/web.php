<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BarangController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Middleware 'auth' digunakan untuk memastikan pengguna harus login
Route::middleware(['auth'])->group(function () {

    // Menampilkan daftar barang
    Route::get('/barangs', [BarangController::class, 'index'])->name('barangs.index');
    Route::get('/barangs/{id}', [BarangController::class, 'show'])->name('barangs.show');
    Route::get('/barangs/{id}/edit', [BarangController::class, 'edit'])->name('barangs.edit');
    Route::delete('/barangs/{id}', [BarangController::class, 'destroy'])->name('barangs.destroy');

    // Menampilkan halaman utama
    Route::get('/halaman-utama', [BarangController::class, 'halamanUtama'])->name('barangs.halaman-utama');



    // Menambahkan data barang melalui create.blade.php
    Route::get('/create', [BarangController::class, 'create'])->name('barangs.create');
    Route::post('barangs', [BarangController::class, 'store'])->name('barangs.store');

    Route::post('barangs/{id}/pinjam', [BarangController::class, 'pinjam'])->name('barangs.pinjam');

});