<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPresence extends Model
{
    use HasFactory;

    protected $table = 'member_presence';

    protected $fillable = [
        'member_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    /**
     * Get the member for this presence record
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
} 