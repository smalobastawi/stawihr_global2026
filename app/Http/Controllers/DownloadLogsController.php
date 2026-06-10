<?php

namespace App\Http\Controllers;

use App\Models\DataDownload;
use Illuminate\View\View;

class DownloadLogsController extends Controller
{
    public function index(): View
    {
        $downloadLogs = DataDownload::take(200)
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->get();

        $downloadLogs->transform(function ($item) {
            $properties = is_string($item->properties)
                ? json_decode($item->properties)
                : (object) ($item->properties ?? []);

            if (is_object($properties) && isset($properties->attributes)) {
                $item->attributes = $properties->attributes;
            } else {
                $item->attributes = null;
            }

            if (isset($properties->old)) {
                $item->old = $properties->old;
            } else {
                $item->old = null;
            }

            return $item;
        });

        return view('admin.download-logs.index', ['data' => $downloadLogs]);
    }
}
