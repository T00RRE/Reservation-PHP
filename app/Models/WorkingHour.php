<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_closed' => 'boolean',
    ];

    /**
     * Dni tygodnia
     */
    const DAYS = [
        0 => 'Niedziela',
        1 => 'Poniedziałek',
        2 => 'Wtorek',
        3 => 'Środa',
        4 => 'Czwartek',
        5 => 'Piątek',
        6 => 'Sobota',
    ];

    /**
     * Relacja z restauracją
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Scope - tylko otwarte dni
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    /**
     * Scope - dla konkretnego dnia tygodnia
     */
    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Pobierz nazwę dnia
     */
    public function getDayNameAttribute()
    {
        return self::DAYS[$this->day_of_week] ?? 'Nieznany';
    }

    /**
     * Sprawdź czy restauracja jest otwarta w danym czasie
     */
    public function isOpenAt($time)
    {
        if ($this->is_closed) {
            return false;
        }

        // Prosta implementacja - można rozbudować później
        return true;
    }

    /**
     * Pobierz godziny pracy jako string
     */
    public function getHoursStringAttribute()
    {
        if ($this->is_closed) {
            return 'Zamknięte';
        }

        return $this->open_time . ' - ' . $this->close_time;
    }

    /**
     * Sprawdź czy można dokonać rezerwacji w danym czasie
     */
    public function canReserveAt($time, $minimumAdvanceHours = 2)
    {
        return $this->isOpenAt($time);
    }

    /**
     * Pobierz dostępne godziny rezerwacji (uproszczona wersja)
     */
    public function getAvailableReservationTimes()
    {
        if ($this->is_closed) {
            return [];
        }

        // Podstawowe godziny rezerwacji
        return ['12:00', '12:30', '13:00', '13:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30'];
    }
}