<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'email_type',
        'subject',
        'content',
        'recipient_email',
        'recipient_name',
        'member_id',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
        'opened_at',
        'bounced_at',
        'message_id',
        'metadata'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'bounced_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relationships
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Scopes
    public function scopeForHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('email_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'bounced']);
    }

    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', ['sent', 'delivered', 'opened']);
    }

    // Helper methods
    public function isSuccessful()
    {
        return in_array($this->status, ['sent', 'delivered', 'opened']);
    }

    public function isFailed()
    {
        return in_array($this->status, ['failed', 'bounced']);
    }

    public function canRetry()
    {
        return $this->isFailed() && $this->created_at->diffInHours(now()) < 24;
    }

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'sent':
                return 'badge bg-primary';
            case 'delivered':
                return 'badge bg-success';
            case 'opened':
                return 'badge bg-info';
            case 'failed':
                return 'badge bg-danger';
            case 'bounced':
                return 'badge bg-warning';
            default:
                return 'badge bg-secondary';
        }
    }

    public function getEmailTypeLabel()
    {
        switch ($this->email_type) {
            case 'welcome':
                return 'Welcome Email';
            case 'member_email':
                return 'Member Email';
            case 'event_notification':
                return 'Event Notification';
            case 'birthday':
                return 'Birthday Email';
            default:
                return ucfirst(str_replace('_', ' ', $this->email_type));
        }
    }
}
