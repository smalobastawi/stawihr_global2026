 <!-- Bootstrap Core JavaScript -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script src="{!! asset('admin_assets/bootstrap/dist/js/bootstrap.min.js') !!}"></script>
 <!-- Menu Plugin JavaScript -->
 <script src="{!! asset('admin_assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') !!}"></script>
 <!--slimscroll JavaScript -->
 <script src="{!! asset('admin_assets/js/jquery.slimscroll.js') !!}"></script>
 <!--Wave Effects -->
 <script src="{!! asset('admin_assets/js/waves.js') !!}"></script>
 <!--Counter js -->
 <script src="{!! asset('admin_assets/plugins/bower_components/waypoints/lib/jquery.waypoints.js') !!}"></script>
 <script src="{!! asset('admin_assets/plugins/bower_components/counterup/jquery.counterup.min.js') !!}"></script>
 <!-- Sparkline chart JavaScript -->
 <script src="{!! asset('admin_assets/plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js') !!}"></script>
 <!-- Custom Theme JavaScript -->
 <script src="{!! asset('admin_assets/js/custom.min.js') !!}"></script>
 <script src="{!! asset('admin_assets/js/dashboard1.js') !!}"></script>
 <script src="{!! asset('admin_assets/plugins/bower_components/toast-master/js/jquery.toast.js') !!}"></script>
 <script src="{!! asset('admin_assets/plugins/bower_components/datatables/jquery.dataTables.min.js') !!}"></script>
 <script src="{!! asset('admin_assets/plugins/bower_components/sweetalert/sweetalert-dev.js') !!}"></script>
 <!-- bootstrap-datepicker -->
 <script src="{!! asset('admin_assets/plugins/bower_components/datepicker/bootstrap-datepicker.js') !!}"></script>
 <!--TIme picker js-->
 <script src="{!! asset('admin_assets/plugins/bower_components/timepicker/bootstrap-timepicker.min.js') !!}"></script>
 <!-- select2 -->
 <script src="{!! asset('admin_assets/plugins/bower_components/select2/select2.full.min.js') !!}"></script>

 <script src="{!! asset('admin_assets/plugins/bower_components/toast-master/js/jquery.toast.js') !!}"></script>
 <script src="{!! asset('admin_assets/js/toastr.js') !!}"></script>

 <!-- jquery-validator -->
 <script type="text/javascript" src="{!! asset('admin_assets/plugins/bower_components/jquery-validator/jquery-validator.1.15.0.js') !!}"></script>
 <script type="text/javascript" src="{!! asset('admin_assets/plugins/bower_components/jquery-validator/jquery-additional-method.1.15.0.min.js') !!}"></script>
 <!-- Star Ratings -->
 <script src="{!! asset('admin_assets/plugins/bower_components/rateyo/jquery.rateyo.js') !!}"></script>

 <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.3/datatables.min.js"></script>

 {{-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
 <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
 <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
 <!-- Summernote -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote.min.js"></script>

 <!-- In your admin.master blade template header -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 <script>
     $(document).ready(function() {
         $('#myTable').DataTable({
             "pageLength": 2000,
             "ordering": true,
             dom: 'Bfrtip',
             buttons: [
                 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'pageLength'
             ],

         });
     });
     $(document).ready(function() {
         $('#myTable1').DataTable({
             "pageLength": 2000,
             "ordering": true,
             dom: 'Bfrtip',
             buttons: [
                 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'pageLength'
             ],

         });
     });
 </script>
 <script>
     $(document).ready(function() {
         $('#attendanceTable1').DataTable({
             "pageLength": 1000,
             "ordering": true,
             dom: 'Bfrtip',
             buttons: [
                 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'pageLength'
             ],

         });
     });
 </script>
 <script>
     $(document).ready(function() {
         $('#myTablePayrollDetails').DataTable({
             "pageLength": 2000,
             "ordering": true,
             dom: 'Bfrtip',
             buttons: [
                 'excelHtml5', 'csvHtml5', 'pageLength'
             ],

         });
     });
 </script>
 <script>
     // $(function () {
     //     $(".select2").select2();
     //     $('#myTable').DataTable({
     //         "ordering": true,
     //         "pageLength": 100,
     //
     //     });
     //
     // });

     function addMenuClass() {
         var segment3 = '{{ Request::segment(1) }}';
         var url = base_url + segment3;
         // var navItem = $(this).find("[href='" + url + "']");

         $('a[href="' + url + '"]').parents('.treeview-menu').addClass('collapse in');
         $('a[href="' + url + '"]').parents('.treeview-menu').parent().children('.module').addClass('active');
     }

     $(".alert-success").delay(2000).fadeOut("slow");
     //   $(".alert-danger").delay(2000).fadeOut("slow");
     $(document).on("focus", ".yearPicker", function() {
         $(this).datepicker({
             format: 'yyyy',
             minViewMode: 2
         }).on('changeDate', function(e) {
             $(this).datepicker('hide');
         });
     });
     $(document).on("focus", ".dateField", function() {
         $(this).datepicker({
             format: 'dd/mm/yyyy',
             todayHighlight: true,
             clearBtn: true
         }).on('changeDate', function(e) {
             $(this).datepicker('hide');
         });
     });


     $(".monthField").datepicker({
         format: "yyyy-mm",
         viewMode: "months",
         minViewMode: "months"
     }).on('changeDate', function(e) {
         $(this).datepicker('hide');
     });

     $(document).on('click', '.delete', function() {
         var actionTo = $(this).attr('href');
         var token = $(this).attr('data-token');
         var id = $(this).attr('data-id');
         swal({
             title: "Are you sure?",
             text: "You will not be able to recover this data/file!",
             type: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DD6B55",
             confirmButtonText: "Yes, delete it!",
             closeOnConfirm: false
         }, function(isConfirm) {
             if (isConfirm) {
                 $.ajax({
                     url: actionTo,
                     type: 'post',
                     data: {
                         _method: 'delete',
                         _token: token
                     },
                     dataType: 'json',
                     success: function(data) {
                         if (data.status === 'success') {
                             swal("Deleted!", data.message, "success");
                             location.reload();
                         } else {
                             swal("Error!", data.message ||
                                 "Some Error Found !, Please try again.", "error");
                         }
                     },
                     error: function(jqXHR, textStatus, errorThrown) {
                         var errorMessage = "Some Error Found !, Please try again.";
                         if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                             errorMessage = jqXHR.responseJSON.message;
                         }
                         swal("Error!", errorMessage, "error");
                     }
                 });
             } else {
                 swal("Cancelled", "Your data is safe .", "error");
             }
         });
         return false;
     });

     $(document).on('click', '.destroy', function() {
         var actionTo = $(this).attr('href');
         var token = $(this).attr('data-token');
         var id = $(this).attr('data-id');
         swal({
             title: "Are you sure?",
             text: "You will not be able to recover this data/file file!",
             type: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DD6B55",
             confirmButtonText: "Yes, destroy it!",
             closeOnConfirm: false
         }, function(isConfirm) {
             if (isConfirm) {
                 $.ajax({
                     url: actionTo,
                     type: 'post',
                     data: {
                         _method: 'delete',
                         _token: token
                     },
                     success: function(data) {
                         if (data == 'hasForeignKey') {
                             swal({
                                 title: "Oops!",
                                 text: "This data is used elsewhere",
                                 type: "error"
                             });
                         } else if (data == 'success') {
                             swal({
                                 title: "Destroyed!",
                                 text: "Your information was destroyed successfully.",
                                 type: "success"
                             }, function(isConfirm) {
                                 if (isConfirm) {
                                     $('.' + id).fadeOut();
                                     /// location.reload()

                                 }
                             });
                         } else {
                             swal({
                                 title: "Error!",
                                 text: "Some Error Found !, Please try again.",
                                 type: "error"
                             });
                         }
                     }

                 });
             } else {
                 swal("Cancelled", "Your data is safe .", "error");
             }
         });
         return false;
     });

     $(document).on('click', '.restore', function() {
         var actionTo = $(this).attr('href');
         var token = $(this).attr('data-token');
         var id = $(this).attr('data-id');
         swal({
             title: "Are you sure?",
             text: "The data will be restored to active records!",
             type: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DD6B55",
             confirmButtonText: "Yes, restore it!",
             closeOnConfirm: false
         }, function(isConfirm) {
             if (isConfirm) {
                 $.ajax({
                     url: actionTo,
                     type: 'post',
                     data: {
                         _method: 'post',
                         _token: token
                     },
                     success: function(data) {
                         if (data == 'hasForeignKey') {
                             swal({
                                 title: "Oops!",
                                 text: "This data is used elsewhere",
                                 type: "error"
                             });
                         } else if (data == 'success') {
                             swal({
                                 title: "Restored!",
                                 text: "Your information restored successfully.",
                                 type: "success"
                             }, function(isConfirm) {
                                 if (isConfirm) {
                                     $('.' + id).fadeOut();
                                     location.reload()

                                 }
                             });
                         } else {
                             swal({
                                 title: "Error!",
                                 text: "Some Error Found !, Please try again.",
                                 type: "error"
                             });
                         }
                     }

                 });
             } else {
                 swal("Cancelled", "Your data is safe .", "error");
             }
         });
         return false;
     });

     $(document).on('click', '.disable', function() {
         var actionTo = $(this).attr('href');
         var token = $(this).attr('data-token');
         var id = $(this).attr('data-id');
         swal({
             title: "Are you sure?",
             text: "The user will be disabled!",
             type: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DD6B55",
             confirmButtonText: "Yes, disable",
             closeOnConfirm: false
         }, function(isConfirm) {
             if (isConfirm) {
                 $.ajax({
                     url: actionTo,
                     type: 'post',
                     data: {
                         _method: 'get',
                         _token: token
                     },
                     success: function(data) {
                         if (data == 'hasForeignKey') {
                             swal({
                                 title: "Oops!",
                                 text: "This data is used somewhere",
                                 type: "error"
                             });
                         } else if (data == 'success') {
                             swal({
                                 title: "Disabled!",
                                 text: "Record disabled successfully.",
                                 type: "success"
                             }, function(isConfirm) {
                                 if (isConfirm) {
                                     $('.' + id).fadeOut();
                                     location.reload()
                                 }
                             });
                         } else {
                             swal({
                                 title: "Error!",
                                 text: "SomeError Found !, Please try again.",
                                 type: "error"
                             });
                         }
                     }

                 });
             } else {
                 swal("Cancelled", "Your data is safe .", "error");
             }
         });
         return false;
     });


     $(document).on('click', '.enable', function() {
         var actionTo = $(this).attr('href');
         var token = $(this).attr('data-token');
         var id = $(this).attr('data-id');
         swal({
             title: "Are you sure?",
             text: "The user will be enabled!",
             type: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DD6B55",
             confirmButtonText: "Yes, enable",
             closeOnConfirm: false
         }, function(isConfirm) {
             if (isConfirm) {
                 $.ajax({
                     url: actionTo,
                     type: 'post',
                     data: {
                         _method: 'get',
                         _token: token
                     },
                     success: function(data) {
                         if (data == 'hasForeignKey') {
                             swal({
                                 title: "Oops!",
                                 text: "This data is used somewhere",
                                 type: "error"
                             });
                         } else if (data == 'success') {
                             swal({
                                 title: "Enabled!",
                                 text: "Record enabled successfully.",
                                 type: "success",

                             }, function(isConfirm) {
                                 if (isConfirm) {
                                     $('.' + id).fadeOut();
                                     // location.reload()
                                 }
                             });
                         } else {
                             swal({
                                 title: "Error!",
                                 text: "Some Error Found !, Please try again.",
                                 type: "error"
                             });
                         }
                     }

                 });
             } else {
                 swal("Cancelled", "Your data is safe .", "error");
             }
         });
         return false;
     });
 </script>
 <script>
     $(document).ready(function() {
         $('#myTableAdvances').DataTable({
             "pageLength": 100,
             "ordering": true,
             dom: 'Bfrtip',
             buttons: [{
                 extend: 'copyHtml5',
                 footer: true
             }, {
                 extend: 'excelHtml5',
                 footer: true
             }, {
                 extend: 'csvHtml5',
                 footer: true
             }, {
                 extend: 'pdfHtml5',
                 footer: true
             }, 'pageLength'],
             "footerCallback": function(row, data, start, end, display) {
                 var api = this.api(),
                     data;

                 // Remove the formatting to get integer data for summation
                 var intVal = function(i) {
                     return typeof i === 'string' ?
                         i.replace(/[\$,]/g, '') * 1 :
                         typeof i === 'number' ?
                         i : 0;
                 };

                 // Total over all pages
                 total = api
                     .column(5)
                     .data()
                     .reduce(function(a, b) {
                         return intVal(a) + intVal(b);
                     }, 0);

                 // Total over this page
                 pageTotal = api
                     .column(5, {
                         page: 'current'
                     })
                     .data()
                     .reduce(function(a, b) {
                         return intVal(a) + intVal(b);
                     }, 0);

                 // Update footer
                 $(api.column(5).footer()).html(
                     pageTotal
                 );
             }

         });
     });
 </script>

 <script>
     $(document).ready(function() {
         $('#myTableBonuses').DataTable({
             "pageLength": 100,
             "ordering": true,
             dom: 'Bfrtip',
             buttons: [{
                 extend: 'copyHtml5',
                 footer: true
             }, {
                 extend: 'excelHtml5',
                 footer: true
             }, {
                 extend: 'csvHtml5',
                 footer: true
             }, {
                 extend: 'pdfHtml5',
                 footer: true
             }, 'pageLength'],
             "footerCallback": function(row, data, start, end, display) {
                 var api = this.api(),
                     data;

                 // Remove the formatting to get integer data for summation
                 var intVal = function(i) {
                     return typeof i === 'string' ?
                         i.replace(/[\$,]/g, '') * 1 :
                         typeof i === 'number' ?
                         i : 0;
                 };

                 // Total over all pages
                 total = api
                     .column(5)
                     .data()
                     .reduce(function(a, b) {
                         return intVal(a) + intVal(b);
                     }, 0);

                 // Total over this page
                 pageTotal = api
                     .column(5, {
                         page: 'current'
                     })
                     .data()
                     .reduce(function(a, b) {
                         return intVal(a) + intVal(b);
                     }, 0);

                 // Update footer
                 $(api.column(5).footer()).html(
                     pageTotal
                 );
             }

         });
     });
 </script>
 <script>
     $(document).ready(function() {
         $('#myDataTables').DataTable({
             "pageLength": 100,
             "ordering": true,
             dom: 'Bfrtip',
             buttons: [{
                 extend: 'copyHtml5',
                 footer: true
             }, {
                 extend: 'excelHtml5',
                 footer: true
             }, {
                 extend: 'csvHtml5',
                 footer: true
             }, {
                 extend: 'pdfHtml5',
                 footer: true
             }, 'pageLength'],
             "footerCallback": function(row, data, start, end, display) {
                 var api = this.api(),
                     data;

                 // Remove the formatting to get integer data for summation
                 var intVal = function(i) {
                     return typeof i === 'string' ?
                         i.replace(/[\$,]/g, '') * 1 :
                         typeof i === 'number' ?
                         i : 0;
                 };

                 // Total over all pages
                 total = api
                     .column(5)
                     .data()
                     .reduce(function(a, b) {
                         return intVal(a) + intVal(b);
                     }, 0);

                 // Total over this page
                 pageTotal = api
                     .column(5, {
                         page: 'current'
                     })
                     .data()
                     .reduce(function(a, b) {
                         return intVal(a) + intVal(b);
                     }, 0);

                 // Update footer
                 $(api.column(5).footer()).html(
                     pageTotal
                 );
             }

         });
     });

     $(document).on('click', '.sendPasswordReset', function() {
         var actionTo = $(this).attr('href');
         var token = $(this).attr('data-token');
         var id = $(this).attr('data-id');
         swal({
             title: "Are you sure?",
             text: "The user will receive a link to reset the password!",
             type: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DD6B55",
             confirmButtonText: "Yes, send password reset!",
             closeOnConfirm: false
         }, function(isConfirm) {
             if (isConfirm) {
                 $.ajax({
                     url: actionTo,
                     type: 'post',
                     data: {
                         _method: 'post',
                         _token: token
                     },
                     success: function(data) {
                         if (data == 'hasForeignKey') {
                             swal({
                                 title: "Oops!",
                                 text: "This data is used anywhere",
                                 type: "error"
                             });
                         } else if (data == 'success') {
                             swal({
                                 title: "Deleted!",
                                 text: "Your information delete successfully.",
                                 type: "success"
                             }, function(isConfirm) {
                                 if (isConfirm) {
                                     $('.' + id).fadeOut();

                                 }
                             });
                         } else {
                             swal({
                                 title: "Error!",
                                 text: "Some Error Found !, Please try again.",
                                 type: "error"
                             });
                         }
                     }

                 });
             } else {
                 swal("Cancelled", "Your data is safe .", "error");
             }
         });
         return false;
     });
 </script>
 <script>
     $(document).ready(function() {
         // Safely initialize all select2 elements
         $('select.select2').select2({
             width: '100%',
             placeholder: 'Please select...',
             allowClear: true
         });

         // Alternative if you need to handle dynamic elements
         // $(document).on('select2:open', () => {
         //     document.querySelector('.select2-search__field').focus();
         // });




     });
 </script>
