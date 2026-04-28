<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login.form');
});

Route::prefix('admin')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login.form');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    });

    Route::middleware('auth')->group(function (): void {
        Route::middleware('ensure.admin')->group(function (): void {
            Route::get('/', fn () => redirect()->route('admin.tickets.index'))->name('admin.home');
            Route::get('notifications/feed', [AdminNotificationController::class, 'feed'])->name('admin.notifications.feed');
            Route::post('notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('admin.notifications.mark-all-read');
            Route::get('tickets/realtime', [AdminTicketController::class, 'realtime'])->name('admin.tickets.realtime');
            Route::patch('tickets/{ticket}/quick-update', [AdminTicketController::class, 'quickUpdate'])->name('admin.tickets.quick-update');
            Route::resource('tickets', AdminTicketController::class)->except('show')->names('admin.tickets');
            Route::resource('users', AdminUserController::class)->except('show')->names('admin.users');
        });

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});
