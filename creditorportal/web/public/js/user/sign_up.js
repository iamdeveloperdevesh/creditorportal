$(document).ready(function () {
    var pinCodeCheck = false;
    $(document).on("keyup", "#fullName", function () {
        if ($(this).val().match(/[^a-zA-Z ]/g)) {
            $(this).val($(this).val().replace(/[^a-zA-Z]/g, ""));
        }
    });

    jQuery.validator.addMethod("pinCodeCheck", function (value, element, param) {
        return pinCodeCheck;
    }, $.validator.format("Invalid Pincode."));

    jQuery.validator.addMethod("exactlength", function (value, element, param) {
        return this.optional(element) || value.length == param;
    }, $.validator.format("Please enter exactly {0} characters."));

    $.validator.addMethod('valid_mobile', function (value, element, param) {
        var re = new RegExp('^[6-9][0-9]{9}$');
        return this.optional(element) || re.test(value); // Compare with regular expression
    }, 'Enter a valid 10 digit mobile number');


    $('#basicDet').validate({
        errorElement: 'div',
        rules: {
            mobile_no: {
                number: true,
                required: true,
                exactlength: 10,
                valid_mobile: true
            },
            email: {
                validateEmail: true
            },
            fullName: {
                required: true,
            },
            pinCode: {
                required: true,
                number: true,
                exactlength: 6,
                pinCodeCheck: true
            }
        },
        submitHandler: function (form) {
            $('body').removeClass("loaded");
            let email = $('#email').val();
            let fullName = $('#fullName').val();
            let pinCode = $('#pinCode').val();
            let mobileNumber = $('#mobile_no').val();

            $.post("/saveBasicDet", {
                "email": email,
                "fullName": fullName,
                "pinCode": pinCode,
                "mobileNumber": mobileNumber
            }, function (e) {
                if (e == 1) {
                    $('#mbno').val($('#mobile_no').val());
                    $('#modal1').modal('open');
                } else if (e == "exists") {
                    swal({
                        title: "Mobile No or Email Already Exists",
                        text: "Please Login with your Account.",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        closeOnConfirm: false
                    },
                        function () {
                            location.href = "/";
                        });
                } else {
                    $('body').addClass("loaded");
                }
            });
        }
    });

    $('#otpSubmit').validate({
        errorElement: 'div',
        rules: {
            otp: {
                number: true,
                required: true,
                exactlength: 4
            }
        },
        submitHandler: function (form) {
            var mobileNumber = document.getElementById("mobile_no").value;
            var otp_code = document.getElementById("otp").value;


            $.post('/checkOtp', {
                "mobileNumber": mobileNumber,
                "otp_code": otp_code,
                "token": token
            }, function (e) {
                if (e == "4") {
                    swal({
                        title: "Alert",
                        text: "You have exceeded your limit of trying otp please login after 1 hour",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        closeOnConfirm: true
                    });
                }
                else if (e == "1") {
                    location.href = "/dashboard";
                } else if (e == "0") {
                    // Materialize.toast("OTP didn't Match!", 4000, 'rounded');
                    swal({
                        title: "Alert",
                        text: "OTP didn't Match!",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        closeOnConfirm: true
                    });
                } else {
                    swal({
                        title: "Alert",
                        text: "Something went wrong!",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        closeOnConfirm: true
                    });
                    // Materialize.toast("Something went wrong!", 4000, 'rounded');
                    swal({
                        title: "Alert",
                        text: "Something went wrong!",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        closeOnConfirm: true
                    });
                }
            });

        }
    });
    var pinCount = 0;
    $('#pinCode').on('keyup', function () {

        if (this.value.length == 6 && pinCount == 0) {
            ++pinCount;
            // $('.btn ').attr("disabled","disabled");
            $.post('/get_state_citySignUp', { "pincode": $('#pinCode').val() }, function (e) {
                if (e != 0) {
                    // $('.btn ').removeAttr("disabled");
                    pinCodeCheck = true;
                } else {
                    // Materialize.toast("Invalid Pincode!", 4000, 'rounded');
                    swal({
                        title: "Alert",
                        text: "Invalid Pincode!",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        closeOnConfirm: true
                    });
                    pinCodeCheck = false;
                }
            });
        }

        if (this.value.length < 6) {
            pinCount = 0;
        }
    });

    $('#pinCode').keyup(function (e) {
        // $('.btn ').attr("disabled","disabled");
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) { return ''; }));
        } return;
    });

    $(':input').on('focus', function () {
        $(this).attr('autocomplete', 'off');
    });

    function checkNumeric(e) {

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            var a = [];
            var k = e.which;

            for (i = 48; i < 58; i++)
                a.push(i);

            if (!(a.indexOf(k) >= 0)) {
                e.preventDefault();
            }
        }
        return true;
    }

    $('#mobile_no').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) { return ''; }));
        } return;
    });


    $.validator.addMethod('validateEmail', function (value, element, param) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        return reg.test(value); // Compare with regular expression
    }, 'Please enter a valid email address.');
});

$(document).ready(function () {
    $('.modal').modal({
        dismissible: false, // Modal can be dismissed by clicking outside of the modal
        opacity: .5, // Opacity of modal background
        inDuration: 300, // Transition in duration
        outDuration: 200, // Transition out duration
        startingTop: '4%', // Starting top style attribute
        endingTop: '10%'
    });
});