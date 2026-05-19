@extends('admin.master')
@section('content')
@section('title')
    My Self Review - {{ $appraisal->review_period }}
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ess.performance.myAppraisals') }}">My Performance</a></li>
                <li class="breadcrumb-item active">Self Review</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-account fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        <!-- Review Period Info -->
                        <div class="alert" style="background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong style="color: #8a6d3b;"><i class="fa fa-user"></i> Employee:</strong> <span style="color: #8a6d3b;">{{ $appraisal->employee ? $appraisal->employee->full_name : '' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong style="color: #8a6d3b;"><i class="fa fa-calendar"></i> Review Period:</strong> <span style="color: #8a6d3b;">{{ $appraisal->review_period }}</span>
                                </div>
                                <div class="col-md-4">
                                    @if($appraisal->review_start_date && $appraisal->review_end_date)
                                        <strong style="color: #8a6d3b;"><i class="fa fa-clock-o"></i> Period Dates:</strong> <span style="color: #8a6d3b;">{{ $appraisal->review_start_date->format('M d, Y') }} - {{ $appraisal->review_end_date->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('ess.performance.saveSelfReview', $appraisal->appraisal_id) }}" method="POST" id="selfReviewForm">
                            @csrf

                            <div class="alert alert-info">
                                <strong><i class="fa fa-info-circle"></i> Instructions:</strong>
                                Please rate yourself on each performance metric below. Enter a score between 0 and the itemized weight for each metric, and provide comments to justify your rating.
                                <br><br>
                                <span class="text-muted"><i class="fa fa-lock"></i> <strong>Note:</strong> Columns with <span style="background-color: #f0ad4e; color: white; padding: 2px 6px; border-radius: 3px;">orange headers</span> show supervisor ratings (read-only). These will be filled by your supervisor after you submit your self-evaluation.</span>
                            </div>

                            <div class="alert" style="background-color: #d9edf7; border-color: #bce8f1; color: #31708f;">
                                <strong><i class="fa fa-paper-plane"></i> How to Complete:</strong>
                                <ol style="margin-top: 10px; margin-bottom: 0;">
                                    <li>Fill in your <strong>Self Rating</strong> and <strong>Your Comments</strong> for all items below</li>
                                    <li>Click <strong>"Save Progress"</strong> to save your work (you can come back later)</li>
                                    <li>When ready, click <strong>"Submit for Review"</strong> to send to your supervisor</li>
                                </ol>
                            </div>

                            <div class="form-body">
                                <h4>Section A: Performance Measures</h4>
                                @foreach($focusAreaScores as $faScore)
                                    @php $focusArea = $faScore['focusArea']; @endphp
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <strong>{{ $focusArea ? $focusArea->focus_area_name : 'Unknown' }}</strong>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="tr_header">
                                                            <th>Performance Metric</th>
                                                            <th>Performance Target</th>
                                                            <th>Weight</th>
                                                            <th>Self Rating<span class="validateRq">*</span></th>
                                                            <th>Your Comments</th>
                                                            <th style="background-color: #f0ad4e; color: white;">Supervisor Rating</th>
                                                            <th style="background-color: #f0ad4e; color: white;">Supervisor Comments</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($faScore['scores'] as $score)
                                                            <tr>
                                                                <td>{{ $score->goal ? $score->goal->performance_metric : '' }}</td>
                                                                <td>{{ $score->goal ? $score->goal->performance_target : '' }}</td>
                                                                <td>{{ $score->itemized_weighting }}</td>
                                                                <td>
                                                                    <input type="number" name="scores[{{ $score->score_id }}]" class="form-control" step="0.01" min="0" max="{{ $score->itemized_weighting }}" value="{{ $score->self_weighting }}" required>
                                                                </td>
                                                                <td>
                                                                    <textarea name="comments[{{ $score->score_id }}]" class="form-control" rows="2" placeholder="Justify your rating">{{ $score->self_comments }}</textarea>
                                                                </td>
                                                                <td style="background-color: #fcf8e3;">
                                                                    <input type="number" class="form-control" step="0.01" value="{{ $score->review_weighting > 0 ? $score->review_weighting : '' }}" readonly disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                                                </td>
                                                                <td style="background-color: #fcf8e3;">
                                                                    <textarea class="form-control" rows="2" readonly disabled style="background-color: #f5f5f5; cursor: not-allowed;">{{ $score->review_comments }}</textarea>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <strong>Section B: Behavioral Expectations</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="tr_header">
                                                        <th>Behavioral Item</th>
                                                        <th>Weight</th>
                                                        <th>Self Rating<span class="validateRq">*</span></th>
                                                        <th>Your Comments</th>
                                                        <th style="background-color: #f0ad4e; color: white;">Supervisor Rating</th>
                                                        <th style="background-color: #f0ad4e; color: white;">Supervisor Comments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($appraisal->behavioralScores as $bScore)
                                                        <tr>
                                                            <td>{{ $bScore->behavioralItem ? $bScore->behavioralItem->item_name : '' }}</td>
                                                            <td>{{ $bScore->itemized_weighting }}</td>
                                                            <td>
                                                                <input type="number" name="behavioral_scores[{{ $bScore->behavioral_score_id }}]" class="form-control" step="0.01" min="0" max="{{ $bScore->itemized_weighting }}" value="{{ $bScore->self_weighting }}" required>
                                                            </td>
                                                            <td>
                                                                <textarea name="behavioral_comments[{{ $bScore->behavioral_score_id }}]" class="form-control" rows="2" placeholder="Justify your rating">{{ $bScore->self_comments }}</textarea>
                                                            </td>
                                                            <td style="background-color: #fcf8e3;">
                                                                <input type="number" class="form-control" step="0.01" value="{{ $bScore->review_weighting > 0 ? $bScore->review_weighting : '' }}" readonly disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                                            </td>
                                                            <td style="background-color: #fcf8e3;">
                                                                <textarea class="form-control" rows="2" readonly disabled style="background-color: #f5f5f5; cursor: not-allowed;">{{ $bScore->review_comments }}</textarea>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-info btn_style btn-block"><i class="fa fa-save"></i> Save Progress</button>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="{{ route('ess.performance.myAppraisals') }}" class="btn btn-default btn_style btn-block"><i class="fa fa-times"></i> Cancel</a>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-success btn_style btn-block" onclick="submitForReview()">
                                            <i class="fa fa-paper-plane"></i> Submit for Review
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>

                        <!-- Hidden form for submitting to supervisor -->
                        <form id="submitReviewForm" action="{{ route('ess.performance.submitSelfReview', $appraisal->appraisal_id) }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                        <script>
                            function submitForReview() {
                                if (confirm('Are you sure you want to submit your self-evaluation?\n\nOnce submitted, your supervisor will be able to review it. You will not be able to edit your ratings after submission.\n\nMake sure you have filled in all ratings before submitting.')) {
                                    document.getElementById('submitReviewForm').submit();
                                }
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
