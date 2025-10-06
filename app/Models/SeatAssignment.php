<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'seat_id',
        'passenger_name',
        'assignment_date',
    ];

    protected $casts = [
        'assignment_date' => 'datetime',
    ];

    /**
     * Get the booking for the seat assignment
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the seat for the assignment
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}