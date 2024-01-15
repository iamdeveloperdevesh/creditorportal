var eValue = '';
var son_age = 0;
function getgender(e) {
    eValue = e.value;
    if (eValue == 2) {
        $(".gender").val("Male");
    } else if (eValue == 3) {
        $(".gender").val("Female");
    } else if (eValue == 4) {
        $(".gender").val("Male");
    } else if (eValue == 5) {
        $(".gender").val("Female");
    } else if (eValue == 6) {
        $(".gender").val("Female");
    } else if (eValue == 7) {
        $(".gender").val("Male");
    }
}
$(document).ready(function () {
   
   //get all policy
$.ajax({
            url: "/employer/get_all_policy",
            type: "POST",
            async: false,
            dataType: "json",
            success: function (response) {
                console.log(response);
                for (i = 0; i < response.length; i++) {
                    if(response[i].policy_sub_type_id == 2 || response[i].policy_sub_type_id == 3){
                    $(".add_membertab").attr("style","display:none" );
                     $("#v-pills-home").attr("style","display:none" );
                      $("#v-pills-home1").addClass('active show');
                      $("#v-pills-tabContent").addClass('active show');
                      $(".add_membertab").removeClass("active" );
                     $("#v-pills-home").removeClass("active" );
                 }
                    
                }
            }


        }); 
   
   
   
   
   

// onchange of policy number
    $('#policy_numbers').on('change', function (e) {
        var pollicy_no = $('#policy_numbers').val();
        var policy_subtype_id = $('option:selected', this).attr('id');

        $.ajax({
            url: "/employer/get_employee_per_policy",
            type: "POST",
            data: {policy_subtype_id: policy_subtype_id, pollicy_no:pollicy_no},
            async: false,
            dataType: "json",
            success: function (response) {
                $('#emp_detail').empty();
                $('#emp_detail').append('<option value=""> Select employee name</option>');
                for (i = 0; i < response.length; i++) {
                    $('#emp_detail').append('<option value="' + response[i].emp_id + '">' + response[i].emp_firstname + '</option>');
                }
            }


        });

        var policy_type_id = $('option:selected', this).attr('id');
        /*if (policy_type_id == 2 || policy_type_id == 3 || policy_type_id == 4 || policy_type_id == 5 || policy_type_id == 6) {
         $("#members").css('display', 'none');
         getPrice();
         //$("#members").css('display','none');
         } else {
         $("#members").css('display', 'block');
         }; */
        $("#policy_detail").val(pollicy_no);
    });

//    wallet _balance display
    $('#emp_detail').on('change', function (e) {
        var emp_id = $(this).val();
        $.ajax({
            url: "/employer/get_wallet_balance",
            type: "POST",
            data: {emp_id: emp_id},
            async: false,
            dataType: "json",
            success: function (response) {
                if (response[0].flex_amount == 0)
                {
                    $("#wallet_ut").val(response[0].alloted_amount);
                } else
                {
                    $("#wallet_ut").val(response[0].flex_amount);
                }

                $("#pay_ut").val(response[0].pay_amount);
            }
        });
    });

    /* function get_dob(){	 
     var mdate=  $("input[name='s_date']").val().split("-");
     startDate = new Date(Number(mdate[2]), Number(mdate[1]) - 1, Number(mdate[0]));
     startDate.setMonth(startDate.getMonth() + 9);
     return startDate;
     } */
    $('.dob').datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        onSelect: function (dateText, inst)
        {
            $("input[name=dis]:checked").prop("checked", false);
            $(this).val(dateText);
            get_age_family(dateText, eValue);
        }
    });
    $(document).on('change', '#family_member_relation', function () {
        selectChange = $(this).closest(".row");
        getgender(this);
        var parent_id = $(this).val();
        var emp_id = $("#emp_detail").val();
        var $family_date_birth = $(".dob");
        $family_date_birth.val('');
        $.ajax({
            url: "/employer/get_min_max_age",
            type: "POST",
            data: {rel_id: $("#family_member_relation").find("option:selected").val(), policy_id: $("#policy_numbers option:selected").val()},
            dataType: "json",
            success: function (response) {
                if ($("#family_members_id").val() == 2 || $("#family_members_id").val() == 3)
                {
                    if (response.max_age == 0)
                    {
                        $family_date_birth.datepicker("option", "yearRange", "-100:+0");
                        $family_date_birth.datepicker("option", "minDate", "-100Y");
                        $family_date_birth.datepicker("option", "maxDate", "0d");
                    } else
                    {
                        $family_date_birth.datepicker("option", "yearRange", "-" + response.max_age + ":+0");
                        $family_date_birth.datepicker("option", "minDate", "-" + response.max_age + "Y");
                        $family_date_birth.datepicker("option", "maxDate", "0d");
                    }
                } else
                {
                    if (response.min_age == 0 && response.max_age == 0)
                    {
                        $family_date_birth.datepicker("option", "yearRange", "-100:-18");
                        $family_date_birth.datepicker("option", "minDate", "-100Y");
                        $family_date_birth.datepicker("option", "maxDate", "-18Y");
                    } else
                    {
                        $family_date_birth.datepicker("option", "yearRange", "-" + response.max_age + ":-" + response.min_age);
                        $family_date_birth.datepicker("option", "minDate", "-" + response.max_age + "Y");
                        $family_date_birth.datepicker("option", "maxDate", "-" + response.min_age + "Y");
                    }
                }
            }
        });
        $.ajax({
            type: 'POST',
            url: '/employer/policy_parent_data',
            data: {parent_id: parent_id, emp_id: emp_id},
            dataType: "json",
            success: function (result) {
                if ((result.employee_contri) > 0 && result.employee_contri != 0 && result.flex_allocate != '' && result.payroll_allocate != '')
                {
                    $("#payment_parent_wrapper").removeAttr('style', 'display:none');
                    $("#premium_parent").val(result.premium);
                    $("#contri_parent").val(result.employee_contri);
                } else {
                    $("#premium_parent").val(result.premium);
                    $("#contri_parent").val(result.employee_contri);
                }
            }
        });
    });




// Date picker for nominee dob
    $("#nominee_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:+0",
        maxDate: new Date(),
        minDate: "-100Y +1D"
    });
