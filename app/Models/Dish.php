<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'allergens',
        'is_vegetarian',
        'is_vegan',
        'is_available',
        'rating',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'allergens' => 'array',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_available' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relacja z kategorią
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relacja z opiniami
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope - tylko dostępne dania
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope - tylko wegetariańskie
     */
    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    /**
     * Scope - tylko wegańskie
     */
    public function scopeVegan($query)
    {
        return $query->where('is_vegan', true);
    }

    /**
     * Scope - sortowanie według kolejności
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope - filtrowanie po cenie
     */
    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Pobierz średnią ocenę tego dania
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->approved()->avg('rating') ?: 0;
    }

    /**
     * Sprawdź czy danie zawiera określony alergen
     */
    public function hasAllergen($allergen)
    {
        return in_array($allergen, $this->allergens ?: []);
    }

    /**
     * Pobierz listę wszystkich alergenów
     */
    public static function getAllergens()
    {
        return [
            'gluten' => 'Gluten',
            'nuts' => 'Orzechy',
            'dairy' => 'Nabiał',
            'eggs' => 'Jajka',
            'fish' => 'Ryby',
            'shellfish' => 'Skorupiaki',
            'soy' => 'Soja',
            'sesame' => 'Sezam',
        ];
    }

    /**
     * Formatuj cenę
     */
    public function getFormattedPriceAttribute()
    {
        return number_format((float) $this->price, 2) . ' zł';
    }
}