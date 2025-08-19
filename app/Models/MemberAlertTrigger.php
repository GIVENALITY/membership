<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberAlertTrigger extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_alert_id',
        'member_id',
        'hotel_id',
        'trigger_data',
        'status',
        'triggered_at',
        'acknowledged_at',
        'resolved_at',
        'acknowledged_by',
        'notes',
    ];

    protected $casts = [
        'trigger_data' => 'array',
        'triggered_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the alert that triggered this
     */
    public function alert(): BelongsTo
    {
        return $this->belongsTo(MemberAlert::class, 'member_alert_id');
    }

    /**
     * Get the member that triggered this alert
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the hotel
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the user who acknowledged this alert
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Acknowledge this alert
     */
    public function acknowledge(User $user, string $notes = null): void
    {
        $this->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
            'acknowledged_by' => $user->id,
            'notes' => $notes,
        ]);
    }

    /**
     * Resolve this alert
     */
    public function resolve(string $notes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-label-warning',
            'acknowledged' => 'bg-label-info',
            'resolved' => 'bg-label-success',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get the status text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'acknowledged' => 'Acknowledged',
            'resolved' => 'Resolved',
            default => 'Unknown',
        };
    }

    /**
     * Get the time since triggered
     */
    public function getTimeSinceTriggeredAttribute(): string
    {
        return $this->triggered_at->diffForHumans();
    }
}
