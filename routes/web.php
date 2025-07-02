<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
// Strona główna
Route::get('/', function () {
    return redirect()->route('restaurants.index');
});

// Publiczne trasy dla restauracji - ZMIEŃ KOLEJNOŚĆ!
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');

// Chronione trasy (muszą być PRZED trasami z {restaurant})
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');  // <- TO MUSI BYĆ PRZED {restaurant}
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
    Route::get('/restaurants/{restaurant}/edit', [RestaurantController::class, 'edit'])->name('restaurants.edit');
    Route::put('/restaurants/{restaurant}', [RestaurantController::class, 'update'])->name('restaurants.update');
    Route::delete('/restaurants/{restaurant}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');
    Route::patch('/restaurants/{restaurant}/activate', [RestaurantController::class, 'activate'])->name('restaurants.activate');
});
// CRUD dla menu (admin + manager)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('menus', MenuController::class);
    Route::patch('/menus/{menu}/activate', [MenuController::class, 'activate'])->name('menus.activate');
});
// Publiczne trasy z parametrami - MUSZĄ BYĆ OSTATNIE
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
    Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tables', TableController::class);
});
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