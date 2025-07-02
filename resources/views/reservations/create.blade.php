<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowa rezerwacja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPYXKC2bHF8baqQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4a5568;
            text-decoration: none;
            margin-bottom: 25px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: #667eea;
        }

        .restaurant-info {
            background: linear-gradient(135deg, #e3f2fd, #d1eaff);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 5px solid #667eea;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .restaurant-info h3 {
            margin-top: 0;
            color: #1a202c;
            font-size: 1.6rem;
            margin-bottom: 8px;
        }

        .restaurant-info p {
            margin: 4px 0;
            color: #4a5568;
            font-size: 0.95rem;
        }

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
        
        .form-group input[type="date"] {
            padding-right: 40px; /* Space for the calendar icon */
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
            min-height: 90px;
        }

        .error {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 6px;
            font-weight: 500;
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
        }

        .btn-reserve {
            background: linear-gradient(45deg, #48bb78, #38a169); /* Green shades */
            color: white;
            border: none;
            width: 100%;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(56, 161, 105, 0.3);
        }

        .btn-reserve:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(56, 161, 105, 0.4);
            background: linear-gradient(45deg, #38a169, #2f855a);
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            background-color: #a0aec0; /* Gray shade */
            color: white;
            border: none;
            margin-left: 0; /* Remove default margin for consistency */
            width: 93%;
            text-align: center; /* Center text in button */
            box-shadow: 0 2px 10px rgba(160, 174, 192, 0.2);
        }

        .btn-secondary:hover {
            background-color: #718096;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(160, 174, 192, 0.3);
            color: white;
            text-decoration: none;
        }

        .info-box {
            margin-top: 30px;
            padding: 20px;
            background: #e6f0ff; /* Light blue */
            border-radius: 10px;
            border-left: 5px solid #667eea;
            color: #4a5568;
            font-size: 0.95rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .info-box strong {
            color: #2b3a55;
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
            .btn-reserve, .btn-secondary {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('restaurants.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Powrót do listy restauracji
        </a>
        
        <h1>Nowa rezerwacja</h1>
        
        @if($restaurant)
            <div class="restaurant-info">
                <h3>{{ $restaurant->name }}</h3>
                <p><i class="fas fa-map-marker-alt"></i> {{ $restaurant->address }}</p>
                <p><i class="fas fa-phone"></i> {{ $restaurant->phone }}</p>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('reservations.store') }}" method="POST">
            @csrf
            
            @if(!$restaurant)
                <div class="form-group">
                    <label for="restaurant_id">Restauracja *</label>
                    <select name="restaurant_id" id="restaurant_id" required>
                        <option value="">Wybierz restaurację</option>
                        @foreach($restaurants as $rest)
                            <option value="{{ $rest->id }}" {{ old('restaurant_id') == $rest->id ? 'selected' : '' }}>
                                {{ $rest->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('restaurant_id')<div class="error">{{ $message }}</div>@enderror
                </div>
            @else
                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
            @endif
            
            <div class="form-group">
                <label for="reservation_date">Data rezerwacji *</label>
                <input type="date" name="reservation_date" id="reservation_date" 
                       value="{{ old('reservation_date') }}" 
                       min="{{ date('Y-m-d') }}" required>
                @error('reservation_date')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group">
                <label for="reservation_time">Godzina *</label>
                <select name="reservation_time" id="reservation_time" required>
                    <option value="">Wybierz godzinę</option>
                    <option value="12:00" {{ old('reservation_time') == '12:00' ? 'selected' : '' }}>12:00</option>
                    <option value="12:30" {{ old('reservation_time') == '12:30' ? 'selected' : '' }}>12:30</option>
                    <option value="13:00" {{ old('reservation_time') == '13:00' ? 'selected' : '' }}>13:00</option>
                    <option value="13:30" {{ old('reservation_time') == '13:30' ? 'selected' : '' }}>13:30</option>
                    <option value="18:00" {{ old('reservation_time') == '18:00' ? 'selected' : '' }}>18:00</option>
                    <option value="18:30" {{ old('reservation_time') == '18:30' ? 'selected' : '' }}>18:30</option>
                    <option value="19:00" {{ old('reservation_time') == '19:00' ? 'selected' : '' }}>19:00</option>
                    <option value="19:30" {{ old('reservation_time') == '19:30' ? 'selected' : '' }}>19:30</option>
                    <option value="20:00" {{ old('reservation_time') == '20:00' ? 'selected' : '' }}>20:00</option>
                    <option value="20:30" {{ old('reservation_time') == '20:30' ? 'selected' : '' }}>20:30</option>
                    <option value="21:00" {{ old('reservation_time') == '21:00' ? 'selected' : '' }}>21:00</option>
                    <option value="21:30" {{ old('reservation_time') == '21:30' ? 'selected' : '' }}>21:30</option>
                </select>
                @error('reservation_time')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group">
                <label for="guests_count">Liczba gości *</label>
                <select name="guests_count" id="guests_count" required>
                    <option value="">Wybierz liczbę osób</option>
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('guests_count') == $i ? 'selected' : '' }}>
                            {{ $i }} {{ $i == 1 ? 'osoba' : ($i <= 4 ? 'osoby' : 'osób') }}
                        </option>
                    @endfor
                </select>
                @error('guests_count')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group">
                <label for="special_requests">Uwagi (opcjonalne)</label>
                <textarea name="special_requests" id="special_requests" rows="3" 
                          placeholder="np. stolik przy oknie, dieta bezglutenowa...">{{ old('special_requests') }}</textarea>
                @error('special_requests')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <button type="submit" class="btn btn-reserve">
                <i class="fas fa-utensils"></i> Zarezerwuj stolik
            </button>
            
            <a href="{{ route('restaurants.index') }}" class="btn btn-secondary">
                <i class="fas fa-times-circle"></i> Anuluj
            </a>
        </form>
        
        <div class="info-box">
            <strong>ℹ️ Informacja:</strong> Rezerwacja musi być złożona z wyprzedzeniem co najmniej 2 godzin.
        </div>
    </div>
</body>
</html>