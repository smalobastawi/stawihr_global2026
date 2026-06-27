@extends('admin.master')
@section('content')
@section('title')
    Create Development Plan
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ess.pdp.myPlans') }}">My Development Plans</a></li>
                <li class="breadcrumb-item active">Create Plan</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @include('admin.partials.alert')

                        <form action="{{ route('ess.pdp.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Plan Title<span class="validateRq">*</span></label>
                                        <input type="text" name="plan_title" class="form-control required" value="{{ old('plan_title') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Plan Year<span class="validateRq">*</span></label>
                                        <input type="number" name="plan_year" class="form-control required" value="{{ old('plan_year', date('Y')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Review Frequency<span class="validateRq">*</span></label>
                                        @php $freq = old('review_frequency', $setting->default_review_frequency); @endphp
                                        <select name="review_frequency" class="form-control required" required>
                                            <option value="quarterly" {{ $freq == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="bi_annually" {{ $freq == 'bi_annually' ? 'selected' : '' }}>Bi-Annually</option>
                                            <option value="annually" {{ $freq == 'annually' ? 'selected' : '' }}>Annually</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Start Date<span class="validateRq">*</span></label>
                                        <input type="date" name="start_date" class="form-control required" value="{{ old('start_date') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>End Date<span class="validateRq">*</span></label>
                                        <input type="date" name="end_date" class="form-control required" value="{{ old('end_date') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Development Focus</label>
                                <textarea name="development_focus" class="form-control" rows="2">{{ old('development_focus') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Career Aspirations</label>
                                <textarea name="career_aspirations" class="form-control" rows="2">{{ old('career_aspirations') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Create Plan</button>
                            <a href="{{ route('ess.pdp.myPlans') }}" class="btn btn-default">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
