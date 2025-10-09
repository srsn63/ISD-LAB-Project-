<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'code',
        'name',
        'status',
        'today_count',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'today_count' => 'integer',
    ];
}
