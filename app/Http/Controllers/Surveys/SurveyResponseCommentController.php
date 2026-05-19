<?php

namespace App\Http\Controllers\Surveys;

use App\Http\Controllers\Controller;
use App\Models\SurveyResponseComment;
use App\Http\Requests\StoreSurveyResponseCommentRequest;
use App\Http\Requests\UpdateSurveyResponseCommentRequest;

class SurveyResponseCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyResponseCommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyResponseCommentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SurveyResponseComment  $surveyResponseComment
     * @return \Illuminate\Http\Response
     */
    public function show(SurveyResponseComment $surveyResponseComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SurveyResponseComment  $surveyResponseComment
     * @return \Illuminate\Http\Response
     */
    public function edit(SurveyResponseComment $surveyResponseComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyResponseCommentRequest  $request
     * @param  \App\Models\SurveyResponseComment  $surveyResponseComment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyResponseCommentRequest $request, SurveyResponseComment $surveyResponseComment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SurveyResponseComment  $surveyResponseComment
     * @return \Illuminate\Http\Response
     */
    public function destroy(SurveyResponseComment $surveyResponseComment)
    {
        //
    }
}
