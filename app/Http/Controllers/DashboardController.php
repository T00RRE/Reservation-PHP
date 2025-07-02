<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard dla administratora
     */
    public function admin()
    {
        // Sprawdź czy użytkownik jest zalogowany
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $stats = [
            'total_restaurants' => Restaurant::count(),
            'total_users' => User::count(),
            'total_reservations' => Reservation::count(),
            'pending_reviews' => 0, // Tymczasowo 0, bo nie mamy jeszcze Review CRUD
            'todays_reservations' => Reservation::whereDate('reservation_date', today())->count(),
            'active_restaurants' => Restaurant::where('is_active', true)->count(),
        ];

        $recentReservations = Reservation::with(['user', 'restaurant', 'table'])
            ->latest()
            ->take(10)
            ->get();

        $pendingReviews = collect(); // Pusta kolekcja na razie

        return view('dashboard.admin', compact('stats', 'recentReservations', 'pendingReviews'));
    }

    /**
     * Dashboard dla menedżera restauracji
     */
    public function manager()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Nie jesteś przypisany do żadnej restauracji.');
        }

        $stats = [
            'total_tables' => $restaurant->tables()->count(),
            'todays_reservations' => Reservation::where('restaurant_id', $restaurant->id)->whereDate('reservation_date', today())->count(),
            'upcoming_reservations' => Reservation::where('restaurant_id', $restaurant->id)->where('reservation_date', '>=', today())->count(),
            'total_reviews' => 0, // Tymczasowo 0
            'average_rating' => $restaurant->rating,
            'pending_reservations' => Reservation::where('restaurant_id', $restaurant->id)->where('status', 'pending')->count(),
        ];

        $todaysReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->whereDate('reservation_date', today())
            ->with(['user', 'table'])
            ->orderBy('reservation_time')
            ->get();

        $recentReviews = collect(); // Pusta kolekcja

        return view('dashboard.manager', compact('restaurant', 'stats', 'todaysReservations', 'recentReviews'));
    }

    /**
     * Dashboard dla personelu restauracji
     */
    public function staff()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Nie jesteś przypisany do żadnej restauracji.');
        }

        $todaysReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->whereDate('reservation_date', today())
            ->with(['user', 'table'])
            ->orderBy('reservation_time')
            ->get();

        $upcomingReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->where('reservation_date', '>', today())
            ->where('reservation_date', '<=', today()->addDays(7))
            ->with(['user', 'table'])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get();

        return view('dashboard.staff', compact('restaurant', 'todaysReservations', 'upcomingReservations'));
    }

    /**
     * Dashboard dla klienta - POPRAWIONA WERSJA
     */
    public function customer()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Bezpieczne pobranie statystyk
        $stats = [
            'total_reservations' => Reservation::where('user_id', $user->id)->count(),
            'upcoming_reservations' => Reservation::where('user_id', $user->id)
                ->where('reservation_date', '>=', today())
                ->count(),
            'total_reviews' => 0, // Review::where('user_id', $user->id)->count() - gdy będzie model Review
            'average_given_rating' => 0, // Średnia ocen wystawionych przez użytkownika
        ];

        // Nadchodzące rezerwacje
        $upcomingReservations = Reservation::where('user_id', $user->id)
            ->where('reservation_date', '>=', today())
            ->with(['restaurant', 'table'])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->take(5)
            ->get();

        // Ostatnie rezerwacje
        $recentReservations = Reservation::where('user_id', $user->id)
            ->with(['restaurant', 'table'])
            ->latest()
            ->take(10)
            ->get();

        // Polecane restauracje (zamiast ulubionych)
        $favoriteRestaurants = Restaurant::where('is_active', true)
            ->orderBy('rating', 'desc')
            ->take(6)
            ->get();

        return view('dashboard.customer', compact(
            'stats', 
            'upcomingReservations', 
            'recentReservations', 
            'favoriteRestaurants'
        ));
    }

    /**
     * Główna strona dashboard - przekierowuje na odpowiedni dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'manager', 'staff' => redirect()->route('manager.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => redirect()->route('restaurants.index'),
        };
    }
}