{{-- resources/views/bank_branches/index.blade.php --}}

@extends('admin.master')
@section('content')
@section('title')
    Bank Locations Management
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('bank-branches.index') }}">Bank Locations</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <a href="{{ route('bank-branches.create') }}"
                class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New Location
            </a>
            <a href="{{ route('banks.index') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Banks Management
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

                        <div class="table-responsive">
                            <table id="branchesTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Bank Name</th>
                                        <th>Location Code</th>
                                        <th>Location Name</th>
                                        <th>SWIFT Code</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($locations as $key => $location)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $location->bank->name }}</td>
                                            <td>{{ $location->branch_code }}</td>
                                            <td>{{ $location->location_name }}</td>
                                            <td>{{ $location->swift_code ?? 'N/A' }}</td>
                                            <td>{{ $location->phone ?? 'N/A' }}</td>
                                            <td>
                                                @if ($location->status)
                                                    <span class="label label-success">Active</span>
                                                @else
                                                    <span class="label label-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td style="width: 120px;">
                                                <a href="{{ route('bank-branches.show', $location->id) }}"
                                                    class="btn btn-info btn-xs btnColor" title="View Details">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('bank-branches.edit', $location->id) }}"
                                                    class="btn btn-success btn-xs btnColor" title="Edit">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <form action="{{ route('bank-branches.destroy', $location->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger btn-xs deleteBtn btnColor"
                                                        onclick="return confirm('Are you sure you want to delete this location?')"
                                                        title="Delete">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $locations->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#branchesTable').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "columnDefs": [{
                    "orderable": false,
                    "targets": [7]
                } // Disable sorting for action column
            ]
        });
    });
</script>
@endsection
