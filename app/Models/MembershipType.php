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
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'perks' => 'array',
        'discount_rate' => 'decimal:2',
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
} 