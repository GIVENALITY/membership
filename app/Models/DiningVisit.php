<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'bill_amount',
        'discount_amount',
        'final_amount',
        'discount_rate',
        'receipt_path',
        'notes',
        'visited_at',
    ];

    protected $casts = [
        'bill_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'visited_at' => 'datetime',
    ];

    /**
     * Get the member who made this visit
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
} 