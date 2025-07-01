@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edycja rezerwacji</h1>
    <form action="{{ route('reservations.update', $reservation) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="reservation_date" class="form-label">Data</label>
            <input type="date" name="reservation_date" id="reservation_date" class="form-control" value="{{ old('reservation_date', $reservation->reservation_date) }}" required>
        </div>
        <div class="mb-3">
            <label for="reservation_time" class="form-label">Godzina</label>
            <input type="time" name="reservation_time" id="reservation_time" class="form-control" value="{{ old('reservation_time', $reservation->reservation_time) }}" required>
        </div>
        <div class="mb-3">
            <label for="guests_count" class="form-label">Liczba gości</label>
            <input type="number" name="guests_count" id="guests_count" class="form-control" min="1" max="20" value="{{ old('guests_count', $reservation->guests_count) }}" required>
        </div>
        <div class="mb-3">
            <label for="special_requests" class="form-label">Specjalne życzenia</label>
            <textarea name="special_requests" id="special_requests" class="form-control" maxlength="500">{{ old('special_requests', $reservation->special_requests) }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Zapisz zmiany</button>
        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">Anuluj</a>
    </form>
</div>
@endsection
