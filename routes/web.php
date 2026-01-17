<?php

use App\Http\Controllers\Admin\FasilitasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\PenyewaController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\Admin\BiodataController;
use Illuminate\Support\Facades\Route;

// Halaman Depan
Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('kamar/', function () {
    view()->share('title', 'Daftar Kamar');
    return view('pages.dashboards.kamar-dashboard');
})->name('kamar');

Route::get('contact/', function () {
    view()->share('title', 'Hubungi Kami');
    return view('pages.dashboards.contact-dashboard');
})->name('contact');

// Group User
Route::middleware(['auth', 'verified', 'role:User'])->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.users.dashboard');
    })->name('dashboard');
});

// Group Admin
Route::prefix('admin')
    ->middleware(['auth', 'verified', 'role:Admin'])
    ->group(function () {

        // Halaman Dashboard Admin
        Route::get('/dashboard', function () {
            view()->share('title', 'Dashboard Admin');
            return view('pages.admins.dashboard-admin');
        })->name('admin.dashboard');

        // =================================================
        //                DATA KAMAR START
        // =================================================

            Route::get('data-kost/kamar/sampah', [KamarController::class, 'trash'])->name('admin.data-kost.kamar.sampah');
            Route::put('data-kost/kamar/restore/{id}', [KamarController::class, 'restore'])->name('admin.data-kost.kamar.restore');
            Route::delete('data-kost/kamar/force-delete/{id}', [KamarController::class, 'forceDelete'])->name('admin.data-kost.kamar.force-delete');

            Route::resource('data-kost/kamar', KamarController::class)->names('admin.data-kost.kamar');

        // =================================================
        //                DATA KAMAR END
        // =================================================

        // =================================================
        //               DATA FASILITAS START
        // =================================================

            Route::get('data-kost/fasilitas/sampah', [FasilitasController::class, 'trash'])->name('admin.data-kost.fasilitas.sampah');
            Route::put('data-kost/fasilitas/restore/{id}', [FasilitasController::class, 'restore'])->name('admin.data-kost.fasilitas.restore');
            Route::delete('data-kost/fasilitas/force-delete', [FasilitasController::class, 'forceDelete'])->name('admin.data-kost.fasilitas.force-delete');

            Route::resource('data-kost/fasilitas', FasilitasController::class)->names('admin.data-kost.fasilitas')->parameters(['fasilitas' => 'fasilitas']);

        // =================================================
        //               DATA FASILITAS END
        // =================================================

        // =================================================
        //               DATA PENYEWA START
        // =================================================
            Route::get('data-user/penyewa/sampah', [PenyewaController::class, 'trash'])->name('admin.data-user.penyewa.sampah');
            Route::get('data-user/laporan', [PenyewaController::class, 'laporan'])->name('admin.data-user.lap-penyewa');
            Route::put('data-user/penyewa/restore/{id}', [PenyewaController::class, 'restore'])->name('admin.data-user.penyewa.restore');
            Route::delete('data-user/penyewa/force-delete/{id}', [PenyewaController::class, 'forceDelete'])->name('admin.data-user.penyewa.force-delete');

            Route::resource('data-user/penyewa', PenyewaController::class)->names('admin.data-user.penyewa');
        // =================================================
        //               DATA PENYEWA END
        // =================================================

        // =================================================
        //               DATA PETUGAS START
        // =================================================
            Route::get('data-user/petugas/sampah', [PetugasController::class, 'trash'])->name('admin.data-user.petugas.sampah');
            Route::put('data-user/petugas/restore/{id}', [PetugasController::class, 'restore'])->name('admin.data-user.petugas.restore');
            Route::delete('data-user/petugas/force-delete/{id}', [PetugasController::class, 'forceDelete'])->name('admin.data-user.petugas.force-delete');

            Route::resource('data-user/petugas', PetugasController::class)->names('admin.data-user.petugas')->parameters(['petugas' => 'petugas']);
        // =================================================
        //               DATA PETUGAS END
        // =================================================

        // =================================================
        //               DATA BIODATA PENYEWA START
        // =================================================

            Route::get('data-user/biodata/{slug}', [BiodataController::class, 'show'])->name('admin.data-user.biodata');
            Route::post('data-user/biodata/store/{userId}', [BiodataController::class, 'store'])->name('admin.data-user.biodata.store');
            Route::put('data-user/biodata/update/{id}', [BiodataController::class, 'update'])->name('admin.data-user.biodata.update');
            Route::delete('data-user/biodata/delete/{id}', [BiodataController::class, 'destroy'])->name('admin.data-user.biodata.destroy');

        // =================================================
        //               DATA BIODATA PENYEWA END
        // =================================================

        Route::get('/pembayaran', function () {
            view()->share('title', 'Data Pembayaran');
            return view('pages.admins.pembayaran.data-pembayaran');
        })->name('admin.pembayaran');

        Route::get('/pemesanan', function () {
            view()->share('title', 'Data Pemesanan');
            return view('pages.admins.pemesanan.data-pemesanan');
        })->name('admin.pemesanan');

    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
