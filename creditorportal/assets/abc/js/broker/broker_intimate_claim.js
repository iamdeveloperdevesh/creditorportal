$(document).ready(function() {
    var d = new Date();
    var strDate = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
    var date = strDate.split("-");
    var display_date = date[1] + '-' + date[0] + '-' + date[2];
    $("#planned_date").datepicker({
        changeMonth: true,
        changeYear: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "dd-mm-yy",
        minDate: new Date(display_date)
    });
    $('body').on('focus', "#discharge_date", function() {
        var data = $("#planned_date").val();
        var date = data.split("-");
        var display_date = date[1] + '-' + date[0] + '-' + date[2];
        $(this).datepicker({
            changeMonth: true,
            changeYear: true,
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "dd-mm-yy",
            minDate: new Date(display_date)
        });
    });
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

    $("#employer_name").change(function() {
        var company_id = $(this).val();
        $.ajax({
            url: "/broker/get_all_employee_from_employer",
            type: "POST",
            data: {company_id: company_id},
            dataType: "json",
            success: function(response) {
                $('#emp_name').empty();
                $('#emp_name').append('<option value=""> Select Employee name</option>');
                for (i = 0; i < response.length; i++) {
                    $('#emp_name').append('<option value="' + response[i].emp_id + '">' + response[i].emp_firstname + ' ' + response[i].emp_lastname + '</option>');
                }
            }
        });
    });

    $("#emp_name").change(function() {
        var emp_id = $(this).val();
        $.ajax({
            url: "/broker/get_all_policy_no",
            type: "POST",
            data: {emp_id: emp_id},
            dataType: "json",
            success: function(response) {
                $('#policy_no').empty();
                $('#policy_no').append('<option value=""> Select Policy No</option>');
                for (i = 0; i < response.length; i++) {
//         	  	$('#policy_no').append('<option value="'+ response[i].policy_no +'">' + response[i].policy_no +'</option>');
                    if (response[i].policy_sub_type_name == 'Group Mediclaim')
                    {
                        $('#policy_no').append('<option selected class="active" value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
                    }
                }

                var policy_no = $("#policy_no").find("option:selected").val();
                var emp_id = $("#emp_name").val();
                var company_id = $("#employer_name").val();
                $.ajax({
                    url: "/broker/get_family_membername_from_policy_no",
                    type: "POST",
                    data: {emp_id: emp_id, policy_no: policy_no, company_id: company_id},
                    dataType: "json",
                    success: function(response) {
                        $('#patient_name').empty();
                        $('#patient_name').append('<option value=""> Select patient name</option>');
                        $.each(response, function(index, value) {
                            $('#patient_name').append('<option data-rel="' + value.family_id + '" value="' + value.emp_member_id + '">' + value.name + '</option>');
                        });
                    }
                });
            }
        });
    });


    // patient name change get all details
    $("#patient_name").on('change', function() {
        var patient_id = $(this).val();
        $.ajax({
            url: "/broker/get_member_details",
            type: "POST",
            data: {patient_id: patient_id},
            dataType: "json",
            async: true,
            success: function(response) {
                for (i = 0; i < response.length; i++) {
                    $('#email').val(response[i].email);
                    $('#mob_no').val(response[i].mob_no);
                }
            }
        });
    });

    $('.claim_Amount').keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    $("#doctor_name").keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });   // finally submit
    $("#patient_name").keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    $("#hospital_id").keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Z a-z\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });

    $('.claim_amount').keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        }
        return;
    });

    $('#mob_no').keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        }
        return;
    });

    $.validator.addMethod('valid_mobile', function(value, element, param) {
        var re = new RegExp('^[6-9][0-9]{9}$');
        return this.optional(element) || re.test(value); // Compare with regular expression
    }, 'Enter a valid 10 digit mobile number');


    $("#form_id").validate({
        rules: {
            patient_name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            remark: {
                required: true
            },
            planned_date: {
                required: true
            },
            mob_no: {
                required: true,
                valid_mobile: true
            },
            admitted_for: {
                required: true
            },
            claim_Amount: {
                required: true
            },
            hospital_id: {
                required: true
            },
        },
        messages: {
            patient_name: "Please specify patient name",
            email: "Please specify email id",
            email: "please enter valid email",
            remark: "Please specify remark",
            planned_date: "Please specify admission date",
            mob_no: "Please specify mobile no",
            admitted_for: "Please specify admitted for",
            claim_Amount: "Please specify claim amount",
            hospital_id: "Please specify hospital name",

        },
        submitHandler: function(form) {
            var all_data = $("#form_id").serialize() + '&data_override=' + 'NO';
            $.ajax({
                type: 'POST',
                url: '/broker/save_intimate_claim',
                data: all_data,
                success: function(result) {
                    var data_response = JSON.parse(result);
                    if (data_response.success) {
                        $("#getCodeModal").modal("toggle");
                        $("#getCode").html(data_response.success);
                        // window.location.reload(true);
                    } else if (data_response.success_new_claim)
                    {
                        var checkstr = confirm(data_response.success_new_claim);
                        if (checkstr == true)
                        {
                            var all_data = $("#form_id").serialize() + '&data_override=' + 'YES';
                            $.ajax({
                                type: 'POST',
                                url: '/broker/save_intimate_claim',
                                data: all_data,
                                success: function(result) {
                                    var data_response = JSON.parse(result);
                                    $("#getCodeModal").modal("toggle");
                                    $("#getCode").html(data_response.success);
                                    // window.location.reload(true);
                                }
                            });
                        } else
                        {
                            window.location.reload(true);
                        }
                    } else if (data_response.success_new_date_range)
                    {
                        $("#getCodeModal").modal("toggle");
                        $("#getCode").html(data_response.success_new_date_range);
                        // window.location.reload(true);
                    } else
                    {
                        $.each(data_response.messages, function(key, value) {
                            var element = $('#' + key);
                            element.closest('div.data').find('p').remove();
                            element.after(value);
                        });
                        // $("#getCodeModal").modal("toggle");
                        // $("#getCode").html('Some Error Has Been Occured');
                    }
                }
            });
        }
    });
    $("#data").click(function() {
        window.location.reload();
    });
    $("#cancelled").click(function() {
        window.location.reload();
    });

});