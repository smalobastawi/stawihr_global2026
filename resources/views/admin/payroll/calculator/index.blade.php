@extends('admin.master')

@section('title')
    Payroll Calculator
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                    </li>
                    <li><a href="{{ route('payroll.dashboard') }}">Payroll</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                @include('admin.partials.alert')

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-calculator fa-fw"></i> Payroll Calculator
                    </div>
                    <div class="panel-body">
                        <p class="text-muted">
                            Estimate statutory deductions and net pay from a gross salary. This tool is not tied to any
                            employee, company, or payroll period and does not save results.
                        </p>

                        <form action="{{ route('payroll.calculator.calculate') }}" method="POST">
                            @csrf

                            <div class="form-group {{ $errors->has('country_id') ? 'has-error' : '' }}">
                                <label for="country_id">Country / Jurisdiction <span class="text-danger">*</span></label>
                                <select name="country_id" id="country_id" class="form-control" required>
                                    <option value="">Select country</option>
                                    @foreach ($countries as $id => $name)
                                        <option value="{{ $id }}" @selected(old('country_id') == $id)>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group {{ $errors->has('gross_amount') ? 'has-error' : '' }}">
                                <label for="gross_amount">Gross Amount <span class="text-danger">*</span></label>
                                <input type="number"
                                    name="gross_amount"
                                    id="gross_amount"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('gross_amount') }}"
                                    placeholder="Enter gross salary"
                                    required>
                                @error('gross_amount')
                                    <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group" id="kenya-nssf-options" style="display: none;">
                                <label for="nssf_rate_type">Kenya NSSF Rate Type</label>
                                <select name="nssf_rate_type" id="nssf_rate_type" class="form-control">
                                    <option value="2" @selected(old('nssf_rate_type', '2') == '2')>Tier I &amp; II (6%)</option>
                                    <option value="1" @selected(old('nssf_rate_type') == '1')>Old Rate (KES 200)</option>
                                    <option value="3" @selected(old('nssf_rate_type') == '3')>Tier I Only (KES 480)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-info waves-effect waves-light">
                                <i class="fa fa-calculator"></i> Calculate
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        (function () {
            var countrySelect = document.getElementById('country_id');
            var kenyaOptions = document.getElementById('kenya-nssf-options');

            function toggleKenyaOptions() {
                kenyaOptions.style.display = countrySelect.value === '1' ? 'block' : 'none';
            }

            countrySelect.addEventListener('change', toggleKenyaOptions);
            toggleKenyaOptions();
        })();
    </script>
@endsection
