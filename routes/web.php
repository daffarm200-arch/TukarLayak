<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - TukarLayak
|--------------------------------------------------------------------------
*/

// Halaman utama: Mengarahkan pengunjung ke katalog barang atau dashboard jika sudah login
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('items.index');
    }
    return redirect()->route('items.index');
})->name('home');

// Katalog Publik Barang (dapat dilihat oleh semua orang)
Route::get('/items', [ItemController::class, 'index'])->name('items.index');

// Grup Route untuk Pengguna Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    // Registrasi Pengguna Baru
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Login Pengguna
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Grup Route untuk Pengguna Terautentikasi (Sudah Login)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Dashboard Pengguna Biasa
    Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard');

    // Manajemen Barang Milik Pengguna
    Route::get('/my-items', [ItemController::class, 'myItems'])->name('items.my');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    // Pengajuan Permintaan Barang
    Route::get('/items/{item}/request', [ItemRequestController::class, 'create'])->name('requests.create');
    Route::post('/items/{item}/request', [ItemRequestController::class, 'store'])->name('requests.store');

    // Pengelolaan Permintaan Barang
    Route::get('/requests/incoming', [ItemRequestController::class, 'incoming'])->name('requests.incoming');
    Route::get('/requests/my', [ItemRequestController::class, 'myRequests'])->name('requests.my');
    Route::get('/requests/{itemRequest}', [ItemRequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{itemRequest}/accept', [ItemRequestController::class, 'accept'])->name('requests.accept');
    Route::post('/requests/{itemRequest}/reject', [ItemRequestController::class, 'reject'])->name('requests.reject');
    Route::post('/requests/{itemRequest}/cancel', [ItemRequestController::class, 'cancel'])->name('requests.cancel');
    Route::post('/requests/{itemRequest}/complete', [ItemRequestController::class, 'complete'])->name('requests.complete');

    // Grup Khusus Administrator (dilindungi middleware 'auth' & 'admin')
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen Pengguna
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');

        // Manajemen Kategori
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        // Manajemen Barang
        Route::get('/items', [AdminItemController::class, 'index'])->name('items.index');

        // Laporan Aktivitas & Ekspor CSV
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])->name('reports.export');
    });
});

// Detail Barang Publik (diletakkan setelah rute create dan request agar tidak salah tangkap parameter)
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
