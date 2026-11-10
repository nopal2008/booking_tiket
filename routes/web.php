<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PublicController;
use App\Models\Booking;
use Illuminate\Support\Facades\Route;

// Rute Publik
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/packages', [PublicController::class, 'packages'])->name('packages.index');
Route::get('/packages/{slug}/book', [PublicController::class, 'booking'])->name('packages.book');
Route::post('/booking', [PublicController::class, 'storeBooking'])->name('booking.store');
Route::get('/status', [PublicController::class, 'status'])->name('booking.status');

Route::get('/voucher/{code}', function (string $code) {
    $booking = Booking::with(['slot.package', 'items'])->where('booking_code', $code)->firstOrFail();
    return view('public.status', ['code' => $code, 'booking' => $booking]);
})->name('voucher.show');

// Rute Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/checkin', [CheckinController::class, 'index'])->name('checkin.index');
    Route::post('/checkin', [CheckinController::class, 'scan'])->name('checkin.scan');
});
