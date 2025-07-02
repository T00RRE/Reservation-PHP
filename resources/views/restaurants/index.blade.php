@extends('layouts.app')

@section('title', 'Lista Restauracji - RestaurantBook')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-utensils me-3"></i>Zarezerwuj stolik w najlepszych restauracjach
                </h1>
                <p class="lead mb-4">
                    Odkryj wyjątkowe smaki i atmosferę w naszych wyselekcjonowanych restauracjach. 
                    Szybko i łatwo zarezerwuj stolik na wybraną godzinę.
                </p>
                <div class="d-flex gap-3">
                    <a href="#restaurants" class="btn btn-warning btn-lg">
                        <i class="fas fa-search me-2"></i>Przeglądaj restauracje
                    </a>
                    <a href="{{ route('reservations.create') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Nowa rezerwacja
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-concierge-bell" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Restaurants Section -->
<div class="container" id="restaurants">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center mb-5">
                <i class="fas fa-star text-warning me-2"></i>
                Nasze Restauracje
                <i class="fas fa-star text-warning ms-2"></i>
            </h2>
        </div>
    </div>

    @if($restaurants->count() > 0)
        <div class="row g-4">
            @foreach($restaurants as $restaurant)
                <div class="col-lg-4 col-md-6">
                    <div class="card restaurant-card h-100">
                        <!-- Restaurant Image Placeholder -->
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-bold text-primary">{{ $restaurant->name }}</h5>
                                <span class="restaurant-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($restaurant->rating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $restaurant->rating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <small class="text-muted ms-1">({{ number_format($restaurant->rating, 1) }})</small>
                                </span>
                            </div>
                            
                            @if($restaurant->description)
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($restaurant->description, 120) }}
                                </p>
                            @endif
                            
                            <div class="restaurant-details mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <small class="text-muted">{{ $restaurant->address }}</small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-phone text-success me-2"></i>
                                    <small class="text-muted">{{ $restaurant->phone }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope text-info me-2"></i>
                                    <small class="text-muted">{{ $restaurant->email }}</small>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('restaurants.show', $restaurant) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-eye me-2"></i>Zobacz szczegóły
                                    </a>
                                    <a href="{{ route('reservations.create', ['restaurant_id' => $restaurant->id]) }}" 
                                       class="btn btn-success">
                                        <i class="fas fa-calendar-plus me-2"></i>Zarezerwuj stolik
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($restaurants->hasPages())
            <div class="row mt-5">
                <div class="col-12 d-flex justify-content-center">
                    {{ $restaurants->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
        
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-utensils text-muted mb-3" style="font-size: 4rem;"></i>
                    <h3 class="text-muted">Brak restauracji w systemie</h3>
                    <p class="text-muted">Sprawdź ponownie później lub skontaktuj się z administratorem.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Features Section -->
<div class="container-fluid bg-light py-5 mt-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="fas fa-clock text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Szybka rezerwacja</h5>
                        <p class="card-text text-muted">Zarezerwuj stolik w mniej niż 2 minuty</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="fas fa-shield-alt text-success mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Gwarancja jakości</h5>
                        <p class="card-text text-muted">Tylko sprawdzone i wysokiej jakości restauracje</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="fas fa-mobile-alt text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Mobilny dostęp</h5>
                        <p class="card-text text-muted">Zarządzaj rezerwacjami z każdego urządzenia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
@endpush