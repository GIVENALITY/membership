<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'min_points',
        'max_points',
        'multiplier',
        'benefits',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'benefits' => 'array',
        'multiplier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the hotel that owns this tier
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Check if a member qualifies for this tier
     */
    public function memberQualifies($member): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $currentPoints = $member->current_points_balance ?? 0;
        
        if ($currentPoints < $this->min_points) {
            return false;
        }

        if ($this->max_points && $currentPoints > $this->max_points) {
            return false;
        }

        return true;
    }

    /**
     * Get the tier for a given member
     */
    public static function getTierForMember($member)
    {
        return static::where('hotel_id', $member->hotel_id)
            ->where('is_active', true)
            ->where('min_points', '<=', $member->current_points_balance ?? 0)
            ->where(function($query) use ($member) {
                $query->whereNull('max_points')
                      ->orWhere('max_points', '>=', $member->current_points_balance ?? 0);
            })
            ->orderBy('min_points', 'desc')
            ->first();
    }

    /**
     * Get the next tier for a member
     */
    public static function getNextTierForMember($member)
    {
        $currentPoints = $member->current_points_balance ?? 0;
        
        return static::where('hotel_id', $member->hotel_id)
            ->where('is_active', true)
            ->where('min_points', '>', $currentPoints)
            ->orderBy('min_points', 'asc')
            ->first();
    }

    /**
     * Get the tier icon based on points range
     */
    public function getIconAttribute(): string
    {
        if ($this->min_points >= 100) {
            return 'ri-vip-crown-line';
        } elseif ($this->min_points >= 50) {
            return 'ri-star-line';
        } elseif ($this->min_points >= 20) {
            return 'ri-heart-line';
        } else {
            return 'ri-user-line';
        }
    }

    /**
     * Get the tier color based on points range
     */
    public function getColorAttribute(): string
    {
        if ($this->min_points >= 100) {
            return '#ffc107'; // Gold
        } elseif ($this->min_points >= 50) {
            return '#6f42c1'; // Purple
        } elseif ($this->min_points >= 20) {
            return '#e83e8c'; // Pink
        } else {
            return '#17a2b8'; // Blue
        }
    }

    /**
     * Get the tier badge class
     */
    public function getBadgeClassAttribute(): string
    {
        if ($this->min_points >= 100) {
            return 'bg-label-warning';
        } elseif ($this->min_points >= 50) {
            return 'bg-label-primary';
        } elseif ($this->min_points >= 20) {
            return 'bg-label-danger';
        } else {
            return 'bg-label-info';
        }
    }

    /**
     * Get formatted points range
     */
    public function getPointsRangeAttribute(): string
    {
        if ($this->max_points) {
            return "{$this->min_points} - {$this->max_points} points";
        } else {
            return "{$this->min_points}+ points";
        }
    }

    /**
     * Get formatted multiplier
     */
    public function getFormattedMultiplierAttribute(): string
    {
        return number_format($this->multiplier, 1) . 'x';
    }
}
