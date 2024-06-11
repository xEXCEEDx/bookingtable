<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ImageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::patch('/tables/{table}', [TableController::class, 'update']->name('tables.update'));

Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
Route::post('/tables/set-total', [TableController::class, 'setTotal'])->name('tables.setTotal');


Route::patch('/tables/{table}/update-status', [TableController::class, 'updateStatus'])->name('tables.updateStatus');
Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::post('/booking/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations');
Route::get('/reservations/clear-all', [ReservationController::class, 'clearAllReservations'])->name('reservations.clearAll');
Route::put('/reservations/{id}/update-table', [ReservationController::class, 'updateTable'])->name('reservations.updateTable');
Route::delete('reservations/{reservation}/update-table', [ReservationController::class, 'updateTable'])->name('reservations.updateTable');
Route::get('/reservations/search', [ReservationController::class, 'search'])->name('reservations.search');
Route::get('/userreservations', [ReservationController::class, 'userreservations'])->name('userreservations');

Route::get('/', [BookingController::class, 'showDates'])->name('booking_dates');
Route::get('/reservations', [BookingController::class, 'reservations'])->name('reservations');


Route::get('/date', function () {
    return view('date-selection');
})->name('date.selection');
Route::post('/reservations', [ReservationController::class, 'create'])->name('reservations.store');
Route::post('/tables/updateStatusForDate', [TableController::class, 'updateStatusForDate'])->name('tables.updateStatusForDate');
Route::post('/tables/reserve', [TableController::class, 'reserveTable'])->name('tables.reserve');

Route::get('/tables/manage/{date?}', [TableController::class, 'manageTables'])->name('tables.manage');

Route::get('/upload', [ImageController::class, 'showUploadForm'])->name('upload.form');
Route::post('/upload', [ImageController::class, 'uploadImage'])->name('upload.image');
});



require __DIR__.'/auth.php';



























