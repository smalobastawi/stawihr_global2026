<?php

return [
    /*
    |--------------------------------------------------------------------------
    | School MIS (StawiSMS) push integration
    |--------------------------------------------------------------------------
    |
    | Runtime values are primarily managed in the admin UI:
    |   Settings → School MIS Integration
    |
    | Env vars below remain as bootstrap defaults / fallbacks when the
    | school_mis_settings table has no value yet.
    |
    | Auth keys (two directions):
    | 1) School inbound key (from StawiSMS Integrations) → stored as school_api_key
    |    and sent by HR when pushing to StawiSMS.
    | 2) HR pull API key (generated in School MIS settings UI) → pasted into
    |    StawiSMS as the HR remote system token for /api/internal/sync/*.
    |
    */

    'enabled' => env('SCHOOL_MIS_PUSH_ENABLED', false),

    'base_url' => env('SCHOOL_MIS_BASE_URL', 'http://localhost:8000'),

    'api_key' => env('SCHOOL_MIS_API_KEY', ''),

    'timeout' => (int) env('SCHOOL_MIS_TIMEOUT', 30),

    'push_on_employee_save' => env('SCHOOL_MIS_PUSH_ON_EMPLOYEE_SAVE', true),

    'push_on_leave_approve' => env('SCHOOL_MIS_PUSH_ON_LEAVE_APPROVE', true),
];
