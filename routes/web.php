<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

// Strona główna
Route::get('/', function () {
    return redirect()->route('restaurants.index');
});

// Publiczne trasy - dostępne bez logowania
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/restaurants/{restaurant}/menu', [RestaurantController::class, 'menu'])->name('restaurants.menu');

// Dashboard główny - przekierowuje na odpowiedni panel
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Trasy wymagające logowania
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Dashboardy specyficzne dla ról
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/manager/dashboard', [DashboardController::class, 'manager'])->name('manager.dashboard');
    Route::get('/customer/dashboard', [DashboardController::class, 'customer'])->name('customer.dashboard');
    
    // Rezerwacje (tylko dla zalogowanych)
    Route::resource('reservations', ReservationController::class);
    Route::patch('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::get('/api/available-tables', [ReservationController::class, 'getAvailableTables'])->name('api.available-tables');
});

// Laravel Breeze authentication routes
require __DIR__.'/auth.php';

// Fallback route
Route::fallback(function () {
    return redirect()->route('restaurants.index')->with('error', 'Strona nie została znaleziona.');
});