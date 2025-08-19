<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
    ];

    protected $casts = [
        'setting_value' => 'string',
    ];

    /**
     * Get the hotel this setting belongs to
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get setting value with proper type casting
     */
    public function getTypedValueAttribute()
    {
        return match($this->setting_type) {
            'boolean' => filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->setting_value,
            'json' => json_decode($this->setting_value, true),
            default => $this->setting_value,
        };
    }

    /**
     * Set setting value with proper type handling
     */
    public function setTypedValue($value): void
    {
        $this->setting_value = match($this->setting_type) {
            'boolean' => $value ? 'true' : 'false',
            'integer' => (string) $value,
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };
    }

    /**
     * Get a setting value for a hotel
     */
    public static function getValue(int $hotelId, string $key, $default = null)
    {
        $setting = static::where('hotel_id', $hotelId)
            ->where('setting_key', $key)
            ->first();

        if (!$setting) {
            return $default;
        }

        return $setting->typed_value;
    }

    /**
     * Set a setting value for a hotel
     */
    public static function setValue(int $hotelId, string $key, $value, string $type = 'string', string $description = null): void
    {
        static::updateOrCreate(
            ['hotel_id' => $hotelId, 'setting_key' => $key],
            [
                'setting_value' => match($type) {
                    'boolean' => $value ? 'true' : 'false',
                    'integer' => (string) $value,
                    'json' => is_array($value) ? json_encode($value) : $value,
                    default => (string) $value,
                },
                'setting_type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Get all settings for a hotel as an array
     */
    public static function getAllForHotel(int $hotelId): array
    {
        return static::where('hotel_id', $hotelId)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->setting_key => $setting->typed_value];
            })
            ->toArray();
    }
}
