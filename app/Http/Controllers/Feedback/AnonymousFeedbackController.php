<?php
namespace App\Http\Controllers\Feedback;
use App\Http\Controllers\Controller;

use App\Models\AnonymousFeedback;
use App\Http\Requests\StoreAnonymousFeedbackRequest;
use App\Http\Requests\UpdateAnonymousFeedbackRequest;
use App\Lib\Enumerations\FeedbackStatus;
use App\Models\Employee;
use App\Models\EmployeeFeedback;
use App\Models\FeedbackCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AnonymousFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AnonymousFeedback::get();
        return view('admin.employee-feedback.anonymous-feedback.index', compact('data'));
    }

    public function createAnonymous()
    {
        $categories = FeedbackCategories::get();
        return view('admin.employee-feedback.anonymous-feedback.edit', compact('categories'));
    }


    public function storeAnonymous(StoreAnonymousFeedbackRequest $request)
    {
        $response = new AnonymousFeedback();
        $response->category_id = $request->input('category_id');
        $response->content = $request->input('content');
        $response->title = $request->input('title');
        $response->created_at = now();
        $response->save();
        return redirect()->route('ess.feedback.index')->with([ 'success'=>'Anonymous feedback submitted successfully!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AnonymousFeedback  $anonymousFeedback
     * @return \Illuminate\Http\Response
     */
    public function show(AnonymousFeedback $anonymousFeedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AnonymousFeedback  $anonymousFeedback
     * @return \Illuminate\Http\Response
     */
    public function edit(AnonymousFeedback $anonymousFeedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAnonymousFeedbackRequest  $request
     * @param  \App\Models\AnonymousFeedback  $anonymousFeedback
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnonymousFeedbackRequest $request, AnonymousFeedback $anonymousFeedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AnonymousFeedback  $anonymousFeedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(AnonymousFeedback $anonymousFeedback)
    {
        //
    }
    public function review($id)
    {
        $data = AnonymousFeedback::with(['category'])->findOrFail($id);
        return view('admin.employee-feedback.anonymous-feedback.respond', compact('data'));
    }

    public function storeReview(Request $request)
    {
       
        $employeeID = Employee::where('user_id', Auth::id())->first();
        $feedback = AnonymousFeedback::findOrFail($request->feedback_id);
        $feedback->status = FeedbackStatus::REVIEWED;
        $feedback->action_description = $request->action_description;
        $feedback->updated_at = now();
        $feedback->save();


        return redirect()->route('anonymous.feedback.index')->with('success', 'Action successful.');
    }
}
