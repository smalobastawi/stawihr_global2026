@extends('admin.master')

@section('title')
    Companies
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i>@lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-view-list fa-fw"></i>@yield('title')
                        <a href="{{ route('company.create') }}" class="btn btn-info btn-sm pull-right"><i
                                class="fa fa-plus"></i> Add Company</a>
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Domain</th>
                                            <th>Country</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($companies as $company)
                                            <tr>
                                                <td>{{ $company->id }}</td>
                                                <td>{{ $company->name }}</td>
                                                <td>{{ $company->domain }}</td>
                                                <td>{{ $company->country }}</td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $company->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($company->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a style="color: white;" href="{{ route('company.show', $company) }}"
                                                        class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>
                                                    <a href="{{ route('company.edit', $company) }}"
                                                        class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                                    <form action="{{ route('company.destroy', $company) }}" method="POST"
                                                        style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure?')"><i
                                                                class="fa fa-trash"></i> Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No companies found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $companies->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
