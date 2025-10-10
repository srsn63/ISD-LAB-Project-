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
        'check_in_method',
        'boarding_pass_number',
        'seat_number',
        'gate',
        'boarding_time',
        'priority_boarding',
        'status',
        'special_assistance',
        'terminal_number',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'boarding_time' => 'datetime:H:i',
        'priority_boarding' => 'boolean',
    ];

    /**
     * Get the booking for the check-in
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}