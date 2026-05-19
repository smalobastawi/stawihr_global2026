<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\EmployeeFeedback;
use App\Http\Requests\StoreEmployeeFeedbackRequest;
use App\Http\Requests\UpdateEmployeeFeedbackRequest;
use App\Models\Location;
use App\Models\Employee;
use App\Models\EmployeeFeedbackResponse;
use App\Models\FeedbackCategories;
use Illuminate\Support\Facades\Auth;

class EmployeeFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employeeID = Employee::where('user_id', Auth::id())->first();
        if (!$employeeID) {
            $employeeID = new Employee();
        }
        $data = EmployeeFeedback::where('employee_id', $employeeID->employee_id)->with('response')->get();
        return view('admin.employee-feedback.feedback.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = FeedbackCategories::get();

        return view('admin.employee-feedback.feedback.edit', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeeFeedbackRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeFeedbackRequest $request)
    {
        $employeeID = Employee::where('user_id', Auth::id())->first();
        if (!$employeeID) {
            abort(403, 'Forbidden: You do not have access to this resource.');
        }
        $fallBackBranchID = Location::first()->location_id;
        $feedback = new EmployeeFeedback();
        $feedback->employee_id = $employeeID->employee_id;
        $feedback->location_id = $employeeID->location_id ? $employeeID->location_id : $fallBackBranchID;
        $feedback->title = $request->input('title');
        $feedback->content = $request->input('content');
        $feedback->category_id = $request->input('category_id');
        $feedback->financial_year_id = getCurrentFinancialYear()->id;
        $feedback->created_at = now();
        $feedback->save();

        return redirect()->route('ess.feedback.index')->with('success', 'Feedback submitted successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmployeeFeedback  $employeeFeedback
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $feedback = EmployeeFeedback::with(['category', 'response'])->findOrFail($id);

        return response()->json($feedback);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmployeeFeedback  $employeeFeedback
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeFeedback $employeeFeedback)
    {
        return view('admin.employee-feedback.feedback.edit', compact('employeeFeedback'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeFeedbackRequest  $request
     * @param  \App\Models\EmployeeFeedback  $employeeFeedback
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeFeedbackRequest $request, EmployeeFeedback $employeeFeedback)
    {
        $employeeFeedback->feedback = $request->input('feedback');
        $employeeFeedback->save();

        return redirect()->route('ess.feedback.index')->with('success', 'Feedback updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmployeeFeedback  $employeeFeedback
     * @return \Illuminate\Http\Response
     */
    public function destroy($employeeFeedback)
    {

        $employeeFeedback = EmployeeFeedback::findOrFail($employeeFeedback); // Log the ID of the feedback being deleted
        $count = EmployeeFeedbackResponse::where('feedback_id', '=', $employeeFeedback)->count();


        if ($count > 0) {

            return  'hasForeignKey';
        }


        try {
            $employeeFeedback->delete();

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

        // return redirect()->route('ess.feedback.index')->with('success', 'Feedback deleted successfully.');
    }
}
