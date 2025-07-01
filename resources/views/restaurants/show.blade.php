<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $restaurant->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .back-btn { color: #007bff; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .restaurant-header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .restaurant-header h1 { color: #333; margin: 0 0 10px 0; }
        .rating { color: #f39c12; font-size: 18px; font-weight: bold; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; }
        .reservation-form { background: #f8f9fa; padding: 25px; border-radius: 8px; margin-top: 30px; }
        .reservation-form h3 { margin-top: 0; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        .btn-reserve { background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; }
        .btn-reserve:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('restaurants.index') }}" class="back-btn">← Powrót do listy restauracji</a>
        
        <div class="restaurant-header">
            <h1>{{ $restaurant->name }}</h1>
            <div class="rating">★★★★☆ {{ $restaurant->rating }}/5</div>
        </div>

        <div class="info-grid">
            <div>
                <h4>📍 Adres</h4>
                <p>{{ $restaurant->address }}</p>
                
                <h4>📞 Kontakt</h4>
                <p>{{ $restaurant->phone }}</p>
                <p>{{ $restaurant->email }}</p>
            </div>
            <div>
                @if($restaurant->description)
                <h4>ℹ️ O restauracji</h4>
                <p>{{ $restaurant->description }}</p>
                @endif
            </div>
        </div>

        <div class="reservation-form">
            <h3>🍽️ Zarezerwuj stolik</h3>
            <form action="{{ route('reservations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                
                <div class="form-group">
                    <label for="reservation_date">Data:</label>
                    <input type="date" id="reservation_date" name="reservation_date" min="{{ date('Y-m-d') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="reservation_time">Godzina:</label>
                    <select id="reservation_time" name="reservation_time" required>
                        <option value="">Wybierz godzinę</option>
                        <option value="12:00">12:00</option>
                        <option value="12:30">12:30</option>
                        <option value="13:00">13:00</option>
                        <option value="13:30">13:30</option>
                        <option value="18:00">18:00</option>
                        <option value="18:30">18:30</option>
                        <option value="19:00">19:00</option>
                        <option value="19:30">19:30</option>
                        <option value="20:00">20:00</option>
                        <option value="20:30">20:30</option>
                        <option value="21:00">21:00</option>
                        <option value="21:30">21:30</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="guests_count">Liczba osób:</label>
                    <select id="guests_count" name="guests_count" required>
                        <option value="">Wybierz liczbę osób</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'osoba' : ($i <= 4 ? 'osoby' : 'osób') }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="special_requests">Uwagi (opcjonalne):</label>
                    <input type="text" id="special_requests" name="special_requests" placeholder="np. stolik przy oknie, dieta bezglutenowa...">
                </div>
                
                <button type="submit" class="btn-reserve">🍽️ Zarezerwuj stolik</button>
            </form>
        </div>
    </div>
</body>
</html>