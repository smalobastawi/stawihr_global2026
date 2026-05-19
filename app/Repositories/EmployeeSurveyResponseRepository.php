<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

 namespace App\Repositories;

use App\Models\EmployeeSurveyResponse;

 class EmployeeSurveyResponseRepository
 {
   public function getAllResponses()  
   {
      return EmployeeSurveyResponse::with(['survey', 'surveyQuestion', 'employee'])->latest()->et();
   }

   public function show($id)  
   {
      return EmployeeSurveyResponse::with(['survey', 'surveyQuestion', 'employee'])
         ->findOrFail($id);
   }
 }