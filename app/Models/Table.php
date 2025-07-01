<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    protected $table = 'restaurant_tables';
    protected $fillable = [
        'restaurant_id',
        'table_number',
        'capacity',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Statusy stolików
     */
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';
    const STATUS_MAINTENANCE = 'maintenance';

    /**
     * Relacja z restauracją
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Relacja z rezerwacjami
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope - tylko dostępne stoliki
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope - stoliki o danej pojemności lub większej
     */
    public function scopeMinCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }

    /**
     * Sprawdź czy stolik jest dostępny w danym terminie
     */
    public function isAvailableAt($date, $time)
    {
        if ($this->status !== self::STATUS_AVAILABLE) {
            return false;
        }

        return !$this->reservations()
            ->where('reservation_date', $date)
            ->where('reservation_time', $time)
            ->whereNotIn('status', ['cancelled'])
            ->exists();
    }

    /**
     * Pobierz wszystkie dostępne statusy
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_AVAILABLE => 'Dostępny',
            self::STATUS_OCCUPIED => 'Zajęty',
            self::STATUS_RESERVED => 'Zarezerwowany',
            self::STATUS_MAINTENANCE => 'Serwis',
        ];
    }
}