<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Recruitment;

use Exception;

use App\Models\Job;
use App\Models\JobRequisition;
use App\Models\Location;
use App\Models\Department;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\JobPostRequest;
use Illuminate\Support\Facades\Storage;


class JobPostController extends Controller
{

    public function index()
    {
        $results = Job::with('createdBy')->orderBy('job_id', 'DESC')->paginate(10);
        return view('admin.recruitment.job.index', [
            'results' => $results
        ]);
    }

    public function create()
    {
        $locations = Location::where('status', 1)->get();
        $departments = Department::orderBy('department_name')->get();

        // Get all job requisitions that haven't been converted to jobs yet (any status)
        $jobRequisitions = JobRequisition::with(['location', 'department'])
            ->where(function($query) {
                $query->where('is_converted_to_job', false)
                      ->orWhereNull('is_converted_to_job');
            })
            ->orderBy('position_title')
            ->get();

        return view('admin.recruitment.job.form', [
            'locations' => $locations,
            'departments' => $departments,
            'jobRequisitions' => $jobRequisitions,
        ]);
    }

    /**
     * Get job requisition data via AJAX
     */
    public function getRequisitionData($id)
    {
        $jobRequisition = JobRequisition::with(['location', 'department'])
            ->where('job_requisition_id', $id)
            ->first();

        if (!$jobRequisition) {
            return response()->json([
                'success' => false,
                'message' => 'Job requisition not found.'
            ], 404);
        }

        // Return only public-facing fields
        return response()->json([
            'success' => true,
            'requisition_status' => $jobRequisition->status,
            'requisition_status_label' => $jobRequisition->status_label,
            'data' => [
                'job_title' => $jobRequisition->position_title,
                'job_description' => $jobRequisition->job_description,
                'job_requirements' => $jobRequisition->job_requirements,
                'job_type' => $jobRequisition->job_type,
                'employment_type' => $jobRequisition->employment_type,
                'location_id' => $jobRequisition->location_id,
                'department_id' => $jobRequisition->department_id,
                'number_of_positions' => $jobRequisition->number_of_positions,
                'minimum_salary' => $jobRequisition->minimum_salary,
                'maximum_salary' => $jobRequisition->maximum_salary,
                'minimum_qualifications' => $jobRequisition->minimum_qualifications,
                'experience_required' => $jobRequisition->experience_required,
                'skills_competencies' => $jobRequisition->skills_competencies,
                'key_responsibilities' => $jobRequisition->key_responsibilities,
                'other_benefits' => $jobRequisition->other_benefits,
                'application_end_date' => $jobRequisition->required_by_date,
                'audience_type' => $jobRequisition->recruitment_source ?? 'both',
            ]
        ]);
    }

    public function store(JobPostRequest $request)
    {
        $request->validate([
            'jd_file' => 'nullable|file|mimes:pdf,docx|max:2048', //restrict the file to 2MB
        ]);

        $input                             = $request->all();
        $input['created_by']             = Auth::user()->id;
        $input['updated_by']             = Auth::user()->id;
        $input['application_end_date']  = dateConvertFormtoDB($request->application_end_date);
        $input['publish_date']  = dateConvertFormtoDB($request->job_publish_date);
       
        if ($request->hasFile('jd_file')) {
            $file = $request->file('jd_file');
            $fileName = str_replace(' ', '_', $request->name) . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Store using Storage facade
            $path = $file->storeAs('uploads/jobDescriptions', $fileName, 'public');
            $input['jd_file'] = $fileName;
        }

        // If a job requisition is selected, link it
        if ($request->filled('job_requisition_id')) {
            $jobRequisition = JobRequisition::find($request->job_requisition_id);
            if ($jobRequisition) {
                $input['job_requisition_id'] = $request->job_requisition_id;
            }
        }
      
        try {
            $job = Job::create($input);

            // If linked to a requisition, mark the requisition as converted
            if ($request->filled('job_requisition_id')) {
                $jobRequisition = JobRequisition::find($request->job_requisition_id);
                if ($jobRequisition && !$jobRequisition->is_converted_to_job) {
                    $jobRequisition->is_converted_to_job = true;
                    $jobRequisition->converted_job_id = $job->job_id;
                    $jobRequisition->converted_at = now();
                    $jobRequisition->converted_by = Auth::id();
                    $jobRequisition->save();
                }
            }

            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('jobPost.index')->with('success', 'Job successfully created.');
        } else {
            return redirect()->route('jobPost.index')->with('error', 'Some Error Found !, Please try again.'.$bug);
        }
    }

