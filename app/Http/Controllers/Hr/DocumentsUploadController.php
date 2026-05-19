<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentsUploadRequest;
use App\Http\Requests\DocumentUploadUpdateRequest;
use App\Models\DocumentCategory;
use App\Models\HrDocument;
use App\Models\DocumentView;
use App\Models\DocumentConsent;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;


class DocumentsUploadController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $documents = HrDocument::all();
        return view('admin.hr.document-uploads.index')->with('documents', $documents);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = DocumentCategory::all();
        return view('admin.hr.document-uploads.create')->with('categories', $categories);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentsUploadRequest $request)
    {
        $input = $request->all();
        $file = $request->file('file');

        // Calculate the hash of the file to detect duplicates
        $fileHash = hash_file('sha256', $file->getRealPath());

        $fileName = time() . '_' . $file->getClientOriginalName();

        // Prevent uploading of file with the same category
        $existingDocument = HrDocument::where('file_hash', $fileHash)
            ->where('category_id', $input['category_id'])
            ->first();

        if ($existingDocument) {
            return redirect()->route('documents-upload.create')->with('error', 'This file has already been uploaded under the same category.');
        }

        // Store file in storage/app/documents directory
        $filePath = $file->storeAs('documents', $fileName, 'local');

        if (!$filePath) {
            return redirect()->route('documents-upload.create')->with('error', 'Failed to upload file. Please try again.');
        }

        $input['file_path'] = $filePath;
        $input['file_hash'] = $fileHash;

        try {
            $input['created_by'] = auth()->user()->id;
            HrDocument::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('documents-upload.index')->with('success', 'Document Successfully uploaded.');
        } else {
            return redirect()->route('documents-upload.create')->with('error', 'Some Error Found !,Please try again');
        }
    }

        public function viewDeletedDocument($id)
        {
        
        $document = HrDocument::withTrashed()->find($id);
         //on viewing of a document record the view as a count in the Document View model and be sure to update specific model's count using updateOrcreate
        return view('admin.hr.document-uploads.show_deleted')->with('document', $document);
         
      }


    public function restoreDocument($id)
    {
            $document = HrDocument::withTrashed()->find($id);
            if (!$document) {
                return redirect()->back()->with('error', 'Document not found.');
            }
            $document->restore();
            return redirect()->back()->with('success', 'Document restored successfully.');
      }

      public function listTrashed()
      {
       
          $documents = HrDocument::onlyTrashed()->get();

          return view('admin.hr.document-uploads.trashed', compact('documents'));
      }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $document = HrDocument::findOrFail($id);
        //on viewing of a document record the view as a count in the Document View model and be sure to update specific model's count using updateOrcreate
        
        DocumentView::updateOrCreate(
            ['document_id' => $id], 
            ['count' => DB::raw('count + 1')] 
        );

        $count = DocumentView::where('document_id', $id)->first()->count;
        return view('admin.hr.document-uploads.show')->with(['document'=> $document,'count'=> $count]);
    }

    public function renderDocument($id)
    {
        $document = HrDocument::findOrFail($id);
        $path = public_path('uploads/documents/' . $document->file);
        return response()->file($path);
    }
    public function review($id)
    {
        $document = HrDocument::findOrFail($id);
        return view('admin.hr.document-uploads.review')->with('document', $document);
    }

    public function updateReview($id)
    {
        $document = HrDocument::findOrFail($id);
        $approvedBy = $document->approved_by ? json_decode($document->approved_by, true) : [];

       if (request()->status == 1) {

        if (!is_array($approvedBy)) {
            $approvedBy = [];
        }
        
        if (!in_array(auth()->user()->id, $approvedBy)) {
            $approvedBy[] = auth()->user()->id;
        }
    
         $document->approved_by = json_encode($approvedBy);
            $document->approval_comment = request()->approval_reason;
        } elseif(request()->status == 2) {

            $rejectedBy = $document->rejected_by ? json_decode($document->rejected_by, true) : [];
            
            // Ensure both are arrays
            if (!is_array($approvedBy)) {
                $approvedBy = [];
            }
            if (!is_array($rejectedBy)) {
                $rejectedBy = [];
            }
            
            // Validate the rejection reason
            if (!request()->rejection_reason || trim(request()->rejection_reason) === "") {
                return response()->json(['success' => false, 'message' => 'Rejection reason is required.']);
            }
            
            // If the current user ID is in the approved_by array, remove it
            if (($key = array_search(auth()->user()->id, $approvedBy)) !== false) {
                unset($approvedBy[$key]);
            }
            
            // If the current user ID is not already in the rejected_by array, add it
            if (!in_array(auth()->user()->id, $rejectedBy)) {
                $rejectedBy[] = auth()->user()->id;
            }
            
            // Save the updated approved_by and rejected_by arrays as JSON
            $document->approved_by = json_encode($approvedBy);
            $document->rejected_by = json_encode($rejectedBy);
            
            // Update the rejection reason
            $document->review_comment = request()->rejection_reason;

        }
       

        $document->save();
        return response()->json(['success' => true]);

    }

    public function approveDocument($id)
    {
        $document = HrDocument::findOrFail($id);
        $document->approved_by = auth()->user()->id;
        $document->save();
        return redirect()->route('documents-upload.index')->with('success', 'Document Successfully Approved.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $document = HrDocument::findOrFail($id);
        $categories = DocumentCategory::all();
        return view('admin.hr.document-uploads.edit')->with('document', $document)->with('categories', $categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentUploadUpdateRequest $request, $id)
    {
        $data = HrDocument::findOrFail($id);
        $input = $request->all();
        $file = $request->file('file');

        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Delete old file if exists
            if ($data->file_path && Storage::disk('local')->exists($data->file_path)) {
                Storage::disk('local')->delete($data->file_path);
            }

            // Store new file in storage/app/documents directory
            $filePath = $file->storeAs('documents', $fileName, 'local');
            $input['file_path'] = $filePath;
        }

        $input['updated_by'] = auth()->user()->id;

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('documents-upload.index')->with('success', 'Document Successfully updated.');
        } else {
            return redirect()->route('documents-upload.index')->with('error', 'Some Error Found !, Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        try {
            $data = HrDocument::findOrFail($id);

            $data->deleted_by = auth()->user()->id;
            $data->save();
            $data->delete();
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

    /**
     * Serve a document file from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function serveDocument($id)
    {
        $document = HrDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('local')->exists($document->file_path)) {
            return abort(404, 'Document file not found.');
        }

        // For approved documents, allow viewing
        if ($document->approved_by) {
            $file = Storage::disk('local')->get($document->file_path);
            $mimeType = Storage::disk('local')->mimeType($document->file_path);
            $filename = basename($document->file_path);

            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
        }

        return abort(403, 'Document not approved for viewing.');
    }

    /**
     * Download a document file from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadDocument($id)
    {
        $document = HrDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('local')->exists($document->file_path)) {
            return abort(404, 'Document file not found.');
        }

        // For approved documents, allow download
        if ($document->approved_by) {
            $path = Storage::disk('local')->path($document->file_path);
            $filename = basename($document->file_path);

            return response()->download($path, $filename);
        }

        return abort(403, 'Document not approved for download.');
    }

    /**
     * Show consents for a specific document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showConsents($id)
    {
        $document = HrDocument::findOrFail($id);
        $consents = DocumentConsent::with(['employee', 'user'])
            ->where('document_id', $id)
            ->orderBy('consented_at', 'desc')
            ->get();

        // Get consent statistics
        $stats = DocumentConsent::getDocumentConsentStats($id);

        return view('admin.hr.document-uploads.consents', compact('document', 'consents', 'stats'));
    }

    /**
     * Download consent report for a specific document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadConsentReport($id)
    {
        $document = HrDocument::findOrFail($id);
        $consents = DocumentConsent::with(['employee', 'user'])
            ->where('document_id', $id)
            ->orderBy('consented_at', 'desc')
            ->get();

        // Generate CSV
        $filename = 'document_consents_' . $document->id . '_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($consents, $document) {
            $file = fopen('php://output', 'w');

            // Document info header
            fputcsv($file, ['Document Consent Report']);
            fputcsv($file, ['Document Name:', $document->name]);
            fputcsv($file, ['Category:', $document->category->name ?? 'N/A']);
            fputcsv($file, ['Report Generated:', date('d-m-Y H:i:s')]);
            fputcsv($file, []);

            // Column headers
            fputcsv($file, [
                'S.No',
                'Employee Name',
                'Payroll Number',
                'Email',
                'Consented At',
                'IP Address',
                'Acknowledgment Text'
            ]);

            $serial = 1;
            foreach ($consents as $consent) {
                $employee = $consent->employee;
                fputcsv($file, [
                    $serial++,
                    $employee ? trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name}") : 'N/A',
                    $employee ? $employee->payroll_number : 'N/A',
                    $consent->user->email ?? 'N/A',
                    $consent->consented_at->format('d-m-Y H:i:s'),
                    $consent->ip_address ?? 'N/A',
                    $consent->acknowledgment_text
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show overall consent summary for all documents.
     *
     * @return \Illuminate\Http\Response
     */
    public function consentSummary()
    {
        $documents = HrDocument::with(['category', 'consents'])->get();
        $employees = Employee::where('status', 1)->get();

        $summary = [];
        foreach ($documents as $document) {
            $stats = DocumentConsent::getDocumentConsentStats($document->id);
            $summary[$document->id] = [
                'document' => $document,
                'stats' => $stats,
            ];
        }

        return view('admin.hr.document-uploads.consent-summary', compact('documents', 'employees', 'summary'));
    }
}
