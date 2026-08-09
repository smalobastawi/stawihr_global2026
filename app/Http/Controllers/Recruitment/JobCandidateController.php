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
use App\Lib\Enumerations\QualificationLevel;
use App\Support\AtsMatcher;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\Approval\ScheduledInterview;
use App\Http\Requests\JobInterviewRequest;

class JobCandidateController extends Controller
{

    public $perPage = 10;

    public function index(Request $request)
    {
        $jobs = Job::with('location')
            ->orderBy('job_title', 'asc')
            ->get(['job_id', 'job_title', 'location_id']);

        $view = $request->get('view', 'pipeline');
        if (!in_array($view, ['pipeline', 'jobs'], true)) {
            $view = 'pipeline';
        }

        $stage = $request->get('stage', 'applications');
        $allowedStages = ['applications', 'shortlisted', 'rejected', 'interview', 'hired'];
        if (!in_array($stage, $allowedStages, true)) {
            $stage = 'applications';
        }

        $sort = $request->get('sort', 'application_date');
        $allowedSorts = ['applicant_name', 'application_date', 'years_of_experience', 'highest_qualification', 'status', 'match_score'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'application_date';
        }

        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $filters = $this->extractAtsFilters($request);

        $jobId = $request->filled('job_id') ? (int) $request->job_id : null;
        if ($view === 'pipeline' && !$jobId && $jobs->isNotEmpty()) {
            $jobId = $jobs->first()->job_id;
        }

        $selectedJob = $jobId ? Job::with('location')->find($jobId) : null;
        $applicants = null;
        $stageCounts = [];

        if ($view === 'pipeline' && $jobId && $selectedJob) {
            $stageCounts = $this->applicantStageCounts($jobId);
            $query = $this->applicantsForStage($jobId, $stage);
            $this->applyAtsFilters($query, $filters, $selectedJob);

            if ($sort === 'match_score') {
                $allApplicants = $query->get()->map(function (JobApplicant $applicant) use ($selectedJob) {
                    $applicant->match_score = AtsMatcher::score($selectedJob, $applicant);
                    $applicant->meets_criteria = AtsMatcher::meetsCriteria($selectedJob, $applicant);

                    return $applicant;
                });

                $sorted = $direction === 'asc'
                    ? $allApplicants->sortBy('match_score')->values()
                    : $allApplicants->sortByDesc('match_score')->values();

                $page = max(1, (int) $request->get('page', 1));
                $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
                    $sorted->forPage($page, $this->perPage)->values(),
                    $sorted->count(),
                    $this->perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                $applicants = $query->orderBy($sort, $direction)
                    ->paginate($this->perPage)
                    ->withQueryString();

                $applicants->getCollection()->transform(function (JobApplicant $applicant) use ($selectedJob) {
                    $applicant->match_score = AtsMatcher::score($selectedJob, $applicant);
                    $applicant->meets_criteria = AtsMatcher::meetsCriteria($selectedJob, $applicant);

                    return $applicant;
                });
            }
        }

        $jobSummaries = null;
        if ($view === 'jobs') {
            $jobSummaries = $this->jobSummariesQuery()
                ->paginate($this->perPage)
                ->withQueryString();
        }

        return view('admin.recruitment.jobCandidate.index', [
            'jobs' => $jobs,
            'view' => $view,
            'stage' => $stage,
            'sort' => $sort,
            'direction' => $direction,
            'jobId' => $jobId,
            'selectedJob' => $selectedJob,
            'applicants' => $applicants,
            'stageCounts' => $stageCounts,
            'jobSummaries' => $jobSummaries,
            'filters' => $filters,
            'qualificationOptions' => QualificationLevel::options(),
        ]);
    }

    private function extractAtsFilters(Request $request): array
    {
        return [
            'min_experience' => $request->filled('min_experience') ? (int) $request->min_experience : null,
            'qualification' => $request->filled('qualification') ? $request->qualification : null,
            'skill' => $request->filled('skill') ? trim($request->skill) : null,
            'application_source' => $request->filled('application_source') ? $request->application_source : null,
            'notice_period' => $request->filled('notice_period') ? $request->notice_period : null,
            'max_expected_salary' => $request->filled('max_expected_salary') ? (float) $request->max_expected_salary : null,
            'meets_criteria' => $request->boolean('meets_criteria'),
            'keyword' => $request->filled('keyword') ? trim($request->keyword) : null,
        ];
    }

