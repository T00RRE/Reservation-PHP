@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nowa rezerwacja</h1>
    <form action="{{ route('reservations.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="restaurant_id" class="form-label">Restauracja</label>
            <select name="restaurant_id" id="restaurant_id" class="form-control" required>
                <option value="">Wybierz restaurację</option>
                @foreach($restaurants as $restaurant)
                    <option value="{{ $restaurant->id }}" {{ old('restaurant_id', $restaurant->id ?? '') == $restaurant->id ? 'selected' : '' }}>{{ $restaurant->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="reservation_date" class="form-label">Data</label>
            <input type="date" name="reservation_date" id="reservation_date" class="form-control" value="{{ old('reservation_date') }}" required>
        </div>
        <div class="mb-3">
            <label for="reservation_time" class="form-label">Godzina</label>
            <input type="time" name="reservation_time" id="reservation_time" class="form-control" value="{{ old('reservation_time') }}" required>
        </div>
        <div class="mb-3">
            <label for="guests_count" class="form-label">Liczba gości</label>
            <input type="number" name="guests_count" id="guests_count" class="form-control" min="1" max="20" value="{{ old('guests_count', 1) }}" required>
        </div>
        <div class="mb-3">
            <label for="special_requests" class="form-label">Specjalne życzenia</label>
            <textarea name="special_requests" id="special_requests" class="form-control" maxlength="500">{{ old('special_requests') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Zarezerwuj</button>
        <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Anuluj</a>
    </form>
</div>
@endsection
