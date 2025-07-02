<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $menu->name }} - Szczegóły menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Nagłówek -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold text-gray-800">{{ $menu->name }}</h1>
                        <span class="px-3 py-1 text-sm rounded-full 
                            {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $menu->is_active ? 'Aktywne' : 'Nieaktywne' }}
                        </span>
                        <span class="bg-gray-100 text-gray-800 text-sm px-2 py-1 rounded-full">
                            Kolejność: {{ $menu->sort_order }}
                        </span>
                    </div>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-building mr-2"></i>
                        <span class="font-medium">{{ $menu->restaurant->name }}</span>
                        <span class="mx-2">•</span>
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span>{{ $menu->restaurant->address }}</span>
                    </div>
                    @if($menu->description)
                        <p class="text-gray-700 leading-relaxed">{{ $menu->description }}</p>
                    @endif
                </div>
                <div class="flex items-center space-x-2 ml-6">
                    @if(auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'manager' && auth()->user()->restaurant_id === $menu->restaurant_id)))
                        <a href="{{ route('menus.edit', $menu) }}" 
                           class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            Edytuj
                        </a>
                    @endif
                    <a href="{{ route('menus.index') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        ← Powrót do listy
                    </a>
                </div>
            </div>
        </div>

        <!-- Komunikaty -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <i class="fas fa-tags text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_categories'] }}</p>
                        <p class="text-gray-600">{{ $stats['total_categories'] === 1 ? 'Kategoria' : 'Kategorii' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['active_categories'] }}</p>
                        <p class="text-gray-600">Aktywnych kategorii</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                        <i class="fas fa-utensils text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_dishes'] }}</p>
                        <p class="text-gray-600">{{ $stats['total_dishes'] === 1 ? 'Danie' : 'Dań' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-emerald-100 text-emerald-500 mr-4">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['available_dishes'] }}</p>
                        <p class="text-gray-600">Dostępnych dań</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategorie i dania -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Kategorie i dania</h2>
                    @if(auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'manager' && auth()->user()->restaurant_id === $menu->restaurant_id)))
                        <div class="space-x-2">
                            <a href="#" 
                               class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors inline-flex items-center gap-2 text-sm">
                                <i class="fas fa-plus"></i>
                                Dodaj kategorię
                            </a>
                            <a href="#" 
                               class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors inline-flex items-center gap-2 text-sm">
                                <i class="fas fa-utensils"></i>
                                Dodaj danie
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($menu->categories->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($menu->categories as $category)
                        <div class="p-6">
                            <!-- Nagłówek kategorii -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $category->name }}</h3>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $category->is_active ? 'Aktywna' : 'Nieaktywna' }}
                                        </span>
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                            {{ $category->dishes_count }} {{ $category->dishes_count === 1 ? 'danie' : 'dań' }}
                                        </span>
                                    </div>
                                    @if($category->description)
                                        <p class="text-gray-600 text-sm">{{ $category->description }}</p>
                                    @endif
                                </div>
                                @if(auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'manager' && auth()->user()->restaurant_id === $menu->restaurant_id)))
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button class="text-blue-600 hover:text-blue-900 text-sm" title="Edytuj kategorię">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 text-sm" title="Usuń kategorię">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Dania w kategorii -->
                            @if($category->dishes->count() > 0)
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    @foreach($category->dishes as $dish)
                                        <div class="border border-gray-200 rounded-lg p-4 {{ !$dish->is_available ? 'opacity-60 bg-gray-50' : 'bg-white' }}">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <h4 class="font-medium text-gray-800">{{ $dish->name }}</h4>
                                                        @if($dish->is_vegetarian)
                                                            <span class="text-green-600 text-xs" title="Wegetariańskie">
                                                                <i class="fas fa-leaf"></i>
                                                            </span>
                                                        @endif
                                                        @if($dish->is_vegan)
                                                            <span class="text-green-700 text-xs" title="Wegańskie">
                                                                <i class="fas fa-seedling"></i>
                                                            </span>
                                                        @endif
                                                        @if(!$dish->is_available)
                                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full">
                                                                Niedostępne
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($dish->description)
                                                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($dish->description, 100) }}</p>
                                                    @endif
                                                    @if($dish->allergens && count($dish->allergens) > 0)
                                                        <div class="flex flex-wrap gap-1 mb-2">
                                                            @foreach($dish->allergens as $allergen)
                                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full">
                                                                    {{ ucfirst($allergen) }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-right ml-4">
                                                    <div class="text-lg font-bold text-blue-600">{{ number_format($dish->price, 2) }} zł</div>
                                                    @if($dish->rating > 0)
                                                        <div class="flex items-center justify-end mt-1">
                                                            <span class="text-yellow-500 text-sm mr-1">★</span>
                                                            <span class="text-gray-600 text-sm">{{ number_format($dish->rating, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'manager' && auth()->user()->restaurant_id === $menu->restaurant_id)))
                                                <div class="flex justify-end space-x-2 pt-2 border-t border-gray-100">
                                                    <button class="text-blue-600 hover:text-blue-900 text-sm" title="Edytuj danie">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="text-red-600 hover:text-red-900 text-sm" title="Usuń danie">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <i class="fas fa-utensils text-gray-400 text-3xl mb-3"></i>
                                    <p class="text-gray-600">Brak dań w tej kategorii</p>
                                    @if(auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'manager' && auth()->user()->restaurant_id === $menu->restaurant_id)))
                                        <button class="mt-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                            Dodaj pierwsze danie
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-list-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Brak kategorii</h3>
                    <p class="text-gray-600 mb-6">To menu nie ma jeszcze żadnych kategorii ani dań.</p>
                    @if(auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'manager' && auth()->user()->restaurant_id === $menu->restaurant_id)))
                        <div class="space-x-3">
                            <a href="#" 
                               class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors inline-flex items-center gap-2">
                                <i class="fas fa-plus"></i>
                                Dodaj pierwszą kategorię
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Informacje o menu -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informacje o menu</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Utworzono</label>
                    <p class="text-gray-900">{{ $menu->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Ostatnia aktualizacja</label>
                    <p class="text-gray-900">{{ $menu->updated_at->format('d.m.Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p class="text-gray-900">
                        @if($menu->is_active)
                            <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Aktywne</span>
                        @else
                            <span class="text-red-600"><i class="fas fa-pause-circle mr-1"></i>Nieaktywne</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Kolejność wyświetlania</label>
                    <p class="text-gray-900">{{ $menu->sort_order }}</p>
                </div>
            </div>
        </div>

        <!-- Akcje -->
        @if(auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'manager' && auth()->user()->restaurant_id === $menu->restaurant_id)))
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Dostępne akcje</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('menus.edit', $menu) }}" 
                       class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Edytuj menu
                    </a>
                    
                    @if($menu->is_active)
                        <form method="POST" action="{{ route('menus.destroy', $menu) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors inline-flex items-center gap-2"
                                    onclick="return confirm('Czy na pewno chcesz dezaktywować to menu?')">
                                <i class="fas fa-times-circle"></i>
                                Dezaktywuj menu
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('menus.activate', $menu) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors inline-flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Aktywuj menu
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</body>
</html>