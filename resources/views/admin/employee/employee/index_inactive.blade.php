@extends('admin.master')
@section('content')

@section('title', getPageTitle() . ' | ' . config('app.name'))


<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"> <a href="{{ url('/') }}"> Home</a></li>
                @foreach (urlTree() as $item)
                <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ol>
        </div>

        <div class="">
            <a href="{{ route('employee.create') }}"
                class="btn btn-success pull-right m-l-20  waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('employee.add_employee')</a>
        </div>
        <div>
            <a href="{{ route('employee.importView') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i>Bulk Upload</a>
            <a href="{{ route('employee.active') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i>Active User Report</a>
                    <a href="{{ route('employee.index') }}"
                    class="btn btn-primary pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                        class="fa fa-plus-circle" aria-hidden="true"></i>Active</a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">

                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session('errors'))
                        <div class="alert alert-danger">
                            <strong>There were some errors during the import:</strong>
                            <ul>
                                @foreach (session('errors') as $sheet => $rows)
                                @foreach ($rows as $row => $errors)
                                <li><strong>Row {{ $row + 1 }} ({{ $sheet }}):</strong>
                                    <ul>
                                        @foreach ($errors as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </li>
                                @endforeach
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success')
                                }}</strong>
                        </div>
                        @endif
                        @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error')
                                }}</strong>
                        </div>
                        @endif

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="department_id">@lang('employee.department')</label>
                                    <select name="department_id" class="form-control department_id  select2"
                                        onchange="getData(1)" id="department_id" required>
                                        <option value="">--- @lang('employee.select_department') ---</option>
                                        @foreach ($departmentList as $value)
                                        <option value="{{ $value->department_id }}" @if ($value->department_id ==
                                            old('department_id')) {{ 'selected' }} @endif>
                                            {{ $value->department_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="designation_id">@lang('employee.designation')</label>
                                    <select name="designation_id" class="form-control designation_id select2"
                                        onchange="getData(1)" id="designation_id" required>
                                        <option value="">--- @lang('employee.select_designation') ---</option>
                                        @foreach ($designationList as $value)
                                        <option value="{{ $value->designation_id }}" @if ($value->designation_id ==
                                            old('designation_id')) {{ 'selected' }} @endif>
                                            {{ $value->designation_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="role_id">@lang('employee.role')</label>
                                    <select name="role_id" class="form-control role_id  select2" onchange="getData(1)"
                                        id="role_id" required>
                                        <option value="">--- @lang('common.please_select') ---</option>
                                        @foreach ($roleList as $value)
                                        <option value="{{ $value->role_id }}" @if ($value->role_id == old('role_id')) {{
                                            'selected' }} @endif>
                                            {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <br>
                        <div class="data">
                            @include('admin.employee.employee.pagination')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(function() {
        $('.data').on('click', '.pagination a', function(e) {
            getData($(this).attr('href').split('page=')[1]);
            e.preventDefault();
        });
    });

    function getData(page) {
        var department_id = $('#department_id').val();
        var designation_id = $('#designation_id').val();
        var role_id = $('#role_id').val();
        var employee_name = '';

        $.ajax({
            url: '?page=' + page + "&department_id=" + department_id + "&designation_id=" + designation_id +
                "&role_id=" + role_id + "&employee_name=" + employee_name,
            datatype: "html",
        }).done(function(data) {
            $('.data').html(data);
            $("html, body").animate({
                scrollTop: 0
            }, 800);
        }).fail(function() {
            alert('No response from server');
        });
    }

    jQuery(function() {
        $(document).ready(function() {
            $('.select2').select2();
        });
    });
</script>


<style>
    .bdColor {
        color: #8d9ea7;
    }

    #custom-search-input .search-query {
        padding-right: 3px;
        padding-right: 4px \9;
        padding-left: 3px;
        padding-left: 4px \9;
        /* IE7-8 doesn't have border-radius, so don't indent the padding */

        margin-bottom: 0;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }

    #custom-search-input button {
        border: 0;
        background: none;
        /** belows styles are working good */
        padding: 2px 5px;
        margin-top: 2px;
        position: relative;
        left: -28px;
        /* IE7-8 doesn't have border-radius, so don't indent the padding */
        margin-bottom: 0;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        color: #ddd;
    }

    .search-query:focus+button {
        z-index: 3;
    }

    .panel-blue a,
    .panel-info a {
        color: black;
    }
</style>
@endsection