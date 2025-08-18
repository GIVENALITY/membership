<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'membership_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'allergies',
        'dietary_preferences',
        'special_requests',
        'additional_notes',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'address',
        'birth_date',
        'join_date',
        'membership_type_id',
        'status',
        'total_visits',
        'total_spent',
        'current_discount_rate',
        'total_points_earned',
        'total_points_used',
        'current_points_balance',
        'qualifies_for_discount',
        'consecutive_visits',
        'last_visit_date',
        'average_spending_per_visit',
        'last_visit_at',
        'card_image_path',
        'physical_card_status',
        'physical_card_issued_date',
        'physical_card_issued_by',
        'physical_card_notes',
        'physical_card_delivered_date',
        'physical_card_delivered_by',
        'expires_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
        'last_visit_at' => 'datetime',
        'last_visit_date' => 'date',
        'physical_card_issued_date' => 'date',
        'physical_card_delivered_date' => 'date',
        'expires_at' => 'date',
        'total_spent' => 'decimal:2',
        'current_discount_rate' => 'decimal:2',
        'average_spending_per_visit' => 'decimal:2',
        'qualifies_for_discount' => 'boolean',
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
     * Check if member has a virtual card
     */
    public function hasCard(): bool
    {
        return !empty($this->card_image_path);
    }

    /**
     * Get the card URL
     */
    public function getCardUrlAttribute(): ?string
    {
        if (!$this->card_image_path) {
            return null;
        }
        
        return \Storage::url($this->card_image_path);
    }

    /**
     * Check if physical card has been issued
     */
    public function hasPhysicalCard(): bool
    {
        return in_array($this->physical_card_status, ['issued', 'delivered', 'replaced']);
    }

    /**
     * Check if physical card has been delivered
     */
    public function hasPhysicalCardDelivered(): bool
    {
        return in_array($this->physical_card_status, ['delivered', 'replaced']);
    }

    /**
     * Get physical card status badge class
     */
    public function getPhysicalCardStatusBadgeClass(): string
    {
        return match($this->physical_card_status) {
            'not_issued' => 'bg-label-secondary',
            'issued' => 'bg-label-warning',
            'delivered' => 'bg-label-success',
            'lost' => 'bg-label-danger',
            'replaced' => 'bg-label-info',
            default => 'bg-label-secondary'
        };
    }

    /**
     * Get physical card status text
     */
    public function getPhysicalCardStatusText(): string
    {
        return match($this->physical_card_status) {
            'not_issued' => __('app.not_issued'),
            'issued' => __('app.issued'),
            'delivered' => __('app.delivered'),
            'lost' => __('app.lost'),
            'replaced' => __('app.replaced'),
            default => __('app.not_issued')
        };
    }

    /**
     * Get the member's membership type
     */
    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    /**
     * Get the hotel this member belongs to
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the member's points records
     */
    public function points(): HasMany
    {
        return $this->hasMany(MemberPoint::class);
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
     * Calculate discount rate based on points system and membership type progression
     */
    public function calculateDiscountRate(): float
    {
        // Get base discount from membership type progression
        $baseDiscount = $this->membershipType ? 
            $this->membershipType->calculateDiscountForVisits($this->total_visits) : 
            5.0;
        
        // If member qualifies for points-based discount (5+ points)
        if ($this->qualifies_for_discount) {
            return max($baseDiscount, 10.0); // Minimum 10% for qualified members
        }
        
        return $baseDiscount;
    }

    /**
     * Add points for a visit
     */
    public function addPoints($spendingAmount, $numberOfPeople, $diningVisitId = null)
    {
        $pointsEarned = MemberPoint::calculatePoints($spendingAmount, $numberOfPeople);
        $perPersonSpending = $spendingAmount / $numberOfPeople;
        
        // Check if this is a birthday visit
        $isBirthdayVisit = $this->isBirthdayVisit();
        
        // Create points record
        $pointRecord = $this->points()->create([
            'hotel_id' => $this->hotel_id,
            'dining_visit_id' => $diningVisitId,
            'points_earned' => $pointsEarned,
            'points_used' => 0,
            'points_balance' => $pointsEarned,
            'spending_amount' => $spendingAmount,
            'number_of_people' => $numberOfPeople,
            'per_person_spending' => $perPersonSpending,
            'qualifies_for_discount' => $this->current_points_balance + $pointsEarned >= 5,
            'is_birthday_visit' => $isBirthdayVisit,
            'notes' => $isBirthdayVisit ? 'Birthday visit - special treatment applied' : null
        ]);

        // Update member's points totals
        $this->increment('total_points_earned', $pointsEarned);
        $this->increment('current_points_balance', $pointsEarned);
        
        // Update consecutive visits
        $this->updateConsecutiveVisits();
        
        // Update average spending
        $this->updateAverageSpending();
        
        // Check if member now qualifies for discount
        if ($this->current_points_balance >= 5 && !$this->qualifies_for_discount) {
            $this->update(['qualifies_for_discount' => true]);
        }

        // Check if points should be reset based on membership type rules
        $this->checkAndResetPointsIfNeeded();

        return $pointRecord;
    }

    /**
     * Check and reset points if needed based on membership type rules
     */
    public function checkAndResetPointsIfNeeded()
    {
        if (!$this->membershipType) {
            return;
        }

        // Check if points should reset after redemption
        if ($this->membershipType->shouldResetPointsAfterRedemption()) {
            $threshold = $this->membershipType->getPointsResetThreshold();
            
            if ($threshold && $this->current_points_balance >= $threshold) {
                // Reset points to 0
                $this->update([
                    'current_points_balance' => 0,
                    'qualifies_for_discount' => false
                ]);

                // Log the reset in points history
                $this->points()->create([
                    'hotel_id' => $this->hotel_id,
                    'points_earned' => 0,
                    'points_used' => $this->current_points_balance,
                    'points_balance' => 0,
                    'spending_amount' => 0,
                    'number_of_people' => 0,
                    'per_person_spending' => 0,
                    'qualifies_for_discount' => false,
                    'is_birthday_visit' => false,
                    'notes' => "Points reset to 0 after reaching threshold of {$threshold} points"
                ]);
            }
        }
    }

    /**
     * Check if this is a birthday visit
     */
    public function isBirthdayVisit(): bool
    {
        if (!$this->birth_date) {
            return false;
        }

        $birthDate = \Carbon\Carbon::parse($this->birth_date);
        $today = \Carbon\Carbon::now();

        // Check if today is within 7 days of birthday
        return $birthDate->isBirthday($today) || 
               $birthDate->diffInDays($today, false) <= 7;
    }

    /**
     * Update consecutive visits count
     */
    public function updateConsecutiveVisits()
    {
        $lastVisit = $this->diningVisits()
            ->where('is_checked_out', true)
            ->orderBy('checked_out_at', 'desc')
            ->first();

        if (!$lastVisit) {
            $this->update(['consecutive_visits' => 1]);
            return;
        }

        $lastVisitDate = \Carbon\Carbon::parse($lastVisit->checked_out_at);
        $today = \Carbon\Carbon::now();

        // If last visit was yesterday, increment consecutive visits
        if ($lastVisitDate->isYesterday()) {
            $this->increment('consecutive_visits');
        } else {
            // Reset to 1 if not consecutive
            $this->update(['consecutive_visits' => 1]);
        }
    }

    /**
     * Update average spending per visit
     */
    public function updateAverageSpending()
    {
        $totalSpent = $this->diningVisits()
            ->where('is_checked_out', true)
            ->sum('amount_spent');
        
        $visitCount = $this->diningVisits()
            ->where('is_checked_out', true)
            ->count();

        $averageSpending = $visitCount > 0 ? $totalSpent / $visitCount : 0;
        
        $this->update(['average_spending_per_visit' => $averageSpending]);
    }

    /**
     * Get special discount percentage for qualified members
     */
    public function getSpecialDiscountPercentage(int $currentVisitCount = null): float
    {
        // Use provided visit count or fall back to stored total
        $visitCount = $currentVisitCount ?? $this->total_visits;
        
        $baseDiscount = $this->membershipType ? 
            $this->membershipType->calculateDiscountForVisits($visitCount) : 
            5.0;
        $specialDiscount = $baseDiscount;

        // Check for consecutive visit bonus (membership type specific)
        if ($this->membershipType && 
            $this->membershipType->qualifiesForConsecutiveBonus() &&
            $this->membershipType->consecutive_visits_for_bonus &&
            $this->consecutive_visits >= $this->membershipType->consecutive_visits_for_bonus &&
            $this->membershipType->consecutive_visit_bonus_rate) {
            $specialDiscount = max($specialDiscount, $this->membershipType->consecutive_visit_bonus_rate);
        }

        // Check for birthday discount (membership type specific)
        if ($this->isBirthdayVisit() && 
            $this->membershipType && 
            $this->membershipType->qualifiesForBirthdayDiscount() &&
            $this->membershipType->birthday_discount_rate) {
            $specialDiscount = max($specialDiscount, $this->membershipType->birthday_discount_rate);
        }

        return $specialDiscount;
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