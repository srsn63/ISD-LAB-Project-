<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'iata_code',
        'icao_code',
        'name',
        'city',
        'country',
        'timezone',
        'latitude',
        'longitude',
        'elevation_feet',
        'total_terminals',
        'total_runways',
        'international',
        'active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'elevation_feet' => 'integer',
        'total_terminals' => 'integer',
        'total_runways' => 'integer',
        'international' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Get the routes departing from this airport
     */
    public function departingRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'departure_airport_id');
    }

    /**
     * Get the routes arriving at this airport
     */
    public function arrivingRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'arrival_airport_id');
    }

    /**
     * Get the gates for this airport
     */
    public function gates(): HasMany
    {
        return $this->hasMany(Gate::class);
    }

    /**
     * Get the full airport code (IATA/ICAO)
     */
    public function getFullCodeAttribute(): string
    {
        return "{$this->iata_code}/{$this->icao_code}";
    }

    /**
     * Get the airport location
     */
    public function getLocationAttribute(): string
    {
        return "{$this->city}, {$this->country}";
    }

    /**
     * Check if airport is international
     */
    public function isInternational(): bool
    {
        return $this->international;
    }

    /**
     * Check if airport is active
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}