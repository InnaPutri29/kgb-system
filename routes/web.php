<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KgbController;
use App\Http\Controllers\Admin\MasterPejabatController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Halaman utama — redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// -----------------------------------------------------------------------
// ROUTES YANG BUTUH AUTH
// -----------------------------------------------------------------------
Route::middleware(['auth', 'password.changed'])->group(function () {

    // -----------------------------------------------------------------------
    // ADMIN ROUTES
    // -----------------------------------------------------------------------
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Pegawai
        Route::prefix('pegawai')->name('pegawai.')->group(function () {
            Route::get('/', [PegawaiController::class, 'index'])->name('index');
            Route::get('/create', [PegawaiController::class, 'create'])->name('create');
            Route::post('/', [PegawaiController::class, 'store'])->name('store');
            Route::get('/import', [PegawaiController::class, 'showImportForm'])->name('import');
            Route::post('/import', [PegawaiController::class, 'import'])->name('import.store');
            Route::get('/{pegawai}', [PegawaiController::class, 'show'])->name('show');
            Route::get('/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('edit');
            Route::put('/{pegawai}', [PegawaiController::class, 'update'])->name('update');
            Route::delete('/{pegawai}', [PegawaiController::class, 'destroy'])->name('destroy');
        });

        // Proses KGB
        Route::prefix('kgb')->name('kgb.')->group(function () {
            Route::get('/{pegawai}/data-modal', [KgbController::class, 'getDataForModal'])->name('data-modal');
            Route::post('/{pegawai}/proses', [KgbController::class, 'proses'])->name('proses');
            Route::get('/riwayat/{riwayat}/pdf', [KgbController::class, 'downloadPdf'])->name('download-pdf');
        });

        // Master Pejabat
        Route::resource('master-pejabat', MasterPejabatController::class)->except(['create', 'edit']);

        // Master Gaji
        Route::resource('master-gaji', \App\Http\Controllers\Admin\MasterGajiController::class)->except(['create', 'edit', 'show']);

        // Pengaturan Instansi
        Route::get('/pengaturan-instansi', [\App\Http\Controllers\Admin\PengaturanInstansiController::class, 'index'])->name('pengaturan-instansi.index');
        Route::post('/pengaturan-instansi', [\App\Http\Controllers\Admin\PengaturanInstansiController::class, 'update'])->name('pengaturan-instansi.update');
    });

    // -----------------------------------------------------------------------
    // PEGAWAI ROUTES
    // -----------------------------------------------------------------------
    Route::prefix('pegawai-portal')->name('pegawai.')->middleware('role:pegawai')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Pegawai\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/sk/{riwayat}/download', [\App\Http\Controllers\Pegawai\DashboardController::class, 'downloadSk'])->name('sk.download');
    });

    // Profile (diakses oleh semua user yang sudah login)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

// -----------------------------------------------------------------------
// FORCED PASSWORD CHANGE (tidak diblokir oleh middleware password.changed)
// -----------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

require __DIR__.'/auth.php';
