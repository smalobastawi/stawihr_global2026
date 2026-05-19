<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelReady\LicenseConnector\Services\ConnectorService;

class LicenseController extends Controller
{

    public function openLicenses()
    {
        return view('licensing.index');
    }
    public function invalidLicense()
    {
        return 'invalid license. Contact support for assistance';
    }
}
