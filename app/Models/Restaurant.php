<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'phone',
        'email',
        'image',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    /**
     * Relacja z tabelami (stoliki)
     */
   public function tables(): HasMany
{
    return $this->hasMany(Table::class, 'restaurant_id');
}

    /**
     * Relacja z menu
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Relacja z godzinami pracy
     */
    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }

    /**
     * Relacja z opiniami
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope - tylko aktywne restauracje
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Pobierz dostępne stoliki w danym terminie
     */
    public function getAvailableTables($date, $time, $guests)
    {
        return $this->tables()
            ->where('capacity', '>=', $guests)
            ->where('status', 'available')
            ->whereDoesntHave('reservations', function ($query) use ($date, $time) {
                $query->where('reservation_date', $date)
                      ->where('reservation_time', $time)
                      ->whereIn('status', ['pending', 'confirmed']);
            })
            ->get();
    }
}