<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DokLegalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\KategoriDokController;
use App\Http\Controllers\JenisDokController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root ke login atau dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

// Grup rute yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Dashboard/Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // ===================================================
    // User Management Routes - PERHATIKAN URUTAN ROUTE!
    // ===================================================

    // Route create harus didefinisikan SEBELUM route show
    Route::middleware(['access:users,tambah'])->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });

    Route::middleware(['access:users,detail'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        // Tambahkan constraint where agar hanya cocok dengan ID numerik
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->where('user', '[0-9]+');
    });

    // Route untuk user access management
    Route::middleware(['admin'])->group(function () {
        Route::get('/users/{user}/access', [UserAccessController::class, 'edit'])->name('users.access.edit');
        Route::put('/users/{user}/access', [UserAccessController::class, 'update'])->name('users.access.update');
    });

    Route::middleware(['access:users,ubah'])->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}', [UserController::class, 'update']);
    });

    Route::middleware(['access:users,hapus'])->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ===================================================
    // Dokumen Legal Routes - PERHATIKAN URUTAN ROUTE!
    // ===================================================

    // Route create harus didefinisikan SEBELUM route show
    Route::middleware(['access:dokLegal,tambah'])->group(function () {
        Route::get('/dokLegal/create', [DokLegalController::class, 'create'])->name('dokLegal.create');
        Route::post('/dokLegal', [DokLegalController::class, 'store'])->name('dokLegal.store');
    });

    Route::middleware(['access:dokLegal,detail'])->group(function () {
        Route::get('/dokLegal', [DokLegalController::class, 'index'])->name('dokLegal.index');
        Route::get('/dokLegal/{dokLegal}', [DokLegalController::class, 'show'])->name('dokLegal.show')->where('dokLegal', '[0-9]+');
    });

    Route::middleware(['access:dokLegal,download'])->group(function () {
        Route::get('/dokLegal/{dokLegal}/download', [DokLegalController::class, 'download'])->name('dokLegal.download');
    });

    Route::middleware(['access:dokLegal,ubah'])->group(function () {
        Route::get('/dokLegal/{dokLegal}/edit', [DokLegalController::class, 'edit'])->name('dokLegal.edit');
        Route::put('/dokLegal/{dokLegal}', [DokLegalController::class, 'update'])->name('dokLegal.update');
        Route::patch('/dokLegal/{dokLegal}', [DokLegalController::class, 'update']);
    });

    Route::middleware(['access:dokLegal,hapus'])->group(function () {
        Route::delete('/dokLegal/{dokLegal}', [DokLegalController::class, 'destroy'])->name('dokLegal.destroy');
    });

    Route::middleware(['access:dokLegal,monitoring'])->group(function () {
        // Tambahkan route untuk monitoring dokumen legal jika ada
    });

    // ===================================================
    // Perusahaan Routes - PERHATIKAN URUTAN ROUTE!
    // ===================================================

    // Route create harus didefinisikan SEBELUM route show
    Route::middleware(['access:perusahaan,tambah'])->group(function () {
        Route::get('/perusahaan/create', [PerusahaanController::class, 'create'])->name('perusahaan.create');
        Route::post('/perusahaan', [PerusahaanController::class, 'store'])->name('perusahaan.store');
    });

    Route::middleware(['access:perusahaan,detail'])->group(function () {
        Route::get('/perusahaan', [PerusahaanController::class, 'index'])->name('perusahaan.index');
        Route::get('/perusahaan/{perusahaan}', [PerusahaanController::class, 'show'])->name('perusahaan.show')->where('perusahaan', '[0-9]+');
    });

    Route::middleware(['access:perusahaan,ubah'])->group(function () {
        Route::get('/perusahaan/{perusahaan}/edit', [PerusahaanController::class, 'edit'])->name('perusahaan.edit');
        Route::put('/perusahaan/{perusahaan}', [PerusahaanController::class, 'update'])->name('perusahaan.update');
        Route::patch('/perusahaan/{perusahaan}', [PerusahaanController::class, 'update']);
    });

    Route::middleware(['access:perusahaan,hapus'])->group(function () {
        Route::delete('/perusahaan/{perusahaan}', [PerusahaanController::class, 'destroy'])->name('perusahaan.destroy');
    });

    // ===================================================
    // Kategori Dokumen Routes - PERHATIKAN URUTAN ROUTE!
    // ===================================================

    // Route create harus didefinisikan SEBELUM route show
    Route::middleware(['access:kategori-dok,tambah'])->group(function () {
        Route::get('/kategori-dok/create', [KategoriDokController::class, 'create'])->name('kategori-dok.create');
        Route::post('/kategori-dok', [KategoriDokController::class, 'store'])->name('kategori-dok.store');
    });

    Route::middleware(['access:kategori-dok,detail'])->group(function () {
        Route::get('/kategori-dok', [KategoriDokController::class, 'index'])->name('kategori-dok.index');
        Route::get('/kategori-dok/{kategoriDok}', [KategoriDokController::class, 'show'])->name('kategori-dok.show')->where('kategoriDok', '[0-9]+');
    });

    Route::middleware(['access:kategori-dok,ubah'])->group(function () {
        Route::get('/kategori-dok/{kategoriDok}/edit', [KategoriDokController::class, 'edit'])->name('kategori-dok.edit');
        Route::put('/kategori-dok/{kategoriDok}', [KategoriDokController::class, 'update'])->name('kategori-dok.update');
        Route::patch('/kategori-dok/{kategoriDok}', [KategoriDokController::class, 'update']);
    });

    Route::middleware(['access:kategori-dok,hapus'])->group(function () {
        Route::delete('/kategori-dok/{kategoriDok}', [KategoriDokController::class, 'destroy'])->name('kategori-dok.destroy');
    });

    // ===================================================
    // Jenis Dokumen Routes - PERHATIKAN URUTAN ROUTE!
    // ===================================================

    // Route create harus didefinisikan SEBELUM route show
    Route::middleware(['access:jenis-dok,tambah'])->group(function () {
        Route::get('/jenis-dok/create', [JenisDokController::class, 'create'])->name('jenis-dok.create');
        Route::post('/jenis-dok', [JenisDokController::class, 'store'])->name('jenis-dok.store');
    });

    Route::middleware(['access:jenis-dok,detail'])->group(function () {
        Route::get('/jenis-dok', [JenisDokController::class, 'index'])->name('jenis-dok.index');
        Route::get('/jenis-dok/{jenisDok}', [JenisDokController::class, 'show'])->name('jenis-dok.show')->where('jenisDok', '[0-9]+');
    });

    Route::middleware(['access:jenis-dok,ubah'])->group(function () {
        Route::get('/jenis-dok/{jenisDok}/edit', [JenisDokController::class, 'edit'])->name('jenis-dok.edit');
        Route::put('/jenis-dok/{jenisDok}', [JenisDokController::class, 'update'])->name('jenis-dok.update');
        Route::patch('/jenis-dok/{jenisDok}', [JenisDokController::class, 'update']);
    });

    Route::middleware(['access:jenis-dok,hapus'])->group(function () {
        Route::delete('/jenis-dok/{jenisDok}', [JenisDokController::class, 'destroy'])->name('jenis-dok.destroy');
    });

    // API untuk dashboard stats - tidak menggunakan middleware untuk memudahkan akses
    Route::get('/api/dokumen-by-status', [DokLegalController::class, 'getDokumenByStatus']);
    Route::get('/api/dokumen-terbaru', [DokLegalController::class, 'getDokumenTerbaru']);
    Route::get('/api/pengingat/{dokLegal}', [DokLegalController::class, 'getPengingat']);

    // Profile routes - tidak menggunakan middleware untuk memudahkan akses
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route untuk debugging
    Route::get('/debug/users/create', [UserController::class, 'create'])->name('debug.users.create');
});

// Auth routes (login, logout, reset password)
require __DIR__.'/auth.php';