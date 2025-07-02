<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szczegóły rezerwacji</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .back-btn { color: #007bff; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .status { padding: 5px 15px; border-radius: 20px; font-weight: bold; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.confirmed { background: #d4edda; color: #155724; }
        .status.cancelled { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .actions { margin-top: 30px; display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; width: 150px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('reservations.index') }}" class="back-btn">← Powrót do listy rezerwacji</a>
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <h1>Szczegóły rezerwacji #{{ $reservation->id }}</h1>
        
        <table>
            <tr>
                <th>Status</th>
                <td>
                    <span class="status {{ $reservation->status }}">
                        @switch($reservation->status)
                            @case('pending') Oczekuje na potwierdzenie @break
                            @case('confirmed') Potwierdzona @break
                            @case('cancelled') Anulowana @break
                            @case('completed') Zakończona @break
                            @default {{ $reservation->status }}
                        @endswitch
                    </span>
                </td>
            </tr>
            <tr>
                <th>Restauracja</th>
                <td>
                    <strong>{{ $reservation->restaurant->name ?? '-' }}</strong><br>
                    <small>{{ $reservation->restaurant->address ?? '' }}</small>
                </td>
            </tr>
            <tr>
                <th>Stolik</th>
                <td>{{ $reservation->table->table_number ?? '-' }} ({{ $reservation->table->capacity ?? '-' }} osób)</td>
            </tr>
            <tr>
                <th>Data i godzina</th>
                <td>
                    <strong>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d.m.Y') }}</strong> 
                    o godzinie <strong>{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}</strong>
                </td>
            </tr>
            <tr>
                <th>Liczba gości</th>
                <td>{{ $reservation->guests_count }} {{ $reservation->guests_count == 1 ? 'osoba' : ($reservation->guests_count <= 4 ? 'osoby' : 'osób') }}</td>
            </tr>
            @if($reservation->special_requests)
            <tr>
                <th>Specjalne życzenia</th>
                <td>{{ $reservation->special_requests }}</td>
            </tr>
            @endif
            <tr>
                <th>Data utworzenia</th>
                <td>{{ $reservation->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            @if($reservation->confirmed_at)
            <tr>
                <th>Data potwierdzenia</th>
                <td>{{ $reservation->confirmed_at->format('d.m.Y H:i') }}</td>
            </tr>
            @endif
        </table>

        <div class="actions">
            @if($reservation->status === 'pending')
                <form action="{{ route('reservations.confirm', $reservation) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">✓ Potwierdź rezerwację</button>
                </form>
            @endif
            
            @if($reservation->canBeCancelled())
                <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning">✏️ Edytuj</a>
                
                <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" style="display: inline;" 
                      onsubmit="return confirm('Na pewno chcesz anulować tę rezerwację?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">✗ Anuluj</button>
                </form>
            @endif
            
            <a href="{{ route('restaurants.show', $reservation->restaurant) }}" class="btn btn-primary">🍽️ Zobacz restaurację</a>
            <a href="{{ route('reservations.index') }}" class="btn btn-secondary">📋 Lista rezerwacji</a>
        </div>
        
        @if($reservation->status === 'pending')
            <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 5px;">
                <strong>ℹ️ Informacja:</strong> Twoja rezerwacja oczekuje na potwierdzenie przez restaurację. 
                Otrzymasz powiadomienie, gdy zostanie potwierdzona.
            </div>
        @endif
    </div>
</body>
</html>