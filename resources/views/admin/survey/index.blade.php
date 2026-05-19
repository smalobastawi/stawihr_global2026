@extends('admin.master')
@section('title', 'Survey List')
@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        @yield('title')
                    </li>
                </ol>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <a href="{{ route('survey.create') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Add Survey
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
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered">
                                    <thead class="tr_header">
                                        <tr>
                                            <th>S/L</th>
                                            <th>Survey</th>
                                            <th>Targeted Gender</th>
                                            <th>Targeted Departments</th>
                                            <th>Targeted Regions</th>
                                            <th>Targeted Locations</th>
                                            <th>Responses</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {!! $sl = null !!}
                                        @foreach ($data as $value)
                                            <tr class="{!! $value->id !!}">
                                                <td style="width: 100px;">{!! ++$sl !!}</td>
                                                <td>
                                                    <a href="https://docs.google.com/forms/d/{{ $value->google_form_id }}/edit"
                                                        target="_blank">
                                                        {{ $value->title }}
                                                    </a>
                                                </td>

                                                <td>
                                                    {{ Gender::getName($value->target_gender) }}
                                                </td>

                                                <td>
                                                    @if ($value->departments->count() > 0)
                                                        @foreach ($value->departments as $department)
                                                            <span class="badge badge-info">
                                                                {{ $department->department_name }}
                                                            </span>
                                                            @if (!$loop->last)
                                                                &nbsp;
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">No departments selected</span>
                                                    @endif
                                                </td>



                                                <td>
                                                    @if ($value->regions->count() > 0)
                                                        @foreach ($value->regions as $region)
                                                            <span class="badge badge-secondary">
                                                                {{ $region->name }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span an class="text-muted">All regions</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @php
                                                        // Get all locations - both directly selected and from regions
                                                        $allBranches = $value->getAllBranchesAttribute();
                                                    @endphp

                                                    @if ($allBranches->count() > 0)
                                                        @foreach ($allBranches as $location)
                                                            <span class="badge badge-light">
                                                                {{ $location->location_name }}
                                                                @if ($value->locations->contains($location))
                                                                    <i class="fa fa-check text-success"
                                                                        title="Directly selected"></i>
                                                                @else
                                                                    <i class="fa fa-globe text-info"
                                                                        title="From region selection"></i>
                                                                @endif
                                                            </span>
                                                            @if (!$loop->last)
                                                                &nbsp;
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">All locations</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <a href="https://docs.google.com/forms/d/{{ $value->google_form_id }}/edit#responses"
                                                        class="btn btn-primary btn-xs btnColor" target="_blank">
                                                        <i class="fa fa-eye" aria-hidden="true"></i> View Responses
                                                    </a>
                                                    <a href="https://docs.google.com/forms/d/{{ $value->google_form_id }}/edit"
                                                        class="btn btn-success btn-xs btnColor" target="_blank">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true">Edit
                                                            Questions</i>
                                                    </a>
                                                </td>

                                                <td style="width: 100px;">
                                                    @can('survey.edit')
                                                        <a href="{{ route('survey.edit', $value->id) }}"
                                                            class="btn btn-success btn-xs btnColor">
                                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                        </a>
                                                    @endcan
                                                    @can('survey.delete')
                                                        <a href="{!! route('survey.delete', $value->id) !!}"
                                                            class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                            data-token="{!! csrf_token() !!}"
                                                            data-id="{!! $value->id !!}"><i class="fa fa-trash-o"
                                                                aria-hidden="true"></i>
                                                        </a>
                                                    @endcan
                                                    <a href="{{ route('survey.targeted-employees', $value->id) }}"
                                                        class="btn btn-info btn-xs btnColor"
                                                        title="View Targeted Employees">
                                                        <i class="fa fa-users" aria-hidden="true"></i>
                                                    </a>
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

@endsection
