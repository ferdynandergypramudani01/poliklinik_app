<?php

use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\VerifikasiPembayaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dokter\DashboardController as DokterDashboardController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;
use App\Http\Controllers\Pasien\PembayaranController;
use App\Http\Controllers\Pasien\PoliController;
use App\Http\Controllers\Pasien\RiwayatPendaftaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dokter\AntrianController;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// ADMIN
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::resource('polis', AdminPoliController::class);
        Route::resource('dokter', DokterController::class);
        Route::resource('pasien', PasienController::class);
        Route::resource('obat', ObatController::class);

        // Verifikasi pembayaran
        Route::get('/pembayaran', [VerifikasiPembayaranController::class, 'index'])
            ->name('admin.pembayaran.index');

        Route::get('/pembayaran/{id}', [VerifikasiPembayaranController::class, 'show'])
            ->name('admin.pembayaran.show');

        Route::patch('/pembayaran/{id}/konfirmasi', [VerifikasiPembayaranController::class, 'konfirmasi'])
            ->name('admin.pembayaran.konfirmasi');

        Route::patch('/pembayaran/{id}/tolak', [VerifikasiPembayaranController::class, 'tolak'])
            ->name('admin.pembayaran.tolak');
    });

// ==========================================
// DOKTER
// ==========================================
Route::middleware(['auth', 'role:dokter'])
    ->prefix('dokter')
    ->group(function () {

        Route::get('/dashboard', [DokterDashboardController::class, 'index'])
            ->name('dokter.dashboard');

        Route::resource('jadwal-periksa', JadwalPeriksaController::class);

        // Pemeriksaan pasien
        Route::get('/periksa-pasien', [PeriksaPasienController::class, 'index'])
            ->name('periksa-pasien.index');

        Route::post('/periksa-pasien', [PeriksaPasienController::class, 'store'])
            ->name('periksa-pasien.store');

        Route::get('/periksa-pasien/{id}', [PeriksaPasienController::class, 'create'])
            ->name('periksa-pasien.create');

        // Riwayat pasien
        Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])
            ->name('riwayat-pasien.index');

        Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])
            ->name('riwayat-pasien.show');

        // Sistem Antrian
        Route::post('/antrian/{idJadwal}/panggil-berikutnya', [
            AntrianController::class,
            'panggilBerikutnya',
        ])->name('dokter.antrian.panggil');

        Route::post('/antrian/{id}/mulai-pemeriksaan', [
            AntrianController::class,
            'mulaiPemeriksaan',
        ])->name('dokter.antrian.mulai');
    });

// ==========================================
// PASIEN
// ==========================================
Route::middleware(['auth', 'role:pasien'])
    ->prefix('pasien')
    ->group(function () {

        Route::get('/dashboard', [PasienDashboardController::class, 'index'])
            ->name('pasien.dashboard');

        // Pendaftaran poli
        Route::get('/daftar', [PoliController::class, 'get'])
            ->name('pasien.daftar');

        Route::post('/daftar', [PoliController::class, 'submit'])
            ->name('pasien.daftar.submit');

        // Pembayaran
        Route::get('/pembayaran', [PembayaranController::class, 'index'])
            ->name('pasien.pembayaran.index');

        Route::post('/pembayaran/{id}/upload', [PembayaranController::class, 'upload'])
            ->name('pasien.pembayaran.upload');

        // Riwayat pendaftaran
        Route::get('/riwayat-pendaftaran', [RiwayatPendaftaranController::class, 'index'])
            ->name('pasien.riwayat-pendaftaran.index');

        Route::get('/riwayat-pendaftaran/{daftarPoli}', [RiwayatPendaftaranController::class, 'show'])
            ->name('pasien.riwayat-pendaftaran.show');
    });

// ==========================================
// PROFILE
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
});

// ==========================================
// NOTIFIKASI
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
});
