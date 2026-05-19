@extends('admin.leave.applyForLeave.leave_application_form')

@section('page_scripts')
    <script>
        function toDate(dateString) {
            if (!dateString) return null;
            let [d, m, y] = dateString.split('/');
            return new Date(parseInt(y), parseInt(m) - 1, parseInt(d));
        }

        jQuery(function() {

            // Store balance data globally for validation
            let leaveBalanceData = {};

            /** --------------------------------------------------------
             *  1️⃣ INITIALIZE BOTH DATEPICKERS *ONCE* WITH dd/mm/yyyy
             * -------------------------------------------------------- */
            $('.application_from_date, .application_to_date').datepicker({
                format: "dd/mm/yyyy",
                todayHighlight: true,
                clearBtn: true,
                autoclose: true,
                orientation: "auto"
            });

            /** --------------------------------------------------------
             *  2️⃣ WHEN FROM-DATE CHANGES → ADJUST TO-DATE LIMITS
             * -------------------------------------------------------- */
            $('.application_from_date').on('changeDate', function() {
                let val = $(this).val();
                let fromDate = toDate(val);

                if (!fromDate) return;

                // Update TO-DATE picker
                $('.application_to_date').datepicker('setStartDate', fromDate);
            });

            /** --------------------------------------------------------
             *  3️⃣ VALIDATE DATES + GET TOTAL DAYS
             * -------------------------------------------------------- */
            $('.application_from_date, .application_to_date').on('change', function() {

                let fromDateStr = $('.application_from_date').val();
                let toDateStr = $('.application_to_date').val();

                if (!fromDateStr || !toDateStr) {
                    $('#formSubmit').prop('disabled', true);
                    return;
                }

                let from = toDate(fromDateStr);
                let to = toDate(toDateStr);

                // Request total days
                $.ajax({
                    type: 'POST',
                    url: "{{ route('ess.leave.leave.employee.apply.totaldays') }}",
                    data: {
                        application_from_date: fromDateStr,
                        application_to_date: toDateStr,
                        leave_type_id: $('.leave_type_id').val(),
                        _token: $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(days) {
                        validateLeaveDays(days);
                    }
                });

            });

            /** --------------------------------------------------------
             *  4️⃣ VALIDATION FUNCTION FOR LEAVE DAYS
             * -------------------------------------------------------- */
            function validateLeaveDays(requestedDays) {
                if (requestedDays === 0) {
                    $.toast({
                        heading: 'Warning',
                        text: 'You cannot apply for 0 days.',
                        icon: 'warning',
                        position: 'top-right'
                    });
                    $('#formSubmit').prop('disabled', true);
                    $('.number_of_day').val('');
                    return;
                }

                $('.number_of_day').val(requestedDays);

                // Check if we have balance data
                if (!leaveBalanceData.total_available) {
                    $('#formSubmit').prop('disabled', true);
                    $.toast({
                        heading: 'Warning',
                        text: 'Please select a leave type first.',
                        icon: 'warning',
                        position: 'top-right'
                    });
                    return;
                }

                let totalAvailable = parseFloat(leaveBalanceData.total_available);
                let requested = parseFloat(requestedDays);
                let regularBalance = parseFloat(leaveBalanceData.regular_balance || 0);
                let advanceAvailable = parseFloat(leaveBalanceData.advance_available || 0);

                if (requested > totalAvailable) {
                    $.toast({
                        heading: 'Validation Error',
                        text: `You are trying to apply for ${requested} days, but your total available balance is ${totalAvailable} days (${regularBalance} regular balance + ${advanceAvailable} advance available). Please reduce your request or contact HR.`,
                        icon: 'error',
                        position: 'top-right',
                        hideAfter: 10000,
                        stack: 6
                    });

                    $('#formSubmit').prop('disabled', true);
                } else {
                    $('#formSubmit').prop('disabled', false);

                    // Show success message if using advance days
                    if (requested > regularBalance && regularBalance > 0) {
                        let advanceUsed = requested - regularBalance;
                        $.toast({
                            heading: 'Info',
                            text: `This application will use ${regularBalance} regular balance days and ${advanceUsed} advance days.`,
                            icon: 'info',
                            position: 'top-right',
                            hideAfter: 10000,
                            stack: 6
                        });
                    }
                }
            }

            /** --------------------------------------------------------
             *  5️⃣ WHEN LEAVE TYPE CHANGES → CHECK BALANCE
             * -------------------------------------------------------- */
            $('.leave_type_id').on('change', function() {

                let leaveType = $(this).val();
                let employee = $('.employee_id').val();

                if (!leaveType || !employee) {
                    $('.current_balance').val('');
                    leaveBalanceData = {};
                    $('#formSubmit').prop('disabled', true);
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ route('ess.leave.balance') }}",
                    data: {
                        leave_type_id: leaveType,
                        employee_id: employee,
                        _token: $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(data) {
                        leaveBalanceData = data;

                        if (data.regular_balance == 0) {
                            $.toast({
                                heading: 'Warning',
                                text: 'You have no leave balance!',
                                icon: 'warning',
                                position: 'top-right'
                            });
                            $('.current_balance').val('0');
                            $('#formSubmit').prop('disabled', true);
                        } else {
                            $('.current_balance').val(data.regular_balance);
                            $('#formSubmit').prop('disabled', false);
                        }

                        // Revalidate if dates are already selected
                        let currentDays = $('.number_of_day').val();
                        if (currentDays) {
                            validateLeaveDays(currentDays);
                        }
                    }
                });
            });

        });
    </script>
@endsection
