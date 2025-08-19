<?php

namespace App\Services;

use App\Models\Member;
use App\Models\PointsConfiguration;
use App\Models\PointsMultiplier;
use App\Models\PointsTier;

class PointsCalculationService
{
    /**
     * Calculate total points for a member based on all active configurations
     */
    public function calculateTotalPoints(Member $member, array $context = []): array
    {
        $basePoints = 0;
        $multiplier = 1.0;
        $appliedMultipliers = [];
        $appliedConfigurations = [];

        // Get all active points configurations for this hotel
        $configurations = PointsConfiguration::where('hotel_id', $member->hotel_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Calculate base points from each configuration
        foreach ($configurations as $config) {
            $points = $config->calculatePoints($context);
            if ($points > 0) {
                $basePoints += $points;
                $appliedConfigurations[] = [
                    'configuration' => $config,
                    'points' => $points,
                ];
            }
        }

        // Apply multipliers
        $multipliers = PointsMultiplier::where('hotel_id', $member->hotel_id)
            ->where('is_active', true)
            ->get();

        foreach ($multipliers as $mult) {
            if ($mult->appliesTo($member, $context)) {
                $multiplier *= $mult->multiplier_value;
                $appliedMultipliers[] = [
                    'multiplier' => $mult,
                    'value' => $mult->multiplier_value,
                ];
            }
        }

        // Apply tier multiplier
        $tier = PointsTier::getTierForMember($member);
        if ($tier) {
            $multiplier *= $tier->multiplier;
            $appliedMultipliers[] = [
                'multiplier' => $tier,
                'value' => $tier->multiplier,
                'type' => 'tier',
            ];
        }

        $totalPoints = (int) ($basePoints * $multiplier);

        return [
            'base_points' => $basePoints,
            'multiplier' => $multiplier,
            'total_points' => $totalPoints,
            'applied_configurations' => $appliedConfigurations,
            'applied_multipliers' => $appliedMultipliers,
            'tier' => $tier,
        ];
    }

    /**
     * Calculate points for a dining visit
     */
    public function calculateDiningVisitPoints(Member $member, float $spendingAmount, int $numberOfPeople): array
    {
        $context = [
            'spending_amount' => $spendingAmount,
            'number_of_people' => $numberOfPeople,
            'per_person_spending' => $spendingAmount / $numberOfPeople,
            'visit_type' => 'dining',
        ];

        return $this->calculateTotalPoints($member, $context);
    }

    /**
     * Calculate points for a special event
     */
    public function calculateSpecialEventPoints(Member $member, string $eventType): array
    {
        $context = [
            'event_type' => $eventType,
            'visit_type' => 'special_event',
        ];

        return $this->calculateTotalPoints($member, $context);
    }

    /**
     * Calculate points for a referral
     */
    public function calculateReferralPoints(Member $member, string $referralType): array
    {
        $context = [
            'referral_type' => $referralType,
            'visit_type' => 'referral',
        ];

        return $this->calculateTotalPoints($member, $context);
    }

    /**
     * Calculate points for social media engagement
     */
    public function calculateSocialMediaPoints(Member $member, string $platform): array
    {
        $context = [
            'platform' => $platform,
            'visit_type' => 'social_media',
        ];

        return $this->calculateTotalPoints($member, $context);
    }

    /**
     * Calculate birthday bonus points
     */
    public function calculateBirthdayBonusPoints(Member $member): array
    {
        $context = [
            'visit_type' => 'birthday_bonus',
        ];

        return $this->calculateTotalPoints($member, $context);
    }

    /**
     * Calculate holiday bonus points
     */
    public function calculateHolidayBonusPoints(Member $member, string $holiday): array
    {
        $context = [
            'holiday' => $holiday,
            'visit_type' => 'holiday_bonus',
        ];

        return $this->calculateTotalPoints($member, $context);
    }

    /**
     * Get points summary for a member
     */
    public function getMemberPointsSummary(Member $member): array
    {
        $currentTier = PointsTier::getTierForMember($member);
        $nextTier = PointsTier::getNextTierForMember($member);
        $currentPoints = $member->current_points_balance ?? 0;

        $summary = [
            'current_points' => $currentPoints,
            'total_points_earned' => $member->total_points_earned ?? 0,
            'total_points_used' => $member->total_points_used ?? 0,
            'current_tier' => $currentTier,
            'next_tier' => $nextTier,
            'points_to_next_tier' => null,
            'tier_progress' => 0,
        ];

        if ($nextTier) {
            $summary['points_to_next_tier'] = $nextTier->min_points - $currentPoints;
            
            // Calculate progress to next tier
            $previousTier = PointsTier::where('hotel_id', $member->hotel_id)
                ->where('is_active', true)
                ->where('min_points', '<', $nextTier->min_points)
                ->orderBy('min_points', 'desc')
                ->first();

            $tierRange = $nextTier->min_points - ($previousTier ? $previousTier->min_points : 0);
            $progress = $currentPoints - ($previousTier ? $previousTier->min_points : 0);
            
            if ($tierRange > 0) {
                $summary['tier_progress'] = min(100, max(0, ($progress / $tierRange) * 100));
            }
        }

        return $summary;
    }

    /**
     * Get all available points configurations for a hotel
     */
    public function getAvailableConfigurations(int $hotelId): array
    {
        $configurations = PointsConfiguration::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $multipliers = PointsMultiplier::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->get();

        $tiers = PointsTier::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->orderBy('min_points')
            ->get();

        return [
            'configurations' => $configurations,
            'multipliers' => $multipliers,
            'tiers' => $tiers,
        ];
    }

    /**
     * Test points calculation with sample data
     */
    public function testCalculation(int $hotelId, array $sampleData = []): array
    {
        $configurations = PointsConfiguration::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->get();

        $results = [];
        $totalPoints = 0;

        foreach ($configurations as $config) {
            $points = $config->calculatePoints($sampleData);
            if ($points > 0) {
                $results[] = [
                    'configuration' => $config,
                    'points' => $points,
                    'description' => $this->getConfigurationDescription($config, $sampleData),
                ];
                $totalPoints += $points;
            }
        }

        return [
            'total_points' => $totalPoints,
            'breakdown' => $results,
            'sample_data' => $sampleData,
        ];
    }

    /**
     * Get human-readable description of configuration
     */
    private function getConfigurationDescription(PointsConfiguration $config, array $data): string
    {
        switch ($config->type) {
            case 'dining_visit':
                $spending = $data['spending_amount'] ?? 0;
                $people = $data['number_of_people'] ?? 1;
                return "Dining visit: TZS " . number_format($spending) . " for {$people} people";
            
            case 'special_event':
                $eventType = $data['event_type'] ?? 'Unknown';
                return "Special event: {$eventType}";
            
            case 'referral':
                $referralType = $data['referral_type'] ?? 'Unknown';
                return "Referral: {$referralType}";
            
            case 'social_media':
                $platform = $data['platform'] ?? 'Unknown';
                return "Social media: {$platform}";
            
            case 'birthday_bonus':
                return "Birthday bonus";
            
            case 'holiday_bonus':
                $holiday = $data['holiday'] ?? 'Unknown';
                return "Holiday bonus: {$holiday}";
            
            default:
                return $config->name;
        }
    }
}
