@extends('admin.master')

@section('title')
   Activity Logs report
@endsection
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
       
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="card-body">
                   

                    <div class="table-responsive">
                        <table id="example1" class="table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Date</th>
                                    <th>Affected</th>

                                    <th>Action</th>
                                    <th>Changes</th>
                                    <th>Performed By</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if ($data)
                                    @foreach ($data as $log)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $log->created_at }} </td>
                                            <td>
                                                @if($log->subject_type === 'App\Models\Employee' && $log->subject)
                                                    <a href="{{ route('employee.show', $log->subject->employee_id) }}"
                                                       class="text-primary"
                                                       title="View employee profile">
                                                        {{ $log->subject->full_name }}
                                                    </a>
                                                @else
                                                    {{ class_basename($log->subject_type) }}
                                                @endif
                                            </td>
                                            <td>{{ $log->event }} </td>
                                            <td>
                                                @if (!empty($log->old))
                                                    @foreach ($log->old as $key => $value)
                                                        @if ($value !== ($log->attributes->$key ?? null))
                                                            @unless (in_array($key, ['updated_at', 'created_at', 'uuid']))
                                                                <strong>{{ $key }}:</strong>
                                                                <del>{{ $value }}</del> ->
                                                                {{ $log->attributes->$key ?? 'N/A' }} <br>
                                                            @endunless
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->causer)
                                                    @if ($log->causer->first_name)
                                                        {{ $log->causer->first_name }}
                                                    @endif
                                                    @if ($log->causer->last_name)
                                                        {{ ' ' . $log->causer->last_name }}
                                                    @endif
                                                    @if ($log->causer->other_names)
                                                        {{ ' ' . $log->causer->other_names }}
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                @endif
                            </tbody>

                        </table>
                    </div>
                </div><!-- /.card-body -->
               
            </div>
        </div>
    </div>
</div>
@endsection
