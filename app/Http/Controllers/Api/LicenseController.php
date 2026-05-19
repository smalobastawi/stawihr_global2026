<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LaravelReady\LicenseConnector\Services\ConnectorService;
use App\Models\AppLicense;

class LicenseController extends Controller
{
    public function checkLicense(Request $request)
    {
        $licenseKey = '46fad906-bc51-435f-9929-db46cb4baf13';
        $connectorService = new ConnectorService($licenseKey);

        $isLicenseValid = $connectorService->validateLicense();

        if ($isLicenseValid) {
            // License is valid
            echo 'License is valid';

            print_r($connectorService->license);
        } else {
            // License is invalid
            echo 'License is not valid';
        }
    }
}