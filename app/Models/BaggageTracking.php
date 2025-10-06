<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaggageTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'baggage_id',
        'location',
        'status',
        'scan_time',
        'notes',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
    ];

    /**
     * Get the baggage for the tracking record
     */
    public function baggage(): BelongsTo
    {
        return $this->belongsTo(Baggage::class);
    }
}