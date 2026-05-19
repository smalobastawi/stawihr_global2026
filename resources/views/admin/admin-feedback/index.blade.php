@extends('admin.master')
@section('content')
@section('title')
    Employee Feedback
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
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Employee</th>
                                        <th>Category</th>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>HR Response</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($data as $value)
                                        <tr class="{!! $value->id !!}">
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td> {!! $value->employee->first_name !!}&nbsp;{!! $value->employee->middle_name !!}&nbsp;{!! $value->employee->last_name !!}
                                            </td>
                                            <td>{{ optional($value->category)->name ?? 'N/A' }}</td>

                                            <td>{!! $value->title !!}</td>
                                            <td>{!! $value->content !!}</td>
                                            <td>
                                                @if ($value->response)
                                                    {!! $value->response->content !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($value->status == FeedbackStatus::REVIEWED)
                                                    <span style="color: green;">{!! FeedbackStatus::getName($value->status) !!}</span>
                                                @elseif($value->response)
                                                    <span style="color: green;">{!! FeedbackStatus::getName(FeedbackStatus::REVIEWED) !!}</span>
                                                @else
                                                    {!! FeedbackStatus::getName($value->status) !!}
                                                @endif
                                            </td>
                                            <td style="width: 200px; white-space: nowrap;">
                                                <!-- Increased width & prevent wrapping -->
                                                @if ($value->deleted_at)
                                                    <a href="{!! route('feedback.feedback.restore', $value->id) !!}"
                                                        data-token="{!! csrf_token() !!}"
                                                        data-id="{!! $value->id !!}"
                                                        class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                        style="display: inline-block; margin-right: 5px;">
                                                        <!-- Force inline & add spacing -->
                                                        <i class="fa fa-undo" aria-hidden="true"></i> Undo
                                                    </a>
                                                @else
                                                    <button class="btn btn-success btn-xs btnColor viewBtn"
                                                        data-id="{!! $value->id !!}" data-toggle="modal"
                                                        data-target="#viewModal"
                                                        style="display: inline-block; margin-right: 5px;">
                                                        <!-- Force inline & add spacing -->
                                                        <i class="fa fa-eye" aria-hidden="true"></i> View
                                                    </button>

                                                    @if ($value->status != 1)
                                                        <!-- Only show "Review" if status is not Approved -->
                                                        @can('employee.feedback.respond')
                                                            <a href="{!! route('employee.feedback.respond', $value->id) !!}"
                                                                class="btn btn-success btn-xs btnColor"
                                                                style="display: inline-block; margin-right: 5px;">
                                                                <!-- Force inline & add spacing -->
                                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                                Review
                                                            </a>
                                                        @endcan
                                                    @endif
                                                    @can('employee.feedback.delete')
                                                        <a href="{!! route('employee.feedback.delete', $value->id) !!}"
                                                            data-token="{!! csrf_token() !!}"
                                                            data-id="{!! $value->id !!}"
                                                            class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                            style="display: inline-block;"> <!-- Force inline -->
                                                            <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                                        </a>
                                                    @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="viewModalLabel">Feedback Details</h4>
            </div>
            <div class="modal-body">
                <p><strong>Category:</strong> <span id="modalCategory"></span></p>
                <p><strong>Title:</strong> <span id="modalTitle"></span></p>
                <p><strong>Content:</strong> <span id="modalContent"></span></p>
                <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                <p><strong>Response:</strong> <span id="modalResponseContent"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.viewBtn').on('click', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('ess.feedback.show', ['id' => ':id']) }}".replace(':id', id),
                method: 'GET',
                success: function(data) {
                    $('#modalCategory').text(data.category.name);
                    $('#modalTitle').text(data.title);
                    $('#modalContent').text(data.content);
                    $('#modalStatus').text(data.status);
                    var tempDiv = $('<div>').html(data.response.content);
                    $('#modalResponseContent').text(tempDiv.text());
                }
            });
        });
    });
</script>
@endsection
