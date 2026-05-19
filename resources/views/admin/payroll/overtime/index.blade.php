@extends('admin.master')
@section('content')
@section('title')
    Payroll  Overtime Records
@endsection

<style>
    .departmentName{
        position: relative;
    }
    #department_id-error{
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%he;
        width: 100%;
        height: 100%;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <div class="pull-right">
                <a href="{{ route('payroll.overtime.create') }}" class="btn btn-success m-l-5 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-plus-circle"></i> Add Overtime Record
                </a>
                <a href="{{ route('payroll.overtime.template.download') }}" class="btn btn-info m-l-5 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-download"></i> Download Template
                </a>
                <a href="{{ route('payroll.overtime.import.form') }}" class="btn btn-warning m-l-5 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-upload"></i> Import CSV
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <div class="row">
                            <div id="searchBox">
                                <form method="POST">
							@csrf
                                <div class="col-md-3">
                                    <div class="form-group departmentName">
                                        <label class="control-label" for="employee_id">@lang('employee.employee')</label>
                                        <select class="form-control employee_id select2" name="employee_id">
                                            <option value="">---- @lang('common.all') ----</option>
                                            @foreach($employees as $value)
                                                <option value="{{$value->employee_id}}" 
                                                    @if(request('employee_id') == $value->employee_id) selected @endif>
                                                    {{$value->payroll_number  }} - {{$value->fullName()}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" for="month_year">Month/Year</label>
                                        <input type="month" class="form-control" name="month_year" id="monthField"
                                            value="{{request('month_year')}}">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <hr>

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>Payroll No</th>
                                        <th>@lang('employee.employee_name')</th>
                                        <th>Month</th>
                                       <th>Weekend Hours</th>
                                        <th>Weekend Days</th>
                                        <th>Public Holiday Hours</th>
                                        <th>Public Holiday Days</th>
                                        <th>Weekday Hours</th>
                                        <th>Weekday Days</th>
                                       
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($overtimes) > 0)
                                        @foreach($overtimes as $key => $value)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $value->employee->payroll_number ?? '' }}</td>
                                                <td>{{ $value->employee->fullName() ?? '' }} </td>
                                                <td>{{ $value->month_year }}</td>
                                                 
                                                <td>{{ $value->weekend_hours_totals ?? '0.00' }}</td>
                                                <td>{{ $value->weekend_days_totals ?? '0' }}</td>
                                                <td>{{ $value->public_holiday_hours_totals ?? '0.00' }}</td>
                                                <td>{{ $value->public_holiday_days_totals ?? '0' }}</td>
                                                <td>{{ $value->weekday_hours_total ?? '0.00' }}</td>
                                                <td>{{ $value->weekday_days_total ?? '0' }}</td>

                                               
                                                <td>
                                                    <a href="{{ route('payroll.overtime.show', $value->id) }}" class="btn btn-info btn-sm" title="View" style="color: white">
                                                        <i class="fa fa-eye">View</i>
                                                    </a>
                                                    <a href="{{ route('payroll.overtime.edit', $value->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fa fa-edit"> Edit</i>
                                                    </a>
                                                    <form method="POST">
							@csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this overtime record?')">
                                                        <i class="fa fa-trash">Delete</i>
                                                    </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    
                                    @endif
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $overtimes->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
<script>
    $('.employee_id').select2({
        placeholder: 'Search employee',
    });

    </script>
    <script>
    jQuery(function() {
        $("#salaryAdvanceForm").validate();

        // Initialize month picker for existing month field
        $('.monthField').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

        // Initialize month picker for payroll month field
        $('.payrollMonth').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
    });
  
</script>
    @endsection
