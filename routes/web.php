<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

// Setup routes (first time only)
Route::get('/', [SetupController::class, 'show'])->name('setup.show');
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/invites', [AdminController::class, 'createInvites'])->name('admin.invites.create');
        Route::post('/invites/{invite}/revoke', [AdminController::class, 'revokeInvite'])->name('admin.invites.revoke');
        Route::post('/invites/{invite}/reserve', [AdminController::class, 'reserveTable'])->name('admin.invites.reserve');
        Route::post('/invites/{invite}/assign', [AdminController::class, 'assignReservedTable'])->name('admin.invites.assign');
        Route::get('/invites/{invite}/download', [AdminController::class, 'downloadPass'])->name('admin.invites.download');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });
});

// Public invite routes
Route::prefix('invite')->group(function () {
    Route::get('/{token}', [InviteController::class, 'show'])->name('invite.show');
    Route::post('/{token}', [InviteController::class, 'redeem'])->name('invite.redeem');
    Route::get('/{token}/download', [InviteController::class, 'downloadPass'])->name('invite.download');
});
