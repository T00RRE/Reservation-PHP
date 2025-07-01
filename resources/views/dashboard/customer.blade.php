@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel klienta</h1>
    <h4>Twoje statystyki</h4>
    <ul>
        <li>Liczba rezerwacji: {{ $stats['total_reservations'] ?? 0 }}</li>
        <li>Nadchodzące rezerwacje: {{ $stats['upcoming_reservations'] ?? 0 }}</li>
        <li>Liczba recenzji: {{ $stats['total_reviews'] ?? 0 }}</li>
    </ul>

    <h4>Nadchodzące rezerwacje</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Data</th>
                <th>Godzina</th>
                <th>Restauracja</th>
                <th>Stolik</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($upcomingReservations as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_date }}</td>
                    <td>{{ $reservation->reservation_time }}</td>
                    <td>{{ $reservation->restaurant->name ?? '-' }}</td>
                    <td>{{ $reservation->table->table_number ?? '-' }}</td>
                    <td>{{ $reservation->status }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Brak nadchodzących rezerwacji.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Ostatnie rezerwacje</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Data</th>
                <th>Godzina</th>
                <th>Restauracja</th>
                <th>Stolik</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentReservations as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_date }}</td>
                    <td>{{ $reservation->reservation_time }}</td>
                    <td>{{ $reservation->restaurant->name ?? '-' }}</td>
                    <td>{{ $reservation->table->table_number ?? '-' }}</td>
                    <td>{{ $reservation->status }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Brak rezerwacji.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Ulubione restauracje</h4>
    <div class="row">
        @forelse($favoriteRestaurants as $restaurant)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $restaurant->name }}</h5>
                        <p class="card-text">Ocena: {{ $restaurant->rating ?? '-' }}</p>
                        <a href="{{ route('restaurants.show', $restaurant) }}" class="btn btn-primary">Zobacz</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">Brak ulubionych restauracji.</div>
        @endforelse
    </div>
</div>
@endsection
