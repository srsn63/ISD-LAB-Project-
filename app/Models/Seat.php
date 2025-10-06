<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'aircraft_id',
        'seat_number',
        'seat_class',
        'seat_type',
        'is_available',
        'has_extra_legroom',
        'extra_fee',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'has_extra_legroom' => 'boolean',
        'extra_fee' => 'decimal:2',
    ];

    /**
     * Get the aircraft that owns the seat
     */
    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    /**
     * Get the seat assignments for the seat
     */
    public function seatAssignments(): HasMany
    {
        return $this->hasMany(SeatAssignment::class);
    }

    /**
     * Check if seat is available
     */
    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    /**
     * Check if seat is premium (has extra legroom)
     */
    public function isPremium(): bool
    {
        return $this->has_extra_legroom;
    }

    /**
     * Check if seat is window
     */
    public function isWindow(): bool
    {
        return $this->seat_type === 'window';
    }

    /**
     * Check if seat is aisle
     */
    public function isAisle(): bool
    {
        return $this->seat_type === 'aisle';
    }

    /**
     * Check if seat is middle
     */
    public function isMiddle(): bool
    {
        return $this->seat_type === 'middle';
    }

    /**
     * Get seat class label
     */
    public function getClassLabelAttribute(): string
    {
        return match($this->seat_class) {
            'first' => 'First Class',
            'business' => 'Business Class',
            'economy' => 'Economy Class',
            default => 'Unknown'
        };
    }

    /**
     * Get seat type icon
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->seat_type) {
            'window' => '🪟',
            'aisle' => '🚶',
            'middle' => '👥',
            default => '💺'
        };
    }

    /**
     * Get total price including extra fees
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->extra_fee;
    }

    /**
     * Check if seat is assigned for a specific flight
     */
    public function isAssignedForFlight(int $flightId): bool
    {
        return $this->seatAssignments()
            ->whereHas('booking', function ($query) use ($flightId) {
                $query->where('flight_id', $flightId)
                      ->where('booking_status', '!=', 'cancelled');
            })->exists();
    }
}