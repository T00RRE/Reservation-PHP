@extends('layouts.app')

@section('title', 'Panel Klienta')

@section('content')
<div class="modern-dashboard customer">
    <div class="dashboard-hero">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-12"> {{-- Zmieniono kolumny dla lepszej responsywności --}}
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-user"></i>
                            Panel Klienta
                        </div>
                        <h1 class="hero-title">
                            Witaj, {{ auth()->user()->name ?? 'Kliencie' }}! 👋
                        </h1>
                        <p class="hero-subtitle">
                            Zarządzaj swoimi rezerwacjami i odkrywaj nowe smaki.
                        </p>
                        <div class="hero-meta">
                            <span class="meta-item">
                                <i class="fas fa-calendar"></i>
                                Członek od {{ auth()->user()->created_at->format('M Y') ?? 'niedawna' }}
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-star"></i>
                                {{ $stats['total_reviews'] ?? 0 }} recenzji
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 mt-4 mt-lg-0"> {{-- Zmieniono kolumny i dodano margines na mniejszych ekranach --}}
                    <div class="hero-customer-card">
                        <div class="customer-avatar">
                            {{ substr(auth()->user()->name ?? 'K', 0, 1) }}
                        </div>
                        <div class="customer-level">
                            <div class="level-badge {{ ($stats['total_reservations'] ?? 0) >= 10 ? 'gold' : (($stats['total_reservations'] ?? 0) >= 5 ? 'silver' : 'bronze') }}">
                                @if(($stats['total_reservations'] ?? 0) >= 10)
                                    <i class="fas fa-crown"></i> VIP
                                @elseif(($stats['total_reservations'] ?? 0) >= 5)
                                    <i class="fas fa-medal"></i> Premium
                                @else
                                    <i class="fas fa-user"></i> Standard
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_reservations'] ?? 0 }}</div>
                    <div class="stat-label">Łączne rezerwacje</div>
                    <div class="stat-change">
                        <i class="fas fa-history"></i> Od początku
                    </div>
                </div>
                <div class="stat-visual">
                    <div class="progress-ring">
                        <div class="ring" style="--progress: {{ min(($stats['total_reservations'] ?? 0) * 10, 100) }}%"></div>
                        <div class="ring-center">{{ $stats['total_reservations'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['upcoming_reservations'] ?? 0 }}</div>
                    <div class="stat-label">Nadchodzące</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> W tym miesiącu
                    </div>
                </div>
                <div class="upcoming-indicator">
                    @if(($stats['upcoming_reservations'] ?? 0) > 0)
                        <div class="pulse-dot"></div>
                        <span>Aktywne</span>
                    @else
                        <span>Brak planów</span>
                    @endif
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_reviews'] ?? 0 }}</div>
                    <div class="stat-label">Recenzje</div>
                    <div class="stat-change">
                        <i class="fas fa-pen"></i> Wkrótce dostępne
                    </div>
                </div>
                <div class="review-stars">
                    @php
                        $avgRating = $stats['average_given_rating'] ?? 0;
                    @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $avgRating ? 'active' : '' }}"></i>
                    @endfor
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ ($favoriteRestaurants ?? collect())->count() }}</div>
                    <div class="stat-label">Polecane miejsca</div>
                    <div class="stat-change">
                        <i class="fas fa-bookmark"></i> Odkryj więcej
                    </div>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="content-card span-2">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-calendar-alt"></i>
                        Nadchodzące rezerwacje
                    </h3>
                    <div class="header-actions">
                        <a href="{{ route('reservations.create') }}" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Nowa rezerwacja
                        </a>
                    </div>
                </div>
                <div class="card-content">
                    @forelse($upcomingReservations ?? [] as $reservation)
                        <div class="reservation-card upcoming">
                            <div class="reservation-date">
                                <div class="date-day">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d') }}</div>
                                <div class="date-month">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M') }}</div>
                            </div>
                            <div class="reservation-details">
                                <div class="restaurant-name">{{ $reservation->restaurant->name ?? 'Restauracja' }}</div>
                                <div class="reservation-meta">
                                    <span class="time">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                                    </span>
                                    <span class="guests">
                                        <i class="fas fa-users"></i>
                                        {{ $reservation->guests_count }} os. {{-- Skrócono "osób" na "os." --}}
                                    </span>
                                    <span class="table">
                                        <i class="fas fa-chair"></i>
                                        Stolik {{ $reservation->table->table_number ?? '-' }}
                                    </span>
                                </div>
                                @if($reservation->special_requests)
                                    <div class="special-note">
                                        <i class="fas fa-comment"></i>
                                        {{ Str::limit($reservation->special_requests, 50) }} {{-- Ograniczono długość tekstu --}}
                                    </div>
                                @endif
                            </div>
                            <div class="reservation-status">
                                <div class="status-badge {{ $reservation->status }}">
                                    @switch($reservation->status)
                                        @case('pending')
                                            <i class="fas fa-clock"></i> Oczekuje
                                            @break
                                        @case('confirmed')
                                            <i class="fas fa-check-circle"></i> Potwierdzona
                                            @break
                                        @default
                                            {{ $reservation->status }}
                                    @endswitch
                                </div>
                                <div class="reservation-actions">
                                    <a href="{{ route('reservations.show', $reservation) }}" class="action-btn" aria-label="Zobacz szczegóły rezerwacji">
                                        <i class="fas fa-eye"></i>
                                        <span>Zobacz</span>
                                    </a>
                                    @if($reservation->canBeCancelled())
                                        <button class="action-btn cancel" onclick="cancelReservation({{ $reservation->id }})" aria-label="Anuluj rezerwację">
                                            <i class="fas fa-times"></i>
                                            <span>Anuluj</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-calendar-plus"></i>
                            <h4>Brak nadchodzących rezerwacji</h4>
                            <p>Czas zaplanować kolejną kulinarną przygodę!</p>
                            <a href="{{ route('restaurants.index') }}" class="btn-primary">
                                <i class="fas fa-search"></i>
                                Znajdź restaurację
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="content-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-heart"></i>
                        Polecane miejsca
                    </h3>
                </div>
                <div class="card-content">
                    @forelse($favoriteRestaurants ?? [] as $restaurant)
                        <div class="favorite-item">
                            <div class="restaurant-avatar">
                                {{ substr($restaurant->name, 0, 1) }}
                            </div>
                            <div class="restaurant-info">
                                <div class="restaurant-name">{{ $restaurant->name }}</div>
                                <div class="restaurant-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= ($restaurant->rating ?? 0) ? 'active' : '' }}"></i>
                                    @endfor
                                    <span>{{ number_format($restaurant->rating ?? 0, 1) }}</span>
                                </div>
                                <div class="restaurant-cuisine">{{ Str::limit($restaurant->address ?? 'Restauracja', 30) }}</div> {{-- Skrócono długość adresu --}}
                            </div>
                            <div class="restaurant-actions">
                                <a href="{{ route('restaurants.show', $restaurant) }}" class="action-btn primary" aria-label="Zobacz restaurację">
                                    <i class="fas fa-eye"></i>
                                    <span>Zobacz</span>
                                </a>
                                <a href="{{ route('reservations.create', ['restaurant_id' => $restaurant->id]) }}" class="action-btn success" aria-label="Zarezerwuj stolik">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span>Rezerwuj</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-favorites">
                            <i class="fas fa-heart-broken"></i>
                            <p>Nie masz jeszcze ulubionych restauracji.</p>
                            <a href="{{ route('restaurants.index') }}" class="btn-outline">Odkryj miejsca</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="content-card span-2">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-history"></i>
                        Ostatnie rezerwacje
                    </h3>
                </div>
                <div class="card-content">
                    <div class="activity-timeline">
                        @forelse($recentReservations ?? [] as $reservation)
                            <div class="timeline-item">
                                <div class="timeline-date">
                                    {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d.m') }}
                                </div>
                                <div class="timeline-dot {{ $reservation->status }}"></div>
                                <div class="timeline-content">
                                    <div class="activity-header">
                                        <span class="restaurant-name">{{ $reservation->restaurant->name ?? 'Restauracja' }}</span>
                                        <span class="activity-time">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}</span>
                                    </div>
                                    <div class="activity-details">
                                        {{ $reservation->guests_count }} os. • {{-- Skrócono "osób" na "os." --}}
                                        Stolik {{ $reservation->table->table_number ?? '-' }} •
                                        <span class="status-text {{ $reservation->status }}">
                                            {{ $reservation->status === 'completed' ? 'Zrealizowana' : ($reservation->status === 'cancelled' ? 'Anulowana' : 'Potwierdzona') }}
                                        </span>
                                    </div>
                                    @if($reservation->status === 'completed')
                                        <div class="review-prompt">
                                            <a href="#" onclick="showComingSoonAlert('Recenzje')" class="btn-review">
                                                <i class="fas fa-star"></i>
                                                Oceń wizytę
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-timeline">
                                <i class="fas fa-clock"></i>
                                <p>Brak historii rezerwacji.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-bolt"></i>
                        Szybkie akcje
                    </h3>
                </div>
                <div class="card-content">
                    <div class="quick-actions">
                        <a href="{{ route('reservations.create') }}" class="quick-action primary">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-label">Nowa rezerwacja</div>
                        </a>

                        <a href="{{ route('restaurants.index') }}" class="quick-action success">
                            <div class="action-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="action-label">Szukaj restauracji</div>
                        </a>

                        <a href="{{ route('reservations.index') }}" class="quick-action info">
                            <div class="action-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="action-label">Moje rezerwacje</div>
                        </a>

                        <a href="#" onclick="showComingSoonAlert('Recenzje')" class="quick-action warning">
                            <div class="action-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="action-label">Moje recenzje</div>
                        </a>

                        <a href="{{ route('profile.edit') }}" class="quick-action secondary">
                            <div class="action-icon">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <div class="action-label">Profil</div>
                        </a>

                        <a href="{{ route('restaurants.index') }}" class="quick-action danger">
                            <div class="action-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="action-label">Ulubione</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Customer Dashboard */
