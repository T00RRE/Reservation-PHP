@extends('layouts.app')

@section('title', 'Panel Managera - ' . ($restaurant->name ?? 'Restauracja'))

@section('content')
<div class="modern-dashboard manager">
    <!-- Hero Header -->
    <div class="dashboard-hero">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-store"></i>
                            Manager Restauracji
                        </div>
                        <h1 class="hero-title">
                            {{ $restaurant->name ?? 'Twoja Restauracja' }} 🍽️
                        </h1>
                        <p class="hero-subtitle">
                            Zarządzaj swoją restauracją i twórz niezapomniane doświadczenia kulinarne
                        </p>
                        <div class="hero-meta">
                            <span class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $restaurant->address ?? 'Brak adresu' }}
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-star"></i>
                                {{ number_format($restaurant->rating ?? 0, 1) }}/5.0
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-restaurant-card">
                        <div class="restaurant-image">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="restaurant-status">
                            <div class="status-indicator active"></div>
                            <span>Otwarte</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="container-fluid">
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-chair"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_tables'] ?? 0 }}</div>
                    <div class="stat-label">Stoliki</div>
                    <div class="stat-change">
                        <i class="fas fa-info-circle"></i> Łączna pojemność
                    </div>
                </div>
                <div class="stat-visual">
                    <div class="capacity-rings">
                        <div class="ring" style="--progress: 75%"></div>
                        <div class="ring-center">{{ $stats['total_tables'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['todays_reservations'] ?? 0 }}</div>
                    <div class="stat-label">Dzisiaj</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> +{{ rand(5, 15) }}% vs wczoraj
                    </div>
                </div>
                <div class="today-timeline">
                    <div class="timeline-bar">
                        
                    </div>
                    <div class="timeline-labels">
                        <span>12:00</span>
                        <span>18:00</span>
                        <span>22:00</span>
                    </div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['pending_reservations'] ?? 0 }}</div>
                    <div class="stat-label">Oczekuje</div>
                    <div class="stat-change">
                        <i class="fas fa-exclamation-triangle"></i> Wymagają akcji
                    </div>
                </div>
                @if(($stats['pending_reservations'] ?? 0) > 0)
                    <div class="pending-indicator">
                        <div class="urgent-pulse"></div>
                        <span>Pilne!</span>
                    </div>
                @endif
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['average_rating'] ?? 0, 1) }}</div>
                    <div class="stat-label">Średnia ocena</div>
                    <div class="stat-change">
                        <i class="fas fa-heart"></i> Od zadowolonych gości
                    </div>
                </div>
                <div class="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= ($stats['average_rating'] ?? 0) ? 'active' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Today's Reservations -->
            <div class="content-card span-2">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-calendar-check"></i>
                        Dzisiejsze rezerwacje
                    </h3>
                    <div class="header-actions">
                        <div class="time-filter">
                            <span class="current-time">{{ now()->format('H:i') }}</span>
                            <div class="live-dot"></div>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    @if(isset($todaysReservations) && $todaysReservations->count() > 0)
                        <div class="reservations-timeline">
                            @foreach($todaysReservations as $reservation)
                                <div class="timeline-item {{ $reservation->status }}">
                                    <div class="timeline-time">
                                        {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                                    </div>
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="reservation-header">
                                            <div class="guest-info">
                                                <div class="guest-avatar">
                                                    {{ substr($reservation->user->name ?? 'G', 0, 1) }}
                                                </div>
                                                <div class="guest-details">
                                                    <div class="guest-name">{{ $reservation->user->name ?? 'Gość' }}</div>
                                                    <div class="guest-meta">
                                                        <span>{{ $reservation->guests_count }} osób</span>
                                                        <span>•</span>
                                                        <span>Stolik {{ $reservation->table->table_number ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="reservation-status-badge {{ $reservation->status }}">
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
                                        </div>
                                        @if($reservation->special_requests)
                                            <div class="special-requests">
                                                <i class="fas fa-comment"></i>
                                                {{ $reservation->special_requests }}
                                            </div>
                                        @endif
                                        <div class="timeline-actions">
                                            @if($reservation->status === 'pending')
                                                <form action="{{ route('reservations.confirm', $reservation) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="action-btn confirm">
                                                        <i class="fas fa-check"></i>
                                                        Potwierdź
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('reservations.show', $reservation) }}" class="action-btn view">
                                                <i class="fas fa-eye"></i>
                                                Szczegóły
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h4>Brak rezerwacji na dziś</h4>
                            <p>Gdy pojawią się rezerwacje, zobaczysz je w tej linii czasu</p>
                            <a href="{{ route('reservations.create') }}" class="btn-primary">
                                <i class="fas fa-plus"></i>
                                Dodaj rezerwację
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Management -->
            <div class="content-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-tools"></i>
                        Zarządzanie
                    </h3>
                </div>
                <div class="card-content">
                    <div class="management-grid">
                        <a href="{{ route('reservations.create', ['restaurant_id' => $restaurant->id ?? 1]) }}" class="management-item primary">
                            <div class="management-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="management-label">Nowa rezerwacja</div>
                        </a>

                        <a href="{{ route('reservations.index') }}" class="management-item success">
                            <div class="management-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="management-label">Wszystkie rezerwacje</div>
                        </a>

                        <div class="management-item warning disabled">
                            <div class="management-icon">
                                <i class="fas fa-chair"></i>
                            </div>
                            <div class="management-label">Stoliki</div>
                            <small>Wkrótce</small>
                        </div>

                        <div class="management-item info disabled">
                            <div class="management-icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="management-label">Menu</div>
                            <small>Wkrótce</small>
                        </div>

                        <div class="management-item danger disabled">
                            <div class="management-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="management-label">Raporty</div>
                            <small>Wkrótce</small>
                        </div>

                        <a href="{{ route('restaurants.show', $restaurant) }}" class="management-item secondary">
                            <div class="management-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="management-label">Podgląd publiczny</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Restaurant Performance -->
            <div class="content-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-chart-line"></i>
                        Wydajność
                    </h3>
                </div>
                <div class="card-content">
                    <div class="performance-metrics">
                        <div class="metric">
                            <div class="metric-label">Obłożenie dzisiaj</div>
                            <div class="metric-value">{{ min(($stats['todays_reservations'] ?? 0) * 12.5, 100) }}%</div>
                            <div class="metric-bar">
                            
                            </div>
                        </div>

                        <div class="metric">
                            <div class="metric-label">Skuteczność potwierdzeń</div>
                            <div class="metric-value">95%</div>
                            <div class="metric-bar">
                                <div class="metric-progress" style="width: 95%"></div>
                            </div>
                        </div>

                        <div class="metric">
                            <div class="metric-label">Średni czas obsługi</div>
                            <div class="metric-value">1.2h</div>
                            <div class="metric-indicator good">
                                <i class="fas fa-check"></i>
                                Dobry czas
                            </div>
                        </div>
                    </div>

                    <div class="performance-tips">
                        <h5>💡 Wskazówki</h5>
                        <ul>
                            <li>Potwierdź {{ $stats['pending_reservations'] ?? 0 }} oczekujących rezerwacji</li>
                            <li>Szczyt obłożenia: 19:00-21:00</li>
                            <li>Najlepiej oceniane dania: sprawdź menu</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Dashboard Base */
.modern-dashboard.manager {
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

.hero-restaurant-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 24px;
    text-align: center;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.restaurant-image {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 2rem;
    color: white;
}

.restaurant-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: white;
    font-weight: 600;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #4ade80;
}

.status-indicator.active {
    animation: pulse 2s infinite;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
    padding: 0 20px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 20px;
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
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
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
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 0.875rem;
    color: #718096;
    font-weight: 600;
    margin-bottom: 8px;
}

.stat-change {
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-change.positive {
    color: #38a169;
}

.stat-visual {
    flex-shrink: 0;
}

.capacity-rings {
    position: relative;
    width: 60px;
    height: 60px;
}

.ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 4px solid rgba(102, 126, 234, 0.2);
    border-top: 4px solid #667eea;
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
    font-size: 0.875rem;
}

.today-timeline {
    margin-top: 16px;
}

.timeline-bar {
    height: 6px;
    background: rgba(56, 178, 172, 0.2);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 8px;
}

.timeline-progress {
    height: 100%;
    background: linear-gradient(90deg, #38b2ac, #4fd1c7);
    border-radius: 3px;
    transition: width 0.3s ease;
    width: 0%;
}

.timeline-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #a0aec0;
}

.pending-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    font-size: 0.875rem;
    color: #d69e2e;
}

