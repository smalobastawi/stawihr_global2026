@extends('admin.master')
@section('content')
@section('title')
Record Progress - {{ $plan->plan_title }}
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
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('error'))
                            <div class="alert alert-danger"><strong>{{ session()->get('error') }}</strong></div>
                        @endif

                        <form action="{{ route('pdp.progress.store', $plan->pdp_plan_id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Development Goal<span class="validateRq">*</span></label>
                                        <select name="pdp_goal_id" class="form-control required" required>
                                            <option value="">Select Goal</option>
                                            @foreach($plan->goals as $goal)
                                                <option value="{{ $goal->pdp_goal_id }}" {{ (string) old('pdp_goal_id', $selectedGoalId) === (string) $goal->pdp_goal_id ? 'selected' : '' }}>{{ $goal->goal_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Review Period<span class="validateRq">*</span></label>
                                        <select id="review_period" class="form-control required">
                                            <option value="">Select Period</option>
                                            @foreach($periodOptions as $option)
                                                <option value="{{ $option['label'] }}"
                                                    data-year="{{ $option['review_year'] }}"
                                                    data-quarter="{{ $option['review_quarter'] }}"
                                                    data-half="{{ $option['review_half'] }}">{{ $option['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Progress (%)<span class="validateRq">*</span></label>
                                        <input type="number" name="progress_percentage" class="form-control required" min="0" max="100" value="{{ old('progress_percentage', 0) }}" required>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="review_year" id="review_year" value="{{ old('review_year', $plan->plan_year) }}">
                            <input type="hidden" name="review_quarter" id="review_quarter" value="{{ old('review_quarter') }}">
                            <input type="hidden" name="review_half" id="review_half" value="{{ old('review_half') }}">

                            <div class="form-group">
                                <label>Achievement Summary<span class="validateRq">*</span></label>
                                <textarea name="achievement_summary" class="form-control required" rows="3" required>{{ old('achievement_summary') }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Challenges</label>
                                        <textarea name="challenges" class="form-control" rows="2">{{ old('challenges') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Support Needed</label>
                                        <textarea name="support_needed" class="form-control" rows="2">{{ old('support_needed') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Next Steps</label>
                                        <textarea name="next_steps" class="form-control" rows="2">{{ old('next_steps') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Submit Progress</button>
                            <a href="{{ route('pdp.progress.index', $plan->pdp_plan_id) }}" class="btn btn-default">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $('#review_period').change(function () {
        var selected = $(this).find(':selected');
        $('#review_year').val(selected.data('year'));
        $('#review_quarter').val(selected.data('quarter') || '');
        $('#review_half').val(selected.data('half') || '');
    });
</script>
@endsection
