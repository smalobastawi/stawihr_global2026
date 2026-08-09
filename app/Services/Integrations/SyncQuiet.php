<?php

namespace App\Services\Integrations;

class SyncQuiet
{
    private static int $depth = 0;

    public static function running(): bool
    {
        return self::$depth > 0;
    }

    public static function run(callable $callback): mixed
    {
        self::$depth++;

        try {
            return $callback();
        } finally {
            self::$depth = max(0, self::$depth - 1);
        }
    }
}
