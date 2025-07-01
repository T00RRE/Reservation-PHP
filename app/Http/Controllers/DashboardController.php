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
        // $this->authorize('admin-dashboard');

        $stats = [
            'total_restaurants' => Restaurant::count(),
            'total_users' => User::count(),
            'total_reservations' => Reservation::count(),
            'pending_reviews' => Review::pending()->count(),
            'todays_reservations' => Reservation::whereDate('reservation_date', today())->count(),
            'active_restaurants' => Restaurant::active()->count(),
        ];

        $recentReservations = Reservation::with(['user', 'restaurant', 'table'])
            ->latest()
            ->take(10)
            ->get();

        $pendingReviews = Review::pending()
            ->with(['user', 'restaurant', 'dish'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentReservations', 'pendingReviews'));
    }

    /**
     * Dashboard dla menedżera restauracji
     */
    public function manager()
    {
        $user = Auth::user();
        if (!$user) {
            $user = (object)['role' => 'manager', 'id' => 1, 'name' => 'Test Manager', 'restaurant' => null];
        }
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('dashboard.customer')
                ->with('error', 'Nie jesteś przypisany do żadnej restauracji.');
        }

        $stats = [
            'total_tables' => $restaurant->tables()->count(),
            'todays_reservations' => Reservation::where('restaurant_id', $restaurant->id)->whereDate('reservation_date', today())->count(),
            'upcoming_reservations' => Reservation::where('restaurant_id', $restaurant->id)->where('reservation_date', '>=', today())->count(),
            'total_reviews' => Review::where('restaurant_id', $restaurant->id)->where('is_approved', true)->count(),
            'average_rating' => $restaurant->rating,
            'pending_reservations' => Reservation::where('restaurant_id', $restaurant->id)->where('status', 'pending')->count(),
        ];

        $todaysReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->whereDate('reservation_date', today())
            ->with(['user', 'table'])
            ->orderBy('reservation_time')
            ->get();

        $recentReviews = Review::where('restaurant_id', $restaurant->id)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.manager', compact('restaurant', 'stats', 'todaysReservations', 'recentReviews'));
    }

    /**
     * Dashboard dla personelu restauracji
     */
    public function staff()
    {
        $user = Auth::user();
        if (!$user) {
            $user = (object)['role' => 'staff', 'id' => 1, 'name' => 'Test Staff', 'restaurant' => null];
        }
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('dashboard.customer')
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
     * Dashboard dla klienta
     */
    public function customer()
    {

        $user = Auth::user();
        if (!$user) {
            $user = (object)['role' => 'customer', 'id' => 1, 'name' => 'Test Customer'];
        }

        $stats = [
            'total_reservations' => Reservation::where('user_id', $user->id)->count(),
            'upcoming_reservations' => Reservation::where('user_id', $user->id)->where('reservation_date', '>=', today())->count(),
            'total_reviews' => Review::where('user_id', $user->id)->count(),
        ];

        $upcomingReservations = Reservation::where('user_id', $user->id)
            ->where('reservation_date', '>=', today())
            ->with(['restaurant', 'table'])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->take(5)
            ->get();

        $recentReservations = Reservation::where('user_id', $user->id)
            ->with(['restaurant', 'table'])
            ->latest()
            ->take(10)
            ->get();

        $favoriteRestaurants = Restaurant::where('is_active', true)
            ->orderBy('rating', 'desc')
            ->take(6)
            ->get();

        return view('dashboard.customer', compact('stats', 'upcomingReservations', 'recentReservations', 'favoriteRestaurants'));
    }

    /**
     * Główna strona dashboard - przekierowuje na odpowiedni dashboard
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            $user = (object)['role' => 'admin', 'id' => 1, 'name' => 'Test Admin', 'restaurant' => null];
        }
        return match($user->role) {
            'admin' => $this->admin(),
            'manager' => $this->manager(),
            'staff' => $this->staff(),
            'customer' => $this->customer(),
            default => redirect()->route('restaurants.index'),
        };
    }
}