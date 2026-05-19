@extends('admin.master')
@section('content')
@section('title')
    Edit Program Allocation
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">Edit Program Allocation for {{ $employee->full_name }}</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('program-allocation.update', $programAllocation->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="program_id">Program Name</label>
                                            <select name="program_id" id="program_id" class="form-control" required>
                                                @foreach ($programs as $program)
                                                    <option value="{{ $program->id }}" {{ $programAllocation->program_id == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="percentage_allocated">Percentage Allocated</label>
                                            <input type="number" name="percentage_allocated" id="percentage_allocated" class="form-control" step="0.01" min="0" max="100" value="{{ old('percentage_allocated', $programAllocation->percentage_allocated) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="allocation_start_date">Allocation Start Date</label>
                                            <input type="date" name="allocation_start_date" id="allocation_start_date" class="form-control" value="{{ old('allocation_start_date', $programAllocation->allocation_start_date ? $programAllocation->allocation_start_date->format('Y-m-d') : '') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="allocation_end_date">Allocation End Date</label>
                                            <input type="date" name="allocation_end_date" id="allocation_end_date" class="form-control" value="{{ old('allocation_end_date', $programAllocation->allocation_end_date ? $programAllocation->allocation_end_date->format('Y-m-d') : '') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        @foreach (GeneralStatus::toArray() as $key => $value)
                                            <option value="{{ $key }}" {{ $programAllocation->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-info"> <i class="fa fa-check"></i> Update</button>
                                <a href="{{ route('employee.show', $employee->employee_id) }}" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection