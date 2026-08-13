@extends('admin.master')
@section('content')
@section('title')
Performance Appraisal Settings
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
                <div class="panel-heading"><i class="mdi mdi-settings fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <strong>Organization appraisal approach</strong> controls whether HR defines performance areas/metrics
                            for staff to rate against, or whether staff define their own goals/objectives/metrics before rating.
                            Existing appraisals keep the approach they were created with.
                        </div>

                        <form action="{{ route('performance.setting.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Appraisal Approach <span class="validateRq">*</span></label>
                                        @foreach($approaches as $value => $label)
                                            <div class="radio radio-info">
                                                <input type="radio" name="appraisal_approach" id="approach_{{ $value }}"
                                                       value="{{ $value }}" {{ $setting->appraisal_approach == $value ? 'checked' : '' }}>
                                                <label for="approach_{{ $value }}">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                        <small class="text-muted">
                                            <strong>HR-defined:</strong> Staff only rate against areas/metrics set by HR.<br>
                                            <strong>Staff-defined:</strong> Staff set their own focus areas, objectives and metrics, then rate themselves.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Policy Notes</label>
                                        <textarea name="policy_notes" class="form-control" rows="5"
                                                  placeholder="Guidance for staff and managers on how appraisals work in your organization">{{ old('policy_notes', $setting->policy_notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
