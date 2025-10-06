<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightCrewAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_id',
        'crew_member_id',
        'role',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the flight for the crew assignment
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * Get the crew member for the assignment
     */
    public function crewMember(): BelongsTo
    {
        return $this->belongsTo(CrewMember::class);
    }
}