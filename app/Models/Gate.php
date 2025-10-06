<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gate extends Model
{
    use HasFactory;

    protected $fillable = [
        'airport_id',
        'gate_number',
        'terminal',
        'gate_type',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the airport that owns the gate
     */
    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }
}