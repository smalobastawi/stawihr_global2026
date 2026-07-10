<?php

namespace App\Http\Controllers;

use App\Support\SecureUpload;
use Illuminate\Support\Facades\Auth;

class SecureFileController extends Controller
{
    public function show(string $type, string $filename)
    {
        $this->authorizeAccess($type);

        return SecureUpload::response($type, $filename);
    }

    public function download(string $type, string $filename)
    {
        $this->authorizeAccess($type);

        return SecureUpload::response($type, $filename, download: true);
    }

    public function stored(string $path)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $storedPath = SecureUpload::decodeStoredPath($path);

        if (! SecureUpload::isAllowedStoredPath($storedPath)) {
            abort(404);
        }

        return SecureUpload::responseFromStored($storedPath);
    }

    public function downloadStored(string $path)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $storedPath = SecureUpload::decodeStoredPath($path);

        if (! SecureUpload::isAllowedStoredPath($storedPath)) {
            abort(404);
        }

        return SecureUpload::responseFromStored($storedPath, download: true);
    }

    private function authorizeAccess(string $type): void
    {
        if (SecureUpload::isGuestAllowed($type)) {
            return;
        }

        if (! Auth::check()) {
            abort(403);
        }
    }
}
