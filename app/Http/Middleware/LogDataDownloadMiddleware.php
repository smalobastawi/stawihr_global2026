<?php

namespace App\Http\Middleware;

use App\Services\DataDownloadLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LogDataDownloadMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->isDownloadResponse($response)) {
            try {
                DataDownloadLogger::log($request, $response);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $response;
    }

    protected function isDownloadResponse(Response $response): bool
    {
        if ($response instanceof BinaryFileResponse) {
            return true;
        }

        $disposition = $response->headers->get('Content-Disposition', '');

        return stripos($disposition, 'attachment') !== false;
    }
}
