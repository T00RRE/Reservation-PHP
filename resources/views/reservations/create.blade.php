<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowa rezerwacja</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .back-btn { color: #007bff; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; box-sizing: border-box; }
        .btn-reserve { background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; }
        .btn-reserve:hover { background: #218838; }
        .btn-secondary { background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; display: inline-block; margin-left: 10px; }
        .error { color: #dc3545; font-size: 14px; margin-top: 5px; }
        .restaurant-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('restaurants.index') }}" class="back-btn">← Powrót do listy restauracji</a>
        
        <h1>Nowa rezerwacja</h1>
        
        @if($restaurant)
            <div class="restaurant-info">
                <h3>{{ $restaurant->name }}</h3>
                <p>{{ $restaurant->address }}</p>
                <p>{{ $restaurant->phone }}</p>
            </div>
        @endif
        
        @if ($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('reservations.store') }}" method="POST">
            @csrf
            
            @if(!$restaurant)
                <div class="form-group">
                    <label for="restaurant_id">Restauracja *</label>
                    <select name="restaurant_id" id="restaurant_id" required>
                        <option value="">Wybierz restaurację</option>
                        @foreach($restaurants as $rest)
                            <option value="{{ $rest->id }}" {{ old('restaurant_id') == $rest->id ? 'selected' : '' }}>
                                {{ $rest->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('restaurant_id')<div class="error">{{ $message }}</div>@enderror
                </div>
            @else
                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
            @endif
            
            <div class="form-group">
                <label for="reservation_date">Data rezerwacji *</label>
                <input type="date" name="reservation_date" id="reservation_date" 
                       value="{{ old('reservation_date') }}" 
                       min="{{ date('Y-m-d') }}" required>
                @error('reservation_date')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group">
                <label for="reservation_time">Godzina *</label>
                <select name="reservation_time" id="reservation_time" required>
                    <option value="">Wybierz godzinę</option>
                    <option value="12:00" {{ old('reservation_time') == '12:00' ? 'selected' : '' }}>12:00</option>
                    <option value="12:30" {{ old('reservation_time') == '12:30' ? 'selected' : '' }}>12:30</option>
                    <option value="13:00" {{ old('reservation_time') == '13:00' ? 'selected' : '' }}>13:00</option>
                    <option value="13:30" {{ old('reservation_time') == '13:30' ? 'selected' : '' }}>13:30</option>
                    <option value="18:00" {{ old('reservation_time') == '18:00' ? 'selected' : '' }}>18:00</option>
                    <option value="18:30" {{ old('reservation_time') == '18:30' ? 'selected' : '' }}>18:30</option>
                    <option value="19:00" {{ old('reservation_time') == '19:00' ? 'selected' : '' }}>19:00</option>
                    <option value="19:30" {{ old('reservation_time') == '19:30' ? 'selected' : '' }}>19:30</option>
                    <option value="20:00" {{ old('reservation_time') == '20:00' ? 'selected' : '' }}>20:00</option>
                    <option value="20:30" {{ old('reservation_time') == '20:30' ? 'selected' : '' }}>20:30</option>
                    <option value="21:00" {{ old('reservation_time') == '21:00' ? 'selected' : '' }}>21:00</option>
                    <option value="21:30" {{ old('reservation_time') == '21:30' ? 'selected' : '' }}>21:30</option>
                </select>
                @error('reservation_time')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group">
                <label for="guests_count">Liczba gości *</label>
                <select name="guests_count" id="guests_count" required>
                    <option value="">Wybierz liczbę osób</option>
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('guests_count') == $i ? 'selected' : '' }}>
                            {{ $i }} {{ $i == 1 ? 'osoba' : ($i <= 4 ? 'osoby' : 'osób') }}
                        </option>
                    @endfor
                </select>
                @error('guests_count')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group">
                <label for="special_requests">Uwagi (opcjonalne)</label>
                <textarea name="special_requests" id="special_requests" rows="3" 
                          placeholder="np. stolik przy oknie, dieta bezglutenowa...">{{ old('special_requests') }}</textarea>
                @error('special_requests')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <button type="submit" class="btn-reserve">🍽️ Zarezerwuj stolik</button>
            
            <a href="{{ route('restaurants.index') }}" class="btn-secondary">Anuluj</a>
        </form>
        
        <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 5px;">
            <strong>ℹ️ Informacja:</strong> Rezerwacja musi być złożona z wyprzedzeniem co najmniej 2 godzin.
        </div>
    </div>
</body>
</html>