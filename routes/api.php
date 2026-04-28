<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItTeamWorkloadController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->middleware('role:admin');
    Route::post('/tickets/{ticket}/confirm-finished', [TicketController::class, 'confirmFinished'])->middleware('role:user');

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    Route::get('/it-team/workload', [ItTeamWorkloadController::class, 'index'])->middleware('role:user');
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function (): void {
    Route::apiResource('users', UserManagementController::class);
});
