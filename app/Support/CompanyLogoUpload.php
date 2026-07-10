<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyLogoUpload
{
    public const DIRECTORY = 'uploads/company_logos';

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

        if (Storage::disk('local')->exists($storagePath)) {
            Storage::disk('local')->delete($storagePath);
        }

        foreach (self::legacyPublicPaths($filename) as $legacyPath) {
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

        if (Storage::disk('local')->exists($storagePath)) {
            return Storage::disk('local')->path($storagePath);
        }

        foreach (self::legacyPublicPaths($filename) as $legacyPath) {
            if (is_file($legacyPath)) {
                return $legacyPath;
            }
        }

        return null;
    }

    public static function url(?string $filename): ?string
    {
        if ($filename === null || $filename === '') {
            return null;
        }

        $storagePath = self::DIRECTORY . '/' . $filename;

        if (Storage::disk('public')->exists($storagePath)) {
            return asset('storage/' . $storagePath);
        }

        if (Storage::disk('local')->exists($storagePath)) {
            Storage::disk('public')->put($storagePath, Storage::disk('local')->get($storagePath));

            return asset('storage/' . $storagePath);
        }

        foreach (self::legacyPublicPaths($filename) as $legacyPath) {
            if (is_file($legacyPath)) {
                Storage::disk('public')->put($storagePath, file_get_contents($legacyPath));

                return asset('storage/' . $storagePath);
            }
        }

        return null;
    }

    /** @return list<string> */
    private static function legacyPublicPaths(string $filename): array
    {
        return [
            public_path(self::DIRECTORY . '/' . $filename),
            base_path(self::DIRECTORY . '/' . $filename),
        ];
    }
}
