@extends('admin.master')
@section('content')
@section('title')
@lang('payroll_setup.nhif_setup')
@endsection
<style>

	.select2{ width: 100% !important;}

</style>
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			   <ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>	

		</div>
					
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @lang('payroll_setup.rate_of_income_tax')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if(session()->has('success'))
								<div class="alert alert-success alert-dismissable">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
								</div>
							@endif
							@if(session()->has('error'))
								<div class="alert alert-danger alert-dismissable">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
								</div>
							@endif
							<div class="table-responsive">
								<table  class="table table-bordered">
									<thead>
										 <tr class="tr_header">
											<th>@lang('common.serial')</th>
											<th>@lang('payroll_setup.range_start')</th>
											 <th>@lang('payroll_setup.range_end')</th>
											<th>@lang('payroll_setup.amount_deductable')</th>
											<th>@lang('common.update')</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$countTax = count($nhifRates);
										?>
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										@foreach($nhifRates as $key=>$value)
											<tr>
												<td>
                                                    <?php
                                                    if($key == 0){
                                                        echo __('payroll_setup.first');
                                                    }
                                                    else{
                                                        echo  __('payroll_setup.next');
                                                    }
                                                    ?>
												</td>
												<td>
                                                    <?php
                                                    echo '<input type="hidden" class="form-control taxHiddenValue" name="tax_rule_id" value="'.$value->id.'">';
                                                    if($key +1 == $countTax){
                                                        echo __('payroll_setup.remaining_total_income')." ---";
                                                        echo '<input type="hidden" class="form-control range_start" name="range_start" value="0">';
                                                    }else{
                                                    ?>
													<input type="number" disabled class="form-control range_start" name="range_start" value="{{$value->range_start}}">
                                                    <?php } ?>
												</td>
												<td>
													<?php
													echo '<input type="hidden" class="form-control taxHiddenValue" name="tax_rule_id" value="'.$value->id.'">';
													if($key +1 == $countTax){
														echo __('payroll_setup.remaining_total_income')." ---";
														echo '<input type="hidden" class="form-control amount" name="range_end" value="0">';
													}else{
													?>
													<input type="number" disabled class="form-control range_end" name="range_end" value="{{$value->range_end}}">
													<?php } ?>
												</td>
												<td>
                                                    <?php
                                                    if($key +1 == $countTax){
                                                        echo __('payroll_setup.remaining_taxable_amount')."---";
                                                        echo '<input type="hidden" class="form-control amount_deductable" name="amount_deductable" value="0">';
                                                    }else{
                                                    ?>
													<input type="text" disabled class="form-control amount_deductable" name="amount_deductable" value="{{$value->amount_deductable}}">
                                                    <?php } ?>
												</td>
												<td style="width:100px;">
													<button type="button" class="btn btn-sm btn-success saveTaxRow">
														@lang('common.update')
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
	<script type="text/javascript">
		jQuery(function(){

            $(document).on("change",".amount,.percentage_of_tax  ",function(){
                var amount 				= $(this).parents('tr').find('.amount').val();
                var percentage_of_tax   = $(this).parents('tr').find('.percentage_of_tax').val();
                var taxableAmount = 0;
                taxableAmount = (amount * percentage_of_tax) /100;
                $(this).parents('tr').find('.amount_of_tax').val(taxableAmount);
            });


            $("body").on("click",".saveTaxRow  ",function(){
                var taxHiddenValue      = $(this).parents('tr').find('.taxHiddenValue').val();
                var amount 				= $(this).parents('tr').find('.amount').val();
                var percentage_of_tax   = $(this).parents('tr').find('.percentage_of_tax').val();
                var amount_of_tax   	= $(this).parents('tr').find('.amount_of_tax').val();
                var gender   	        = $(this).parents('tr').find('.gender').val();
                var action = "{{ URL::to('taxSetup/updateTaxRule') }}";
                $.ajax({
                    type: "post",
                    url: action,
                    data: {'tax_rule_id': taxHiddenValue, 'amount': amount,'percentage_of_tax':percentage_of_tax,'amount_of_tax':amount_of_tax,'gender':gender,'_token': $('input[name=_token]').val()},
                    success: function (data) {
                        if(data == 'success'){
                            $.toast({
                                heading: 'success',
                                text: 'Income tax rule update successfully !',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'success',
                                hideAfter: 3000,
                                stack: 6
                            });
                        }else{
                            $.toast({
                                heading: 'Problem',
                                text: 'Something error found !',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'error',
                                hideAfter: 3000,
                                stack: 6
                            });
                        }

                    }
                });
			})
		});
	</script>
@endsection
