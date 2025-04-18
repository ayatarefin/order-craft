<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    // Registration routes
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    
    // Password reset routes
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    // Logout route
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
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
    Route::get('/orders/{order}/stats', function (App\Models\Order $order) {
        return response()->json([
            'unclaimed' => $order->files()->where('status', 'unclaimed')->count(),
            'in_progress' => $order->files()->where('status', 'in_progress')->count(),
            'completed' => $order->files()->where('status', 'completed')->count(),
            'total' => $order->files()->count(),
        ]);
    });
});