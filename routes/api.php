<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// ==========================
// TEST ROUTE
// ==========================
Route::get('/test', function () {
    return response()->json(['message' => 'API route is working!']);
});

// ==========================
// AUTHENTICATION ROUTES
// ==========================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
});

// ==========================
// USER ROUTES (Protected)
// ==========================
Route::middleware(['auth:sanctum'])->group(function () {
    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show'); //remove
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // General user routes
    Route::put('/update-account', [UserController::class, 'update'])->name('users.update');
});

// ==========================
// VEHICLE ROUTES (Protected)
// ==========================
Route::middleware(['auth:sanctum'])->group(function () {
    // Public routes
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/{id}', [VehicleController::class, 'show'])->name('vehicles.show'); //remove

    // Owner-only routes
    Route::middleware('role:owner')->group(function () {
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::put('/vehicles/{id}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
        Route::get('/owner/vehicles', [VehicleController::class, 'getOwnerVehicles'])->name('vehicles.owner'); //remove
    });
});

// ==========================
// BOOKING ROUTES (Protected)
// ==========================
Route::middleware(['auth:sanctum'])->group(function () {
    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    });

    // General booking routes
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show'); //remove

    // Renter-only routes
    Route::middleware('role:renter')->group(function () {
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
        Route::get('/renter/bookings', [BookingController::class, 'getRenterBookings'])->name('bookings.renter'); //remove
    });

    // Owner-only routes
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/bookings', [BookingController::class, 'getOwnerBookings'])->name('bookings.owner'); //remove
    });
});
