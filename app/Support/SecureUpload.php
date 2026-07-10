<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SecureUpload
{
    public const EMPLOYEE_PHOTO = 'employee-photo';
    public const EMPLOYEE_DOCS = 'employee-docs';
    public const LEAVE_APPLICATION = 'leave-application';
    public const HR_DOCUMENTS = 'hr-documents';
    public const NOTICE = 'notice';
    public const TRAINING_CERTIFICATE = 'training-certificate';
    public const JOB_DESCRIPTIONS = 'job-descriptions';
    public const COMPANY_LOGOS = 'company-logos';
    public const DISCIPLINARY = 'disciplinary';

    /** @var array<string, string> */
    private const DIRECTORIES = [
        self::EMPLOYEE_PHOTO => 'uploads/employeePhoto',
        self::EMPLOYEE_DOCS => 'uploads/employeeDocs',
        self::LEAVE_APPLICATION => 'uploads/leaveApplication',
        self::HR_DOCUMENTS => 'uploads/documents',
        self::NOTICE => 'uploads/notice',
        self::TRAINING_CERTIFICATE => 'uploads/employeeTrainingCertificate',
        self::JOB_DESCRIPTIONS => 'uploads/jobDescriptions',
        self::COMPANY_LOGOS => 'uploads/company_logos',
        self::DISCIPLINARY => 'disciplinacy/uploads',
    ];

    /** @var array<string, string> Legacy public paths for backward compatibility. */
    private const LEGACY_PUBLIC_DIRECTORIES = [
        self::EMPLOYEE_PHOTO => 'uploads/employeePhoto',
        self::EMPLOYEE_DOCS => 'uploads/employeeDocs',
        self::LEAVE_APPLICATION => 'uploads/leaveApplication',
        self::HR_DOCUMENTS => 'uploads/documents',
        self::NOTICE => 'uploads/notice',
        self::TRAINING_CERTIFICATE => 'uploads/employeeTrainingCertificate',
        self::JOB_DESCRIPTIONS => 'uploads/jobDescriptions',
        self::COMPANY_LOGOS => 'uploads/company_logos',
    ];

    /** @var list<string> Stored paths (as saved in DB) that may be served after auth. */
    private const ALLOWED_STORED_PATH_PREFIXES = [
        'certificates/',
        'documents/',
        'disciplinacy/uploads/',
        'responses/files/',
        'uploads/',
    ];

    /** @var list<string> Upload types accessible without authentication (login branding). */
    private const GUEST_ALLOWED_TYPES = [
        self::COMPANY_LOGOS,
    ];

    public static function directory(string $type): string
    {
        if (! isset(self::DIRECTORIES[$type])) {
            throw new \InvalidArgumentException("Unknown secure upload type: {$type}");
        }

        return self::DIRECTORIES[$type];
    }

    public static function isGuestAllowed(string $type): bool
    {
        return in_array($type, self::GUEST_ALLOWED_TYPES, true);
    }

    public static function store(UploadedFile $file, string $type, string $filename): string
    {
        $directory = self::directory($type);
        Storage::disk('local')->putFileAs($directory, $file, $filename);

        return $filename;
    }

    public static function storeWithPath(UploadedFile $file, string $relativePath): string
    {
        $relativePath = self::normalizeStoredPath($relativePath);
        Storage::disk('local')->putFileAs(
            dirname($relativePath),
            $file,
            basename($relativePath)
        );

        return $relativePath;
    }

    public static function delete(string $type, ?string $filename): void
    {
        if ($filename === null || $filename === '') {
            return;
        }

        $storagePath = self::directory($type) . '/' . $filename;
        if (Storage::disk('local')->exists($storagePath)) {
            Storage::disk('local')->delete($storagePath);

            return;
        }

        $legacyPath = self::legacyPublicPath($type, $filename);
        if ($legacyPath !== null && is_file($legacyPath)) {
            unlink($legacyPath);
        }
    }

    public static function deleteStoredPath(?string $storedPath): void
    {
        if ($storedPath === null || $storedPath === '') {
            return;
        }

        $storedPath = self::normalizeStoredPath($storedPath);

        if (Storage::disk('local')->exists($storedPath)) {
            Storage::disk('local')->delete($storedPath);

            return;
        }

        if (Storage::disk('public')->exists($storedPath)) {
            Storage::disk('public')->delete($storedPath);

            return;
        }

        $legacyPath = self::legacyPublicStoredPath($storedPath);
        if ($legacyPath !== null && is_file($legacyPath)) {
            unlink($legacyPath);
        }
    }

    public static function exists(string $type, ?string $filename): bool
    {
        return self::path($type, $filename) !== null;
    }

    public static function storedPathExists(?string $storedPath): bool
    {
        return self::pathFromStored($storedPath) !== null;
    }

    public static function path(string $type, ?string $filename): ?string
    {
        if ($filename === null || $filename === '') {
            return null;
        }

        $storagePath = self::directory($type) . '/' . $filename;
        if (Storage::disk('local')->exists($storagePath)) {
            return Storage::disk('local')->path($storagePath);
        }

        $legacyPath = self::legacyPublicPath($type, $filename);
        if ($legacyPath !== null && is_file($legacyPath)) {
            return $legacyPath;
        }

        $legacyPublicDiskPath = self::directory($type) . '/' . $filename;
        if (Storage::disk('public')->exists($legacyPublicDiskPath)) {
            return Storage::disk('public')->path($legacyPublicDiskPath);
        }

        return null;
    }

    public static function pathFromStored(?string $storedPath): ?string
    {
        if ($storedPath === null || $storedPath === '') {
            return null;
        }

        $storedPath = self::normalizeStoredPath($storedPath);

        if (Storage::disk('local')->exists($storedPath)) {
            return Storage::disk('local')->path($storedPath);
        }

        if (Storage::disk('public')->exists($storedPath)) {
            return Storage::disk('public')->path($storedPath);
        }

        $legacyPath = self::legacyPublicStoredPath($storedPath);
        if ($legacyPath !== null && is_file($legacyPath)) {
            return $legacyPath;
        }

        return null;
    }

    public static function url(string $type, ?string $filename): ?string
    {
        if ($filename === null || $filename === '' || ! self::exists($type, $filename)) {
            return null;
        }

        return route('secure-file.show', [
            'type' => $type,
            'filename' => $filename,
        ]);
    }

    public static function urlFromStored(?string $storedPath): ?string
    {
        if ($storedPath === null || $storedPath === '' || ! self::storedPathExists($storedPath)) {
            return null;
        }

        return route('secure-file.stored', [
            'path' => self::encodeStoredPath($storedPath),
        ]);
    }

    public static function isAllowedStoredPath(string $storedPath): bool
    {
        $storedPath = self::normalizeStoredPath($storedPath);

        foreach (self::ALLOWED_STORED_PATH_PREFIXES as $prefix) {
            if (str_starts_with($storedPath, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public static function encodeStoredPath(string $storedPath): string
    {
        return rtrim(strtr(base64_encode($storedPath), '+/', '-_'), '=');
    }

    public static function decodeStoredPath(string $encodedPath): string
    {
        $normalized = strtr($encodedPath, '-_', '+/');
        $padding = strlen($normalized) % 4;
        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);

        if ($decoded === false) {
            abort(404);
        }

        return self::normalizeStoredPath($decoded);
    }

    public static function response(string $type, string $filename, bool $download = false): BinaryFileResponse
    {
        $absolutePath = self::path($type, $filename);

        if ($absolutePath === null) {
            abort(404);
        }

        return self::fileResponse($absolutePath, $filename, $download);
    }

    public static function responseFromStored(string $storedPath, bool $download = false): BinaryFileResponse
    {
        $absolutePath = self::pathFromStored($storedPath);

        if ($absolutePath === null) {
            abort(404);
        }

        return self::fileResponse($absolutePath, basename($storedPath), $download);
    }

    private static function fileResponse(string $absolutePath, string $filename, bool $download): BinaryFileResponse
    {
        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';

        if ($download) {
            return response()->download($absolutePath, $filename, [
                'Content-Type' => $mimeType,
            ]);
        }

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    private static function legacyPublicPath(string $type, string $filename): ?string
    {
        if (! isset(self::LEGACY_PUBLIC_DIRECTORIES[$type])) {
            return null;
        }

        $candidates = [
            public_path(self::LEGACY_PUBLIC_DIRECTORIES[$type] . '/' . $filename),
            base_path(self::LEGACY_PUBLIC_DIRECTORIES[$type] . '/' . $filename),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private static function legacyPublicStoredPath(string $storedPath): ?string
    {
        $candidates = [
            public_path($storedPath),
            base_path($storedPath),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private static function normalizeStoredPath(string $storedPath): string
    {
        $storedPath = str_replace('\\', '/', $storedPath);
        $storedPath = ltrim($storedPath, '/');

        if (str_contains($storedPath, '..')) {
            abort(404);
        }

        return $storedPath;
    }
}
