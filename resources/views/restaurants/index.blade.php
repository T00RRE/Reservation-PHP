@extends('layouts.app')

@section('title', 'Odkryj Restauracje - RestaurantBook')

@section('content')
<div class="restaurants-modern">
    <div class="hero-search">
        <div class="hero-background">
            <div class="hero-overlay"></div>
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="title-main">Odkryj najlepsze</span>
                    <span class="title-highlight">restauracje</span>
                    <span class="title-sub">w Twojej okolicy</span>
                </h1>
                <p class="hero-subtitle">
                    Zarezerwuj stolik w wyjątkowych miejscach. Ponad {{ $restaurants->total() }} restauracji czeka na Ciebie!
                </p>
                
                <form method="GET" action="{{ route('restaurants.index') }}" class="search-form">
                    <div class="search-container">
                        <div class="search-field">
                            <div class="search-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   placeholder="Szukaj restauracji, kuchni, lokalizacji..."
                                   value="{{ request('search') }}"
                                   class="search-input">
                        </div>
                        
                        <div class="filter-field">
                            <select name="rating" class="filter-select">
                                <option value="">Wszystkie oceny</option>
                                <option value="4.5" {{ request('rating') == '4.5' ? 'selected' : '' }}>4.5+ ⭐</option>
                                <option value="4.0" {{ request('rating') == '4.0' ? 'selected' : '' }}>4.0+ ⭐</option>
                                <option value="3.5" {{ request('rating') == '3.5' ? 'selected' : '' }}>3.5+ ⭐</option>
                            </select>
                        </div>
                        
                        <div class="filter-field">
                            <select name="cuisine" class="filter-select">
                                <option value="">Rodzaj kuchni</option>
                                <option value="włoska" {{ request('cuisine') == 'włoska' ? 'selected' : '' }}>Włoska</option>
                                <option value="japońska" {{ request('cuisine') == 'japońska' ? 'selected' : '' }}>Japońska</option>
                                <option value="polska" {{ request('cuisine') == 'polska' ? 'selected' : '' }}>Polska</option>
                                <option value="indyjska" {{ request('cuisine') == 'indyjska' ? 'selected' : '' }}>Indyjska</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                            <span>Szukaj</span>
                        </button>
                    </div>
                    
                    @if(request()->hasAny(['search', 'rating', 'cuisine']))
                        <div class="active-filters">
                            <span class="filters-label">Aktywne filtry:</span>
                            @if(request('search'))
                                <span class="filter-tag">
                                    <i class="fas fa-search"></i>
                                    "{{ request('search') }}"
                                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('search')) }}" class="remove-filter">×</a>
                                </span>
                            @endif
                            @if(request('rating'))
                                <span class="filter-tag">
                                    <i class="fas fa-star"></i>
                                    {{ request('rating') }}+ gwiazdek
                                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('rating')) }}" class="remove-filter">×</a>
                                </span>
                            @endif
                            @if(request('cuisine'))
                                <span class="filter-tag">
                                    <i class="fas fa-utensils"></i>
                                    {{ request('cuisine') }}
                                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('cuisine')) }}" class="remove-filter">×</a>
                                </span>
                            @endif
                            <a href="{{ route('restaurants.index') }}" class="clear-all">
                                <i class="fas fa-times"></i>
                                Wyczyść wszystkie
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    @if(isset($topRestaurants) && $topRestaurants->count() > 0)
    <div class="top-restaurants">
        <div class="container">
            <div class="section-header">
                <h2>
                    <i class="fas fa-crown"></i>
                    Najlepiej oceniane
                </h2>
                <p>Sprawdzone miejsca z najwyższymi ocenami</p>
            </div>
            
            <div class="top-grid">
                @foreach($topRestaurants as $index => $restaurant)
                    <div class="top-card rank-{{ $index + 1 }}">
                        <div class="rank-badge">
                            @if($index === 0)
                                <i class="fas fa-crown"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <div class="restaurant-image">
                            <div class="image-placeholder">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="rating-overlay">
                                <span class="rating-value">{{ number_format($restaurant->rating, 1) }}</span>
                                <div class="stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $restaurant->rating ? 'active' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="top-card-content">
                            <h3>{{ $restaurant->name }}</h3>
                            <p>{{ Str::limit($restaurant->address, 50) }}</p>
                            <div class="card-actions">
                                <a href="{{ route('restaurants.show', $restaurant) }}" class="btn-view">
                                    <i class="fas fa-eye"></i>
                                    Zobacz
                                </a>
                                <a href="{{ route('reservations.create', ['restaurant_id' => $restaurant->id]) }}" class="btn-reserve">
                                    <i class="fas fa-calendar-plus"></i>
                                    Rezerwuj
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="results-section">
        <div class="container">
            <div class="results-header">
                <div class="results-info">
                    <h2>
                        @if(request()->hasAny(['search', 'rating', 'cuisine']))
                            Wyniki wyszukiwania
                            <span class="results-count">({{ $restaurants->total() }} {{ $restaurants->total() == 1 ? 'restauracja' : ($restaurants->total() <= 4 ? 'restauracje' : 'restauracji') }})</span>
                        @else
                            Wszystkie restauracje
                            <span class="results-count">({{ $restaurants->total() }} {{ $restaurants->total() == 1 ? 'restauracja' : ($restaurants->total() <= 4 ? 'restauracje' : 'restauracji') }})</span>
                        @endif
                    </h2>
                    @if(request('search'))
                        <p class="search-query">
                            Szukasz: <strong>"{{ request('search') }}"</strong>
                        </p>
                    @endif
                </div>
                
                <div class="view-options">
                    <button class="view-btn active" data-view="grid">
                        <i class="fas fa-th"></i>
                        Siatka
                    </button>
                    <button class="view-btn" data-view="list">
                        <i class="fas fa-list"></i>
                        Lista
                    </button>
                </div>
            </div>

            @if($restaurants->count() > 0)
                <div class="restaurants-grid" id="restaurants-container">
                    @foreach($restaurants as $restaurant)
                        <div class="restaurant-card">
                            <div class="card-image">
                                <div class="image-placeholder">
                                    <i class="fas fa-concierge-bell"></i>
                                </div>
                                <div class="card-badges">
                                    @if($restaurant->rating >= 4.5)
                                        <span class="badge premium">
                                            <i class="fas fa-star"></i>
                                            Premium
                                        </span>
                                    @endif
                                    <span class="badge rating">
                                        <i class="fas fa-star"></i>
                                        {{ number_format($restaurant->rating, 1) }}
                                    </span>
                                </div>
                                <div class="quick-actions">
                                    <button class="quick-btn favorite" title="Dodaj do ulubionych">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="quick-btn share" title="Udostępnij">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-content">
                                <div class="restaurant-header">
                                    <h3 class="restaurant-name">{{ $restaurant->name }}</h3>
                                    <div class="restaurant-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $restaurant->rating ? 'active' : '' }}"></i>
                                        @endfor
                                        <span class="rating-text">{{ number_format($restaurant->rating, 1) }}</span>
                                    </div>
                                </div>
                                
                                @if($restaurant->description)
                                    <p class="restaurant-description">
                                        {{ Str::limit($restaurant->description, 120) }}
                                    </p>
                                @endif
                                
                                <div class="restaurant-details">
                                    <div class="detail-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ Str::limit($restaurant->address, 40) }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-phone"></i>
                                        <span>{{ $restaurant->phone }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span>Codziennie 12:00 - 22:00</span>
                                    </div>
                                </div>
                                
                                <div class="card-actions">
                                    <a href="{{ route('restaurants.show', $restaurant) }}" class="btn-secondary">
                                        <i class="fas fa-info-circle"></i>
                                        Szczegóły
                                    </a>
                                    <a href="{{ route('reservations.create', ['restaurant_id' => $restaurant->id]) }}" class="btn-primary">
                                        <i class="fas fa-calendar-plus"></i>
                                        Zarezerwuj
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($restaurants->hasPages())
                    <div class="pagination-wrapper">
                        {{ $restaurants->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @endif
                
            @else
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Nie znaleziono restauracji</h3>
                    <p>
                        @if(request()->hasAny(['search', 'rating', 'cuisine']))
                            Spróbuj zmienić kryteria wyszukiwania lub 
                            <a href="{{ route('restaurants.index') }}">przeglądaj wszystkie restauracje</a>
                        @else
                            W tej chwili nie ma dostępnych restauracji w systemie.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'rating', 'cuisine']))
                        <a href="{{ route('restaurants.index') }}" class="btn-primary">
                            <i class="fas fa-refresh"></i>
                            Pokaż wszystkie restauracje
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Modern Restaurants Dashboard */
.restaurants-modern {
    background: #f8fafc;
    min-height: 100vh;
}

