<?php

namespace App\Http\Controllers;

use App\Models\StaffContract;
use App\Http\Requests\StoreStaffContractRequest;
use App\Http\Requests\UpdateStaffContractRequest;
use App\Models\Employee;
use App\Models\EmployeeDocuments;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;

class StaffContractController extends Controller
{
  
     protected $employeeRepositories;

    public function __construct(EmployeeRepository $employeeRepositories)
    {
        $this->employeeRepositories = $employeeRepositories;
    }
    public function index()
    {
        $staffContracts = StaffContract::with('employee')->get();
       
        return view('admin.employee.staffContracts.index', ['staffContracts'=>$staffContracts]);
    }

    public function create($id = null)
    {
        $staffDetails = Employee::where('employee_id', $id)->first();
        $staffList = Employee::all();
        return view('admin.employee.staffContracts.edit', compact('staffDetails', 'staffList'));
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreStaffContractRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');
        if(isset($data['end_date']))
        {
            $end_date = $data['end_date'];
        } else {
            $end_date = '0000-00-00';
        }
        $staffContractData = [
            'employee_id' => $data['employee_id'],
            'hire_date' => $data['hire_date'],
            'probation_start_date' => $data['hire_date'],
            'probation_end_date' => $data['probation_end_date'],
            'start_date' => $data['start_date'],
            'end_date' => $end_date,
            'contract_type' => $data['contract_type']
        ];
        StaffContract::create($staffContractData);
        $employeee= Employee::where('employee_id', $request->employee_id)->first();
       
        $employeeDocuments = $this->employeeRepositories->makeEmployeeDocumentsDataFormat($request->all(), $employeee, 'update');
        if (count($employeeDocuments) > 0) {
            foreach ($employeeDocuments as $employeeDocument) {
                EmployeeDocuments::create($employeeDocument);
            }
        }

        return redirect()->route('employee.show', $employeee->employee_id)->with([ 'success'=>'Contract updated successfully']);
    }

    public function show(StaffContract $staffContract)
    {
        //
    }

    public function edit( $id)
    {
        $editModeData = StaffContract::with('employee')->findOrFail($id);
        $staffDetails = $editModeData->employee;
        $employeeDocuments = EmployeeDocuments::where('employee_id', $staffDetails->employee_id)->get();
        return view('admin.employee.staffContracts.edit', compact('editModeData', 'staffDetails', 'employeeDocuments'));
    }

    public function update( Request $request, $id)
    {
        $data = $request->except(['_token', '_method']);
        $staffContract = StaffContract::findOrFail($id);

        $staffContract->update($data);
        $employeee = Employee::where('employee_id', $request->employee_id)->first();

        // Handle deletion of existing documents
        if ($request->filled('delete_employee_documents_cid')) {
            $docIdsToDelete = explode(',', $request->delete_employee_documents_cid);
            foreach ($docIdsToDelete as $docId) {
                $docId = trim($docId);
                if (!empty($docId)) {
                    $document = EmployeeDocuments::find($docId);
                    if ($document) {
                        // Delete the file from storage
                        $filePath = public_path('uploads/employeeDocs/' . $document->document_link);
                        if ($document->document_link && file_exists($filePath)) {
                            unlink($filePath);
                        }
                        $document->delete();
                    }
                }
            }
        }

        // Add new documents
        $employeeDocuments = $this->employeeRepositories->makeEmployeeDocumentsDataFormat($request->all(), $employeee, 'update');

        if (count($employeeDocuments) > 0) {
            foreach ($employeeDocuments as $employeeDocument) {
                EmployeeDocuments::create($employeeDocument);
            }
        }

        return redirect()->back()->with([ 'success'=>'Contract updated successfully']);
    }

    public function delete($id)
    {
        try{
            $data = StaffContract::FindOrFail($id);
            $data->delete();
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

    public function destroy(StaffContract $staffContract)
    {
        //
    }
}
