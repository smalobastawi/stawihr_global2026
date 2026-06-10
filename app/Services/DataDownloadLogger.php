<?php

namespace App\Services;

use App\Models\DataDownload;
use App\Models\GroupedMenuRoutePermission;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DataDownloadLogger
{
    public static function log(Request $request, Response $response): void
    {
        $route = $request->route();
        $routeName = $route?->getName();

        $groupMenu = $routeName
            ? GroupedMenuRoutePermission::where('permission', $routeName)->first()
            : null;

        $description = $groupMenu?->permission_description
            ?? $routeName
            ?? 'Data download';

        $requestData = collect($request->except(['_token', '_method', 'password']))
            ->filter(fn ($value) => !is_object($value))
            ->all();

        $properties = [
            'attributes' => array_filter([
                'filename' => self::resolveFilename($response),
                'route_name' => $routeName,
                'url' => $request->getRequestUri(),
                'method' => $request->method(),
                'content_type' => $response->headers->get('Content-Type'),
                'filters' => !empty($requestData) ? $requestData : null,
                'description' => $description,
            ], fn ($value) => $value !== null && $value !== ''),
        ];

        $subject = self::resolveSubject($request);

        DataDownload::create([
            'log_name' => $routeName ?? 'download',
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'event' => 'download',
            'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
            'causer_id' => Auth::id(),
            'properties' => $properties,
            'batch_uuid' => (string) Str::uuid(),
            'company_id' => CompanyContext::defaultCompanyIdForNewRecord(),
        ]);
    }

    protected static function resolveFilename(Response $response): ?string
    {
        if ($response instanceof BinaryFileResponse) {
            $disposition = $response->headers->get('Content-Disposition', '');

            return self::parseFilenameFromDisposition($disposition)
                ?? $response->getFile()?->getFilename();
        }

        return self::parseFilenameFromDisposition($response->headers->get('Content-Disposition', ''));
    }

    protected static function parseFilenameFromDisposition(string $disposition): ?string
    {
        if ($disposition === '') {
            return null;
        }

        if (preg_match('/filename\*=UTF-8\'\'([^;]+)/i', $disposition, $matches)) {
            return urldecode($matches[1]);
        }

        if (preg_match('/filename="([^"]+)"/i', $disposition, $matches)) {
            return $matches[1];
        }

        if (preg_match('/filename=([^;]+)/i', $disposition, $matches)) {
            return trim($matches[1], " \"'");
        }

        return null;
    }

    protected static function resolveSubject(Request $request): ?object
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        foreach ($route->parameters() as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'getKey')) {
                return $parameter;
            }
        }

        return null;
    }
}
