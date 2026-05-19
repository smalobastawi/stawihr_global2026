@extends('admin.master')
@section('title')
    @lang('region.region_list')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <a href="{{ route('region.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('region.add_region')
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @include('admin.partials.alert')
                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('region.name')</th>
                                            <th>@lang('region.locations')</th>
                                            <th>@lang('region.staff_count')</th>
                                            <th>@lang('region.approvers')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($regions as $key => $region)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $region->name }}</td>
                                                <td>
                                                    @foreach ($region->locations as $location)
                                                        <span class="label label-info" style="margin-right: 5px;">
                                                            {{ $location->location_name }}
                                                        </span>
                                                    @endforeach
                                                    @if ($region->locations->isEmpty())
                                                        <span class="text-muted">No locations</span>
                                                    @endif
                                                </td>
                                                <td>{{ $region->employees_count }}</td>
                                                <td>
                                                    @foreach ($region->leaveApprovers as $approver)
                                                        <span class="label label-success" style="margin-right: 5px;">
                                                            {{ $approver->first_name }} {{ $approver->middle_name }}
                                                            {{ $approver->last_name }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <a href="{{ route('region.edit', $region->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                        data-url="{{ route('region.delete', $region->id) }}"
                                                        data-id="{{ $region->id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>

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

@section('page_scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('body').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var $button = $(this);
                var url = $button.data('url');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: url,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            beforeSend: function() {
                                $button.prop('disabled', true);
                            },
                            complete: function() {
                                $button.prop('disabled', false);
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Remove table row with fade out animation
                                    $button.closest('tr').fadeOut(300, function() {
                                        $(this).remove();
                                    });

                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message ||
                                            'Region deleted successfully',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    showErrorAlert(response.message);
                                }
                            },
                            error: function(xhr) {
                                var errorMsg = xhr.responseJSON?.message ||
                                    xhr.statusText ||
                                    'There was a problem deleting the region.';
                                showErrorAlert(errorMsg);
                            }
                        });
                    }
                });
            });

            function showErrorAlert(message) {
                Swal.fire({
                    title: 'Error!',
                    text: message,
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    </script>
@endsection
