<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Nagłówek -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Zarządzanie Menu</h1>
                    <p class="text-gray-600 mt-1">Wszystkie menu w systemie</p>
                </div>
                <div class="space-x-2">
                    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'manager']))
                        <a href="{{ route('menus.create') }}" 
                           class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Dodaj menu
                        </a>
                    @endif
                    <a href="{{ route('restaurants.index') }}" 
                       class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        ← Restauracje
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

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Filtry -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('menus.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <!-- Wyszukiwanie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Szukaj</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Nazwa menu, restauracji..."
                               class="pl-10 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="is_active" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Wszystkie</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktywne</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nieaktywne</option>
                    </select>
                </div>

                <!-- Restauracja (tylko dla adminów) -->
                @if(auth()->check() && auth()->user()->role === 'admin')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Restauracja</label>
                    <select name="restaurant_id" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Wszystkie restauracje</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" 
                                    {{ request('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                {{ $restaurant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Przyciski -->
                <div class="flex items-end space-x-2">
                    <button type="submit" 
                            class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                        <i class="fas fa-search"></i>
                        Filtruj
                    </button>
                    <a href="{{ route('menus.index') }}" 
                       class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                        Wyczyść
                    </a>
                </div>
            </form>
        </div>

        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $menus->total() }}</p>
                        <p class="text-gray-600">Wszystkich menu</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $menus->where('is_active', true)->count() }}</p>
                        <p class="text-gray-600">Aktywnych</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-500 mr-4">
                        <i class="fas fa-tags text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $menus->sum('categories_count') }}</p>
                        <p class="text-gray-600">Kategorii</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                        <i class="fas fa-utensils text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $menus->sum('dishes_count') }}</p>
                        <p class="text-gray-600">Dań</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista menu -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($menus->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="flex items-center gap-1 hover:text-gray-700">
                                        Menu
                                        <i class="fas fa-sort text-gray-400"></i>
                                    </a>
                                </th>
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Restauracja
                                </th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategorie
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dania
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'sort_order', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="flex items-center gap-1 hover:text-gray-700">
                                        Kolejność
                                        <i class="fas fa-sort text-gray-400"></i>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Akcje
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($menus as $menu)
                                <tr class="hover:bg-gray-50 {{ !$menu->is_active ? 'opacity-60' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $menu->name }}</div>
                                            @if($menu->description)
                                                <div class="text-sm text-gray-500">{{ Str::limit($menu->description, 60) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $menu->restaurant->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($menu->restaurant->address, 40) }}</div>
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                {{ $menu->categories_count }} {{ $menu->categories_count === 1 ? 'kategoria' : 'kategorii' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                {{ $menu->dishes_count }} {{ $menu->dishes_count === 1 ? 'danie' : 'dań' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            {{ $menu->sort_order }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $menu->is_active ? 'Aktywne' : 'Nieaktywne' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('menus.show', $menu) }}" 
                                               class="text-blue-600 hover:text-blue-900" title="Zobacz szczegóły">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'manager' && auth()->user()->restaurant_id === $menu->restaurant_id)))
                                                <a href="{{ route('menus.edit', $menu) }}" 
                                                   class="text-yellow-600 hover:text-yellow-900" title="Edytuj">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($menu->is_active)
                                                    <form method="POST" action="{{ route('menus.destroy', $menu) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900" 
                                                                title="Dezaktywuj"
                                                                onclick="return confirm('Czy na pewno chcesz dezaktywować to menu?')">
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('menus.activate', $menu) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="text-green-600 hover:text-green-900" 
                                                                title="Aktywuj">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginacja -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $menus->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-list-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Brak menu</h3>
                    <p class="text-gray-600 mb-6">
                        @if(request()->hasAny(['search', 'is_active', 'restaurant_id']))
                            Nie znaleziono menu spełniających kryteria wyszukiwania.
                        @else
                            Nie ma jeszcze żadnych menu w systemie.
                        @endif
                    </p>
                    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'manager']))
                        <a href="{{ route('menus.create') }}" 
                           class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Dodaj pierwsze menu
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>