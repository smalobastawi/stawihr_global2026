@extends('admin.master')
@section('content')
@section('title')
    Master Roll
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-filter"></i> Filters
                    <a data-toggle="collapse" href="#filterPanel" class="pull-right">
                        <i class="fa fa-chevron-down"></i>
                    </a>
                </div>
                <div id="filterPanel" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('employee.masterRoll') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Status</label>
                                        <div class="col-md-8">
                                            <select name="status" class="form-control select2">
                                                <option value="">All</option>
                                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Department</label>
                                        <div class="col-md-8">
                                            <select name="department_id" class="form-control select2">
                                                <option value="">All Departments</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->department_id }}" {{ request('department_id') == $department->department_id ? 'selected' : '' }}>{{ $department->department_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Section</label>
                                        <div class="col-md-8">
                                            <select name="section_id" class="form-control select2">
                                                <option value="">All Sections</option>
                                                @foreach($sections as $section)
                                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Date From</label>
                                        <div class="col-md-8">
                                            <input type="text" name="date_from" class="form-control dateField" value="{{ request('date_from') }}" placeholder="DD/MM/YYYY">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Date To</label>
                                        <div class="col-md-8">
                                            <input type="text" name="date_to" class="form-control dateField" value="{{ request('date_to') }}" placeholder="DD/MM/YYYY">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-filter"></i> Apply Filters</button>
                                            <a href="{{ route('employee.masterRoll') }}" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title') <span class="badge badge-info">{{ $employees->total() }} employees</span></div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Showing {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} employees
                        </div>
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Status</th>
                                        <th>Termination Date</th>
                                        <th>Name</th>
                                        <th>ID/Passport</th>
                                        <th>Department</th>
                                        <th>Section</th>
                                        <th>Designation</th>
                                        <th>Work Location</th>
                                        <th>Hourly Salary</th>
                                        <th>Supervisor</th>
                                        <th>KRA Pin</th>
                                        <th>NSSF No</th>
                                        <th>NHIF No</th>
                                        <th>Payroll Number</th>
                                        <th>NSSF Rate Type</th>
                                        <th>Employee Section</th>
                                        <th>Driving License Number</th>
                                        <th>Residential Status</th>
                                        <th>Residential Area</th>
                                        <th>Highest Qualification</th>
                                        <th>Payroll Profile</th>
                                        <th>Nationality</th>
                                        <th>Tribe</th>
                                        <th>@lang('employee.personal_email')</th>
                                        <th>Next of Kin</th>
                                        <th>Next of Kin Phone</th>
                                        <th>Personal Phone</th>
                                        <!-- New Columns -->
                                        <th>Full Name</th>
                                        <th>Contract Status</th>
                                        <th>Residence Location</th>
                                        <th>Sub-Location</th>
                                        <th>Contract Type</th>
                                        <th>Start Date</th>
                                        <th>Years in Service</th>
                                        <th>End of Probation</th>
                                        <th>End of Contract</th>
                                        <th>Age</th>
                                        <th>Bank</th>
                                        <th>Bank Branch</th>
                                        <th>Branch Code</th>
                                        <th>Account Number</th>
                                        <th>Contract Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td>{{ $employee->employee_id }}</td>
                                            <td>
                                                @if($employee->status == 1)
                                                    <span class="label label-success">Active</span>
                                                @else
                                                    <span class="label label-danger">Terminated</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($employee->terminations && $employee->terminations->isNotEmpty())
                                                    {{ dateConvertDBtoForm($employee->terminations->first()->termination_date) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $employee->fullname() ?? 'N/A' }}</td>
                                            <td>{{ $employee->national_id ?? 'N/A' }}</td>
                                            <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                                            <td>{{ $employee->employeeSection->name ?? 'N/A' }}</td>
                                            <td>{{ $employee->designation->designation_name ?? 'N/A' }}</td>
                                            <td>{{ $employee->workLocation->location_name ?? 'N/A' }}</td>
                                            <td>
                                                @if($employee->hourlySalaries && $employee->hourlySalaries->isNotEmpty())
                                                    {{ number_format($employee->hourlySalaries->first()->hourly_salary, 2) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($employee->supervisor)
                                                    {{ $employee->supervisor->fullname() ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $employee->KRA_Pin ?? 'N/A' }}</td>
                                            <td>{{ $employee->NSSF_no ?? 'N/A' }}</td>
                                            <td>{{ $employee->NHIF_no ?? 'N/A' }}</td>
                                            <td>{{ $employee->payroll_number ?? 'N/A' }}</td>
                                            <td>{{ $employee->nssf_rate_type ?? 'N/A' }}</td>
                                            <td>{{ $employee->employeeSection->name ?? 'N/A' }}</td>
                                            <td>{{ $employee->driving_license_number ?? 'N/A' }}</td>
                                            <td>
                                                @if($employee->residential_status)
                                                    {{ ResidencyStatus::getName($employee->residential_status) ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $employee->residential_area ?? 'N/A' }}</td>
                                            <td>{{ $employee->highest_qualification ?? 'N/A' }}</td>
                                            <td>{{ $employee->payroll_profile ?? 'N/A' }}</td>
                                            <td>{{ $employee->nationality ?? 'N/A' }}</td>
                                            <td>{{ $employee->tribe ?? 'N/A' }}</td>
                                            <td>{{ $employee->personal_email ?? 'N/A' }}</td>
                                            <td>{{ $employee->next_of_kin ?? 'N/A' }}</td>
                                            <td>{{ $employee->next_of_kin_phone ?? 'N/A' }}</td>
                                            <td>{{ $employee->personal_phone ?? 'N/A' }}</td>
                                            <!-- New Columns Data -->
                                            <td>{{ $employee->full_name ?? 'N/A' }}</td>
                                            <td>{{ $employee->contract_status ?? 'N/A' }}</td>
                                            <td>{{ $employee->location ?? 'N/A' }}</td>
                                            <td>{{ $employee->sub_location ?? 'N/A' }}</td>
                                            <td>{{ $employee->contract_type ?? 'N/A' }}</td>
                                            <td>{{ $employee->start_date ?? 'N/A' }}</td>
                                            <td>{{ $employee->years_in_service ?? 'N/A' }}</td>
                                            <td>{{ $employee->end_of_probation ?? 'N/A' }}</td>
                                            <td>{{ $employee->end_of_contract ?? 'N/A' }}</td>
                                            <td>{{ $employee->age ?? 'N/A' }}</td>
                                            <td>{{ $employee->bank ?? 'N/A' }}</td>
                                            <td>{{ $employee->bank_branch ?? 'N/A' }}</td>
                                            <td>{{ $employee->bank_branch_code ?? 'N/A' }}</td>
                                            <td>{{ $employee->bank_account_number ?? 'N/A' }}</td>
                                            <td>
                                                @if($employee->contractDetails && $employee->contractDetails->isNotEmpty())
                                                    @foreach ($employee->contractDetails as $contract)
                                                        {{ $contract->start_date ?? 'N/A' }} - {{ $contract->end_date ?? 'N/A' }}<br>
                                                    @endforeach
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination-wrapper" style="text-align: center; padding: 20px;">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
