<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipType extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'price',
        'billing_cycle',
        'perks',
        'max_visits_per_month',
        'discount_rate',
        'discount_progression',
        'points_required_for_discount',
        'has_special_birthday_discount',
        'birthday_discount_rate',
        'has_consecutive_visit_bonus',
        'consecutive_visits_for_bonus',
        'consecutive_visit_bonus_rate',
        'points_reset_after_redemption',
        'points_reset_threshold',
        'points_reset_notes',
        'is_active',
        'sort_order',
        'card_template_image',
        'card_field_mappings',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'perks' => 'array',
        'discount_progression' => 'array',
        'card_field_mappings' => 'array',
        'discount_rate' => 'decimal:2',
        'birthday_discount_rate' => 'decimal:2',
        'consecutive_visit_bonus_rate' => 'decimal:2',
        'has_special_birthday_discount' => 'boolean',
        'has_consecutive_visit_bonus' => 'boolean',
        'points_reset_after_redemption' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Ensure perks attribute is always an array when accessed
     */
    public function getPerksAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * Get members with this membership type
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get the hotel this membership type belongs to
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get formatted price with billing cycle
     */
    public function getFormattedPriceAttribute(): string
    {
        $cycle = $this->billing_cycle === 'monthly' ? 'month' : 'year';
        return 'TZS ' . number_format($this->price) . ' per ' . $cycle;
    }

    /**
     * Get perks as HTML list
     */
    public function getPerksListAttribute(): string
    {
        if (empty($this->perks)) {
            return 'No perks defined';
        }

        $html = '<ul class="list-unstyled mb-0">';
        foreach ($this->perks as $perk) {
            $html .= '<li><i class="icon-base ri ri-check-line text-success me-2"></i>' . htmlspecialchars($perk) . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Get visits limit text
     */
    public function getVisitsLimitTextAttribute(): string
    {
        if ($this->max_visits_per_month === null) {
            return 'Unlimited visits';
        }
        return $this->max_visits_per_month . ' visits per month';
    }

    /**
     * Scope for active membership types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    /**
     * Get discount progression as HTML
     */
    public function getDiscountProgressionHtmlAttribute(): string
    {
        if (empty($this->discount_progression)) {
            return '<span class="text-muted">No progression defined</span>';
        }

        $html = '<ul class="list-unstyled mb-0">';
        foreach ($this->discount_progression as $progression) {
            $visits = $progression['visits'] ?? 0;
            $discount = $progression['discount'] ?? 0;
            $html .= '<li><i class="icon-base ri ri-arrow-right-line text-primary me-2"></i>';
            $html .= '<strong>' . $visits . ' visits:</strong> ' . $discount . '% discount</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Calculate discount rate based on visit count
     */
    public function calculateDiscountForVisits(int $visitCount): float
    {
        if (empty($this->discount_progression)) {
            return $this->discount_rate;
        }

        // Sort progression by visits (ascending)
        $progression = collect($this->discount_progression)->sortBy('visits');
        
        $applicableDiscount = $this->discount_rate; // Base discount

        foreach ($progression as $prog) {
            if ($visitCount >= ($prog['visits'] ?? 0)) {
                $applicableDiscount = $prog['discount'] ?? $applicableDiscount;
            }
        }

        return $applicableDiscount;
    }

    /**
     * Get next discount milestone
     */
    public function getNextDiscountMilestone(int $currentVisits): ?array
    {
        if (empty($this->discount_progression)) {
            return null;
        }

        $progression = collect($this->discount_progression)->sortBy('visits');
        
        foreach ($progression as $prog) {
            $requiredVisits = $prog['visits'] ?? 0;
            if ($currentVisits < $requiredVisits) {
                return [
                    'visits' => $requiredVisits,
                    'discount' => $prog['discount'] ?? 0,
                    'remaining' => $requiredVisits - $currentVisits
                ];
            }
        }

        return null; // Already at max discount
    }

    /**
     * Check if member qualifies for birthday discount
     */
    public function qualifiesForBirthdayDiscount(): bool
    {
        return $this->has_special_birthday_discount;
    }

    /**
     * Check if member qualifies for consecutive visit bonus
     */
    public function qualifiesForConsecutiveBonus(): bool
    {
        return $this->has_consecutive_visit_bonus;
    }

    /**
     * Get default discount progression for membership types
     */
    public static function getDefaultProgression(string $type = 'basic'): array
    {
        $progressions = [
            'basic' => [
                ['visits' => 5, 'discount' => 8.0],
                ['visits' => 10, 'discount' => 10.0],
                ['visits' => 15, 'discount' => 12.0],
                ['visits' => 20, 'discount' => 15.0],
            ],
            'premium' => [
                ['visits' => 3, 'discount' => 10.0],
                ['visits' => 7, 'discount' => 15.0],
                ['visits' => 12, 'discount' => 18.0],
                ['visits' => 18, 'discount' => 20.0],
            ],
            'vip' => [
                ['visits' => 2, 'discount' => 15.0],
                ['visits' => 5, 'discount' => 20.0],
                ['visits' => 10, 'discount' => 25.0],
                ['visits' => 15, 'discount' => 30.0],
            ]
        ];

        return $progressions[$type] ?? $progressions['basic'];
    }

    /**
     * Check if points should reset after redemption
     */
    public function shouldResetPointsAfterRedemption(): bool
    {
        return $this->points_reset_after_redemption;
    }

    /**
     * Get points reset threshold
     */
    public function getPointsResetThreshold(): ?int
    {
        return $this->points_reset_threshold;
    }

    /**
     * Check if member should reset points based on threshold
     */
    public function shouldResetPointsAtThreshold(int $currentPoints): bool
    {
        if (!$this->points_reset_after_redemption) {
            return false;
        }

        $threshold = $this->getPointsResetThreshold();
        return $threshold && $currentPoints >= $threshold;
    }

    /**
     * Get points reset policy description
     */
    public function getPointsResetPolicyAttribute(): string
    {
        if (!$this->points_reset_after_redemption) {
            return 'Points never reset - accumulate indefinitely';
        }

        $threshold = $this->getPointsResetThreshold();
        if ($threshold) {
            return "Points reset to 0 when reaching {$threshold} points";
        }

        return 'Points reset to 0 after each redemption';
    }

    /**
     * Get points reset notes
     */
    public function getPointsResetNotesAttribute(): string
    {
        return $this->points_reset_notes ?? 'No additional notes';
    }

    /**
     * Get card field mappings with default structure
     */
    public function getCardFieldMappingsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * Get available member fields for card mapping
     */
    public static function getAvailableMemberFields(): array
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'full_name' => 'Full Name',
            'membership_id' => 'Membership ID',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'birth_date' => 'Birth Date',
            'join_date' => 'Join Date',
            'membership_type_name' => 'Membership Type Name',
            'hotel_name' => 'Hotel Name',
        ];
    }

    /**
     * Get card template URL
     */
    public function getCardTemplateUrlAttribute(): ?string
    {
        if (!$this->card_template_image) {
            return null;
        }
        return asset('storage/' . $this->card_template_image);
    }

    /**
     * Check if card template is configured
     */
    public function hasCardTemplate(): bool
    {
        return !empty($this->card_template_image) && !empty($this->card_field_mappings);
    }
} 