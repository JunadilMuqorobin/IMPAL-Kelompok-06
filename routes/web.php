<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;

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

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('/', function () {
//     return view('welcome');
// });
