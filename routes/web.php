<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

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

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Manajemen Buku - Admin
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return redirect()->route('admin.books.index');
    })->name('admin.dashboard');

    Route::get('/admin/books', [BukuController::class, 'adminIndex'])->name('admin.books.index');
    Route::get('/admin/books/create', [BukuController::class, 'adminCreate'])->name('admin.books.create');
    Route::post('/admin/books', [BukuController::class, 'adminStore'])->name('admin.books.store');
    Route::get('/admin/books/statistics', [BukuController::class, 'statistics'])->name('admin.books.statistics');

    Route::get('/admin/books/{buku}', [BukuController::class, 'adminShow'])->name('admin.books.show');
    Route::get('/admin/books/{buku}/edit', [BukuController::class, 'adminEdit'])->name('admin.books.edit');
    Route::put('/admin/books/{buku}', [BukuController::class, 'adminUpdate'])->name('admin.books.update');
    Route::delete('/admin/books/{buku}', [BukuController::class, 'adminDestroy'])->name('admin.books.destroy');
    Route::patch('/admin/books/{buku}/status', [BukuController::class, 'updateStatus'])->name('admin.books.status');
    Route::post('/admin/books/{buku}/return', [BukuController::class, 'returnBook'])->name('admin.books.return');
});

// Katalog & Transaksi Buku - Member
Route::middleware(['auth:member'])->group(function () {
    Route::get('/member/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');

    Route::get('/member/books', [BukuController::class, 'index'])->name('member.books.index');
    Route::get('/member/books/borrowed', [BukuController::class, 'myBorrowed'])->name('member.books.borrowed');
    Route::get('/member/books/reservations', [BukuController::class, 'myReservations'])->name('member.books.reservations');
    Route::get('/member/books/reviews', [BukuController::class, 'reviewHistory'])->name('member.books.reviews');

    Route::get('/member/books/{buku}', [BukuController::class, 'show'])->name('member.books.show');
    Route::post('/member/books/{buku}/borrow', [BukuController::class, 'borrow'])->name('member.books.borrow');
    Route::post('/member/books/{buku}/reserve', [BukuController::class, 'reserve'])->name('member.books.reserve');
    Route::post('/member/books/{buku}/review', [BukuController::class, 'storeReview'])->name('member.books.review');

    Route::post('/member/bookings/{booking}/claim', [BukuController::class, 'claimReservation'])->name('member.bookings.claim');
    Route::post('/member/bookings/{booking}/cancel', [BukuController::class, 'cancelReservation'])->name('member.bookings.cancel');
});
