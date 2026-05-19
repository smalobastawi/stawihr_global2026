@extends('admin.master')
@section('content')
@section('title')
Supervisor Review - {{ $appraisal->employee ? $appraisal->employee->full_name : '' }} ({{ $appraisal->review_period }})
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
            <div class="panel panel-warning">
                <div class="panel-heading"><i class="mdi mdi-account-secret fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        <form action="{{ route('performance.supervisor.saveReview', $appraisal->appraisal_id) }}" method="POST" id="supervisorReviewForm">
                            @csrf

                        <div class="form-body">
                            <h4>Section A: Performance Measure</h4>
                            @foreach($focusAreaScores as $faScore)
                                @php $focusArea = $faScore['focusArea']; @endphp
                                <h5>{{ $focusArea ? $focusArea->focus_area_name : 'Unknown' }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="tr_header">
                                                <th>Performance Metric</th>
                                                <th>Self Weight</th>
                                                <th>Review Weighting<span class="validateRq">*</span></th>
                                                <th>Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($faScore['scores'] as $score)
                                                <tr>
                                                    <td>{{ $score->goal ? $score->goal->performance_metric : '' }}</td>
                                                    <td>{{ $score->self_weighting }}</td>
                                                    <td>
                                                        <input type="number" name="scores[{{ $score->score_id }}]" class="form-control" step="0.01" min="0" max="{{ $score->itemized_weighting }}" value="{{ $score->review_weighting }}" required>
                                                    </td>
                                                    <td>
                                                        <textarea name="comments[{{ $score->score_id }}]" class="form-control" rows="2" placeholder="Supervisor comments">{{ $score->review_comments }}</textarea>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                            @endforeach

                            <h5>Behavioral Expectations</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Item</th>
                                            <th>Self Weight</th>
                                            <th>Review Weighting<span class="validateRq">*</span></th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appraisal->behavioralScores as $bScore)
                                            <tr>
                                                <td>{{ $bScore->behavioralItem ? $bScore->behavioralItem->item_name : '' }}</td>
                                                <td>{{ $bScore->self_weighting }}</td>
                                                <td>
                                                    <input type="number" name="behavioral_scores[{{ $bScore->behavioral_score_id }}]" class="form-control" step="0.01" min="0" max="{{ $bScore->itemized_weighting }}" value="{{ $bScore->review_weighting }}" required>
                                                </td>
                                                <td>
                                                    <textarea name="behavioral_comments[{{ $bScore->behavioral_score_id }}]" class="form-control" rows="2" placeholder="Supervisor comments">{{ $bScore->review_comments }}</textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="supervisor_comments">Supervisor Overall Comments</label>
                                        <textarea name="supervisor_comments" id="supervisor_comments" class="form-control" rows="3" placeholder="Enter overall comments">{{ $appraisal->supervisor_comments }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-warning btn_style"><i class="fa fa-check"></i> Save Supervisor Review</button>
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