.urgent-pulse {
    width: 8px;
    height: 8px;
    background: #d69e2e;
    border-radius: 50%;
    animation: pulse 1s infinite;
}

.rating-stars {
    display: flex;
    gap: 4px;
    margin-top: 16px;
}

.rating-stars .fa-star {
    color: #e2e8f0;
    transition: color 0.2s;
}

.rating-stars .fa-star.active {
    color: #f6d55c;
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 24px;
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
    padding: 24px 24px 0;
    display: flex;
    justify-content: between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 0;
    padding-bottom: 16px;
}

.card-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 8px;
}

.header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.time-filter {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(102, 126, 234, 0.1);
    padding: 8px 16px;
    border-radius: 20px;
}

.current-time {
    font-weight: 600;
    color: #667eea;
    font-size: 0.875rem;
}

.live-dot {
    width: 8px;
    height: 8px;
    background: #4ade80;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.card-content {
    padding: 24px;
}

/* Reservations Timeline */
.reservations-timeline {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    position: relative;
}

.timeline-time {
    font-weight: 700;
    color: #2d3748;
    min-width: 60px;
    padding-top: 4px;
    font-size: 0.875rem;
}

.timeline-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e2e8f0;
    margin-top: 8px;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}

.timeline-item.confirmed .timeline-dot {
    background: #38a169;
    box-shadow: 0 0 0 4px rgba(56, 161, 105, 0.2);
}

.timeline-item.pending .timeline-dot {
    background: #d69e2e;
    box-shadow: 0 0 0 4px rgba(214, 158, 46, 0.2);
}

