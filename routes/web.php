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

    Route::middleware(['can:manage-master-data'])->prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\MasterDataController::class, 'index'])->name('index');
        Route::resource('districts', \App\Http\Controllers\Operator\DistrictController::class)->except(['show', 'destroy']);
        Route::post('districts/{district}/destroy', [\App\Http\Controllers\Operator\DistrictController::class, 'destroy'])->name('districts.destroy'); // use post for safe delete
        Route::resource('document-types', \App\Http\Controllers\Operator\DocumentTypeController::class)->except(['show', 'destroy']);
        Route::post('document-types/{document_type}/destroy', [\App\Http\Controllers\Operator\DocumentTypeController::class, 'destroy'])->name('document-types.destroy');
        Route::resource('report-statuses', \App\Http\Controllers\Operator\ReportStatusController::class)->only(['index', 'edit', 'update']);
        Route::resource('sub-operators', \App\Http\Controllers\Operator\SubOperatorController::class)->except(['show', 'destroy'])->middleware('can:manage-sub-operator');
    });
});

Route::middleware(['auth'])->group(function () {

    Route::post('documents/{document}', [DocumentController::class, 'update']); // Use POST because file upload in PHP sometimes struggles with PUT
    Route::delete('documents/{document}', [DocumentController::class, 'destroy']);
    Route::get('documents/{document}/preview', [DocumentController::class, 'show'])->name('documents.preview');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');


});
