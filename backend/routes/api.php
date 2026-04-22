<?php

use App\Http\Controllers\Api\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Api\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Api\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Api\Admin\RoomTypeController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Reception\ReservationController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────
Route::get('/home', [GuestController::class, 'home']);
Route::get('/room-types', [GuestController::class, 'roomTypes']);
Route::get('/room-types/{roomType}', [GuestController::class, 'roomType']);
Route::get('/search-rooms', [GuestController::class, 'searchRooms']);

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::patch('/me', [AuthController::class, 'updateProfile']);
    Route::put('/me/password', [AuthController::class, 'updatePassword']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // ── Customer ──────────────────────────────────────────────────────────────
    Route::prefix('my')->name('customer.')->group(function () {
        Route::get('/bookings', [CustomerBookingController::class, 'index']);
        Route::post('/bookings', [CustomerBookingController::class, 'store']);
        Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show']);
        Route::post('/bookings/{booking}/confirm', [CustomerBookingController::class, 'confirm']);
        Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel']);
    });

    // ── Reception ─────────────────────────────────────────────────────────────
    Route::middleware('system_portal:reception')->prefix('reception')->group(function () {
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::get('/reservations/{booking}', [ReservationController::class, 'show']);
        Route::post('/reservations/{booking}/confirm', [ReservationController::class, 'confirm']);
        Route::post('/reservations/{booking}/cancel', [ReservationController::class, 'cancel']);
        Route::post('/reservations/{booking}/check-in', [ReservationController::class, 'checkIn']);
        Route::post('/reservations/{booking}/check-out', [ReservationController::class, 'checkOut']);
        Route::post('/reservations/{booking}/services', [ReservationController::class, 'addService']);
        Route::patch('/rooms/{room}/status', [AdminRoomController::class, 'updateStatus']);
    });

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::middleware('system_portal:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Bookings
        Route::get('/bookings', [AdminBookingController::class, 'index']);
        Route::get('/bookings/stats', [AdminBookingController::class, 'stats']);

        // Room types
        Route::apiResource('room-types', RoomTypeController::class);

        // Rooms
        Route::apiResource('rooms', AdminRoomController::class);

        // Services
        Route::get('/services', [ServiceController::class, 'index']);
        Route::post('/services', [ServiceController::class, 'store']);
        Route::put('/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

        // Staff
        Route::apiResource('staff', StaffController::class);

        // Permissions
        Route::get('/permissions', [AdminPermissionController::class, 'index']);
        Route::post('/permissions/system-roles', [AdminPermissionController::class, 'storeSystemRole']);
        Route::patch('/permissions/{user}/role', [AdminPermissionController::class, 'updateRole']);
        Route::patch('/permissions/{user}/perms', [AdminPermissionController::class, 'updatePermissions']);
        Route::delete('/permissions/{user}/reset', [AdminPermissionController::class, 'resetPermissions']);

        // Chat / Messages
        Route::get('/messages', [AdminChatController::class, 'index']);
        Route::get('/messages/{conversation}', [AdminChatController::class, 'messages']);
        Route::post('/messages/{conversation}/reply', [AdminChatController::class, 'reply']);
    });
});
