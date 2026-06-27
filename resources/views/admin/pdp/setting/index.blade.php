@extends('admin.master')
@section('content')
@section('title')
PDP Policy Settings
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
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        <form action="{{ route('pdp.setting.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Default Review Frequency<span class="validateRq">*</span></label>
                                        <select name="default_review_frequency" class="form-control required">
                                            <option value="quarterly" {{ $setting->default_review_frequency == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="bi_annually" {{ $setting->default_review_frequency == 'bi_annually' ? 'selected' : '' }}>Bi-Annually</option>
                                            <option value="annually" {{ $setting->default_review_frequency == 'annually' ? 'selected' : '' }}>Annually</option>
                                        </select>
                                        <small class="text-muted">Sets how often staff should record progress on development goals.</small>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Policy Notes</label>
                                        <textarea name="policy_notes" class="form-control" rows="3" placeholder="Guidance for staff and managers on PDP expectations">{{ $setting->policy_notes }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" name="allow_employee_self_service" id="allow_employee_self_service" value="1" {{ $setting->allow_employee_self_service ? 'checked' : '' }}>
                                        <label for="allow_employee_self_service">Allow employees to create and manage their own plans</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" name="require_supervisor_approval" id="require_supervisor_approval" value="1" {{ $setting->require_supervisor_approval ? 'checked' : '' }}>
                                        <label for="require_supervisor_approval">Require supervisor approval on plans</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" name="require_hr_review" id="require_hr_review" value="1" {{ $setting->require_hr_review ? 'checked' : '' }}>
                                        <label for="require_hr_review">Require HR review on plans</label>
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
