<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnggotaTimController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PersetujuanController;
use App\Http\Controllers\GanttController;

/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'dashboardPublik'])->name('home');
Route::get('/kegiatan/{kegiatan}', [DashboardController::class, 'showKegiatanPublik'])->name('kegiatan.publik.show');


/*
|--------------------------------------------------------------------------
| Rute Autentikasi (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Rute yang Dilindungi
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/gantt-data', [GanttController::class, 'data'])->name('gantt.data');
    // Rute untuk menampilkan halaman Gantt Chart
    Route::get('/gantt', function () {
        return view('gantt_chart');
    });
    // API untuk mengambil data Gantt spesifik per kegiatan
    Route::get('/api/kegiatan/{id}/gantt', [GanttController::class, 'kegiatanData'])->name('gantt.kegiatan.data');

    // --- Grup Rute Superadmin ---
    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboardB'])->name('dashboard');
        Route::resource('users', UserController::class);
    });

    // --- Grup Rute Ketua Tim ---
    Route::middleware('role:ketua_tim')->prefix('ketua-tim')->name('ketua-tim.')->group(function () {
        Route::resource('kegiatan', KegiatanController::class);
        Route::post('/kegiatan/{kegiatan}/handle-persetujuan', [PersetujuanController::class, 'handle'])->name('kegiatan.handlePersetujuan');
    });

    // --- Grup Rute Anggota Tim ---
    Route::middleware('role:anggota_tim')->prefix('anggota-tim')->name('anggota-tim.')->group(function () {
        Route::get('/kegiatan', [AnggotaTimController::class, 'index'])->name('kegiatan.index');
        Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show');
        Route::put('/kegiatan/{kegiatan}/update-progress', [AnggotaTimController::class, 'updateProgress'])->name('kegiatan.updateProgress');
    });

    // --- Grup Rute Kepala BPS ---
    Route::middleware('role:kepala')->prefix('kepala')->name('kepala.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboardKepala'])->name('dashboard');
    });

    // --- Grup Rute Bersama untuk Tim ---
    Route::middleware('role:ketua_tim,anggota_tim')->prefix('tim')->name('tim.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboardC'])->name('dashboard');
    });

    // --- Rute Notifikasi ---
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});
