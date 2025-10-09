<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'airline',
        'status',
        'origin',
        'destination',
        'departure_at',
        'arrival_at',
        'price',
        'seats',
        'first_class_seats',
        'business_class_seats',
        'economy_class_seats',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'seats' => 'integer',
        'first_class_seats' => 'integer',
        'business_class_seats' => 'integer',
        'economy_class_seats' => 'integer',
        'departure_at' => 'datetime',
        'arrival_at' => 'datetime',
    ];
}
