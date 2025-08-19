<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsMultiplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'membership_type_id',
        'name',
        'description',
        'multiplier_type',
        'multiplier_value',
        'conditions',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'multiplier_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the hotel that owns this multiplier
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the membership type for this multiplier
     */
    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    /**
     * Check if this multiplier applies to a given member and context
     */
    public function appliesTo($member, $context = []): bool
    {
        if (!$this->is_active) {
            return false;
        }

        switch ($this->multiplier_type) {
            case 'membership_type':
                return $this->checkMembershipTypeCondition($member);
            
            case 'visit_frequency':
                return $this->checkVisitFrequencyCondition($member, $context);
            
            case 'spending_tier':
                return $this->checkSpendingTierCondition($member, $context);
            
            case 'time_based':
                return $this->checkTimeBasedCondition($context);
            
            case 'custom':
                return $this->checkCustomCondition($member, $context);
            
            default:
                return false;
        }
    }

    /**
     * Check membership type condition
     */
    private function checkMembershipTypeCondition($member): bool
    {
        if (!$this->membership_type_id) {
            return true; // Applies to all membership types
        }

        return $member->membership_type_id === $this->membership_type_id;
    }

    /**
     * Check visit frequency condition
     */
    private function checkVisitFrequencyCondition($member, $context): bool
    {
        $conditions = $this->conditions;
        $visitCount = $member->total_visits ?? 0;
        $consecutiveVisits = $member->consecutive_visits ?? 0;

        // Check total visits
        if (isset($conditions['min_total_visits']) && $visitCount < $conditions['min_total_visits']) {
            return false;
        }

        if (isset($conditions['max_total_visits']) && $visitCount > $conditions['max_total_visits']) {
            return false;
        }

        // Check consecutive visits
        if (isset($conditions['min_consecutive_visits']) && $consecutiveVisits < $conditions['min_consecutive_visits']) {
            return false;
        }

        if (isset($conditions['max_consecutive_visits']) && $consecutiveVisits > $conditions['max_consecutive_visits']) {
            return false;
        }

        return true;
    }

    /**
     * Check spending tier condition
     */
    private function checkSpendingTierCondition($member, $context): bool
    {
        $conditions = $this->conditions;
        $spendingAmount = $context['spending_amount'] ?? 0;
        $perPersonSpending = $context['per_person_spending'] ?? 0;

        // Check total spending
        if (isset($conditions['min_spending']) && $spendingAmount < $conditions['min_spending']) {
            return false;
        }

        if (isset($conditions['max_spending']) && $spendingAmount > $conditions['max_spending']) {
            return false;
        }

        // Check per person spending
        if (isset($conditions['min_per_person_spending']) && $perPersonSpending < $conditions['min_per_person_spending']) {
            return false;
        }

        if (isset($conditions['max_per_person_spending']) && $perPersonSpending > $conditions['max_per_person_spending']) {
            return false;
        }

        return true;
    }

    /**
     * Check time-based condition
     */
    private function checkTimeBasedCondition($context): bool
    {
        $conditions = $this->conditions;
        $currentTime = now();

        // Check specific time ranges
        if (isset($conditions['time_ranges'])) {
            foreach ($conditions['time_ranges'] as $range) {
                $startTime = $range['start_time'] ?? '00:00';
                $endTime = $range['end_time'] ?? '23:59';
                
                $currentTimeStr = $currentTime->format('H:i');
                if ($currentTimeStr >= $startTime && $currentTimeStr <= $endTime) {
                    return true;
                }
            }
        }

        // Check specific days of week
        if (isset($conditions['days_of_week'])) {
            $currentDay = $currentTime->format('l'); // Monday, Tuesday, etc.
            if (!in_array($currentDay, $conditions['days_of_week'])) {
                return false;
            }
        }

        // Check specific dates
        if (isset($conditions['specific_dates'])) {
            $currentDate = $currentTime->format('Y-m-d');
            if (!in_array($currentDate, $conditions['specific_dates'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check custom condition
     */
    private function checkCustomCondition($member, $context): bool
    {
        $conditions = $this->conditions;
        
        // Custom logic based on conditions
        // This can be extended based on specific business rules
        
        return true;
    }

    /**
     * Get the multiplier icon based on type
     */
    public function getIconAttribute(): string
    {
        return match($this->multiplier_type) {
            'membership_type' => 'ri-vip-crown-line',
            'visit_frequency' => 'ri-time-line',
            'spending_tier' => 'ri-money-dollar-circle-line',
            'time_based' => 'ri-calendar-line',
            'custom' => 'ri-settings-line',
            default => 'ri-star-line',
        };
    }

    /**
     * Get the multiplier color based on type
     */
    public function getColorAttribute(): string
    {
        return match($this->multiplier_type) {
            'membership_type' => '#ffc107',
            'visit_frequency' => '#17a2b8',
            'spending_tier' => '#28a745',
            'time_based' => '#6f42c1',
            'custom' => '#6c757d',
            default => '#007bff',
        };
    }
}
