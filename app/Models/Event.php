<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'title',
        'description',
        'image',
        'start_date',
        'end_date',
        'location',
        'max_capacity',
        'price',
        'is_public',
        'is_active',
        'status',
        'settings'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'settings' => 'array'
    ];

    /**
     * Get the hotel that owns the event
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the registrations for this event
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Get confirmed registrations for this event
     */
    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'confirmed');
    }

    /**
     * Get pending registrations for this event
     */
    public function pendingRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'pending');
    }

    /**
     * Check if event is full
     */
    public function isFull(): bool
    {
        if (!$this->max_capacity) {
            return false;
        }
        
        return $this->confirmedRegistrations()->sum('number_of_guests') >= $this->max_capacity;
    }

    /**
     * Get available spots
     */
    public function getAvailableSpots(): int
    {
        if (!$this->max_capacity) {
            return -1; // Unlimited
        }
        
        $registeredGuests = $this->confirmedRegistrations()->sum('number_of_guests');
        return max(0, $this->max_capacity - $registeredGuests);
    }

    /**
     * Check if event is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    /**
     * Check if event is ongoing
     */
    public function isOngoing(): bool
    {
        $now = Carbon::now();
        return $this->start_date->lte($now) && $this->end_date->gte($now);
    }

    /**
     * Check if event is past
     */
    public function isPast(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Get event status text
     */
    public function getStatusText(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        
        if ($this->status === 'cancelled') {
            return 'Cancelled';
        }
        
        if ($this->isPast()) {
            return 'Completed';
        }
        
        if ($this->isOngoing()) {
            return 'Ongoing';
        }
        
        if ($this->isUpcoming()) {
            return 'Upcoming';
        }
        
        return ucfirst($this->status);
    }

    /**
     * Get event status badge class
     */
    public function getStatusBadgeClass(): string
    {
        if (!$this->is_active) {
            return 'badge bg-secondary';
        }
        
        if ($this->status === 'cancelled') {
            return 'badge bg-danger';
        }
        
        if ($this->isPast()) {
            return 'badge bg-success';
        }
        
        if ($this->isOngoing()) {
            return 'badge bg-primary';
        }
        
        if ($this->isUpcoming()) {
            return 'badge bg-info';
        }
        
        return 'badge bg-warning';
    }

    /**
     * Scope for active events
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public events
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for published events
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', Carbon::now());
    }

    /**
     * Scope for events by hotel
     */
    public function scopeByHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }
}
