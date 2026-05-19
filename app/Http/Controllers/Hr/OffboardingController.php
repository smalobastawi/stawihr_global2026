<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentsUploadRequest;
use App\Http\Requests\DocumentUploadUpdateRequest;
use App\Models\DocumentCategory;
use App\Models\HrDocument;
use App\Models\DocumentView;
use App\Models\Offboarding;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;


class OffboardingController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
    $offboarding_items = Offboarding::all();
    return view('admin.hr.offboarding-process.index')->with('offboarding_items', $offboarding_items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createProcess()
    {

        return view('admin.hr.offboarding-process.create');
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProcess()
    {
        
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
            $fileName = time() . $file->getClientOriginalName();
            $file->move('uploads/documents', $fileName);
            $input['file_path'] = $fileName;
        } else {
            $input['file_path'] = $data->file;
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
}
