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
    ];
}
