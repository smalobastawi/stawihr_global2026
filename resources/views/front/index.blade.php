@php
$front_setting = getFrontData();
@endphp
@extends('front.master')

@section('title')
{{ $front_setting && $front_setting->company_title ? $front_setting->company_title : '' }}
@endsection

@section('meta')

@if($front_setting && $front_setting->logo)
<meta name="og:image" content="{{ asset('storage/uploads/front/'.$front_setting->logo) }}" />
@endif

@if($front_setting && $front_setting->company_title)
<meta name="og:title" content="{{ $front_setting->company_title }}" />
@endif

@if($front_setting && $front_setting->about_us_description)
<meta name="og:description" content="{{ $front_setting->about_us_description }}" />
<meta name="description" content="{{ $front_setting->about_us_description }}" />
@endif

@if($front_setting && $front_setting->og_url ?? false)
<meta name="og:url" content="{{ $front_setting->og_url }}" />
@elseif($front_setting && ($front_setting->og_url ?? true))
<meta name="og:url" content="{{ url('/') }}" />
@endif

@endsection


@section('content')
    <!-- Start Home -->
    <section  class="bg-home" style="background: url('{{ url('front-assets/images/cover.png') }}') center center;">
        <div class="bg-overlay"></div>
        <div class="home-center">
            <div class="home-desc-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="title-heading text-center text-white">

                                <h1 class="heading font-weight-bold mb-4">StawiHR -  HR and Payroll Solution </h1>
                                <h6 class="small-title text-uppercase text-light mb-3">
{{--                                {!! $front_setting->short_description !!}--}}
                               </h6>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

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
