$(document).ready(function() {
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
    // change on employer_name get policy type
    $("#employer_name").change(function() {
        var company_id = $(this).val();
        $.ajax({
            url: "/broker/get_policy_type",
            type: "POST",
            data: {company_id: company_id},
            dataType: "json",
            success: function(response) {
                $('#policy_type').empty();
                $('#policy_type').append('<option value=""> Select Policy Type</option>');
                for (i = 0; i < response.length; i++) {
                    $('#policy_type').append('<option value="' + response[i].policy_detail_id + '">' + response[i].policy_sub_type_name + '</option>');
                }
            }
        });
    });

    // change on policy type get policy no
    $("#policy_type").change(function() {
        var policy_type = $(this).val();
        $.ajax({
            url: "/broker/get_policy_no",
            type: "POST",
            data: {policy_type: policy_type},
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

                    var selectedDate = new Date(date);
                    var msecsInADay = 86400000;
                    var endDate = new Date(selectedDate.getTime() + msecsInADay);

                    //Set Minimum Date of EndDatePicker After Selected Date of StartDatePicker
                    $("#to_date").datepicker("option", "minDate", endDate);
                    // $("#endDatePicker").datepicker( "option", "maxDate", '+2y' );

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
    $("#btn_save").click(function() {
        var employer_id = $('#employer_name option:selected').val();
        var policy_type = $('#policy_type option:selected').val();
        var policy_no = $('#policy_no option:selected').val();
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
            window.location = '/broker/claim_excel_data?employer_id=' + employer_id + '&policy_no=' + policy_no + '&from_date=' + from_date + '&to_date=' + to_date;

        }
    });
    $("#btn_save_pdf").click(function() {
        var employer_id = $('#employer_name option:selected').val();
        var policy_type = $('#policy_type option:selected').val();
        var policy_no = $('#policy_no option:selected').val();
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
            window.location = '/broker/claim_pdf_data?employer_id=' + employer_id + '&policy_no=' + policy_no + '&from_date=' + from_date + '&to_date=' + to_date;
        }
    });

     $("#btn_submit").click(function() {
        var employer_id = $('#employer_name option:selected').val();
        var policy_type = $('#policy_type option:selected').val();
        var policy_no = $('#policy_no option:selected').val();
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
            $.ajax({
                url: "/show_all_claimsummary_report_data",
                type: "POST",
                data: {employer_id: employer_id,policy_type:policy_type,policy_no:policy_no,from_date:from_date,to_date:to_date},
                dataType: "json",
                success: function(response) {
                    $("#count_cashless").text(response.count_cashless);
                    $("#sum_cashless").text(response.sum_cashless);
                    $("#sum_incurred_cashless").text(response.sum_incurred_cashless);
                    $("#count_underquery").text(response.count_underquery);
                    $("#sum_underquery").text(response.sum_underquery);
                    $("#sum_incurred_underquery").text(response.sum_incurred_underquery);
                    $("#count_forsettlement").text(response.count_forsettlement);
                    $("#sum_forsettlement").text(response.sum_forsettlement);
                    $("#sum_incurred_forsettlement").text(response.sum_incurred_forsettlement);
                    $("#count_settled").text(response.count_settled);
                    $("#sum_settled").text(response.sum_settled);
                    $("#sum_incurred_settled").text(response.sum_incurred_settled);
                    $("#count_repudiated").text(response.count_repudiated);
                    $("#sum_repudiated").text(response.sum_repudiated);
                    $("#sum_incurred_repudiated").text(response.sum_incurred_repudiated);
                    $("#count_cancelled").text(response.count_cancelled);
                    $("#sum_cancelled").text(response.sum_cancelled);
                    $("#sum_incurred_cancelled").text(response.sum_incurred_cancelled);
                    $("#count_total").text(response.count_total);
                    $("#sum_total").text(response.sum_total);
                    $("#sum_incurred_total").text(response.sum_incurred_total);
                    $("#count_paid").text(response.count_paid);
                    $("#sum_paid").text(response.sum_paid);
                    $("#count_outstanding").text(response.count_outstanding);
                    $("#sum_outstanding").text(response.sum_outstanding);
                    $("#count_prorata").text(response.count_prorata);
                    $("#sum_prorata").text(response.sum_prorata);
                    $("#count_ICR").text(response.count_ICR);
                    $("#sum_ICR").text(response.sum_ICR);
                    $("#count_total_icr").text(response.count_total_icr);
                    $("#sum_total_icr").text(response.sum_total_icr);
                    $("#count_totalpremium").text(response.count_totalpremium);
                    $("#sum_totalpremium").text(response.sum_totalpremium);
                    $("#self_add").text(response.self_add);
                    $("#member_add").text(response.member_add);
                    $("#all_add").text(response.all_add);
                    $("#all_active_premium").text(response.all_active_premium);
                    $("#self_delete").text(response.self_delete);
                    $("#member_delete").text(response.member_delete);
                    $("#all_delete").text(response.all_delete);
                    $("#all_delete_premium").text(response.all_delete_premium);
                    $("#self_correction").text(response.self_correction);
                    $("#member_correction").text(response.member_correction);
                    $("#all_correction").text(response.all_correction);
                    $("#all_correction_premium").text(response.all_correction_premium);
                    $("#Internal_processing1").text(response.Internal_processing1);
                    $("#External_Processing1").text(response.External_Processing1);
                    $("#Overall_TAT1").text(response.Overall_TAT1);
                    $("#internal_process_per1").text(response.internal_process_per1);
                    $("#external_process_per1").text(response.external_process_per1);
                    $("#overall_tat_per1").text(response.overall_tat_per1);
                    $("#Internal_processing2").text(response.Internal_processing2);
                    $("#External_Processing2").text(response.External_Processing2);
                    $("#Overall_TAT2").text(response.Overall_TAT2);
                    $("#internal_process_per2").text(response.internal_process_per2);
                    $("#external_process_per2").text(response.external_process_per2);
                    $("#overall_tat_per2").text(response.overall_tat_per2);
                    $("#Internal_processing3").text(response.Internal_processing3);
                    $("#External_Processing3").text(response.External_Processing3);
                    $("#Overall_TAT3").text(response.Overall_TAT3);
                    $("#internal_process_per3").text(response.internal_process_per3);
                    $("#external_process_per3").text(response.external_process_per3);
                    $("#overall_tat_per3").text(response.overall_tat_per3);
                    $("#Internal_processing4").text(response.Internal_processing4);
                    $("#External_Processing4").text(response.External_Processing4);
                    $("#Overall_TAT4").text(response.Overall_TAT4);
                    $("#internal_process_per4").text(response.internal_process_per4);
                    $("#external_process_per4").text(response.external_process_per4);
                    $("#overall_tat_per4").text(response.overall_tat_per4);
                    $("#Internal_processing5").text(response.Internal_processing5);
                    $("#External_Processing5").text(response.External_Processing5);
                    $("#Overall_TAT5").text(response.Overall_TAT5);
                    $("#internal_process_per5").text(response.internal_process_per5);
                    $("#external_process_per5").text(response.external_process_per5);
                    $("#overall_tat_per5").text(response.overall_tat_per5);
                     $("#Internal_processing6").text(response.Internal_processing6);
                    $("#External_Processing6").text(response.External_Processing6);
                    $("#Overall_TAT6").text(response.Overall_TAT6);
                }
            });
        }
     });
});


