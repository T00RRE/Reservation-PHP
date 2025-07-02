@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edycja rezerwacji</h1>

    {{-- Dodatkowe informacje o rezerwacji, które mogą być przydatne --}}
    <div class="reservation-summary">
        <p>Restauracja: <strong>{{ $reservation->restaurant->name ?? 'N/A' }}</strong></p>
        <p>Stolik: <strong>{{ $reservation->table->table_number ?? 'N/A' }}</strong></p>
        <p>Status: <span class="status-badge status-{{ strtolower($reservation->status) }}">{{ $reservation->status }}</span></p>
    </div>

    <form action="{{ route('reservations.update', $reservation) }}" method="POST">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label for="reservation_date">Data rezerwacji *</label>
            <input type="date" name="reservation_date" id="reservation_date"
                   value="{{ old('reservation_date', $reservation->reservation_date) }}"
                   min="{{ date('Y-m-d') }}" required>
            @error('reservation_date')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="reservation_time">Godzina *</label>
            {{-- Możesz dostosować listę godzin, jeśli potrzebujesz konkretnych interwałów --}}
            <select name="reservation_time" id="reservation_time" required>
                @php
                    $times = ['12:00', '12:30', '13:00', '13:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30'];
                    $oldTime = old('reservation_time', \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i'));
                @endphp
                <option value="">Wybierz godzinę</option>
                @foreach($times as $time)
                    <option value="{{ $time }}" {{ $oldTime == $time ? 'selected' : '' }}>{{ $time }}</option>
                @endforeach
            </select>
            @error('reservation_time')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="guests_count">Liczba gości *</label>
            <select name="guests_count" id="guests_count" required>
                <option value="">Wybierz liczbę osób</option>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ old('guests_count', $reservation->guests_count) == $i ? 'selected' : '' }}>
                        {{ $i }} {{ $i == 1 ? 'osoba' : ($i <= 4 ? 'osoby' : 'osób') }}
                    </option>
                @endfor
            </select>
            @error('guests_count')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="special_requests">Specjalne życzenia (opcjonalne)</label>
            <textarea name="special_requests" id="special_requests" rows="4"
                      placeholder="np. stolik przy oknie, dieta bezglutenowa..."
                      maxlength="500">{{ old('special_requests', $reservation->special_requests) }}</textarea>
            @error('special_requests')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Zapisz zmiany</button>
            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary"><i class="fas fa-times-circle"></i> Anuluj</a>
        </div>
    </form>
</div>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 40px 20px;
        background: #f0f2f5;
        color: #333;
        line-height: 1.6;
    }

    .container {
        max-width: 700px;
        margin: 20px auto;
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
    }

    h1 {
        font-size: 2.5rem;
        color: #1a202c;
        margin-bottom: 30px;
        text-align: center;
        font-weight: 700;
    }

    .reservation-summary {
        background: #e6f0ff; /* Light blue */
        padding: 18px 25px;
        border-radius: 10px;
        margin-bottom: 25px;
        border-left: 5px solid #667eea;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .reservation-summary p {
        margin: 0;
        color: #4a5568;
        font-size: 0.95rem;
    }

    .reservation-summary strong {
        color: #2b3a55;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: capitalize;
        color: white;
    }

    .status-potwierdzona { background-color: #38a169; } /* Green */
    .status-oczekująca { background-color: #f6ad55; } /* Orange */
    .status-anulowana { background-color: #e53e3e; } /* Red */
    .status-zakończona { background-color: #4a5568; } /* Gray/Dark */

    /* Error messages block */
    .alert-danger {
        background-color: #ffebeb;
        color: #cc0000;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid #ffcccc;
        box-shadow: 0 2px 8px rgba(255, 0, 0, 0.1);
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 25px;
        list-style-type: disc;
    }

    .alert-danger ul li {
        margin-bottom: 5px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4a5568;
        font-size: 1rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
        box-sizing: border-box;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        -webkit-appearance: none; /* Remove default browser styling for select */
        -moz-appearance: none;
        appearance: none;
        background-color: #fff;
    }

    .form-group input[type="date"],
    .form-group input[type="time"] {
        padding-right: 40px; /* Space for the calendar/clock icon */
    }

    .form-group select {
        background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%234a5568%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-6.5%200-12.3%203.2-16.1%208.1-3.9%204.9-5.7%2011.3-4.9%2017.6L129.5%20275c3.7%205.8%209.6%209.2%2016.1%209.2h0c6.5%200%2012.3-3.2%2016.1-8.1l117-174.5c3.9-5%205.7-11.4%204.9-17.6z%22%2F%3E%3C%2Fsvg%3E');
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 12px;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        outline: none;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .error {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 6px;
        font-weight: 500;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        font-size: 1rem;
        cursor: pointer;
        border: none; /* Remove default button border */
    }

    .btn-success {
        background: linear-gradient(45deg, #48bb78, #38a169); /* Green shades */
        color: white;
        box-shadow: 0 4px 15px rgba(56, 161, 105, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(56, 161, 105, 0.4);
        background: linear-gradient(45deg, #38a169, #2f855a);
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background-color: #a0aec0; /* Gray shade */
        color: white;
        box-shadow: 0 2px 10px rgba(160, 174, 192, 0.2);
    }

    .btn-secondary:hover {
        background-color: #718096;
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(160, 174, 192, 0.3);
        color: white;
        text-decoration: none;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container {
            margin: 20px 15px;
            padding: 25px 30px;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 25px;
        }
        .form-actions {
            flex-direction: column;
            gap: 10px;
        }
        .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        body {
            padding: 20px 10px;
        }
        .container {
            padding: 20px;
        }
        h1 {
            font-size: 1.8rem;
        }
        .reservation-summary {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection