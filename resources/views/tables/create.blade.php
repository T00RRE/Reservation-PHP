<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dodaj nowy stolik') }}
            </h2>
            <a href="{{ route('tables.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Powrót do listy
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Błędy walidacji -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Wystąpiły błędy:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tables.store') }}" class="space-y-6">
                        @csrf

                        <!-- Restauracja -->
                        <div>
                            <label for="restaurant_id" class="block text-sm font-medium text-gray-700">
                                Restauracja <span class="text-red-500">*</span>
                            </label>
                            @if($restaurants->count() > 1)
                                <select id="restaurant_id" 
                                        name="restaurant_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                    <option value="">Wybierz restaurację</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}" 
                                                {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                            {{ $restaurant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="restaurant_id" value="{{ $restaurants->first()->id }}">
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-gray-50 rounded-md text-gray-600">
                                    {{ $restaurants->first()->name }}
                                </div>
                            @endif
                            @error('restaurant_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Numer stolika -->
                        <div>
                            <label for="table_number" class="block text-sm font-medium text-gray-700">
                                Numer stolika <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="table_number" 
                                   name="table_number" 
                                   value="{{ old('table_number') }}"
                                   placeholder="np. S1, S2, VIP1"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">
                                Unikalny numer stolika w restauracji (max 10 znaków)
                            </p>
                            @error('table_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pojemność -->
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700">
                                Pojemność (liczba osób) <span class="text-red-500">*</span>
                            </label>
                            <select id="capacity" 
                                    name="capacity" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <option value="">Wybierz pojemność</option>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ old('capacity') == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i === 1 ? 'osoba' : ($i <= 4 ? 'osoby' : 'osób') }}
                                    </option>
                                @endfor
                            </select>
                            @error('capacity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>
                                    Dostępny
                                </option>
                                <option value="occupied" {{ old('status') === 'occupied' ? 'selected' : '' }}>
                                    Zajęty
                                </option>
                                <option value="reserved" {{ old('status') === 'reserved' ? 'selected' : '' }}>
                                    Zarezerwowany
                                </option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>
                                    Serwis
                                </option>
                            </select>
                            <div class="mt-1 text-sm text-gray-500">
                                <div class="grid grid-cols-2 gap-2">
                                    <div><span class="text-green-600">●</span> Dostępny - można rezerwować</div>
                                    <div><span class="text-red-600">●</span> Zajęty - obecnie używany</div>
                                    <div><span class="text-yellow-600">●</span> Zarezerwowany - ma rezerwację</div>
                                    <div><span class="text-gray-600">●</span> Serwis - niedostępny</div>
                                </div>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Opis -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Opis (opcjonalnie)
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="np. Stolik przy oknie, Stolik VIP z widokiem na ogród, Stolik dla rodzin z dziećmi"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Dodatkowe informacje o stoliku (max 255 znaków)
                            </p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Przyciski -->
                        <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                            <a href="{{ route('tables.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Anuluj
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Dodaj stolik
                            </button>
                        </div>
                    </form>

                    <!-- Wskazówki -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">💡 Wskazówki:</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Numer stolika musi być unikalny w ramach restauracji</li>
                            <li>• Używaj czytelnych numerów jak S1, S2, VIP1, BAR1</li>
                            <li>• Pojemność to maksymalna liczba osób, które mogą usiąść</li>
                            <li>• Nowe stoliki domyślnie są dostępne do rezerwacji</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>