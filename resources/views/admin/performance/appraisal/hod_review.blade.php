@extends('admin.master')
@section('content')
@section('title')
HOD Review - {{ $appraisal->employee ? $appraisal->employee->full_name : '' }} ({{ $appraisal->review_period }})
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @foreach (urlTree() as $item)
                    <li class="breadcrumb-item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading"><i class="mdi mdi-account-star fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        <form action="{{ route('performance.appraisal.saveHodReview', $appraisal->appraisal_id) }}" method="POST" id="hodReviewForm">
                            @csrf

                        <div class="form-body">
                            <h4>Section A: Performance Measure Summary</h4>
                            @foreach($focusAreaScores as $faScore)
                                @php $focusArea = $faScore['focusArea']; @endphp
                                <h5>{{ $focusArea ? $focusArea->focus_area_name : 'Unknown' }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="tr_header">
                                                <th>Performance Metric</th>
                                                <th>Self Weight</th>
                                                <th>Review Weight</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($faScore['scores'] as $score)
                                                <tr>
                                                    <td>{{ $score->goal ? $score->goal->performance_metric : '' }}</td>
                                                    <td>{{ $score->self_weighting }}</td>
                                                    <td>{{ $score->review_weighting }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach

                            <hr>
                            <h4>Section B: Development Plan</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Competency</th>
                                            <th>Expected Proficiency</th>
                                            <th>SMART Objective</th>
                                            <th>Self Rating</th>
                                            <th>Reviewer Rating</th>
                                            <th>Agreed Rating</th>
                                            <th>Focus for Next Period</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appraisal->developmentPlans as $plan)
                                            <tr>
                                                <td>
                                                    <input type="text" name="development_plans[{{ $plan->development_plan_id }}][competency_name]" class="form-control" value="{{ $plan->competency_name }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="development_plans[{{ $plan->development_plan_id }}][expected_proficiency]" class="form-control" value="{{ $plan->expected_proficiency }}">
                                                </td>
                                                <td>
                                                    <textarea name="development_plans[{{ $plan->development_plan_id }}][smart_objective]" class="form-control" rows="2">{{ $plan->smart_objective }}</textarea>
                                                </td>
                                                <td>
                                                    <input type="number" name="development_plans[{{ $plan->development_plan_id }}][self_rating]" class="form-control" step="0.1" value="{{ $plan->self_rating }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="development_plans[{{ $plan->development_plan_id }}][reviewer_rating]" class="form-control" step="0.1" value="{{ $plan->reviewer_rating }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="development_plans[{{ $plan->development_plan_id }}][agreed_rating]" class="form-control" step="0.1" value="{{ $plan->agreed_rating }}">
                                                </td>
                                                <td>
                                                    <textarea name="development_plans[{{ $plan->development_plan_id }}][competencies_of_focus]" class="form-control" rows="2">{{ $plan->competencies_of_focus }}</textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @for($i = 0; $i < 3; $i++)
                                            <tr>
                                                <td><input type="text" name="new_development_plans[{{ $i }}][competency_name]" class="form-control" placeholder="Competency"></td>
                                                <td><input type="text" name="new_development_plans[{{ $i }}][expected_proficiency]" class="form-control" placeholder="Proficiency"></td>
                                                <td><textarea name="new_development_plans[{{ $i }}][smart_objective]" class="form-control" rows="2" placeholder="SMART Objective"></textarea></td>
                                                <td><input type="number" name="new_development_plans[{{ $i }}][self_rating]" class="form-control" step="0.1"></td>
                                                <td><input type="number" name="new_development_plans[{{ $i }}][reviewer_rating]" class="form-control" step="0.1"></td>
                                                <td><input type="number" name="new_development_plans[{{ $i }}][agreed_rating]" class="form-control" step="0.1"></td>
                                                <td><textarea name="new_development_plans[{{ $i }}][competencies_of_focus]" class="form-control" rows="2" placeholder="Focus for next period"></textarea></td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>

                            <hr>
                            <h4>Section C: Learning Plan</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Course Title</th>
                                            <th>Due Date</th>
                                            <th>Learning Hours</th>
                                            <th>Mid-Year Status</th>
                                            <th>End-Year Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appraisal->learningPlans as $plan)
                                            <tr>
                                                <td><input type="text" name="learning_plans[{{ $plan->learning_plan_id }}][course_title]" class="form-control" value="{{ $plan->course_title }}"></td>
                                                <td><input type="date" name="learning_plans[{{ $plan->learning_plan_id }}][due_date]" class="form-control" value="{{ $plan->due_date ? $plan->due_date->format('Y-m-d') : '' }}"></td>
                                                <td><input type="text" name="learning_plans[{{ $plan->learning_plan_id }}][learning_hours]" class="form-control" value="{{ $plan->learning_hours }}"></td>
                                                <td>
                                                    <select name="learning_plans[{{ $plan->learning_plan_id }}][mid_year_status]" class="form-control">
                                                        <option value="not_started" {{ $plan->mid_year_status == 'not_started' ? 'selected' : '' }}>Not Started</option>
                                                        <option value="in_progress" {{ $plan->mid_year_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="completed" {{ $plan->mid_year_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="learning_plans[{{ $plan->learning_plan_id }}][end_year_status]" class="form-control">
                                                        <option value="not_started" {{ $plan->end_year_status == 'not_started' ? 'selected' : '' }}>Not Started</option>
                                                        <option value="in_progress" {{ $plan->end_year_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="completed" {{ $plan->end_year_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @for($i = 0; $i < 3; $i++)
                                            <tr>
                                                <td><input type="text" name="new_learning_plans[{{ $i }}][course_title]" class="form-control" placeholder="Course Title"></td>
                                                <td><input type="date" name="new_learning_plans[{{ $i }}][due_date]" class="form-control"></td>
                                                <td><input type="text" name="new_learning_plans[{{ $i }}][learning_hours]" class="form-control" placeholder="e.g. 12hrs"></td>
                                                <td>
                                                    <select name="new_learning_plans[{{ $i }}][mid_year_status]" class="form-control">
                                                        <option value="not_started">Not Started</option>
                                                        <option value="in_progress">In Progress</option>
                                                        <option value="completed">Completed</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="new_learning_plans[{{ $i }}][end_year_status]" class="form-control">
                                                        <option value="not_started">Not Started</option>
                                                        <option value="in_progress">In Progress</option>
                                                        <option value="completed">Completed</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="hod_comments">HOD Overall Comments</label>
                                        <textarea name="hod_comments" id="hod_comments" class="form-control" rows="3" placeholder="Enter overall comments">{{ $appraisal->hod_comments }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn_style"><i class="fa fa-check"></i> Save HOD Review</button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('performance.appraisal.index') }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
                                </div>
                            </div>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
