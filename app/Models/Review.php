<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'dish_id',
        'rating',
        'comment',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

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
     * Relacja z daniem
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }

    /**
     * Scope - tylko zatwierdzone opinie
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope - oczekujące na zatwierdzenie
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope - opinie o restauracjach
     */
    public function scopeForRestaurants($query)
    {
        return $query->whereNotNull('restaurant_id')->whereNull('dish_id');
    }

    /**
     * Scope - opinie o daniach
     */
    public function scopeForDishes($query)
    {
        return $query->whereNotNull('dish_id')->whereNull('restaurant_id');
    }

    /**
     * Scope - sortowanie według daty (najnowsze pierwsze)
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Zatwierdź opinię
     */
    public function approve()
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        // Aktualizuj średnią ocenę restauracji lub dania
        $this->updateAverageRating();
    }

    /**
     * Odrzuć opinię
     */
    public function reject()
    {
        $this->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);
    }

    /**
     * Aktualizuj średnią ocenę restauracji lub dania
     */
    private function updateAverageRating()
    {
        if ($this->restaurant_id) {
            // Aktualizuj ocenę restauracji
            $avgRating = $this->restaurant->reviews()->approved()->avg('rating');
            $this->restaurant->update(['rating' => round($avgRating, 1)]);
        }

        if ($this->dish_id) {
            // Aktualizuj ocenę dania
            $avgRating = $this->dish->reviews()->approved()->avg('rating');
            $this->dish->update(['rating' => round($avgRating, 1)]);
        }
    }

    /**
     * Pobierz gwiazdki jako string
     */
    public function getStarsAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Sprawdź czy opinia może być edytowana
     */
    public function canBeEdited()
    {
        // Można edytować do 24h po utworzeniu, jeśli nie jest zatwierdzona
        return !$this->is_approved && $this->created_at->diffInHours(now()) < 24;
    }
}