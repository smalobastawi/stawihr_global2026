<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Recruitment;

use Exception;

use App\Models\Job;

use App\Models\User;

use App\Models\Employee;

use App\Models\Interview;

use Illuminate\Support\Str;

use App\Models\JobApplicant;

use Illuminate\Http\Request;
use App\Mail\HireNotificationMail;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Lib\Enumerations\JobStatus;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\Approval\ScheduledInterview;
use App\Http\Requests\JobInterviewRequest;

class JobCandidateController extends Controller
{

    public $perPage = 10;

    public function index()
    {
        $results = Job::select(
            'job.*',
            DB::raw('(select count(job_applicant_id) from job_applicant where (status = ' . JobStatus::$SHORTLIST . ' or status = ' . JobStatus::$CALL_FOR_INTERVIEW . ') and job.job_id = job_applicant.job_id) as shortList'),
            DB::raw('(select count(job_applicant_id) from job_applicant where status = ' . JobStatus::$REJECT . ' and job.job_id = job_applicant.job_id) as reject'),
            DB::raw('(select count(job_applicant_id) from job_applicant where job.job_id = job_applicant.job_id) as totalApplication'),
            DB::raw('(SELECT COUNT(job_applicant_id) FROM job_applicant WHERE status = ' . JobStatus::$CALL_FOR_INTERVIEW . ' AND job.job_id = job_applicant.job_id) AS interview'),
            DB::raw('(SELECT COUNT(job_applicant_id) FROM job_applicant WHERE status = ' . JobStatus::$HIRE . ' AND job.job_id = job_applicant.job_id) AS hire')
        )
            ->orderBy('job_id', 'DESC')->paginate($this->perPage);
        return view('admin.recruitment.jobCandidate.index', ['results' => $results]);
    }

    public function applyCandidateList($id)
    {
        $job     = Job::where('job_id', $id)->first();
        $results = JobApplicant::with('job')->where('job_id', $id)->orderBy('status', 'ASC')->orderBy('job_applicant_id', 'DESC')->paginate($this->perPage);
        return view('admin.recruitment.jobCandidate.applyCandidateList', [
            'results' => $results,
            'job' => $job,
            'job_id' => $id
        ]);
    }

    public function searchCandidateList(Request $request, $job_id)
    {

        $id = $job_id;
        $job     = Job::where('job_id', $id)->first();
        // Retrieve filter inputs
        $experience_id = $request->input('experience_id');  // Years of experience
        $highest_qualification = $request->input('highest_qualification');  // Highest qualification

        // Start building the query
        $query = JobApplicant::query();

        // Filter by experience if provided
        if (!empty($experience_id)) {
            $query->where('years_of_experience', $experience_id);
        }

        // Filter by highest qualification if provided
        if ($highest_qualification && $highest_qualification !== 'None') {
            $query->where('highest_qualification', $highest_qualification);
        }
        // Filter by job ID
        $query->where('job_id', $id);

        // Execute the query and get the results
        $results = $query->paginate(10); // 10 items per page (adjust as needed)


        return view('admin.recruitment.jobCandidate.applyCandidateList', [
            'results' => $results,
            'job' => $job,
            'job_id' => $id
        ]);
    }


