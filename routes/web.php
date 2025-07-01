<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DashboardController;

// Strona główna
Route::get('/', function () {
    return redirect()->route('restaurants.index');
});

// Publiczne trasy - dostępne bez logowania
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/restaurants/{restaurant}/menu', [RestaurantController::class, 'menu'])->name('restaurants.menu');

// Tymczasowe trasy auth (zamiast Auth::routes())
Route::get('/login', function() { return 'Login page - do zrobienia'; })->name('login');
Route::get('/register', function() { return 'Register page - do zrobienia'; })->name('register');

// Dashboard (bez middleware)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/customer', [DashboardController::class, 'customer'])->name('dashboard.customer');

// Rezerwacje (bez middleware)
Route::resource('reservations', ReservationController::class);
Route::get('/api/available-tables', [ReservationController::class, 'getAvailableTables'])->name('api.available-tables');

// Fallback route
Route::fallback(function () {
    return redirect()->route('restaurants.index')->with('error', 'Strona nie została znaleziona.');
});