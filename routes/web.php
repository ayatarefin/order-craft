<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/download', [OrderController::class, 'downloadCompleted'])->name('orders.download');
    
    // Folders
    Route::get('/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');
    
    // Files
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::get('/files/{file}/edit', [FileController::class, 'edit'])->name('files.edit');
    Route::put('/files/{file}', [FileController::class, 'update'])->name('files.update');
    Route::post('/files/claim-batch', [FileController::class, 'claimBatch'])->name('files.claim-batch');
    Route::put('/files/{file}/complete', [FileController::class, 'markCompleted'])->name('files.complete');
    Route::get('/files/{file}/open-in-editor', [FileController::class, 'openInEditor'])->name('files.open-in-editor');
    
    // Users (Admin only)
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});