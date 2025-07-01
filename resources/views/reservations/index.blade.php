@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista rezerwacji</h1>
    <a href="{{ route('reservations.create') }}" class="btn btn-primary mb-3">Nowa rezerwacja</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Restauracja</th>
                <th>Stolik</th>
                <th>Data</th>
                <th>Godzina</th>
                <th>Liczba gości</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->restaurant->name ?? '-' }}</td>
                    <td>{{ $reservation->table->table_number ?? '-' }}</td>
                    <td>{{ $reservation->reservation_date }}</td>
                    <td>{{ $reservation->reservation_time }}</td>
                    <td>{{ $reservation->guests_count }}</td>
                    <td>{{ $reservation->status }}</td>
                    <td>
                        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-sm btn-info">Szczegóły</a>
                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-warning">Edytuj</a>
                        <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Na pewno anulować?')">Usuń</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">Brak rezerwacji.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $reservations->links() }}
</div>
@endsection
