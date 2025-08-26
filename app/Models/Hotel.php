<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'website',
        'description',
        'logo_path',
        'banner_path',
        'primary_color',
        'secondary_color',
        'tertiary_color',
        'is_active',
        'email_verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the users associated with this hotel.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the members associated with this hotel.
     */
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get the membership types associated with this hotel.
     */
    public function membershipTypes()
    {
        return $this->hasMany(MembershipType::class);
    }

    /**
     * Get the dining visits associated with this hotel.
     */
    public function diningVisits()
    {
        return $this->hasManyThrough(DiningVisit::class, Member::class);
    }

    /**
     * Get the hotel's settings
     */
    public function settings()
    {
        return $this->hasMany(RestaurantSetting::class);
    }

    /**
     * Get a specific setting value
     */
    public function getSetting(string $key, $default = null)
    {
        return RestaurantSetting::getValue($this->id, $key, $default);
    }

    /**
     * Set a specific setting value
     */
    public function setSetting(string $key, $value, string $type = 'string', string $description = null): void
    {
        RestaurantSetting::setValue($this->id, $key, $value, $type, $description);
    }

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return asset('assets/img/default-logo.png');
    }

    /**
     * Get the banner URL.
     */
    public function getBannerUrlAttribute()
    {
        if ($this->banner_path) {
            return asset('storage/' . $this->banner_path);
        }
        return asset('assets/img/default-banner.png');
    }

    /**
     * Scope to get only active hotels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 