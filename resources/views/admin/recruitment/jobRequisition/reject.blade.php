@extends('admin.master')

@section('title', trans('job_requisition.reject_requisition'))

@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li><a href="{{ route('jobRequisition.index') }}">@lang('job_requisition.job_requisition_list')</a></li>
                    <li><a
                            href="{{ route('jobRequisition.show', $result->job_requisition_id) }}">{{ $result->requisition_number }}</a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('jobRequisition.show', $result->job_requisition_id) }}"
                    class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i> @lang('common.back')
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <i class="mdi mdi-close-circle fa-fw"></i>
                        @lang('job_requisition.reject_requisition'): {{ $result->requisition_number }}
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <!-- Requisition Summary -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4><i class="fa fa-info-circle"></i> @lang('job_requisition.requisition_summary')</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <td><strong>@lang('job_requisition.position_title')</strong></td>
                                            <td>{{ $result->position_title }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('job_requisition.number_of_positions')</strong></td>
                                            <td>{{ $result->number_of_positions }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('job_requisition.job_type')</strong></td>
                                            <td>{{ ucfirst($result->job_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('job_requisition.employment_type')</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $result->employment_type)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('job_requisition.urgency_level')</strong></td>
                                            <td><span
                                                    class="label {{ $result->urgency_class }}">{{ $result->urgency_label }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('job_requisition.required_by_date')</strong></td>
                                            <td>{{ date('d M Y', strtotime($result->required_by_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('job_requisition.requested_by')</strong></td>
                                            <td>
                                                @isset($result->requestedBy)
                                                    {{ $result->requestedBy->first_name }}
                                                    {{ $result->requestedBy->last_name }}
                                                    <br><small
                                                        class="text-muted">{{ date('d M Y H:i', strtotime($result->created_at)) }}</small>
                                                @endisset
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <!-- Rejection Form -->
                            <form class="form-horizontal" id="rejectForm" method="POST" action="{{ route('jobRequisition.reject', $result->job_requisition_id) }}"> @csrf

                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-8">
                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span></button>
                                                @foreach ($errors->all() as $error)
                                                    <strong>{!! $error !!}</strong><br>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">
                                                @lang('job_requisition.rejection_reason')<span class="validateRq">*</span>
                                            </label>
                                            <div class="col-md-9">
                                                <textarea name="rejection_reason">{{ Request::old('rejection_reason') }}</textarea>
                                                <span class="help-block">
                                                    @lang('job_requisition.rejection_reason_help')
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-9 col-md-offset-3">
                                                    <button type="submit" class="btn btn-danger btn_style">
                                                        <i class="fa fa-times"></i> @lang('common.reject')
                                                    </button>
                                                    <a href="{{ route('jobRequisition.show', $result->job_requisition_id) }}"
                                                        class="btn btn-default">
                                                        @lang('common.cancel')
                                                    </a>
                                                    <a href="{{ route('jobRequisition.approve.form', $result->job_requisition_id) }}"
                                                        class="btn btn-success">
                                                        <i class="fa fa-check"></i> @lang('common.approve')
                                                    </a>
                                                </div>
                                            </div>
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
    </div>

@endsection

@section('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            // Auto-resize textarea
            $('textarea').each(function() {
                this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
            }).on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Form validation
            $('#rejectForm').submit(function(e) {
                if (confirm('@lang('job_requisition.confirm_reject')')) {
                    return true;
                }
                return false;
            });
        });
    </script>
@endsection

