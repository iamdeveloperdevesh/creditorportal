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
//   GET policy against employer and insuer
    $("#insurer_id").change(function() {
        var company_id = $('#employer_name').val();
        var insurer_id = $(this).val();
        $.ajax({
            url: "/get_all_policy_against_company_and_insurer",
            type: "POST",
            data: {company_id: company_id, insurer_id: insurer_id},
            dataType: "json",
            success: function(response) {
                $('#policy_no').empty();
                $('#policy_no').append('<option value=""> Select Policy</option>');
                for (i = 0; i < response.length; i++) {
                    $('#policy_no').append('<option value="' + response[i].policy_no + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
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

    $('#export_excel').on('click', function() {
        if (employer_name == "") {
            swal("", "please select employer name");
            return false;
        } else if (insurer_id == "") {
            swal("", "please select insurer name");
            return false;
        } else if (policy_no == "") {
            swal("", "please select policy no");
            return false;
        } else if (from_date == "") {
            swal("", "please select from date");
            return false;
        } else if (to_date == "") {
            swal("", "please select to date");
            return false;
        } else {
            window.location = '/broker/get_member_report_endorsement_from_policy_no?employer_name=' + $('#employer_name option:selected').val() + '&insurer_id=' + $('#insurer_id option:selected').val() + '&policy_no=' + $('#policy_no option:selected').val() + '&from_date=' + $("#from_date").val() + '&to_date=' + $("#to_date").val();
        }
//            var employer_name =  $('#employer_name option:selected').val();
//            var insurer_id =  $('#insurer_id option:selected').val();
//            var policy_no =  $('#policy_no option:selected').val();
//            var from_date = $("#from_date").val();
//            var to_date = $("#to_date").val();
//            if(employer_name == ""){
//                 swal("","please select employer name");
//                 return false;
//            }
//            if(insurer_id == ""){
//                 swal("","please select insurer name");
//                 return false;
//            }
//            if(policy_no == ""){
//                 swal("","please select policy no");
//                 return false;
//            }
//            if(from_date == ""){
//                 swal("","please select from date");
//                 return false;
//            }
//            if(to_date == ""){
//                 swal("","please select to date");
//                 return false;
//            }
//            $.ajax({
//                url: "/broker/get_member_report_enrolled_from_policy_no",
//                type: "POST",
//                async: false,
//                data:{employer_name: employer_name,insurer_id:insurer_id,policy_no : policy_no,to_date:to_date,from_date :from_date},
//                dataType: "json",
//                success: function (response) {
//                    console.log(response);
//                }
//            });
    });
});


