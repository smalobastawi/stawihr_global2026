@extends('admin.master')
@section('content')
@section('title')
Performance Appraisal Details
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
            <div class="white-box">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="box-title">Appraisal: {{ $appraisal->review_period }}</h3>
                        <p><strong>Employee:</strong> {{ $appraisal->employee ? $appraisal->employee->full_name : '' }}</p>
                        <p><strong>Supervisor:</strong> {{ $appraisal->supervisor ? $appraisal->supervisor->full_name : 'N/A' }}</p>
                        <p><strong>Status:</strong>
                            @if($appraisal->status == 'draft')
                                <span class="label label-default">Draft</span>
                            @elseif($appraisal->status == 'self_review')
                                <span class="label label-info">Self Review</span>
                            @elseif($appraisal->status == 'supervisor_review')
                                <span class="label label-warning">Supervisor Review</span>
                            @elseif($appraisal->status == 'hod_review')
                                <span class="label label-primary">HOD Review</span>
                            @elseif($appraisal->status == 'finalized')
                                <span class="label label-success">Finalized</span>
                            @else
                                <span class="label label-primary">Closed</span>
                            @endif
                        </p>
                        @if($appraisal->review_start_date && $appraisal->review_end_date)
                            <p style="margin-top: 10px;">
                                <span class="label label-info" style="font-size: 12px;">
                                    <i class="fa fa-calendar"></i> 
                                    Period: {{ $appraisal->review_start_date->format('M d, Y') }} - {{ $appraisal->review_end_date->format('M d, Y') }}
                                </span>
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6 text-right">
                        @if($appraisal->total_review_weighting > 0)
                            <h1 class="text-success">{{ $appraisal->total_review_weighting }}</h1>
                            <p>Total Review Score</p>
                        @endif
                        @if($appraisal->total_self_weighting > 0)
                            <p><strong>Self Score:</strong> {{ $appraisal->total_self_weighting }}</p>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="alert {{ $appraisal->employee_signed ? 'alert-success' : 'alert-warning' }}">
                            <strong>Employee Signed:</strong> {{ $appraisal->employee_signed ? 'Yes (' . $appraisal->employee_sign_date . ')' : 'No' }}
                            @if(!$appraisal->employee_signed && in_array($appraisal->status, ['finalized', 'closed']))
                                <form action="{{ route('performance.appraisal.employeeSign', $appraisal->appraisal_id) }}" method="POST" style="display:inline;margin-left:10px;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success">Sign</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert {{ $appraisal->supervisor_signed ? 'alert-success' : 'alert-warning' }}">
                            <strong>Supervisor Signed:</strong> {{ $appraisal->supervisor_signed ? 'Yes (' . $appraisal->supervisor_sign_date . ')' : 'No' }}
                            @if(!$appraisal->supervisor_signed && in_array($appraisal->status, ['finalized', 'closed']))
                                <form action="{{ route('performance.appraisal.supervisorSign', $appraisal->appraisal_id) }}" method="POST" style="display:inline;margin-left:10px;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success">Sign</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert {{ $appraisal->hod_signed ? 'alert-success' : 'alert-warning' }}">
                            <strong>HOD Signed:</strong> {{ $appraisal->hod_signed ? 'Yes (' . $appraisal->hod_sign_date . ')' : 'No' }}
                            @if(!$appraisal->hod_signed && in_array($appraisal->status, ['finalized', 'closed']))
                                <form action="{{ route('performance.appraisal.hodSign', $appraisal->appraisal_id) }}" method="POST" style="display:inline;margin-left:10px;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success">Sign</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section A: Performance Measure -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> Section A: Performance Measure</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @foreach($focusAreaScores as $faScore)
                            @php $focusArea = $faScore['focusArea']; @endphp
                            <h4>{{ $focusArea ? $focusArea->focus_area_name : 'Unknown' }} (Weight: {{ $focusArea ? $focusArea->weight : 0 }}%)</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Strategic Objective</th>
                                            <th>Performance Metric</th>
                                            <th>Performance Target</th>
                                            <th>Itemized Weight</th>
                                            <th>Self Weight</th>
                                            <th>Review Weight</th>
                                            <th>Self Comments</th>
                                            <th>Review Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($faScore['scores'] as $score)
                                            <tr>
                                                <td>{{ $score->goal ? $score->goal->strategic_objective : '' }}</td>
                                                <td>{{ $score->goal ? $score->goal->performance_metric : '' }}</td>
                                                <td>{{ $score->goal ? Str::limit($score->goal->performance_target, 50) : '' }}</td>
                                                <td>{{ $score->itemized_weighting }}</td>
                                                <td>{{ $score->self_weighting }}</td>
                                                <td><strong>{{ $score->review_weighting }}</strong></td>
                                                <td>{{ $score->self_comments }}</td>
                                                <td>{{ $score->review_comments }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="info">
                                            <td colspan="4"><strong>Focus Area Total</strong></td>
                                            <td><strong>{{ $faScore['self_total'] }}</strong></td>
                                            <td><strong>{{ $faScore['review_total'] }}</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                        @endforeach

                        <h4>Behavioral Expectations</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Item</th>
                                        <th>Weight</th>
                                        <th>Self Weight</th>
                                        <th>Review Weight</th>
                                        <th>Self Comments</th>
                                        <th>Review Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appraisal->behavioralScores as $bScore)
                                        <tr>
                                            <td>{{ $bScore->behavioralItem ? $bScore->behavioralItem->item_name : '' }}</td>
                                            <td>{{ $bScore->itemized_weighting }}</td>
                                            <td>{{ $bScore->self_weighting }}</td>
                                            <td><strong>{{ $bScore->review_weighting }}</strong></td>
                                            <td>{{ $bScore->self_comments }}</td>
                                            <td>{{ $bScore->review_comments }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($appraisal->employee_comments || $appraisal->supervisor_comments)
                            <div class="row">
                                @if($appraisal->employee_comments)
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <strong>Employee Comments:</strong> {{ $appraisal->employee_comments }}
                                        </div>
                                    </div>
                                @endif
                                @if($appraisal->supervisor_comments)
                                    <div class="col-md-6">
                                        <div class="alert alert-warning">
                                            <strong>Supervisor Comments:</strong> {{ $appraisal->supervisor_comments }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section B: Development Plan -->
    @if($appraisal->developmentPlans->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-warning">
                <div class="panel-heading"><i class="mdi mdi-trending-up fa-fw"></i> Section B: Development Plan</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
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
                                            <td>{{ $plan->competency_name }}</td>
                                            <td>{{ $plan->expected_proficiency }}</td>
                                            <td>{{ $plan->smart_objective }}</td>
                                            <td>{{ $plan->self_rating }}</td>
                                            <td>{{ $plan->reviewer_rating }}</td>
                                            <td><strong>{{ $plan->agreed_rating }}</strong></td>
                                            <td>{{ $plan->competencies_of_focus }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Section C: Learning Plan -->
    @if($appraisal->learningPlans->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading"><i class="mdi mdi-school fa-fw"></i> Section C: Learning Plan</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
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
                                            <td>{{ $plan->course_title }}</td>
                                            <td>{{ $plan->due_date ? $plan->due_date->format('Y-m-d') : '' }}</td>
                                            <td>{{ $plan->learning_hours }}</td>
                                            <td><span class="label label-{{ $plan->mid_year_status == 'completed' ? 'success' : ($plan->mid_year_status == 'in_progress' ? 'warning' : 'default') }}">{{ ucfirst(str_replace('_', ' ', $plan->mid_year_status)) }}</span></td>
                                            <td><span class="label label-{{ $plan->end_year_status == 'completed' ? 'success' : ($plan->end_year_status == 'in_progress' ? 'warning' : 'default') }}">{{ ucfirst(str_replace('_', ' ', $plan->end_year_status)) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($appraisal->pipPlans->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-danger">
                <div class="panel-heading"><i class="mdi mdi-alert fa-fw"></i> Linked PIP Plans</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Outcome</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appraisal->pipPlans as $pip)
                                        <tr>
                                            <td>{{ $pip->plan_period_start ? $pip->plan_period_start->format('Y-m-d') : '' }} - {{ $pip->plan_period_end ? $pip->plan_period_end->format('Y-m-d') : '' }}</td>
                                            <td><span class="label label-{{ $pip->status == 'completed' ? 'success' : ($pip->status == 'active' ? 'info' : 'default') }}">{{ ucfirst($pip->status) }}</span></td>
                                            <td><span class="label label-{{ $pip->outcome == 'successful_completion' ? 'success' : ($pip->outcome == 'failure' ? 'danger' : 'warning') }}">{{ ucfirst(str_replace('_', ' ', $pip->outcome)) }}</span></td>
                                            <td>
                                                <a href="{{ route('pip.plan.show', $pip->pip_id) }}" class="btn btn-primary btn-xs btnColor">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
