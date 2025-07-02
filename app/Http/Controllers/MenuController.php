<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Menu::with('restaurant');
        
        // Filtrowanie w zależności od roli
        if ($user && ($user->role === 'manager' || $user->role === 'staff')) {
            $query->whereHas('restaurant', function($q) use ($user) {
                $q->where('id', $user->restaurant_id);
            });
        }
        
        // Wyszukiwanie po nazwie menu lub restauracji
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhereHas('restaurant', function($restaurantQuery) use ($request) {
                      $restaurantQuery->where('name', 'like', "%{$request->search}%");
                  });
            });
        }
        
        // Filtrowanie po statusie
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        // Filtrowanie po restauracji (dla adminów)
        if ($request->restaurant_id && $user && $user->role === 'admin') {
            $query->where('restaurant_id', $request->restaurant_id);
        }
        
        // Sortowanie
        $sortBy = $request->sort ?? 'sort_order';
        $sortDirection = $request->direction ?? 'asc';
        $allowedSorts = ['name', 'sort_order', 'created_at'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        $menus = $query->withCount(['categories', 'categories as dishes_count' => function($q) {
            $q->withCount('dishes');
        }])->paginate(15);
        
        // Lista restauracji dla filtra (tylko dla adminów)
        $restaurants = $user && $user->role === 'admin' 
            ? Restaurant::active()->orderBy('name')->get() 
            : collect();
        
        return view('menus.index', compact('menus', 'restaurants'));
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Brak uprawnień do dodawania menu.');
        }
        
        // Lista restauracji w zależności od roli
        if ($user->role === 'admin') {
            $restaurants = Restaurant::active()->orderBy('name')->get();
        } else {
            $restaurants = collect([$user->restaurant]);
        }
        
        // Domyślna restauracja z query param
        $selectedRestaurant = $request->restaurant_id 
            ? Restaurant::find($request->restaurant_id)
            : ($user->role === 'manager' ? $user->restaurant : null);
        
        return view('menus.create', compact('restaurants', 'selectedRestaurant'));
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Brak uprawnień do dodawania menu.');
        }

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer|min:0|max:99',
            'is_active' => 'boolean',
        ], [
            'restaurant_id.required' => 'Wybierz restaurację.',
            'restaurant_id.exists' => 'Wybrana restauracja nie istnieje.',
            'name.required' => 'Nazwa menu jest wymagana.',
            'name.max' => 'Nazwa menu nie może być dłuższa niż 255 znaków.',
            'sort_order.required' => 'Kolejność sortowania jest wymagana.',
            'sort_order.min' => 'Kolejność sortowania musi być liczbą od 0 do 99.',
            'sort_order.max' => 'Kolejność sortowania musi być liczbą od 0 do 99.',
        ]);

        // Sprawdź uprawnienia do restauracji
        if ($user->role === 'manager' && $validated['restaurant_id'] != $user->restaurant_id) {
            abort(403, 'Brak uprawnień do tej restauracji.');
        }
        
        // Sprawdź czy kolejność sortowania nie jest zajęta
        $existingMenu = Menu::where('restaurant_id', $validated['restaurant_id'])
            ->where('sort_order', $validated['sort_order'])
            ->exists();
            
        if ($existingMenu) {
            return back()->withErrors([
                'sort_order' => 'Ta kolejność sortowania jest już zajęta w tej restauracji.'
            ])->withInput();
        }

        $menu = Menu::create($validated);

        return redirect()->route('menus.show', $menu)
            ->with('success', 'Menu zostało utworzone pomyślnie.');
    }

    /**
     * Display the specified menu.
     */
    public function show(Menu $menu)
    {
        $menu->load([
            'restaurant',
            'categories' => function($query) {
                $query->orderBy('sort_order')->withCount('dishes');
            },
            'categories.dishes' => function($query) {
                $query->orderBy('sort_order');
            }
        ]);
        
        $stats = [
            'total_categories' => $menu->categories()->count(),
            'active_categories' => $menu->categories()->where('is_active', true)->count(),
            'total_dishes' => $menu->categories()->withCount('dishes')->get()->sum('dishes_count'),
            'available_dishes' => $menu->categories()
                ->with(['dishes' => function($q) { $q->where('is_available', true); }])
                ->get()
                ->sum(function($category) { return $category->dishes->count(); }),
        ];

        return view('menus.show', compact('menu', 'stats'));
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Menu $menu)
    {
        $user = Auth::user();
        
        // Sprawdź uprawnienia
        if (!$user || (!$user->isAdmin() && !($user->isManager() && $user->restaurant_id === $menu->restaurant_id))) {
            abort(403, 'Brak uprawnień do edycji tego menu.');
        }

        // Lista restauracji w zależności od roli
        if ($user->role === 'admin') {
            $restaurants = Restaurant::active()->orderBy('name')->get();
        } else {
            $restaurants = collect([$user->restaurant]);
        }

        return view('menus.edit', compact('menu', 'restaurants'));
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $user = Auth::user();
        
        // Sprawdź uprawnienia
        if (!$user || (!$user->isAdmin() && !($user->isManager() && $user->restaurant_id === $menu->restaurant_id))) {
            abort(403, 'Brak uprawnień do edycji tego menu.');
        }

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer|min:0|max:99',
            'is_active' => 'boolean',
        ]);

        // Sprawdź uprawnienia do restauracji
        if ($user->role === 'manager' && $validated['restaurant_id'] != $user->restaurant_id) {
            abort(403, 'Brak uprawnień do tej restauracji.');
        }
        
        // Sprawdź czy kolejność sortowania nie jest zajęta (pomijając obecne menu)
        $existingMenu = Menu::where('restaurant_id', $validated['restaurant_id'])
            ->where('sort_order', $validated['sort_order'])
            ->where('id', '!=', $menu->id)
            ->exists();
            
        if ($existingMenu) {
            return back()->withErrors([
                'sort_order' => 'Ta kolejność sortowania jest już zajęta w tej restauracji.'
            ])->withInput();
        }

        $menu->update($validated);

        return redirect()->route('menus.show', $menu)
            ->with('success', 'Menu zostało zaktualizowane.');
    }

    /**
     * Remove the specified menu from storage (dezaktywacja).
     */
    public function destroy(Menu $menu)
{
    $user = Auth::user();
    
    // Sprawdź uprawnienia
    if (!$user || (!$user->isAdmin() && !($user->isManager() && $user->restaurant_id === $menu->restaurant_id))) {
        abort(403, 'Brak uprawnień do usunięcia tego menu.');
    }

    // "Miękkie" usunięcie - dezaktywacja (bez sprawdzania relacji)
    $menu->update(['is_active' => false]);

    return redirect()->route('menus.index')
        ->with('success', 'Menu zostało dezaktywowane.');
}


    /**
     * Activate menu
     */
    public function activate(Menu $menu)
    {
        $user = Auth::user();
        
        if (!$user || (!$user->isAdmin() && !($user->isManager() && $user->restaurant_id === $menu->restaurant_id))) {
            abort(403);
        }

        $menu->update(['is_active' => true]);

        return back()->with('success', 'Menu zostało aktywowane.');
    }
}