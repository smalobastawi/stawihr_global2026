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
                                            <th style="width: 40%;">ID</th>
                                            <td>{{ $company->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $company->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Logo</th>
                                            <td>
                                                @if ($company->logo && companyLogoUrl($company))
                                                    <img src="{{ companyLogoUrl($company) }}" alt="{{ $company->name }} logo"
                                                        style="max-height: 80px; max-width: 200px; object-fit: contain;">
                                                @else
                                                    <img src="{{ defaultLogoUrl() }}" alt="Default logo"
                                                        style="max-height: 80px; max-width: 200px; object-fit: contain;">
                                                    @if ($company->logo)
                                                        <p class="text-muted small m-t-5">Uploaded logo file is missing from storage. Re-upload the logo.</p>
                                                    @else
                                                        <span class="text-muted">No logo uploaded</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Domain</th>
                                            <td>{{ $company->domain ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="label label-{{ $company->status == 'active' ? 'success' : 'danger' }}">
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

                                    <h4 style="margin-top: 25px;">Payroll &amp; Currency Settings</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%;">
                                                Payroll Country
                                                @include('admin.partials.field-tooltip', ['tooltip' => 'Which country\'s tax and statutory deduction rules apply when payroll is processed (e.g. Kenya PAYE, NSSF, SHIF).'])
                                            </th>
                                            <td>
                                                {{ $company->payroll_country
                                                    ? \App\Lib\Enumerations\PayrollCountry::getName($company->payroll_country)
                                                    : ($company->country ?? 'N/A') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                Statutory Base Currency
                                                @include('admin.partials.field-tooltip', ['tooltip' => 'Currency used for all statutory payroll calculations: PAYE, NSSF, SHIF, housing levy, and pension.'])
                                            </th>
                                            <td>
                                                {{ $company->getPayrollBaseCurrency() }}
                                                — {{ \App\Lib\Enumerations\Currency::getName($company->getPayrollBaseCurrency()) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                Multi-Currency Payroll
                                                @include('admin.partials.field-tooltip', ['tooltip' => 'When enabled, employees may have salary or payment in a currency different from the statutory base currency. Exchange rates are set under Payroll → Setup → Exchange Rates.'])
                                            </th>
                                            <td>
                                                {{ $company->allow_employee_payment_currency
                                                    ? 'Enabled — staff may use different salary or payment currencies'
                                                    : 'Disabled — all amounts use the statutory base currency' }}
                                            </td>
                                        </tr>
                                        @if ($company->allow_employee_payment_currency)
                                            <tr>
                                                <th>
                                                    Default Payment Currency
                                                    @include('admin.partials.field-tooltip', ['tooltip' => 'Default bank payment currency for employees who do not have an individual payment currency on their payroll profile.'])
                                                </th>
                                                <td>
                                                    @php $paymentCurrency = $company->default_payment_currency ?: $company->getPayrollBaseCurrency(); @endphp
                                                    {{ $paymentCurrency }}
                                                    — {{ \App\Lib\Enumerations\Currency::getName($paymentCurrency) }}
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h4>Company Statistics</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%;">Number of Employees</th>
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

                                    <h4 style="margin-top: 25px;">Contact &amp; Correspondence</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%;">Registered Address</th>
                                            <td>{!! nl2br(e($company->address ?? 'N/A')) !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Official Phone</th>
                                            <td>{{ $company->official_contact_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Official Email</th>
                                            <td>
                                                @if ($company->official_email)
                                                    <a href="mailto:{{ $company->official_email }}">{{ $company->official_email }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Contact Person</th>
                                            <td>{{ $company->company_contact_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Representative Phone</th>
                                            <td>{{ $company->representative_phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Representative Email</th>
                                            <td>
                                                @if ($company->representative_email)
                                                    <a href="mailto:{{ $company->representative_email }}">{{ $company->representative_email }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Print Head (Payslips &amp; Reports)</th>
                                            <td>
                                                @if ($company->print_head_description)
                                                    <div class="well well-sm m-b-0">{!! $company->print_head_description !!}</div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <h4 style="margin-top: 10px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                <i class="mdi mdi-account-card-details fa-fw"></i> Kenya Government Employer Information
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%;">KRA PIN</th>
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
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%;">NITA Registration Number</th>
                                            <td>{{ $company->nita_registration_number ?? 'N/A' }}</td>
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
