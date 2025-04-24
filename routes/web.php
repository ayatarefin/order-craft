<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // Logout route
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/download', [OrderController::class, 'downloadCompleted'])->name('orders.download');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');



    // Folders
    Route::get('/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');

    // Files
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::get('/files/{file}/edit', [FileController::class, 'edit'])->name('files.edit');
    Route::put('/files/{file}', [FileController::class, 'update'])->name('files.update');
    Route::post('/files/claim-batch', [FileController::class, 'claimBatch'])->name('files.claim-batch');
    Route::put('/files/{file}/complete', [FileController::class, 'markCompleted'])->name('files.complete');
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/employee-download', [FileController::class, 'employeeDownload'])->name('files.download.employee');
    // Show the photopea app in the web
    Route::get('/files/{file}/edit-online', [FileController::class, 'editOnline'])->name('files.edit-online');
    Route::post('/files/{file}/photopea-save', [FileController::class, 'saveFromPhotopea'])->name('files.photopea.save');



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
Route::get('/phpinfo', function () {
    phpinfo();
});
