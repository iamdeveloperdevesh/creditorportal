<style type="text/css">
    #otphide,#otphide2,#otphide3,#mobile_no_error,#captcha_error,#email_error
    {
        display: none;
    }
    @media (min-width:1025px){
        .login-wd {
            flex: 0 0 60%;
            max-width: 58%;
        }
    }
    @media (min-width:1281px)  {
        .login-wd {
            flex: 0 0 54%;
            max-width: 54%;
        }
    }

    .login-img-1 {
        position: relative;
        top: 10%;
    }

    .col-form-label {
        font-weight: 600;
        color: #fff;
        letter-spacing: 1px;
        font-size: 14px !important;
    }

    .col-form-info {
        font-weight: 500;
        color: var(--yellow);
        letter-spacing: 1px;
        font-size: 10px !important;
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #152981;
        opacity: 1;
    }

    .form-control {
        background-color: #152981;
        opacity: 1;
        color: #fff !important;
    }

    .form-control {
        color: #fff !important;
        border: 1px solid #e3e4e8;
    }

    .form-control:focus {
        color: #fff !important;
        border: 1px solid #e3e4e8;
        background-color: #152981;
    }

    .loginFormBtn {
        height: 60px;
        width: 100%;
        padding: 12px 0;
        border-radius: 4px;
        background-color: var(--yellow);
        /* font-family: "Inter-Semibold"; */
        font-weight: 600;
        font-size: 16px;
        line-height: 24px;
        color: #080808;
        margin-top: 32px;
        margin-bottom: 7px;
        border: none;
        outline: none;
    }

    .mob-auth {
        font-weight: 300;
        font-size: 12px;
        line-height: 18px;
        color: #fff;
        margin: 15px 0 0 0;
    }
</style>

<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
    <div class="container">
        <div class="welcome-msg">
            <b></b>

        </div>
        <div class="row mt-3">
            <div class="col-lg-4 col-md-4 "></div>
            <div class="col-lg-4 col-md-4 " style="">
                <div class="card ">

                    <div class="card-body" style="padding-bottom:0px;background-color: #152981;    box-shadow: 3px 3px 18px 1px grey;
">

                       <!-- <div class="border-bot"></div>-->
                        <form action="check_otp_abc" method="POST">
                            <input type="hidden" id="csrf" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" value="<?php echo($error_status); ?>" readonly id="error_status">
                            <input type="hidden" value="<?php echo($message); ?>" readonly id="error_message">
                            <div class="body-login">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group col-md-12">
                                            <label for="example-text-input" class="col-form-label">Mobile Number</label>
                                            <input class="form-control" type="text" autocomplete="off" value = "<?php echo($mob_no); ?>"  maxlength="10" placeholder="Enter Mobile No" id="mob_no" name="mob_no"  required />
                                            <span class="col-form-info">
                                                Mobile No. will be your Customer Login ID
                                            </span>
                                            <span id="mobile_no_error" class="alert alert-danger col-md-12 mt-2"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group col-md-12">
                                            <label for="example-text-input" class="col-form-label">Personal Email ID</label>
                                            <input class="form-control" type="text" autocomplete="off" value = "<?php echo($email_id); ?>"   placeholder="Enter Email Id" id="email" name="email"  required/>
                                            <span class="col-form-info">
                                                Your quote, policy and all communications will be sent on it
                                            </span>
                                            <span id="email_error" class="alert alert-danger col-md-12 mt-2"></span>
                                        </div>
                                    </div>


                                    <!-- captcha update - upendra -->
                                    <!-- <div class="col-md-6 captcha_logic_box" style="padding-right:0px;">
                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Captcha Text</label>
                                            <input class="form-control" type="text" autocomplete="off" maxlength="10" placeholder="Enter Captcha Text" id="entered_captcha" autocomplete="off"/>

                                            <span id="captcha_error" class="alert alert-danger"></span>
                                        </div>
                                    </div>-->

                                    <!-- captcha update - upendra -->
                                    <!--<div class="col-md-6 text-center mt-1  captcha_logic_box">
                                        <div class="form-group btn-generate" style="background: none; border: none;">
                                  <span id="captcha_image_span">
                                    <?php
