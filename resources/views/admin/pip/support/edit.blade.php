@extends('admin.master')
@section('content')
@section('title')
Edit Support Resource
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
                        <form action="{{ route('pip.support.update', $editModeData->resource_id) }}" method="POST" id="supportForm">
                            @csrf
                            @method('PUT')

                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Support Type<span class="validateRq">*</span></label>
                                            <select name="support_type" class="form-control" required>
                                                <option value="training" {{ $editModeData->support_type == 'training' ? 'selected' : '' }}>Training</option>
                                                <option value="mentorship" {{ $editModeData->support_type == 'mentorship' ? 'selected' : '' }}>Mentorship</option>
                                                <option value="tools" {{ $editModeData->support_type == 'tools' ? 'selected' : '' }}>Tools</option>
                                                <option value="counseling" {{ $editModeData->support_type == 'counseling' ? 'selected' : '' }}>Counseling</option>
                                                <option value="other" {{ $editModeData->support_type == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Description<span class="validateRq">*</span></label>
                                            <input type="text" name="description" class="form-control" value="{{ $editModeData->description }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Provider<span class="validateRq">*</span></label>
                                            <select name="provider" class="form-control" required>
                                                <option value="hr" {{ $editModeData->provider == 'hr' ? 'selected' : '' }}>HR</option>
                                                <option value="supervisor" {{ $editModeData->provider == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                                <option value="external" {{ $editModeData->provider == 'external' ? 'selected' : '' }}>External</option>
                                                <option value="peer" {{ $editModeData->provider == 'peer' ? 'selected' : '' }}>Peer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Scheduled Date</label>
                                            <input type="date" name="scheduled_date" class="form-control" value="{{ $editModeData->scheduled_date ? $editModeData->scheduled_date->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> Update</button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="{{ route('pip.support.index', $plan->pip_id) }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
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
