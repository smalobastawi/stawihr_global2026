@extends('admin.master')

@section('content')

@section('title')
    @if (isset($editModeData))
        Edit Company Permissions
    @else
        Create Company Permissions
    @endif
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-6">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}">
                        <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                    </a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-4 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('company.permissions.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i>View Company Permissions
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            <form action="{{ route('company.permissions.update', $editModeData['user_id']) }}" method="POST"
                                enctype="multipart/form-data" id="companyForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('company.permissions.store') }}" method="POST"
                                enctype="multipart/form-data" id="companyForm">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-8">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (session()->has('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                                            <strong>{{ session()->get('success') }}</strong>
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i class="glyphicon glyphicon-remove"></i>&nbsp;
                                            <strong>{{ session()->get('error') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <!-- User Select -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">User<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="user_id" id="user_id"
                                                class="form-control required select2" onchange="getPermissions()">
                                                <option value="">Select User</option>
                                                @foreach ($employees as $employee)
                                                    @if ($employee->user)
                                                        <option value="{{ $employee->user->id }}"
                                                            {{ (isset($editModeData) && $editModeData['user_id'] == $employee->user->id) || old('user_id') == $employee->user->id ? 'selected' : '' }}>
                                                            {{ $employee->first_name . ' ' . $employee->last_name . ' (' . $employee->email . ')' }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Company Select -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Company<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="company_id" id="company_id"
                                                class="form-control required select2" onchange="getPermissions()">
                                                <option value="">Select Company</option>
                                                @foreach ($companies as $company)
                                                    <option value="{{ $company->id }}"
                                                        {{ (isset($editModeData) && $editModeData['company_id'] == $company->id) || old('company_id') == $company->id ? 'selected' : '' }}>
                                                        {{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <div class="ShowPermissions" id="ShowPermissions">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-offset-4 col-md-8">
                                            <button type="submit" id="formSubmit" disabled="disabled" class="btn btn-info btn_style">
                                                @if (isset($editModeData))
                                                    <i class="fa fa-pencil"></i> @lang('common.update')
                                                @else
                                                    <i class="fa fa-check"></i> @lang('common.save')
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')
<script>
    $(document).on('change', '[data-menu]', function(event) {
        if (this.checked == false) {
            var getMenuId = $(this).attr('data-menu');
            $('[data-formenu="' + getMenuId + '"]').prop('checked', false);
        }
    });

    $(document).on('change', '[data-formenu]', function(event) {
        if (this.checked == true) {
            var getMenuId = $(this).attr('data-formenu');
            $('[data-menu="' + getMenuId + '"]').prop('checked', true);
        }
    });

    $(document).on("click", '.checkAll', function(event) {
        if (this.checked) {
            $('.inputCheckbox').each(function() {
                this.checked = true;
            });
        } else {
            $('.inputCheckbox').each(function() {
                this.checked = false;
            });
        }
    });

    function getPermissions() {
        var user_id = $('#user_id').val();
        var company_id = $('#company_id').val();

        if (user_id != '' && company_id != '') {
            $('body').find('#formSubmit').attr('disabled', false);
        } else {
            $('body').find('#formSubmit').attr('disabled', true);
            return false;
        }

        var action = "{{ route('company.permissions.get') }}";
        $.ajax({
            type: 'POST',
            url: action,
            data: {
                user_id: user_id,
                company_id: company_id,
                '_token': $('input[name=_token]').val()
            },
            success: function(result) {
                $('.ShowPermissions').html(result);
            }
        });
    }

    // Module level - When a module is checked/unchecked
    $(document).on("click", '.menucls', function(event) {
        var menuId = $(this).attr('id');
        if (this.checked) {
            $('.' + menuId).each(function() {
                this.checked = true;
            });
        } else {
            $('.' + menuId).each(function() {
                this.checked = false;
            });
        }
    });

    // Menu level - When a menu is checked/unchecked
    $(document).on("click", '.pmgcls', function(event) {
        var groupId = $(this).data('group-id');
        if (this.checked) {
            $('.pmgcls__' + groupId).each(function() {
                this.checked = true;
            });
        } else {
            $('.pmgcls__' + groupId).each(function() {
                this.checked = false;
            });
        }
    });

    // Section level - When a section is checked/unchecked
    $(document).on("click", '.sub_section_cls', function(event) {
        var sectionId = $(this).attr('id').replace('subSectionCls__', '');
        if (this.checked) {
            $('.subSectionCls__' + sectionId).each(function() {
                this.checked = true;
            });
        } else {
            $('.subSectionCls__' + sectionId).each(function() {
                this.checked = false;
            });
        }
    });

    // ActionType level - When an action type is checked/unchecked
    $(document).on("click", '.action_type_cls', function(event) {
        var actionTypeId = $(this).attr('id').replace('actionCls__', '');
        if (this.checked) {
            $('.actionCls__' + actionTypeId).each(function() {
                this.checked = true;
            });
        } else {
            $('.actionCls__' + actionTypeId).each(function() {
                this.checked = false;
            });
        }
    });

    // Auto-load permissions if in edit mode
    @if (isset($editModeData))
        $(document).ready(function() {
            getPermissions();
        });
    @endif
</script>
@endsection