/*                                    echo $captcha_image['image'];
                                    */?>
                                  </span>
                                            <span style="cursor: pointer;" id="refresh_captcha">
                                      <i class="fa fa-refresh" aria-hidden="true"></i>
                                  </span>
                                        </div>
                                    </div>-->

                                    <!-- OTP Enter Div -->

                                    <div class="col-md-6" id="otphide2" style="padding-right:0px;">
                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">OTP</label>
                                            <input class="form-control" autocomplete="off" type="text" name="otp" placeholder="Enter OTP" id="pos_otp">
                                        </div>
                                    </div>

                                    <div class="col-md-6 text-center" id="otphide3">

                                        <!-- <button type="button" onclick="otp_generate()" class="btn btn-generate" id="generate_otp" name="generate_otp" style="margin-right: 4px; background: #dddddd;">Resend OTP
                                        </button>
                                     -->
                                        <!-- <button type="button" id="validate_otp" name="validate_otp"
                                        class="btn btn-verify">
                                        Verify OTP
                                        </button> -->
                                        <button type="button" name="validate_otp"
                                                class="btn btn-verify loginFormBtn" onclick="verify_otp(this)">
                                            Verify OTP
                                        </button>

                                    </div>

                                    <div class="col-md-12 text-center">
                                <span id="gene_otp">
                                <button type="button" onclick="otp_generate(this)" class="btn btn-generate loginFormBtn" id="generate_otp" name="generate_otp">Generate OTP</button>
                                </span>

                                        <span id="otphide">
                                <button type="button" onclick="otp_generate(this)" class="btn btn-generate loginFormBtn" id="generate_otp" name="generate_otp" style="margin-left: 05px; background: #dddddd;">Resend OTP
                                   </button>
                                 </span>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group col-md-12">
                                            <p class="mob-auth">I hereby authorize Alliance Insurance Brokers Pvt. Ltd. to communicate or reach out to me over calls,
                                                SMS, emails, WhatsApp, and any other mode of communication for my insurance needs. I am aware that
                                                this authorization will override my registry under NDNC.</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 "></div>
            <!--<div class="col-md-5 text-center display-none-sm">
                <img class="login-img-1" src="/public/assets/abc/images/family_new.png" style="width: 97%;">
            </div>-->
        </div>

    </div>
