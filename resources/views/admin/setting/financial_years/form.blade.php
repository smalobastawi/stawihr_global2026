@extends('admin.master')

@section('title')
    {{ isset($editModeData) ? 'Edit Financial Year' : 'Create Financial Year' }}
@endsection

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <div class="row bg-title">
        <div class="col-md-8">
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('dashboard') }}">
                        <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                    </a>
                </li>
                <li class="active">@yield('title')</li>
            </ol>
        </div>

        <div class="col-md-4 text-right">
            <a href="{{ route('financial_year.index') }}" class="btn btn-success">
                <i class="fa fa-list-ul"></i> View Years
            </a>
        </div>
    </div>

    <!-- Panel -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <i class="mdi mdi-clipboard-text"></i> @yield('title')
        </div>

        <div class="panel-body">

            <!-- FORM -->
            <form action="{{ isset($editModeData) 
                ? route('financial_year.update', $editModeData->id) 
                : route('financial_year.store') }}" 
                method="POST" 
                class="form-horizontal">

                @csrf
                @if(isset($editModeData)) @method('PUT') @endif

                <!-- Alerts -->
                <div class="col-md-8 col-md-offset-2">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                </div>

                <!-- Year Name -->
                <div class="form-group">
                    <label class="col-md-4 control-label">Year Name *</label>
                    <div class="col-md-6">
                        <input type="text" name="name"
                               value="{{ old('name', $editModeData->name ?? '') }}"
                               class="form-control" placeholder="E.g FY_2025">
                    </div>
                </div>

                <!-- Start Date -->
                <div class="form-group">
                    <label class="col-md-4 control-label">Start Date *</label>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="start_date"
                                   value="{{ old('start_date', isset($editModeData) ? \Carbon\Carbon::parse($editModeData->start_date)->format('d/m/Y') : '') }}"
                                   class="form-control dateField start_date" readonly>
                        </div>
                    </div>
                </div>

                <!-- End Date -->
                <div class="form-group">
                    <label class="col-md-4 control-label">End Date *</label>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="end_date"
                                   value="{{ old('end_date', isset($editModeData) ? \Carbon\Carbon::parse($editModeData->end_date)->format('d/m/Y') : '') }}"
                                   class="form-control dateField end_date" readonly>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label class="col-md-4 control-label">Status *</label>
                    <div class="col-md-6">
                        <select name="status" class="form-control select2">
                            <option value="1" {{ old('status', $editModeData->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                            <option value="2" {{ old('status', $editModeData->status ?? '') == 2 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Submit -->
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-info">
                            {{ isset($editModeData) ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

<!-- JS -->
<script>
$(function () {

    $('.start_date, .end_date').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        clearBtn: true
    });

});
</script>