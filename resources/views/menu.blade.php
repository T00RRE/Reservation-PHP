@extends('layouts.app')

@section('title', $restaurant->name . ' - Menu - RestaurantBook')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('restaurants.index') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>Restauracje
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('restaurants.show', $restaurant) }}" class="text-decoration-none">
                    {{ $restaurant->name }}
                </a>
            </li>
            <li class="breadcrumb-item active">Menu</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold text-primary mb-4 text-center">
                <i class="fas fa-book-open me-2"></i>Menu dla {{ $restaurant->name }}
            </h1>

            @if($restaurant->menus->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i>Ta restauracja nie ma jeszcze dodanego menu.
                </div>
            @else
                @foreach($restaurant->menus as $menu)
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-success text-white text-center">
                            <h4 class="mb-0">{{ $menu->name }}</h4>
                            @if($menu->description)
                                <p class="mb-0"><small>{{ $menu->description }}</small></p>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            @if($menu->categories->isEmpty())
                                <div class="alert alert-warning text-center" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Brak kategorii w tym menu.
                                </div>
                            @else
                                @foreach($menu->categories as $category)
                                    <h5 class="fw-bold text-primary border-bottom pb-2 mt-4">
                                        <i class="fas fa-list me-2"></i>{{ $category->name }}
                                    </h5>
                                    @if($category->description)
                                        <p class="text-muted mb-3">{{ $category->description }}</p>
                                    @endif

                                    @if($category->dishes->isEmpty())
                                        <div class="alert alert-secondary" role="alert">
                                            Brak dań w tej kategorii.
                                        </div>
                                    @else
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                            @foreach($category->dishes as $dish)
                                                <div class="col">
                                                    <div class="card h-100 border-0 shadow-sm">
                                                        <div class="card-body d-flex flex-column">
                                                            <h6 class="card-title fw-bold text-dark">{{ $dish->name }}</h6>
                                                            @if($dish->description)
                                                                <p class="card-text small text-muted mb-2">{{ $dish->description }}</p>
                                                            @endif
                                                            <div class="mt-auto d-flex justify-content-between align-items-center pt-2">
                                                                <span class="fs-5 fw-bold text-success">{{ $dish->formatted_price }}</span>
                                                                <div>
                                                                    @if($dish->is_vegetarian)
                                                                        <span class="badge bg-success me-1">Wege <i class="fas fa-leaf"></i></span>
                                                                    @endif
                                                                    @if($dish->is_vegan)
                                                                        <span class="badge bg-info me-1">Wegan <i class="fas fa-seedling"></i></span>
                                                                    @endif
                                                                    @if($dish->is_gluten_free)
                                                                        <span class="badge bg-warning text-dark">Bezglutenowe <i class="fas fa-wheat-alt"></i></span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header.bg-success {
        border-radius: .375rem .375rem 0 0;
    }
    .card.shadow-sm {
        transition: transform 0.2s ease-in-out;
    }
    .card.shadow-sm:hover {
        transform: translateY(-5px);
    }
    .badge i {
        font-size: 0.8em;
    }
</style>
@endpush