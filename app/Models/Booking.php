<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'passenger_id',
        'flight_id',
        'booking_status',
        'booking_date',
        'booking_class',
        'total_amount',
        'payment_status',
        'payment_method',
        'special_requests',
        'travel_insurance',
        'booked_by_email',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'travel_insurance' => 'boolean',
    ];

    /**
     * Get the passenger that owns the booking
     */
    public function passenger(): BelongsTo
    {
        return $this->belongsTo(Passenger::class);
    }

    /**
     * Get the flight for the booking
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * Get the payments for the booking
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the seat assignments for the booking
     */
    public function seatAssignments(): HasMany
    {
        return $this->hasMany(SeatAssignment::class);
    }

    /**
     * Get the check-ins for the booking
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * Get the baggage for the booking
     */
    public function baggage(): HasMany
    {
        return $this->hasMany(Baggage::class);
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->booking_status === 'confirmed';
    }

    /**
     * Check if booking is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->booking_status === 'cancelled';
    }

    /**
     * Check if booking is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Get booking status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->booking_status) {
            'confirmed' => 'green',
            'pending' => 'yellow',
            'cancelled' => 'red',
            'refunded' => 'gray',
            default => 'blue'
        };
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()
            ->where('payment_status', 'completed')
            ->sum('amount');
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total_amount - $this->total_paid);
    }

    /**
     * Check if booking has travel insurance
     */
    public function hasTravelInsurance(): bool
    {
        return $this->travel_insurance;
    }
}