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
        @if (session('import_errors'))
            <div class="alert alert-danger">
                <strong>Data was partially imported due to errors:</strong>
                <ul>
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ session('warning') }}</strong>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ session('error') }}</strong>
            </div>
        @endif

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="row">
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
                    <p class="text-muted">Required employee name columns: <strong>first_name</strong>, <strong>last_name</strong>. <strong>middle_name</strong> is optional.</p>
                </div>

            </div>
            <div class="col-md-4">
                <label>Points to note</label>
                <ul>
                    <li>Do not import the same file twice, once the file has been imported, check the employee list and
                        correct any error that may have occurred.</li>
                </ul>
            </div>
            <br>
        </div>
        @can('importSupervisors')
            <div class="row">
                <form method="post" enctype="multipart/form-data" action="{{ route('importSupervisors') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <a href="{{ isset($sample_supervisor_file_link) ? $sample_supervisor_file_link : '#' }}">
                            <div class="btn btn-danger "> Download Supervisor sample import file Here</div>
                        </a>
                        <table class="table">
                            <tr>
                                <td width="40%" align="right"><label>Supervisor Upload:Select File for Upload</label></td>
                                <td width="30">
                                    <input type="file" name="select_file" required />
                                </td>
                                <td width="30%" align="left">
                                    <input type="submit" name="upload" class="btn btn-primary" value="Upload Supervisors">
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
            </div>
        @endcan

        @if ($type == 'contracts')
            <div class="row">
                <form method="post" enctype="multipart/form-data" action="{{ route('contractsImport') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <a href="{{ isset($sample_contracts_file_link) ? $sample_contracts_file_link : '#' }}">
                            <div class="btn btn-danger">
                                Download Staff Contracts sample import file Here
                            </div>
                        </a>
                        <table class="table">
                            <tr>
                                <td width="40%" align="right">
                                    <label>Staff Contracts Upload:Select File for Upload</label>
                                </td>
                                <td width="30">
                                    <input type="file" name="select_file" required />
                                </td>
                                <td width="30%" align="left">
                                    <input type="submit" name="upload" class="btn btn-primary"
                                        value="Upload Staff Contracts">
                                </td>
                            </tr>
                            <tr>
                                <td width="40%" align="right"></td>
                                <td width="30">
                                    <span class="text-muted">
                                        Accepted formats: .xls, .xslx, .csv
                                    </span>
                                </td>
                                <td width="30%" align="left"></td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>
        @endif


    </div>

@endsection
