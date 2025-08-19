<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'type',
        'rules',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rules' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the hotel that owns this configuration
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Calculate points based on this configuration
     */
    public function calculatePoints($data = []): int
    {
        if (!$this->is_active) {
            return 0;
        }

        switch ($this->type) {
            case 'dining_visit':
                return $this->calculateDiningVisitPoints($data);
            
            case 'special_event':
                return $this->calculateSpecialEventPoints($data);
            
            case 'referral':
                return $this->calculateReferralPoints($data);
            
            case 'social_media':
                return $this->calculateSocialMediaPoints($data);
            
            case 'birthday_bonus':
                return $this->calculateBirthdayBonusPoints($data);
            
            case 'holiday_bonus':
                return $this->calculateHolidayBonusPoints($data);
            
            case 'custom':
                return $this->calculateCustomPoints($data);
            
            default:
                return 0;
        }
    }

    /**
     * Calculate points for dining visits
     */
    private function calculateDiningVisitPoints($data): int
    {
        $rules = $this->rules;
        $spendingAmount = $data['spending_amount'] ?? 0;
        $numberOfPeople = $data['number_of_people'] ?? 1;
        $perPersonSpending = $spendingAmount / $numberOfPeople;

        // Check minimum spending per person
        $minSpendingPerPerson = $rules['min_spending_per_person'] ?? 0;
        if ($perPersonSpending < $minSpendingPerPerson) {
            return 0;
        }

        // Calculate base points
        $basePoints = 0;
        
        // Points per person
        if (isset($rules['points_per_person'])) {
            $maxPeople = $rules['max_people'] ?? $numberOfPeople;
            $eligiblePeople = min($numberOfPeople, $maxPeople);
            $basePoints += $rules['points_per_person'] * $eligiblePeople;
        }

        // Points per amount spent
        if (isset($rules['points_per_amount'])) {
            $pointsPerAmount = $rules['points_per_amount'];
            $basePoints += floor($spendingAmount / $pointsPerAmount);
        }

        // Points per person spending
        if (isset($rules['points_per_person_spending'])) {
            $pointsPerPersonSpending = $rules['points_per_person_spending'];
            $basePoints += floor($perPersonSpending / $pointsPerPersonSpending) * $numberOfPeople;
        }

        return (int) $basePoints;
    }

    /**
     * Calculate points for special events
     */
    private function calculateSpecialEventPoints($data): int
    {
        $rules = $this->rules;
        return $rules['base_points'] ?? 0;
    }

    /**
     * Calculate points for referrals
     */
    private function calculateReferralPoints($data): int
    {
        $rules = $this->rules;
        $referralType = $data['referral_type'] ?? 'new_member';
        
        return $rules['points_by_type'][$referralType] ?? $rules['base_points'] ?? 0;
    }

    /**
     * Calculate points for social media engagement
     */
    private function calculateSocialMediaPoints($data): int
    {
        $rules = $this->rules;
        $platform = $data['platform'] ?? 'general';
        
        return $rules['points_by_platform'][$platform] ?? $rules['base_points'] ?? 0;
    }

    /**
     * Calculate birthday bonus points
     */
    private function calculateBirthdayBonusPoints($data): int
    {
        $rules = $this->rules;
        return $rules['bonus_points'] ?? 0;
    }

    /**
     * Calculate holiday bonus points
     */
    private function calculateHolidayBonusPoints($data): int
    {
        $rules = $this->rules;
        $holiday = $data['holiday'] ?? 'general';
        
        return $rules['points_by_holiday'][$holiday] ?? $rules['base_points'] ?? 0;
    }

    /**
     * Calculate custom points
     */
    private function calculateCustomPoints($data): int
    {
        $rules = $this->rules;
        // Custom calculation logic based on rules
        return $rules['base_points'] ?? 0;
    }

    /**
     * Get the configuration icon based on type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'dining_visit' => 'ri-restaurant-line',
            'special_event' => 'ri-calendar-event-line',
            'referral' => 'ri-user-add-line',
            'social_media' => 'ri-share-line',
            'birthday_bonus' => 'ri-cake-line',
            'holiday_bonus' => 'ri-gift-line',
            'custom' => 'ri-settings-line',
            default => 'ri-star-line',
        };
    }

    /**
     * Get the configuration color based on type
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'dining_visit' => '#28a745',
            'special_event' => '#ffc107',
            'referral' => '#17a2b8',
            'social_media' => '#6f42c1',
            'birthday_bonus' => '#fd7e14',
            'holiday_bonus' => '#e83e8c',
            'custom' => '#6c757d',
            default => '#007bff',
        };
    }
}
