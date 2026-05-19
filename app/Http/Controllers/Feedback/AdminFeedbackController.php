<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\EmployeeFeedback;
use App\Http\Requests\StoreEmployeeFeedbackRequest;
use App\Http\Requests\UpdateEmployeeFeedbackRequest;
use App\Lib\Enumerations\FeedbackStatus;
use App\Models\Employee;
use App\Models\EmployeeFeedbackResponse;
use App\Models\FeedbackCategories;
use Google\Service\Classroom\Feed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) {
            abort(403, 'Forbidden: You do not have access to this resource.');
        }
        $data = EmployeeFeedback::where('employee_id', '!=', $employee->employee_id)->with(['response', 'employee'])->get();

        return view('admin.admin-feedback.index', compact('data'));
    }

    public function respond($id)
    {
        $data = EmployeeFeedback::with(['category', 'employee'])->findOrFail($id);
        return view('admin.admin-feedback.respond', compact('data'));
    }

    public function storeResponse(Request $request)
    {

        $employeeID = Employee::where('user_id', Auth::id())->first();
        $feedback = EmployeeFeedback::findOrFail($request->feedback_id);
        $feedback->status = FeedbackStatus::REVIEWED;
        $feedback->updated_at = now();
        $feedback->save();

        $response = new EmployeeFeedbackResponse();
        $response->responder_id = $employeeID->employee_id;
        $response->location_id = $feedback->location_id;
        $response->content = $request->input('content');
        $response->feedback_id = $request->input('feedback_id');
        $response->created_at = now();

        $response->save();


        return redirect()->route('employee.feedback.index')->with('success', 'Feedback submitted successfully.');
    }
    public function show($id)
    {
        $feedback = EmployeeFeedback::with(['category', 'response'])->findOrFail($id);
        return response()->json($feedback);
    }

    public function edit(EmployeeFeedback $employeeFeedback)
    {
        return view('admin.admin-feedback.edit', compact('employeeFeedback'));
    }


    public function update(UpdateEmployeeFeedbackRequest $request, EmployeeFeedback $employeeFeedback)
    {
        $employeeFeedback->feedback = $request->input('feedback');
        $employeeFeedback->save();

        return redirect()->route('my.feedback.index')->with('success', 'Feedback updated successfully.');
    }

    public function destroy(EmployeeFeedback $employeeFeedback)
    {
        $employeeFeedback->delete();

        return redirect()->route('my.feedback.index')->with('success', 'Feedback deleted successfully.');
    }
}
