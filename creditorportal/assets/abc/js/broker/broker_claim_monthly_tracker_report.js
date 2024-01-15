$(document).ready(function(){
    
    $.ajax({
        url: "/broker/get_all_employer",
        type: "POST",
        dataType: "json",
        success: function(response) {
            $('#employer_name').empty();
            $('#employer_name').append('<option value=""> Select Employer name</option>');
            for (i = 0; i < response.length; i++) {
                $('#employer_name').append('<option value="' + response[i].company_id + '">' + response[i].comapny_name + '</option>');
            }
        }
    });
//    all_insurer
    // $.ajax({
    //     url: "/broker/get_all_insurer",
    //     type: "POST",
    //     dataType: "json",
    //     success: function(response) {
    //         $('#insurer_id').empty();
    //         $('#insurer_id').append('<option value=""> Select Insurer name</option>');
    //         for (i = 0; i < response.length; i++) {
    //             $('#insurer_id').append('<option value="' + response[i].insurer_id + '">' + response[i].ins_co_name + '</option>');
    //         }
    //     }
    // });
    // //    all_tpa
    // $.ajax({
    //     url: "/broker/get_all_tpa",
    //     type: "POST",
    //     dataType: "json",
    //     success: function(response) {
    //         $('#TPA_ID').empty();
    //         $('#TPA_ID').append('<option value=""> Select TPA name</option>');
    //         for (i = 0; i < response.length; i++) {
    //             $('#TPA_ID').append('<option value="' + response[i].TPA_id + '">' + response[i].TPA_name + '</option>');
    //         }
    //     }
    // });
    
    // //    get_all_policy_no
    // $.ajax({
    //     url: "/broker/get_allof_policy_no",
    //     type: "POST",
    //     dataType: "json",
    //     success: function(response) {
    //         $('#policy_no').empty();
    //         $('#policy_no').append('<option value=""> Select Policy #</option>');
    //         for (i = 0; i < response.length; i++) {
    //             $('#policy_no').append('<option value="' + response[i].policy_no + '">' + response[i].policy_no + '</option>');
    //         }
    //     }
    // });
    //    get all insurer
    $("#employer_name").change(function() {
        var company_id = $(this).val();
        $.ajax({
            url: "/get_all_insuer_against_company",
            type: "POST",
            data: {company_id: company_id},
            dataType: "json",
            success: function(response) {
                $('#insurer_id').empty();
                $('#insurer_id').append('<option value=""> Select Insurer name</option>');
                for (i = 0; i < response.length; i++) {
                    $('#insurer_id').append('<option value="' + response[i].ID + '">' + response[i].insurer_name + '</option>');
                }
            }
        });
    });
    // change on employer_name get policy type
    $("#insurer_id").change(function() {
        var insurer_id = $(this).val();
        var company_id = $('#employer_name').val();
        $.ajax({
            url: "/broker/get_tpa_against_insurer",
            type: "POST",
            data: {company_id: company_id,insurer_id:insurer_id},
            dataType: "json",
            success: function(response) {
                $('#TPA_ID').empty();
                $('#TPA_ID').append('<option value=""> Select TPA nAME</option>');
                for (i = 0; i < response.length; i++) {
                    $('#TPA_ID').append('<option value="' + response[i].tpa_id + '">' + response[i].TPA_name + '</option>');
                }
            }
        });
    });

    // change on policy type get policy no
    $("#TPA_ID").change(function() {
        var TPA_ID = $(this).val();
        var insurer_id = $('#insurer_id').val();
        var company_id = $('#employer_name').val();
        $.ajax({
            url: "/get_all_policy_against_company_and_insurer",
            type: "POST",
            data: {insurer_id: insurer_id,company_id:company_id},
            dataType: "json",
            success: function(response) {
                $('#policy_no').empty();
                $('#policy_no').append('<option value=""> Select Policy #</option>');
                for (i = 0; i < response.length; i++) {
                    $('#policy_no').append('<option value="' + response[i].policy_no + '">' + response[i].policy_no + '</option>');
                }
            }
        });
    });
     $("#from_date").datepicker
            ({
                dateFormat: "dd-mm-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                yearRange: "-100Y:",
                onSelect: function(date) {
//                    var selectedDate = new Date(date);
//                    var msecsInADay = 86400000;
//                    var endDate = new Date(selectedDate.getTime() + msecsInADay);
//                    //Set Minimum Date of EndDatePicker After Selected Date of StartDatePicker
//                    $("#to_date").datepicker("option", "minDate", endDate);
                    var date = $(this).datepicker("getDate");
                    var tempStartDate = new Date(date);
                    var $returning_on = $("#to_date");
                    tempStartDate.setDate(date.getDate() + 1);
                    $returning_on.datepicker("option", "minDate", tempStartDate);
                    $returning_on.datepicker("option", "maxDate", new Date());

                },
                maxDate: new Date(),
                minDate: "-100Y +1D"
            });
    $("#to_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        maxDate: new Date(),
        // minDate: "-100Y +1D"
    });
    $("#export_data").click(function(){
        var employer_id = [];
        var insurer_id = [];
        var TPA_ID = [];
        var policy_no = [];
        $('#employer_name :selected').each(function(i, sel){ 
           employer_id.push( $(sel).val()); 
        });
        $('#insurer_id :selected').each(function(i, sel){ 
           insurer_id.push( $(sel).val()); 
        });
        $('#TPA_ID :selected').each(function(i, sel){ 
           TPA_ID.push( $(sel).val()); 
        });
        $('#policy_no :selected').each(function(i, sel){ 
           policy_no.push( $(sel).val()); 
        });
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        if (employer_id == "")
        {
            swal("", "please select employer name");
            return false;
        } else if (from_date == "") {
            swal("", "please select from date");
            return false;
        } else if (to_date == "") {
            swal("", "please select to date");
            return false;
        } else
        {
            window.location = '/broker/claim_monthly_tracker_excel_data?employer_id=' + employer_id + '&policy_no=' + policy_no + '&insurer_id=' + insurer_id + '&TPA_ID=' + TPA_ID + '&from_date=' + from_date + '&to_date=' + to_date;
        }
    });
    
    $("#btn_save11").click(function() {
        var employer_id =  $('#employer_name').val();
        var insurer_id = $('#insurer_id').val();
        var TPA_ID = $('#TPA_ID').val();
        var policy_no = $('#policy_no').val();
        // $('#employer_name :selected').each(function(i, sel){ 
        //    employer_id.push( $(sel).val()); 
        // });
        // $('#insurer_id :selected').each(function(i, sel){ 
        //    insurer_id.push( $(sel).val()); 
        // });
        // $('#TPA_ID :selected').each(function(i, sel){ 
        //    TPA_ID.push( $(sel).val()); 
        // });
        // $('#policy_no :selected').each(function(i, sel){ 
        //    policy_no.push( $(sel).val()); 
        // });
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        if (employer_id == "")
        {
            swal("", "please select employer name");
            return false;
        } else if (from_date == "") {
            swal("", "please select from date");
            return false;
        } else if (to_date == "") {
            swal("", "please select to date");
            return false;
        } else
        {
            $.ajax({
                url: "/show_all_monthly_claim_report_data",
                type: "POST",
                data: {employer_id: employer_id,policy_no:policy_no,insurer_id:insurer_id,TPA_ID:TPA_ID,from_date:from_date,to_date:to_date},
                dataType: "json",
                success: function(response) {
                    $('#count_5').text(response.count_5);
                    $('#count_six_to_ten_days').text(response.count_six_to_ten_days);
                    $('#count_eleven_to_fifteen_days').text(response.count_eleven_to_fifteen_days);
                    $('#count_sixteen_to_thirty_days').text(response.count_sixteen_to_thirty_days);
                    $('#count_above_thirty_days').text(response.count_above_thirty_days);
                    $('#total_claim_paid').text(response.total_claim_paid);
                    $('#count_5_per').text(response.count_5_per);
                    $('#count_six_to_ten_days_per').text(response.count_six_to_ten_days_per);
                    $('#count_eleven_to_fifteen_days_per').text(response.count_eleven_to_fifteen_days_per);
                    $('#count_sixteen_to_thirty_days_per').text(response.count_sixteen_to_thirty_days_per);
                    $('#count_above_thirty_days_per').text(response.count_above_thirty_days_per);
                    $('#total_per').text(response.total_per);
                    $('#count_5_pending_claim').text(response.count_5_pending_claim);
                    $('#count_six_to_ten_days_pending_claim').text(response.count_six_to_ten_days_pending_claim);
                    $('#count_eleven_to_fifteen_days_pending_claim').text(response.count_sixteen_to_thirty_days_per);
                    $('#count_sixteen_to_thirty_days_pending_claim').text(response.count_sixteen_to_thirty_days_per);
                    $('#count_above_thirty_days_pending_claim').text(response.count_above_thirty_days_pending_claim);
                    $('#total_claim_pending').text(response.total_claim_pending);
                    $('#count_5_per_pending').text(response.count_5_per);
                    $('#count_six_to_ten_days_per_pending').text(response.count_six_to_ten_days_per);
                    $('#count_eleven_to_fifteen_days_per_pending').text(response.count_eleven_to_fifteen_days_per);
                    $('#count_sixteen_to_thirty_days_per_pending').text(response.count_sixteen_to_thirty_days_per);
                    $('#count_above_thirty_days_per_pending').text(response.count_above_thirty_days_per);
                    $('#total_per_pending_data_pending').text(response.total_per_pending_data);
                    $('#count_5_pending_insurer').text(response.count_5_pending_insurer);
                    $('#count_six_to_ten_days_pending_insurer').text(response.count_six_to_ten_days_pending_insurer);
                    $('#count_eleven_to_fifteen_days_pending_insurer').text((response.count_eleven_to_fifteen_days_pending_insurer).toFixed(2));
                    $('#count_sixteen_to_thirty_days_pending_insurer').text(response.count_sixteen_to_thirty_days_pending_insurer);
                    $('#count_above_thirty_days_pending_insurer').text(response.count_above_thirty_days_pending_insurer);
                    $('#total_claim_pending_insurer').text(response.total_claim_pending_insurer);
                    $('#count_5_pending_insurer_per').text(response.count_5_pending_insurer_per);
                    $('#count_six_to_ten_days_pending_insurer_per').text(response.count_six_to_ten_days_pending_insurer_per);
                    $('#count_eleven_to_fifteen_days_pending_insurer_per').text(response.count_eleven_to_fifteen_days_pending_insurer_per);
                    $('#count_sixteen_to_thirty_days_pending_insurer_per').text(response.count_sixteen_to_thirty_days_pending_insurer_per);
                    $('#count_above_thirty_days_pending_insurer_per').text(response.count_above_thirty_days_pending_insurer_per);
                    $('#total_per_pending_insurer').text(response.total_per_pending_insurer);
                    $('#count_5_pending_tpa').text(response.count_5_pending_tpa);
                    $('#count_six_to_ten_days_pending_tpa').text(response.count_six_to_ten_days_pending_tpa);
                    $('#count_eleven_to_fifteen_days_pending_tpa').text(response.count_eleven_to_fifteen_days_pending_tpa);
                    $('#count_sixteen_to_thirty_days_pending_tpa').text(response.count_sixteen_to_thirty_days_pending_tpa);
                    $('#count_above_thirty_days_pending_tpa').text(response.count_above_thirty_days_pending_tpa);
                    $('#total_claim_pending_tpa').text(response.total_claim_pending_tpa);
                    $('#count_5_pending_tpa_per').text(response.count_5_pending_tpa_per);
                    $('#count_six_to_ten_days_pending_tpa_per').text(response.count_six_to_ten_days_pending_tpa_per);
                    $('#count_eleven_to_fifteen_days_pending_tpa_per').text(response.count_eleven_to_fifteen_days_pending_tpa_per);
                    $('#count_sixteen_to_thirty_days_pending_tpa_per').text(response.count_sixteen_to_thirty_days_pending_tpa_per);
                    $('#count_above_thirty_days_pending_tpa_per').text(response.count_above_thirty_days_pending_tpa_per);
                    $('#total_per_pending_tpa').text(response.total_per_pending_tpa);
                    $('#count_5_deficiency').text(response.count_5_deficiency);
                    $('#count_six_to_ten_days_deficiency').text(response.count_six_to_ten_days_deficiency);
                    $('#count_eleven_to_fifteen_days_deficiency').text(response.count_eleven_to_fifteen_days_deficiency);
                    $('#count_sixteen_to_thirty_days_deficiency').text(response.count_sixteen_to_thirty_days_deficiency);
                    $('#count_above_thirty_days_deficiency').text(response.count_above_thirty_days_deficiency);
                    $('#total_claim_deficiency').text(response.total_claim_deficiency);
                    $('#count_5_deficiency_per').text(response.count_5_deficiency_per);
                    $('#count_six_to_ten_days_deficiency_per').text(response.count_six_to_ten_days_deficiency_per);
                    $('#count_eleven_to_fifteen_days_deficiency_per').text(response.count_eleven_to_fifteen_days_deficiency_per);
                    $('#count_sixteen_to_thirty_days_deficiency_per').text(response.count_sixteen_to_thirty_days_deficiency_per);
                    $('#count_above_thirty_days_deficiency_per').text(response.count_above_thirty_days_deficiency_per);
                    $('#total_per_pending_deficiency').text(response.total_per_pending_deficiency);
                    $('#count_cashless_claim_zero_to_one_hour').text(response.count_cashless_claim_zero_to_one_hour);
                    $('#count_cashless_claim_zero_to_one_hour_per').text(response.count_cashless_claim_zero_to_one_hour_per);
                    $('#diffe_benchmark_1').text(response.diffe_benchmark_1);
                    $('#count_cashless_claim_one_to_two_hour').text(response.count_cashless_claim_one_to_two_hour);
                    $('#count_cashless_claim_one_to_two_hour_per').text(response.count_cashless_claim_one_to_two_hour_per);
                    $('#diff_benchmark_2').text(response.diff_benchmark_2);
                    $('#count_cashless_claim_abovetwo_hour').text(response.count_cashless_claim_abovetwo_hour);
                    $('#count_cashless_claim_abovetwo_hour_per').text(response.count_cashless_claim_abovetwo_hour_per);
                    $('#diff_benchmark_3').text(response.diff_benchmark_3);
                    $('#from_date1').html(from_date);
                    $('#to_date1').html(to_date);
                    
                }
            });
        }
    });

    $("#btn_save_pdf").click(function() {
         var employer_id =  $('#employer_name').val();
        var insurer_id = $('#insurer_id').val();
        var TPA_ID = $('#TPA_ID').val();
        var policy_no = $('#policy_no').val();
          var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        if (employer_id == "")
        {
            swal("", "please select employer name");
            return false;
        } else if (from_date == "") {
            swal("", "please select from date");
            return false;
        } else if (to_date == "") {
            swal("", "please select to date");
            return false;
        } else {
            window.location = '/broker/monthly_claim_tracker_pdf_data?employer_id=' + employer_id + '&insurer_id=' + insurer_id + '&TPA_ID=' + TPA_ID + '&policy_no=' + policy_no + '&from_date=' + from_date + '&to_date=' + to_date;
        }
    });
    
})


