<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Baggage extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'baggage_tag',
        'weight_kg',
        'baggage_type',
        'special_handling',
        'current_location',
        'status',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
    ];

    /**
     * Get the booking for the baggage
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the tracking records for the baggage
     */
    public function trackingRecords(): HasMany
    {
        return $this->hasMany(BaggageTracking::class);
    }
}