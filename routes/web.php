<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DokLegalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\KategoriDokController;
use App\Http\Controllers\JenisDokController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SecurityErrorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes dengan Rate Limiting untuk Keamanan
|--------------------------------------------------------------------------
*/

// Redirect root ke login atau dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

// Security error pages - Halaman eror keamanan
Route::prefix('security')->name('security.')->group(function () {
    Route::get('/blocked', [SecurityErrorController::class, 'blocked'])->name('blocked');
    Route::get('/unauthorized', [SecurityErrorController::class, 'unauthorized'])->name('unauthorized');
    Route::get('/ip-not-whitelisted', [SecurityErrorController::class, 'ipNotWhitelisted'])->name('ip-not-whitelisted');
    Route::get('/error', [SecurityErrorController::class, 'securityError'])->name('error');
});

// Grup rute yang memerlukan autentikasi dengan rate limiting
Route::middleware(['auth', 'throttle:general'])->group(function () {
    // Dashboard/Home - Rate limit lebih tinggi untuk halaman utama
    Route::middleware('throttle:dashboard')->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    });

    // ===================================================
    // User Management Routes dengan Rate Limiting Ketat
    // ===================================================
    Route::prefix('users')->name('users.')->group(function () {

        // Create & Store - Rate limit ketat untuk mencegah spam
        Route::middleware(['access:users,tambah', 'throttle:create'])->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });

        // Read operations - Rate limit sedang
        Route::middleware(['access:users,detail', 'throttle:read'])->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show')->where('user', '[0-9]+');
        });

        // User Access Management - Admin only dengan rate limit ketat
        Route::middleware(['admin', 'throttle:admin'])->group(function () {
            Route::get('/{user}/access', [UserAccessController::class, 'edit'])->name('access.edit');
            Route::put('/{user}/access', [UserAccessController::class, 'update'])->name('access.update');
        });

        // Update operations - Rate limit sedang
        Route::middleware(['access:users,ubah', 'throttle:update'])->group(function () {
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::patch('/{user}', [UserController::class, 'update']);
        });

        // Delete operations - Rate limit ketat
        Route::middleware(['access:users,hapus', 'throttle:delete'])->group(function () {
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
    });

    // ===================================================
    // Dokumen Legal Routes dengan Rate Limiting
    // ===================================================
    Route::prefix('dokLegal')->name('dokLegal.')->group(function () {

        // Create & Store
        Route::middleware(['access:dokLegal,tambah', 'throttle:create'])->group(function () {
            Route::get('/create', [DokLegalController::class, 'create'])->name('create');
            Route::post('/', [DokLegalController::class, 'store'])->name('store');
        });

        // Read operations
        Route::middleware(['access:dokLegal,detail', 'throttle:read'])->group(function () {
            Route::get('/', [DokLegalController::class, 'index'])->name('index');
            Route::get('/{dokLegal}', [DokLegalController::class, 'show'])->name('show')->where('dokLegal', '[0-9]+');
            Route::get('/{dokLegal}/view', [DokLegalController::class, 'view'])->name('view');
        });

        // Download - Rate limit ketat untuk mencegah abuse
        Route::middleware(['access:dokLegal,download', 'throttle:download'])->group(function () {
            Route::get('/{dokLegal}/download', [DokLegalController::class, 'download'])->name('download');
        });

        // Update operations
        Route::middleware(['access:dokLegal,ubah', 'throttle:update'])->group(function () {
            Route::get('/{dokLegal}/edit', [DokLegalController::class, 'edit'])->name('edit');
            Route::put('/{dokLegal}', [DokLegalController::class, 'update'])->name('update');
            Route::patch('/{dokLegal}', [DokLegalController::class, 'update']);
        });

        // Delete operations
        Route::middleware(['access:dokLegal,hapus', 'throttle:delete'])->group(function () {
            Route::delete('/{dokLegal}', [DokLegalController::class, 'destroy'])->name('destroy');
        });

        // Export - Rate limit ketat untuk operasi berat
        Route::middleware('throttle:export')->group(function () {
            Route::post('/export-excel', [DokLegalController::class, 'exportExcel'])->name('export-excel');
        });

        // Stats & API endpoints - Rate limit sedang
        Route::middleware('throttle:api')->group(function () {
            Route::get('/stats', [DokLegalController::class, 'getDocumentStats'])->name('getDocumentStats');
        });
    });

    // ===================================================
    // Perusahaan Routes dengan Rate Limiting
    // ===================================================
    Route::prefix('perusahaan')->name('perusahaan.')->group(function () {

        Route::middleware(['access:perusahaan,tambah', 'throttle:create'])->group(function () {
            Route::get('/create', [PerusahaanController::class, 'create'])->name('create');
            Route::post('/', [PerusahaanController::class, 'store'])->name('store');
        });

        Route::middleware(['access:perusahaan,detail', 'throttle:read'])->group(function () {
            Route::get('/', [PerusahaanController::class, 'index'])->name('index');
            Route::get('/{perusahaan}', [PerusahaanController::class, 'show'])->name('show')->where('perusahaan', '[0-9]+');
        });

        Route::middleware(['access:perusahaan,ubah', 'throttle:update'])->group(function () {
            Route::get('/{perusahaan}/edit', [PerusahaanController::class, 'edit'])->name('edit');
            Route::put('/{perusahaan}', [PerusahaanController::class, 'update'])->name('update');
            Route::patch('/{perusahaan}', [PerusahaanController::class, 'update']);
        });

        Route::middleware(['access:perusahaan,hapus', 'throttle:delete'])->group(function () {
            Route::delete('/{perusahaan}', [PerusahaanController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('throttle:export')->group(function () {
            Route::post('/export-excel', [PerusahaanController::class, 'exportExcel'])->name('export-excel');
        });
    });

    // ===================================================
    // Kategori Dokumen Routes dengan Rate Limiting
    // ===================================================
    Route::prefix('kategori-dok')->name('kategori-dok.')->group(function () {

        Route::middleware(['access:kategori-dok,tambah', 'throttle:create'])->group(function () {
            Route::get('/create', [KategoriDokController::class, 'create'])->name('create');
            Route::post('/', [KategoriDokController::class, 'store'])->name('store');
        });

        Route::middleware(['access:kategori-dok,detail', 'throttle:read'])->group(function () {
            Route::get('/', [KategoriDokController::class, 'index'])->name('index');
            Route::get('/{kategoriDok}', [KategoriDokController::class, 'show'])->name('show')->where('kategoriDok', '[0-9]+');
        });

        Route::middleware(['access:kategori-dok,ubah', 'throttle:update'])->group(function () {
            Route::get('/{kategoriDok}/edit', [KategoriDokController::class, 'edit'])->name('edit');
            Route::put('/{kategoriDok}', [KategoriDokController::class, 'update'])->name('update');
            Route::patch('/{kategoriDok}', [KategoriDokController::class, 'update']);
        });

        Route::middleware(['access:kategori-dok,hapus', 'throttle:delete'])->group(function () {
            Route::delete('/{kategoriDok}', [KategoriDokController::class, 'destroy'])->name('destroy');
        });
    });

    // ===================================================
    // Jenis Dokumen Routes dengan Rate Limiting
    // ===================================================
    Route::prefix('jenis-dok')->name('jenis-dok.')->group(function () {

        Route::middleware(['access:jenis-dok,tambah', 'throttle:create'])->group(function () {
            Route::get('/create', [JenisDokController::class, 'create'])->name('create');
            Route::post('/', [JenisDokController::class, 'store'])->name('store');
        });

        Route::middleware(['access:jenis-dok,detail', 'throttle:read'])->group(function () {
            Route::get('/', [JenisDokController::class, 'index'])->name('index');
            Route::get('/{jenisDok}', [JenisDokController::class, 'show'])->name('show')->where('jenisDok', '[0-9]+');
        });

        Route::middleware(['access:jenis-dok,ubah', 'throttle:update'])->group(function () {
            Route::get('/{jenisDok}/edit', [JenisDokController::class, 'edit'])->name('edit');
            Route::put('/{jenisDok}', [JenisDokController::class, 'update'])->name('update');
            Route::patch('/{jenisDok}', [JenisDokController::class, 'update']);
        });

        Route::middleware(['access:jenis-dok,hapus', 'throttle:delete'])->group(function () {
            Route::delete('/{jenisDok}', [JenisDokController::class, 'destroy'])->name('destroy');
        });
    });

    // ===================================================
    // API Routes dengan Rate Limiting Ketat
    // ===================================================
    Route::prefix('api')->middleware('throttle:api')->group(function () {
        Route::get('/dokumen-by-status', [DokLegalController::class, 'getDokumenByStatus']);
        Route::get('/dokumen-terbaru', [DokLegalController::class, 'getDokumenTerbaru']);
        Route::get('/pengingat/{dokLegal}', [DokLegalController::class, 'getPengingat']);
    });

    // ===================================================
    // Profile & Settings Routes dengan Rate Limiting
    // ===================================================
    Route::middleware('throttle:profile')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/update-password', [App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.update-password');
    });

    // ===================================================
    // Cabang Rekap Routes dengan Rate Limiting
    // ===================================================
    Route::prefix('cabang-rekap')->name('cabang.')->middleware('throttle:read')->group(function () {
        Route::get('/', [App\Http\Controllers\CabangRekapController::class, 'index'])->name('rekap');
        Route::get('/detail/{perusahaanId}', [App\Http\Controllers\CabangRekapController::class, 'detail'])->name('detail');
    });

    // Debug route - hapus di production!
    if (config('app.debug')) {
        Route::get('/debug/users/create', [UserController::class, 'create'])->name('debug.users.create');
    }
});

// Auth routes (login, logout, reset password)
require __DIR__ . '/auth.php';