.modern-dashboard.customer {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 0;
}

/* Dashboard Hero Section */
.dashboard-hero {
    padding: 40px 0;
    color: white;
}

.hero-content {
    padding: 20px 0;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 16px;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 12px;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 24px;
    line-height: 1.5;
}

.hero-meta {
    display: flex;
    gap: 24px;
    margin-top: 16px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95rem;
}

.hero-customer-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 24px;
    text-align: center;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.customer-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 2rem;
    color: white;
    font-weight: 700;
}

.customer-level {
    margin-top: 16px;
}

.level-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
}

.level-badge.bronze {
    background: linear-gradient(45deg, #cd7f32, #daa520);
    color: white;
}

.level-badge.silver {
    background: linear-gradient(45deg, #c0c0c0, #e5e5e5);
    color: #333;
}

.level-badge.gold {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #333;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); /* Zmniejszono minimalną szerokość kolumny */
    gap: 20px; /* Zmniejszono odstępy */
    margin-bottom: 32px;
    padding: 0 20px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px; /* Zmniejszono padding */
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 15px; /* Zmniejszono odstępy */
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card.primary::before {
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.stat-card.success::before {
    background: linear-gradient(90deg, #38b2ac, #4fd1c7);
}

.stat-card.warning::before {
    background: linear-gradient(90deg, #d69e2e, #f6ad55);
}

.stat-card.info::before {
    background: linear-gradient(90deg, #3182ce, #63b3ed);
}

.stat-icon {
    width: 50px; /* Zmniejszono rozmiar ikony */
    height: 50px; /* Zmniejszono rozmiar ikony */
    border-radius: 10px; /* Nieco mniejszy promień */
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px; /* Zmniejszono rozmiar czcionki ikony */
    flex-shrink: 0;
}

.stat-card.primary .stat-icon {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.stat-card.success .stat-icon {
    background: linear-gradient(135deg, #38b2ac, #4fd1c7);
    color: white;
}

.stat-card.warning .stat-icon {
    background: linear-gradient(135deg, #d69e2e, #f6ad55);
    color: white;
}

.stat-card.info .stat-icon {
    background: linear-gradient(135deg, #3182ce, #63b3ed);
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.8rem; /* Nieco mniejsza czcionka */
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 2px; /* Zmniejszono margines */
}

.stat-label {
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
    color: #718096;
    font-weight: 600;
    margin-bottom: 6px; /* Zmniejszono margines */
}

.stat-change {
    font-size: 0.7rem; /* Zmniejszono rozmiar czcionki */
    display: flex;
    align-items: center;
    gap: 4px;
    color: #4a5568;
}

.stat-change.positive {
    color: #38a169;
}

.stat-visual {
    flex-shrink: 0;
}

.progress-ring {
    position: relative;
    width: 50px; /* Zmniejszono rozmiar */
    height: 50px; /* Zmniejszono rozmiar */
}

.ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3px solid rgba(102, 126, 234, 0.2); /* Zmniejszono grubość */
    border-top: 3px solid #667eea; /* Zmniejszono grubość */
    transition: transform 0.3s ease;
}

.ring[style*="--progress"] {
    transform: rotate(calc(var(--progress) * 3.6deg));
}

.ring-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-weight: bold;
    color: #667eea;
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
}

.upcoming-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px; /* Zmniejszono margines */
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
}

.pulse-dot {
    width: 8px;
    height: 8px;
    background: #38a169;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.review-stars {
    display: flex;
    gap: 3px; /* Zmniejszono odstępy */
    margin-top: 10px; /* Zmniejszono margines */
}

.review-stars .fa-star {
    color: #e2e8f0;
    transition: color 0.2s;
    font-size: 0.8rem; /* Zmniejszono rozmiar gwiazdek */
}

.review-stars .fa-star.active {
    color: #f6d55c;
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px; /* Zmniejszono odstępy */
    padding: 0 20px 40px;
}

.content-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.content-card.span-2 {
    grid-column: span 2;
}

.card-header {
    padding: 18px 20px 12px; /* Zmniejszono padding */
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
}

.card-header h3 {
    margin: 0;
    font-size: 1rem; /* Nieco mniejsza czcionka nagłówka */
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 8px;
}

.header-actions {
    display: flex;
    gap: 10px; /* Zmniejszono odstępy */
    align-items: center;
}

.btn-primary {
    padding: 8px 16px; /* Zmniejszono padding dla przycisku */
    font-size: 0.875rem; /* Zmniejszono rozmiar czcionki */
}

.card-content {
    padding: 20px; /* Zmniejszono padding */
}

/* Reservation Cards */
.reservation-card {
    background: #f7fafc;
    border-radius: 12px;
    padding: 16px; /* Zmniejszono padding */
    margin-bottom: 12px; /* Zmniejszono margines */
    display: flex;
    align-items: center;
    gap: 15px; /* Zmniejszono odstępy */
    border-left: 4px solid #e2e8f0;
    transition: all 0.3s;
}

.reservation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.reservation-card.upcoming {
    border-left-color: #38a169;
    background: #f0fff4;
}

.reservation-date {
    text-align: center;
    background: white;
    border-radius: 10px; /* Nieco mniejszy promień */
    padding: 10px; /* Zmniejszono padding */
    min-width: 55px; /* Zmniejszono minimalną szerokość */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.date-day {
    font-size: 1.3rem; /* Nieco mniejsza czcionka */
    font-weight: 700;
    color: #2d3748;
    line-height: 1;
}

.date-month {
    font-size: 0.7rem; /* Zmniejszono rozmiar czcionki */
    color: #718096;
    text-transform: uppercase;
    margin-top: 2px; /* Zmniejszono margines */
}

.reservation-details {
    flex: 1;
    min-width: 0; /* Dodane dla poprawnego obcinania tekstu */
}

.reservation-details .restaurant-name {
    font-weight: 600;
    color: #2d3748;
    font-size: 1rem; /* Nieco mniejsza czcionka */
    margin-bottom: 6px; /* Zmniejszono margines */
    white-space: nowrap; /* Zapobiega zawijaniu tekstu */
    overflow: hidden; /* Ukrywa nadmiarowy tekst */
    text-overflow: ellipsis; /* Dodaje "..." */
}

.reservation-meta {
    display: flex;
    gap: 12px; /* Zmniejszono odstępy */
    margin-bottom: 6px; /* Zmniejszono margines */
    flex-wrap: wrap;
}

.reservation-meta span {
    display: flex;
    align-items: center;
    gap: 3px; /* Zmniejszono odstępy */
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
    color: #718096;
}

.special-note {
    background: rgba(102, 126, 234, 0.1);
    padding: 6px 10px; /* Zmniejszono padding */
    border-radius: 6px;
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
    color: #4a5568;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px; /* Zmniejszono margines */
    white-space: nowrap; /* Zapobiega zawijaniu tekstu */
    overflow: hidden; /* Ukrywa nadmiarowy tekst */
    text-overflow: ellipsis; /* Dodaje "..." */
}

.reservation-status {
    display: flex;
    flex-direction: column;
    align-items: flex-end; /* Wyrównanie do prawej */
    gap: 10px; /* Zmniejszono odstępy */
}

.status-badge {
    padding: 5px 10px; /* Zmniejszono padding */
    border-radius: 16px; /* Nieco mniejszy promień */
    font-size: 0.7rem; /* Zmniejszono rozmiar czcionki */
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.status-badge.pending {
    background: #fed7d7;
    color: #c53030;
}

.status-badge.confirmed {
    background: #c6f6d5;
    color: #2f855a;
}

.reservation-actions {
    display: flex;
    gap: 6px; /* Zmniejszono odstępy */
}

.action-btn {
    width: 72px; /* Zmniejszono rozmiar przycisku */
    height: 32px; /* Zmniejszono rozmiar przycisku */
    border-radius: 6px; /* Nieco mniejszy promień */
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #e2e8f0;
    color: #4a5568;
    text-decoration: none;
    font-size: 0.75rem; /* Zmniejszono rozmiar czcionki */
    padding: 0 6px; /* Dostosowano padding */
    gap: 3px; /* Mniejszy odstęp między ikoną a tekstem */
}

.action-btn:hover {
    transform: translateY(-1px);
    background: #cbd5e0;
}

.action-btn span { /* Ukrycie tekstu dla małych ekranów, jeśli jest potrzeba */
    display: none;
}
@media (min-width: 768px) { /* Pokaż tekst na większych ekranach */
    .action-btn span {
        display: inline;
    }
}


.action-btn.cancel {
    background: #fed7d7;
    color: #c53030;
}

.action-btn.primary {
    background: #bee3f8;
    color: #2b6cb0;
}

.action-btn.success {
    background: #c6f6d5;
    color: #2f855a;
}

/* Favorite Items */
.favorite-item {
    display: flex;
    align-items: center;
    gap: 10px; /* Zmniejszono odstępy */
    padding: 12px; /* Zmniejszono padding */
    background: #f7fafc;
    border-radius: 10px; /* Nieco mniejszy promień */
    margin-bottom: 10px; /* Zmniejszono margines */
    transition: all 0.3s;
}

.favorite-item:hover {
    background: #edf2f7;
    transform: translateY(-1px);
}

.restaurant-avatar {
    width: 40px; /* Zmniejszono rozmiar */
    height: 40px; /* Zmniejszono rozmiar */
    border-radius: 10px; /* Nieco mniejszy promień */
    background: linear-gradient(45deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.1rem; /* Zmniejszono rozmiar czcionki */
}

.restaurant-info {
    flex: 1;
    min-width: 0; /* Dodane dla poprawnego obcinania tekstu */
}

.favorite-item .restaurant-name { /* Specyficzna selekcja by nie nadpisać ogólnego */
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 2px; /* Zmniejszono margines */
    font-size: 0.95rem; /* Zmniejszono rozmiar czcionki */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.restaurant-rating {
    display: flex;
    align-items: center;
    gap: 3px; /* Zmniejszono odstępy */
    margin-bottom: 2px;
}

.restaurant-rating .fa-star {
    font-size: 0.7rem; /* Zmniejszono rozmiar gwiazdek */
    color: #e2e8f0;
}

.restaurant-rating .fa-star.active {
    color: #f6d55c;
}

.restaurant-rating span {
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
    color: #718096;
    margin-left: 2px; /* Zmniejszono margines */
}

.restaurant-cuisine {
    font-size: 0.7rem; /* Zmniejszono rozmiar czcionki */
    color: #a0aec0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.favorite-item .restaurant-actions { /* Specyficzna selekcja */
    display: flex;
    flex-direction: column; /* Ułożenie przycisków pionowo */
    gap: 6px; /* Zmniejszono odstępy */
    align-items: flex-end; /* Wyrównanie do prawej */
}

.empty-favorites {
    text-align: center;
    padding: 30px 15px; /* Zmniejszono padding */
    color: #718096;
}

.empty-favorites i {
    font-size: 1.8rem; /* Zmniejszono rozmiar ikony */
    color: #cbd5e0;
    margin-bottom: 10px; /* Zmniejszono margines */
}

.empty-favorites p {
    margin-bottom: 12px; /* Zmniejszono margines */
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
}

.btn-outline {
    padding: 6px 14px; /* Zmniejszono padding */
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
}

/* Activity Timeline */
.activity-timeline {
    display: flex;
    flex-direction: column;
    gap: 15px; /* Zmniejszono odstępy */
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    gap: 12px; /* Zmniejszono odstępy */
    position: relative;
}

.timeline-date {
    font-weight: 700;
    color: #2d3748;
    min-width: 45px; /* Nieco mniejsza szerokość */
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
    padding-top: 4px;
}

.timeline-dot {
    width: 10px; /* Zmniejszono rozmiar */
    height: 10px; /* Zmniejszono rozmiar */
    border-radius: 50%;
    margin-top: 6px; /* Zmniejszono margines */
    flex-shrink: 0;
}

.timeline-dot.completed {
    background: #38a169;
    box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.2); /* Zmniejszono cień */
}

.timeline-dot.cancelled {
    background: #e53e3e;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.2);
}

.timeline-dot.confirmed {
    background: #3182ce;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.2);
}

.timeline-content {
    flex: 1;
    background: #f7fafc;
    padding: 14px; /* Zmniejszono padding */
    border-radius: 10px; /* Nieco mniejszy promień */
    border-left: 3px solid #e2e8f0;
}

.timeline-item .timeline-content {
    border-left-color: #38a169;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px; /* Zmniejszono margines */
}

.timeline-content .restaurant-name { /* Specyficzna selekcja */
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9rem; /* Zmniejszono rozmiar czcionki */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.activity-time {
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
    color: #718096;
    flex-shrink: 0; /* Zapobiega zmniejszaniu się */
    margin-left: 8px; /* Odstęp od nazwy restauracji */
}

.activity-details {
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
    color: #718096;
    margin-bottom: 10px; /* Zmniejszono margines */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.status-text.completed {
    color: #38a169;
    font-weight: 600;
}

.status-text.cancelled {
    color: #e53e3e;
    font-weight: 600;
}

.status-text.confirmed {
    color: #3182ce;
    font-weight: 600;
}

.review-prompt {
    margin-top: 10px; /* Zmniejszono margines */
}

.btn-review {
    padding: 7px 14px; /* Zmniejszono padding */
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
}

.empty-timeline {
    text-align: center;
    padding: 30px 15px; /* Zmniejszono padding */
    color: #718096;
}

.empty-timeline i {
    font-size: 1.8rem; /* Zmniejszono rozmiar ikony */
    color: #cbd5e0;
    margin-bottom: 10px; /* Zmniejszono margines */
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); /* Dostosowano kolumny dla małych ekranów */
    gap: 12px; /* Zmniejszono odstępy */
}

.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px; /* Zmniejszono padding */
    border-radius: 10px; /* Nieco mniejszy promień */
    text-decoration: none;
    transition: all 0.3s;
    text-align: center;
}

.quick-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.quick-action .action-icon {
    font-size: 1.5rem; /* Nieco mniejsza ikona */
    margin-bottom: 8px; /* Zmniejszono margines */
}

.quick-action .action-label {
    font-size: 0.8rem; /* Zmniejszono rozmiar czcionki */
    font-weight: 600;
    line-height: 1.3; /* Lepsze odstępy dla dwulinijkowych etykiet */
}


.quick-action.primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
.quick-action.success { background: linear-gradient(135deg, #38b2ac, #4fd1c7); color: white; }
.quick-action.info { background: linear-gradient(135deg, #3182ce, #63b3ed); color: white; }
.quick-action.warning { background: linear-gradient(135deg, #d69e2e, #f6ad55); color: white; }
.quick-action.danger { background: linear-gradient(135deg, #e53e3e, #fc8181); color: white; }
.quick-action.secondary {
    background: #f7fafc;
    color: #4a5568;
    border: 2px solid #e2e8f0; /* Dodane border, którego brakowało */
}
.quick-action.secondary:hover {
    background: #edf2f7;
    color: #2d3748;
}

/* Responsywność */
@media (max-width: 991px) { /* Tablet i niżej */
    .content-grid {
        grid-template-columns: 1fr; /* Wszystkie kolumny pod sobą */
    }
    .content-card.span-2 {
        grid-column: span 1; /* span 2 nie ma już sensu */
    }
    .dashboard-hero .row {
        flex-direction: column; /* Ułożenie elementów w kolumnie */
        text-align: center;
    }
    .hero-content, .hero-customer-card {
        padding: 15px 0; /* Zmniejszony padding */
    }
    .hero-customer-card {
        margin-top: 20px; /* Odstęp między sekcjami */
    }
    .hero-title {
        font-size: 2rem; /* Mniejszy tytuł na małych ekranach */
    }
    .hero-subtitle {
        font-size: 1rem; /* Mniejszy podtytuł */
    }
    .hero-meta {
        justify-content: center; /* Wyśrodkowanie na mniejszych ekranach */
        gap: 15px; /* Mniejsze odstępy */
    }
    .stat-card {
        flex-direction: column; /* Elementy w stat-card jeden pod drugim */
        text-align: center;
        gap: 10px; /* Mniejsze odstępy */
    }
    .stat-content {
        order: 2; /* Kolejność elementów, żeby ikona była u góry */
    }
    .stat-icon {
        order: 1;
    }
    .stat-visual {
        order: 3;
    }
    .upcoming-indicator, .review-stars {
        justify-content: center; /* Wyśrodkowanie */
    }
}

@media (max-width: 767px) { /* Telefon i niżej */
    .stats-grid {
        grid-template-columns: 1fr; /* Jeden stat-card na wiersz */
        padding: 0 15px; /* Zmniejszony padding boczny */
    }
    .content-grid {
        padding: 0 15px 30px; /* Zmniejszony padding boczny */
    }
    .card-header {
        flex-direction: column; /* Elementy w nagłówku jeden pod drugim */
        align-items: flex-start; /* Wyrównanie do lewej */
        gap: 10px;
    }
    .header-actions {
        width: 100%; /* Rozciągnięcie przycisku na całą szerokość */
        justify-content: flex-start;
    }
    .btn-primary {
        width: 100%; /* Rozciągnięcie przycisku */
        text-align: center;
    }
    .reservation-card {
        flex-direction: column; /* Elementy rezerwacji jeden pod drugim */
        align-items: flex-start;
        gap: 10px;
        padding: 12px; /* Dalsze zmniejszenie paddingu */
    }
    .reservation-date {
        align-self: flex-start; /* Wyrównanie daty do lewej */
        min-width: unset; /* Usunięcie minimalnej szerokości */
        padding: 8px; /* Dalsze zmniejszenie paddingu */
    }
    .reservation-details {
        width: 100%; /* Zajmuje całą szerokość */
    }
    .reservation-meta {
        flex-wrap: wrap; /* Zawijanie elementów meta */
        gap: 8px; /* Mniejsze odstępy */
    }
    .reservation-status {
        width: 100%;
        align-items: flex-start; /* Wyrównanie statusu i akcji do lewej */
    }
    .favorite-item {
        flex-direction: column; /* Ulubione elementy jeden pod drugim */
        align-items: flex-start;
        padding: 10px; /* Dalsze zmniejszenie paddingu */
    }
    .favorite-item .restaurant-actions {
        width: 100%;
        flex-direction: row; /* Przyciski znowu obok siebie */
        justify-content: space-around; /* Rozłożenie na szerokość */
        margin-top: 10px;
    }
    .quick-actions {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); /* Dalsze dostosowanie dla małych telefonów */
        gap: 8px;
    }
    .action-btn {
        width: auto; /* Pozwól na automatyczną szerokość */
        padding: 8px 10px; /* Większy padding dla łatwiejszego klikania */
        font-size: 0.8rem;
    }
    .action-btn span {
        display: inline; /* Pokaż tekst na telefonach */
    }
    .timeline-date {
        min-width: 40px; /* Dalsze zmniejszenie min-width */
    }
}


/* General utilities */
.container-fluid {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-lg-8, .col-lg-4, .col-md-12 {
    position: relative;
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
}

@media (min-width: 768px) {
    .col-md-12 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 992px) {
    .col-lg-8 {
        flex: 0 0 66.666667%;
        max-width: 66.666667%;
    }
    .col-lg-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
    .mt-lg-0 {
        margin-top: 0 !important;
    }
}


/* Animations */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(56, 161, 105, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(56, 161, 105, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(56, 161, 105, 0);
    }
}

/* Global Button Styles (if not already defined in layouts/app.blade.php) */
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #667eea;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease, transform 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-primary:hover {
    background: #764ba2;
    transform: translateY(-1px);
    color: white;
    text-decoration: none;
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    color: #667eea;
    border: 2px solid #667eea;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
    transform: translateY(-1px);
    text-decoration: none;
}

/* Helper for "Coming Soon" alerts */
.swal2-popup {
    font-family: inherit; /* Upewnij się, że SweetAlert2 używa czcionki z aplikacji */
}

.swal2-title {
    font-size: 1.5rem !important;
}

.swal2-content {
    font-size: 1rem !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function cancelReservation(reservationId) {
        Swal.fire({
            title: 'Czy na pewno chcesz anulować?',
            text: "Tej operacji nie będzie można cofnąć!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Tak, anuluj!',
            cancelButtonText: 'Nie, zostaw'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tutaj powinien być kod do wysłania żądania anulowania rezerwacji (np. AJAX)
                // Przykład (zakładając, że masz trasę 'reservations.cancel'):
                // fetch(`/reservations/${reservationId}/cancel`, {
                //     method: 'POST',
                //     headers: {
                //         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                //         'Content-Type': 'application/json'
                //     }
                // })
                // .then(response => response.json())
                // .then(data => {
                //     if(data.success) {
                //         Swal.fire('Anulowano!', 'Twoja rezerwacja została anulowana.', 'success');
                //         location.reload(); // Odśwież stronę po sukcesie
                //     } else {
                //         Swal.fire('Błąd!', data.message || 'Nie udało się anulować rezerwacji.', 'error');
                //     }
                // })
                // .catch(error => {
                //     Swal.fire('Błąd sieci!', 'Spróbuj ponownie później.', 'error');
                // });

                // Na potrzeby demo:
                Swal.fire(
                    'Anulowano!',
                    'Twoja rezerwacja została anulowana (symulacja).',
                    'success'
                ).then(() => {
                    location.reload();
                });
            }
        });
    }

    function showComingSoonAlert(featureName) {
        Swal.fire({
            title: 'Już wkrótce!',
            text: `${featureName} będzie dostępne w przyszłej aktualizacji. Dziękujemy za cierpliwość!`,
            icon: 'info',
            confirmButtonText: 'Rozumiem'
        });
    }
</script>
@endsection