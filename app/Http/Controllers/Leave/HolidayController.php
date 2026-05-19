<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Leave;

use App\Http\Requests\HolidayRequest;

use App\Models\User;
use Illuminate\Http\Request;

use App\Models\Holiday;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class HolidayController extends Controller
{

    public function index(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $results = Holiday::orderBy('holiday_id', 'desc')->get();
        return view('admin.leave.holiday.index',['results'=>$results, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function create(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $departments=Department::pluck('department_name','department_id',)->toArray();
        return view('admin.leave.holiday.form',['signed_in_user_role'=>$signed_in_user_role,'departments'=>$departments]);
    }

 
    public function store(HolidayRequest $request)
    {
        DB::beginTransaction();
        try {
            $holiday = Holiday::create($request->validated());

            // Sync departments if provided
            if ($request->has('departments')) {
                $holiday->departments()->sync($request->input('departments'));
            }

            DB::commit();
            return redirect()->route('holiday.index')->with('success', 'Holiday successfully saved.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating holiday: " . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }
    


    public function edit($id){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $departments=Department::pluck('department_name','department_id',)->toArray();
        $editModeData = Holiday::findOrFail($id);
        return view('admin.leave.holiday.form',['editModeData' => $editModeData,'departments'=>$departments,'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function update(HolidayRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $holiday = Holiday::findOrFail($id);
            $holiday->update($request->validated());

            // Sync departments
            if ($request->has('departments')) {
                $holiday->departments()->sync($request->input('departments'));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Holiday successfully updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating holiday: " . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }
    


    public function destroy($id){
        try{
            $holiday = Holiday::findOrFail($id);

            // Check for linked records before attempting deletion
            $linkedRecords = [];

            // Check for holiday details (public holidays with dates)
            $holidayDetailsCount = \App\Models\HolidayDetails::where('holiday_id', $id)->count();
            if ($holidayDetailsCount > 0) {
                $linkedRecords[] = $holidayDetailsCount . ' holiday date(s)';
            }

            // Check for department associations
            $departmentsCount = $holiday->departments()->count();
            if ($departmentsCount > 0) {
                $linkedRecords[] = $departmentsCount . ' department(s)';
            }

            // If there are linked records, prevent deletion and return error
            if (!empty($linkedRecords)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this holiday because it is linked to: ' . implode(', ', $linkedRecords) . '. Please remove these associations first.'
                ], 422);
            }

            $holiday->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Holiday deleted successfully.'
            ]);
        }
        catch(\Illuminate\Database\QueryException $e){
            // Handle database query exceptions (including foreign key constraints)
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            // Check for foreign key constraint violation (MySQL: 1451, 23000; PostgreSQL: 23503)
            if (strpos($errorMessage, '1451') !== false ||
                strpos($errorMessage, '23000') !== false ||
                strpos($errorMessage, 'foreign key constraint') !== false ||
                strpos($errorMessage, 'Integrity constraint violation') !== false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this holiday because it is linked to other records. Please remove all associations first.'
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred while deleting the holiday.'
            ], 500);
        }
        catch(\Exception $e){
            Log::error("Error deleting holiday ID {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while deleting the holiday. Please try again.'
            ], 500);
        }
    }


}
