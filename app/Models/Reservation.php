<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'table_id',
        'reservation_date',
        'reservation_time',
        'guests_count',
        'status',
        'special_requests',
        'notes',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime:H:i',
        'guests_count' => 'integer',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Statusy rezerwacji
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    /**
     * Relacja z użytkownikiem
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacja z restauracją
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Relacja ze stolikiem
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Scope - aktywne rezerwacje (nie anulowane)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED]);
    }

    /**
     * Scope - rezerwacje na dziś
     */
    public function scopeToday($query)
    {
        return $query->where('reservation_date', Carbon::today());
    }

    /**
     * Scope - nadchodzące rezerwacje
     */
    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', Carbon::today())
                    ->where('status', '!=', self::STATUS_CANCELLED);
    }

    /**
     * Potwierdź rezerwację
     */
    public function confirm()
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Anuluj rezerwację
     */
    public function cancel()
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Sprawdź czy rezerwacja może być anulowana
     */
    public function canBeCancelled()
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return false;
        }

        // Można anulować do 2 godzin przed rezerwacją
        $reservationDateTime = Carbon::parse($this->reservation_date . ' ' . $this->reservation_time);
        return $reservationDateTime->diffInHours(now()) >= 2;
    }

    /**
     * Sprawdź czy rezerwacja wymaga przypomnienia
     */
    public function needsReminder()
    {
        $reservationDateTime = Carbon::parse($this->reservation_date . ' ' . $this->reservation_time);
        $hoursUntilReservation = now()->diffInHours($reservationDateTime, false);
        
        return $hoursUntilReservation <= 24 && $hoursUntilReservation > 0 && 
               $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Pobierz wszystkie dostępne statusy
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Oczekująca',
            self::STATUS_CONFIRMED => 'Potwierdzona',
            self::STATUS_CANCELLED => 'Anulowana',
            self::STATUS_COMPLETED => 'Zakończona',
        ];
    }
}