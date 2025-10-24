<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockDashboardController;
use App\Http\Controllers\StockImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/upload', [StockImportController::class, 'showUpload'])->name('upload.show');
    Route::post('/upload', [StockImportController::class, 'import'])->name('upload.import');
    Route::get('/dashboard', [StockDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
