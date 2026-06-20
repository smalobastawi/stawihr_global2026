<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class AppVersion
{
    public static function read(): array
    {
        $path = base_path('VERSION.json');

        if (!File::exists($path)) {
            return [
                'version' => config('app.hr_version', '0.0.0-dev'),
                'git_ref' => null,
                'commit' => null,
                'built_at' => null,
            ];
        }

        return json_decode(File::get($path), true) ?: [];
    }

    public static function version(): string
    {
        return (string) (static::read()['version'] ?? config('app.hr_version', '0.0.0-dev'));
    }

    public static function commit(): ?string
    {
        $commit = static::read()['commit'] ?? null;

        return $commit ? (string) $commit : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function write(array $payload): void
    {
        File::put(
            base_path('VERSION.json'),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n"
        );
    }
}
