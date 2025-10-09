<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_flight_id',
        'user_id',
        'booked_by_email',
        'seat_class',
        'quantity',
        'unit_price',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function flight(): BelongsTo
    {
        return $this->belongsTo(AdminFlight::class, 'admin_flight_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
