@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Szczegóły rezerwacji</h1>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $reservation->id }}</td>
        </tr>
        <tr>
            <th>Restauracja</th>
            <td>{{ $reservation->restaurant->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Stolik</th>
            <td>{{ $reservation->table->table_number ?? '-' }}</td>
        </tr>
        <tr>
            <th>Data</th>
            <td>{{ $reservation->reservation_date }}</td>
        </tr>
        <tr>
            <th>Godzina</th>
            <td>{{ $reservation->reservation_time }}</td>
        </tr>
        <tr>
            <th>Liczba gości</th>
            <td>{{ $reservation->guests_count }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $reservation->status }}</td>
        </tr>
        <tr>
            <th>Specjalne życzenia</th>
            <td>{{ $reservation->special_requests ?? '-' }}</td>
        </tr>
    </table>
    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning">Edytuj</a>
    <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Powrót</a>
</div>
@endsection
