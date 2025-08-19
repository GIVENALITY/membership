<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'type',
        'conditions',
        'severity',
        'is_active',
        'send_email',
        'show_dashboard',
        'show_quickview',
        'email_template',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'send_email' => 'boolean',
        'show_dashboard' => 'boolean',
        'show_quickview' => 'boolean',
    ];

    /**
     * Get the hotel that owns the alert
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the alert triggers for this alert
     */
    public function triggers(): HasMany
    {
        return $this->hasMany(MemberAlertTrigger::class);
    }

    /**
     * Get active triggers for this alert
     */
    public function activeTriggers(): HasMany
    {
        return $this->hasMany(MemberAlertTrigger::class)->where('status', 'active');
    }

    /**
     * Check if a member triggers this alert
     */
    public function checkMember(Member $member): bool
    {
        if (!$this->is_active) {
            return false;
        }

        switch ($this->type) {
            case 'spending_threshold':
                return $this->checkSpendingThreshold($member);
            
            case 'visit_frequency':
                return $this->checkVisitFrequency($member);
            
            case 'points_threshold':
                return $this->checkPointsThreshold($member);
            
            case 'birthday_approaching':
                return $this->checkBirthdayApproaching($member);
            
            case 'membership_expiry':
                return $this->checkMembershipExpiry($member);
            
            default:
                return false;
        }
    }

    /**
     * Check spending threshold alert
     */
    private function checkSpendingThreshold(Member $member): bool
    {
        $conditions = $this->conditions;
        $threshold = $conditions['amount'] ?? 0;
        $period = $conditions['period'] ?? 'month'; // day, week, month, year
        
        $query = DiningVisit::where('member_id', $member->id)
            ->where('is_checked_out', true)
            ->where('hotel_id', $this->hotel_id);

        switch ($period) {
            case 'day':
                $query->whereDate('created_at', now()->toDateString());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $totalSpent = $query->sum('final_amount');
        
        return $totalSpent >= $threshold;
    }

    /**
     * Check visit frequency alert
     */
    private function checkVisitFrequency(Member $member): bool
    {
        $conditions = $this->conditions;
        $daysThreshold = $conditions['days'] ?? 30;
        $lastVisit = DiningVisit::where('member_id', $member->id)
            ->where('is_checked_out', true)
            ->where('hotel_id', $this->hotel_id)
            ->latest('created_at')
            ->first();

        if (!$lastVisit) {
            return true; // No visits at all
        }

        $daysSinceLastVisit = now()->diffInDays($lastVisit->created_at);
        return $daysSinceLastVisit >= $daysThreshold;
    }

    /**
     * Check points threshold alert
     */
    private function checkPointsThreshold(Member $member): bool
    {
        $conditions = $this->conditions;
        $threshold = $conditions['points'] ?? 0;
        $operator = $conditions['operator'] ?? 'gte'; // gte, lte, eq
        
        $currentPoints = $member->current_points_balance;
        
        switch ($operator) {
            case 'gte':
                return $currentPoints >= $threshold;
            case 'lte':
                return $currentPoints <= $threshold;
            case 'eq':
                return $currentPoints == $threshold;
            default:
                return false;
        }
    }

    /**
     * Check birthday approaching alert
     */
    private function checkBirthdayApproaching(Member $member): bool
    {
        if (!$member->birth_date) {
            return false;
        }

        $conditions = $this->conditions;
        $daysAhead = $conditions['days_ahead'] ?? 7;
        
        $birthday = \Carbon\Carbon::parse($member->birth_date);
        $nextBirthday = $birthday->copy()->setYear(now()->year);
        
        if ($nextBirthday->isPast()) {
            $nextBirthday->addYear();
        }
        
        $daysUntilBirthday = now()->diffInDays($nextBirthday, false);
        return $daysUntilBirthday <= $daysAhead && $daysUntilBirthday >= 0;
    }

    /**
     * Check membership expiry alert
     */
    private function checkMembershipExpiry(Member $member): bool
    {
        $conditions = $this->conditions;
        $daysAhead = $conditions['days_ahead'] ?? 30;
        
        // This would need to be implemented based on your membership expiry logic
        // For now, we'll return false as membership expiry isn't implemented yet
        return false;
    }

    /**
     * Get the alert icon based on type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'spending_threshold' => 'ri-money-dollar-circle-line',
            'visit_frequency' => 'ri-time-line',
            'points_threshold' => 'ri-star-line',
            'birthday_approaching' => 'ri-cake-line',
            'membership_expiry' => 'ri-calendar-close-line',
            default => 'ri-notification-line',
        };
    }

    /**
     * Get the alert badge color based on severity
     */
    public function getBadgeColorAttribute(): string
    {
        return match($this->severity) {
            'low' => 'bg-label-info',
            'medium' => 'bg-label-warning',
            'high' => 'bg-label-danger',
            'critical' => 'bg-label-dark',
            default => 'bg-label-warning',
        };
    }
}
