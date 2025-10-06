<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aircraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'aircraft_type',
        'model',
        'airline_id',
        'total_seats',
        'first_class_seats',
        'business_class_seats',
        'economy_class_seats',
        'manufacturer',
        'manufacturing_year',
        'status',
        'last_maintenance_date',
        'next_maintenance_date',
    ];

    protected $casts = [
        'manufacturing_year' => 'integer',
        'total_seats' => 'integer',
        'first_class_seats' => 'integer',
        'business_class_seats' => 'integer',
        'economy_class_seats' => 'integer',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];

    /**
     * Get the airline that owns the aircraft
     */
    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }

    /**
     * Get the seats for the aircraft
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Get the flights for the aircraft
     */
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    /**
     * Check if aircraft needs maintenance soon
     */
    public function needsMaintenanceSoon(int $days = 30): bool
    {
        return $this->next_maintenance_date && 
               $this->next_maintenance_date <= now()->addDays($days);
    }

    /**
     * Get aircraft age in years
     */
    public function getAgeAttribute(): int
    {
        return now()->year - $this->manufacturing_year;
    }
}