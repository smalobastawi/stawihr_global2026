<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Front;

use Exception;
use App\Models\Job;
use App\Models\User;
use App\Models\Employee;
use App\Models\Services;
use App\Models\JobApplicant;
use Illuminate\Http\Request;
use App\Lib\Enumerations\JobStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobApplicationConfirmation;
use App\Mail\NewApplicationHrNotification;
use App\Http\Requests\JobApplicationRequest;

class WebController extends Controller
{
    //
    public function index(Request $request)
    {
        $published = 1;
        $active = 1;
        $job = Job::where('status', '=', $published)
            ->where('application_end_date', '>=', date('Y-m-d'))
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        if (request()->ajax()) {
            $job = Job::where('status', '=', $published)
                ->where('application_end_date', '>=', date('Y-m-d'))
                ->paginate(5);

            return View('front.job_pagination', ['jobs' => $job])->render();
        }

        $services = Services::where('status', '=', $active)->get();
        return view('front.index', ['jobs' => $job, 'services' => $services]);
    }

    public function jobDetails($id, $slug)
    {
        $job = Job::find($id);

        return view(
            'front.job_details',
            [
                'job' => $job
            ]
        );
    }

    public function jobApplyForm($id, $slug = null)
    {
        $job = Job::find($id);

        if (!$job || $job->status != 1 || $job->application_end_date < now()) {
            return redirect()->back()->with('error', 'The job you are trying to apply for is not available.');
        }

        return view('front.job_apply', [
            'job' => $job
        ]);
    }

    public function internalJobDetails($id, $slug)
    {
        $job = Job::find($id);
        if (!$job || $job->status != 1 || $job->application_end_date < now()) {
            return redirect()->back()->with('error', 'The job you are trying to view is not available.');
        }

        return view('front.internal_job_details', [
            'job' => $job
        ]);
    }

    public function jobApply(JobApplicationRequest $request)
    {
        try {

            // Check if email exists in User or Employee tables (redundant check for security)
            $emailExists = User::where('email', $request->email)->exists() || Employee::where('email', $request->email)->exists();

            if ($emailExists) {
                return redirect()->back()->with('error', 'This email is already associated with an existing employee or user account.');
            }

            // Check existing application for this job
            $existingApplication = JobApplicant::where('applicant_email', $request->email)
                ->where('job_id', $request->job_id)
                ->first();

            if ($existingApplication) {
                return redirect()->back()->with('error', 'This email has already been used for an application to this position!');
            }

            // Handle file upload
            $resumePath = $request->file('resume')->store('resumes', 'public');

            $job = Job::findOrFail($request->job_id);
            if (!$job || $job->status != 1 || $job->application_end_date < now()) {
                return redirect()->back()->with('error', 'The job you are applying for is not available.');
            }
            // Create application with all fields
            $application = JobApplicant::create([
                'job_id' => $job->job_id,
                'applicant_name' => $request->name,
                'applicant_email' => $request->email,
                'phone' => $request->phone,
                'cover_letter' => $request->input('cover_letter', ''),
                'attached_resume' => $resumePath,
                'years_of_experience' => $request->years_of_experience,
                'highest_qualification' => $request->highest_qualification,
                'location_id' => $job->location_id, // Auto-set for internal
                'application_source' => $request->application_source,
                'application_date' => now(),

                // Enhanced fields
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nationality' => $request->nationality,
                'current_address' => $request->current_address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'current_employer' => $request->current_employer,
                'current_position' => $request->current_position,
                'notice_period' => $request->notice_period,
                'expected_salary' => $request->expected_salary,
                'linkedin_url' => $request->linkedin_url,
                'portfolio_url' => $request->portfolio_url,
                'referral_source' => $request->referral_source,
                'additional_comments' => $request->additional_comments,
            ]);

            Mail::to($request->email)->send(new JobApplicationConfirmation(
                $application,
                $job->load(['branch', 'createdBy']),
                $application
            ));

            // Send notification to HR admins
            $hrAdmins = Employee::whereHas('user.roles', function ($q) {
                $q->where('name', 'HR Administrator');
            })->where('status', 1)->with('user')->get()
                ->pluck('email')
                ->filter()
                ->unique();
                
            if ($hrAdmins->isNotEmpty()) {
                Mail::to($hrAdmins)->send(new NewApplicationHrNotification(
                    $application,
                    $job
                ));
            }

            return redirect()->back()->with('success', 'Your application has been submitted successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
