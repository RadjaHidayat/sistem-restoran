<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\LupaPasswordController;
use Illuminate\Support\Facades\Route;

// ==================== Halaman tanpa login ====================
Route::get('/signin', [AuthController::class, 'showLogin'])->name('signin');
Route::post('/signin', [AuthController::class, 'login'])->name('signin.proses');

Route::get('/signup', [AuthController::class, 'showRegister'])->name('signup');
Route::post('/signup', [AuthController::class, 'register'])->name('signup.proses');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==================== Lupa password (hanya untuk yang belum login) ====================
Route::middleware('guest')->group(function () {
    Route::get('/lupa-password', [LupaPasswordController::class, 'form'])->name('password.request');
    Route::post('/lupa-password', [LupaPasswordController::class, 'kirimLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [LupaPasswordController::class, 'formReset'])->name('password.reset');
    Route::post('/reset-password', [LupaPasswordController::class, 'simpanReset'])->name('password.update');
});

// ==================== Pemesanan pelanggan lewat QR (tanpa login) ====================
Route::get('/pesan/{kodeQr}', [PesanController::class, 'menu'])->name('pesan.menu');
Route::post('/pesan/{kodeQr}', [PesanController::class, 'store'])->middleware('throttle:10,1')->name('pesan.store');
Route::get('/pesan/{kodeQr}/{kodePesanan}', [PesanController::class, 'status'])->name('pesan.status');

// ==================== Halaman yang wajib login ====================
Route::middleware('auth')->group(function () {

    // ---------- Admin dan kasir ----------
    // (ditaruh paling atas supaya /pesanan/create tidak tertukar dengan /pesanan/{pesanan})
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
        Route::post('pesanan', [PesananController::class, 'store'])->name('pesanan.store');

        Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::post('pesanan/{pesanan}/bayar', [PembayaranController::class, 'store'])->name('pembayaran.store');

        Route::resource('reservasi', ReservasiController::class)->except(['show', 'destroy']);
        Route::resource('meja', MejaController::class)->except(['create', 'show']);
        Route::get('meja/{meja}/qr', [MejaController::class, 'qr'])->name('meja.qr');
    });

    // ---------- Semua staf: admin, kasir, koki ----------
    Route::middleware('role:admin,kasir,koki')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::redirect('/ecommerce', '/')->name('ecommerce');

        Route::get('pesanan', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
        Route::patch('pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');

        Route::get('/profile', [ProfilController::class, 'index'])->name('profile');
               
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
    });

    // ---------- Khusus admin ----------
    Route::middleware('role:admin')->group(function () {
        Route::resource('kategori', KategoriController::class)->except(['create', 'show']);
        Route::resource('menu', MenuController::class)->except(['show']);
        Route::resource('pengguna', PenggunaController::class)->except(['show']);

        // Halaman contoh bawaan TailAdmin (hanya untuk referensi tampilan)
        Route::get('/calendar', function () {
            return view('pages.calendar', ['title' => 'Calendar']);
        })->name('calendar');

        Route::get('/form-elements', function () {
            return view('pages.forms.form-elements', ['title' => 'Form Elements']);
        })->name('form-elements');

        Route::get('/basic-tables', function () {
            return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
        })->name('basic-tables');

        Route::get('/blank', function () {
            return view('pages.pages.blank', ['title' => 'Blank']);
        })->name('blank');

        Route::get('/error-404', function () {
            return view('pages.errors.error-404', ['title' => 'Error 404']);
        })->name('error-404');

        Route::get('/line-chart', function () {
            return view('pages.chart.line-chart', ['title' => 'Line Chart']);
        })->name('line-chart');

        Route::get('/bar-chart', function () {
            return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
        })->name('bar-chart');

        Route::get('/alerts', function () {
            return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
        })->name('alerts');

        Route::get('/avatars', function () {
            return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
        })->name('avatars');

        Route::get('/badge', function () {
            return view('pages.ui-elements.badge', ['title' => 'Badges']);
        })->name('badge');

        Route::get('/buttons', function () {
            return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
        })->name('buttons');

        Route::get('/images', function () {
            return view('pages.ui-elements.images', ['title' => 'Images']);
        })->name('images');

        Route::get('/videos', function () {
            return view('pages.ui-elements.videos', ['title' => 'Videos']);
        })->name('videos');

        Route::redirect('/badges', '/badge');
    });
    
    // ==================== Area pelanggan (wajib login sebagai pelanggan) ====================
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
    Route::get('/akun/riwayat', [AkunController::class, 'riwayat'])->name('akun.riwayat');

    Route::get('/akun/reservasi/buat', [AkunController::class, 'buatReservasi'])->name('akun.reservasi.buat');
    Route::post('/akun/reservasi', [AkunController::class, 'simpanReservasi'])->name('akun.reservasi.simpan');
    Route::patch('/akun/reservasi/{reservasi}/batal', [AkunController::class, 'batalReservasi'])->name('akun.reservasi.batal');

    Route::get('/akun/pesan', [AkunController::class, 'pesanMenu'])->name('akun.pesan');
    Route::post('/akun/pesan', [AkunController::class, 'simpanPesanan'])->middleware('throttle:10,1')->name('akun.pesan.simpan');
    Route::get('/akun/pesanan/{pesanan}', [AkunController::class, 'detailPesanan'])->name('akun.pesanan');
    
    Route::get('/akun/profil', [AkunController::class, 'profil'])->name('akun.profil');
    Route::put('/akun/profil', [AkunController::class, 'simpanProfil'])->name('akun.profil.simpan');
    Route::put('/akun/password', [AkunController::class, 'gantiPassword'])->name('akun.password');
    Route::delete('/akun', [AkunController::class, 'nonaktifkan'])->name('akun.nonaktifkan');
});
    
});