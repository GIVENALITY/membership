<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'member_id',
        'number_of_people',
        'notes',
        'is_checked_out',
        'amount_spent',
        'discount_amount',
        'final_amount',
        'receipt_path',
        'checkout_notes',
        'checked_out_at',
        'recorded_by',
        'checked_out_by',
    ];

    protected $attributes = [
        'amount_spent' => 0,
        'discount_amount' => 0,
        'final_amount' => 0,
        'is_checked_out' => false,
    ];

    protected $casts = [
        'is_checked_out' => 'boolean',
        'amount_spent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'checked_out_at' => 'datetime',
    ];

    /**
     * Get the member associated with this visit
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the hotel associated with this visit
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the user who recorded this visit
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the user who checked out this visit
     */
    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    /**
     * Get the receipt URL
     */
    public function getReceiptUrlAttribute()
    {
        if ($this->receipt_path) {
            return asset('storage/' . $this->receipt_path);
        }
        return null;
    }

    /**
     * Scope for active visits (not checked out)
     */
    public function scopeActive($query)
    {
        return $query->where('is_checked_out', false);
    }

    /**
     * Scope for completed visits (checked out)
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_checked_out', true);
    }

    /**
     * Get the discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->amount_spent && $this->amount_spent > 0) {
            return round(($this->discount_amount / $this->amount_spent) * 100, 2);
        }
        return 0;
    }

    /**
     * Get the visit duration in minutes
     */
    public function getDurationAttribute()
    {
        if ($this->checked_out_at && $this->created_at) {
            return $this->created_at->diffInMinutes($this->checked_out_at);
        }
        return null;
    }
} 