</div>
<!-- main content area end -->
<script>

    var emailRegex = /^([A-Za-z0-9_\.])+\@([A-Za-z0-9_\.])+\.([A-Za-z]{2,4})$/

    function mobileInputError(errorMsg = "") {

        if (errorMsg.length > 0) {
            $('#mobile_no_error').css("display", "block");
            $('#mobile_no_error').text(errorMsg);
        } else {
            $('#mobile_no_error').css("display", "none");
        }
    }

    function emailInputError(errorMsg = "") {
        if (errorMsg.length > 0) {
            $('#email_error').css("display", "block");
            $('#email_error').text(errorMsg);
        } else {
            $('#email_error').css("display", "none");
        }
    }

    // sanitize mobile input
    $('#mob_no').on('input', function() {
        // Replace any non-digit characters with an empty string
        this.value = this.value.replace(/[^0-9]/g, '');
    })

    // validate mobile input
    $('#mob_no').on('input', function() {
        var re = /^[6-9]$/;
        if (this.value.length > 0) {
            if (!re.test(this.value.substr(0, 1))) {
                mobileInputError("Enter a valid 10-digit number starting from 6 to 9")
            } else {
                mobileInputError()
            }
        } else {
            mobileInputError()
        }
    });

    // validate mobile number length on blur
    $('#mob_no').on('blur', function() {
        if (this.value.length < 10) {
            mobileInputError("Please enter a 10-digit mobile number")
        } else {
            mobileInputError()
        }
    });

    // Validate email input 
    $('#email').on('blur', function() {
        if (this.value.length > 0) {
            if (!this.value.match(emailRegex)) {
                emailInputError("Please enter a valid Email ID")
            } else {
                emailInputError()
            }
        } else {
            emailInputError()
        }
    });

    //  limit otp input to 4 digits only
    $('#pos_otp').on('input', function() {
        var sanitized_value = this.value.replace(/[^0-9]/g, '');
        if (sanitized_value.length > 4) {
            sanitized_value = sanitized_value.substr(0, 4);
        }
        this.value = sanitized_value
    });

    function otp_generate(elem) {
        var mob_no = $("#mob_no").val();
        var email = $("#email").val();

        var re = new RegExp('^[6-9][0-9]{9}$');
        var buttonText = $(elem).html().trim();
        if (!email.match(emailRegex)) {
            if (email.length == 0 || mob_no.length == 0) {
                email.length == 0 && emailInputError("Email ID is required.")
                mob_no.length == 0 && mobileInputError("Mobile number is required.")
            } else {
                emailInputError("Please enter a valid Email ID")
            }
            return;
        } else {
            emailInputError()
        }

        $("#pos_otp").val('');

        if (mob_no.length == 0 || email.length == 0) {
            if (mob_no.length == 0) {
                mobileInputError("Mobile Number is required.")
            }
            if (email.length == 0) {
                emailInputError("Email ID is required.")
            }

        } else {
            mobileInputError()

            //captcha - update - upendra - 16-10-2021
            /*  var entered_captcha = $("#entered_captcha").val();
            if(entered_captcha == ''){
                $("#captcha_error").show();
                $('#captcha_error').css("display" , "block");
                $("#captcha_error").html("Please enter captcha text");
                return;
            }else{
                $("#captcha_error").hide();
            }*/

            // $("#myModal1").modal("hide");
            // $("#sms_body").html('');
            // ajaxindicatorstop();
            $.ajax({
                url: "/customerportal/generate_otp_abc",
                type: "POST",
                data: {
                    "mob_no": mob_no,
                    "email": email,
                    //"entered_captcha":entered_captcha
                },
                async: false,
                dataType: "json",
                success: function(response) {
                    if (response.status == "error") {
                        alert(response.message)
                    } else {
                        $("#otphide").show("");
                        $("#otphide2").show("");
                        $("#otphide3").show("");
                        $("#gene_otp").hide("");
                        // $( "#resend_otp").show("");

                        //captcha - update - upendra - 16-10-2021
                        //$("#entered_captcha").val('');
                        // refresh_captcha();
                        if (buttonText == "Generate OTP") {
                            $(".captcha_logic_box").hide();
                        }

                    }
                    /*alert(response.status);

                    var obj = JSON.parse(response);

                    if(obj.status == '1'){
                        alert('otp success');
                    }*/
                    // $("#pos_otp").val("");
                    // $("#myModal1").modal("show");
                },
                error: function(xhr) {
                    alert(JSON.parse(xhr.responseText).message);
                    refresh_captcha();
                    $("#entered_captcha").val('');
                    $("#pos_otp").val('');
                }
            });
        }
    }


    function verify_otp() {

        $.ajax({
            url: "/customerportal/check_otp_abc",
            type: "POST",
            data: {
                otp: $("#pos_otp").val(),
                mob_no: $("#mob_no").val()
            },
            async: false,
            dataType: "json",
            success: function(response) {


                var resp = response;
                if (response.status == 1) {
                    swal("Success!", "OTP Verified Successfully.", "success")
                        .then(function() {
                            location.replace(response.url);
                        });
                } else {
                    swal("Invalid OTP!", "Please enter the correct OTP.", "error")
                        .then(function() {
                            $("#pos_otp").val("")
                        });
                }
            },
        });
    }

    function refresh_captcha() {
        $("#entered_captcha").val('');
        $.ajax({
            url: "/customerportal/refresh_captcha_abc",
            type: "POST",
            data: {

            },
            async: true,
            dataType: "text",
            cache: false,
            success: function(response) {
                // ajaxindicatorstop();
                // alert(response);
                $("#captcha_image_span").html(response);
            },
        });
    }

    $(document).on("click", "#refresh_captcha", function() {
        refresh_captcha();
    });
</script>