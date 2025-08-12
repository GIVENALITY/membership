<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'hotel_id',
        'role',
        'is_active',
        'phone',
        'address',
        'bio',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the hotel that owns the user.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole($roles)
    {
        return in_array($this->role, (array) $roles);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is manager
     */
    public function isManager()
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is cashier
     */
    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    /**
     * Check if user is frontdesk
     */
    public function isFrontdesk()
    {
        return $this->role === 'frontdesk';
    }

    /**
     * Check if user can manage other users
     */
    public function canManageUsers()
    {
        return in_array($this->role, ['superadmin', 'admin', 'manager']);
    }

    /**
     * Check if user can access superadmin features
     */
    public function canAccessSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayNameAttribute()
    {
        return ucfirst($this->role);
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute()
    {
        return match($this->role) {
            'superadmin' => 'dark',
            'admin' => 'danger',
            'manager' => 'primary',
            'cashier' => 'warning',
            'frontdesk' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }
        return asset('assets/img/avatars/default-avatar.png');
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }
}