.timeline-content {
    flex: 1;
    background: #f7fafc;
    border-radius: 12px;
    padding: 20px;
    border-left: 4px solid #e2e8f0;
}

.timeline-item.confirmed .timeline-content {
    border-left-color: #38a169;
    background: #f0fff4;
}

.timeline-item.pending .timeline-content {
    border-left-color: #d69e2e;
    background: #fffaf0;
}

.reservation-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.guest-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.guest-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1rem;
}

.guest-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 4px;
}

.guest-meta {
    font-size: 0.875rem;
    color: #718096;
}

.reservation-status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.reservation-status-badge.pending {
    background: #fed7d7;
    color: #c53030;
}

.reservation-status-badge.confirmed {
    background: #c6f6d5;
    color: #2f855a;
}

.special-requests {
    background: rgba(102, 126, 234, 0.1);
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 0.875rem;
    color: #4a5568;
    display: flex;
    align-items: center;
    gap: 8px;
}

.timeline-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn {
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}

.action-btn.confirm {
    background: #c6f6d5;
    color: #2f855a;
}

.action-btn.view {
    background: #e6fffa;
    color: #319795;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Management Grid */
.management-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.management-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s;
    border: 2px solid transparent;
    text-align: center;
    position: relative;
}

.management-item:hover:not(.disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.management-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Management Item Colors - Each as separate rule */
.management-item.primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.management-item.success {
    background: linear-gradient(135deg, #56ab2f, #a8e6cf);
    color: white;
}

.management-item.warning {
    background: linear-gradient(135deg, #f093fb, #f5576c);
    color: white;
}

.management-item.info {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: white;
}

.management-item.danger {
    background: linear-gradient(135deg, #ff416c, #ff4b2b);
    color: white;
}

.management-item.secondary {
    background: #f7fafc;
    color: #4a5568;
    border-color: #e2e8f0;
}

.management-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
}

.management-item.secondary .management-icon {
    background: #e2e8f0;
    color: #4a5568;
}

.management-label {
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 4px;
}

.management-item small {
    font-size: 0.75rem;
    opacity: 0.8;
}

/* Performance Metrics */
.performance-metrics {
    margin-bottom: 24px;
}

.metric {
    margin-bottom: 20px;
}

.metric-label {
    font-size: 0.875rem;
    color: #718096;
    margin-bottom: 6px;
    font-weight: 500;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
}

.metric-bar {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
}

.metric-progress {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 3px;
    transition: width 0.3s ease;
    width: 0%;
}

.metric-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.875rem;
    margin-top: 8px;
}

.metric-indicator.good {
    color: #38a169;
}

.performance-tips h5 {
    color: #2d3748;
    margin-bottom: 12px;
    font-size: 1rem;
    font-weight: 600;
}

.performance-tips ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.performance-tips li {
    padding: 8px 0;
    color: #718096;
    font-size: 0.875rem;
    border-bottom: 1px solid #e2e8f0;
    position: relative;
    padding-left: 16px;
}

.performance-tips li:before {
    content: '•';
    color: #667eea;
    position: absolute;
    left: 0;
    font-weight: bold;
}

.performance-tips li:last-child {
    border-bottom: none;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 3rem;
    color: #cbd5e0;
    margin-bottom: 16px;
}

.empty-state h4 {
    color: #4a5568;
    margin-bottom: 8px;
    font-size: 1.125rem;
}

.empty-state p {
    margin-bottom: 24px;
    font-size: 0.875rem;
}

/* Buttons */
.btn-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

/* Animations */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .content-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .content-card.span-2 {
        grid-column: span 2;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-meta {
        flex-direction: column;
        gap: 12px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .content-card.span-2 {
        grid-column: span 1;
    }
    
    .management-grid {
        grid-template-columns: 1fr;
    }
    
    .reservation-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .timeline-actions {
        width: 100%;
    }
    
    .action-btn {
        flex: 1;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .dashboard-hero {
        padding: 20px 0;
    }
    
    .hero-title {
        font-size: 1.75rem;
    }
    
    .stats-grid,
    .content-grid {
        padding: 0 15px;
    }
    
    .stat-card {
        padding: 16px;
        flex-direction: column;
        text-align: center;
    }
    
    .stat-visual {
        margin-top: 16px;
    }
    
    .timeline-item {
        flex-direction: column;
        gap: 8px;
    }
    
    .timeline-time {
        min-width: auto;
        font-size: 0.875rem;
        padding-top: 0;
    }
    
    .timeline-dot {
        display: none;
    }
}

/* Accessibility Improvements */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Focus States */
.action-btn:focus,
.management-item:focus,
.btn-primary:focus {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .stat-card,
    .content-card {
        border: 2px solid #000;
    }
    
    .hero-restaurant-card {
        border: 2px solid #fff;
    }
}

/* Print Styles */
@media print {
    .modern-dashboard.manager {
        background: white !important;
        color: black !important;
    }
    
    .hero-restaurant-card,
    .stat-card,
    .content-card {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
    
    .action-btn,
    .management-item,
    .btn-primary {
        display: none !important;
    }
}