<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsController extends Controller
{
    public function index(): View
    {
        $changes = [];
        $activityLogs = Activity::take(200)->with(['causer', 'subject'])->orderBy('created_at', 'desc')->get();


        // Decode JSON strings in each log entry
        // $activityLogs->transform(function ($item, $key) {
        //     $item->attributes = json_decode($item->properties)->attributes;

        //     $properties = json_decode($item->properties);
        //     if (isset($properties->old)) {
        //         $item->old = $properties->old;
        //     } else {
        //         $item->old = null; // or handle it as needed
        //     }
        //    // $item->old = json_decode($item->properties)->old;
        //     return $item;
        // });
        $activityLogs->transform(function ($item, $key) {
            $properties = json_decode($item->properties);
            // Ensure $properties is an object before accessing its attributes
            if (is_object($properties) && isset($properties->attributes)) {
                $item->attributes = $properties->attributes;
            } else {
                $item->attributes = null; // or handle it as needed
            }

            if (isset($properties->old)) {
                $item->old = $properties->old;
            } else {
                $item->old = null; // or handle it as needed
            }

            return $item;
        });

        return view('admin.activity-logs.index', ['data' => $activityLogs]);
    }
}
