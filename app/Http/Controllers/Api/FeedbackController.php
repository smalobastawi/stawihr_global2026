<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeFeedback;
use App\Models\EmployeeFeedbackResponse;
use App\Models\AnonymousFeedback;
use App\Models\FeedbackCategories;
use App\Models\Employee;
use App\Models\Location;
use App\Lib\Enumerations\GeneralStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Get feedback categories
     */
    public function getCategories()
    {
        try {
            $categories = FeedbackCategories::where('status', GeneralStatus::ACTIVE)
                ->orderBy('name')
                ->get(['id', 'name', 'description', 'status']);
            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all feedback submitted by the authenticated employee (general feedback)
     */
    public function getEmployeeFeedback()
    {
        try {
            $employeeID = Employee::where('user_id', Auth::id())->first();

            if (!$employeeID) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            $feedback = EmployeeFeedback::where('employee_id', $employeeID->employee_id)
                ->with(['category', 'response'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $feedback
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific feedback details
     */
    public function getEmployeeFeedbackDetails($id)
    {
        try {
            $employee = Employee::where('user_id', Auth::id())->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            $feedback = EmployeeFeedback::with(['category', 'response', 'employee'])
                ->where('employee_id', $employee->employee_id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $feedback
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit a new general feedback
     */
    public function submitEmployeeFeedback(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category_id' => 'required|integer|exists:feedback_categories,id'
            ]);

            $employeeID = Employee::where('user_id', Auth::id())->first();

            if (!$employeeID) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            $fallBackBranchID = Location::first()->location_id ?? null;

            $feedback = new EmployeeFeedback();
            $feedback->employee_id = $employeeID->employee_id;
            $feedback->location_id = $employeeID->location_id ?? $fallBackBranchID;
            $feedback->title = $request->input('title');
            $feedback->content = $request->input('content');
            $feedback->category_id = $request->input('category_id');
            $feedback->status = 'pending';
            $feedback->created_by = Auth::id();
            $feedback->save();

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully',
                'data' => $feedback
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a general feedback
     */
    public function deleteEmployeeFeedback($id)
    {
        try {
            $employee = Employee::where('user_id', Auth::id())->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            $feedback = EmployeeFeedback::where('employee_id', $employee->employee_id)
                ->findOrFail($id);

            // Check if feedback has a response
            $hasResponse = EmployeeFeedbackResponse::where('feedback_id', $id)->count();

            if ($hasResponse > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete feedback that has a response'
                ], 400);
            }

            $feedback->delete();

            return response()->json([
                'success' => true,
                'message' => 'Feedback deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit anonymous feedback (no authentication required)
     */
    public function submitAnonymousFeedback(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category_id' => 'required|integer|exists:feedback_categories,id'
            ]);

            $feedback = new AnonymousFeedback();
            $feedback->title = $request->input('title');
            $feedback->content = $request->input('content');
            $feedback->category_id = $request->input('category_id');
            $feedback->status = 'pending';
            $feedback->save();

            return response()->json([
                'success' => true,
                'message' => 'Anonymous feedback submitted successfully',
                'data' => $feedback
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get anonymous feedback categories for public access
     */
    public function getAnonymousCategories()
    {
        try {
            $categories = FeedbackCategories::where('status', GeneralStatus::ACTIVE)
                ->orderBy('name')
                ->get(['id', 'name', 'description', 'status']);
            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
