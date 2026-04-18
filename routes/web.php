<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\AIChatbotController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CustomerAccountController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HotelRoomController;
use App\Http\Controllers\Admin\HotelServiceController;
use App\Http\Controllers\Admin\PasswordChangeRequestController as AdminPasswordChangeRequestController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\PhpEnvironmentController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Guest\ChatWidgetController;
use App\Http\Controllers\Guest\SearchController as GuestSearchController;
use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Guest\RoomSearchController;
use App\Http\Controllers\InvoiceShowController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reception\DashboardController as ReceptionDashboardController;
use App\Http\Controllers\Reception\InvoicePdfController;
use App\Http\Controllers\Reception\PasswordChangeRequestController as ReceptionPasswordChangeRequestController;
use App\Http\Controllers\Reception\ReservationController as ReceptionReservationController;
use App\Http\Controllers\Reception\ReservationServiceController as ReceptionReservationServiceController;
use App\Http\Controllers\Reception\RoomController as ReceptionRoomController;
use App\Http\Controllers\Reception\StayController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', HomeController::class)->name('home');
Route::post('/ai-chat', [AIChatbotController::class, 'chat'])->name('ai.chat');
Route::get('/search-rooms', [RoomSearchController::class, 'index'])->name('guest.search-rooms');
Route::get('/search/suggest', [GuestSearchController::class, 'suggest'])->name('guest.search.suggest');
Route::get('/search', [RoomSearchController::class, 'index'])->name('guest.search');

Route::middleware(['auth', 'role:'.User::ROLE_CUSTOMER])->group(function () {
    Route::get('/widget-chat/messages', [ChatWidgetController::class, 'fetch'])->name('guest.chat.messages');
    Route::post('/widget-chat/messages', [ChatWidgetController::class, 'store'])->name('guest.chat.store');
});

Route::post('/vnpay/create-payment', [PaymentController::class, 'create'])->name('vnpay.create-payment');
Route::get('/vnpay/vnpay-return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
Route::get('/vnpay/continue-booking/{booking}', [BookingController::class, 'continueAfterVnpay'])
    ->middleware('signed')
    ->name('booking.vnpay-continue');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-bookings', function () {
        return redirect()->route('customer.bookings.index');
    })->name('my-bookings');
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isStaff()) {
            return redirect()->route('reception.dashboard');
        }

        return redirect()->route('customer.bookings.index');
    })->name('dashboard');

    Route::middleware('role:'.User::ROLE_CUSTOMER)->group(function () {
        Route::get('/booking/{room}', [BookingController::class, 'createForRoom'])->name('customer.bookings.create-room');
        Route::post('/booking/{room}', [BookingController::class, 'storeForRoom'])->name('customer.bookings.store-room');
    });

    Route::middleware('role:'.User::ROLE_CUSTOMER)->prefix('my')->name('customer.')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{booking}/review', [BookingController::class, 'review'])->name('bookings.review');
        Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirmReview'])->name('bookings.confirm-review');
        Route::get('/bookings/{booking}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
        Route::get('/bookings/{booking}/payment/{gateway}', [BookingController::class, 'paymentGateway'])
            ->where('gateway', 'vnpay|momo')
            ->name('bookings.payment.gateway');
        Route::post('/bookings/{booking}/payment/complete', [BookingController::class, 'paymentComplete'])->name('bookings.payment.complete');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    });

    Route::get('/invoices/{invoice}', InvoiceShowController::class)->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', InvoicePdfController::class)->name('invoices.pdf');

    Route::middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_RECEPTIONIST.','.User::ROLE_MANAGER.','.User::ROLE_ACCOUNTANT)->prefix('reception')->name('reception.')->group(function () {
        Route::get('/', ReceptionDashboardController::class)->name('dashboard');
        Route::get('/reservations', [ReceptionReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{booking}', [ReceptionReservationController::class, 'show'])->name('reservations.show');
        Route::post('/reservations/{booking}/confirm', [ReceptionReservationController::class, 'confirm'])->name('reservations.confirm');
        Route::post('/reservations/{booking}/cancel', [ReceptionReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::get('/reservations/{booking}/check-in-form', [StayController::class, 'checkInForm'])->name('stays.check-in-form');
        Route::post('/reservations/{booking}/check-in', [StayController::class, 'checkIn'])->name('stays.check-in');
        Route::post('/reservations/{booking}/check-in-complete', [StayController::class, 'completeCheckIn'])->name('stays.check-in-complete');
        Route::get('/reservations/{booking}/checkout-payment', [StayController::class, 'checkoutPayment'])->name('stays.checkout-payment');
        Route::post('/reservations/{booking}/checkout-cash', [StayController::class, 'checkOutCash'])->name('stays.check-out-cash');
        Route::post('/reservations/{booking}/check-out', [StayController::class, 'checkOut'])->name('stays.check-out');
        Route::post('/reservations/{booking}/services', [ReceptionReservationServiceController::class, 'store'])->name('reservation-services.store');
        Route::patch('/rooms/{room}/status', [ReceptionRoomController::class, 'updateStatus'])->name('rooms.status');
    });

    Route::middleware('role:'.User::ROLE_ADMIN)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
        Route::get('/system/php-upload', PhpEnvironmentController::class)->name('system.php-upload');

        Route::resource('room-types', RoomTypeController::class)->except(['show']);
        Route::resource('hotel-rooms', HotelRoomController::class)->parameters(['hotel-rooms' => 'room'])->except(['show']);
        Route::resource('services', HotelServiceController::class)->except(['show']);

        Route::resource('staff', StaffController::class)->except(['show']);
        Route::get('/customers', [CustomerAccountController::class, 'index'])->name('customers.index');

        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/check-in', [StayController::class, 'checkIn'])->name('bookings.check-in');

        Route::get('/messages', [AdminChatController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}/json', [AdminChatController::class, 'messages'])->name('messages.json');
        Route::post('/messages/{conversation}/reply', [AdminChatController::class, 'reply'])->name('messages.reply');

        Route::get('/password-change-requests', [AdminPasswordChangeRequestController::class, 'index'])->name('password-change-requests.index');
        Route::post('/password-change-requests/{passwordChangeRequest}/approve', [AdminPasswordChangeRequestController::class, 'approve'])->name('password-change-requests.approve');
        Route::post('/password-change-requests/{passwordChangeRequest}/reject', [AdminPasswordChangeRequestController::class, 'reject'])->name('password-change-requests.reject');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::patch('/permissions/{user}/role', [PermissionController::class, 'updateRole'])->name('permissions.update-role');
        Route::patch('/permissions/{user}/perms', [PermissionController::class, 'updatePermissions'])->name('permissions.update-perms');
        Route::delete('/permissions/{user}/reset', [PermissionController::class, 'resetPermissions'])->name('permissions.reset');
        Route::patch('/permissions/{user}', [PermissionController::class, 'update'])->name('permissions.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/password-change-request', [ReceptionPasswordChangeRequestController::class, 'store'])->name('profile.password-change-request.store');
});
