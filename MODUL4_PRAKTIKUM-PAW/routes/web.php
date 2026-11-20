
<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

// 1. PUBLIC ROUTES
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog/{buku}', [HomeController::class, 'show'])->name('buku.publik.show');

// 2. ROUTES AUTENTIKASI
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// 3. ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD Data Buku
    Route::resource('buku', BukuController::class);

    // CRUD Data Anggota
    Route::resource('anggota', AnggotaController::class)
        ->parameters(['anggota' => 'anggota']);

    // Peminjaman dan Pengembalian
    Route::resource('peminjaman', PeminjamanController::class);

    Route::put('/peminjaman/{peminjaman}/kembali',
        [PeminjamanController::class, 'kembali'])
        ->name('peminjaman.kembali');
});
