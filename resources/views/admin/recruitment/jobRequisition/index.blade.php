@extends('admin.master')

@section('title', trans('job_requisition.job_requisition_list'))

@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <a href="{{ route('jobRequisition.create') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('job_requisition.create_new_requisition')
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
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

                            <!-- Filters -->
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="GET" action="{{ route('jobRequisition.index') }}" class="form-inline">
                                        <div class="form-group">
                                            <label for="status">@lang('common.status'):</label>
                                            <select name="status" id="status" class="form-control">
                                                @foreach ($statusOptions as $key => $label)
                                                    <option value="{{ $key }}"
                                                        {{ isset($filters['status']) && $filters['status'] == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="urgency_level">@lang('job_requisition.urgency_level'):</label>
                                            <select name="urgency_level" id="urgency_level" class="form-control">
                                                @foreach ($urgencyOptions as $key => $label)
                                                    <option value="{{ $key }}"
                                                        {{ isset($filters['urgency_level']) && $filters['urgency_level'] == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="date_from">@lang('common.from_date'):</label>
                                            <input type="date" name="date_from" id="date_from" class="form-control"
                                                value="{{ $filters['date_from'] ?? '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="date_to">@lang('common.to_date'):</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control"
                                                value="{{ $filters['date_to'] ?? '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="search">@lang('common.search'):</label>
                                            <input type="text" name="search" id="search" class="form-control"
                                                placeholder="@lang('job_requisition.search_placeholder')" value="{{ $filters['search'] ?? '' }}">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-info">
                                                <i class="fa fa-search"></i> @lang('common.search')
                                            </button>
                                            <a href="{{ route('jobRequisition.index') }}" class="btn btn-default">
                                                @lang('common.reset')
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="myTable" class="table table-hover manage-u-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('job_requisition.requisition_number')</th>
                                            <th>@lang('job_requisition.position_title')</th>
                                            <th>@lang('job_requisition.urgency_level')</th>
                                            <th>@lang('job_requisition.number_of_positions')</th>
                                            <th>@lang('job_requisition.required_by')</th>
                                            <th>@lang('common.status')</th>
                                            <th>@lang('job_requisition.requested_by')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sl = null @endphp
                                        @foreach ($results as $value)
                                            <tr>
                                                <td style="width: 70px;">{!! ++$sl !!}</td>
                                                <td>
                                                    <strong>{!! $value->requisition_number !!}</strong>
                                                </td>
                                                <td>
                                                    {!! $value->position_title !!}
                                                </td>
                                                <td>
                                                    <span class="label {{ $value->urgency_class }}">
                                                        {{ $value->urgency_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $value->number_of_positions }}
                                                </td>
                                                <td>
                                                    {{ date('d M Y', strtotime($value->required_by_date)) }}
                                                    <br />
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($value->required_by_date)->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="label {{ $value->status_class }}">
                                                        {{ $value->status_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @isset($value->requestedBy)
                                                        {{ $value->requestedBy->first_name }}
                                                        {{ $value->requestedBy->last_name }}
                                                        <br />
                                                        <small class="text-muted">
                                                            {{ date('d M Y', strtotime($value->created_at)) }}
                                                        </small>
                                                    @endisset
                                                </td>
                                                <td style="width: 120px;">
                                                    <a title="@lang('common.view')"
                                                        href="{{ route('jobRequisition.show', $value->job_requisition_id) }}"
                                                        class="btn btn-info btn-xs btnColor">
                                                        <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                                                    </a>
                                                    @if ($value->canEdit())
                                                        <a href="{{ route('jobRequisition.edit', $value->job_requisition_id) }}"
                                                            class="btn btn-success btn-xs btnColor">
                                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="#" data-token="{!! csrf_token() !!}"
                                                            data-id="{!! $value->job_requisition_id !!}"
                                                            class="delete btn btn-danger btn-xs deleteBtn btnColor">
                                                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                    @if ($value->canSubmitForApproval())
                                                        <form method="POST"
                                                            action="{{ route('jobRequisition.submit', $value->job_requisition_id) }}"
                                                            style="display: inline-block;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-xs btnColor"
                                                                onclick="return confirm('@lang('job_requisition.confirm_submit')')">
                                                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if ($value->canApprove())
                                                        <a href="{{ route('jobRequisition.approve.form', $value->job_requisition_id) }}"
                                                            class="btn btn-success btn-xs btnColor">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="{{ route('jobRequisition.reject.form', $value->job_requisition_id) }}"
                                                            class="btn btn-danger btn-xs btnColor">
                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                    @if ($value->canConvertToJob())
                                                        <form method="POST"
                                                            action="{{ route('jobRequisition.convert', $value->job_requisition_id) }}"
                                                            style="display: inline-block;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary btn-xs btnColor"
                                                                onclick="return confirm('@lang('job_requisition.confirm_convert')')">
                                                                <i class="fa fa-briefcase" aria-hidden="true"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    {{ $results->links() }}
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('.deleteBtn').click(function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var token = $(this).data('token');

                if (confirm('@lang('common.confirm_delete')')) {
                    $.ajax({
                        url: '{{ url('recruitment/jobRequisition') }}' + '/' + id + '/delete',
                        type: 'DELETE',
                        data: {
                            '_token': token
                        },
                        success: function(data) {
                            if (data.status === 'success') {
                                location.reload();
                            } else {
                                alert(data.message || '@lang('common.error_occurred')');
                            }
                        },
                        error: function() {
                            alert('@lang('common.error_occurred')');
                        }
                    });
                }
            });
        });
    </script>
@endsection