// validation for add members
    $("#first_name").keyup(function (e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z ]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $("#last_name").keyup(function (e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z ]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    // validation for nominee section//
    $('body').on("keyup", ".nominee_fname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".nominee_lname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".nominee_relation", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z-]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_relation", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z-]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", ".share_per", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", ".nominee_firstname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", ".rel_nominee", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", ".nominee_lastname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_name", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_rel", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z-]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_fname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_lname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
// Start of on load append policy number in add member into policy
    $.ajax({
        url: "/employer/get_all_policy_numbers",
        type: "POST",
        async: false,
        dataType: "json",
        //data: {"employer": "true", type: 1},
        data: {"employer": "true"},
        success: function (response) {
            console.log(response);
            $('#policy_numbers').empty();
            $('#policy_numbers').append('<option value=""> Select policy Type</option>');
            $('#policy_detail').empty();
            $('#policy_detail').append('<option value=""> Select policy Type</option>');
            for (i = 0; i < response.length; i++) {

                $('#policy_numbers').append('<option value="' + response[i].policy_detail_id + '" id="' + response[i].policy_sub_type_id + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                $('#policy_detail').append('<option value="' + response[i].policy_detail_id + '">' + response[i].policy_sub_type_name + '</option>');

            }
        }
    });
// Start of on load append employee id  in add member into policy
    $.ajax({
        url: "/get_all_emp_no",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            $('#emp_id').append('<option value=""> Select employee name</option>');
            for (i = 0; i < response.length; i++) {

                $('#emp_id').append('<option value="' + response[i].emp_id + '">' + response[i].emp_firstname + '</option>');
            }
        }
    });
// Start of on load append member relation  in add member into policy
    /* $.ajax({
     url: "/get_all_member_relation",
     type: "POST",
     async: false,
     dataType: "json",
     success: function (response) {
     
     $('#family_member_relation').empty();
     $('#family_member_relation').append('<option value=""> Select Members</option>');
     for (i = 0; i < response.length; i++) {
     $('#family_member_relation').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
     }
     }
     }); */
// Start of on load append member relation  in add member into policy
    /*  $.ajax({
     url: "/get_mem_realtion_nominee",
     type: "POST",
     async: true,
     dataType: "json",
     success: function (response) {
     $('#nominee_relation').empty();
     $('#nominee_relation').append('<option value=""> Select Members</option>');
     for (i = 0; i < response.length; i++) { 
     $('#nominee_relation').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
     }
     }         
     });
     */

    $('#cancl').on("click", function () {
        $('#member_form')[0].reset();
    });

    $('#cancl2').on("click", function () {
        $('#member_form_nominee')[0].reset();
    });
    $("#data_final_parent").click(function () {
        var flex_amount = $('#wallet_parent').text();
        var pay_amount = $('#pay_parent').text();
        var employee_contri = $("#contri_parent").val();

        if (employee_contri != 0) {
            var premium = (parseInt($("#premium_parent").val()) * employee_contri / 100);
        } else {
            var premium = $("#premium_parent").val();
        }
        var formData = new FormData();
        var upload_document = $('#upload_document').val();
        formData.append("upload_document", upload_document);
        if (upload_document) {
            formData.append('upload_document_id', $('#upload_document').get(0).files[0]);
        } else {
            formData.append("upload_document_id", "");
        }
        formData.append("policy_numbers", $('#policy_numbers').val());
        formData.append("emp_detail", $('#emp_detail').val());
        formData.append("family_member_relation", $('#family_member_relation').val());
        formData.append("first_name", $('#first_name').val());
        formData.append("last_name", $('#last_name').val());
        formData.append("dob", $('#dob').val());
        formData.append("gender", $('#gender').val());
        formData.append("family_id", $('#family_id').val());
        formData.append("salary", $('#salary').val());
        formData.append("pay", $('#pay').val());
        formData.append("marriage_date", $('#marriage_date').val());
        formData.append("s_date", $('#s_date').val());
        formData.append("disable_child", $("input[name=dis]:checked").val());
        formData.append("deduction_type", "F,S");
        formData.append("flex_amount", flex_amount);
        formData.append("pay_amount", pay_amount);
        formData.append("final_amount", premium);
        $.ajax({
            type: 'POST',
            url: "/employer/get_family_details",
            data: formData,

            contentType: false,
            processData: false,
            success: function (response) {

                var data_response = JSON.parse(response);
                var v = data_response.details;
                if (v.status == "Adults") {

                    swal({
                        title: "Warning",
                        text: v.message,
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });

                    return;
                } else if (v.status == "Empty") {
                    swal({
                        title: "Warning",
                        text: "Please upload file",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });

                    return;
                }
                if (v.status == "Same child") {
                    swal({
                        title: "Warning",
                        text: "Same Child Already added",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });
                    return;
                }
                if (v.status == "max_child_limit") {
                    swal({
                        title: "Alert",
                        text: "Maximum Child Limit Exceeded!",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true,
                        allowOutsideClick: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true,
                        allowEscapeKey: false
                    },
                            function ()
                            {
                            });
                    return;
                }
                if (v.status == "2 added") {
                    swal({
                        title: "Warning",
                        text: "You can not add more than 2 adults",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });
                    return;
                }
                if (v.status == "4 added") {
                    swal({
                        title: "Warning",
                        text: "You can not add more than 4 adults",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });
                    return;
                }
                if (v.status == "incorrect Family") {
                    swal({
                        title: "Warning",
                        text: "You can not add more than 4 adults",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });
                    return;
                } else if (v.status == "Same child") {
                    swal({
                        title: "Warning",
                        text: "Same Child Already added",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });
                    return;
                }
                if (v == 'false') {
                    swal({
                        title: "alert",
                        text: "Sorry You can not add more",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });

                    return;
                }
                if (data_response.details == false) {
                    swal({
                        title: "Warning",
                        text: "Sorry You can not add more",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });
                    return;
                } else if (data_response.details.status == "enroll1") {

                    swal({
                        title: "Warning",
                        text: "Enrollment window is closed for kids",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });

                    return;
                } else if (data_response.details.status == "error") {
                    swal({
                        title: "Warning",
                        text: "Maximum Child Limit Exceeded!",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });
                    return;
                } else if (data_response.details.status == "enroll") {

                    swal({
                        title: "Warning",
                        text: "Enrollment window is closed for spouse",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });


                    return;
                } else if (data_response.details.status == "enroll2") {

                    swal({
                        title: "Warning",
                        text: "Enrollment window is closed",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                            function ()
                            {
                                location.reload();
                            });

                    return;
                } else if (data_response.details.status == "age_difference_error") {
                    swal({
                        title: "Alert",
                        text: "Employee and family member age difference must be atleast 18 years",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true,
                        allowOutsideClick: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true,
                        allowEscapeKey: false
                    },
                            function ()
                            {
                            });
                    return;
                }


                if (data_response.details == "true") {
                    swal("", "Submitted Successfully");
                    $("#v-pills-home").removeClass('active show');
                    $("#v-pills-home-tab").removeClass('active');
                    $("#v-pills-profile").addClass('active show');
                    $("#v-pills-profile-tab").addClass('active ');

                    $('#policy_detail').attr("style", "pointer-events: none;");

                } else {

                    $.each(data_response.messages, function (key, value) {
                        var element = $('#' + key);
                        element.closest('div.data').find('.error').remove();
                        element.after(value);
                    });

                }
            }
        });
//        submit_member("F,S", flex_amount, pay_amount,premium);
    });


    $("#member_form").validate({

        rules: {
            policy_numbers: {
                required: true
            },

            emp_detail: {
                required: true
            },
            salutation: {
                required: true
            },
            family_member_relation:{
                required: true  
            },
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            dob: {
                required: true
            },
            gender: {
                required: true
            }
        },
        messages: {
            policy_numbers: "Please select policy number",
            emp_detail: "Please select employee number",
            salutation: "Please Select salutation",
            first_name: "please enter valid name",
            last_name: "Please enter valid name",
            dob: "Please specify DOB date",
            gender: "Please select gender",
            family_member_relation: "Please select family member",
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            var formData = new FormData();

            var upload_document = $('#upload_document').val();
            formData.append("upload_document", upload_document);
            if (upload_document) {
                formData.append('upload_document_id', $('#upload_document').get(0).files[0]);
            } else {
                formData.append("upload_document_id", "");
            }

            formData.append("policy_numbers", $('#policy_numbers').val());
            formData.append("emp_detail", $('#emp_detail').val());
            formData.append("family_member_relation", $('#family_member_relation').val());
            formData.append("first_name", $('#first_name').val());
            formData.append("last_name", $('#last_name').val());
            formData.append("dob", $('#dob').val());
            formData.append("gender", $('#gender').val());
            formData.append("family_id", $('#family_id').val());
            formData.append("salary", $('#salary').val());
            formData.append("pay", $('#pay').val());
            formData.append("marriage_date", $('#marriage_date').val());
            formData.append("s_date", $('#s_date').val());
            formData.append("disable_child", $("input[name=dis]:checked").val());

            if ($("#payment_parent_wrapper").is(":visible"))
            {

                var selected_val = $("input[name='dc_type']:checked").val();
                var employee_contri = $("#contri_parent").val();
                if (employee_contri != 0)
                {
                    var premium = (parseInt($("#premium_parent").val()) * employee_contri / 100);
                } else
                {
                    var premium = $("#premium_parent").val();
                }
                var wallet_bal = parseInt($("#wallet_ut").val());

                if (selected_val !== undefined)
                {

                    if (selected_val == 'F')
                    {

                        if (premium > wallet_bal && wallet_bal != 0)
                        {

                            $('#wallet_parent').html("<i class='fa fa-inr'></i> " + wallet_bal);
                            var cut_amt = parseInt(premium - wallet_bal);
                            $('#pay_parent').html("<i class='fa fa-inr'></i> " + cut_amt);
                            $("#getCodeModal_parent").modal("toggle");
                        } else if (wallet_bal == 0 && premium > wallet_bal)
                        {

                            $("#getCodeModal").modal("toggle");
                            $("#getCode").html('Flex balance is not enough');
                            return false;
                        } else
                        {
                            formData.append("deduction_type", "F");
                            formData.append("flex_amount", premium);
                            formData.append("pay_amount", "");
                            formData.append("final_amount", premium);
                            $.ajax({
                                type: 'POST',
                                url: "/employer/get_family_details",
                                data: formData,

                                contentType: false,
                                processData: false,
                                success: function (response) {

                                    var data_response = JSON.parse(response);

                                    var v = data_response.details;
                                    if (v.status == "Adults") {

                                        swal({
                                            title: "Alert",
                                            text: v.message,
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });

                                        return;
                                    } else if (v.status == "Empty") {
                                        swal({
                                            title: "Alert",
                                            text: "Please upload file",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });

                                        return;
                                    }
                                    if (v.status == "Same child") {
                                        swal({
                                            title: "Alert",
                                            text: "Same Child Already added",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });
                                        return;
                                    }
                                    if (v.status == "max_child_limit") {
                                        swal({
                                            title: "Alert",
                                            text: "Maximum Child Limit Exceeded!",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true,
                                            allowOutsideClick: false,
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true,
                                            allowEscapeKey: false
                                        },
                                                function ()
                                                {
                                                });
                                        return;
                                    }


                                    if (v.status == "2 added") {
                                        swal({
                                            title: "Alert",
                                            text: "You can not add more than 2 adults",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });
                                        return;
                                    }
                                    if (v.status == "4 added") {
                                        swal({
                                            title: "Alert",
                                            text: "You can not add more than 4 adults",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });
                                        return;
                                    }
                                    if (v.status == "incorrect Family") {
                                        swal({
                                            title: "Alert",
                                            text: "You can not add more than 4 adults",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });
                                        return;
                                    } else if (v.status == "Same child") {
                                        swal({
                                            title: "Alert",
                                            text: "Same Child Already added",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });
                                        return;
                                    }

                                    if (v == 'false') {
                                        swal({
                                            title: "Alert",
                                            text: "Sorry You can not add more",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });

                                        return;
                                    }

                                    if (data_response.details == false) {
                                        swal({
                                            title: "Alert",
                                            text: "Sorry You can not add more",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });
                                        return;
                                    } else if (data_response.details.status == "error") {
                                        swal({
                                            title: "Alert",
                                            text: "Maximum Child Limit Exceeded!",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });
                                        return;
                                    } else if (data_response.details.status == "enroll1") {

                                        swal({
                                            title: "Alert",
                                            text: "Enrollment window is closed for kids",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });

                                        return;
                                    } else if (data_response.details.status == "enroll") {

                                        swal({
                                            title: "Alert",
                                            text: "Enrollment window is closed for spouse",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });


                                        return;
                                    } else if (data_response.details.status == "enroll2") {

                                        swal({
                                            title: "Alert",
                                            text: "Enrollment window is closed",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                                function ()
                                                {
                                                    location.reload();
                                                });

                                        return;
                                    } else if (data_response.details.status == "age_difference_error") {
                                        swal({
                                            title: "Alert",
                                            text: "Employee and family member age difference must be atleast 18 years",
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true,
                                            allowOutsideClick: false,
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true,
                                            allowEscapeKey: false
                                        },
                                                function ()
                                                {
                                                });
                                        return;
                                    }


                                    if (data_response.details == "true") {
                                        swal("", "Submitted Successfully");
                                        $("#v-pills-home").removeClass('active show');
                                        $("#v-pills-home-tab").removeClass('active');
                                        $("#v-pills-profile").addClass('active show');
                                        $("#v-pills-profile-tab").addClass('active ');

                                        $('#policy_detail').attr("style", "pointer-events: none;");

                                    } else {

                                        $.each(data_response.messages, function (key, value) {
                                            var element = $('#' + key);
                                            element.closest('div.data').find('.error').remove();
                                            element.after(value);
                                        });

                                    }
                                }
                            });
                        }
                    } else
                    {
                        formData.append("deduction_type", "S");
                        formData.append("flex_amount", "");
                        formData.append("pay_amount", premium);
                        formData.append("final_amount", premium);
                        $.ajax({
                            type: 'POST',
                            url: "/employer/get_family_details",
                            data: formData,

                            contentType: false,
                            processData: false,
                            success: function (response) {

                                var data_response = JSON.parse(response);
                                var v = data_response.details;
                                if (v.status == "Adults") {

                                    swal({
                                        title: "Warning",
                                        text: v.message,
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });

                                    return;
                                } else if (v.status == "Empty") {
                                    swal({
                                        title: "Warning",
                                        text: "Please upload file",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });

                                    return;
                                }
                                if (v.status == "Same child") {
                                    swal({
                                        title: "Warning",
                                        text: "Same Child Already added",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });
                                    return;
                                }
                                if (v.status == "max_child_limit") {
                                    swal({
                                        title: "Alert",
                                        text: "Maximum Child Limit Exceeded!",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true,
                                        allowOutsideClick: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        dangerMode: true,
                                        allowEscapeKey: false
                                    },
                                            function ()
                                            {
                                            });
                                    return;
                                }


                                if (v.status == "2 added") {
                                    swal({
                                        title: "Warning",
                                        text: "You can not add more than 2 adults",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });
                                    return;
                                }
                                if (v.status == "4 added") {
                                    swal({
                                        title: "Warning",
                                        text: "You can not add more than 4 adults",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });
                                    return;
                                }
                                if (v.status == "incorrect Family") {
                                    swal({
                                        title: "Warning",
                                        text: "You can not add more than 4 adults",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });
                                    return;
                                } else if (v.status == "Same child") {
                                    swal({
                                        title: "Warning",
                                        text: "Same Child Already added",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });
                                    return;
                                }

                                if (v == 'false') {
                                    swal({
                                        title: "alert",
                                        text: "Sorry You can not add more",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });

                                    return;
                                }
                                if (data_response.details == false) {
                                    swal({
                                        title: "Warning",
                                        text: "Sorry You can not add more",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });
                                    return;
                                } else if (data_response.details.status == "error") {
                                    swal({
                                        title: "Warning",
                                        text: "Maximum Child Limit Exceeded!",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });
                                    return;
                                } else if (data_response.details.status == "enroll1") {

                                    swal({
                                        title: "Warning",
                                        text: "Enrollment window is closed for kids",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });

                                    return;
                                } else if (data_response.details.status == "enroll") {

                                    swal({
                                        title: "Warning",
                                        text: "Enrollment window is closed for spouse",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });


                                    return;
                                } else if (data_response.details.status == "enroll2") {

                                    swal({
                                        title: "Warning",
                                        text: "Enrollment window is closed",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                            function ()
                                            {
                                                location.reload();
                                            });

                                    return;
                                } else if (data_response.details.status == "age_difference_error") {
                                    swal({
                                        title: "Alert",
                                        text: "Employee and family member age difference must be atleast 18 years",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true,
                                        allowOutsideClick: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        dangerMode: true,
                                        allowEscapeKey: false
                                    },
                                            function ()
                                            {
                                            });
                                    return;
                                }


                                if (data_response.details == "true") {
                                    swal("", "Submitted Successfully");
                                    $("#v-pills-home").removeClass('active show');
                                    $("#v-pills-home-tab").removeClass('active');
                                    $("#v-pills-profile").addClass('active show');
                                    $("#v-pills-profile-tab").addClass('active ');

                                    $('#policy_detail').attr("style", "pointer-events: none;");

                                } else {

                                    $.each(data_response.messages, function (key, value) {
                                        var element = $('#' + key);
                                        element.closest('div.data').find('.error').remove();
                                        element.after(value);
                                    });

                                }
                            }
                        });

                    }

                } else
                {
                    swal({
                        title: "Alert",
                        text: "Please Select Deduction Type",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true,
                        allowOutsideClick: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true,
                        allowEscapeKey: false
                    },
                            function ()
                            {
                                //location.reload();
                            });
                    return;
                }
            } else
            {
                formData.append("deduction_type", "");
                formData.append("flex_amount", "");
                formData.append("pay_amount", "");
                formData.append("final_amount", "");
                $.ajax({
                    type: 'POST',
                    url: "/employer/get_family_details",
                    data: formData,

                    contentType: false,
                    processData: false,
                    success: function (response) {

                        var data_response = JSON.parse(response);
                        var v = data_response.details;
                        if (v.status == "Adults") {

                            swal({
                                title: "Alert",
                                text: v.message,
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });

                            return;
                        } else if (v.status == "Empty") {
                            swal({
                                title: "Alert",
                                text: "Please upload file",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });

                            return;
                        }
                        if (v.status == "Same child") {
                            swal({
                                title: "Alert",
                                text: "Same Child Already added",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });
                            return;
                        }
                        if (v.status == "max_child_limit") {
                            swal({
                                title: "Alert",
                                text: "Maximum Child Limit Exceeded!",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true,
                                allowOutsideClick: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false,
                                dangerMode: true,
                                allowEscapeKey: false
                            },
                                    function ()
                                    {
                                    });
                            return;
                        }
                        if (v.status == "error") {
                            swal({
                                title: "Alert",
                                text: "Maximum Child Limit Exceeded!",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true,
                                allowOutsideClick: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false,
                                dangerMode: true,
                                allowEscapeKey: false
                            },
                                    function ()
                                    {
                                    });
                            return;
                        }

                        if (v.status == "policy_not_available") {

                            swal({
                                title: "Alert",
                                text: v.message,
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });

                            return;
                        }


                        if (v.status == "2 added") {
                            swal({
                                title: "Warning",
                                text: "You can not add more than 2 adults",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });
                            return;
                        }
                        if (v.status == "4 added") {
                            swal({
                                title: "Warning",
                                text: "You can not add more than 4 adults",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });
                            return;
                        }
                        if (v.status == "incorrect Family") {
                            swal({
                                title: "Warning",
                                text: "You can not add more than 4 adults",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });
                            return;
                        } else if (v.status == "Same child") {
                            swal({
                                title: "Warning",
                                text: "Same Child Already added",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });
                            return;
                        }
                        if (v == 'false') {
                            swal({
                                title: "alert",
                                text: "Sorry You can not add more",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });

                            return;
                        }
                        if (data_response.details == false) {
                            swal({
                                title: "Warning",
                                text: "Sorry You can not add more",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });
                            return;
                        } else if (data_response.details.status == "enroll1") {

                            swal({
                                title: "Warning",
                                text: "Enrollment window is closed for kids",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });

                            return;
                        } else if (data_response.details.status == "enroll") {

                            swal({
                                title: "Warning",
                                text: "Enrollment window is closed for spouse",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });


                            return;
                        } else if (data_response.details.status == "enroll2") {

                            swal({
                                title: "Warning",
                                text: "Enrollment window is closed",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                                    function ()
                                    {
                                        location.reload();
                                    });

                            return;
                        }


                        if (data_response.details == "true") {
                            swal("", "Submitted Successfully");
                            $("#v-pills-home").removeClass('active show');
                            $("#v-pills-home-tab").removeClass('active');
                            $("#v-pills-profile").addClass('active show');
                            $("#v-pills-profile-tab").addClass('active ');

                            $('#policy_detail').attr("style", "pointer-events: none;");

                        } else {

                            $.each(data_response.messages, function (key, value) {
                                var element = $('#' + key);
                                element.closest('div.data').find('.error').remove();
                                element.after(value);
                            });

                        }
                    }
                });
            }
//$.ajax({
//   type: 'POST',
//                url: "/employer/get_family_details",
//                data: formData,
//
//                contentType: false,
//                processData: false,
//                success: function (response) {	
//
//         var data_response = JSON.parse(response);
//var v = data_response.details;
//if (v.status == "Adults"){
// 
//swal({
//                        title: "Warning",
//                        text: v.message,
//                        type: "warning",
//                        showCancelButton: false,
//                        confirmButtonText: "Ok!",
//                        closeOnConfirm: true
//                    },
//                    function ()
//                    {
//                         location.reload();
//                    }); 
//
//return;
//}
//else if(v.status == "Empty"){
//swal({
//                        title: "Warning",
//                        text: "Please upload file",
//                        type: "warning",
//                        showCancelButton: false,
//                        confirmButtonText: "Ok!",
//                        closeOnConfirm: true
//                    },
//                    function ()
//                    {
//                         location.reload();
//                    }); 
//
//return;
//                    }
//                    if(v.status =="Same child"){
//                        swal({
//                                                title: "Warning",
//                                                text: "Same Child Already added",
//                                                type: "warning",
//                                                showCancelButton: false,
//                                                confirmButtonText: "Ok!",
//                                                closeOnConfirm: true
//                                            },
//                                            function ()
//                                            {
//                                                 location.reload();
//                                            });
//                        return;
//                        }
//                       
//                        
//                        if(v.status =="2 added"){
//                        swal({
//                                                title: "Warning",
//                                                text: "You can not add more than 2 adults",
//                                                type: "warning",
//                                                showCancelButton: false,
//                                                confirmButtonText: "Ok!",
//                                                closeOnConfirm: true
//                                            },
//                                            function ()
//                                            {
//                                                 location.reload();
//                                            });
//                        return;
//                        }
//                        if(v.status =="4 added"){
//                        swal({
//                                                title: "Warning",
//                                                text: "You can not add more than 4 adults",
//                                                type: "warning",
//                                                showCancelButton: false,
//                                                confirmButtonText: "Ok!",
//                                                closeOnConfirm: true
//                                            },
//                                            function ()
//                                            {
//                                                 location.reload();
//                                            });
//                        return;
//                        }
//                        if(v.status =="incorrect Family"){
//                        swal({
//                                                title: "Warning",
//                                                text: "You can not add more than 4 adults",
//                                                type: "warning",
//                                                showCancelButton: false,
//                                                confirmButtonText: "Ok!",
//                                                closeOnConfirm: true
//                                            },
//                                            function ()
//                                            {
//                                                 location.reload();
//                                            });
//                        return;
//                        }
//
//
//
//
//
//else if(v.status =="Same child"){
//                       swal({
//                        title: "Warning",
//                        text: "Same Child Already added",
//                        type: "warning",
//                        showCancelButton: false,
//                        confirmButtonText: "Ok!",
//                        closeOnConfirm: true
//                    },
//                    function ()
//                    {
//                         location.reload();
//                    });
//return;
//}
//if(v == 'false'){
// swal({
//                        title: "alert",
//                       text: "Sorry You can not add more",
//                        type: "warning",
//                        showCancelButton: false,
//                        confirmButtonText: "Ok!",
//                        closeOnConfirm: true
//                    },
//                    function ()
//                    {
//                         location.reload();
//                    });	
//
//return;
//}
//         if(data_response.details == false){
//              swal({
//                        title: "Warning",
//                       text: "Sorry You can not add more",
//                        type: "warning",
//                        showCancelButton: false,
//                        confirmButtonText: "Ok!",
//                        closeOnConfirm: true
//                    },
//                    function ()
//                    {
//                         location.reload();
//                    });	
//return;
//}
//     else if (data_response.details.status == "enroll1"){
// 
//                         swal({
//                        title: "Warning",
//                       text: "Enrollment window is closed for kids",
//                        type: "warning",
//                        showCancelButton: false,
//                        confirmButtonText: "Ok!",
//                        closeOnConfirm: true
//                    },
//                    function ()
//                    {
//                         location.reload();
//                    });	
//
//return;
//}
// else if (data_response.details.status == "enroll"){
// 
// swal({
//                        title: "Warning",
//                       text: "Enrollment window is closed for spouse",
//                        type: "warning",
//                        showCancelButton: false,
//                        confirmButtonText: "Ok!",
//                        closeOnConfirm: true
//                    },
//                    function ()
//                    {
//                        location.reload();
//                    }); 
//
//
//return;
//}
//else if (data_response.details.status == "enroll2"){
// 
//swal({
//                        title: "Warning",
//                        text: "Enrollment window is closed",
//                        type: "warning",
//                        showCancelButton: false,
//                        confirmButtonText: "Ok!",
//                        closeOnConfirm: true
//                    },
//                    function ()
//                    {
//                         location.reload();
//                    }); 
//
//return;
//}
//
//
//        if(data_response.details == "true") {
//          swal("","Submitted Successfully");
// $("#v-pills-home").removeClass('active show');
// $("#v-pills-home-tab").removeClass('active');
// $("#v-pills-profile").addClass('active show');
//    	   $("#v-pills-profile-tab").addClass('active ');
//
//$('#policy_detail').attr("style", "pointer-events: none;");
//
//    }
//        else {
//         
//                            $.each(data_response.messages, function(key, value){
//                            var element = $('#' + key);
//                            element.closest('div.data').find('.error').remove();
//                            element.after(value);
//                        });
//
//        }
// }    
//   }); 
        }
    });


    $('#submit_nominee').on("click", function () {
        var family_members_idArray = {};
        var total = 0;
        var status = true;
        $('#member_form_nominee .form_row_data1').each(function (key, value) {
            if (Number($(this).find('.share_per').val()) == 0) {
                status = false;
            }
        });
        if (!status)
        {
            swal("", "Share percentage can not be zero");
            return false;
        } else
        {
            status = true;
            $('#member_form_nominee .form_row_data1').each(function (key, value) {
                total += Number($(this).find('.share_per').val());
                if ($(this).find('.nominee_relation').val() == '')
                {
                    ($(this).find('.nominee_relation')).after("<span style='color:red;'>family member is required</span>");
                    //location.reload();
                    status = false;
                }

                if ($(this).find('.nominee_fname').val() == '')
                {
                    // alert('nominee_fname');
                    ($(this).find('.nominee_fname')).after("<span style='color:red;'>First name is required</span>");
                    //location.reload();
                    status = false;
                }

                if ($(this).find('.nominee_lname').val() == '')
                {
                    ($(this).find('.nominee_lname')).after("<span style='color:red;'>Last name is required</span>");
                    //location.reload();
                    status = false;
                }

                if ($(this).find('.nominee_dob').val() == '')
                {
                    ($(this).find('.nominee_dob')).after("<span style='color:red;'>D.O.B is required</span>");
                    //location.reload();
                    status = false;
                }

                if ($(this).find('.share_per').val() == '')
                {
                    ($(this).find('.share_per')).after("<span style='color:red;'>Share is required</span>");
                    //location.reload();
                    status = false;
                } else
                {
                    family_members_idArray["form" + key] = {
                        "family_members_idArr": $(this).find('.nominee_relation').val(),
                        "first_nameArr": $(this).find('.nominee_fname').val(),
                        "last_nameArr": $(this).find('.nominee_lname').val(),
                        "family_date_birthArr": $(this).find('.nominee_dob').val(),
                        "share_perArr": $(this).find('.share_per').val(),
                        "guardian_relationArr": $(this).find('.guardian_relation').val(),
                        "guardian_fnameArr": $(this).find('.guardian_fname').val(),
                        "guardian_lnameArr": $(this).find('.guardian_lname').val(),
                        "guardian_dobArr": $(this).find('.guardian_dob').val()
                    };
                }

            });
            if (status) {
                $.ajax({
                    url: "/employer/get_share_per_nominee",
                    type: "POST",
                    data: {emp_id: $("#emp_id").val()},
                    dataType: "json",
                    success: function (response1) {
                        if (response1 != 0)
                        {
                            var balance = parseInt(100 - response1);
                            if (balance == 0 && total == 0)
                            {
                                swal({
                                    title: "Alert",
                                    text: "Nominees Share % Cannot Exceed 100%",
                                    type: "warning",
                                    showCancelButton: false,
                                    confirmButtonText: "Ok!",
                                    closeOnConfirm: true,
                                    allowOutsideClick: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true,
                                    allowEscapeKey: false
                                },
                                        function ()
                                        {
//                                        location.reload();
                                        });
                                return;
                            } else if (balance == total)
                            {
                                $.ajax({
                                    url: "/employer/add_nominee_data",
                                    type: "post",
                                    data: {family_members_idArray: family_members_idArray, emp_id: $("#emp_id").val()},
                                    dataType: "json",
                                    success: function (response) {
                                        if (response.msg == true) {
                                            //alert(1);
                                            swal({
                                                title: "Success",
                                                text: "Submitted Successfully",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
                                                        location.reload();
                                                    });
                                        } else if (response.status == 'error') {
                                            swal({
                                                title: "Warning",
                                                text: "Please Fill All Nominee And Guardian Details",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
                                                        // location.reload();
                                                    });
                                        } else if (response.status == 'error1') {
                                            swal({
                                                title: "Warning",
                                                text: "You cannot add nominee until employee is not enrolled",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
                                                        // location.reload();
                                                    });
                                        } else if (response.status == 'error2') {
                                            swal({
                                                title: "Warning",
                                                text: "You cannot add 0 share percentage",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
                                                        //                                     location.reload();
                                                    });
                                        } else
                                        {
                                            swal({
                                                title: "Warning",
                                                text: "Nominees Share % Cannot Exceed 100%",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
//                                                                location.reload();
                                                    });
                                        }
                                    }
                                });
                            } else if (balance == 0)
                            {
                                swal({
                                    title: "Warning",
                                    text: "Nominees Share % Cannot Exceed 100%",
                                    type: "warning",
                                    showCancelButton: false,
                                    confirmButtonText: "Ok!",
                                    closeOnConfirm: true,
                                    allowOutsideClick: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true,
                                    allowEscapeKey: false
                                },
                                        function ()
                                        {
//                                                    location.reload();
                                        });
                                return;
                            } else
                            {
                                swal({
                                    title: "Warning",
                                    text: "Share percent should be " + balance + "%",
                                    type: "warning",
                                    showCancelButton: false,
                                    confirmButtonText: "Ok!",
                                    closeOnConfirm: true,
                                    allowOutsideClick: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true,
                                    allowEscapeKey: false
                                },
                                        function ()
                                        {
//                                                    location.reload();
                                        });
                                return;
                            }
                        } else
                        {
                            if (total > 100 || total < 100)
                            {
                                swal({
                                    title: "Warning",
                                    text: "Share percent should be 100%",
                                    type: "warning",
                                    showCancelButton: false,
                                    confirmButtonText: "Ok!",
                                    closeOnConfirm: true,
                                    allowOutsideClick: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true,
                                    allowEscapeKey: false
                                },
                                        function ()
                                        {
                                            //location.reload();
                                        });
                                return;
                            } else
                            {
                                $.ajax({
                                    url: "/employer/add_nominee_data",
                                    type: "post",
                                    data: {family_members_idArray: family_members_idArray, emp_id: $("#emp_id").val()},
                                    dataType: "json",
                                    success: function (response) {
                                        if (response.msg == true) {
                                            //alert(1);
                                            swal({
                                                title: "Success",
                                                text: "Submitted Successfully",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
                                                        location.reload();
                                                    });
                                        } else if (response.status == 'error') {
                                            swal({
                                                title: "Warning",
                                                text: "Please Fill All Nominee And Guardian Details",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
                                                        // location.reload();
                                                    });
                                        } else if (response.status == 'error1') {
                                            swal({
                                                title: "Warning",
                                                text: "You cannot add nominee until employee is not enrolled",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
                                                        // location.reload();
                                                    });
                                        } else if (response.status == 'error2') {
                                            swal({
                                                title: "Warning",
                                                text: "You cannot add 0 share percentage",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
                                                        //                                     location.reload();
                                                    });
                                        } else
                                        {
                                            swal({
                                                title: "Warning",
                                                text: "Nominees Share % Cannot Exceed 100%",
                                                type: "warning",
                                                showCancelButton: false,
                                                confirmButtonText: "Ok!",
                                                closeOnConfirm: true,
                                                allowOutsideClick: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true,
                                                allowEscapeKey: false
                                            },
                                                    function ()
                                                    {
//                                                                 location.reload();
                                                    });
                                        }
                                    }
                                });
                            }
                        }
                    }
                });

            }

        }
    });
//$("#member_form_nominee").validate({
//   
//  rules: {
//    policy_detail: {
//      required: true
//    },
//emp_id: {
//      required: true
//    },
//nominee_relation: {
//      required: true
//    },
//nominee_dob: {
//      required: true
//    },
//nominee_fname: {
//        required:true
//    },
//nominee_lname: {
//      required: true
//    }
//  },
//  messages: {
//    policy_numbers: "Please select policy number",
//    emp_id: "Please select employee",
//    nominee_relation: "Please select nominee relation type",
//nominee_dob: "Please specify birth date",
//nominee_fname: "please enter valid name",
//nominee_lname: "Please enter valid name",
//  },
//   errorElement : 'div',
//        errorPlacement: function(error, element) {
//        var placement = $(element).data('error');
//        if (placement) {
//            $(placement).append(error)
//        } else {
//            error.insertAfter(element);
//        }
//    },
//   submitHandler: function(form) {
//   var share = $("#share_per").val();
//   if(share >100 || share <100) {
//   swal("","Share percent should be 100%");
//       return;
//   }  
//    $.ajax({
//                url: "/employer/add_nominee_member",
//                type: "POST",
//                async: false,
//                data:$("#member_form_nominee").serialize(),
//                success: function (response) {
//                	var data_response = JSON.parse(response);
//            if(data_response.nominee_detail == true){
//          swal("Submitted Successfully");
//    	  $("#v-pills-home1").removeClass('active show');
//    	 $("#v-pills-profile").addClass('active show');
//    	 $("#v-pills-home1-tab").removeClass('active');
//    	 $("#v-pills-profile-tab").addClass('active ');
//           }
//   else if(data_response.nominee_detail == false){
//  swal("","You can not add more nominee");  
//   }
//               else {
//                 $.each(data_response.messages, function(key, value){
//                            var element = $('#' + key);
//                            element.closest('div.data').find('.error').remove();
//                            element.after(value);
//                        });
//               }
//}
//            }); 
//   }
//});
// onchange of member id shows data
    $(document).on('change', '#family_member_relation', function () {
        selectChange = $(this).closest(".row");
        var emp_id = $("#emp_detail").val();

        $.ajax({
            url: "/home/get_family_details_from_relationship_employer",
            type: "POST",
            data: {
                "emp_id": emp_id,
                "relation_id": $(this).val()
            },
            async: false,
            dataType: "json",
            success: function (response) {
                $("#first_name").val("");
                $("#last_name").val("");
                $("#gender").val("");
                $("input[type='text'][name='dob']").val("");
                ////console.log(response);
                if (response.length != 0) {
                    if (response[0].fr_id == "2" || response[0].fr_id == "3") {
                        $("#body_modal").html("");
                        for ($i = 0; $i < response.length; $i++) {
                            $("#body_modal").append('<input type="radio" name ="radio_option" value= ' + response[$i]['family_id'] + '> ' + response[$i].family_firstname + '<br>');
                        }
                        $("#myModal").modal();
                    } else if (response[0].family_id == "0") {
                        $("#first_name").val(response[0].emp_firstname);
                        $("#last_name").val(response[0].emp_lastname);
                        $("#gender").val(response[0].gender);
                        $("#family_id").val(response[0].family_id);
                        $("input[type='text'][name='dob']").val(response[0].bdate);
                        $("input[name='marriage_date']").val(response[0].marriage_date);
                    } else {

                        $("#first_name").val(response[0].family_firstname);
                        $("#last_name").val(response[0].family_lastname);
                        $("#gender").val(response[0].family_gender);
                        $("#family_id").val(response[0].family_id);
                        $("input[type='text'][name='dob']").val(response[0].family_dob);
                        $("input[name='marriage_date']").val(response[0].marriage_date);
                    }
                }
            }
        });
    });
    $(document).on('click', '#modal-submit', function () {
        if ($("input[name='radio_option']:checked").val() == undefined) {
            swal("please select at least one member");
            return false;
        }
        $.ajax({
            url: "/get_individual_family_details",
            type: "POST",
            data: {
                "family_id": $("input[name='radio_option']:checked").val()
            },
            async: false,
            dataType: "json",
            success: function (response) {
                ////console.log(response);
                ////console.log(response.constructor);
                if (response.length != 0) {
                    selectChange.find("#first_name").val(response.family_firstname);
                    selectChange.find("#last_name").val(response.family_lastname);
                    selectChange.find("input[type='text'][name='dob']").val(response.family_dob);
                    selectChange.find("#family_id").val(response.family_id);
                    selectChange.find("#gender").val(response.family_gender);
                    get_age_family(response.family_dob, eValue);
                }
                $("#myModal").modal("hide");
            }
        });
    });


    // onclick ob radio shows upload

    $(document).on('change', 'input[type="radio"]', function () {
        if (this.name != 'radio_option' && this.name != 'child_kid_modal') {
            var selectedValue = $("input[name='" + this.name + "']:checked").val();
            if (selectedValue == 'disable_child')
            {
                $('#disable_child').prop("checked", true);
                $('#unmarried_child').prop("checked", false);
                //$(this).closest("div .disable").append('<input type="file" name="upload_document" id="upload_document"><img src="" id="schild" name="" style="display:none;width:200px; height:100px">');
                $(".disable_file_wrapper").css("display", "block");
            } else {
                $('#unmarried_child').prop("checked", true);
                $('#disable_child').prop("checked", false);
                //$("input[name$='upload_document'], #schild").css("display", "none");
                $(".disable_file_wrapper").css("display", "none");
            }
        }
    });
    $(document).on('change', '#emp_detail', function () {

        getPrice();
        var emp_id = $("#emp_detail").val();

        /*  ////console.log(emp_id);       
         $.ajax({
         url: "/employer/get_emp_data",
         type: "POST",
         data: {
         "emp_id": emp_id,
         },
         async: true,
         dataType: "json",
         success: function (response) {
         $("#first_name").val("");
         $("#last_name").val("");
         $("#gender").val("");
         $("input[type='text'][name='dob']").val(""); 
         ////console.log(response);
         ////console.log(response.bdate);
         if(response.length != 0){
         if(subtype_id!=1){
         
         $("#first_name").val(response. emp_firstname);
         $("#last_name").val(response.emp_lastname);
         $("#gender").val(response.gender);
         $("#dob").val(response.bdate);
         $("#salary").val(response. total_salary);
         $("#pay").val(response.emp_pay);
         }
         }
         
         
         }
         }); */
        $.ajax({
            url: "/get_smallest_date_employer",
            type: "POST",
            data: {
                "emp_id": emp_id
            },
            async: false,
            dataType: "json",
            success: function (response) {
                son_age = response.date.split("-");
                son_age = new Date(Number(son_age[2]), Number(son_age[1]) - 1, Number(son_age[0]))
                son_age.setYear(son_age.getFullYear() + 18);

                parent_age = response.date.split("-");
                parent_age = new Date(Number(parent_age[2]), Number(parent_age[1]) - 1, Number(parent_age[0]))
                parent_age.setYear(parent_age.getFullYear() - 18)

            }
        });

        /* $.ajax({
         url: "/employer/get_all_per_sdob",
         type: "POST",
         data: {
         "emp_id": emp_id
         
         },
         async: true,
         dataType: "json",
         success: function (response) {
         ////console.log(response);
         
         if(response.length!=0){
         
         $("input[name='s_date']").val(response.family_dob);
         }
         
         }             
         }); */
    });


    function get_age_family(e, data = '') {
        var emp_id = $("#emp_detail").val();
        var today = new Date();
        var dob = new Date(e);
        var z = e.split("-");
        var dob = new Date(z[2], z[1] - 1, z[0]);
        var today_mon = today.getMonth();
        var dob_mon = dob.getMonth();
        var dob_day = dob.getDate();
        var today_day = today.getDate();


        var age_distance = (today.getFullYear() - dob.getFullYear());
        $.ajax({
            url: "/employer/check_dis_unm_child",
            type: "POST",
            dataType: "json",
            data: {
                "emp_id": emp_id
            },
            success: function (response) {
                //////console.log(response);
                var dis = response.special_child_check;
                var unm = response.unmarried_child_check;
                /*if (dis == 1 && unm == 1 && age_distance >= 25 && (data == 2 || data == 3))
                 {
                 selectChange.find(('div .disable')).removeAttr('style', 'display:none');
                 selectChange.find(('div .unmarried')).removeAttr('style', 'display:none');
                 selectChange.find(('div .unmarried')).find('.unmarried_child').prop("checked", true);
                 } else {
                 selectChange.find(('div .disable')).attr('style', 'display:none');
                 selectChange.find(('div .unmarried')).attr('style', 'display:none');
                 selectChange.find(('div .unmarried')).find('.unmarried_child').prop("checked", false);
                 }*/

                if (dis == 1 && unm == 1 && age_distance >= 25 && (data == 2 || data == 3))
                {
                    selectChange.find(('div .disable')).removeAttr('style', 'display:none');
                    selectChange.find(('div .unmarried')).removeAttr('style', 'display:none');
                    selectChange.find(('div .unmarried')).find('.unmarried_child').prop("checked", true);
                    $("#disable_child_wrapper").removeAttr('style', 'display:none');
                } else {
                    selectChange.find(('div .disable')).attr('style', 'display:none');
                    selectChange.find(('div .unmarried')).attr('style', 'display:none');
                    selectChange.find(('div .unmarried')).find('.unmarried_child').prop("checked", false);
                    $("#disable_child_wrapper").attr('style', 'display:none');
                }
            }
        });
        if (today_mon >= dob_mon && today_day >= dob_day) {

            if (age_distance > 0)
            {
                /* selectChange.find("input[name=age]").val(age_distance);
                 selectChange.find("input[name=age_type]").val('years'); */

            } else
            {
                var month = '' + (today.getMonth() + 1);

                if (month.length < 2)
                {
                    var strDate = '0' + today.getDate() + "-" + '0' + month + "-" + today.getFullYear();
                }
                var date = strDate.split("-");
                var display_date = date[2] + '-' + date[1] + '-' + date[0];
                var date = e.split("-");
                var date_bday = date[2] + '-' + date[1] + '-' + date[0];
                var day = (Date.parse(display_date) - Date.parse(date_bday));
                var days = Math.floor(day / (1000 * 60 * 60 * 24));
                /* selectChange.find("input[name=age]").val(days);
                 selectChange.find("input[name=age_type]").val('days'); */
            }
        } else {

            if (age_distance > 1)
            {
                /* selectChange.find("input[name=age]").val(age_distance-1);
                 selectChange.find("input[name=age_type]").val('years'); */

            } else if (age_distance == 1) {
                var month = '' + (today.getMonth() + 1);

                if (month.length < 2)
                {
                    var strDate = '0' + today.getDate() + "-" + '0' + month + "-" + today.getFullYear();
                }
                var date = strDate.split("-");
                var display_date = date[2] + '-' + date[1] + '-' + date[0];
                var date = e.split("-");
                var date_bday = date[2] + '-' + date[1] + '-' + date[0];
                var day = (Date.parse(display_date) - Date.parse(date_bday));
                var days = Math.floor(day / (1000 * 60 * 60 * 24));
                /* selectChange.find("input[name=age]").val(days);
                 selectChange.find("input[name=age_type]").val('days'); */
            } else
            {
                var month = '' + (today.getMonth() + 1);

                if (month.length < 2)
                {
                    var strDate = '0' + today.getDate() + "-" + '0' + month + "-" + today.getFullYear();
                }
                var date = strDate.split("-");
                var display_date = date[2] + '-' + date[1] + '-' + date[0];
                var date = e.split("-");
                var date_bday = date[2] + '-' + date[1] + '-' + date[0];
                var day = (Date.parse(display_date) - Date.parse(date_bday));
                var days = Math.floor(day / (1000 * 60 * 60 * 24));
                /* selectChange.find("input[name=age]").val(days);
                 selectChange.find("input[name=age_type]").val('days'); */
            }

    }


    }

    function getPrice() {

        var subtype_id = $("#policy_numbers").val();
        var emp_id = $("#emp_detail").val();
        ////console.log(emp_id);
        $.ajax({
            url: "/employer/get_emp_data",
            type: "POST",
            data: {
                "emp_id": emp_id,
                "subtype_id": subtype_id,
            },
            async: false,
            dataType: "json",
            success: function (response) {

                $("#first_name").val("");
                $("#last_name").val("");
                $("#gender").val("");
                $("input[type='text'][name='dob']").val("");
                var response1 = response.get_emp_data;
                var response_mem = response.getemp_member_ic;

                if (response1.length != 0) {
                    if (subtype_id != 1) {

                        $("#first_name").val(response1[0].emp_firstname);
                        $("#last_name").val(response1[0].emp_lastname);
                        $("#gender").val(response1[0].gender);
                        $("#dob").val(response1[0].bdate);
                        $("#salary").val(response1[0].total_salary);
                        $("#pay").val(response1[0].emp_pay);
                    }
                }
                if (response_mem.length != 0) {
                    $("#family_member_relation").empty();
                    $('#family_member_relation').append('<option value=""> Select Members</option>');
                    for (i = 0; i <= response_mem.length; i++) {
                        $('#family_member_relation').append('<option value="' + response_mem[i].fr_id + '">' + response_mem[i].fr_name + '</option>');
                    }
                }
            }
        });
    }

//$(document).on('focus', '#dob',  function(e){
//
//if(eValue==2){
//
//$("#dob").datepicker({
//        dateFormat: "dd-mm-yy",
//        prevText: '<i class="fa fa-angle-left"></i>',
//        nextText: '<i class="fa fa-angle-right"></i>',
//        changeMonth: true,
//        changeYear: true,
//        yearRange: son_age.getYear()+":",
//        minDate:   son_age,
//maxDate: new Date(),
//onSelect: function (dateText, inst) 
//{
//
//$(this).val(dateText);
//get_age_family(dateText,eValue);
//}
//    });
//}
//else if(eValue==3){
//
//$("#dob").datepicker({
//        dateFormat: "dd-mm-yy",
//        prevText: '<i class="fa fa-angle-left"></i>',
//        nextText: '<i class="fa fa-angle-right"></i>',
//        changeMonth: true,
//        changeYear: true,
//        yearRange: son_age.getYear()+":",
//        minDate:   son_age,
//maxDate: new Date(),
//onSelect: function (dateText, inst) 
//{
//
//$(this).val(dateText);
//get_age_family(dateText,eValue);
//}
//    });
//}
//
//else {
//
//$("#dob").datepicker({
//        dateFormat: "dd-mm-yy",
//        prevText: '<i class="fa fa-angle-left"></i>',
//        nextText: '<i class="fa fa-angle-right"></i>',
//        changeMonth: true,
//        changeYear: true,
//         yearRange: "-100Y:-18Y",
//        maxDate: "-18Y",
//        minDate: "-100Y:-18Y",
//onSelect: function (dateText, inst) 
//{
//
//$(this).val(dateText);
//get_age_family(dateText,eValue);
//}
//    });
//}
//});

    $("#emp_id").on('change', function () {
        if ($(this).val() != '')
        {
            $("#member_form_nominee").removeAttr('style', 'display:none;');
        } else
        {
            $("#member_form_nominee").attr('style', 'display:none;');
        }
    });

    $(document).on('click', '.nominee_dob', function (e) {
        $(this)
                .removeClass('hasDatepicker')
                .removeData('datepicker')
                .unbind()
                .datepicker({
                    dateFormat: "dd-mm-yy",
                    prevText: '<i class="fa fa-angle-left"></i>',
                    nextText: '<i class="fa fa-angle-right"></i>',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-100Y:+0",
                    maxDate: new Date(),
                    minDate: "-100Y +1D",

                    onSelect: function (dateText, inst) {
                        // debugger;
                        selectChange = $(this).closest(".row");
                        $(this).val(dateText);
                        var nominee_bday = dateText.split("-");
                        var today = new Date();
                        var age_distance = (today.getFullYear() - nominee_bday[2]);
                        selectChange.find(".guardian_div").html("");
                        if (age_distance < 18)
                        {
                            selectChange.append('<div class="col-md-12 guardian_div"><h6 class="color-1 mt-5"> Guardian Details </h6> <div class="row"> <div class="col-lg-3"> <div class="form-group"> <label class="col-form-label">Relation With Nominee</label> <input class="form-control guardian_relation" type="text" value="" id="guardian_relation" autocomplete="off" name="guardian_relation"> </div> </div> <div class="col-lg-2"> <div class="form-group"> <label for="example-text-input" class="col-form-label">First Name</label> <input class="form-control guardian_fname" type="text" value="" id="guardian_fname" autocomplete="off" name="guardian_fname"> </div> </div> <div class="col-lg-2"> <div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name</label> <input class="form-control guardian_lname" type="text" autocomplete="off" value="" id="guardian_lname" name="guardian_lname"> </div> </div> <div class="col-lg-2"> <div class="form-group"> <label for="example-date-input" class="col-form-label">Date Of Birth </label> <input class="form-control guardian_dob" type="" autocomplete="off" name="guardian_dob" readonly="readonly"> </div> </div> </div> </div>');
                        } else
                        {
                            selectChange.find(".guardian_div").html("");
                        }
                    }
                });
        $(this).datepicker('show');
//$("a .ui-datepicker-prev").click();
    });

    $(document).on('click', '.guardian_dob', function (e) {
        $(this).removeClass("hasDatepicker");
// $(this).removeAttr("id");
        $(this).datepicker({
            dateFormat: "dd-mm-yy",
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100Y:-18Y",
            maxDate: "-18Y",
            minDate: "-100Y:-18Y",
            onSelect: function (dateText, inst) {
                $(this).val(dateText);
            }
        });
        $(this).datepicker('show');
    });

    $('#cancl2').on("click", function () {
        $('#member_form_nominee')[0].reset();
    });

    var max_fields = 5; //maximum input boxes allowed
    var wrapper = $(".wrapper");
    var add_button = $(".add_more_form");

    var x = 1;
    add_button.click(function (e) {
        var family_date_birth = $("input[name=nominee_dob]").val();

//$(".nominee_relation option[value= "+z+"]").remove();
        var first_name = $("input[name=nominee_fname]").val();
        var last_name = $("input[name=nominee_lname]").val();

        if (family_date_birth == '' || first_name == '' || last_name == '') {
            swal("", "Please First fill the form");

        } else {
            e.preventDefault();
            if (x < max_fields) {
                x++;


                var clone = $(".form_row_data1:first").clone().find(".guardian_div").html("").end().find("span").text("").end().find("input[type='text'] ").val("").end().find(".nominee_dob").attr("id", "").removeClass('hasDatepicker').removeData('datepicker').unbind().end().append('<a  href="#" class="remove_field">Remove</a>');

                wrapper.append(clone);

            }

        }
    });

    wrapper.on("click", ".remove_field", function (e) {
        e.preventDefault();
        $(this).parent('div').remove();
        x--;
    });

});
