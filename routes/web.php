<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\BiodataController;
use App\Http\Controllers\Admin\PenyewaController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\Admin\FasilitasController;
use App\Http\Controllers\Admin\PemesananController;

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

            Route::group(['prefix' => 'kamar', 'as' => 'data-kost.kamar.'], function () {
                Route::get('/', [KamarController::class, 'index'])->name('index');
                Route::get('/sampah', [KamarController::class, 'trash'])->name('sampah');
                Route::post('/store', [KamarController::class, 'store'])->name('store');
                Route::put('/update/{id}', [KamarController::class, 'update'])->name('update');
                Route::delete('/destroy/{id}', [KamarController::class, 'destroy'])->name('destroy');
                Route::put('/restore/{id}', [KamarController::class, 'restore'])->name('restore');
                Route::delete('/force-delete/{id}', [KamarController::class, 'force-delete'])->name('force-delete');
            });

        // =================================================
        //                DATA KAMAR END
        // =================================================

        // =================================================
        //               DATA FASILITAS START
        // =================================================

            Route::group(['prefix' => 'fasilitas', 'as' => 'data-kost.fasilitas.'], function () {
                Route::get('/', [FasilitasController::class, 'index'])->name('index');
                Route::get('/sampah', [FasilitasController::class, 'trash'])->name('sampah');
                Route::post('/store', [FasilitasController::class, 'store'])->name('store');
                Route::put('/update/{id}', [FasilitasController::class, 'update'])->name('update');
                Route::delete('/destroy/{id}', [FasilitasController::class, 'destroy'])->name('destroy');
                Route::put('/restore/{id}', [FasilitasController::class, 'restore'])->name('restore');
                Route::delete('/force-delete/{id}', [FasilitasController::class, 'force-delete'])->name('force-delete');
            });

        // =================================================
        //               DATA FASILITAS END
        // =================================================

        // =================================================
        //               DATA PENYEWA START
        // =================================================

            Route::group(['prefix' => 'penyewa', 'as' => 'data-user.penyewa.'], function () {
                Route::get('/', [PenyewaController::class, 'index'])->name('index');
                Route::get('/lap-penyewa', [PenyewaController::class, 'laporan'])->name('lap-penyewa');
                Route::get('/sampah', [PenyewaController::class, 'trash'])->name('sampah');
                Route::post('/store', [PenyewaController::class, 'store'])->name('store');
                Route::put('/update/{id}', [PenyewaController::class, 'update'])->name('update');
                Route::delete('/destroy/{id}', [PenyewaController::class, 'destroy'])->name('destroy');
                Route::put('/restore/{id}', [PenyewaController::class, 'restore'])->name('restore');
                Route::delete('/force-delete/{id}', [PenyewaController::class, 'force-delete'])->name('force-delete');
            });
            
        // =================================================
        //               DATA PENYEWA END
        // =================================================

        // =================================================
        //               DATA BIODATA PENYEWA START
        // =================================================

            Route::group(['prefix' => 'data-user/biodata', 'as' => 'data-user.biodata.'], function () {
                Route::get('/{slug}', [BiodataController::class, 'show'])->name('show');
                Route::post('/store/{userId}', [BiodataController::class, 'store'])->name('store');
                Route::put('/update/{id}', [BiodataController::class, 'update'])->name('update');
                Route::delete('/destroy/{id}', [BiodataController::class, 'destroy'])->name('destroy');
            });

        // =================================================
        //               DATA BIODATA PENYEWA END
        // =================================================

        // =================================================
        //               DATA PETUGAS START
        // =================================================

            Route::group(['prefix' => 'petugas', 'as' => 'data-user.petugas.'], function () {
                Route::get('/', [PetugasController::class, 'index'])->name('index');
                Route::get('/sampah', [PetugasController::class, 'trash'])->name('sampah');
                Route::post('/store', [PetugasController::class, 'store'])->name('store');
                Route::put('/update/{id}', [PetugasController::class, 'update'])->name('update');
                Route::delete('/destroy/{id}', [PetugasController::class, 'destroy'])->name('destroy');
                Route::put('/restore/{id}', [PetugasController::class, 'restore'])->name('restore');
                Route::delete('/force-delete/{id}', [PetugasController::class, 'force-delete'])->name('force-delete');
            });

        // =================================================
        //               DATA PETUGAS END
        // =================================================

        // =================================================
        //               DATA PEMESANAN START
        // =================================================

            Route::group(['prefix' => 'pemesanan', 'as' => 'pemesanan.'], function () {
                Route::get('/', [PemesananController::class, 'index'])->name('index');
                Route::get('/sampah', [PemesananController::class, 'trash'])->name('sampah');
                Route::post('/store', [PemesananController::class, 'store'])->name('store');
                Route::delete('/destroy/{id}', [PemesananController::class, 'destroy'])->name('destroy');
                Route::put('/restore/{id}', [PemesananController::class, 'restore'])->name('restore');
                Route::delete('/force/{id}', [PemesananController::class, 'force'])->name('force');
                
                // Route khusus AJAX untuk ambil data kamar di Modal
                Route::get('/get-kamars', [PemesananController::class, 'getKamars'])->name('getKamars');
            });

        // =================================================
        //               DATA PEMESANAN END
        // =================================================

        Route::get('/pembayaran', function () {
            view()->share('title', 'Data Pembayaran');
            return view('pages.admins.pembayaran.data-pembayaran');
        })->name('admin.pembayaran');

    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
