<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TerminationChecklist;
use App\Models\TerminationChecklistAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TerminationChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $termination_checklist_items = TerminationChecklist::all();
        return view('admin.employee.terminationChecklist.index')->with('termination_checklist_items', $termination_checklist_items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.employee.terminationChecklist.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        TerminationChecklist::create([
            'checklist_name' => $request->checklist_name,
            'description' => $request->description,
            'created_by' => Auth::user()->id,
        ]);

        return redirect()->route('termination-checklist.index')->with('success', 'Termination Checklist item created successfully');
    }


    public function updateChecklist(Request $request)
{
    $request->validate([
        'checklist_id' => 'required|integer|exists:checklists,id',
        'comment' => 'nullable|string',
        'checked' => 'boolean'
    ]);

    $checklist = TerminationChecklist::findOrFail($request->checklist_id);
    $checklist->comment = $request->comment;
    $checklist->checked = $request->checked;
    $checklist->save();

    return response()->json(['message' => 'Checklist updated successfully']);
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $termination_checklist_item = TerminationChecklist::find($id);
        return view('admin.employee.terminationChecklist.edit')->with('termination_checklist_item', $termination_checklist_item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateTerminationChecklistAction(Request $request, $id)
    {


        TerminationChecklistAction::create([
            'termination_checklist_id' => $request->termination_checklist_id,
            'termination_id' => $request->termination_id,
            'comment' => $request->comment,
            'status' => true,
            'actioned_by' => auth()->user()->id,
        ]);
       

        return redirect()->route('termination-checklist.index')->with('success', 'Termination Checklist Action item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try{

            TerminationChecklist::find($id)->delete();       
            echo "success";
            
        } catch (\Exception $e) {
            echo 'error';

        }
    }
}
