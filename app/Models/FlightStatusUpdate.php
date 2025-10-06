<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightStatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_id',
        'old_status',
        'new_status',
        'reason',
        'updated_by',
    ];

    /**
     * Get the flight for the status update
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }
}