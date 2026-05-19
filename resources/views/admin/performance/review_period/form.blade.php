@extends('admin.master')
@section('content')
@section('title')
{{ isset($editModeData) ? 'Edit' : 'Add' }} Review Period
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
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        @if(isset($editModeData))
                            <form action="{{ route('performance.reviewPeriod.update', $editModeData->period_id) }}" method="POST" id="reviewPeriodForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('performance.reviewPeriod.store') }}" method="POST" id="reviewPeriodForm">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="period_name">Period Name<span class="validateRq">*</span></label>
                                        <input type="text" name="period_name" id="period_name" class="form-control required" 
                                               placeholder="e.g. Q1 2026, Jan - June 2026, Annual 2026" 
                                               value="{{ old('period_name', isset($editModeData) ? $editModeData->period_name : '') }}" required>
                                        <small class="text-muted">Give this review period a descriptive name</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control" 
                                               placeholder="e.g. 1, 2, 3..." 
                                               value="{{ old('sort_order', isset($editModeData) ? $editModeData->sort_order : 0) }}">
                                        <small class="text-muted">Lower numbers appear first in dropdowns</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="start_date">Start Date<span class="validateRq">*</span></label>
                                        <input type="date" name="start_date" id="start_date" class="form-control required" 
                                               value="{{ old('start_date', isset($editModeData) ? $editModeData->start_date->format('Y-m-d') : '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_date">End Date<span class="validateRq">*</span></label>
                                        <input type="date" name="end_date" id="end_date" class="form-control required" 
                                               value="{{ old('end_date', isset($editModeData) ? $editModeData->end_date->format('Y-m-d') : '') }}" required>
                                        <small class="text-danger" id="date_error" style="display: none;">End date must be after start date</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                                   {{ old('is_active', isset($editModeData) ? $editModeData->is_active : true) ? 'checked' : '' }}>
                                            <strong>Active</strong>
                                        </label>
                                        <small class="text-muted block">Inactive periods won't appear in dropdowns</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" 
                                                  placeholder="Optional description or notes about this review period">{{ old('description', isset($editModeData) ? $editModeData->description : '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> Save</button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('performance.reviewPeriod.index') }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const dateError = document.getElementById('date_error');
    const form = document.getElementById('reviewPeriodForm');

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && endDate < startDate) {
            dateError.style.display = 'block';
            return false;
        } else {
            dateError.style.display = 'none';
            return true;
        }
    }

    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);

    form.addEventListener('submit', function(e) {
        if (!validateDates()) {
            e.preventDefault();
            alert('End date must be after or equal to start date.');
        }
    });
});
</script>
@endsection
