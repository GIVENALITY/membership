<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MemberPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'hotel_id',
        'dining_visit_id',
        'points_earned',
        'points_used',
        'points_balance',
        'spending_amount',
        'number_of_people',
        'per_person_spending',
        'qualifies_for_discount',
        'is_birthday_visit',
        'notes'
    ];

    protected $casts = [
        'qualifies_for_discount' => 'boolean',
        'is_birthday_visit' => 'boolean',
        'spending_amount' => 'decimal:2',
        'per_person_spending' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function diningVisit()
    {
        return $this->belongsTo(DiningVisit::class);
    }

    /**
     * Calculate points based on spending and number of people
     * @deprecated Use PointsCalculationService instead
     */
    public static function calculatePoints($spendingAmount, $numberOfPeople)
    {
        // For backward compatibility, use the new service
        $service = new \App\Services\PointsCalculationService();
        
        // Create a temporary member for calculation (this is a fallback)
        $tempMember = new \App\Models\Member();
        $tempMember->hotel_id = auth()->user()->hotel_id ?? 1;
        
        $result = $service->calculateDiningVisitPoints($tempMember, $spendingAmount, $numberOfPeople);
        
        return $result['total_points'];
    }

    /**
     * Check if this visit qualifies for special 20% discount
     */
    public function qualifiesForSpecialDiscount()
    {
        // Must have 5 or more points total
        if ($this->member->current_points_balance < 5) {
            return false;
        }

        // Must be 5th consecutive visit
        if ($this->member->consecutive_visits < 5) {
            return false;
        }

        // Must be above average 50k per person
        if ($this->per_person_spending < 50000) {
            return false;
        }

        return true;
    }

    /**
     * Check if this is a birthday visit
     */
    public function isBirthdayVisit()
    {
        if (!$this->member->birth_date) {
            return false;
        }

        $birthDate = Carbon::parse($this->member->birth_date);
        $visitDate = Carbon::parse($this->created_at);

        // Check if visit is within 7 days of birthday
        return $birthDate->isBirthday($visitDate) || 
               $birthDate->diffInDays($visitDate, false) <= 7;
    }
} 