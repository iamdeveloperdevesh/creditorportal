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
    $('#submit_data').on('click', function() {
        var employer_name = $('#employer_name option:selected').val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        if (employer_name == "") {
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
                url: "/get_all_renewal_policy",
                type: "POST",
                async: false,
                data: {employer_name: employer_name, to_date: to_date, from_date: from_date},
                dataType: "json",
                success: function(response) {
                    var data_str = '';
                    $("#tbody_data").html("");
                    for (i = 0; i < (response).length; i++) {
                        if (response[i].all_data[0]['policy_no'] != null) {
                            if (response[i].all_data[0]['policy_sub_type_name'] == 'Group Mediclaim') {
                                if (response[i].policy_member != '')
                                {
                                    var loss_ratio = ((response[i].policy_member[0]['total_claim'] / response[i].all_data[0]['sum_premium']) * 100);
                                    var final_loss_ratio = loss_ratio.toFixed(2);
                                }else{
									var final_loss_ratio = 0;
								}
                            } else {
                                var final_loss_ratio = 0;
                            }
                            data_str += '<tr>';
                            data_str += '<td><div class="form-group"><input class="policy_no" type="text" value="' + response[i].all_data[0]['policy_no'] + '" id="policy_no" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-190" type="text" value="' + response[i].all_data[0]['policy_sub_type_name'] + '" id="policy_sub_type_name" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-190" type="text" value="' + response[i].all_data[0]['insurer_name'] + '" id="insurer_name" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-190" type="text" value="' + response[i].all_data[0]['TPA_name'] + '" id="TPA_name" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-190" type="text" value="' + response[i].all_data[0]['comapny_name'] + '" id="comapny_name" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-130" type="text" value="' + response[i].all_data[0]['start_date'] + '" id="start_date" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-130" type="text" value="' + response[i].all_data[0]['end_date'] + '" id="end_date" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-130" type="text" value="' + response[i].all_data[0]['sum_insured'] + '" id="sum_insured" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-190" type="text" value="' + response[i].all_data[0]['sum_premium'] + '" id="sum_premium" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-80" type="text" value="' + response[i].all_data[0]['id'] + '" id="member_id" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-80" type="text" value="' + final_loss_ratio + '%" id="member_id" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><input class="form-control align-center wd-80" type="text" value="80" id="member_id" readonly=""></div></td>';
                            data_str += '<td><div class="form-group"><button type="button" class="btn sub-btn no-radius-btn" data-policyno = "' + response[i].all_data[0]['policy_no'] + '" data-policytype = "' + response[i].all_data[0]['policy_sub_type_name'] + '" data-company = "' + response[i].all_data[0]['comapny_name'] + '" data-policystart = "' + response[i].all_data[0]['start_date'] + '" data-policyend= "' + response[i].all_data[0]['end_date'] + '"  id="submit_data_pdf">Submit</button></div></td>';
                            data_str += '</tr>';
                        }
                    }
                    $("#tbody_data").html(data_str);
                }
            });
        }
    });
    $(document).on('click', '#submit_data_pdf', function(event) {
        var policy_no = $(this).attr("data-policyno");
        var policytype = $(this).attr("data-policytype");
        var company = $(this).attr("data-company");
        var policystart_date = $(this).attr("data-policystart");
        var policyend_date = $(this).attr("data-policyend");
        window.location = '/broker/get_pdf_renewal?policy_no=' + policy_no + '&policytype=' + policytype + '&company=' + company + '&policystart_date=' + policystart_date + '&policyend_date=' + policyend_date;
    });
});

