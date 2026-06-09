<div class="header-section">
    <div class="row">
        <div class="col-md-4">APPENDIX 2A</div>
        <div class="col-md-4"></div>
        <div class="col-md-4 text-right">(P.9A)</div>
    </div>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">
            <img src="{{ asset('admin_assets/img/KRAlogo.png') }}" width="220px" alt="KRA Logo">
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <strong>ISO 9001:2015 CERTIFIED</strong><br>
            <strong>KENYA REVENUE AUTHORITY DOMESTIC TAXES DEPARTMENT</strong><br>
            <strong>TAX DEDUCTION CARD YEAR {{ $taxationData['tax_year'] }}</strong>
        </div>
    </div>
</div>

<div class="row header-section">
    <div class="col-md-6">
        <strong>Employer's Name:</strong> {{ $company?->name ?? '' }}<br>
        <strong>Employee's Main Name:</strong> {{ $employeeDetails->first_name ?? '' }}<br>
        <strong>Employee's Other Names:</strong> {{ $employeeDetails->last_name ?? '' }}
    </div>
    <div class="col-md-6 text-right">
        <strong>Employer's PIN:</strong> {{ $company?->kra_pin ?? '...........................' }}<br>
        <strong>Employee's PIN:</strong> {{ $employeeDetails->KRA_Pin ?? '...........................' }}<br>
        <strong>Employee's Payroll No:</strong> {{ $employeeDetails->payroll_number ?? '...........................' }}
    </div>
</div>
