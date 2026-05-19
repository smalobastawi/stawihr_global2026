<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use App\Http\Controllers\Controller;
use App\Models\EmployeeEducationQualification;
use App\Notifications\EmployeeQualificationAdded;
use App\Http\Requests\StoreEmployeeQualificationRequest;

class EmployeeEducationQualificationController extends Controller
{
    //

    public function store(StoreEmployeeQualificationRequest $request, Employee $employee)
    {
        $data = $request->except("_token");

        // Process the certificate file if uploaded
        $certificatePath = null;
        if ($request->hasFile('certificate')) {
            $certificatePath = $request->file('certificate')->store('certificates', 'public'); // Store in 'storage/app/public/certificates'
        }

        $employeeEducationQualificationData = [
            'employee_id' => $employee->employee_id,
            'institute' => $data['institute'],
            'board_university' => $data['board_university'],
            'degree' => $data['degree'],
            'passing_year' => $data['passing_year'],
            'result' => $data['result'],
            'cgpa' => $data['cgpa'],
            'certificate' => $certificatePath, // Save the file path
        ];


        EmployeeEducationQualification::create($employeeEducationQualificationData);

        // Notify approvers
        $this->notifyQualificationApprovers($employee, $employeeEducationQualificationData);

        return response()->json([
            'status' => true,
            'message' => 'Successfully added education qualifications'
        ]);
    }

    protected function notifyQualificationApprovers(Employee $employee, array $qualificationData)
    {
        $approvers = collect();

        // 1. Get all HR admins from the same region
        $hrAdmins = Employee::whereHas('user.roles', function ($query) {
            $query->where('name', 'HR Administrator');
        })
            ->whereHas('branch.region', function ($query) use ($employee) {
                if ($employee->branch && $employee->branch->region) {
                    $query->where('id', $employee->branch->region->id);
                }
            })
            ->get();

        // 2. Get verification team for certain degrees from same region
        if (in_array(strtolower($qualificationData['degree']), ['phd', 'masters', 'bachelor'])) {
            $verificationTeam = Employee::whereHas('user.roles', function ($query) {
                $query->where('name', 'Verification Officer');
            })
                ->whereHas('branch.region', function ($query) use ($employee) {
                    if ($employee->branch && $employee->branch->region) {
                        $query->where('id', $employee->branch->region->id);
                    }
                })
                ->get();

            $approvers = $approvers->merge($verificationTeam);
        }

        // 3. Get direct supervisor if exists
        if ($employee->supervisor_id) {
            $supervisor = Employee::find($employee->supervisor_id);
            if ($supervisor) {
                $approvers->push($supervisor);
            }
        }

        // 4. Get regional approvers - call the method through the Employee model
        $regionalApprovers = $employee->getLocationLeaveApprovers();
        if ($regionalApprovers->isNotEmpty()) {
            $approvers = $approvers->merge($regionalApprovers);
        }

        // Combine all approvers, remove duplicates, and filter out null values
        $approvers = $approvers->unique('employee_id')->filter();

        // Send notifications
        foreach ($approvers as $approver) {
            if ($approver->user) {
                $approver->user->notify(new EmployeeQualificationAdded($employee, $qualificationData));
            }
        }
    }
}
