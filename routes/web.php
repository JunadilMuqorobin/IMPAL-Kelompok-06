<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LanggananPremiumController;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Auth Member (Login & Signup)
Route::get('/login', [AuthController::class, 'showMemberLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginMember']);

Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'registerMember']);

// Route Auth Admin
Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);

// Dashboard Member (Wajib Login Member)
Route::middleware(['auth:member'])->group(function () {
    Route::get('/member/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');
});

// Sisi Member: Self-Service Upgrade & Pembayaran Cashless
Route::get('/member/upgrade-premium', [LanggananPremiumController::class, 'index'])
    ->name('member.premium.index');

Route::post('/member/upgrade-premium', [LanggananPremiumController::class, 'store'])
    ->name('member.premium.store');

// Sisi Admin: Monitoring Transaksi Premium (Read-Only tanpa konfirmasi manual)
Route::get('/admin/monitoring/premium', [LanggananPremiumController::class, 'adminMonitoring'])
    ->name('admin.premium.monitoring');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('/', function () {
//     return view('welcome');
// });