    public function shortlist($id)
    {
        try {
            JobApplicant::where('job_applicant_id', $id)->update(['status' => JobStatus::$SHORTLIST]);
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Job application shortListed.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    public function reject($id)
    {
        try {
            JobApplicant::where('job_applicant_id', $id)
                ->update(['status' => JobStatus::$REJECT]);
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Job application rejected.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    public function shortListedApplicant($id)
    {
        $job     = Job::where('job_id', $id)->first();
        $results = JobApplicant::where('job_id', $id)
            ->where(function ($query) {
                $query->where('status', JobStatus::$SHORTLIST)
                    ->orWhere('status', JobStatus::$CALL_FOR_INTERVIEW);
            })
            ->orderBy('status', 'ASC')
            ->paginate($this->perPage);

        return view('admin.recruitment.jobCandidate.shortListedApplicant', ['results' => $results, 'job' => $job]);
    }

    public function jobInterview($id)
    {
        $results = JobApplicant::with('job')->where('job_applicant_id', $id)
            ->where('status', JobStatus::$SHORTLIST)
            ->first();
        return view('admin.recruitment.jobCandidate.callForInterview', ['results' => $results]);
    }

    public function jobInterviewStore(JobInterviewRequest $request, $id)
    {

        $input                         = $request->all();
        $input['job_applicant_id']     = $id;
        $input['interview_time']     = date("H:i:s", strtotime($request->interview_time));
        $input['interview_date']    = dateConvertFormtoDB($request->interview_date);

        try {
            DB::beginTransaction();

            Interview::create($input);

            $data = JobApplicant::where('job_applicant_id', $id)->first();

            //get the email variables
            //inform the applicant about the interview through email
            $applicantName = $data->applicant_name;
            $applicantEmail = $data->applicant_email;
            $job = $data->job_id;
            $jobtitle = Job::where('job_id', $job)->first()->job_title;
            $interviewTime = date("H:i:s", strtotime($request->interview_time));
            $interviewDate   = dateConvertFormtoDB($request->interview_date);

            //update applicant status to call for interview
            $data->update(['status' => JobStatus::$CALL_FOR_INTERVIEW]);

            DB::commit();
            $bug = 0;
            //send email to the applicant            
            try {
                Mail::to($applicantEmail)->send(new ScheduledInterview($applicantName, $applicantEmail, $jobtitle, $interviewDate, $interviewTime));
            } catch (\Exception $e) {
                Log::info($e->getMessage() . ' We could not send email to the applicant.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('jobCandidate.shortListedApplicant', $data->job_id)->with('success', 'Job interview added.');
        } else {
            return redirect()->back()->with('error', 'Some Error Found !, Please try again.');
        }
    }

    public function rejectedApplicant($id)
    {
        $job     = Job::where('job_id', $id)->first();
        $results = JobApplicant::where('job_id', $id)->where('status', JobStatus::$REJECT)->paginate($this->perPage);
        return view('admin.recruitment.jobCandidate.rejectedApplicant', ['results' => $results, 'job' => $job]);
    }

    public function jobInterviewList($id)
    {
        $job     = Job::where('job_id', $id)->first();
        $results = JobApplicant::with('interviewInfo')->where('job_id', $id)->where('status', JobStatus::$CALL_FOR_INTERVIEW)->paginate($this->perPage);
        return view('admin.recruitment.jobCandidate.interviewList', ['results' => $results, 'job' => $job]);
    }

    public function jobHireList($id)
    {
        $job     = Job::where('job_id', $id)->first();
        $results = JobApplicant::with('interviewInfo')->where('job_id', $id)->where('status', JobStatus::$HIRE)->paginate($this->perPage);
        return view('admin.recruitment.jobCandidate.hireList', [
            'results' => $results,
            'job' => $job
        ]);
    }

    public function hire($id)
    {

        try {
            DB::beginTransaction();

            $data = JobApplicant::where('job_applicant_id', $id)->first();
            $data->update(['status' => JobStatus::$HIRE, 'hire_date' => date('y-m-d')]);

            // find employee by email 
            $existingEmployee  = Employee::where('email', $data->applicant_email)->first();
            if ($existingEmployee) {
                // If employee already exists, update the status and other details
                return redirect()->route('employee.edit', [
                    'employee' => $existingEmployee->employee_id,
                ])->with('error', 'This applicant is already an employee. Please edit their details.');
            }

            // Create user account if applicant is external (internal applicants may already have accounts)
            $username = $this->generateUsernameFromEmail($data->applicant_email);
            $password = Str::random(12); // Generate Random password

            // Create user
            $user = User::create([
                'user_name' => $username,
                'email' => $data->applicant_email,
                'password' => bcrypt($password),
                'status' => 1, // Assuming 1 means active
                'created_by' => auth()->id(),
                'password_changed_at' => now(),
            ]);

            // Handle Employee role assignment
            $roleName = 'Employee';
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $role = Role::create([
                    'name' => $roleName,
                    'guard_name' => 'web'
                ]);
            }

            // Assign Employee role
            $user->assignRole($role);

            // Split name into components
            $nameParts = $this->splitFullName($data->applicant_name);

            // Create employee with separated names
            $employee = Employee::create([
                'user_id' => $user->id,
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                'middle_name' => $nameParts['middle_name'] ?? null,
                'email' => $data->applicant_email,
                'phone' => $data->phone,
                'location_id' => $data->location_id,
                'status' => 1,
                'date_of_joining' => $data->hire_date,
                'created_by' => auth()->id(),
            ]);

            $data->update(['employee_id' => $employee->employee_id]);

            // Send email notification
            Mail::to($data->applicant_email)->send(new HireNotificationMail($username, $password));

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('employee.edit', [
                'employee' => $employee->employee_id,
            ])
                ->with('success', 'Candidate hired successfully.');
        } else {
            return redirect()->back()->with('error', 'Some Error Found !, Please try again.');
        }
    }

    /**
     * Split full name into first, middle, and last name components
     */
    protected function splitFullName($fullName)
    {
        $fullName = trim($fullName);
        $nameParts = preg_split('/\s+/', $fullName);

        $result = [
            'first_name' => '',
            'middle_name' => '',
            'last_name' => ''
        ];

        // Handle empty names
        if (empty($nameParts)) {
            return $result;
        }

        // First name is always the first part
        $result['first_name'] = array_shift($nameParts);

        // Last name is the last part if multiple parts exist
        if (!empty($nameParts)) {
            $result['last_name'] = array_pop($nameParts);
        }

        // Anything remaining is middle name
        if (!empty($nameParts)) {
            $result['middle_name'] = implode(' ', $nameParts);
        }

        return $result;
    }

    protected function generateUsernameFromEmail($email)
    {
        $username = strtok($email, '@'); // Get part before @
        $username = preg_replace('/[^a-zA-Z0-9]/', '', $username); // Remove special chars

        // Check if username exists and append numbers if needed
        $originalUsername = $username;
        $counter = 1;

        while (User::where('user_name', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        return $username;
    }

    public function viewResume($id)
    {
        try {
            $applicant = JobApplicant::findOrFail($id);

            if (!Storage::disk('public')->exists($applicant->attached_resume)) {
                abort(404, 'Resume not found.');
            }

            $filePath = Storage::disk('public')->path($applicant->attached_resume);
            $mimeType = Storage::disk('public')->mimeType($applicant->attached_resume);
            $extension = pathinfo($applicant->attached_resume, PATHINFO_EXTENSION);

            // For PDF files
            if ($extension === 'pdf') {
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . 'Resume_' . $applicant->applicant_name . '.pdf"'
                ]);
            }
            // For DOCX files
            elseif ($extension === 'docx') {
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . 'Resume_' . $applicant->applicant_name . '.docx"'
                ]);
            }
            // For other file types, fall back to download
            else {
                return Storage::disk('public')->download(
                    $applicant->attached_resume,
                    'Resume_' . $applicant->applicant_name . '.' . $extension
                );
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error viewing resume: ' . $e->getMessage());
        }
    }

    public function downloadResume($id)
    {

        try {

            $applicant = JobApplicant::findOrFail($id);

            if (!Storage::disk('public')->exists($applicant->attached_resume)) {
                abort(404, 'Resume not found.');
            }

            return Storage::disk('public')->download(
                $applicant->attached_resume,
                'Resume_' . $applicant->applicant_name . '.' . pathinfo($applicant->attached_resume, PATHINFO_EXTENSION)
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading resume: ' . $e->getMessage());
        }
    }
}
