<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'restaurant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_STAFF = 'staff';
    const ROLE_CUSTOMER = 'customer';

    /**
     * Relacja z restauracją (dla manager i staff)
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
     * Relacja z opiniami
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Sprawdź czy użytkownik ma daną rolę
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Sprawdź czy użytkownik jest administratorem
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Sprawdź czy użytkownik jest menedżerem
     */
    public function isManager()
    {
        return $this->hasRole(self::ROLE_MANAGER);
    }

    /**
     * Sprawdź czy użytkownik jest personelem
     */
    public function isStaff()
    {
        return $this->hasRole(self::ROLE_STAFF);
    }

    /**
     * Sprawdź czy użytkownik jest klientem
     */
    public function isCustomer()
    {
        return $this->hasRole(self::ROLE_CUSTOMER);
    }

    /**
     * Sprawdź czy użytkownik może zarządzać restauracją
     */
    public function canManageRestaurant($restaurantId = null)
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isManager() || $this->isStaff()) {
            return $restaurantId ? $this->restaurant_id == $restaurantId : true;
        }

        return false;
    }

    /**
     * Scope - użytkownicy z daną rolą
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope - personel restauracji (manager + staff)
     */
    public function scopeRestaurantStaff($query, $restaurantId = null)
    {
        $query = $query->whereIn('role', [self::ROLE_MANAGER, self::ROLE_STAFF]);
        
        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        }
        
        return $query;
    }

    /**
     * Pobierz wszystkie dostępne role
     */
    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_MANAGER => 'Menedżer',
            self::ROLE_STAFF => 'Personel',
            self::ROLE_CUSTOMER => 'Klient',
        ];
    }

    /**
     * Pobierz nazwę roli
     */
    public function getRoleNameAttribute()
    {
        return self::getRoles()[$this->role] ?? 'Nieznana';
    }
}