    private function applyAtsFilters($query, array $filters, Job $job): void
    {
        if ($filters['min_experience'] !== null) {
            $query->where('years_of_experience', '>=', $filters['min_experience']);
        }

        if (!empty($filters['qualification']) && $filters['qualification'] !== QualificationLevel::NONE) {
            $query->whereIn('highest_qualification', QualificationLevel::atOrAbove($filters['qualification']));
        }

        if (!empty($filters['skill'])) {
            $skill = $filters['skill'];
            $query->where(function ($q) use ($skill) {
                $q->where('skills', 'like', '%' . $skill . '%');
            });
        }

        if (!empty($filters['application_source']) && in_array($filters['application_source'], ['internal', 'external'], true)) {
            $query->where('application_source', $filters['application_source']);
        }

        if (!empty($filters['notice_period'])) {
            $query->where('notice_period', $filters['notice_period']);
        }

        if ($filters['max_expected_salary'] !== null) {
            $query->where(function ($q) use ($filters) {
                $q->whereNull('expected_salary')
                    ->orWhere('expected_salary', '<=', $filters['max_expected_salary']);
            });
        }

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('applicant_name', 'like', '%' . $keyword . '%')
                    ->orWhere('applicant_email', 'like', '%' . $keyword . '%')
                    ->orWhere('skills', 'like', '%' . $keyword . '%')
                    ->orWhere('current_position', 'like', '%' . $keyword . '%')
                    ->orWhere('current_employer', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($filters['meets_criteria'])) {
            if ($job->min_years_experience !== null) {
                $query->where('years_of_experience', '>=', (int) $job->min_years_experience);
            }

            if (!empty($job->required_qualification) && $job->required_qualification !== QualificationLevel::NONE) {
                $query->whereIn('highest_qualification', QualificationLevel::atOrAbove($job->required_qualification));
            }

            foreach (AtsMatcher::parseSkills($job->required_skills) as $requiredSkill) {
                $query->where('skills', 'like', '%' . $requiredSkill . '%');
            }
        }
    }

    private function jobSummariesQuery()
    {
        return Job::with('location')->select(
            'job.*',
            DB::raw('(select count(job_applicant_id) from job_applicant where (status = ' . JobStatus::$SHORTLIST . ' or status = ' . JobStatus::$CALL_FOR_INTERVIEW . ') and job.job_id = job_applicant.job_id) as shortList'),
            DB::raw('(select count(job_applicant_id) from job_applicant where status = ' . JobStatus::$REJECT . ' and job.job_id = job_applicant.job_id) as reject'),
            DB::raw('(select count(job_applicant_id) from job_applicant where job.job_id = job_applicant.job_id) as totalApplication'),
            DB::raw('(SELECT COUNT(job_applicant_id) FROM job_applicant WHERE status = ' . JobStatus::$CALL_FOR_INTERVIEW . ' AND job.job_id = job_applicant.job_id) AS interview'),
            DB::raw('(SELECT COUNT(job_applicant_id) FROM job_applicant WHERE status = ' . JobStatus::$HIRE . ' AND job.job_id = job_applicant.job_id) AS hire')
        )->orderBy('job_id', 'DESC');
    }

    private function applicantsForStage(int $jobId, string $stage)
    {
        $query = JobApplicant::where('job_id', $jobId);

        return match ($stage) {
            'shortlisted' => $query->where('status', JobStatus::$SHORTLIST),
            'rejected' => $query->where('status', JobStatus::$REJECT),
            'interview' => $query->where('status', JobStatus::$CALL_FOR_INTERVIEW),
            'hired' => $query->where('status', JobStatus::$HIRE),
            default => $query,
        };
    }

    private function applicantStageCounts(int $jobId): array
    {
        return [
            'applications' => JobApplicant::where('job_id', $jobId)->count(),
            'shortlisted' => JobApplicant::where('job_id', $jobId)->where('status', JobStatus::$SHORTLIST)->count(),
            'rejected' => JobApplicant::where('job_id', $jobId)->where('status', JobStatus::$REJECT)->count(),
            'interview' => JobApplicant::where('job_id', $jobId)->where('status', JobStatus::$CALL_FOR_INTERVIEW)->count(),
            'hired' => JobApplicant::where('job_id', $jobId)->where('status', JobStatus::$HIRE)->count(),
        ];
    }

    public function applyCandidateList($id)
    {
        $job = Job::where('job_id', $id)->firstOrFail();
        $results = JobApplicant::with('job')
            ->where('job_id', $id)
            ->orderBy('status', 'ASC')
            ->orderBy('job_applicant_id', 'DESC')
            ->paginate($this->perPage);

        $results->getCollection()->transform(function (JobApplicant $applicant) use ($job) {
            $applicant->match_score = AtsMatcher::score($job, $applicant);
            $applicant->meets_criteria = AtsMatcher::meetsCriteria($job, $applicant);

            return $applicant;
        });

        return view('admin.recruitment.jobCandidate.applyCandidateList', [
            'results' => $results,
            'job' => $job,
            'job_id' => $id,
            'filters' => $this->extractAtsFilters(request()),
        ]);
    }

    public function searchCandidateList(Request $request, $job_id)
    {
        $id = $job_id;
        $job = Job::where('job_id', $id)->firstOrFail();
        $filters = $this->extractAtsFilters($request);

        // Backward-compatible aliases from the legacy filter form
        if (!$filters['min_experience'] && $request->filled('experience_id')) {
            $filters['min_experience'] = (int) $request->experience_id;
        }
        if (empty($filters['qualification']) && $request->filled('highest_qualification')) {
            $filters['qualification'] = $request->highest_qualification;
        }

        $query = JobApplicant::where('job_id', $id);
        $this->applyAtsFilters($query, $filters, $job);

        $results = $query->orderBy('status', 'ASC')
            ->orderBy('job_applicant_id', 'DESC')
            ->paginate(10)
            ->withQueryString();

        $results->getCollection()->transform(function (JobApplicant $applicant) use ($job) {
            $applicant->match_score = AtsMatcher::score($job, $applicant);
            $applicant->meets_criteria = AtsMatcher::meetsCriteria($job, $applicant);

            return $applicant;
        });

        return view('admin.recruitment.jobCandidate.applyCandidateList', [
            'results' => $results,
            'job' => $job,
            'job_id' => $id,
            'filters' => $filters,
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
