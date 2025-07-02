<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
class RestaurantController extends Controller
{
    /**
     * Display a listing of restaurants.
     */
    public function index(Request $request)
    {
        $query = Restaurant::query();
        
        // Wyszukiwanie
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('address', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }
        
        // Filtrowanie po ocenie
        if ($request->rating) {
            $query->where('rating', '>=', $request->rating);
        }
        
        // Filtrowanie po statusie (aktywne/nieaktywne)
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        // Filtrowanie po kuchni (wyszukiwanie w opisie)
        if ($request->cuisine) {
            $query->where('description', 'like', "%{$request->cuisine}%");
        }
        
        // Sortowanie
        $sortBy = $request->sort ?? 'name';
        $sortDirection = $request->direction ?? 'asc';
        
        $allowedSorts = ['name', 'rating', 'created_at', 'address'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        // Tylko aktywne dla gości
        if (!Auth::check() || Auth::user()->role === 'customer') {
            $query->where('is_active', true);
        }
        
        $restaurants = $query->withCount(['tables'])
                           ->paginate(12)
                           ->appends($request->query());

        // Top restauracje dla strony głównej
        $topRestaurants = Restaurant::where('is_active', true)
            ->where('rating', '>=', 4.5)
            ->orderBy('rating', 'desc')
            ->take(3)
            ->get();

        return view('restaurants.index', compact('restaurants', 'topRestaurants'));
    }

    /**
     * Show the form for creating a new restaurant.
     */
    public function create()
    {
        // Tylko admin może dodawać restauracje
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Brak uprawnień do dodawania restauracji.');
        }
        
        return view('restaurants.create');
    }

    /**
     * Store a newly created restaurant in storage.
     */
    public function store(Request $request)
    {
        // Tylko admin może dodawać restauracje
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Brak uprawnień do dodawania restauracji.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:restaurants,name',
            'description' => 'nullable|string|min:10|max:2000',
            'address' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^\+48[0-9]{9}$/'],
            'email' => 'required|email|max:255|unique:restaurants,email',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ], [
            'name.unique' => 'Restauracja o tej nazwie już istnieje.',
            'email.unique' => 'Adres email jest już używany przez inną restaurację.',
            'phone.regex' => 'Numer telefonu musi być w formacie +48xxxxxxxxx',
            'description.min' => 'Opis musi mieć co najmniej 10 znaków.',
            'image.image' => 'Plik musi być obrazem.',
            'image.max' => 'Obraz nie może być większy niż 2MB.',
        ]);

        // Upload obrazu
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
    $restaurant->load([
        'tables' => function($query) {
            $query->where('status', 'available')->orderBy('capacity');
        },
        'menus.categories.dishes' => function($query) {
            $query->where('is_available', true)->orderBy('sort_order');
        },
        'workingHours' => function($query) {
            $query->orderBy('day_of_week');
        },
        'reviews' => function($query) {
            $query->where('is_approved', true)->with('user')->latest()->take(5);
        }
    ]);
    
    // Statystyki BEZ reservations
    $stats = [
        'total_tables' => $restaurant->tables()->count(),
        'available_tables' => $restaurant->tables()->where('status', 'available')->count(),
        'total_reviews' => $restaurant->reviews()->where('is_approved', true)->count(),
        'avg_rating' => $restaurant->reviews()->where('is_approved', true)->avg('rating'),
        // USUŃ tę linię: 'total_reservations' => $restaurant->reservations()->count(),
    ];

    return view('restaurants.show', compact('restaurant', 'stats'));
}

    /**
     * Show the form for editing the specified restaurant.
     */
    public function edit(Restaurant $restaurant)
    {
        $user = Auth::user();
        
        // Sprawdź uprawnienia
        if (!$user || (!$user->isAdmin() && !($user->isManager() && $user->restaurant_id === $restaurant->id))) {
            abort(403, 'Brak uprawnień do edycji tej restauracji.');
        }

        return view('restaurants.edit', compact('restaurant'));
    }

    /**
     * Update the specified restaurant in storage.
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $user = Auth::user();
        
        // Sprawdź uprawnienia
        if (!$user || (!$user->isAdmin() && !($user->isManager() && $user->restaurant_id === $restaurant->id))) {
            abort(403, 'Brak uprawnień do edycji tej restauracji.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:restaurants,name,' . $restaurant->id,
            'description' => 'nullable|string|min:10|max:2000',
            'address' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^\+48[0-9]{9}$/'],
            'email' => 'required|email|max:255|unique:restaurants,email,' . $restaurant->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ], [
            'name.unique' => 'Restauracja o tej nazwie już istnieje.',
            'email.unique' => 'Adres email jest już używany przez inną restaurację.',
            'phone.regex' => 'Numer telefonu musi być w formacie +48xxxxxxxxx',
            'description.min' => 'Opis musi mieć co najmniej 10 znaków.',
        ]);

        // Upload nowego obrazu
        if ($request->hasFile('image')) {
            // Usuń stary obraz
            if ($restaurant->image && Storage::disk('public')->exists($restaurant->image)) {
                Storage::disk('public')->delete($restaurant->image);
            }
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant->update($validated);

        return redirect()->route('restaurants.show', $restaurant)
            ->with('success', 'Restauracja została zaktualizowana.');
    }

    /**
     * Remove the specified restaurant from storage (dezaktywacja).
     */
    public function destroy(Restaurant $restaurant)
{
    // Tylko admin może usuwać restauracje
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        abort(403, 'Brak uprawnień do usuwania restauracji.');
    }

    // "Miękkie" usunięcie - dezaktywacja (bez sprawdzania rezerwacji)
    $restaurant->update(['is_active' => false]);

    return redirect()->route('restaurants.index')
        ->with('success', 'Restauracja została dezaktywowana.');
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
            ->where('is_active', true)
            ->with(['categories' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order')->with(['dishes' => function($dishQuery) {
                    $dishQuery->where('is_available', true)->orderBy('sort_order');
                }]);
            }])
            ->orderBy('sort_order')
            ->get();

        return view('restaurants.menu', compact('restaurant', 'menus'));
    }

    /**
     * Activate restaurant (admin only)
     */
    public function activate(Restaurant $restaurant)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $restaurant->update(['is_active' => true]);

        return back()->with('success', 'Restauracja została aktywowana.');
    }
}