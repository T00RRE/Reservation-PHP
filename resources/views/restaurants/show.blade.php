@extends('layouts.app')

@section('title', $restaurant->name . ' - RestaurantBook')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
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
        <!-- Restaurant Info -->
        <div class="col-lg-8">
            <!-- Header Card -->
            <div class="card mb-4">
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
                        <div class="col-md-4 text-center">
                            <div class="bg-light rounded p-4">
                                <i class="fas fa-concierge-bell text-primary mb-2" style="font-size: 3rem;"></i>
                                <p class="small text-muted mb-0">Gotowi do Państwa obsługi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card mb-4">
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
                                    <a href="tel:{{ $restaurant->phone }}" class="text-decoration-none">{{ $restaurant->phone }}</a>
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
                                    <a href="mailto:{{ $restaurant->email }}" class="text-decoration-none">{{ $restaurant->email }}</a>
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

            <!-- Menu Preview -->
            @if($restaurant->menus->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-book-open me-2"></i>Menu restauracji</h5>
                </div>
                <div class="card-body">
                    @foreach($restaurant->menus->take(1) as $menu)
                        @foreach($menu->categories->take(2) as $category)
                            <h6 class="fw-bold text-primary border-bottom pb-2">
                                <i class="fas fa-list me-2"></i>{{ $category->name }}
                            </h6>
                            <div class="row">
                                @foreach($category->dishes->take(3) as $dish)
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="fw-bold">{{ $dish->name }}</h6>
                                            <p class="small text-muted mb-2">{{ Str::limit($dish->description, 60) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-success">{{ $dish->formatted_price }}</span>
                                                @if($dish->is_vegetarian)
                                                    <span class="badge bg-success">Wege</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('restaurants.menu', $restaurant) }}" class="btn btn-outline-success">
                            <i class="fas fa-eye me-2"></i>Zobacz pełne menu
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Reservation Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Zarezerwuj stolik</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><small>{{ $error }}</small></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                        
                        <div class="mb-3">
                            <label for="reservation_date" class="form-label fw-bold">
                                <i class="fas fa-calendar me-1"></i>Data rezerwacji
                            </label>
                            <input type="date" class="form-control" name="reservation_date" id="reservation_date" 
                                   value="{{ old('reservation_date') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reservation_time" class="form-label fw-bold">
                                <i class="fas fa-clock me-1"></i>Godzina
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
                        
                        <div class="mb-3">
                            <label for="guests_count" class="form-label fw-bold">
                                <i class="fas fa-users me-1"></i>Liczba gości
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
                        
                        <div class="mb-3">
                            <label for="special_requests" class="form-label fw-bold">
                                <i class="fas fa-comment me-1"></i>Uwagi <small class="text-muted">(opcjonalne)</small>
                            </label>
                            <textarea class="form-control" name="special_requests" id="special_requests" rows="3" 
                                      placeholder="np. stolik przy oknie, dieta bezglutenowa...">{{ old('special_requests') }}</textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-utensils me-2"></i>Zarezerwuj stolik
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fas fa-info-circle me-2"></i>Informacje
                        </h6>
                        <ul class="small text-muted mb-0">
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
@endsection

@push('styles')
<style>
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .sticky-top {
        z-index: 1020;
    }
    
    .card-header.bg-primary,
    .card-header.bg-success,
    .card-header.bg-warning {
        border: none;
    }
    
    .restaurant-rating .fas,
    .restaurant-rating .far {
        font-size: 1.2rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Form enhancement
    document.getElementById('reservationForm').addEventListener('submit', function(e) {
        let submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sprawdzanie dostępności...';
    });

    // Date validation
    document.getElementById('reservation_date').addEventListener('change', function() {
        let selectedDate = new Date(this.value);
        let today = new Date();
        
        if (selectedDate < today) {
            this.setCustomValidity('Data nie może być w przeszłości');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
@endpush