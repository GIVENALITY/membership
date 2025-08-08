<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'email',
        'subject',
        'message',
        'type',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Get the member for this notification
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
} 