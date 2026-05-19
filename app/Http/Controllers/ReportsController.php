<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function errorLog()
    {
        $data = ErrorLog::take(500)->with('user')->get();


        return view('reports.error_logs',
        [
            'data'=>$data,
        ]
        );
    }
}
