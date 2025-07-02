<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stolik {{ $table->table_number }} - {{ $table->restaurant->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('tables.edit', $table) }}" 
                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Edytuj
                </a>
                <a href="{{ route('tables.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Powrót do listy
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Podstawowe informacje -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informacje o stoliku</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Numer stolika</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $table->table_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Restauracja</label>
                            <p class="mt-1 text-lg text-gray-900">
                                <a href="{{ route('restaurants.show', $table->restaurant) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $table->restaurant->name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Pojemność</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $table->capacity }} {{ $table->capacity === 1 ? 'osoba' : ($table->capacity <= 4 ? 'osoby' : 'osób') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 text-sm rounded-full
                                    @if($table->status === 'available') bg-green-100 text-green-800
                                    @elseif($table->status === 'occupied') bg-red-100 text-red-800
                                    @elseif($table->status === 'reserved') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    @switch($table->status)
                                        @case('available') Dostępny @break
                                        @case('occupied') Zajęty @break
                                        @case('reserved') Zarezerwowany @break
                                        @case('maintenance') Serwis @break
                                        @default {{ ucfirst($table->status) }}
                                    @endswitch
                                </span>
                            </p>
                        </div>

                        @if($table->description)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Opis</label>
                            <p class="mt-1 text-gray-900">{{ $table->description }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Utworzono</label>
                            <p class="mt-1 text-gray-900">{{ $table->created_at->format('d.m.Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Ostatnia aktualizacja</label>
                            <p class="mt-1 text-gray-900">{{ $table->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statystyki rezerwacji -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statystyki rezerwacji</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $table->reservations()->count() }}
                            </div>
                            <div class="text-sm text-blue-800">Wszystkich rezerwacji</div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $table->reservations()->where('status', 'confirmed')->count() }}
                            </div>
                            <div class="text-sm text-green-800">Potwierdzonych</div>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ $table->reservations()->where('status', 'pending')->count() }}
                            </div>
                            <div class="text-sm text-yellow-800">Oczekujących</div>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ $table->reservations()->where('reservation_date', '>=', now()->toDateString())->count() }}
                            </div>
                            <div class="text-sm text-purple-800">Nadchodzących</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ostatnie rezerwacje -->
            @if($table->reservations->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Ostatnie rezerwacje</h3>
                        <a href="{{ route('reservations.index', ['table_id' => $table->id]) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            Zobacz wszystkie →
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Data</th>
                                    <th class="py-2 px-4 border-b text-left">Godzina</th>
                                    <th class="py-2 px-4 border-b text-left">Klient</th>
                                    <th class="py-2 px-4 border-b text-left">Osoby</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-center">Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($table->reservations as $reservation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">
                                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d.m.Y') }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $reservation->user->name }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $reservation->guests_count }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($reservation->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                @switch($reservation->status)
                                                    @case('confirmed') Potwierdzona @break
                                                    @case('pending') Oczekująca @break
                                                    @case('cancelled') Anulowana @break
                                                    @case('completed') Zakończona @break
                                                    @default {{ ucfirst($reservation->status) }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <a href="{{ route('reservations.show', $reservation) }}" 
                                               class="text-blue-600 hover:text-blue-900 text-sm">
                                                Zobacz
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="text-gray-400 mb-2">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4M6 7h12l-1 9H7L6 7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Brak rezerwacji</h3>
                    <p class="text-gray-600 mb-4">Ten stolik nie ma jeszcze żadnych rezerwacji.</p>
                    <a href="{{ route('reservations.create', ['restaurant_id' => $table->restaurant_id]) }}" 
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Dodaj pierwszą rezerwację
                    </a>
                </div>
            </div>
            @endif

            <!-- Akcje -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dostępne akcje</h3>
                    
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('tables.edit', $table) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            ✏️ Edytuj stolik
                        </a>

                        <a href="{{ route('reservations.create', ['restaurant_id' => $table->restaurant_id]) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            📅 Nowa rezerwacja
                        </a>

                        @if($table->status !== 'maintenance')
                            <form method="POST" 
                                  action="{{ route('tables.destroy', $table) }}" 
                                  class="inline"
                                  onsubmit="return confirm('Czy na pewno chcesz dezaktywować ten stolik? Stolik zostanie przeniesiony do serwisu.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    🔧 Dezaktywuj stolik
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('tables.update', $table) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="restaurant_id" value="{{ $table->restaurant_id }}">
                                <input type="hidden" name="table_number" value="{{ $table->table_number }}">
                                <input type="hidden" name="capacity" value="{{ $table->capacity }}">
                                <input type="hidden" name="status" value="available">
                                <input type="hidden" name="description" value="{{ $table->description }}">
                                <button type="submit" 
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    ✅ Aktywuj stolik
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>