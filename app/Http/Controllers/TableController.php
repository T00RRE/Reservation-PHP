<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    /**
     * Display a listing of tables.
     */
    public function index(Request $request)
{
    $user = Auth::user();
    
    // Query builder dla stolików
    $query = Table::with('restaurant');
    
    // Filtrowanie w zależności od roli
    if ($user && ($user->role === 'manager' || $user->role === 'staff')) {
        $query->where('restaurant_id', $user->restaurant_id);
    }
    
    // Wyszukiwanie po numerze stolika
    if ($request->search) {
        $query->where('table_number', 'like', "%{$request->search}%");
    }
    
    // Filtrowanie po pojemności
    if ($request->capacity) {
        $query->where('capacity', '>=', $request->capacity);
    }
    
    // Filtrowanie po statusie
    if ($request->status) {
        $query->where('status', $request->status);
    }
    
    // Filtrowanie po restauracji (dla adminów)
    if ($request->restaurant_id && $user->role === 'admin') {
        $query->where('restaurant_id', $request->restaurant_id);
    }
    
    // Sortowanie
    $sortBy = $request->sort ?? 'table_number';
    $sortDirection = $request->direction ?? 'asc';
    $query->orderBy($sortBy, $sortDirection);
    
    $tables = $query->paginate(15);
    
    // Lista restauracji dla filtra (tylko dla adminów)
    $restaurants = $user && $user->role === 'admin' 
        ? Restaurant::orderBy('name')->get() 
        : collect();
    
    return view('tables.index', compact('tables', 'restaurants'));
}

    /**
     * Show the form for creating a new table.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Lista restauracji w zależności od roli
        if ($user->role === 'admin') {
            $restaurants = Restaurant::active()->orderBy('name')->get();
        } else {
            $restaurants = collect([$user->restaurant]);
        }
        
        return view('tables.create', compact('restaurants'));
    }

    /**
     * Store a newly created table in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_number' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'description' => 'nullable|string|max:255',
        ]);
        
        // Sprawdź czy użytkownik może dodawać stoliki do tej restauracji
        if ($user->role !== 'admin' && $validated['restaurant_id'] != $user->restaurant_id) {
            abort(403, 'Brak uprawnień do tej restauracji.');
        }
        
        // Sprawdź unikalność numeru stolika w restauracji
        $exists = Table::where('restaurant_id', $validated['restaurant_id'])
            ->where('table_number', $validated['table_number'])
            ->exists();
            
        if ($exists) {
            return back()->withErrors([
                'table_number' => 'Stolik o tym numerze już istnieje w tej restauracji.'
            ])->withInput();
        }
        
        Table::create($validated);
        
        return redirect()->route('tables.index')
            ->with('success', 'Stolik został dodany pomyślnie.');
    }

    /**
     * Display the specified table.
     */
    public function show(Table $table)
    {
        $table->load(['restaurant', 'reservations' => function($query) {
            $query->with('user')->latest()->take(10);
        }]);
        
        return view('tables.show', compact('table'));
    }

    /**
     * Show the form for editing the specified table.
     */
    public function edit(Table $table)
    {
        $user = Auth::user();
        
        // Sprawdź uprawnienia
        if ($user->role !== 'admin' && $table->restaurant_id != $user->restaurant_id) {
            abort(403, 'Brak uprawnień do edycji tego stolika.');
        }
        
        // Lista restauracji w zależności od roli
        if ($user->role === 'admin') {
            $restaurants = Restaurant::active()->orderBy('name')->get();
        } else {
            $restaurants = collect([$user->restaurant]);
        }
        
        return view('tables.edit', compact('table', 'restaurants'));
    }

    /**
     * Update the specified table in storage.
     */
    public function update(Request $request, Table $table)
    {
        $user = Auth::user();
        
        // Sprawdź uprawnienia
        if ($user->role !== 'admin' && $table->restaurant_id != $user->restaurant_id) {
            abort(403, 'Brak uprawnień do edycji tego stolika.');
        }
        
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_number' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'description' => 'nullable|string|max:255',
        ]);
        
        // Sprawdź czy użytkownik może przenosić stolik do innej restauracji
        if ($user->role !== 'admin' && $validated['restaurant_id'] != $user->restaurant_id) {
            abort(403, 'Brak uprawnień do tej restauracji.');
        }
        
        // Sprawdź unikalność numeru stolika w restauracji (pomijając aktualny stolik)
        $exists = Table::where('restaurant_id', $validated['restaurant_id'])
            ->where('table_number', $validated['table_number'])
            ->where('id', '!=', $table->id)
            ->exists();
            
        if ($exists) {
            return back()->withErrors([
                'table_number' => 'Stolik o tym numerze już istnieje w tej restauracji.'
            ])->withInput();
        }
        
        $table->update($validated);
        
        return redirect()->route('tables.index')
            ->with('success', 'Stolik został zaktualizowany.');
    }

    /**
     * Remove the specified table from storage (dezaktywacja).
     */
    public function destroy(Table $table)
    {
        $user = Auth::user();
        
        // Sprawdź uprawnienia
        if ($user->role !== 'admin' && $table->restaurant_id != $user->restaurant_id) {
            abort(403, 'Brak uprawnień do usunięcia tego stolika.');
        }
        
        // Sprawdź czy stolik ma aktywne rezerwacje
        $hasActiveReservations = $table->reservations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('reservation_date', '>=', now()->toDateString())
            ->exists();
            
        if ($hasActiveReservations) {
            return back()->with('error', 'Nie można usunąć stolika z aktywnymi rezerwacjami.');
        }
        
        // "Miękkie" usunięcie - zmiana statusu na maintenance
        $table->update(['status' => 'maintenance']);
        
        return redirect()->route('tables.index')
            ->with('success', 'Stolik został dezaktywowany.');
    }
}