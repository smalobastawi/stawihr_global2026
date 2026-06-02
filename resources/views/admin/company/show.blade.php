@extends('admin.master')

@section('title')
    View Company
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i>@lang('dashboard.dashboard')</a></li>
                    <li><a href="{{ route('company.index') }}">Companies</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-eye fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Company Details</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>ID</th>
                                            <td>{{ $company->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $company->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Logo</th>
                                            <td>
                                                @if($company->logo)
                                                    <img src="{{ companyLogoUrl($company) }}" alt="{{ $company->name }} logo"
                                                        style="max-height: 80px; max-width: 200px; object-fit: contain;">
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Domain</th>
                                            <td>{{ $company->domain }}</td>
                                        </tr>
                                        <tr>
                                            <th>Country</th>
                                            <td>{{ $company->country }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span
                                                    class="label label-{{ $company->status == 'active' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($company->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $company->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $company->updated_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </table>

                                    <h4 style="margin-top: 25px;">Kenya Government Employer Information</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>KRA PIN</th>
                                            <td>{{ $company->kra_pin ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Registration Number</th>
                                            <td>{{ $company->registration_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>NSSF Employer Number</th>
                                            <td>{{ $company->nssf_employer_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>SHIF Employer Code</th>
                                            <td>{{ $company->shif_employer_code ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Employer Number</th>
                                            <td>{{ $company->employer_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>NITA Registration Number</th>
                                            <td>{{ $company->nita_registration_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>eCitizen Identifier</th>
                                            <td>{{ $company->ecitizen_identifier ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h4>Company Statistics</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Number of Employees</th>
                                            <td>{{ $employeeCount }}</td>
                                        </tr>
                                        <tr>
                                            <th>Number of Departments</th>
                                            <td>{{ $departmentCount }}</td>
                                        </tr>
                                        <tr>
                                            <th>Number of Active Payroll Profiles</th>
                                            <td>{{ $activePayrollProfilesCount }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="form-actions">
                                <a href="{{ route('company.index') }}" class="btn btn-default">@lang('common.back')</a>
                                <a href="{{ route('company.edit', $company) }}" class="btn btn-warning"><i
                                        class="fa fa-edit"></i> Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
