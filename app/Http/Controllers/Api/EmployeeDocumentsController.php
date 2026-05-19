<?php

namespace App\Http\Controllers\Api;

use App\Models\EmployeeDocuments;
use App\Http\Requests\StoreEmployeeDocumentsRequest;
use App\Http\Requests\UpdateEmployeeDocumentsRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class EmployeeDocumentsController extends Controller
{
    /**
     * Display a listing of the logged-in user's documents.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt to employee documents index');
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated access'
            ], 401);
        }

        try {
            $userId = Auth::id();
            
            Log::info('Fetching documents for user ID: ' . $userId);

            $query = EmployeeDocuments::with(['employee', 'uploadedBy'])
                ->where('status', 1)
                ->where('employee_id', $userId);

            // Apply filters if provided
            if ($request->filled('document_type')) {
                $query->where('document_type', $request->document_type);
            }

            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }

            $documents = $query->orderBy('created_at', 'desc')->get();

            Log::info('Successfully retrieved documents for user ID: ' . $userId);

            return response()->json([
                'status' => 'success',
                'data' => $documents
            ], 200);

        } catch (\Exception $e) {
            Log::error('Document index error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new document for the logged-in user.
     *
     * @param StoreEmployeeDocumentsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEmployeeDocumentsRequest $request)
    {
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt to store employee document');
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated access'
            ], 401);
        }

        try {
            $userId = Auth::id();
            Log::info('Attempting to store document for user ID: ' . $userId);

            $document = new EmployeeDocuments();
            $document->uuid = Str::uuid();
            $document->document_name = $request->document_name;
            $document->employee_id = $userId;
            $document->national_id = $request->national_id;
            $document->date_uploaded = Carbon::now()->toDateString();
            $document->document_type = $request->document_type;
            $document->location_id = $request->location_id;
            $document->uploaded_by = $userId;
            $document->created_by = $userId;
            $document->status = 1;

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                
                // Validate file type and size
                if (!in_array($file->getClientMimeType(), ['application/pdf', 'image/jpeg', 'image/png'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid file type. Only PDF, JPEG, and PNG files are allowed.'
                    ], 422);
                }

                if ($file->getSize() > 5242880) { // 5MB in bytes
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File size exceeds 5MB limit.'
                    ], 422);
                }

                $fileName = time() . '_' . Str::slug($file->getClientOriginalName());
                $path = $file->storeAs('employee_documents', $fileName, 'public');
                $document->document_link = $path;
            }

            $document->save();

            Log::info('Document stored successfully for user ID: ' . $userId);

            return response()->json([
                'status' => 'success',
                'message' => 'Document uploaded successfully',
                'data' => $document
            ], 201);

        } catch (\Exception $e) {
            Log::error('Document store error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Error uploading document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single document belonging to the logged-in user.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($uuid)
    {
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt to view document');
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated access'
            ], 401);
        }

        try {
            $userId = Auth::id();
            Log::info('Attempting to fetch document: ' . $uuid . ' for user ID: ' . $userId);

            $document = EmployeeDocuments::with(['employee', 'uploadedBy'])
                ->where('uuid', $uuid)
                ->where('employee_id', $userId)
                ->where('status', 1)
                ->firstOrFail();

            Log::info('Document retrieved successfully');

            return response()->json([
                'status' => 'success',
                'data' => $document
            ], 200);

        } catch (\Exception $e) {
            Log::error('Document show error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Document not found or unauthorized'
            ], 404);
        }
    }

    /**
     * Update a document owned by the logged-in user.
     *
     * @param UpdateEmployeeDocumentsRequest $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEmployeeDocumentsRequest $request, $uuid)
    {
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt to update document');
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated access'
            ], 401);
        }

        try {
            $userId = Auth::id();
            Log::info('Attempting to update document: ' . $uuid . ' for user ID: ' . $userId);

            $document = EmployeeDocuments::where('uuid', $uuid)
                ->where('employee_id', $userId)
                ->firstOrFail();

            $document->document_name = $request->filled('document_name') ? $request->document_name : $document->document_name;
            $document->document_type = $request->filled('document_type') ? $request->document_type : $document->document_type;
            $document->national_id = $request->filled('national_id') ? $request->national_id : $document->national_id;
            $document->location_id = $request->filled('location_id') ? $request->location_id : $document->location_id;
            $document->updated_by = $userId;

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                
                // Validate file type and size
                if (!in_array($file->getClientMimeType(), ['application/pdf', 'image/jpeg', 'image/png'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid file type. Only PDF, JPEG, and PNG files are allowed.'
                    ], 422);
                }

                if ($file->getSize() > 5242880) { // 5MB in bytes
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File size exceeds 5MB limit.'
                    ], 422);
                }

                // Delete old file if it exists
                if ($document->document_link) {
                    Storage::disk('public')->delete($document->document_link);
                }

                $fileName = time() . '_' . Str::slug($file->getClientOriginalName());
                $path = $file->storeAs('employee_documents', $fileName, 'public');
                $document->document_link = $path;
            }

            $document->save();

            Log::info('Document updated successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Document updated successfully',
                'data' => $document
            ], 200);

        } catch (\Exception $e) {
            Log::error('Document update error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Error updating document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete a document owned by the logged-in user.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($uuid)
    {
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt to delete document');
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated access'
            ], 401);
        }

        try {
            $userId = Auth::id();
            Log::info('Attempting to delete document: ' . $uuid . ' for user ID: ' . $userId);

            $document = EmployeeDocuments::where('uuid', $uuid)
                ->where('employee_id', $userId)
                ->firstOrFail();

            $document->status = 0;
            $document->deleted_at = Carbon::now();
            $document->deleted_by = $userId;
            $document->save();

            Log::info('Document deleted successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Document deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Document delete error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting document: ' . $e->getMessage()
            ], 500);
        }
    }
}