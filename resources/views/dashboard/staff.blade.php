@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel pracownika restauracji</h1>
    <h3>Restauracja: {{ $restaurant->name ?? '-' }}</h3>

    <h4>Dzisiejsze rezerwacje</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Godzina</th>
                <th>Stolik</th>
                <th>Gość</th>
                <th>Liczba osób</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todaysReservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->reservation_time }}</td>
                    <td>{{ $reservation->table->table_number ?? '-' }}</td>
                    <td>{{ $reservation->user->name ?? '-' }}</td>
                    <td>{{ $reservation->guests_count }}</td>
                    <td>{{ $reservation->status }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Brak rezerwacji na dziś.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Nadchodzące rezerwacje (7 dni)</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Data</th>
                <th>Godzina</th>
                <th>Stolik</th>
                <th>Gość</th>
                <th>Liczba osób</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($upcomingReservations as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_date }}</td>
                    <td>{{ $reservation->reservation_time }}</td>
                    <td>{{ $reservation->table->table_number ?? '-' }}</td>
                    <td>{{ $reservation->user->name ?? '-' }}</td>
                    <td>{{ $reservation->guests_count }}</td>
                    <td>{{ $reservation->status }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Brak nadchodzących rezerwacji.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
