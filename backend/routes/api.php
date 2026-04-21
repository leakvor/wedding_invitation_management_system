<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\GuestMoneyController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/public/check-invitation', [InvitationController::class, 'checkInvitation']);
Route::put('/public/rsvp/{guest}', [InvitationController::class, 'updateRsvp']);
Route::get('/public/content', [InvitationController::class, 'content']);
Route::get('/public/invitations/{slug}', [InvitationController::class, 'show']);
Route::post('/public/invitations/{slug}/comments', [InvitationController::class, 'storeComment']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/guests/search', [GuestController::class, 'search']);
    Route::get('/guests/export', [GuestController::class, 'export']);
    Route::apiResource('guests', GuestController::class);
    Route::post('/guests/{guest}/money', [GuestMoneyController::class, 'store']);

    Route::get('/stats', [DashboardController::class, 'stats']);
    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);
    Route::post('/settings/upload', [SettingController::class, 'upload']);
    Route::get('/guests/{guest}/share', [GuestController::class, 'share']);
});
