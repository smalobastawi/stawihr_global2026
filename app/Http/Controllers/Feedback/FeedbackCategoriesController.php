<?php

namespace App\Http\Controllers\Feedback;
use App\Http\Controllers\Controller;

use App\Models\FeedbackCategories;
use App\Http\Requests\StoreFeedbackCategoriesRequest;
use App\Http\Requests\UpdateFeedbackCategoriesRequest;
use Illuminate\Support\Facades\Auth;

class FeedbackCategoriesController extends Controller
{
  
    public function index()
    {
        $data = FeedbackCategories::get();
        return view('admin.employee-feedback.feedback-category.index', compact('data'));
    }
    public function trash()
    {
        $data  = FeedbackCategories::onlyTrashed()->get();
        return view('admin.employee-feedback.feedback-category.trash', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.employee-feedback.feedback-category.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFeedbackCategoriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFeedbackCategoriesRequest $request)
    {
        $data = $request->except('_token');
        $data ['created_by'] = Auth::id();
        $data ['created_at'] =  now();
       
        FeedbackCategories::create($data);
        return redirect()->route("feedback.category.index")->with(['success'=>'Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeedbackCategories  $feedbackCategories
     * @return \Illuminate\Http\Response
     */
    public function show(FeedbackCategories $feedbackCategories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeedbackCategories  $feedbackCategories
     * @return \Illuminate\Http\Response
     */
    public function edit( $id)
    {

        $editModeData =  FeedbackCategories::findOrFail($id);
        return view('admin.employee-feedback.feedback-category.edit', compact('editModeData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFeedbackCategoriesRequest  $request
     * @param  \App\Models\FeedbackCategories  $feedbackCategories
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFeedbackCategoriesRequest $request,  $id)
    {
        
        
        $data = $request->validated();
        $feedbackCategories = FeedbackCategories::findOrFail($id);
        $data ['updated_by'] = Auth::id();
        $feedbackCategories->update($data);
    
        return redirect()
            ->route('feedback.category.index')
            ->with('success', 'Feedback category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeedbackCategories  $feedbackCategories
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $data = FeedbackCategories::findOrFail($id);
        try{
           
            $data ['deleted_by'] = Auth::id();
            $data ->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
       
    }
    public function destroy( $id)
    {
        $feedbackCategories = FeedbackCategories::withTrashed()->findOrFail($id);
        try{
           
            $feedbackCategories ->destroy($id);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
   
    }

    public function restore($id)
    {
        $data = FeedbackCategories::withTrashed()->findOrFail($id);
        try{
           
            $data->restore();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
        
    }

}
