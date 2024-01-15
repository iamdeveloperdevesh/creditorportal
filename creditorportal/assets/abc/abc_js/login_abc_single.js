$(document).ready(function() {
    var isReferral = $('input[name="is_refferal"]:checked').val();
    var referralEmpIdBox = $('#referral_emp_id_box');
    var referralEmpIdBoxErr = $('#referral_emp_id-error');
    if(isReferral == 1){
        referralEmpIdBox.show();
    } else {
        referralEmpIdBox.hide();
        referralEmpIdBoxErr.hide();
    }
});

$("form#loginForm input[type=text]").attr("autocomplete", "off");

$("#bdate").datepicker({
    dateFormat: "dd-mm-yy",
    prevText: '<i class="fa fa-angle-left"></i>',
    nextText: '<i class="fa fa-angle-right"></i>',
    changeMonth: true,
    changeYear: true,
    maxDate: 0,
    yearRange: "-100: +0",
    onSelect: function (dateText, inst) {$(this).val(dateText);},
    onClose: function() {$(this).valid();},
});

$('.referral-radio-btn').on('change', function() {
    var isReferral = $(this).val();
    var referralEmpIdBox = $('#referral_emp_id_box');
    var referralEmpIdBoxErr = $('#referral_emp_id-error');
    var referralEmpId = $('#referral_emp_id');
    if(isReferral == 1){
        referralEmpIdBox.show();
    } else {
        referralEmpIdBox.hide();
        referralEmpIdBoxErr.hide();
        referralEmpId.val('');
    }
});

$.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || value == value.match(/^[a-zA-Z ]*$/);
}, "Please enter a valid name.");

$.validator.addMethod('validMobileNominee',function(value, element, param){
    var mobileInput = value;
    var validMobRe = new RegExp('^[6-9][0-9]{9}$');
    return this.optional(element) || validMobRe.test(mobileInput) && mobileInput.length > 0;
}, 'Enter valid mobile number');

$.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || value == value.match(/^[a-zA-Z0-9 ]*$/);
}, "Please enter a valid string.");

$('#loginForm').validate({
    rules: {
        salutation: {
            required: true,
        },
        first_name: {
            required: true,
            lettersonly: true
        },
        last_name: {
            required: true,
            lettersonly: true
        },
        mob_no: {
            required: true,
            validMobileNominee:true
        },
        bdate: {
            required: true
        },
        referral_emp_id: {
            required : function(element) {
                return ($('input[name="is_refferal"]:checked').val() == 1);
            },
            alphanumeric: true
        }
    },
    messages:{
        salutation:{
            required: "Salutation is required",
        },
        first_name:{
            required: "First name is required",
            lettersonly: "Enter only alphabates"
        },
        last_name:{
            required: "Last name is required",
            lettersonly: "Enter only alphabates"
        },
        mob_no:{
            required: "Mobile No is required"
        },
        bdate: {
            required: "DOB is required"
        },
        referral_emp_id: {
            required: "Employee ID is required",
            alphanumeric: "Enter alphanumeric only"
        }
    },
    submitHandler: function (form) { // for demo
        var form = $("#loginForm").serialize();
        ajaxindicatorstart();
        $.ajax({
            type: "POST",
            url: "/check_existing_journey",
            async: false,
            data: form,
            dataType: 'json',
            success: function (response) {
                ajaxindicatorstop();
                if(response.status == 'success'){
                    swal({
                        title: "Do you want to continue with previous journey?",
                        text: "Mobile number already exists!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, proceed!",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            ajaxindicatorstart();
                            $.ajax({
                                type: "POST",
                                url: "/submit_login_form_abc_single",
                                async: false,
                                data: form,
                                dataType: 'json',
                                success: function (response) {
                                    ajaxindicatorstop();
                                    if(response.status == 'success'){
                                        window.location.href = response.url;
                                    } else if(response.status == 'validation error'){
                                        swal("Alert",response.message, "warning");
                                    } else{
                                        swal("Alert","No URL found !!", "warning");
                                    }   
                                }
                            });
                        } else {
                            ajaxindicatorstop();    
                        }
                    });     
                } else {
                    ajaxindicatorstart();
                    $.ajax({
                        type: "POST",
                        url: "/submit_login_form_abc_single",
                        async: false,
                        data: form,
                        dataType: 'json',
                        success: function (response) {
                            ajaxindicatorstop();
                            if(response.status == 'success'){
                                window.location.href = response.url;
                            } else if(response.status == 'validation error'){
                                swal("Alert",response.message, "warning");
                            } else{
                                swal("Alert","No URL found !!", "warning");
                            }   
                        }
                    });
                }  
            }
        });
    }
});
$(document).on("click", "#submitBtn", function(){       
    $('#loginForm').valid();
});
   