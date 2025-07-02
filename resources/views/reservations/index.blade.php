@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista rezerwacji</h1>
    <a href="{{ route('reservations.create') }}" class="btn btn-primary mb-3">Nowa rezerwacja</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Restauracja</th>
                <th>Stolik</th>
                <th>Data</th>
                <th>Godzina</th>
                <th>Liczba gości</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->restaurant->name ?? '-' }}</td>
                    <td>{{ $reservation->table->table_number ?? '-' }}</td>
                    <td>{{ $reservation->reservation_date }}</td>
                    <td>{{ $reservation->reservation_time }}</td>
                    <td>{{ $reservation->guests_count }}</td>
                    <td>{{ $reservation->status }}</td>
                    <td>
                        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-sm btn-info">Szczegóły</a>
                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-warning">Edytuj</a>
                        <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Na pewno anulować?')">Usuń</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">Brak rezerwacji.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $reservations->links() }}
</div>

{{-- Dodaj style CSS bezpośrednio w kodzie Blade --}}
<style>
/* Style dla strony listy rezerwacji (index.blade.php) */

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 30px;
    text-align: center;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    border: none;
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.2);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    color: white; /* Ensure text color remains white on hover */
    text-decoration: none;
}

.mb-3 {
    margin-bottom: 1.5rem !important;
}

/* Table styles */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    background-color: #ffffff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    overflow: hidden; /* Ensures rounded corners apply to content */
}

.table-bordered {
    border: 1px solid #e5e7eb;
}

.table thead th {
    background-color: #f3f4f6;
    color: #4b5563;
    font-weight: 700;
    padding: 15px 20px;
    text-align: left;
    border-bottom: 2px solid #e5e7eb;
    font-size: 0.95rem;
}

.table tbody tr:nth-child(even) {
    background-color: #f9fafb;
}

.table tbody tr:hover {
    background-color: #f0f4f8;
    transition: background-color 0.3s ease;
}

.table tbody td {
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
    color: #374151;
    font-size: 0.9rem;
    vertical-align: middle;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

/* Action buttons in table */
.table .btn-sm {
    padding: 6px 12px;
    font-size: 0.85rem;
    border-radius: 6px;
    margin-right: 5px;
    min-width: 80px; /* Ensure consistent button width */
}

.table .btn-info {
    background-color: #3b82f6; /* Blue shade */
    color: white;
    border: none;
}

.table .btn-info:hover {
    background-color: #2563eb;
    color: white;
    text-decoration: none;
}

.table .btn-warning {
    background-color: #f59e0b; /* Orange shade */
    color: white;
    border: none;
}

.table .btn-warning:hover {
    background-color: #d97706;
    color: white;
    text-decoration: none;
}

.table .btn-danger {
    background-color: #ef4444; /* Red shade */
    color: white;
    border: none;
}

.table .btn-danger:hover {
    background-color: #dc2626;
    color: white;
    text-decoration: none;
}

/* Empty state for table */
.table tbody td[colspan="8"] {
    text-align: center;
    padding: 40px 20px;
    font-style: italic;
    color: #6b7280;
    font-size: 1rem;
}

/* Pagination for reservations list */
.pagination-wrapper {
    margin-top: 30px;
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
    text-decoration: none;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Responsive adjustments for table */
@media (max-width: 768px) {
    .table thead {
        display: none; /* Hide table headers on small screens */
    }

    .table, .table tbody, .table tr, .table td {
        display: block;
        width: 100%;
    }
    
    .table tr {
        margin-bottom: 15px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
        border: none;
        border-bottom: 1px solid #e5e7eb;
    }

    .table td:last-child {
        border-bottom: none;
    }

    .table td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        width: calc(50% - 30px);
        padding-right: 10px;
        white-space: nowrap;
        text-align: left;
        font-weight: 700;
        color: #4b5563;
    }
    
    /* Assign data-label to each td for responsive display */
    /* Upewnij się, że ten fragment jest zgodny z Twoimi nagłówkami tabeli */
    .table tbody td:nth-of-type(1)::before { content: "ID:"; }
    .table tbody td:nth-of-type(2)::before { content: "Restauracja:"; }
    .table tbody td:nth-of-type(3)::before { content: "Stolik:"; }
    .table tbody td:nth-of-type(4)::before { content: "Data:"; }
    .table tbody td:nth-of-type(5)::before { content: "Godzina:"; }
    .table tbody td:nth-of-type(6)::before { content: "Liczba gości:"; }
    .table tbody td:nth-of-type(7)::before { content: "Status:"; }
    .table tbody td:nth-of-type(8)::before { content: "Akcje:"; }

    .table .btn-sm {
        width: auto;
        margin-bottom: 5px;
        margin-right: 0;
    }

    .table tbody td:nth-of-type(8) {
        text-align: center;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        padding-left: 15px; /* Adjust padding for action buttons */
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 2rem;
    }
    .btn-primary {
        width: 100%;
        margin-bottom: 15px;
    }
}
</style>
@endsection