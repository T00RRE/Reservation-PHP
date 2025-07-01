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
        
        $reservations = match($user->role) {
            'admin' => Reservation::with(['user', 'restaurant', 'table'])->latest()->paginate(20),
            'manager', 'staff' => Reservation::where('restaurant_id', $user->restaurant_id)
                ->with(['user', 'table'])->latest()->paginate(20),
            'customer' => Reservation::where('user_id', $user->id)->with(['restaurant', 'table'])->latest()->paginate(10),
            default => collect(),
        };

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create(Request $request)
    {
        dd($request->all()); 
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

        // Sprawdź czy stolik należy do restauracji
        $table = Table::where('id', $validated['restaurant_id'])
            ->where('restaurant_id', $validated['restaurant_id'])
            ->firstOrFail();

        // Sprawdź pojemność stolika
        if ($validated['guests_count'] > $table->capacity) {
    return back()->withErrors([
        'guests_count' => 'Liczba gości przekracza pojemność dostępnego stolika (' . $table->capacity . ' osób).'
    ])->withInput();
}

        // Sprawdź dostępność stolika
        if (!$table->isAvailableAt($validated['reservation_date'], $validated['reservation_time'])) {
            return back()->withErrors([
                'restaurant_id' => 'Stolik nie jest dostępny w wybranym terminie.'
            ])->withInput();
        }

        // Sprawdź limit rezerwacji na dzień (max 3 na użytkownika)
        $dailyReservations = Reservation::where('user_id', Auth::id())
            ->where('reservation_date', $validated['reservation_date'])
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($dailyReservations >= 3) {
            return back()->withErrors([
                'reservation_date' => 'Przekroczyłeś limit rezerwacji na ten dzień (maksymalnie 3).'
            ])->withInput();
        }

        // Sprawdź minimalny czas wyprzedzenia (2 godziny)
        $reservationDateTime = Carbon::parse($validated['reservation_date'] . ' ' . $validated['reservation_time']);
        if ($reservationDateTime->diffInHours(now()) < 2) {
            return back()->withErrors([
                'reservation_time' => 'Rezerwacja musi być dokonana z wyprzedzeniem co najmniej 2 godzin.'
            ])->withInput();
        }

        $reservationData = $validated;
        $reservationData['user_id'] = 1; // Tymczasowo ustawiamy użytkownika #1
        $reservationData['table_id'] = 1; // Tymczasowo ustawiamy stolik #1
        $reservationData['status'] = 'pending';

        $reservation = Reservation::create($reservationData);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Rezerwacja została złożona pomyślnie!');
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation)
    {
        // $this->authorize('view', $reservation);
        
        $reservation->load(['user', 'restaurant', 'table']);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified reservation.
     */
    public function edit(Reservation $reservation)
    {
        // $this->authorize('update', $reservation);

        if (!$reservation->canBeCancelled()) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Nie można edytować tej rezerwacji.');
        }

        $restaurant = $reservation->restaurant;
        $availableTables = $restaurant->tables()
            ->available()
            ->minCapacity($reservation->guests_count)
            ->get();

        return view('reservations.edit', compact('reservation', 'availableTables'));
    }

    /**
     * Update the specified reservation in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        // $this->authorize('update', $reservation);

        if (!$reservation->canBeCancelled()) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Nie można edytować tej rezerwacji.');
        }

        $validated = $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'guests_count' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $table = Table::where('id', $validated['table_id'])
            ->where('restaurant_id', $reservation->restaurant_id)
            ->firstOrFail();

        if ($validated['guests_count'] > $table->capacity) {
            return back()->withErrors([
                'guests_count' => 'Liczba gości przekracza pojemność stolika.'
            ])->withInput();
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
        // $this->authorize('delete', $reservation);

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
        // $this->authorize('confirm', $reservation);

        $reservation->confirm();

        return back()->with('success', 'Rezerwacja została potwierdzona.');
    }

    /**
     * Get available tables for given parameters
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