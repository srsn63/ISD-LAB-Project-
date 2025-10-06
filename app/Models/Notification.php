<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_id',
        'recipient_type',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'scheduled_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Check if notification is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->scheduled_at && $this->scheduled_at > now();
    }

    /**
     * Get notification priority color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->type) {
            'flight_status_update' => 'blue',
            'booking_confirmation' => 'green',
            'payment_confirmation' => 'green',
            'flight_delay' => 'orange',
            'flight_cancellation' => 'red',
            'maintenance_alert' => 'yellow',
            'license_expiry' => 'orange',
            'system_alert' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get notification icon
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'flight_status_update' => '✈️',
            'booking_confirmation' => '✅',
            'payment_confirmation' => '💳',
            'flight_delay' => '⏰',
            'flight_cancellation' => '❌',
            'maintenance_alert' => '🔧',
            'license_expiry' => '📋',
            'system_alert' => '⚠️',
            default => '📢'
        };
    }

    /**
     * Get time since notification was created
     */
    public function getTimeSinceAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for notifications by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for notifications by recipient
     */
    public function scopeForRecipient($query, int $recipientId, string $recipientType)
    {
        return $query->where('recipient_id', $recipientId)
                    ->where('recipient_type', $recipientType);
    }
}