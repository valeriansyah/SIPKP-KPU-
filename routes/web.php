<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Pelapor\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth Routes
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Application Web Routes
Route::middleware(['auth', 'role:pelapor'])->prefix('pelapor')->name('pelapor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pelapor'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [ReportController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [ReportController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{report}', [ReportController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/{report}/edit', [ReportController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{report}', [ReportController::class, 'update'])->name('laporan.update');
});

Route::middleware(['auth', 'role:sub_operator'])->prefix('sub-operator')->name('sub_operator.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'subOperator'])->name('dashboard');
    Route::get('/antrean', [ReportController::class, 'index'])->name('antrean');
    Route::get('/laporan/{report}', [ReportController::class, 'show'])->name('laporan.show');
    Route::post('/laporan/{report}/verifikasi', [VerificationController::class, 'store'])->name('laporan.verifikasi');
});

Route::middleware(['auth', 'role:operator_provinsi'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'operator'])->name('dashboard');
    Route::get('/monitoring', [ReportController::class, 'index'])->name('monitoring');
    Route::get('/laporan/{report}', [ReportController::class, 'show'])->name('laporan.show');
});

Route::middleware(['auth'])->group(function () {
    Route::apiResource('reports', ReportController::class)->except(['destroy']);

    // Document routes
    Route::post('reports/{report}/documents', [DocumentController::class, 'store']);
    Route::post('documents/{document}', [DocumentController::class, 'update']); // Use POST because file upload in PHP sometimes struggles with PUT
    Route::delete('documents/{document}', [DocumentController::class, 'destroy']);
    Route::get('documents/{document}/preview', [DocumentController::class, 'show'])->name('documents.preview');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Verification routes
    Route::post('reports/{report}/verify', [VerificationController::class, 'store']);
});
