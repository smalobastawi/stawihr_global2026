<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSubscriptionStatus extends Model
{
    protected $table = 'portal_subscription_status';

    protected $fillable = [
        'is_suspended',
        'reason',
        'support_email',
        'support_phone',
        'portal_subscription_id',
        'domain',
        'suspended_at',
        'unsuspended_at',
    ];

    protected $casts = [
        'is_suspended' => 'boolean',
        'suspended_at' => 'datetime',
        'unsuspended_at' => 'datetime',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'is_suspended' => false,
        ]);
    }

    public static function applySuspension(array $payload): self
    {
        $record = static::current();

        $record->update([
            'is_suspended' => true,
            'reason' => $payload['reason'] ?? null,
            'support_email' => $payload['support_email'] ?? null,
            'support_phone' => $payload['support_phone'] ?? null,
            'portal_subscription_id' => $payload['portal_subscription_id'] ?? null,
            'domain' => $payload['domain'] ?? null,
            'suspended_at' => isset($payload['suspended_at'])
                ? \Carbon\Carbon::parse($payload['suspended_at'])
                : now(),
            'unsuspended_at' => null,
        ]);

        return $record->fresh();
    }

    public static function clearSuspension(): self
    {
        $record = static::current();

        $record->update([
            'is_suspended' => false,
            'reason' => null,
            'support_email' => null,
            'support_phone' => null,
            'unsuspended_at' => now(),
        ]);

        return $record->fresh();
    }
}
