<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Http\Requests\TerminationRequest;

use App\Repositories\CommonRepository;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\GeneralStatus;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Termination;

use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use App\Models\TerminationChecklist;
use App\Models\TerminationChecklistAction;
use App\Models\TerminationDocs;
use App\Models\User;
use App\Models\LeaversAndJoiners;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class TerminationController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository){
        $this->commonRepository = $commonRepository;
    }



    public function index(){
        $results = Termination::with(['terminateTo','terminateBy'])
            ->withTrashed() // Include soft-deleted records (reinstated before this update)
            ->orderBy('termination_id','DESC')
            ->get();
        return view('admin.employee.termination.index',['results'=>$results]);
    }



    public function create(){
        $employeeList = $this->commonRepository->employeeList();
        $terminatedBy = Employee::where('status', GeneralStatus::ACTIVE)
    ->whereHas('user', function($query) {
        $query->permission('termination.create');
    })
    ->select(['employee_id', 'first_name', 'last_name', 'middle_name'])
    ->get()
    ->mapWithKeys(function ($employee) {
        // Format the name as you prefer (e.g., "First Middle Last")
        $fullName = $employee->first_name . 
           ($employee->middle_name ? ' ' . $employee->middle_name : '') . 
           ' ' . $employee->last_name;
        
        return [$employee->employee_id => $fullName];
    })
    ->all();
        $termination_checklist_items = TerminationChecklist::with('checkListActions')->get();

        return view('admin.employee.termination.form',['employeeList' => $employeeList,'termination_checklist_items'=>$termination_checklist_items, 'terminatedBy'=>$terminatedBy]);
    }



    public function store(TerminationRequest $request) {
        $input = $request->all();
        $employee = Employee::where('employee_id',$request->terminate_to)->first();
        $input['notice_date'] = dateConvertFormtoDB($request->notice_date);
        $input['termination_date'] = dateConvertFormtoDB($request->termination_date);
        // Auto-approve termination
        $input['status'] = 2;

        try{
            DB::beginTransaction();

            $termination = Termination::create($input);
            $employeeDocuments = $this->make_termination_document($request->all(),$employee, $termination, 'update');

            if (count($employeeDocuments) > 0) {
                foreach ($employeeDocuments as $employeeDocument) {
                    TerminationDocs::create($employeeDocument);
                }
            }

            // Update employee status to terminated (3) immediately
            $employee->update(['status' => 3, 'date_of_leaving' => $input['termination_date']]);
            User::where('id',$employee->user_id)->update(['status' => 3]);

            // Create leaver record
            LeaversAndJoiners::create([
                'employee_id' => $employee->employee_id,
                'payroll_number' => $employee->payroll_number,
                'national_id' => $employee->national_id,
                'first_name' => $employee->first_name,
                'middle_name' => $employee->middle_name,
                'last_name' => $employee->last_name,
                'date_of_movement' => $input['termination_date'],
                'date_approved' => now(),
                'movement_type' => 'Leaver',
                'approval_status' => 'Approved',
                'reason' => $input['subject'] ?? 'Employee termination',
                'created_by' => Auth::user()->id,
            ]);

            DB::commit();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
            Log::info($e);
            DB::rollback();
        }

        if($bug==0){
            return redirect()->route('termination.index')->with('success', 'Employee termination successfully saved and approved.');
        }else {
            return redirect()->route('termination.index')->with('error', 'An error occured, contact support for assistance. '. $bug);
        }
    }

    public function edit($id){
        $editModeData = Termination::findOrFail($id);
        $terminatedBy = Employee::where('status', GeneralStatus::ACTIVE)
        ->whereHas('user', function($query) {
            $query->permission('termination.create');
        })
        ->select(['employee_id', 'first_name', 'last_name', 'middle_name'])
        ->get()
        ->mapWithKeys(function ($employee) {
            // Format the name as you prefer (e.g., "First Middle Last")
            $fullName = $employee->first_name . 
               ($employee->middle_name ? ' ' . $employee->middle_name : '') . 
               ' ' . $employee->last_name;
            
            return [$employee->employee_id => $fullName];
        })
        ->all();
        $employeeList = $this->commonRepository->employeeListTermination();
        $termination_checklist_items = TerminationChecklist::whereNotIn('id',$editModeData->checkListActions()->pluck('termination_checklist_id')->toArray())->get();
        $actionedItems = $editModeData->checkListActions;

       
        return view('admin.employee.termination.form',['actionedItems'=>$actionedItems,'employeeList' => $employeeList,'editModeData'=>$editModeData,'termination_checklist_items'=>$termination_checklist_items, 'terminatedBy'=>$terminatedBy]);
    }



    public function show($id){
        $results = Termination::with(['terminateTo.department','terminateBy', 'terminationDocs'])->where('termination_id',$id)->first();
        $checklist_actions = TerminationChecklistAction::with(['checklist'])->where('termination_id',$id)->get();

        return view('admin.employee.termination.details',['result' => $results,'checklist_actions'=>$checklist_actions]);
    }



    public function update(TerminationRequest $request,$id) {
        $data  						= Termination::findOrFail($id);
        $employee = Employee::where('employee_id',$request->terminate_to)->first();
        $input 						= $request->all();
        $input['notice_date']       = dateConvertFormtoDB($request->notice_date);
        $input['termination_date']  = dateConvertFormtoDB($request->termination_date);

        // Auto-approve termination on update
        $input['status'] = 2;

        try{
            DB::beginTransaction();

                $data->update($input);
                $employeeDocuments = $this->make_termination_document($request->all(),$employee, $data, 'update');

            if (count($employeeDocuments) > 0) {
                foreach ($employeeDocuments as $employeeDocument) {
                    TerminationDocs::create($employeeDocument);
                }
            }

            // Update employee status to terminated (3) and set leaving date
            $employee->update(['status' => 3, 'date_of_leaving' => $input['termination_date']]);
            User::where('id',$employee->user_id)->update(['status' => 3]);

            // Save checklist items
            if ($request->has('checklist')) {
                foreach ($request->checklist as $checklistId => $details) {
                    TerminationChecklistAction::create([
                        'termination_checklist_id' => $checklistId,
                        'termination_id' => $id,
                        'actioned_by' => auth()->user()->id,
                        'status'=>1,
                        'comment' => $details['comment'] ?? null,
                    ]);
                }
            }
            DB::commit();
            $bug = 0;
        }
        catch(\Exception $e){
            Log::info($e);
            DB::rollback();
            $bug = $e->getMessage();
        }

        if($bug == 0){
            return redirect()->route('termination.index')->with('success', 'Employee termination successfully updated and approved.');
        }else {
            return redirect()->back()->with('error', 'An error occured, contact support for assistance. ');
        }
    }



    public function report($id){
        $checklist_actions = TerminationChecklistAction::with(['checklist'])->where('termination_id',$id)->get();
        return view('admin.employee.termination.report',['checklist_actions' => $checklist_actions]);
    }
    public function destroy($id){
        try{
            $data = Termination::FindOrFail($id);
            $data->delete();
            $terminatioDocs = TerminationDocs::where('termination_id', $id)->get();
            foreach ($terminatioDocs as $doc) {
                // Delete file from storage
                Storage::delete('uploads/employeeDocs/' . $doc->file_url);
                $doc->delete();
            }
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function import()
    {
        $sample_file_link = url('admin_assets/sample_files/sample termination_import_file.xlsx');

        return view('admin.employee.termination.import', compact('sample_file_link'));
    }

    private static function make_termination_document($data, $employee, $termination,  $action = false)
    {
        $documentData = [];
        if (isset($data['document_name'])) {
            
          
            for ($i = 1; $i < count($data['document_name']); $i++) {

                $uuid = Str::uuid();

                $fileName = $data['document_name'][$i].'_'.$uuid.'.' . $data['document_file'][$i]->getClientOriginalExtension();

                $data['document_file'][$i]->move(public_path('uploads/employeeDocs'), $fileName);
                    if (file_exists(public_path('uploads/employeeDocs') . $fileName) and !empty($fileName)) {
                        unlink(public_path('uploads/employeeDocs') . $fileName);
                    }

                $documentData[$i] = [
                    'employee_id' => $employee->employee_id,
                    'document_name' => $data['document_name'][$i],
                    'termination_id' => $termination->termination_id,
                    'file_url' => $fileName,
                    'created_at' => Carbon::now(),

                ];
                if ($action == 'update') {
                    $documentData[$i]['terminationDocuments_cid'] = $data['terminationDocuments_cid'][$i];
                }
            }
        }

        return $documentData;
    }

    public function deleteTerminationDoc(Request $request)
    {
        $document = TerminationDocs::findOrFail($request->id);
        // Delete file from storage
        Storage::delete('uploads/employeeDocs/' . $document->file_url);
        $document->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Reinstate a terminated employee without approval workflow
     */
    public function reinstate($id)
    {
        try{
            DB::beginTransaction();

            $termination = Termination::findOrFail($id);
            $employee = Employee::where('employee_id', $termination->terminate_to)->first();

            if (!$employee) {
                return redirect()->back()->with('error', 'Employee not found.');
            }

            // Check if employee is already active
            if ($employee->status == 1) {
                return redirect()->back()->with('warning', 'Employee is already active.');
            }

            // Reactivate the employee
            $employee->update([
                'status' => 1,
                'date_of_leaving' => null
            ]);

            // Reactivate user account
            User::where('id', $employee->user_id)->update(['status' => 1]);

            // Mark termination as reinstated instead of soft-deleting
            $termination->update(['reinstatement_status' => 1]);

            DB::commit();

            return redirect()->route('termination.index')->with('success', 'Employee has been successfully reinstated.');
        } catch(\Exception $e){
            DB::rollback();
            Log::error('Reinstatement error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during reinstatement: ' . $e->getMessage());
        }
    }
}