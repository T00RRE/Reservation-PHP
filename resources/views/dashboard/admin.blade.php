@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel administratora</h1>
    <h4>Statystyki</h4>
    <ul>
        <li>Liczba restauracji: {{ $stats['total_restaurants'] ?? 0 }}</li>
        <li>Liczba użytkowników: {{ $stats['total_users'] ?? 0 }}</li>
        <li>Liczba rezerwacji: {{ $stats['total_reservations'] ?? 0 }}</li>
        <li>Oczekujące recenzje: {{ $stats['pending_reviews'] ?? 0 }}</li>
        <li>Dzisiejsze rezerwacje: {{ $stats['todays_reservations'] ?? 0 }}</li>
        <li>Aktywne restauracje: {{ $stats['active_restaurants'] ?? 0 }}</li>
    </ul>

    <h4>Ostatnie rezerwacje</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Restauracja</th>
                <th>Stolik</th>
                <th>Gość</th>
                <th>Data</th>
                <th>Godzina</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentReservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->restaurant->name ?? '-' }}</td>
                    <td>{{ $reservation->table->table_number ?? '-' }}</td>
                    <td>{{ $reservation->user->name ?? '-' }}</td>
                    <td>{{ $reservation->reservation_date }}</td>
                    <td>{{ $reservation->reservation_time }}</td>
                    <td>{{ $reservation->status }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Brak rezerwacji.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Oczekujące recenzje</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Restauracja</th>
                <th>Gość</th>
                <th>Ocena</th>
                <th>Treść</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingReviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>{{ $review->restaurant->name ?? '-' }}</td>
                    <td>{{ $review->user->name ?? '-' }}</td>
                    <td>{{ $review->rating ?? '-' }}</td>
                    <td>{{ $review->content ?? '-' }}</td>
                    <td>{{ $review->status ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Brak oczekujących recenzji.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
