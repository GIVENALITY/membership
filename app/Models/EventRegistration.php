<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'member_id',
        'registration_code',
        'name',
        'email',
        'phone',
        'number_of_guests',
        'total_amount',
        'status',
        'special_requests',
        'guest_details',
        'registered_at',
        'confirmed_at',
        'cancelled_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'guest_details' => 'array',
        'registered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            if (empty($registration->registration_code)) {
                $registration->registration_code = self::generateRegistrationCode();
            }
            
            if (empty($registration->registered_at)) {
                $registration->registered_at = Carbon::now();
            }
        });
    }

    /**
     * Get the event for this registration
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the member for this registration (if applicable)
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Generate a unique registration code
     */
    public static function generateRegistrationCode(): string
    {
        do {
            $code = 'REG-' . strtoupper(Str::random(8));
        } while (self::where('registration_code', $code)->exists());

        return $code;
    }

    /**
     * Confirm the registration
     */
    public function confirm(): bool
    {
        if ($this->status === 'pending') {
            $this->update([
                'status' => 'confirmed',
                'confirmed_at' => Carbon::now()
            ]);
            return true;
        }
        
        return false;
    }

    /**
     * Cancel the registration
     */
    public function cancel(): bool
    {
        if (in_array($this->status, ['pending', 'confirmed'])) {
            $this->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now()
            ]);
            return true;
        }
        
        return false;
    }

    /**
     * Mark as attended
     */
    public function markAsAttended(): bool
    {
        if ($this->status === 'confirmed') {
            $this->update(['status' => 'attended']);
            return true;
        }
        
        return false;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'badge bg-warning',
            'confirmed' => 'badge bg-success',
            'cancelled' => 'badge bg-danger',
            'attended' => 'badge bg-primary',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get status text
     */
    public function getStatusText(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Check if registration is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if registration is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if registration is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if registration is attended
     */
    public function isAttended(): bool
    {
        return $this->status === 'attended';
    }

    /**
     * Scope for confirmed registrations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for pending registrations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for registrations by event
     */
    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope for registrations by member
     */
    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }
}
