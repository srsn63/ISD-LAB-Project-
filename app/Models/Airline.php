<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Airline extends Model
{
    use HasFactory;

    protected $fillable = [
        'airline_code',
        'icao_code',
        'name',
        'country',
        'headquarters',
        'website',
        'logo_url',
        'active',
        'contact_info',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the aircraft that belong to the airline
     */
    public function aircraft(): HasMany
    {
        return $this->hasMany(Aircraft::class);
    }

    /**
     * Get the flights for the airline
     */
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    /**
     * Get the active aircraft count
     */
    public function getActiveAircraftCountAttribute(): int
    {
        return $this->aircraft()->where('status', 'active')->count();
    }

    /**
     * Get the total fleet size
     */
    public function getFleetSizeAttribute(): int
    {
        return $this->aircraft()->count();
    }
}