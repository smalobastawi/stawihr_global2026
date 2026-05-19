@extends('admin.master')
@section('title')
    Employee- Upload from excel
@endsection
@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div>
                <a href="{{ route('employee.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                        class="fa fa-folder-plus" aria-hidden="true"></i> Go to employee list</a>
            </div>
        </div>
        <h3 align="center" class="text-dark">Bulk Upload employees from excel file</h3>
        <br />
        @if (session('errors'))
            <div class="alert alert-danger">
                <strong>There were some errors during the import:</strong>
                <ul>
                    @foreach (session('errors') as $sheet => $rows)
                        @foreach ($rows as $row => $errors)
                            <li><strong>Row {{ $row + 1 }} ({{ $sheet }}):</strong>
                                <ul>

                                    <li>{{ $errors }}</li>

                                </ul>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <form method="post" enctype="multipart/form-data" action="{{ route('importUsers') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <table class="table">
                    <tr>
                        <td width="40%" align="right"><label>Select File for Upload</label></td>
                        <td width="30">
                            <input type="file" name="select_file" required />
                        </td>
                        <td width="30%" align="left">
                            <input type="submit" name="upload" class="btn btn-primary" value="Upload">
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" align="right"></td>
                        <td width="30"><span class="text-muted">Accepted formats: .xls, .xslx, .csv</span></td>
                        <td width="30%" align="left"></td>
                    </tr>
                </table>
            </div>
        </form>

        <div class="col-md-4">
            <a href="{{ $sample_file_link }}">
                <div class="btn btn-danger "> Download sample import file Here</div>
            </a>
        </div>
        <div class="col-md-4">

            <br>
            <div class="">
                <label>Before importing Ensure you have setup the following records</label>

                <ol>
                    <li>User roles and their permissions</li>
                    <li>Departments</li>
                    <li>Locations </li>
                    <li>Designation/Jot title</li>
                </ol>
            </div>

        </div>
        <div class="col-md-4">
            <label>Points to note</label>
            <ul>
                <li>Do not import the same file twice, once the file has been imported, check the employee list and correct
                    any error that may have occurred.</li>
            </ul>
        </div>

    </div>

@endsection
