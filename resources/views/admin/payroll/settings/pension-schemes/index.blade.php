@extends('admin.master')

@section('title', 'Pension Schemes')

@section('content')
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h4 class="page-title">@lang('payroll.pension_schemes')</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
                <li>@lang('payroll.pension_schemes')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="box-title m-b-0">@lang('payroll.pension_schemes_management')</h3>
                        <p class="text-muted m-b-30">@lang('payroll.manage_employee_pension_schemes')</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('payroll.settings.pension-schemes.create') }}"
                            class="btn btn-success btn-outline">
                            <i class="fa fa-plus"></i> @lang('payroll.create_scheme')
                        </a>
                        <form method="POST" action="{{ route('payroll.settings.pension-schemes.create-defaults') }}"
                            style="display: inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-info btn-outline"
                                onclick="return confirm('@lang('payroll.create_default_pension_schemes')')">
                                <i class="fa fa-magic"></i> @lang('payroll.create_defaults')
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('payroll.scheme_name')</th>
                                <th>@lang('payroll.code')</th>
                                <th>@lang('payroll.provider')</th>
                                <th>@lang('payroll.max_employee_rate')</th>
                                <th>@lang('payroll.max_employer_rate')</th>
                                <th>@lang('payroll.employees')</th>
                                <th>@lang('payroll.status')</th>
                                <th>@lang('payroll.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pensionSchemes as $scheme)
                                <tr>
                                    <td>
                                        <strong>{{ $scheme->name }}</strong>
                                        @if ($scheme->description)
                                            <br><small class="text-muted">{{ Str::limit($scheme->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $scheme->code }}</code>
                                    </td>
                                    <td>
                                        {{ $scheme->provider_name }}
                                        @if ($scheme->provider_contact)
                                            <br><small class="text-muted">{{ $scheme->provider_contact }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="label label-info">{{ $scheme->max_employee_rate }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $scheme->max_employer_rate }}%</span>
                                    </td>
                                    <td>
                                        <span class="badge">{{ $scheme->employee_payrolls_count }}</span>
                                    </td>
                                    <td>
                                        @if ($scheme->is_active)
                                            <span class="label label-success">@lang('payroll.active')</span>
                                        @else
                                            <span class="label label-danger">@lang('payroll.inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('payroll.settings.pension-schemes.show', $scheme) }}"
                                                class="btn btn-info btn-xs" title="@lang('payroll.view')">
                                                <i class="fa fa-eye"></i>
                                            </a>

                                            <a href="{{ route('payroll.settings.pension-schemes.edit', $scheme) }}"
                                                class="btn btn-warning btn-xs" title="@lang('payroll.edit')">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            <a href="{{ route('payroll.settings.pension-schemes.toggle-status', $scheme) }}"
                                                class="btn btn-{{ $scheme->is_active ? 'danger' : 'success' }} btn-xs"
                                                title="@lang($scheme->is_active ? 'payroll.deactivate' : 'payroll.activate')" onclick="return confirm('@lang($scheme->is_active ? 'payroll.deactivate_this_scheme' : 'payroll.activate_this_scheme')')">
                                                <i class="fa fa-{{ $scheme->is_active ? 'ban' : 'check' }}"></i>
                                            </a>

                                            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal"
                                                data-target="#calculateModal" data-scheme-id="{{ $scheme->id }}"
                                                data-scheme-name="{{ $scheme->name }}" title="@lang('payroll.calculate_contribution')">
                                                <i class="fa fa-calculator"></i>
                                            </button>

                                            @if ($scheme->employee_payrolls_count == 0)
                                                <form method="POST"
                                                    action="{{ route('payroll.settings.pension-schemes.delete', $scheme) }}"
                                                    style="display: inline-block;"
                                                    onsubmit="return confirm('@lang('payroll.delete_this_pension_scheme')')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs"
                                                        title="@lang('payroll.delete')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <p class="text-muted">@lang('payroll.no_pension_schemes_found')</p>
                                        <a href="{{ route('payroll.settings.pension-schemes.create') }}"
                                            class="btn btn-success">
                                            <i class="fa fa-plus"></i> @lang('payroll.create_first_scheme')
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pensionSchemes->hasPages())
                    <div class="text-center">
                        {{ $pensionSchemes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Calculate Contribution Modal -->
    <div class="modal fade" id="calculateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('payroll.calculate_pension_contribution')</h4>
                </div>
                <div class="modal-body">
                    <form id="calculateForm">
                        <div class="form-group">
                            <label>@lang('payroll.pension_scheme')</label>
                            <input type="text" id="schemeName" class="form-control" readonly>
                            <input type="hidden" id="schemeId">
                        </div>
                        <div class="form-group">
                            <label>@lang('payroll.pensionable_pay') <span class="text-danger">*</span></label>
                            <input type="number" id="pensionablePay" class="form-control" step="0.01" min="0"
                                required>
                        </div>
                        <div id="calculationResults" style="display: none;">
                            <hr>
                            <h5>@lang('payroll.calculation_results')</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>@lang('payroll.employee_contribution'):</strong></td>
                                    <td id="employeeContribution" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('payroll.employer_contribution'):</strong></td>
                                    <td id="employerContribution" class="text-right"></td>
                                </tr>
                                <tr class="success">
                                    <td><strong>@lang('payroll.total_contribution'):</strong></td>
                                    <td id="totalContribution" class="text-right"></td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('payroll.close')</button>
                    <button type="button" class="btn btn-primary"
                        onclick="calculateContribution()">@lang('payroll.calculate')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Handle calculate modal
            $('#calculateModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var schemeId = button.data('scheme-id');
                var schemeName = button.data('scheme-name');

                $('#schemeId').val(schemeId);
                $('#schemeName').val(schemeName);
                $('#pensionablePay').val('');
                $('#calculationResults').hide();
            });
        });

        function calculateContribution() {
            var schemeId = $('#schemeId').val();
            var pensionablePay = $('#pensionablePay').val();

            if (!pensionablePay || pensionablePay <= 0) {
                alert('@lang('payroll.please_enter_valid_pensionable_pay')');
                return;
            }

            $.ajax({
                url: '/payroll/settings/pension-schemes/' + schemeId + '/calculate-contribution',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    pensionable_pay: pensionablePay
                },
                success: function(response) {
                    $('#employeeContribution').text(response.employee_contribution.toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'KES',
                        minimumFractionDigits: 2
                    }) + ' (' + response.employee_rate + '%)');

                    $('#employerContribution').text(response.employer_contribution.toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'KES',
                        minimumFractionDigits: 2
                    }) + ' (' + response.employer_rate + '%)');

                    $('#totalContribution').text(response.total_contribution.toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'KES',
                        minimumFractionDigits: 2
                    }));

                    $('#calculationResults').show();
                },
                error: function(xhr) {
                    alert('@lang('payroll.error_calculating_contribution')');
                }
            });
        }
    </script>
@endsection
