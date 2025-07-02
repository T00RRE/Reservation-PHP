<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj menu: {{ $menu->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Nagłówek -->
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edytuj menu</h1>
                        <p class="text-gray-600 mt-1">{{ $menu->name }} - {{ $menu->restaurant->name }}</p>
                    </div>
                    <div class="space-x-2">
                        <a href="{{ route('menus.show', $menu) }}" 
                           class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            Zobacz szczegóły
                        </a>
                        <a href="{{ route('menus.index') }}" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            ← Powrót do listy
                        </a>
                    </div>
                </div>
            </div>

            <!-- Błędy walidacji -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <strong>Wystąpiły błędy:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Obecne informacje -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="font-medium text-gray-900 mb-2">ℹ️ Obecne informacje:</h3>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong>Restauracja:</strong> {{ $menu->restaurant->name }}</p>
                    <p><strong>Status:</strong> 
                        <span class="px-2 py-1 text-xs rounded-full {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $menu->is_active ? 'Aktywne' : 'Nieaktywne' }}
                        </span>
                    </p>
                    <p><strong>Kategorii:</strong> {{ $menu->categories()->count() }}</p>
                    <p><strong>Utworzono:</strong> {{ $menu->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>Ostatnia aktualizacja:</strong> {{ $menu->updated_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>

            <!-- Formularz -->
            <div class="bg-white p-6 rounded-lg shadow">
                <form method="POST" action="{{ route('menus.update', $menu) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Restauracja -->
                    <div>
                        <label for="restaurant_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Restauracja <span class="text-red-500">*</span>
                        </label>
                        @if($restaurants->count() > 1)
                            <select id="restaurant_id" 
                                    name="restaurant_id" 
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="">Wybierz restaurację</option>
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}" 
                                            {{ old('restaurant_id', $menu->restaurant_id) == $restaurant->id ? 'selected' : '' }}>
                                        {{ $restaurant->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="restaurant_id" value="{{ $restaurants->first()->id }}">
                            <div class="w-full p-3 border border-gray-300 bg-gray-50 rounded-lg text-gray-600">
                                <i class="fas fa-building mr-2"></i>{{ $restaurants->first()->name }}
                            </div>
                        @endif
                        <p class="text-sm text-gray-500 mt-1">Wybierz restaurację dla której edytujesz menu</p>
                        @error('restaurant_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nazwa menu -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nazwa menu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $menu->name) }}"
                               placeholder="np. Menu Główne, Karta Win, Menu Sezonowe"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        <p class="text-sm text-gray-500 mt-1">Podaj nazwę menu (maksymalnie 255 znaków)</p>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Opis -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Opis menu
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Opisz menu - jakie dania zawiera, dla kogo jest przeznaczone, w jakich godzinach dostępne..."
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $menu->description) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Opcjonalny opis menu (maksymalnie 1000 znaków)</p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kolejność sortowania -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Kolejność wyświetlania <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-5 gap-2">
                            @for($i = 1; $i <= 10; $i++)
                                <label class="flex items-center justify-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" 
                                           name="sort_order" 
                                           value="{{ $i }}" 
                                           {{ old('sort_order', $menu->sort_order) == $i ? 'checked' : '' }}
                                           class="sr-only">
                                    <span class="font-medium text-gray-700">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Wybierz kolejność w jakiej menu będzie wyświetlane (1 = pierwsze). Każda restauracja może mieć tylko jedno menu z daną kolejnością.
                        </p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status aktywności -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status menu</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $menu->is_active) == '1' ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        <span class="font-medium text-gray-900">Aktywne</span>
                                    </div>
                                    <p class="text-sm text-gray-500">Menu będzie widoczne dla klientów i dostępne do zamawiania</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" 
                                       name="is_active" 
                                       value="0"
                                       {{ old('is_active', $menu->is_active) == '0' ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-pause-circle text-yellow-500 mr-2"></i>
                                        <span class="font-medium text-gray-900">Nieaktywne</span>
                                    </div>
                                    <p class="text-sm text-gray-500">Menu nie będzie widoczne dla klientów (tryb roboczy)</p>
                                </div>
                            </label>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Przyciski -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('menus.show', $menu) }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                            Anuluj
                        </a>
                        <button type="submit" 
                                class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Zapisz zmiany
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ostrzeżenia o zmianach -->
            @if($menu->categories()->count() > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                    <h4 class="font-medium text-yellow-900 mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Uwagi o edycji menu:
                    </h4>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>• Menu zawiera <strong>{{ $menu->categories()->count() }} {{ $menu->categories()->count() === 1 ? 'kategorię' : 'kategorii' }}</strong> z daniami</li>
                        <li>• Zmiana nazwy menu będzie widoczna wszędzie w systemie</li>
                        <li>• Dezaktywacja menu ukryje wszystkie jego kategorie i dania przed klientami</li>
                        <li>• Zmiana kolejności może wpłynąć na kolejność wyświetlania menu klientom</li>
                        @if($menu->restaurant->menus()->where('sort_order', $menu->sort_order)->where('id', '!=', $menu->id)->exists())
                            <li class="text-red-700">• <strong>Uwaga:</strong> Kolejność {{ $menu->sort_order }} jest już zajęta przez inne menu w tej restauracji</li>
                        @endif
                    </ul>
                </div>
            @endif

            <!-- Wskazówki -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                <h4 class="font-medium text-blue-900 mb-2">
                    <i class="fas fa-lightbulb mr-2"></i>Wskazówki przy edycji menu:
                </h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• <strong>Nazwa menu</strong> - powinna być krótka i opisowa</li>
                    <li>• <strong>Kolejność</strong> - menu z niższą kolejnością wyświetlają się pierwsze</li>
                    <li>• <strong>Status nieaktywny</strong> - używaj podczas pracy nad menu</li>
                    <li>• <strong>Przeniesienie do innej restauracji</strong> - przeniesie również wszystkie kategorie i dania</li>
                    <li>• Po zapisaniu zmian możesz dalej edytować kategorie i dania</li>
                </ul>
            </div>
        </div>
    </div>

    <style>
        /* Radio button styling */
        input[type="radio"]:checked + span {
            background-color: #3b82f6;
            color: white;
        }
        
        input[type="radio"]:checked + * {
            background-color: #eff6ff;
            border-color: #3b82f6;
        }
        
        label:has(input[type="radio"]:checked) {
            background-color: #eff6ff;
            border-color: #3b82f6;
        }
    </style>
</body>
</html>