    public function edit($id)
    {
        $editModeData = Job::with('jobRequisition')->findOrFail($id);
        $locations = Location::where('status', 1)->get();
        $departments = Department::orderBy('department_name')->get();

        // Get all job requisitions that haven't been converted, plus the current one if it has a requisition
        $jobRequisitionsQuery = JobRequisition::with(['location', 'department'])
            ->where(function($query) {
                $query->where('is_converted_to_job', false)
                      ->orWhereNull('is_converted_to_job');
            });

        // If editing a job with a requisition, include that requisition in the list
        if ($editModeData->job_requisition_id) {
            $jobRequisitionsQuery->orWhere('job_requisition_id', $editModeData->job_requisition_id);
        }

        $jobRequisitions = $jobRequisitionsQuery->orderBy('position_title')->get();

        return view('admin.recruitment.job.form', [
            'editModeData' => $editModeData,
            'locations' => $locations,
            'departments' => $departments,
            'jobRequisitions' => $jobRequisitions,
        ]);
    }

    public function show($id)
    {
        $results = Job::with(['createdBy'])->where('job_id', $id)->first();
        $employee = employeeInfo() ?? null;
        return view('admin.recruitment.job.details', [
            'result' => $results,
            'employee' => $employee
        ]);
    }

    public function viewJdFile($job_id)
    {
        try {
            $job = Job::findOrFail($job_id);
            $filePath = public_path('uploads/jobDescriptions/' . $job->jd_file);

            if (!file_exists($filePath)) {
                abort(404, "Job description file not found at: " . $filePath);
            }

            $mimeType = mime_content_type($filePath);

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $job->jd_file . '"'
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error viewing file: ' . $e->getMessage());
        }
    }

    public function downloadJdFile($job_id)
    {
        try {
            $job = Job::findOrFail($job_id);
            $filePath = public_path('uploads/jobDescriptions/' . $job->jd_file);

            if (!file_exists($filePath)) {
                abort(404, "Job description file not found.");
            }
            return response()->download($filePath, $job->jd_file);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }

    public function update(JobPostRequest $request, $id)
    {
        $data                             = Job::findOrFail($id);
        $input                             = $request->all();
        $input['created_by']             = Auth::user()->id;
        $input['updated_by']             = Auth::user()->id;
        $input['application_end_date']  = dateConvertFormtoDB($request->application_end_date);
        $input['publish_date']  = dateConvertFormtoDB($request->job_publish_date);

        // Handle job requisition change
        if ($request->filled('job_requisition_id')) {
            // If changing to a different requisition
            if ($data->job_requisition_id != $request->job_requisition_id) {
                // Unlink old requisition if exists
                if ($data->job_requisition_id) {
                    $oldRequisition = JobRequisition::find($data->job_requisition_id);
                    if ($oldRequisition) {
                        $oldRequisition->is_converted_to_job = false;
                        $oldRequisition->converted_job_id = null;
                        $oldRequisition->converted_at = null;
                        $oldRequisition->converted_by = null;
                        $oldRequisition->save();
                    }
                }

                // Link new requisition
                $newRequisition = JobRequisition::find($request->job_requisition_id);
                if ($newRequisition) {
                    $input['job_requisition_id'] = $request->job_requisition_id;
                }
            }
        } else {
            // If removing requisition link
            if ($data->job_requisition_id) {
                $oldRequisition = JobRequisition::find($data->job_requisition_id);
                if ($oldRequisition) {
                    $oldRequisition->is_converted_to_job = false;
                    $oldRequisition->converted_job_id = null;
                    $oldRequisition->converted_at = null;
                    $oldRequisition->converted_by = null;
                    $oldRequisition->save();
                }
                $input['job_requisition_id'] = null;
            }
        }

        try {
            $data->update($input);

            // Mark new requisition as converted if linked
            if ($request->filled('job_requisition_id')) {
                $newRequisition = JobRequisition::find($request->job_requisition_id);
                if ($newRequisition && !$newRequisition->is_converted_to_job) {
                    $newRequisition->is_converted_to_job = true;
                    $newRequisition->converted_job_id = $data->job_id;
                    $newRequisition->converted_at = now();
                    $newRequisition->converted_by = Auth::id();
                    $newRequisition->save();
                }
            }

            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('jobPost.index')->with('success', 'Job successfully updated.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    public function destroy($id)
    {
        try {
            $data = Job::FindOrFail($id);

            // Delink the job requisition if this job was linked to one
            if ($data->job_requisition_id) {
                $jobRequisition = JobRequisition::find($data->job_requisition_id);
                if ($jobRequisition) {
                    $jobRequisition->is_converted_to_job = false;
                    $jobRequisition->converted_job_id = null;
                    $jobRequisition->converted_at = null;
                    $jobRequisition->converted_by = null;
                    $jobRequisition->save();
                }
            }

            $data->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }
}
