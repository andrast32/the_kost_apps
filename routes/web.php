<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KamarController;
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
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Group Admin
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Halaman Dashboard Admin
        Route::get('/dashboard', function () {
            view()->share('title', 'Dashboard Admin');
            return view('pages.admins.dashboard-admin');
        })->name('dashboard');

        // =================================================
        // DATA KAMAR
        // =================================================

        Route::get('data-kost/kamar/sampah', [KamarController::class, 'trash'])->name('data-kost.kamar.sampah');
        Route::put('data-kost/kamar/restore/{id}', [KamarController::class, 'restore'])->name('data-kost.kamar.restore');
        Route::delete('data-kost/kamar/force-delete/{id}', [KamarController::class, 'forceDelete'])->name('data-kost.kamar.force-delete');

        Route::resource('data-kost/kamar', KamarController::class)->names('data-kost.kamar');

        // =================================================

        Route::get('/biodata', function () {
            view()->share('title', 'Biodata Penyewa');
            return view('pages.admins.biodata.data-bio');
        })->name('biodata');

        Route::get('/pembayaran', function () {
            view()->share('title', 'Data Pembayaran');
            return view('pages.admins.pembayaran.data-pembayaran');
        })->name('pembayaran');

        Route::get('/pemesanan', function () {
            view()->share('title', 'Data Pemesanan');
            return view('pages.admins.pemesanan.data-pemesanan');
        })->name('pemesanan');

        Route::get('/data-kost/fasilitas', function () {
            view()->share('title', 'Data Fasilitas');
            return view('pages.admins.data-kost.fasilitas.data-fasilitas');
        })->name('data-kost.fasilitas');

        Route::get('/data-user/penyewa', function () {
            view()->share('title', 'Data Penyewa');
            return view('pages.admins.data-user.penyewa.data-penyewa');
        })->name('data-user.penyewa');

        Route::get('/data-user/petugas', function () {
            view()->share('title', 'Data Petugas');
            return view('pages.admins.data-user.petugas.data-petugas');
        })->name('data-user.petugas');

        Route::get('/success', function () {
            return redirect()->back()->with('alert', [
                'icon' => 'success',
                'title' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
            ]);
        });

        Route::get('/gagal', function () {
            return redirect()->back()->with('alert', [
                'icon' => 'error',
                'title' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
            ]);
        });

    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
