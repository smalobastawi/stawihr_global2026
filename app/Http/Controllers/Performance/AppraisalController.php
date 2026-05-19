<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Performance\PerformanceAppraisal;
use App\Models\Performance\PerformanceAppraisalScore;
use App\Models\Performance\PerformanceAppraisalBehavioralScore;
use App\Models\Performance\PerformanceFocusArea;
use App\Models\Performance\PerformanceGoal;
use App\Models\Performance\PerformanceBehavioralItem;
use App\Models\Performance\PerformanceDevelopmentPlan;
use App\Models\Performance\PerformanceLearningPlan;
use App\Models\Performance\ReviewPeriod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppraisalController extends Controller
{
    public function index()
    {
        $signedInUser = Auth::user();
        $employee = Employee::where('user_id', $signedInUser->id)->first();

        if ($signedInUser->hasRole('HR Administrator') || $signedInUser->hasRole('SuperAdmin')) {
            $results = PerformanceAppraisal::with(['employee', 'supervisor'])->get();
        } elseif ($employee) {
            $results = PerformanceAppraisal::with(['employee', 'supervisor'])
                ->where('employee_id', $employee->employee_id)
                ->orWhere('supervisor_id', $employee->employee_id)
                ->get();
        } else {
            $results = collect();
        }

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.appraisal.index', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    /**
     * Show appraisals where current user is supervisor and need to be reviewed
     */
    public function supervisorEvaluations()
    {
        $signedInUser = Auth::user();
        $employee = Employee::where('user_id', $signedInUser->id)->first();

        if (!$employee) {
            return view('admin.performance.appraisal.supervisor_evaluations', [
                'results' => collect(),
                'signed_in_user_role' => null,
            ])->with('warning', 'No employee record found for your account.');
        }

        // Get appraisals where this user is the supervisor
        // and status indicates employee has completed self-evaluation
        $results = PerformanceAppraisal::with(['employee', 'supervisor'])
            ->where('supervisor_id', $employee->employee_id)
            ->whereIn('status', ['self_review', 'supervisor_review'])
            ->orderBy('created_at', 'desc')
            ->get();

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.appraisal.supervisor_evaluations', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    /**
     * Show appraisals awaiting HOD review
     */
    public function hodEvaluations()
    {
        $signedInUser = Auth::user();
        $employee = Employee::where('user_id', $signedInUser->id)->first();

        if (!$employee) {
            return view('admin.performance.appraisal.hod_evaluations', [
                'results' => collect(),
                'signed_in_user_role' => null,
            ])->with('warning', 'No employee record found for your account.');
        }

        // Get appraisals where this user is a Department Head (HOD)
        // and status indicates supervisor review is complete
        // Note: In a real scenario, you might have a department_head_id field
        // For now, we show all appraisals at supervisor_review or hod_review status
        $results = PerformanceAppraisal::with(['employee', 'supervisor'])
            ->whereIn('status', ['supervisor_review', 'hod_review'])
            ->orderBy('created_at', 'desc')
            ->get();

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.appraisal.hod_evaluations', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create()
    {
        $employees = Employee::where('status', 1)->get();
        $focusAreas = PerformanceFocusArea::where('is_active', 1)->with('goals')->get();
        $behavioralItems = PerformanceBehavioralItem::where('is_active', 1)->orderBy('sort_order')->get();
        $reviewPeriods = ReviewPeriod::active()->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $departments = Department::all();
        return view('admin.performance.appraisal.form', [
            'employees' => $employees,
            'focusAreas' => $focusAreas,
            'behavioralItems' => $behavioralItems,
            'reviewPeriods' => $reviewPeriods,
            'signed_in_user_role' => $signed_in_user_role,
            'preselectedEmployee' => null,
            'preselectedFocusAreas' => collect(),
            'departments' => $departments,
        ]);
    }

    public function focusAreasForEmployee($employeeId)
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return response()->json([]);
        }

        $focusAreas = PerformanceFocusArea::where('is_active', 1)
            ->where(function ($q) use ($employee) {
                $q->whereNull('department_id')
                    ->orWhere('department_id', $employee->department_id);
            })
            ->where(function ($q) use ($employee) {
                $q->whereNull('designation_id')
                    ->orWhere('designation_id', $employee->designation_id);
            })
            ->with('goals')
            ->get();

        return response()->json($focusAreas);
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'supervisor_id' => 'nullable|exists:employee,employee_id',
            'review_period_id' => 'required|exists:review_periods,period_id',
        ]);

        // Get the review period details
        $reviewPeriod = ReviewPeriod::findOrFail($input['review_period_id']);
        $input['review_period'] = $reviewPeriod->period_name;
        $input['review_start_date'] = $reviewPeriod->start_date;
        $input['review_end_date'] = $reviewPeriod->end_date;
        $input['status'] = 'draft';

        try {
            $appraisal = PerformanceAppraisal::create($input);

            // Pre-populate scores from goals linked to employee's department/designation
            $employee = Employee::find($input['employee_id']);
            $focusAreas = PerformanceFocusArea::where('is_active', 1)
                ->where(function ($q) use ($employee) {
                    $q->whereNull('department_id')
                        ->orWhere('department_id', $employee->department_id);
                })
                ->where(function ($q) use ($employee) {
                    $q->whereNull('designation_id')
                        ->orWhere('designation_id', $employee->designation_id);
                })
                ->with('goals')
                ->get();

            foreach ($focusAreas as $focusArea) {
                foreach ($focusArea->goals as $goal) {
                    PerformanceAppraisalScore::create([
                        'appraisal_id' => $appraisal->appraisal_id,
                        'goal_id' => $goal->goal_id,
                        'itemized_weighting' => $goal->itemized_weighting,
                        'self_weighting' => 0,
                        'review_weighting' => 0,
                    ]);
                }
            }

            // Pre-populate behavioral scores
            $behavioralItems = PerformanceBehavioralItem::where('is_active', 1)->orderBy('sort_order')->get();
            foreach ($behavioralItems as $item) {
                PerformanceAppraisalBehavioralScore::create([
                    'appraisal_id' => $appraisal->appraisal_id,
                    'behavioral_item_id' => $item->behavioral_item_id,
                    'itemized_weighting' => $item->weight,
                    'self_weighting' => 0,
                    'review_weighting' => 0,
                ]);
            }

            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.appraisal.index')->with('success', 'Performance appraisal created successfully.');
        } else {
            return redirect()->route('performance.appraisal.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    /**
     * Download CSV template for bulk uploading appraisals
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="appraisal_upload_template.csv"',
        ];

        $columns = [
            'employee_id',
            'employee_name',
            'supervisor_id',
            'supervisor_name',
            'review_period',
            'review_start_date (YYYY-MM-DD)',
            'review_end_date (YYYY-MM-DD)',
        ];

        // Get active employees for reference
        $employees = Employee::where('status', 1)
            ->select('employee_id', 'first_name', 'last_name')
            ->get()
            ->map(function ($e) {
                return [
                    $e->employee_id,
                    $e->first_name . ' ' . $e->last_name,
                    '',
                    '',
                    'e.g. Jan - June 2026',
                    date('Y-m-d'),
                    date('Y-m-d', strtotime('+6 months')),
                ];
            });

        $callback = function () use ($columns, $employees) {
            $file = fopen('php://output', 'w');

            // Add instructions as first rows
            fputcsv($file, ['PERFORMANCE APPRAISAL BULK UPLOAD TEMPLATE']);
            fputcsv($file, ['Instructions:']);
            fputcsv($file, ['1. Fill in the employee_id and supervisor_id from the Employee Reference table below']);
            fputcsv($file, ['2. review_period: Enter period like "Jan - June 2026" or "July - Dec 2026"']);
            fputcsv($file, ['3. Dates must be in YYYY-MM-DD format (e.g. 2026-01-01)']);
            fputcsv($file, ['4. All goals and behavioral items will be auto-populated based on employee department/designation']);
            fputcsv($file, ['5. Status will be set to "draft" - employee can then complete self-evaluation']);
            fputcsv($file, ['']);
            fputcsv($file, ['COLUMN HEADERS:']);
            fputcsv($file, $columns);
            fputcsv($file, ['']);
            fputcsv($file, ['SAMPLE DATA (replace with actual data):']);

            // Add sample row
            fputcsv($file, [
                'EMP001',
                'John Doe',
                'EMP002',
                'Jane Smith',
                'Jan - June 2026',
                '2026-01-01',
                '2026-06-30',
            ]);

            fputcsv($file, ['']);
            fputcsv($file, ['EMPLOYEE REFERENCE (Active Employees):']);
            fputcsv($file, ['employee_id', 'full_name', 'department_id', 'designation_id']);

            foreach (Employee::where('status', 1)->get() as $emp) {
                fputcsv($file, [
                    $emp->employee_id,
                    $emp->full_name,
                    $emp->department_id,
                    $emp->designation_id,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process bulk upload of appraisals from CSV
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $data = array_map('str_getcsv', file($path));

        if (count($data) < 2) {
            return redirect()->back()->with('error', 'CSV file is empty or invalid.');
        }

        $results = [
            'success' => [],
            'errors' => [],
        ];

        $rowNumber = 0;
        foreach ($data as $row) {
            $rowNumber++;

            // Skip header rows and empty rows
            if (empty($row[0]) || !is_numeric($row[0])) {
                continue;
            }

            $employeeId = trim($row[0]);
            $supervisorId = !empty($row[2]) ? trim($row[2]) : null;
            $reviewPeriod = trim($row[4] ?? '');
            $startDate = !empty($row[5]) ? trim($row[5]) : null;
            $endDate = !empty($row[6]) ? trim($row[6]) : null;

            // Validation
            if (empty($employeeId) || empty($reviewPeriod)) {
                $results['errors'][] = "Row {$rowNumber}: Employee ID and Review Period are required.";
                continue;
            }

            // Check if employee exists
            $employee = Employee::where('employee_id', $employeeId)->where('status', 1)->first();
            if (!$employee) {
                $results['errors'][] = "Row {$rowNumber}: Employee '{$employeeId}' not found or inactive.";
                continue;
            }

            // Check if supervisor exists (if provided)
            if ($supervisorId) {
                $supervisor = Employee::where('employee_id', $supervisorId)->where('status', 1)->first();
                if (!$supervisor) {
                    $results['errors'][] = "Row {$rowNumber}: Supervisor '{$supervisorId}' not found or inactive.";
                    continue;
                }
            }

            // Check if appraisal already exists for this employee and period
            $existing = PerformanceAppraisal::where('employee_id', $employeeId)
                ->where('review_period', $reviewPeriod)
                ->first();
            if ($existing) {
                $results['errors'][] = "Row {$rowNumber}: Appraisal already exists for employee '{$employeeId}' for period '{$reviewPeriod}'.";
                continue;
            }

            try {
                // Create appraisal
                $appraisal = PerformanceAppraisal::create([
                    'employee_id' => $employeeId,
                    'supervisor_id' => $supervisorId,
                    'review_period' => $reviewPeriod,
                    'review_start_date' => $startDate,
                    'review_end_date' => $endDate,
                    'status' => 'draft',
                ]);

                // Pre-populate scores from goals
                $focusAreas = PerformanceFocusArea::where('is_active', 1)
                    ->where(function ($q) use ($employee) {
                        $q->whereNull('department_id')
                            ->orWhere('department_id', $employee->department_id);
                    })
                    ->where(function ($q) use ($employee) {
                        $q->whereNull('designation_id')
                            ->orWhere('designation_id', $employee->designation_id);
                    })
                    ->with('goals')
                    ->get();

                foreach ($focusAreas as $focusArea) {
                    foreach ($focusArea->goals as $goal) {
                        PerformanceAppraisalScore::create([
                            'appraisal_id' => $appraisal->appraisal_id,
                            'goal_id' => $goal->goal_id,
                            'itemized_weighting' => $goal->itemized_weighting,
                            'self_weighting' => 0,
                            'review_weighting' => 0,
                        ]);
                    }
                }

                // Pre-populate behavioral scores
                $behavioralItems = PerformanceBehavioralItem::where('is_active', 1)->orderBy('sort_order')->get();
                foreach ($behavioralItems as $item) {
                    PerformanceAppraisalBehavioralScore::create([
                        'appraisal_id' => $appraisal->appraisal_id,
                        'behavioral_item_id' => $item->behavioral_item_id,
                        'itemized_weighting' => $item->weight,
                        'self_weighting' => 0,
                        'review_weighting' => 0,
                    ]);
                }

                $results['success'][] = "Row {$rowNumber}: Appraisal created for '{$employee->full_name}' ({$employeeId}) - Period: {$reviewPeriod}";
            } catch (\Exception $e) {
                $results['errors'][] = "Row {$rowNumber}: Error creating appraisal - " . $e->getMessage();
            }
        }

        $message = '';
        if (count($results['success']) > 0) {
            $message .= count($results['success']) . ' appraisal(s) created successfully. ';
        }
        if (count($results['errors']) > 0) {
            $message .= count($results['errors']) . ' error(s) found. Details: ' . implode('; ', array_slice($results['errors'], 0, 3));
            if (count($results['errors']) > 3) {
                $message .= ' ... and ' . (count($results['errors']) - 3) . ' more errors.';
            }
        }

        if (count($results['success']) > 0) {
            return redirect()->route('performance.appraisal.index')->with('success', $message);
        } else {
            return redirect()->route('performance.appraisal.index')->with('error', $message);
        }
    }

    public function show($id)
    {
        $appraisal = PerformanceAppraisal::with([
            'employee',
            'supervisor',
            'scores.goal.focusArea',
            'behavioralScores.behavioralItem',
            'developmentPlans',
            'learningPlans',
            'pipPlans'
        ])->findOrFail($id);

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        // Group scores by focus area for display
        $focusAreaScores = [];
        foreach ($appraisal->scores as $score) {
            $faId = $score->goal ? $score->goal->focus_area_id : 0;
            if (!isset($focusAreaScores[$faId])) {
                $focusAreaScores[$faId] = [
                    'focusArea' => $score->goal ? $score->goal->focusArea : null,
                    'scores' => [],
                    'self_total' => 0,
                    'review_total' => 0,
                ];
            }
            $focusAreaScores[$faId]['scores'][] = $score;
            $focusAreaScores[$faId]['self_total'] += $score->self_weighting;
            $focusAreaScores[$faId]['review_total'] += $score->review_weighting;
        }

        return view('admin.performance.appraisal.show', [
            'appraisal' => $appraisal,
            'focusAreaScores' => $focusAreaScores,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function edit($id)
    {
        $editModeData = PerformanceAppraisal::findOrFail($id);
        $employees = Employee::where('status', 1)->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.appraisal.form', [
            'editModeData' => $editModeData,
            'employees' => $employees,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $appraisal = PerformanceAppraisal::findOrFail($id);

        $input = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'supervisor_id' => 'nullable|exists:employee,employee_id',
            'review_period' => 'required|string|max:100',
            'review_start_date' => 'nullable|date',
            'review_end_date' => 'nullable|date',
        ]);

        try {
            $appraisal->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.appraisal.index')->with('success', 'Performance appraisal updated successfully.');
        } else {
            return redirect()->route('performance.appraisal.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $appraisal = PerformanceAppraisal::findOrFail($id);
            $appraisal->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo 'error';
        }
    }

    // Self Review
    public function selfReview($id)
    {
        $appraisal = PerformanceAppraisal::with([
            'scores.goal.focusArea',
            'behavioralScores.behavioralItem',
            'employee'
        ])->findOrFail($id);

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        // Group by focus area
        $focusAreaScores = [];
        foreach ($appraisal->scores as $score) {
            $faId = $score->goal ? $score->goal->focus_area_id : 0;
            if (!isset($focusAreaScores[$faId])) {
                $focusAreaScores[$faId] = [
                    'focusArea' => $score->goal ? $score->goal->focusArea : null,
                    'scores' => [],
                ];
            }
            $focusAreaScores[$faId]['scores'][] = $score;
        }

        return view('admin.performance.appraisal.self_review', [
            'appraisal' => $appraisal,
            'focusAreaScores' => $focusAreaScores,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function saveSelfReview(Request $request, $id)
    {
        $appraisal = PerformanceAppraisal::findOrFail($id);

        $scores = $request->input('scores', []);
        $comments = $request->input('comments', []);

        foreach ($scores as $scoreId => $selfWeighting) {
            $score = PerformanceAppraisalScore::find($scoreId);
            if ($score) {
                $score->self_weighting = $selfWeighting;
                $score->self_comments = $comments[$scoreId] ?? null;
                $score->save();
            }
        }

        // Behavioral scores
        $behavioralScores = $request->input('behavioral_scores', []);
        $behavioralComments = $request->input('behavioral_comments', []);

        foreach ($behavioralScores as $scoreId => $selfWeighting) {
            $score = PerformanceAppraisalBehavioralScore::find($scoreId);
            if ($score) {
                $score->self_weighting = $selfWeighting;
                $score->self_comments = $behavioralComments[$scoreId] ?? null;
                $score->save();
            }
        }

        $appraisal->status = 'self_review';
        $appraisal->save();

        return redirect()->route('performance.appraisal.show', $id)->with('success', 'Self review saved successfully.');
    }

    // Supervisor Review
    public function supervisorReview($id)
    {
        $appraisal = PerformanceAppraisal::with([
            'scores.goal.focusArea',
            'behavioralScores.behavioralItem',
            'employee'
        ])->findOrFail($id);

        // ENFORCE WORKFLOW: Supervisor can only review after employee has completed self-evaluation
        // Status must be 'self_review' or 'supervisor_review' (NOT 'draft')
        if (!in_array($appraisal->status, ['self_review', 'supervisor_review'])) {
            return redirect()->route('performance.appraisal.index')
                ->with('error', 'Supervisor review is not available yet. Employee must complete self-evaluation first.');
        }

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $focusAreaScores = [];
        foreach ($appraisal->scores as $score) {
            $faId = $score->goal ? $score->goal->focus_area_id : 0;
            if (!isset($focusAreaScores[$faId])) {
                $focusAreaScores[$faId] = [
                    'focusArea' => $score->goal ? $score->goal->focusArea : null,
                    'scores' => [],
                ];
            }
            $focusAreaScores[$faId]['scores'][] = $score;
        }

        return view('admin.performance.appraisal.supervisor_review', [
            'appraisal' => $appraisal,
            'focusAreaScores' => $focusAreaScores,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function saveSupervisorReview(Request $request, $id)
    {
        $appraisal = PerformanceAppraisal::findOrFail($id);

        // ENFORCE WORKFLOW: Supervisor can only save review after employee has completed self-evaluation
        if (!in_array($appraisal->status, ['self_review', 'supervisor_review'])) {
            return redirect()->route('performance.appraisal.index')
                ->with('error', 'Cannot save supervisor review. Employee must complete self-evaluation first.');
        }

        $scores = $request->input('scores', []);
        $comments = $request->input('comments', []);

        foreach ($scores as $scoreId => $reviewWeighting) {
            $score = PerformanceAppraisalScore::find($scoreId);
            if ($score) {
                $score->review_weighting = $reviewWeighting;
                $score->review_comments = $comments[$scoreId] ?? null;
                $score->save();
            }
        }

        // Behavioral scores
        $behavioralScores = $request->input('behavioral_scores', []);
        $behavioralComments = $request->input('behavioral_comments', []);

        foreach ($behavioralScores as $scoreId => $reviewWeighting) {
            $score = PerformanceAppraisalBehavioralScore::find($scoreId);
            if ($score) {
                $score->review_weighting = $reviewWeighting;
                $score->review_comments = $behavioralComments[$scoreId] ?? null;
                $score->save();
            }
        }

        $appraisal->supervisor_comments = $request->input('supervisor_comments');
        $appraisal->status = 'supervisor_review';
        $appraisal->save();

        return redirect()->route('performance.appraisal.show', $id)->with('success', 'Supervisor review saved successfully.');
    }

    // HOD Review
    public function hodReview($id)
    {
        $appraisal = PerformanceAppraisal::with([
            'scores.goal.focusArea',
            'behavioralScores.behavioralItem',
            'employee',
            'developmentPlans',
            'learningPlans'
        ])->findOrFail($id);

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $focusAreaScores = [];
        foreach ($appraisal->scores as $score) {
            $faId = $score->goal ? $score->goal->focus_area_id : 0;
            if (!isset($focusAreaScores[$faId])) {
                $focusAreaScores[$faId] = [
                    'focusArea' => $score->goal ? $score->goal->focusArea : null,
                    'scores' => [],
                ];
            }
            $focusAreaScores[$faId]['scores'][] = $score;
        }

        return view('admin.performance.appraisal.hod_review', [
            'appraisal' => $appraisal,
            'focusAreaScores' => $focusAreaScores,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function saveHodReview(Request $request, $id)
    {
        $appraisal = PerformanceAppraisal::findOrFail($id);

        // Update development plans
        if ($request->has('development_plans')) {
            foreach ($request->input('development_plans', []) as $planId => $planData) {
                $plan = PerformanceDevelopmentPlan::find($planId);
                if ($plan) {
                    $plan->update($planData);
                }
            }
        }

        // Create new development plans
        if ($request->has('new_development_plans')) {
            foreach ($request->input('new_development_plans', []) as $planData) {
                if (!empty($planData['competency_name'])) {
                    $planData['appraisal_id'] = $id;
                    PerformanceDevelopmentPlan::create($planData);
                }
            }
        }

        // Update learning plans
        if ($request->has('learning_plans')) {
            foreach ($request->input('learning_plans', []) as $planId => $planData) {
                $plan = PerformanceLearningPlan::find($planId);
                if ($plan) {
                    $plan->update($planData);
                }
            }
        }

        // Create new learning plans
        if ($request->has('new_learning_plans')) {
            foreach ($request->input('new_learning_plans', []) as $planData) {
                if (!empty($planData['course_title'])) {
                    $planData['appraisal_id'] = $id;
                    PerformanceLearningPlan::create($planData);
                }
            }
        }

        $appraisal->hod_comments = $request->input('hod_comments');
        $appraisal->status = 'hod_review';
        $appraisal->save();

        return redirect()->route('performance.appraisal.show', $id)->with('success', 'HOD review saved successfully.');
    }

    public function finalize(Request $request, $id)
    {
        $appraisal = PerformanceAppraisal::with('scores.goal.focusArea')->findOrFail($id);
        $signedInUser = Auth::user();
        $employee = Employee::where('user_id', $signedInUser->id)->first();

        $totalItemized = $appraisal->scores()->sum('itemized_weighting');
        $totalSelf = $appraisal->scores()->sum('self_weighting') + $appraisal->behavioralScores()->sum('self_weighting');
        $totalReview = $appraisal->scores()->sum('review_weighting') + $appraisal->behavioralScores()->sum('review_weighting');

        $appraisal->total_itemized_weighting = $totalItemized;
        $appraisal->total_self_weighting = $totalSelf;
        $appraisal->total_review_weighting = $totalReview;
        $appraisal->status = 'finalized';
        $appraisal->finalized_by = $employee ? $employee->employee_id : null;
        $appraisal->finalized_at = now();
        $appraisal->save();

        return redirect()->route('performance.appraisal.show', $id)->with('success', 'Performance appraisal finalized.');
    }

    // Sign-offs
    public function employeeSign($id)
    {
        $appraisal = PerformanceAppraisal::findOrFail($id);
        $appraisal->employee_signed = true;
        $appraisal->employee_sign_date = now();
        $appraisal->save();

        return redirect()->route('performance.appraisal.show', $id)->with('success', 'Employee signature recorded.');
    }

    public function supervisorSign($id)
    {
        $appraisal = PerformanceAppraisal::findOrFail($id);
        $appraisal->supervisor_signed = true;
        $appraisal->supervisor_sign_date = now();
        $appraisal->save();

        return redirect()->route('performance.appraisal.show', $id)->with('success', 'Supervisor signature recorded.');
    }

    public function hodSign($id)
    {
        $appraisal = PerformanceAppraisal::findOrFail($id);
        $appraisal->hod_signed = true;
        $appraisal->hod_sign_date = now();
        $appraisal->save();

        return redirect()->route('performance.appraisal.show', $id)->with('success', 'HOD signature recorded.');
    }
}
