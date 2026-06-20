<?php

namespace App\Http\Controllers;

use App\Models\PortalSubscriptionStatus;
use Illuminate\Contracts\View\View;

class SubscriptionSuspensionController extends Controller
{
    public function show(): View
    {
        $status = PortalSubscriptionStatus::current();

        return view('subscription.suspended', [
            'status' => $status,
            'portalSupportUrl' => config('portal.support_url'),
        ]);
    }
}
