<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj restaurację</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Nagłówek -->
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Dodaj nową restaurację</h1>
                    <a href="{{ route('restaurants.index') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        ← Powrót
                    </a>
                </div>
            </div>

            <!-- Błędy -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <strong>Błędy:</strong>
                    <ul class="mt-2">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formularz -->
            <div class="bg-white p-6 rounded-lg shadow">
                <form method="POST" action="{{ route('restaurants.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Nazwa -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nazwa restauracji *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Adres -->
                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Adres *
                        </label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address') }}"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Telefon -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefon *
                        </label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}"
                               placeholder="+48123456789"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        <p class="text-sm text-gray-600 mt-1">Format: +48xxxxxxxxx</p>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Opis -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Opis restauracji
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                        <p class="text-sm text-gray-600 mt-1">Minimalnie 10 znaków</p>
                    </div>

                    <!-- Zdjęcie -->
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            Zdjęcie restauracji
                        </label>
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               class="w-full p-3 border border-gray-300 rounded-lg">
                        <p class="text-sm text-gray-600 mt-1">Maksymalny rozmiar: 2MB</p>
                    </div>

                    <!-- Status aktywności -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm font-medium text-gray-700">Restauracja aktywna</span>
                        </label>
                    </div>

                    <!-- Przyciski -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('restaurants.index') }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                            Anuluj
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                            Dodaj restaurację
                        </button>
                    </div>
                </form>
            </div>

            <!-- Wskazówki -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                <h3 class="font-medium text-blue-900 mb-2">💡 Wskazówki:</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Nazwa restauracji musi być unikalna</li>
                    <li>• Numer telefonu musi być w formacie +48xxxxxxxxx</li>
                    <li>• Email musi być unikalny w systemie</li>
                    <li>• Opis pomoże klientom poznać restaurację</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>