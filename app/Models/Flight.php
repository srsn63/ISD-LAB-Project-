<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'airline_id',
        'aircraft_id',
        'route_id',
        'flight_date',
        'scheduled_departure',
        'scheduled_arrival',
        'actual_departure',
        'actual_arrival',
        'status',
        'departure_gate',
        'arrival_gate',
        'delay_reason',
        'available_seats',
        'base_price',
    ];

    protected $casts = [
        'flight_date' => 'date',
        'scheduled_departure' => 'datetime:H:i',
        'scheduled_arrival' => 'datetime:H:i',
        'actual_departure' => 'datetime:H:i',
        'actual_arrival' => 'datetime:H:i',
        'available_seats' => 'integer',
        'base_price' => 'decimal:2',
    ];

    /**
     * Get the airline that owns the flight
     */
    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }

    /**
     * Get the aircraft for the flight
     */
    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    /**
     * Get the route for the flight
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get the bookings for the flight
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the status updates for the flight
     */
    public function statusUpdates(): HasMany
    {
        return $this->hasMany(FlightStatusUpdate::class);
    }

    /**
     * Get the crew assignments for the flight
     */
    public function crewAssignments(): HasMany
    {
        return $this->hasMany(FlightCrewAssignment::class);
    }

    /**
     * Check if flight is delayed
     */
    public function isDelayed(): bool
    {
        return $this->status === 'delayed' || 
               ($this->actual_departure && $this->actual_departure > $this->scheduled_departure);
    }

    /**
     * Check if flight is on time
     */
    public function isOnTime(): bool
    {
        return !$this->isDelayed() && $this->status !== 'cancelled';
    }

    /**
     * Get flight duration in minutes
     */
    public function getDurationAttribute(): int
    {
        if ($this->actual_departure && $this->actual_arrival) {
            return $this->actual_departure->diffInMinutes($this->actual_arrival);
        }
        
        return $this->scheduled_departure->diffInMinutes($this->scheduled_arrival);
    }

    /**
     * Get delay duration in minutes
     */
    public function getDelayDurationAttribute(): int
    {
        if ($this->actual_departure && $this->scheduled_departure) {
            return max(0, $this->scheduled_departure->diffInMinutes($this->actual_departure));
        }
        
        return 0;
    }

    /**
     * Check if flight has available seats
     */
    public function hasAvailableSeats(int $requested = 1): bool
    {
        return $this->available_seats >= $requested;
    }

    /**
     * Get occupancy rate
     */
    public function getOccupancyRateAttribute(): float
    {
        $totalSeats = $this->aircraft->total_seats ?? 0;
        if ($totalSeats === 0) return 0;
        
        $occupiedSeats = $totalSeats - $this->available_seats;
        return round(($occupiedSeats / $totalSeats) * 100, 2);
    }
}