<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FrontUpload
{
    public const DIRECTORY = 'uploads/front';

    public static function store(UploadedFile $file, string $filename): string
    {
        Storage::disk('public')->putFileAs(self::DIRECTORY, $file, $filename);

        return $filename;
    }

    public static function delete(?string $filename): void
    {
        if ($filename === null || $filename === '') {
            return;
        }

        $storagePath = self::DIRECTORY . '/' . $filename;
        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);

            return;
        }

        foreach (self::legacyPaths($filename) as $legacyPath) {
            if (is_file($legacyPath)) {
                unlink($legacyPath);
            }
        }
    }

    public static function exists(?string $filename): bool
    {
        return self::path($filename) !== null;
    }

    public static function path(?string $filename): ?string
    {
        if ($filename === null || $filename === '') {
            return null;
        }

        $storagePath = self::DIRECTORY . '/' . $filename;
        if (Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->path($storagePath);
        }

        foreach (self::legacyPaths($filename) as $legacyPath) {
            if (is_file($legacyPath)) {
                return $legacyPath;
            }
        }

        return null;
    }

    public static function url(?string $filename): ?string
    {
        if ($filename === null || $filename === '' || ! self::exists($filename)) {
            return null;
        }

        return asset('storage/' . self::DIRECTORY . '/' . $filename);
    }

    /** @return list<string> */
    private static function legacyPaths(string $filename): array
    {
        return [
            public_path(self::DIRECTORY . '/' . $filename),
            base_path(self::DIRECTORY . '/' . $filename),
        ];
    }
}
