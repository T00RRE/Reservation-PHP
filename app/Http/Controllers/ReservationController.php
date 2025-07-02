<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            $user = (object)['role' => 'customer', 'id' => 1, 'name' => 'Test User', 'restaurant_id' => null];
        }
        
        $reservations = match($user->role) {
            'admin' => Reservation::with(['user', 'restaurant', 'table'])->latest()->paginate(20),
            'manager', 'staff' => Reservation::where('restaurant_id', $user->restaurant_id ?? 1)
                ->with(['user', 'table'])->latest()->paginate(20),
            'customer' => Reservation::where('user_id', $user->id)->with(['restaurant', 'table'])->latest()->paginate(10),
            default => Reservation::with(['user', 'restaurant', 'table'])->latest()->paginate(10),
        };

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create(Request $request)
    {
        $restaurantId = $request->get('restaurant_id');
        $restaurant = null;
        
        if ($restaurantId) {
            $restaurant = Restaurant::findOrFail($restaurantId);
        }

        $restaurants = Restaurant::active()->orderBy('name')->get();
        
        return view('reservations.create', compact('restaurant', 'restaurants'));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'guests_count' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Sprawdź minimalny czas wyprzedzenia (2 godziny)
        //$reservationDateTime = Carbon::parse($validated['reservation_date'] . ' ' . $validated['reservation_time']);
        //if ($reservationDateTime->diffInHours(now()) < 2) {
         //   return back()->withErrors([
          //      'reservation_time' => 'Rezerwacja musi być dokonana z wyprzedzeniem co najmniej 2 godzin.'
          //  ])->withInput();
//}

        // Znajdź restaurację
        $restaurant = Restaurant::findOrFail($validated['restaurant_id']);

        // Znajdź dostępny stolik
        $availableTables = $restaurant->getAvailableTables(
            $validated['reservation_date'],
            $validated['reservation_time'], 
            $validated['guests_count']
        );

        if ($availableTables->isEmpty()) {
            return back()->withErrors([
                'guests_count' => 'Brak dostępnych stolików na wybrany termin i liczbę gości. Spróbuj inną godzinę.'
            ])->withInput();
        }

        // Wybierz pierwszy dostępny stolik (najmniejszy wystarczający)
        $table = $availableTables->sortBy('capacity')->first();

        // Sprawdź limit rezerwacji na dzień (max 3 na użytkownika)
        $userId = Auth::id() ?? 1; // Tymczasowo użytkownik #1
        $dailyReservations = Reservation::where('user_id', $userId)
            ->where('reservation_date', $validated['reservation_date'])
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($dailyReservations >= 3) {
            return back()->withErrors([
                'reservation_date' => 'Przekroczyłeś limit rezerwacji na ten dzień (maksymalnie 3).'
            ])->withInput();
        }

        // Utwórz rezerwację
        $reservation = Reservation::create([
            'user_id' => $userId,
            'restaurant_id' => $validated['restaurant_id'],
            'table_id' => $table->id,
            'reservation_date' => $validated['reservation_date'],
            'reservation_time' => $validated['reservation_time'],
            'guests_count' => $validated['guests_count'],
            'special_requests' => $validated['special_requests'],
            'status' => 'pending'
        ]);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Rezerwacja została złożona pomyślnie! Stolik: ' . $table->table_number);
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'restaurant', 'table']);
        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified reservation.
     */
    public function edit(Reservation $reservation)
    {
        if (!$reservation->canBeCancelled()) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Nie można edytować tej rezerwacji.');
        }

        $restaurant = $reservation->restaurant;
        $availableTables = $restaurant->tables()
            ->where('capacity', '>=', $reservation->guests_count)
            ->where('status', 'available')
            ->get();

        return view('reservations.edit', compact('reservation', 'availableTables'));
    }

    /**
     * Update the specified reservation in storage.
     */
        public function update(Request $request, Reservation $reservation)
    {
        if (!$reservation->canBeCancelled()) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Nie można edytować tej rezerwacji.');
        }

        $validated = $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'guests_count' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Znajdź nowy stolik jeśli potrzeba
        if ($validated['guests_count'] != $reservation->guests_count) {
            $restaurant = $reservation->restaurant; // Załaduj relację
            $availableTables = $restaurant->getAvailableTables(
                $validated['reservation_date'],
                $validated['reservation_time'],
                $validated['guests_count']
            );

            if ($availableTables->isEmpty()) {
                return back()->withErrors([
                    'guests_count' => 'Brak dostępnych stolików na nową liczbę gości.'
                ])->withInput();
            }

            $validated['table_id'] = $availableTables->first()->id;
        }

        $reservation->update($validated);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Rezerwacja została zaktualizowana.');
    }

    /**
     * Remove the specified reservation from storage (cancel).
     */
    public function destroy(Reservation $reservation)
    {
        if (!$reservation->canBeCancelled()) {
            return back()->with('error', 'Nie można anulować tej rezerwacji.');
        }

        $reservation->cancel();

        return redirect()->route('reservations.index')
            ->with('success', 'Rezerwacja została anulowana.');
    }

    /**
     * Confirm reservation (for staff/manager)
     */
    public function confirm(Reservation $reservation)
    {
        $reservation->confirm();
        return back()->with('success', 'Rezerwacja została potwierdzona.');
    }

    /**
     * Get available tables for given parameters (API)
     */
    public function getAvailableTables(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'guests' => 'required|integer|min:1|max:20',
        ]);

        $restaurant = Restaurant::findOrFail($validated['restaurant_id']);
        $availableTables = $restaurant->getAvailableTables(
            $validated['date'],
            $validated['time'],
            $validated['guests']
        );

        return response()->json([
            'success' => true,
            'tables' => $availableTables->map(function ($table) {
                return [
                    'id' => $table->id,
                    'table_number' => $table->table_number,
                    'capacity' => $table->capacity,
                    'description' => $table->description,
                ];
            }),
        ]);
    }
}