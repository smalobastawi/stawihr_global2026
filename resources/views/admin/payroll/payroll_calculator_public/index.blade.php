@php
$front_setting = getFrontData();
@endphp
@extends('front.master')

@section('title')
{{ $front_setting->company_title }}
@endsection

@section('meta')

<meta name="og:title" content="{{ $front_setting->company_title }}" />
<meta name="og:image" content="{{ asset('storage/uploads/front/'.$front_setting->logo) }}" />
<meta name="og:url" content="{{ url('/') }}" />
<meta name="og:description" content="{{ $front_setting->about_us_description }}" />
<meta name="description" content="{{ $front_setting->about_us_description }}" />

@endsection

@section('content')
    <!-- Start Home -->
    <div  class="" style="center center;">
        
        <div class="home-center">
            <div class="home-desc-center">
                <div class="container">
                    <div class="row justify-content-center">
                        
                        <div class="col-lg-12">
                            <div class="row">
                                <!-- panel -->
                                <div class="col-lg-3 col-xs-6">
                                    <a href="{{route('payrollcaculator_paye')}}">
                                        <!-- small box -->
                                    </a><div class="small-box bg-teal"><a href="#">
                                         

                                        </a><a href="{{ route('payrollcaculator_paye') }}" class="small-box-footer"><div class="btn waves-effect waves-light"> PAYE Calculator </div><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                    <a href="{{route('payrollcaculator_nhif')}}">
                                        <!-- small box -->
                                    </a><div class="small-box bg-maroon"><a href="{{route('payrollcaculator_nhif')}}">
                                           
                    
                                        </a><a href="{{route('payrollcaculator_nhif')}}" class="small-box-footer"><div class="btn waves-effect waves-light"> NHIF Calculator </div><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->


                                <div class="col-lg-3 col-xs-6">
                                   <div class="small-box bg-orange"><a href="{{route('payrollcaculator_nssf')}}">
                                           
                                        </a>
                                        <a href="{{route('payrollcaculator_nssf')}}" class="small-box-footer"><div class="btn waves-effect waves-light"> NSSF Calculator </div><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                    <div class="small-box bg-orange"><a href="{{route('payrollcaculator_ahl')}}">
                                            
                                         </a>
                                         <a href="{{route('payrollcaculator_ahl')}}" class="small-box-footer"><div class="btn waves-effect waves-light"> AHL Calculator </div><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                     </div>
 
                                 </div><!-- ./col -->


                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @endsection

@push('javascript')
<script>
        $(function() {
            $('.data').on('click', '.pagination a', function (e) {
                getData($(this).attr('href').split('page=')[1]);
                e.preventDefault();
            });


        });

        function getData(page) {

            $.ajax({
                url : '?page=' + page,
                datatype: "html",
            }).done(function (data) {
                $('.data').html(data);
                $('html,body').animate({
        scrollTop: $(".career").offset().top},
        'slow');
            }).fail(function () {
                alert('No response from server');
            });
        }
    </script>
    
    <script>
       
       $(document).ready(function(){
         // Add smooth scrolling to all links
         $(".navigation-menu li a").on('click', function(event) {
       
           // Make sure this.hash has a value before overriding default behavior
           if (this.hash !== "") {
             // Prevent default anchor click behavior
             event.preventDefault();
       
             // Store hash
             var hash = this.hash;
       
             // Using jQuery's animate() method to add smooth page scroll
             // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
             $('html, body').animate({
               scrollTop: $(hash).offset().top
             }, 800, function(){
          
               // Add hash (#) to URL when done scrolling (default click behavior)
               window.location.hash = hash;
             });
           } // End if
         });
       });
       
           </script>
@endpush
