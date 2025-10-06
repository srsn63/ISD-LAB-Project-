<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'check_in_time',
        'boarding_pass_number',
        'gate_number',
        'seat_number',
        'baggage_tags',
        'special_assistance',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'baggage_tags' => 'array',
    ];

    /**
     * Get the booking for the check-in
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}