/* Hero Section */
.hero-search {
    position: relative;
    padding: 120px 0 80px;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}

.hero-pattern {
    position: absolute;
    inset: 0;
    background-image: 
        radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
}

.hero-content {
    position: relative;
    z-index: 10;
    text-align: center;
    color: white;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 24px;
    line-height: 1.1;
}

.title-main {
    display: block;
    opacity: 0.9;
}

.title-highlight {
    display: block;
    background: linear-gradient(45deg, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.title-sub {
    display: block;
    font-size: 2.5rem;
    opacity: 0.8;
}

.hero-subtitle {
    font-size: 1.25rem;
    margin-bottom: 48px;
    opacity: 0.9;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

/* Search Form */
.search-form {
    max-width: 900px;
    margin: 0 auto;
}

.search-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    display: grid;
    grid-template-columns: 1fr auto auto auto;
    gap: 16px;
    align-items: center;
}

.search-field {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-size: 1.1rem;
    z-index: 10;
}

.search-input {
    width: 100%;
    padding: 16px 16px 16px 48px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    background: white;
    transition: all 0.3s;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-select {
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    background: white;
    font-size: 0.9rem;
    min-width: 150px;
    cursor: pointer;
    transition: all 0.3s;
}

.filter-select:focus {
    outline: none;
    border-color: #667eea;
}

.search-btn {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 16px 32px;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s;
    white-space: nowrap;
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

/* Active Filters */
.active-filters {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.filters-label {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.9rem;
}

.filter-tag {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.remove-filter {
    color: white;
    text-decoration: none;
    margin-left: 6px;
    font-weight: bold;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.remove-filter:hover {
    opacity: 1;
}

.clear-all {
    background: rgba(239, 68, 68, 0.2);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
}

.clear-all:hover {
    background: rgba(239, 68, 68, 0.3);
    color: white;
    text-decoration: none;
}

/* Top Restaurants */
.top-restaurants {
    padding: 80px 0;
    background: white;
}

.section-header {
    text-align: center;
    margin-bottom: 48px;
}

.section-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.section-header h2 i {
    color: #fbbf24;
}

.section-header p {
    font-size: 1.1rem;
    color: #6b7280;
}

.top-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 32px;
    max-width: 1000px;
    margin: 0 auto;
}

.top-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.4s ease;
    position: relative;
}

.top-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.top-card.rank-1 {
    border: 3px solid #fbbf24;
}

.top-card.rank-2 {
    border: 3px solid #d1d5db;
}

.top-card.rank-3 {
    border: 3px solid #cd7f32;
}

.rank-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    z-index: 10;
}

.rank-1 .rank-badge {
    background: linear-gradient(45deg, #fbbf24, #f59e0b);
}

.rank-2 .rank-badge {
    background: linear-gradient(45deg, #d1d5db, #9ca3af);
}

.rank-3 .rank-badge {
    background: linear-gradient(45deg, #cd7f32, #a0522d);
}

.restaurant-image {
    height: 200px;
    background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.image-placeholder {
    font-size: 3rem;
    color: #9ca3af;
}

.rating-overlay {
    position: absolute;
    bottom: 16px;
    right: 16px;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    color: white;
    padding: 8px 12px;
    border-radius: 12px;
    text-align: center;
}

.rating-value {
    font-weight: bold;
    font-size: 1.1rem;
    display: block;
    margin-bottom: 4px;
}

.stars {
    display: flex;
    gap: 2px;
}

.stars .fa-star {
    font-size: 0.8rem;
    color: #4b5563;
}

.stars .fa-star.active {
    color: #fbbf24;
}

.top-card-content {
    padding: 24px;
}

.top-card-content h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.top-card-content p {
    color: #6b7280;
    margin-bottom: 20px;
}

.card-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.btn-view {
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    text-decoration: none;
    color: #4b5563;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-view:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
    text-decoration: none;
}

.btn-reserve {
    padding: 12px 16px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-reserve:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

/* Results Section */
.results-section {
    padding: 60px 0 80px;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 20px;
}

.results-info h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.results-count {
    color: #6b7280;
    font-weight: 400;
}

.search-query {
    color: #667eea;
    font-size: 1.1rem;
    margin: 0;
}

.view-options {
    display: flex;
    gap: 8px;
}

.view-btn {
    padding: 10px 16px;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}

.view-btn.active,
.view-btn:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

/* Restaurant Grid */
.restaurants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 32px;
}

.restaurant-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s ease;
}

.restaurant-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.card-image {
    height: 220px;
    background: linear-gradient(45deg, #f8fafc, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.card-image .image-placeholder {
    font-size: 4rem;
    color: #cbd5e0;
    transition: all 0.3s;
}

.restaurant-card:hover .image-placeholder {
    transform: scale(1.1);
    color: #a0aec0;
}

.card-badges {
    position: absolute;
    top: 16px;
    left: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    backdrop-filter: blur(10px);
}

.badge.premium {
    background: rgba(251, 191, 36, 0.9);
    color: white;
}

.badge.rating {
    background: rgba(0, 0, 0, 0.7);
    color: white;
}

.quick-actions {
    position: absolute;
    top: 16px;
    right: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    opacity: 0;
    transform: translateX(20px);
    transition: all 0.3s;
}

.restaurant-card:hover .quick-actions {
    opacity: 1;
    transform: translateX(0);
}

.quick-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.9);
    color: #4b5563;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-btn:hover {
    background: white;
    color: #667eea;
    transform: scale(1.1);
}

.card-content {
    padding: 24px;
}

.restaurant-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.restaurant-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    flex: 1;
}

.restaurant-rating {
    display: flex;
    align-items: center;
    gap: 4px;
}

.restaurant-rating .fa-star {
    font-size: 0.9rem;
    color: #d1d5db; /* Default star color */
}

.restaurant-rating .fa-star.active {
    color: #fbbf24; /* Active star color */
}

.rating-text {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
}

.restaurant-description {
    font-size: 0.95rem;
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 20px;
    min-height: 45px; /* Ensures consistent height for cards without descriptions */
}

.restaurant-details {
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4b5563;
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item i {
    color: #667eea;
}

.btn-secondary {
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    text-decoration: none;
    color: #4b5563;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-secondary:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
    text-decoration: none;
}

.btn-primary {
    padding: 12px 16px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    max-width: 600px;
    margin: 40px auto;
}

.no-results-icon {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 24px;
}

.no-results h3 {
    font-size: 1.8rem;
    color: #1f2937;
    margin-bottom: 16px;
}

.no-results p {
    font-size: 1.1rem;
    color: #6b7280;
    margin-bottom: 32px;
}

.no-results p a {
    color: #667eea;
    text-decoration: underline;
}

.no-results .btn-primary {
    display: inline-flex;
    margin-top: 20px;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 40px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    border-radius: 0.25rem;
}

.page-item {
    margin: 0 5px;
}

.page-item .page-link {
    position: relative;
    display: block;
    padding: 0.75rem 1rem;
    color: #667eea;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.3s;
    text-decoration: none;
}

.page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #667eea;
    border-color: #667eea;
}

.page-item .page-link:hover {
    z-index: 2;
    color: #fff;
    background-color: #764ba2;
    border-color: #764ba2;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .search-container {
        grid-template-columns: 1fr 1fr;
    }
    .search-btn {
        grid-column: span 2;
        width: 100%;
        justify-content: center;
    }
    .filter-field {
        min-width: unset;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.8rem;
    }
    .title-sub {
        font-size: 1.8rem;
    }
    .hero-subtitle {
        font-size: 1rem;
    }

    .search-container {
        grid-template-columns: 1fr;
    }
    .search-btn {
        grid-column: span 1;
    }

    .top-grid {
        grid-template-columns: 1fr;
    }

    .results-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .view-options {
        width: 100%;
        justify-content: center;
    }

    .restaurants-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .hero-search {
        padding: 80px 0 60px;
    }
    .hero-title {
        font-size: 2.2rem;
    }
    .title-sub {
        font-size: 1.5rem;
    }
    .search-input {
        padding: 12px 12px 12px 40px;
    }
    .filter-select {
        padding: 12px;
    }
    .search-btn {
        padding: 12px 24px;
    }
    .active-filters {
        flex-direction: column;
        align-items: flex-start;
    }
    .filter-tag, .clear-all {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection