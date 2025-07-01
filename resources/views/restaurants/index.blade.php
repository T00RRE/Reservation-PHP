<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Restauracji</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .restaurant { border: 1px solid #ddd; padding: 20px; margin: 10px 0; border-radius: 5px; }
        .restaurant h3 { color: #333; margin: 0 0 10px 0; }
        .rating { color: #f39c12; font-weight: bold; }
    </style>
</head>
<body>
    <h1>System Rezerwacji Restauracji</h1>
    <h2>Lista Restauracji</h2>
    
    @if($restaurants->count() > 0)
        @foreach($restaurants as $restaurant)
            <div class="restaurant">
            <h3><a href="{{ route('restaurants.show', $restaurant) }}" style="color: #333; text-decoration: none;">{{ $restaurant->name }}</a></h3>
                <p><strong>Adres:</strong> {{ $restaurant->address }}</p>
                <p><strong>Telefon:</strong> {{ $restaurant->phone }}</p>
                <p><strong>Email:</strong> {{ $restaurant->email }}</p>
                <p><strong>Ocena:</strong> <span class="rating">{{ $restaurant->rating }}/5</span></p>
                @if($restaurant->description)
                    <p>{{ $restaurant->description }}</p>
                @endif
                <p style="margin-top: 15px;">
        <a href="{{ route('restaurants.show', $restaurant) }}" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
            🍽️ Zobacz szczegóły i zarezerwuj
        </a>
    </p>
            </div>
        @endforeach
    @else
        <p>Brak restauracji w systemie.</p>
    @endif
</body>
</html>