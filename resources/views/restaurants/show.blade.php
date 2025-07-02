@extends('layouts.app')

@section('title', $restaurant->name . ' - RestaurantBook')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('restaurants.index') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>Restauracje
                </a>
            </li>
            <li class="breadcrumb-item active">{{ $restaurant->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4 header-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="card-title fw-bold text-primary mb-2">
                                <i class="fas fa-utensils me-2"></i>{{ $restaurant->name }}
                            </h1>
                            <div class="restaurant-rating mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($restaurant->rating))
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($i - 0.5 <= $restaurant->rating)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                                <span class="ms-2 fw-bold">{{ number_format($restaurant->rating, 1) }}/5</span>
                                <small class="text-muted ms-1">(Średnia ocen)</small>
                            </div>
                            @if($restaurant->description)
                                <p class="text-muted mb-0">{{ $restaurant->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block"> {{-- Hide on small screens --}}
                            <div class="bg-light rounded p-4 info-widget">
                                <i class="fas fa-concierge-bell text-primary mb-2" style="font-size: 3rem;"></i>
                                <p class="small text-muted mb-0">Gotowi do Państwa obsługi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 info-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informacje kontaktowe</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-danger text-white me-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <strong>Adres:</strong><br>
                                    <span class="text-muted">{{ $restaurant->address }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <strong>Telefon:</strong><br>
                                    <a href="tel:{{ $restaurant->phone }}" class="text-decoration-none text-body">{{ $restaurant->phone }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-info text-white me-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <strong>Email:</strong><br>
                                    <a href="mailto:{{ $restaurant->email }}" class="text-decoration-none text-body">{{ $restaurant->email }}</a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <strong>Godziny otwarcia:</strong><br>
                                    <span class="text-muted">Codziennie 12:00 - 22:00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($restaurant->menus->count() > 0)
            <div class="card mb-4 menu-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-book-open me-2"></i>Menu restauracji</h5>
                </div>
                <div class="card-body">
                    @foreach($restaurant->menus->take(1) as $menu)
                        @foreach($menu->categories->take(2) as $category)
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-list me-2"></i>{{ $category->name }}
                            </h6>
                            <div class="row">
                                @foreach($category->dishes->take(3) as $dish)
                                    <div class="col-md-4 mb-3">
                                        <div class="dish-item border rounded p-3 h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="fw-bold mb-1">{{ $dish->name }}</h6>
                                                <p class="small text-muted mb-2">{{ Str::limit($dish->description, 60) }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                                <span class="fw-bold text-success fs-5">{{ $dish->formatted_price }}</span>
                                                @if($dish->is_vegetarian)
                                                    <span class="badge bg-success"><i class="fas fa-leaf me-1"></i>Wege</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('restaurants.menu', $restaurant) }}" class="btn btn-outline-success btn-lg">
                            <i class="fas fa-eye me-2"></i>Zobacz pełne menu
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top reservation-sidebar" style="top: 20px;">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Zarezerwuj stolik</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                        
                        <div class="mb-3 form-group-custom">
                            <label for="reservation_date" class="form-label fw-bold">
                                <i class="fas fa-calendar-alt me-2"></i>Data rezerwacji
                            </label>
                            <input type="date" class="form-control" name="reservation_date" id="reservation_date" 
                                   value="{{ old('reservation_date') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3 form-group-custom">
                            <label for="reservation_time" class="form-label fw-bold">
                                <i class="fas fa-clock me-2"></i>Godzina
                            </label>
                            <select class="form-select" name="reservation_time" id="reservation_time" required>
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
                        </div>
                        
                        <div class="mb-3 form-group-custom">
                            <label for="guests_count" class="form-label fw-bold">
                                <i class="fas fa-users me-2"></i>Liczba gości
                            </label>
                            <select class="form-select" name="guests_count" id="guests_count" required>
                                <option value="">Wybierz liczbę osób</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('guests_count') == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'osoba' : ($i <= 4 ? 'osoby' : 'osób') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="mb-3 form-group-custom">
                            <label for="special_requests" class="form-label fw-bold">
                                <i class="fas fa-comment-dots me-2"></i>Uwagi <small class="text-muted">(opcjonalne)</small>
                            </label>
                            <textarea class="form-control" name="special_requests" id="special_requests" rows="3" 
                                      placeholder="np. stolik przy oknie, dieta bezglutenowa...">{{ old('special_requests') }}</textarea>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-warning btn-lg reserve-btn">
                                <i class="fas fa-calendar-check me-2"></i>Zarezerwuj stolik
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-4 p-3 bg-light rounded info-box-sidebar">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fas fa-info-circle me-2"></i>Informacje
                        </h6>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>Rezerwacja z wyprzedzeniem min. 2h</li>
                            <li>Maksymalnie 3 rezerwacje dziennie</li>
                            <li>Potwierdzenie w ciągu 15 minut</li>
                            <li>Możliwość anulowania do 2h przed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f2f5;
        color: #333;
    }

    .container {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    /* Breadcrumb styles */
    .breadcrumb {
        background-color: #e9ecef;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .breadcrumb-item a {
        color: #667eea;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .breadcrumb-item a:hover {
        color: #5a67d8;
    }

    .breadcrumb-item.active {
        color: #4a5568;
        font-weight: 600;
    }

    /* Card styles */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        overflow: hidden; /* Ensures rounded corners apply to content */
    }

    .card-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 18px 25px;
        font-size: 1.25rem;
        font-weight: 600;
        color: white;
        background: linear-gradient(90deg, #667eea, #764ba2); /* Primary gradient */
    }

    .card-header.bg-primary { background: linear-gradient(90deg, #667eea, #764ba2); }
    .card-header.bg-success { background: linear-gradient(90deg, #48bb78, #38a169); }
    .card-header.bg-warning { background: linear-gradient(90deg, #f6ad55, #ed8936); } /* Orange gradient */


    /* Restaurant Header Card */
    .header-card .card-body {
        padding: 30px;
    }

    .header-card h1 {
        font-size: 2.8rem;
        color: #1a202c;
        margin-bottom: 15px;
    }

    .restaurant-rating .fas,
    .restaurant-rating .far {
        font-size: 1.4rem;
        color: #fbd38d; /* Brighter warning color for stars */
    }

    .restaurant-rating .fw-bold {
        font-size: 1.2rem;
        color: #2d3748;
    }

    .info-widget {
        background-color: #eff6ff; /* Lighter blue */
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #c3daff;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }

    /* Contact Info Card */
    .icon-circle {
        width: 45px;
        height: 45px;
        min-width: 45px; /* Ensure consistent size */
        min-height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .icon-circle.bg-danger { background-color: #e53e3e !important; } /* Tailwind red-600 */
    .icon-circle.bg-success { background-color: #38a169 !important; } /* Tailwind green-600 */
    .icon-circle.bg-info { background-color: #3182ce !important; } /* Tailwind blue-600 */
    .icon-circle.bg-warning { background-color: #dd6b20 !important; } /* Tailwind orange-600 */


    /* Menu Preview Card */
    .menu-card .card-body {
        padding: 25px;
    }

    .menu-card h6.text-primary {
        color: #667eea !important;
        font-size: 1.15rem;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }

    .dish-item {
        background-color: #fdfdfd;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        padding: 15px;
    }

    .dish-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .dish-item .fw-bold {
        color: #2d3748;
    }

    .dish-item .text-success {
        color: #38a169 !important;
    }

    .dish-item .badge.bg-success {
        background-color: #48bb78 !important;
        padding: 6px 10px;
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .btn-outline-success {
        color: #38a169;
        border-color: #38a169;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-success:hover {
        background-color: #38a169;
        color: white;
        box-shadow: 0 4px 15px rgba(56, 161, 105, 0.3);
    }

    /* Reservation Sidebar */
    .reservation-sidebar {
        border-top: 4px solid #f6ad55; /* Accent color on top */
    }

    .reservation-sidebar .card-body {
        padding: 25px;
    }

    .form-group-custom label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4a5568;
        font-size: 1rem;
    }

    .form-group-custom .form-control,
    .form-group-custom .form-select {
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

    .form-group-custom .form-control:focus,
    .form-group-custom .form-select:focus,
    .form-group-custom textarea:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        outline: none;
    }

    .form-group-custom select {
        background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%234a5568%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-6.5%200-12.3%203.2-16.1%208.1-3.9%204.9-5.7%2011.3-4.9%2017.6L129.5%20275c3.7%205.8%209.6%209.2%2016.1%209.2h0c6.5%200%2012.3-3.2%2016.1-8.1l117-174.5c3.9-5%205.7-11.4%204.9-17.6z%22%2F%3E%3C%2Fsvg%3E');
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 12px;
    }

    .form-group-custom textarea {
        resize: vertical;
        min-height: 80px;
    }

    .reserve-btn {
        background: linear-gradient(45deg, #f6ad55, #ed8936); /* Orange gradient */
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(237, 137, 54, 0.3);
        transition: all 0.3s ease;
    }

    .reserve-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(237, 137, 54, 0.4);
        background: linear-gradient(45deg, #ed8936, #dd6b20);
        color: white;
        text-decoration: none;
    }

    .info-box-sidebar {
        background-color: #eef2f6 !important; /* Lighter background for info */
        border: 1px solid #d1d9e0;
        padding: 15px;
        border-radius: 10px;
    }

    .info-box-sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-box-sidebar ul li {
        margin-bottom: 5px;
        display: flex;
        align-items: center;
    }

    .info-box-sidebar ul li:last-child {
        margin-bottom: 0;
    }

    .info-box-sidebar ul li::before {
        content: "\2022"; /* Bullet point */
        color: #667eea;
        font-weight: bold;
        display: inline-block;
        width: 1em;
        margin-left: -1em;
    }


    /* General styling for Bootstrap classes to ensure consistency */
    .text-primary { color: #667eea !important; }
    .text-success { color: #38a169 !important; }
    .text-warning { color: #fbd38d !important; } /* Lighter for stars */
    .text-danger { color: #e53e3e !important; }
    .text-info { color: #3182ce !important; }
    .text-muted { color: #718096 !important; } /* Darker muted text */

    .bg-primary { background-color: #667eea !important; }
    .bg-success { background-color: #38a169 !important; }
    .bg-warning { background-color: #f6ad55 !important; }
    .bg-danger { background-color: #e53e3e !important; }
    .bg-info { background-color: #3182ce !important; }
    .bg-light { background-color: #f8f9fa !important; } /* Bootstrap default light */


    /* Responsive Adjustments */
    @media (max-width: 991.98px) { /* Medium devices and down */
        .sticky-top {
            position: static !important; /* Disable sticky sidebar */
            margin-top: 30px;
        }
    }

    @media (max-width: 767.98px) { /* Small devices and down */
        .container {
            padding: 20px 15px;
        }
        h1 {
            font-size: 2.2rem;
            text-align: left;
        }
        .header-card .card-body {
            padding: 20px;
        }
        .info-widget {
            display: none; /* Hide concierge bell on very small screens */
        }
        .row > div {
            margin-bottom: 20px; /* Add spacing between cards on mobile */
        }
        .form-group-custom label {
            font-size: 0.95rem;
        }
        .form-group-custom .form-control,
        .form-group-custom .form-select {
            padding: 10px 12px;
            font-size: 0.95rem;
        }
    }
</style>
@endsection

@push('scripts')
<script>
    // Form enhancement
    document.getElementById('reservationForm').addEventListener('submit', function(e) {
        let submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sprawdzanie dostępności...';
    });

    // Date validation for reservation form
    document.getElementById('reservation_date').addEventListener('change', function() {
        let selectedDate = new Date(this.value);
        let today = new Date();
        // Reset time for comparison to only compare dates
        today.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            this.setCustomValidity('Data rezerwacji nie może być w przeszłości.');
        } else {
            this.setCustomValidity('');
        }
        // Force validation message display if invalid
        if (!this.checkValidity()) {
            this.reportValidity();
        }
    });

    // Initial check for date field on page load if old value exists
    document.addEventListener('DOMContentLoaded', function() {
        const reservationDateInput = document.getElementById('reservation_date');
        if (reservationDateInput && reservationDateInput.value) {
            let selectedDate = new Date(reservationDateInput.value);
            let today = new Date();
            today.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                reservationDateInput.setCustomValidity('Data rezerwacji nie może być w przeszłości.');
            } else {
                reservationDateInput.setCustomValidity('');
            }
        }
    });
</script>
@endpush