<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/transaksi', function () {
//     return view('transaksi');
// })->name('transaksi');

// Route::get('/pelanggan', function () {
//     return view('pelanggan');
// })->name('pelanggan');

// Route::get('/layanan', function () {
//     return view('layanan');
// })->name('layanan');

// Route::get('/pengguna', function () {
//     return view('pengguna');
// })->name('pengguna');

// Route::get('/laporan', function () {
//     return view('laporan');
// })->name('laporan');

Route::get('/makePassword/{password}', function ($password) {
    // Menggunakan Hash::make untuk membuat bcrypt hash
    $hashedPassword = Hash::make($password);

    // Mengembalikan hashed password
    return 'Hashed Password: ' . $hashedPassword;
});

Route::middleware(['check.token'])->group(function () {
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan');
    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan');
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');

    Route::get('/pelanggan/create', [PelangganController::class, 'create'])->name('pelanggan.create');
    Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::get('/pelanggan/{id}/edit', [PelangganController::class, 'edit'])->name('pelanggan.edit');
    Route::post('/pelanggan/{id}/update', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'delete'])->name('pelanggan.delete');

    Route::get('/layanan/create', [layananController::class, 'create'])->name('layanan.create');
    Route::post('/layanan', [layananController::class, 'store'])->name('layanan.store');
    Route::get('/layanan/{id}/edit', [layananController::class, 'edit'])->name('layanan.edit');
    Route::post('/layanan/{id}/update', [layananController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{id}', [layananController::class, 'delete'])->name('layanan.delete');

    Route::get('/pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::get('/pengguna/{id}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
    Route::post('/pengguna/{id}/update', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'delete'])->name('pengguna.delete');
    Route::post('/pengguna/reset-password/{id}', [PenggunaController::class, 'resetUserPassword'])->name('pengguna.resetPassword');
    Route::post('/pengguna/change-password', [PenggunaController::class, 'changePassword'])->name('pengguna.changePassword');

    Route::get('/transaksi/create', [transaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [transaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}/edit', [transaksiController::class, 'edit'])->name('transaksi.edit');
    Route::post('/transaksi/{id}/update', [transaksiController::class, 'update'])->name('transaksi.update');
    Route::delete('/transaksi/{id}', [transaksiController::class, 'delete'])->name('transaksi.delete');
    Route::get('/transaksi/{id}/view', [TransaksiController::class, 'viewDetail'])->name('transaksi.view');
    Route::post('/transaksi/{id}/update-status', [TransaksiController::class, 'updateStatus'])->name('transaksi.updateStatus');

    Route::get('/password/change', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/update', [AuthController::class, 'changePassword'])->name('password.update');

    Route::get('/report/generate-pdf', [LaporanController::class, 'generatePDF'])->name('report.generatePDF');
    Route::get('/transaksi/{id}/print', [TransaksiController::class, 'print'])->name('transaksi.print');
});
