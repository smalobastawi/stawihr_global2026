@extends('admin.master')

@section('title')
    @if (isset($region))
        @lang('region.edit_region')
    @else
        @lang('region.add_region')
    @endif
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li class="breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                    </li>
                    <li><a href="{{ route('region.index') }}">@lang('region.region_list')</a></li>
                    <li class="active">@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-map-marker-radius fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <form method="POST"
                                action="{{ isset($region) ? route('region.update', $region->id) : route('region.store') }}"
                                class="form-horizontal form-bordered">
                                @csrf
                                @if (isset($region))
                                    @method('PUT')
                                @endif

                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-8 col-md-offset-2">
                                            <!-- Region Name Field -->
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('region.name')<span
                                                        class="text-danger">*</span></label>
                                                <div class="col-md-9">
                                                    <input type="text" name="name"
                                                        value="{{ old('name', $region->name ?? '') }}"
                                                        class="form-control input-lg" placeholder="@lang('region.name_placeholder')"
                                                        required>
                                                    <small class="form-text text-muted">@lang('region.name_help_text')</small>
                                                </div>
                                            </div>

                                            <!-- Regional Head/Manager Field -->
                                            <div class="form-group">
                                                <label class="control-label col-md-3">Regional Head</label>
                                                <div class="col-md-9">
                                                    <select name="manager_id" class="form-control select2-single"
                                                        data-placeholder="Select regional head">
                                                        <option value=""></option>
                                                        @foreach ($employees as $id => $name)
                                                            <option value="{{ $id }}"
                                                                @if (isset($region) && $region->manager_id == $id) selected @endif>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Footer with Submit Button -->
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-info btn-lg">
                                                <i class="fa {{ isset($region) ? 'fa-refresh' : 'fa-save' }}"></i>
                                                {{ isset($region) ? __('common.update') : __('common.save') }}
                                            </button>
                                            <a href="{{ route('region.index') }}" class="btn btn-default btn-lg">
                                                <i class="fa fa-times"></i> @lang('common.cancel')
                                            </a>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "@lang('region.select_approvers_placeholder')",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });

            $('.select2-single').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });

            // Add some styling to the select2 dropdown
            $('.select2-container--default .select2-selection--multiple').css({
                'min-height': '38px',
                'border-radius': '4px'
            });
        });
    </script>
    <style>
        /* Custom Form Styling */
        .form-bordered .form-group {
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .form-bordered .form-group:last-child {
            border-bottom: none;
        }

        .input-lg {
            height: 46px;
            padding: 10px 16px;
        }

        .btn-lg {
            padding: 10px 30px;
            font-size: 16px;
        }

        .panel-heading i {
            margin-right: 10px;
        }

        .select2-selection__choice {
            background-color: #3498db !important;
            color: white !important;
            border: none !important;
        }

        .select2-selection__choice__remove {
            color: white !important;
        }
    </style>
@endsection
