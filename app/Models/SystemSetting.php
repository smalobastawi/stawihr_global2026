<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'email_notifications_enabled',
        'sms_notifications_enabled',
        'inapp_notifications_enabled',
    ];

    protected $casts = [
        'email_notifications_enabled' => 'boolean',
        'sms_notifications_enabled' => 'boolean',
        'inapp_notifications_enabled' => 'boolean',
    ];

    /**
     * Get the single system settings record, creating one if it doesn't exist.
     */
    public static function getSettings(): self
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'email_notifications_enabled' => true,
                'sms_notifications_enabled' => true,
                'inapp_notifications_enabled' => true,
            ]);
        }

        return $settings;
    }
}
