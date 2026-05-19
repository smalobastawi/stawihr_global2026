<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApprovalSettingsController extends Controller
{
    /**
     * Get all approval settings
     * 
     * @route GET /api/settings/approval
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApprovalSettings()
    {
        $settings = DB::table('approval_settings')
            ->select(
                'id',
                'module_id',
                'approvers_list',
                'approver_numbers',
                'created_at',
                'updated_at'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Create or update approval settings
     * 
     * @route POST /api/settings/approval
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createApprovalSettings(Request $request)
    {
        // Validate the request
        $request->validate([
            'module_id' => 'required|integer|min:1',  
            'approver_numbers' => 'required|integer|min:1',
            'approvers_list' => 'required|string'
        ]);

        try {
            $moduleId = (int) $request->module_id;  

            // Check if module_id already exists
            $existingSettings = DB::table('approval_settings')
                ->where('module_id', $moduleId)
                ->first();

            if ($existingSettings) {
                // Update existing settings
                DB::table('approval_settings')
                    ->where('module_id', $moduleId)
                    ->update([
                        'approver_numbers' => $request->approver_numbers,
                        'approvers_list' => $request->approvers_list,
                        'updated_at' => now()
                    ]);

                $settingId = $existingSettings->id;
            } else {
                // Create new settings
                $settingId = DB::table('approval_settings')->insertGetId([
                    'module_id' => $moduleId,
                    'approver_numbers' => $request->approver_numbers,
                    'approvers_list' => $request->approvers_list,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Get the created/updated settings
            $settings = DB::table('approval_settings')
                ->where('id', $settingId)
                ->first();

            return response()->json([
                'status' => 'success',
                'message' => $existingSettings ? 'Approval settings updated successfully' : 'Approval settings created successfully',
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save approval settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
