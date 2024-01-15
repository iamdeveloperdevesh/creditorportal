
$('#submitOtp').on("click", function () {
    var mobileNumber = document.getElementById("mobileNumber").value;
    var otp_code = document.getElementById("otp").value;

    var re = new RegExp('^[6-9][0-9]{9}$');
    if(!re.test( mobileNumber )) {
        // Materialize.toast("Invalid Mobile Number!", 4000, 'rounded');
        swal({
            title: "Alert",
            text: "Invalid Mobile Number!",
            type: "warning",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ok",
            closeOnConfirm: true
        });
        return;
    }

    $.post('/checkOtp', {
        "mobileNumber": mobileNumber,
        "otp_code": otp_code,
        "token": token
    }, function (e) {
        if(e == "4") {
            swal({
                title: "Alert",
                text: "You have exceeded your limit of trying otp please login after 1 hour",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
        }else if (e == "2") {
            location.href = "/admin/home";
        } else if (e == "1") {
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
});

function sendOtp(msg) {
    if (msg == 1) {
        $('#otpMsg').show();
        /* Resend Otp only after 3 minutes */
        setTimeout(checkSession(), 120000);

    } else {
        checkSession();
    }
}

function checkSession() {
    var mobileNumber = document.getElementById("mobileNumber").value;

    var re = new RegExp('^[6-9][0-9]{9}$');
    if(!re.test( mobileNumber )) {
        // Materialize.toast("Invalid Mobile Number!", 4000, 'rounded');
        swal({
            title: "Alert",
            text: "Invalid Mobile Number!",
            type: "warning",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ok",
            closeOnConfirm: true
        });
        return;
    }

    $.post('generateOtp', {
        "mobileNumber": mobileNumber,
        "token": token
    }, function (e) {
        if (e == "0") {
            swal({
                title: "Account Not Found?",
                text: "Please sign up for new Account",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            }
            );
        } else if (e == "deactive") {
            // Materialize.toast("You Account has been deactivated", 4000, 'rounded');
            swal({
                title: "Alert",
                text: "You Account has been deactivated!",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
        } else if (e == 1) {
            /* already logged in */
            swal({
                title: "Are you sure?",
                text: "Your previous session is already running, Do you want to start with new session. Any Previous sessions will be lost!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!",
                closeOnConfirm: true
            },
                function () {
                    $('body').removeClass("loaded");
                    $.post("/clearPrevSession", {
                        "mobileNumber": mobileNumber,
                        "token": token
                    }, function (e) {
                        $('body').addClass("loaded");
                        $('#submitBtn').hide();
                        $('#login-page2').show();
                    });
                });

        } else {
            $('#login-page2').show();
            $('#submitBtn').hide();
        }
    });
}

$("#submitBtn").on("click", function () {
    sendOtp(0);
});

var otpButtonCount = 0;
var submitBtnCount = 0;

function checkNumeric(e, id, num, Btn) {
    var data = document.getElementById(id).value;

    if (id == "mobileNumber" && data.length < (num + 1)) {
        $("#submitBtn").hide();
        $("#login-page2").hide();
        $("#submitOtp").hide();
        $("#otp").val("");
        submitBtnCount = 0;
        otpButtonCount = 0;
    } 
    
    if(id == "otp" && data.length < (num + 1)) {
        $("#submitBtn").hide();
        $("#submitOtp").hide();
        submitBtnCount = 0;
    }

    if(id == "mobileNumber" && otpButtonCount == 0 && data.length == (num + 1)) {
        otpButtonCount++;
        if (data.length > num) {
            $('#' + Btn).show();
        } else {
            $('#' + Btn).hide();
        }
    }else if(id == "otp" && submitBtnCount == 0 && data.length == (num + 1)) {
        submitBtnCount++
        if (data.length > num) {
            $('#' + Btn).show();
        } else {
            $('#' + Btn).hide();
        }
    }

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

$(':input').keyup(function (e) {
    var $th = $(this);
    if (e.keyCode == 13) {
        if ($th.attr("id") == "mobileNumber")
            $('#submitBtn').click();
        else {
            $("#submitOtp").click();
        }
    }

    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^0-9]/g, function (str) {
            return '';
        }));
    }
    return;
});

$(':input').on('focus', function () {
    $(this).attr('autocomplete', 'off');
});


// (function() {
//     $('#loginForm').validate({
//     ignore: ".ignore",
//       rules: {
//         email: {
//             validateEmail:true
//         },
//         password: {
//             required: true
//         }
//     },
//     messages: {
//         email: "Please provide valid Email",
//         password: "Please provide Password",
//     },
//     invalidHandler: function(f, v) {
//         // validateAll("bankBreadCrums");
//     },
//     errorElement : 'div',
//         errorPlacement: function(error, element) {
//         var placement = $(element).data('error');
//         if (placement) {
//             $(placement).append(error);
//         } else {
//             error.insertAfter(element);
//         }
//     },
//       submitHandler: function(form) {
//         $.post("/verifyEmailPassword", {"email": $("#email").val(), "password":$("#password").val()}, function(e) {

//         });
//       }
//     });

//     $.validator.addMethod('validateEmail', function(value, element, param) {
//         var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
//         return reg.test( value ); // Compare with regular expression
//     },'Please enter a valid email address.');
// })();
