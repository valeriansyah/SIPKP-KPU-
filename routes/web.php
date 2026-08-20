<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register/send-otp', [AuthController::class, 'sendRegistrationOTP']);
Route::post('/register/verify-otp', [AuthController::class, 'verifyRegistrationOTP']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth Routes
Route::get('/auth/google/redirect', [\App\Http\Controllers\GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::post('/forgot-password/send-otp', [AuthController::class, 'sendResetOTP']);
Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyResetOTP']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);

// Application Web Routes
Route::middleware(['auth', 'role:pelapor'])->prefix('pelapor')->name('pelapor.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'pelapor'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Pelapor\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Pelapor\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/laporan', [\App\Http\Controllers\ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [\App\Http\Controllers\ReportController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [\App\Http\Controllers\ReportController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{report}', [\App\Http\Controllers\ReportController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/{report}/edit', [\App\Http\Controllers\ReportController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{report}', [\App\Http\Controllers\ReportController::class, 'update'])->name('laporan.update');
});

Route::middleware(['auth', 'role:sub_operator'])->prefix('sub-operator')->name('sub_operator.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'subOperator'])->name('dashboard');
    Route::get('/antrean', [\App\Http\Controllers\ReportController::class, 'index'])->name('antrean');
    Route::get('/laporan/{report}', [\App\Http\Controllers\ReportController::class, 'show'])->name('laporan.show');
    Route::post('/laporan/{report}/verifikasi', [\App\Http\Controllers\VerificationController::class, 'store'])->name('laporan.verifikasi');
});

Route::middleware(['auth', 'role:operator_provinsi'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'operator'])->name('dashboard');
    Route::get('/monitoring', [\App\Http\Controllers\ReportController::class, 'index'])->name('monitoring');
    Route::get('/laporan/{report}', [\App\Http\Controllers\ReportController::class, 'show'])->name('laporan.show');
});

Route::middleware(['auth'])->group(function () {
    Route::apiResource('reports', \App\Http\Controllers\ReportController::class)->except(['destroy']);
    
    // Document routes
    Route::post('reports/{report}/documents', [\App\Http\Controllers\DocumentController::class, 'store']);
    Route::post('documents/{document}', [\App\Http\Controllers\DocumentController::class, 'update']); // Use POST because file upload in PHP sometimes struggles with PUT
    Route::delete('documents/{document}', [\App\Http\Controllers\DocumentController::class, 'destroy']);
    
    // Verification routes
    Route::post('reports/{report}/verify', [\App\Http\Controllers\VerificationController::class, 'store']);
});
