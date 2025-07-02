@extends('layouts.app')

@section('title', 'Panel Administratora')

@section('content')
<div class="modern-dashboard">
    <!-- Hero Header -->
    <div class="dashboard-hero">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-crown"></i>
                            Administrator
                        </div>
                        <h1 class="hero-title">
                            Witaj z powrotem! 👋
                        </h1>
                        <p class="hero-subtitle">
                            Zarządzaj swoim imperium kulinarnym z tego centralnego panelu
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-stats">
                        <div class="stat-circle">
                            <div class="stat-number">{{ $stats['total_restaurants'] ?? 0 }}</div>
                            <div class="stat-label">Restauracji</div>
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
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_restaurants'] ?? 0 }}</div>
                    <div class="stat-label">Restauracje</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> +12% ten miesiąc
                    </div>
                </div>
                <div class="stat-chart">
                    <div class="chart-bar" style="height: 70%"></div>
                    <div class="chart-bar" style="height: 40%"></div>
                    <div class="chart-bar" style="height: 90%"></div>
                    <div class="chart-bar" style="height: 60%"></div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="stat-label">Użytkownicy</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> +8% ten tydzień
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar" style="width: 75%"></div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_reservations'] ?? 0 }}</div>
                    <div class="stat-label">Rezerwacje</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> +23% dziś
                    </div>
                </div>
                <div class="stat-sparkline">
                    <svg width="100" height="30">
                        <polyline points="0,25 20,15 40,20 60,10 80,5 100,15" 
                                  fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['todays_reservations'] ?? 0 }}</div>
                    <div class="stat-label">Dzisiaj</div>
                    <div class="stat-change">
                        <i class="fas fa-clock"></i> W czasie rzeczywistym
                    </div>
                </div>
                <div class="live-indicator">
                    <div class="pulse"></div>
                    <span>Na żywo</span>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Recent Reservations -->
            <div class="content-card span-2">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-list-alt"></i>
                        Najnowsze rezerwacje
                    </h3>
                    <div class="header-actions">
                        <button class="btn-filter active">Wszystkie</button>
                        <button class="btn-filter">Dzisiaj</button>
                        <button class="btn-filter">Oczekujące</button>
                    </div>
                </div>
                <div class="card-content">
                    @if($recentReservations->count() > 0)
                        <div class="reservations-list">
                            @foreach($recentReservations->take(8) as $reservation)
                                <div class="reservation-item">
                                    <div class="reservation-avatar">
                                        <div class="avatar-circle">
                                            {{ substr($reservation->user->name ?? 'U', 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="reservation-details">
                                        <div class="reservation-name">{{ $reservation->user->name ?? 'Nieznany' }}</div>
                                        <div class="reservation-restaurant">{{ $reservation->restaurant->name ?? '-' }}</div>
                                        <div class="reservation-meta">
                                            <span class="meta-item">
                                                <i class="fas fa-calendar"></i>
                                                {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d.m') }}
                                            </span>
                                            <span class="meta-item">
                                                <i class="fas fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                                            </span>
                                            <span class="meta-item">
                                                <i class="fas fa-users"></i>
                                                {{ $reservation->guests_count }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="reservation-status">
                                        <span class="status-badge {{ $reservation->status }}">
                                            @switch($reservation->status)
                                                @case('pending')
                                                    <i class="fas fa-clock"></i> Oczekuje
                                                    @break
                                                @case('confirmed')
                                                    <i class="fas fa-check"></i> Potwierdzona
                                                    @break
                                                @case('cancelled')
                                                    <i class="fas fa-times"></i> Anulowana
                                                    @break
                                                @default
                                                    {{ $reservation->status }}
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="reservation-actions">
                                        <a href="{{ route('reservations.show', $reservation) }}" 
                                           class="action-btn view">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($reservation->status === 'pending')
                                            <form action="{{ route('reservations.confirm', $reservation) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-btn confirm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h4>Brak rezerwacji</h4>
                            <p>Gdy pojawią się nowe rezerwacje, zobaczysz je tutaj</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-bolt"></i>
                        Szybkie akcje
                    </h3>
                </div>
                <div class="card-content">
                    <div class="quick-actions">
                        <a href="{{ route('restaurants.index') }}" class="quick-action primary">
                            <div class="action-icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Restauracje</div>
                                <div class="action-subtitle">Zarządzaj lokalizacjami</div>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </a>

                        <a href="{{ route('reservations.index') }}" class="quick-action success">
                            <div class="action-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Rezerwacje</div>
                                <div class="action-subtitle">Wszystkie rezerwacje</div>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </a>

                        <a href="{{ route('reservations.create') }}" class="quick-action warning">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Nowa rezerwacja</div>
                                <div class="action-subtitle">Dodaj rezerwację</div>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </a>

                        <div class="quick-action disabled">
                            <div class="action-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Opinie</div>
                                <div class="action-subtitle">Wkrótce dostępne</div>
                            </div>
                            <i class="fas fa-lock action-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="content-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-heartbeat"></i>
                        Status systemu
                    </h3>
                </div>
                <div class="card-content">
                    <div class="system-health">
                        <div class="health-item">
                            <div class="health-indicator green"></div>
                            <div class="health-info">
                                <div class="health-label">Serwer</div>
                                <div class="health-status">Działa prawidłowo</div>
                            </div>
                            <div class="health-value">99.9%</div>
                        </div>
                        <div class="health-item">
                            <div class="health-indicator green"></div>
                            <div class="health-info">
                                <div class="health-label">Baza danych</div>
                                <div class="health-status">Wszystko OK</div>
                            </div>
                            <div class="health-value">98.5%</div>
                        </div>
                        <div class="health-item">
                            <div class="health-indicator yellow"></div>
                            <div class="health-info">
                                <div class="health-label">Powiadomienia</div>
                                <div class="health-status">W przygotowaniu</div>
                            </div>
                            <div class="health-value">0%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modern-dashboard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 0;
}

/* Hero Section */
.dashboard-hero {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9));
    padding: 60px 0;
    color: white;
    margin-bottom: 40px;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    margin-bottom: 20px;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 16px;
    background: linear-gradient(45deg, #fff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    font-weight: 400;
}

.hero-stats {
    display: flex;
    justify-content: center;
}

.stat-circle {
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.stat-circle .stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: white;
}

.stat-circle .stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--accent-color);
}

.stat-card.primary { --accent-color: linear-gradient(45deg, #667eea, #764ba2); }
.stat-card.success { --accent-color: linear-gradient(45deg, #56ab2f, #a8e6cf); }
.stat-card.warning { --accent-color: linear-gradient(45deg, #f093fb, #f5576c); }
.stat-card.info { --accent-color: linear-gradient(45deg, #4facfe, #00f2fe); }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    background: var(--accent-color);
    margin-bottom: 20px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #2d3748;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 1rem;
    color: #718096;
    font-weight: 600;
    margin-bottom: 12px;
}

.stat-change {
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-change.positive { color: #38a169; }

.stat-chart {
    display: flex;
    align-items: end;
    gap: 4px;
    height: 40px;
    margin-top: 16px;
}

.chart-bar {
    flex: 1;
    background: var(--accent-color);
    border-radius: 2px;
    opacity: 0.7;
}

.stat-progress {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    margin-top: 16px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: var(--accent-color);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.live-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    font-size: 0.875rem;
    color: #718096;
}

.pulse {
    width: 8px;
    height: 8px;
    background: #38a169;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
    100% { opacity: 1; transform: scale(1); }
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 24px;
    margin-bottom: 40px;
}

.content-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.content-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.content-card.span-2 {
    grid-column: span 8;
}

.content-card:not(.span-2) {
    grid-column: span 4;
}

.card-header {
    padding: 24px 32px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-actions {
    display: flex;
    gap: 8px;
}

.btn-filter {
    padding: 6px 16px;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 20px;
    font-size: 0.875rem;
    color: #718096;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-filter.active, .btn-filter:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.card-content {
    padding: 32px;
}

/* Reservations List */
.reservations-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.reservation-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    border-radius: 12px;
    transition: all 0.2s;
}

.reservation-item:hover {
    background: #f7fafc;
}

.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(45deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
}

.reservation-details {
    flex: 1;
}

.reservation-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 4px;
}

.reservation-restaurant {
    color: #718096;
    font-size: 0.875rem;
    margin-bottom: 8px;
}

.reservation-meta {
    display: flex;
    gap: 16px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    color: #a0aec0;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.status-badge.pending { background: #fed7d7; color: #c53030; }
.status-badge.confirmed { background: #c6f6d5; color: #2f855a; }
.status-badge.cancelled { background: #fed7d7; color: #c53030; }

.reservation-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.action-btn.view { background: #e6fffa; color: #319795; }
.action-btn.confirm { background: #f0fff4; color: #38a169; }
.action-btn:hover { transform: scale(1.1); }

/* Quick Actions */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.quick-action {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s;
    border: 1px solid #e2e8f0;
}

.quick-action:hover:not(.disabled) {
    transform: translateX(4px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.quick-action.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.quick-action.primary .action-icon { background: linear-gradient(45deg, #667eea, #764ba2); }
.quick-action.success .action-icon { background: linear-gradient(45deg, #56ab2f, #a8e6cf); }
.quick-action.warning .action-icon { background: linear-gradient(45deg, #f093fb, #f5576c); }

.action-content {
    flex: 1;
}

.action-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 4px;
}

.action-subtitle {
    font-size: 0.875rem;
    color: #718096;
}

.action-arrow {
    color: #a0aec0;
    transition: all 0.2s;
}

.quick-action:hover .action-arrow {
    transform: translateX(4px);
    color: #667eea;
}

/* System Health */
.system-health {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.health-item {
    display: flex;
    align-items: center;
    gap: 16px;
}

.health-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    position: relative;
}

.health-indicator.green {
    background: #38a169;
    box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.2);
}

.health-indicator.yellow {
    background: #d69e2e;
    box-shadow: 0 0 0 3px rgba(214, 158, 46, 0.2);
}

.health-info {
    flex: 1;
}

.health-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 4px;
}

.health-status {
    font-size: 0.875rem;
    color: #718096;
}

.health-value {
    font-weight: 600;
    color: #2d3748;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #a0aec0;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
}

.empty-state h4 {
    margin-bottom: 12px;
    color: #718096;
}

@media (max-width: 768px) {
    .content-card.span-2,
    .content-card:not(.span-2) {
        grid-column: span 12;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    }
    
    .hero-title {
        font-size: 2rem;
    }
}
</style>
@endsection