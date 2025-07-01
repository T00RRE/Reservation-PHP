<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    /**
     * Display a listing of restaurants.
     */
    public function index()
    {
        $restaurants = Restaurant::where('is_active', true)
    ->orderBy('rating', 'desc')
    ->paginate(10);

        return view('restaurants.index', compact('restaurants'));
    }

    /**
     * Show the form for creating a new restaurant.
     */
    public function create()
    {
        // $this->authorize('create', Restaurant::class);
        
        return view('restaurants.create');
    }

    /**
     * Store a newly created restaurant in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Restaurant::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant = Restaurant::create($validated);

        return redirect()->route('restaurants.show', $restaurant)
            ->with('success', 'Restauracja została utworzona pomyślnie.');
    }

    /**
     * Display the specified restaurant.
     */
    public function show(Restaurant $restaurant)
    {
        $restaurant->load(['menus.categories.dishes', 'workingHours', 'reviews.user']);
        
        // Pobierz dostępne stoliki na dziś
      //  $availableTables = $restaurant->tables()
        //    ->available()
       //     ->orderBy('capacity')
       //     ->get();

        return view('restaurants.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified restaurant.
     */
    public function edit(Restaurant $restaurant)
    {
        // $this->authorize('update', $restaurant);

        return view('restaurants.edit', compact('restaurant'));
    }

    /**
     * Update the specified restaurant in storage.
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        // $this->authorize('update', $restaurant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant->update($validated);

        return redirect()->route('restaurants.show', $restaurant)
            ->with('success', 'Restauracja została zaktualizowana.');
    }

    /**
     * Remove the specified restaurant from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        // $this->authorize('delete', $restaurant);

        $restaurant->delete();

        return redirect()->route('restaurants.index')
            ->with('success', 'Restauracja została usunięta.');
    }

    /**
     * Show available tables for reservation
     */
    public function availableTables(Restaurant $restaurant, Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $time = $request->get('time', '18:00');
        $guests = $request->get('guests', 2);

        $availableTables = $restaurant->getAvailableTables($date, $time, $guests);

        return response()->json([
            'tables' => $availableTables,
            'date' => $date,
            'time' => $time,
            'guests' => $guests,
        ]);
    }

    /**
     * Show restaurant menu
     */
    public function menu(Restaurant $restaurant)
    {
        $menus = $restaurant->menus()
            ->active()
            ->with(['categories' => function($query) {
                $query->active()->ordered()->with(['dishes' => function($dishQuery) {
                    $dishQuery->available()->ordered();
                }]);
            }])
            ->ordered()
            ->get();

        return view('restaurants.menu', compact('restaurant', 'menus'));
    }
}