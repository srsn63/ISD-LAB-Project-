<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_status',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the booking that owns the payment
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    /**
     * Check if payment is refunded
     */
    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    /**
     * Get payment status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'completed' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            'refunded' => 'gray',
            default => 'blue'
        };
    }

    /**
     * Get masked card number (for display)
     */
    public function getMaskedCardNumberAttribute(): string
    {
        if (!$this->transaction_id) {
            return 'N/A';
        }
        
        // If transaction_id contains card info, mask it
        if (strlen($this->transaction_id) >= 12) {
            return '**** **** **** ' . substr($this->transaction_id, -4);
        }
        
        return $this->transaction_id;
    }
}