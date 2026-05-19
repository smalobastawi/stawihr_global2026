@extends('admin.master')
@section('content')
@section('title')
    Banks Management
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
            <a href="{{ route('banks.import') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Bulk Upload Banks
            </a>
            <a href="{{ route('bank-branches.import') }}"
                class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Bulk Upload Locations
            </a>
            <a href="{{ route('banks.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New Bank
            </a>
            <a href="{{ route('bank-branches.create') }}"
                class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New Location
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
                            <table id="banksTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Bank Name</th>
                                        <th>Bank Code</th>
                                        <th>Date Created</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($banks as $key => $bank)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $bank->name }}</td>
                                            <td>{{ $bank->bank_code }}</td>
                                            <td>{{ $bank->created_at->format('d-m-Y') }}</td>
                                            <td style="width: 100px;">
                                                <a href="{{ route('banks.edit', $bank->id) }}"
                                                    class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <form action="{{ route('banks.destroy', $bank->id) }}" method="POST"
                                                    style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger btn-xs deleteBtn btnColor"
                                                        onclick="return confirm('Are you sure you want to delete this bank?')">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $banks->links() }}
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
        $('#banksTable').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false
        });
    });
</script>
@endsection
