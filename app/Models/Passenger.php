<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Passenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'nationality',
        'passport_number',
        'passport_expiry',
        'emergency_contact_name',
        'emergency_contact_phone',
        'meal_preference',
        'seat_preference',
        'frequent_flyer',
        'frequent_flyer_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
        'frequent_flyer' => 'boolean',
    ];

    /**
     * Get the bookings for the passenger
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the passenger's full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if passenger is a frequent flyer
     */
    public function isFrequentFlyer(): bool
    {
        return $this->frequent_flyer;
    }

    /**
     * Check if passport is expiring within given months
     */
    public function isPassportExpiringSoon(int $months = 6): bool
    {
        return $this->passport_expiry <= now()->addMonths($months);
    }
}