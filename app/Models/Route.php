<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'departure_airport_id',
        'arrival_airport_id',
        'distance_km',
        'estimated_duration',
        'is_active',
    ];

    protected $casts = [
        'distance_km' => 'integer',
        'estimated_duration' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the departure airport
     */
    public function departureAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    /**
     * Get the arrival airport
     */
    public function arrivalAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    /**
     * Get the flights for this route
     */
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    /**
     * Check if route is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get route name
     */
    public function getRouteNameAttribute(): string
    {
        return "{$this->departureAirport->iata_code} → {$this->arrivalAirport->iata_code}";
    }

    /**
     * Get route description
     */
    public function getRouteDescriptionAttribute(): string
    {
        return "{$this->departureAirport->city} to {$this->arrivalAirport->city}";
    }

    /**
     * Get estimated duration in hours and minutes
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = intval($this->estimated_duration / 60);
        $minutes = $this->estimated_duration % 60;
        
        if ($hours > 0) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }
        
        return "{$minutes}m";
    }

    /**
     * Get distance in different units
     */
    public function getDistanceMilesAttribute(): float
    {
        return round($this->distance_km * 0.621371, 2);
    }

    /**
     * Get distance in nautical miles
     */
    public function getDistanceNauticalMilesAttribute(): float
    {
        return round($this->distance_km * 0.539957, 2);
    }

    /**
     * Check if route is international
     */
    public function isInternational(): bool
    {
        return $this->departureAirport->country !== $this->arrivalAirport->country;
    }

    /**
     * Check if route is domestic
     */
    public function isDomestic(): bool
    {
        return !$this->isInternational();
    }

    /**
     * Get route category based on distance
     */
    public function getCategoryAttribute(): string
    {
        return match(true) {
            $this->distance_km <= 500 => 'Short-haul',
            $this->distance_km <= 3000 => 'Medium-haul',
            default => 'Long-haul'
        };
    }

    /**
     * Get upcoming flights count
     */
    public function getUpcomingFlightsCountAttribute(): int
    {
        return $this->flights()
            ->where('flight_date', '>=', today())
            ->count();
    }

    /**
     * Get popular routes (static method to be called on model)
     */
    public static function getPopularRoutes(int $limit = 10)
    {
        return static::withCount(['flights' => function ($query) {
                $query->where('flight_date', '>=', now()->subMonths(3));
            }])
            ->having('flights_count', '>', 0)
            ->orderBy('flights_count', 'desc')
            ->limit($limit)
            ->get();
    }
}