<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrewMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'hire_date',
        'license_number',
        'license_expiry',
        'base_location',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'license_expiry' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the flight assignments for the crew member
     */
    public function flightAssignments(): HasMany
    {
        return $this->hasMany(FlightCrewAssignment::class);
    }

    /**
     * Get the crew member's full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if crew member is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if license is expiring soon
     */
    public function isLicenseExpiringSoon(int $days = 30): bool
    {
        return $this->license_expiry && 
               $this->license_expiry <= now()->addDays($days) &&
               $this->license_expiry > now();
    }

    /**
     * Check if license is expired
     */
    public function isLicenseExpired(): bool
    {
        return $this->license_expiry && 
               $this->license_expiry < now();
    }

    /**
     * Get years of service
     */
    public function getYearsOfServiceAttribute(): int
    {
        return now()->diffInYears($this->hire_date);
    }

    /**
     * Check if crew member is available for a specific date
     */
    public function isAvailableForDate(string $date): bool
    {
        return $this->is_active && 
               !$this->flightAssignments()
                    ->whereHas('flight', function ($query) use ($date) {
                        $query->where('flight_date', $date);
                    })->exists();
    }

    /**
     * Get crew member's current flights
     */
    public function getCurrentFlights()
    {
        return $this->flightAssignments()
            ->with('flight')
            ->whereHas('flight', function ($query) {
                $query->where('flight_date', today())
                      ->whereIn('status', ['scheduled', 'boarding', 'departed', 'in_flight']);
            })->get();
    }

    /**
     * Get upcoming flights
     */
    public function getUpcomingFlights(int $days = 7)
    {
        return $this->flightAssignments()
            ->with('flight')
            ->whereHas('flight', function ($query) use ($days) {
                $query->where('flight_date', '>', today())
                      ->where('flight_date', '<=', today()->addDays($days));
            })
            ->orderBy('flight_date')
            ->get();
    }
}