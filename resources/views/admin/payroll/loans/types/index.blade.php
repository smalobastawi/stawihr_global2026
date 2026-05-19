@extends('admin.master')

@section('title')
    Loan Types
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                @can('loans.types.create')
                    <a href="{{ route('loans.types.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Add Loan Type</a>
                @endcan
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
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Max Amount</th>
                                            <th>Interest Rate</th>
                                            <th>Max Duration</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $type)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $type->name }}</td>
                                                <td>{{ $type->description ?? 'N/A' }}</td>
                                                <td>{{ $type->max_amount ? number_format($type->max_amount, 2) : 'N/A' }}</td>
                                                <td>{{ $type->interest_rate }}%</td>
                                                <td>{{ $type->max_duration_months }} months</td>
                                                <td>
                                                    @if ($type->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('loans.types.edit', $type->id) }}" class="btn btn-success btn-xs" title="Edit"><i class="fa fa-pencil-square-o"></i></a>
                                                    <a href="javascript:void(0)" onclick="deleteLoanType({{ $type->id }})" class="btn btn-danger btn-xs" title="Delete"><i class="fa fa-trash-o"></i></a>
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

    <script type="text/javascript">
        function deleteLoanType(id) {
            if (confirm('Are you sure you want to delete this loan type?')) {
                $.ajax({
                    url: '{{ url("payroll/loans/types") }}/' + id + '/delete',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response == 'success') {
                            location.reload();
                        } else {
                            alert('Error deleting loan type.');
                        }
                    }
                });
            }
        }
    </script>
@endsection
