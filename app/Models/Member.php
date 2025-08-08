<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'birth_date',
        'join_date',
        'membership_type',
        'status',
        'total_visits',
        'total_spent',
        'current_discount_rate',
        'last_visit_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
        'last_visit_at' => 'datetime',
        'total_spent' => 'decimal:2',
        'current_discount_rate' => 'decimal:2',
    ];

    /**
     * Get the member's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the member's dining visits
     */
    public function diningVisits(): HasMany
    {
        return $this->hasMany(DiningVisit::class);
    }

    /**
     * Get the member's presence records
     */
    public function presenceRecords(): HasMany
    {
        return $this->hasMany(MemberPresence::class);
    }

    /**
     * Get the member's email notifications
     */
    public function emailNotifications(): HasMany
    {
        return $this->hasMany(EmailNotification::class);
    }

    /**
     * Check if member is present today
     */
    public function isPresentToday(): bool
    {
        return $this->presenceRecords()
            ->where('date', now()->toDateString())
            ->where('status', 'present')
            ->exists();
    }

    /**
     * Calculate discount rate based on visit count
     */
    public function calculateDiscountRate(): float
    {
        if ($this->total_visits >= 21) {
            return 20.0;
        } elseif ($this->total_visits >= 11) {
            return 15.0;
        } elseif ($this->total_visits >= 6) {
            return 10.0;
        } else {
            return 5.0;
        }
    }

    /**
     * Generate next membership ID
     */
    public static function generateMembershipId(): string
    {
        $lastMember = self::orderBy('id', 'desc')->first();
        
        if (!$lastMember) {
            return 'MS001';
        }

        $lastNumber = (int) substr($lastMember->membership_id, 2);
        $nextNumber = $lastNumber + 1;
        
        return 'MS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
} 