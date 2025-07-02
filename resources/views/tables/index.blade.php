<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Zarządzanie stolikami') }}
            </h2>
            <a href="{{ route('tables.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Dodaj stolik
            </a>
        </div>
    </x-slot>

    <div class="py-12">
    <!-- TEMPORARY DEBUG - usuń to później -->
    <div style="background: yellow; padding: 10px; margin: 10px;">
        <strong>DEBUG INFO:</strong><br>
        User: {{ auth()->user()->name ?? 'not logged' }} ({{ auth()->user()->role ?? 'no role' }})<br>
        Restaurant ID: {{ auth()->user()->restaurant_id ?? 'null' }}<br>
        Tables count: {{ $tables->count() }}<br>
        Total tables: {{ $tables->total() }}<br>
        Query params: {{ json_encode(request()->all()) }}<br>
        @if($tables->count() > 0)
            First table: {{ $tables->first()->table_number }} (Restaurant: {{ $tables->first()->restaurant->name }})
        @endif
    </div>
    <!-- END DEBUG -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Komunikaty -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Filtry wyszukiwania -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form method="GET" action="{{ route('tables.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            
                            <!-- Wyszukiwanie po numerze -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Numer stolika</label>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="S1, S2..."
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <!-- Filtr pojemności -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Min. pojemność</label>
                                <select name="capacity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Wszystkie</option>
                                    <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2+</option>
                                    <option value="4" {{ request('capacity') == '4' ? 'selected' : '' }}>4+</option>
                                    <option value="6" {{ request('capacity') == '6' ? 'selected' : '' }}>6+</option>
                                    <option value="8" {{ request('capacity') == '8' ? 'selected' : '' }}>8+</option>
                                </select>
                            </div>

                            <!-- Filtr statusu -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Wszystkie</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Dostępny</option>
                                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Zajęty</option>
                                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Zarezerwowany</option>
                                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Serwis</option>
                                </select>
                            </div>

                            <!-- Filtr restauracji (tylko dla adminów) -->
                            @if(auth()->user() && auth()->user()->role === 'admin')
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Restauracja</label>
                                <select name="restaurant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Wszystkie</option>
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
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Filtruj
                                </button>
                                <a href="{{ route('tables.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Wyczyść
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Sortowanie -->
                    <div class="mb-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Znaleziono {{ $tables->total() }} stolików
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Sortuj:</span>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'table_number', 'direction' => 'asc']) }}" 
                               class="text-blue-600 hover:text-blue-800">Numer ↑</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'capacity', 'direction' => 'desc']) }}" 
                               class="text-blue-600 hover:text-blue-800">Pojemność ↓</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => 'asc']) }}" 
                               class="text-blue-600 hover:text-blue-800">Status</a>
                        </div>
                    </div>

                    <!-- Tabela stolików -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Numer</th>
                                    @if(auth()->user() && auth()->user()->role === 'admin')
                                        <th class="py-2 px-4 border-b text-left">Restauracja</th>
                                    @endif
                                    <th class="py-2 px-4 border-b text-left">Pojemność</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-left">Opis</th>
                                    <th class="py-2 px-4 border-b text-center">Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tables as $table)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b font-medium">
                                            {{ $table->table_number }}
                                        </td>
                                        @if(auth()->user() && auth()->user()->role === 'admin')
                                            <td class="py-2 px-4 border-b">
                                                {{ $table->restaurant->name }}
                                            </td>
                                        @endif
                                        <td class="py-2 px-4 border-b">
                                            {{ $table->capacity }} osób
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($table->status === 'available') bg-green-100 text-green-800
                                                @elseif($table->status === 'occupied') bg-red-100 text-red-800
                                                @elseif($table->status === 'reserved') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($table->status) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-sm text-gray-600">
                                            {{ Str::limit($table->description, 50) }}
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('tables.show', $table) }}" 
                                                   class="text-blue-600 hover:text-blue-900">Zobacz</a>
                                                <a href="{{ route('tables.edit', $table) }}" 
                                                   class="text-green-600 hover:text-green-900">Edytuj</a>
                                                @if($table->status !== 'maintenance')
                                                    <form method="POST" 
                                                          action="{{ route('tables.destroy', $table) }}" 
                                                          class="inline"
                                                          onsubmit="return confirm('Czy na pewno chcesz dezaktywować ten stolik?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900">
                                                            Dezaktywuj
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                            Brak stolików spełniających kryteria wyszukiwania.
                                            <a href="{{ route('tables.create') }}" class="text-blue-600 hover:text-blue-800">
                                                Dodaj pierwszy stolik
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginacja -->
                    <div class="mt-6">
                        {{ $tables->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>