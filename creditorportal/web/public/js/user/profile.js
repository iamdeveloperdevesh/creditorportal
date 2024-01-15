$('document').ready(function () {
    var neStates = [
        "Arunachal Pradesh", "Nagaland", "Manipur", "Mizoram", "Meghalaya", "Sikkim", "Assam", "Tripura"
    ];

    var isNe = "0";

    if ($("#profilePreview").attr("data-href")) {
        $("#photofile").addClass("ignore");
    }

    if ($("#gstPreview").attr("data-href")) {
        $("#gstFile").addClass("ignore");
    }

    showBankDoc(document.getElementById("bank_id_proof_type"));

    $("#bank_name").empty();
    $("#bank_name").append('<option value="" selected>Select Bank</option>');
    $.post("/getBankName", {}, function (e) {
        var ifscMasters = JSON.parse(e);
        for (i = 0; i < ifscMasters.length; ++i) {
            $("#bank_name").append("<option value='" + ifscMasters[i].bank_name + "'>" + ifscMasters[i].bank_name + "</option>");
        }
        $("#bank_name").material_select();
    });

    $('.collapsible').collapsible();

    isNe = $("#isNe").val();

    //ignore validation for adhaar card if pincode is in north East States
    getPincode("1");

    setValues();

    function disableCheckbox(e) {
        $('#license').find(':checkbox').each(function () {
            if (e.checked && this != e) {
                this.checked = false;
                $(this).attr("disabled", "disabled");
                hideShow(this);
            } else {
                $(this).prop("disabled", false);
            }
        });
    }

    var date1 = new Date();
    date1.setFullYear(date1.getFullYear() - 19);

    // new Cleave('.dob', {
    //     date: true,
    //     datePattern: ['d', 'm', 'Y'],
    //     delimiters: ['-']
    // });

    // $('.dob').datepicker({
    //     maxDate: date1,
    //     dateFormat: 'dd-mm-yy',
    //     changeMonth: true,
    //     changeYear: true,
    //     yearRange: "-100:+0"
    // });

    $('#dob').pickadate({
        selectMonths: true,
        selectYears: 100,
        min: new Date(1920, 0, 1),
        max: date1,
        close: 'Ok',
        format: 'dd-mm-yyyy',
        closeOnSelect: true,
        container: "body"
    });

    // $('#dob').pickadate({
    //     selectMonths: true,
    //     selectYears: 100,
    //     min: new Date(1920, 0, 1),
    //     max: new Date(),
    //     today: 'Today',
    //     clear: 'Clear',
    //     close: 'Ok',
    //     format: 'dd-mm-yyyy',
    //     closeOnSelect: true,
    //     container: "body"
    // });

    $('#bank_name').on('change', function () {
        $('#ifsc_code').val("");
        $("#bank_city").val("");
        $("#bank_city").material_select();
        $("#bank_branch").val("");
        $("#bank_branch").material_select();

        getBankCity(this.value);
    });

    //ignore validation

    if ($('#eduStatus').val()) {
        $('#edufile').addClass("ignore");
    }

    if ($('#docStatus').val()) {
        $('#panfile').addClass("ignore");
        $('#adhaarfile').addClass("ignore");
    }

    if ($('#bankStatus').val()) {
        $('#bankfile').addClass("ignore");
    }

    enableCollapsable();

    if ($('#ifsc_code').val().trim().length > 0)
        getIfscCode($('#ifsc_code').val());


    $('#pincode').on('keyup', function () {
        if (this.value.length == 6)
            getPincode("0");
    });

    function getPincode(status) {
        $.post('/get_state_city', { "pincode": $('#pincode').val() }, function (e) {
            if (e != 0) {
                var e = JSON.parse(e);
                //check for north east state
                if (neStates.includes(e.state_name.trim())) {
                    isNe = "1";


                    //1 = document.ready or 0 = focus out pincode
                    hideShowIdProof(isNe, status);
                } else {
                    isNe = "0";

                    hideShowIdProof(isNe, status);
                }

                $('#city').val(e.city_name);
                $('#state').val(e.state_name);
            } else {
                $('#city').val("");
                $('#state').val("");
            }
        });
    }

    function getBankCity(bank_name, bank_name_value) {
        $.post("/getBankCity", {
            "bank_name": bank_name
        }, function (e) {
            var obj = JSON.parse(e);
            $('#bank_city').empty();
            $('#bank_city').append('<option value="" selected>Select City</option>');
            for (i = 0; i < obj.length; ++i) {
                $("#bank_city").append("<option value='" + obj[i].bank_city + "'>" + obj[i].bank_city + "</option>");
            }

            if (bank_name_value)
                $("#bank_city").val(bank_name_value);

            $("#bank_city").material_select();
        });
    }

    $("#bank_city").on("change", function (e) {
        getBankBranch(this.value);
    });

    function getBankBranch(bank_city, bank_city_value) {

        $.post("/getBankBranch", {
            "bank_name": $('#bank_name').val(),
            "bank_city": bank_city,
        }, function (e) {
            var obj = JSON.parse(e);
            $('#bank_branch').empty();
            $('#bank_branch').append('<option value="" selected>Select Branch</option>');

            for (i = 0; i < obj.length; ++i) {
                $("#bank_branch").append("<option data-ifsc='" + obj[i].ifsc_code + "' value='" + obj[i].bank_branch + "'>" + obj[i].bank_branch + "</option>");
            }

            if (bank_city_value)
                $("#bank_branch").val(bank_city_value);
            $("#bank_branch").material_select();
        });
    }

    $('#bank_branch').on('change', function () {
        $('#ifsc_code').val(this.selectedOptions[0].getAttribute("data-ifsc"));
    });

    $('#ifsc_code').on('keyup', function () {
        $("#bank_name").val("");

        $('#bank_city').empty();
        $('#bank_city').append('<option value="" selected>Select City</option>');

        $('#bank_branch').empty();
        $('#bank_branch').append('<option value="" selected>Select Branch</option>');

        $("#bank_name").material_select();
        $("#bank_city").material_select();
        $("#bank_branch").material_select();

        if (this.value.length == 11) {
            //set all value by ifsc code
            getIfscCode(this.value);
        }
    });

    function getIfscCode(code) {
        $.post('/user/getIfscCode', {
            'ifsc_code': code
        }, function (e) {
            
            var obj = JSON.parse(e);
                if (obj) {
                    $('#bank_name').val(obj.bank_name);
                    $("#bank_name").material_select();
                    //populate city by bank name
                    getBankCity(obj.bank_name, obj.bank_city);
                    //populate branch by city
                    getBankBranch(obj.bank_city, obj.bank_branch);

                    $('#ifsc_code').val(obj.ifsc_code);
                }else {
                    swal("Alert", "Invalid IFSC Code", "warning");
                }
        });
    }

    function setValues() {
        $('#insuranceExperienceInYears').material_select();
        $('#monthlyMotorPremium').material_select();
        $('#monthlyHealthPremium').material_select();
        $('#monthlyLifePremium').material_select();
        $('#idtype').material_select();
        $("#bank_name").material_select();
        $("#bank_branch").material_select();
        $("#bank_city").material_select();

        $('#license').find(':checkbox').each(function () {
            hideShow(this);
        });
    }

    function disableCheckbox(e) {
        $('#license').find(':checkbox').each(function () {
            if (e.checked && this != e) {
                this.checked = false;
                $(this).attr("disabled", "disabled");
                hideShow(this);
            } else {
                $(this).prop("disabled", false);
            }
        });
    }

    $('#bussinessDet').validate({
        ignore: ".ignore",
        rules: {
            /* license: {
             required: function (element) {
             var count = 0;
             $('#license').find(':checkbox').each(function(e){
             if(this.checked) {
             ++count;
             }
             
             if(count != 0) {
             return true;
             }else {
             return false;
             }
             });
             } 
             },
             posLife: {
             required: '#4:checked'
             },
             posGeneral: {
             required: '#5:checked'
             } */

        },
        invalidHandler: function (f, v) {
            // validateAll("bussBreadCrums");
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            var userStatus = $("#userStatus").val();
            var bankStatus = $("#bankStatus").val();
            var bussStatus = $("#bussStatus").val();

            if (userStatus != "1") {
                swal("Alert", "Please fill Personal Information First", "error");
                // $('.collapsible').collapsible('open', 0);
                openPesonalAccordian();
                return
            }

            if (bankStatus != "1") {
                swal("Alert", "Please fill Bank Details First", "error");
                // $('.collapsible').collapsible('open', 1);
                openBankAccordian();
                return
            }

            if(!$("#userAgree").prop("checked")) {
                swal("Alert", "Please Agree Terms & Condition", "error");
                return;
            }

            var primarySourceOfIncome = $('#primarySourceOfIncome').val();
            var insuranceExperienceInYears = $('#insuranceExperienceInYears').val();
            var monthlyMotorPremium = $('#monthlyMotorPremium').val();
            var monthlyHealthPremium = $('#monthlyHealthPremium').val();
            var monthlyLifePremium = $('#monthlyLifePremium').val();
            var dedicatedSpace = $('input[name="dedicatedSpace"]:checked').val();
            var agencyHealth = $('#agencyHealth').val();
            var agencyLife = $('#agencyLife').val();
            var agencyGeneral = $('#agencyGeneral').val();
            var posLife = $('#posLife').val();
            var posGeneral = $('#posGeneral').val();
            var surveyor = $('#surveyor').val();

            var bd_checked_lic = [];
            $('#license').find(':checkbox').each(function () {
                if (this.checked) {
                    bd_checked_lic.push($(this).attr("id"));
                }
            });

            bd_checked_lic = JSON.stringify(bd_checked_lic);

            $.post("/bussDetSave", {
                "primarySourceOfIncome": primarySourceOfIncome,
                "insuranceExperienceInYears": insuranceExperienceInYears,
                "monthlyMotorPremium": monthlyMotorPremium,
                "monthlyHealthPremium": monthlyHealthPremium,
                "monthlyLifePremium": monthlyLifePremium,
                "dedicatedSpace": dedicatedSpace,
                "agencyHealth": agencyHealth,
                "token": token,
                "agencyLife": agencyLife,
                "agencyGeneral": agencyGeneral,
                "posLife": posLife,
                "posGeneral": posGeneral,
                "surveyor": surveyor,
                "bd_checked_lic": bd_checked_lic
            }, function (e) {
                if (e == "1") {
                    $('#bussBreadCrums').removeClass("active");
                    $('#bussBreadCrums').addClass("visited");

                    if (userStatus == "1" && bankStatus == "1" && bussStatus == "1") {
                        swal({
                            title: "Success",
                            text: "Information Successfully Updated.",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#C7222A;",
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        },
                            function () {
                                // location.href = "/certification/home";
                            });
                    } else {
                        swal({
                            title: "Success",
                            text: "Information Successfully Updated, Do you want to check certification option",
                            type: "success",
                            showCancelButton: true,
                            confirmButtonColor: "#C7222A;",
                            confirmButtonText: "Yes, Take me there!",
                            closeOnConfirm: true
                        },
                            function () {
                                location.href = "/certification/home";
                            });
                    }

                } else {
                    swal("Alert", "Something went Wrong", "error");
                }
            });
        }
    });

    $('#bankDet').validate({
        ignore: ".ignore",
        rules: {
            bankfile: {
                required: true
            },
            bank_name: {
                required: true
            },
            bank_branch: {
                required: true
            },
            bank_city: {
                required: true
            },
            ac_holder_name: {
                required: true
            },
            bank_ac_no: {
                required: true,
                number: true
            },
            ifsc_code: {
                required: true,
                ifsc_codeCheck: 11
            },
            bankStatfile: {
                required: true
            }
        },
        messages: {
            bankfile: "Please provide Bank document",
        },
        invalidHandler: function (f, v) {
            // validateAll("bankBreadCrums");
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
            var userStatus = $("#userStatus").val();

            if (userStatus != "1") {
                swal("Alert", "Please fill Personal information First", "error");
                // $('.collapsible').collapsible('open', 0);
                openPesonalAccordian();
                return;
            }

            $('body').removeClass("loaded");
            var bank_id_proof_type = $('#bank_id_proof_type').val();
            var bankfile = document.querySelector("input[name='bankfile']").files[0];
            var bankStatfile = document.querySelector("input[name='bankStatfile']").files[0];
            var bank_name = $('#bank_name').val();
            var bank_branch = $('#bank_branch').val();
            var bank_city = $('#bank_city').val();
            var ac_holder_name = $('#ac_holder_name').val();

            var bank_ac_no = $('#bank_ac_no').val();
            var ifsc_code = $('#ifsc_code').val();

            var data = new FormData();

            data.append('bank_name', bank_name);
            data.append('bank_branch', bank_branch);
            data.append('bank_city', bank_city);
            data.append('ac_holder_name', ac_holder_name);
            data.append("token", token);
            data.append('bank_ac_no', bank_ac_no);
            data.append('ifsc_code', ifsc_code);
            data.append('id_proof', bank_id_proof_type);

            if (bank_id_proof_type == "1") {
                data.append('bankStatfile', bankStatfile);
                data.append('bankfile', bankfile);
            } else {
                data.append('bankfile', bankfile);
            }

            $.ajax({
                type: "POST",
                url: "/bankDetSave",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                async: true,
                mimeType: "multipart/form-data",
                success: function (data) {

                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        swal("Alert", "Please check for File Type (Allowed File Types JPG), File Size (Allowed File Size 2Mb)", "error");
                    }

                    if (data[0].errorCode == "0" && data[1].errorCode == "0") {
                        $("#bankStatus").val("1");

                        $("#bankPreview")
                            .attr("data-href", data[0].path)
                            .css("display", "inline");

                        $("#bankStatPreview")
                            .attr("data-href", data[1].path)
                            .css("display", "inline");

                        swal({
                            title: "Success",
                            text: "Information Successfully Updated",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#C7222A;",
                            confirmButtonText: "Ok",
                            closeOnConfirm: true
                        },
                            function () {
                                enableCollapsable();
                            });
                    } else {
                        swal("Alert", "Please check for File Type, File Size \n(Allowed File Types 'JPG'), \n(Allowed File Size 2Mb)", "error");
                    }
                }
            });
        }
    });

    $("#edufile").on("change", function () {
        var input = document.querySelector("input[name='edufile']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#eduPreview").show();
                $("#eduPreview").attr("data-href", e.target.result);

            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#eduPreview").on("click", function () {
        var attr = $(this).attr("data-href");

        window.open(attr);
    });

    $("#gstFile").on("change", function () {
        var input = document.querySelector("input[name='gstFile']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#gstPreview").show();
                $("#gstPreview").attr("data-href", e.target.result);

            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#gstPreview").on("click", function () {
        var attr = $(this).attr("data-href");


        // var image = new Image();
        // image.src = attr;

        // var w = window.open("");
        // w.document.write(image.outerHTML);
        window.open(attr);
    });

    $("#bankfile").on("change", function () {
        var input = document.querySelector("input[name='bankfile']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#bankPreview").show();
                $("#bankPreview").attr("data-href", e.target.result);

            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#bankPreview").on("click", function () {
        var attr = $(this).attr("data-href");


        window.open(attr);
    });

    $("#bankStatfile").on("change", function () {
        var input = document.querySelector("input[name='bankStatfile']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#bankStatPreview").show();
                $("#bankStatPreview").attr("data-href", e.target.result);

            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#bankStatPreview").on("click", function () {
        var attr = $(this).attr("data-href");


        window.open(attr);
    });

    $("#panfile").on("change", function () {
        var input = document.querySelector("input[name='panfile']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#panPreview").show();
                $("#panPreview").attr("data-href", e.target.result);

            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#panPreview").on("click", function () {
        var attr = $(this).attr("data-href");

        window.open(attr);
    });

    $("#adhaarfile").on("change", function () {
        var input = document.querySelector("input[name='adhaarfile']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#adhaarPreview").show();
                $("#adhaarPreview").attr("data-href", e.target.result);

            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#adhaarPreview").on("click", function () {
        var attr = $(this).attr("data-href");


        window.open(attr);
    });

    $("#adhaarBackfile").on("change", function () {
        var input = document.querySelector("input[name='adhaarBackfile']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#adhaarBackPreview").show();
                $("#adhaarBackPreview").attr("data-href", e.target.result);

            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#adhaarBackPreview").on("click", function () {
        var attr = $(this).attr("data-href");


        window.open(attr);
    });

    $("#photofile").on("change", function () {
        var input = document.querySelector("input[name='photofile']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#profilePreview").show();
                $("#profilePreview").attr("data-href", e.target.result);

            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#profilePreview").on("click", function () {
        var attr = $(this).attr("data-href");


        window.open(attr);
    });

    function hideShowIdProof(isNeArg, status) {
        if (isNeArg == "1") {
            $("#adhaar_no").addClass("ignore");
            $("#adhaarfile").addClass("ignore");
            $("#adhaarBackfile").addClass("ignore");
            $("#idProofNo").removeClass("ignore");

            if (status == "0") {
                $("#adhaar_no").val("");
                $("#adhaarfile").val("");
                $("#idProofNo").val("");
                $("#adhaarBackfile").val("");
            }

            $(".adhaarDiv").addClass("hide");
            $(".idProofDiv").removeClass("hide");
        } else {
            $("#adhaar_no").removeClass("ignore");
            if ($("#adhaarPreview").attr("data-href") != "")
                $("#adhaarfile").addClass("ignore");
            else
                $("#adhaarfile").removeClass("ignore");

            if (($("#adhaarBackPreview").attr("data-href")).trim() != "")
                $("#adhaarBackfile").addClass("ignore");
            else
                $("#adhaarBackfile").removeClass("ignore");

            $("#idProofNo").addClass("ignore");

            if (status == "0") {
                $("#adhaar_no").val("");
                $("#adhaarfile").val("");
                $("#idProofNo").val("");
            }

            $(".adhaarDiv").removeClass("hide");
            $(".idProofDiv").addClass("hide");
        }
    }

    jQuery.validator.addMethod("exactlength", function (value, element, param) {
        if (value.length == 0)
            return true;
        return this.optional(element) || value.length == param;
    }, $.validator.format("Please enter exactly {0} characters."));

    jQuery.validator.addMethod("adharlength", function (value, element, param) {
        if (value.length == 0)
            return true;
        return this.optional(element) || value.length == param;
    }, $.validator.format("Invalid Aadhar Card Number."));

    jQuery.validator.addMethod("panCardCheck", function (value, element, param) {
        var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
        return regpan.test(value);
    }, $.validator.format("Invalid Pan Card Details."));

    jQuery.validator.addMethod("ifsc_codeCheck", function (value, element, param) {
        if (value.length == 0)
            return true;
        return this.optional(element) || value.length == param;
    }, $.validator.format("Invalid IFSC Code."));

    $("body").on("blur", "#pan_card_no", function (e) {
        e.preventDefault();

        $(this).val($(this).val().toUpperCase());
    });

    $("body").on("blur", "#gstNo", function (e) {
        e.preventDefault();

        $(this).val($(this).val().toUpperCase());
    });

    $('#personalDet').validate({
        ignore: ".ignore",
        rules: {
            full_name: {
                required: true,
            },
            adhaarBackfile: {
                required: true
            },
            adhaar_no: {
                required: true,
                number: true,
                adharlength: 12
            },
            adhaarfile: {
                required: true
            },
            dob: {
                required: true,
                exactlength: 10
            },
            email: {
                validateEmail: true
            },
            address: {
                required: true,
                minlength: 5
            },
            alternate_mobile: {
                exactlength: 10,
                valid_mobile: true
            },
            pincode: {
                required: true,
                number: true,
                minlength: 6
            },
            city: {
                required: true
            },
            gstNo: {
                validate_gst: true
            },
            gstFile: {
                validate_gstFile: true
            },
            state: {
                required: true
            },
            gender: {
                required: true
            },
            edufile: {
                required: true
            },
            pan_card_no: {
                panCardCheck: true
            },
            panfile: {
                required: true
            },
            photofile: {
                required: true
            },
            idProofNo: {
                required: true
            },
            educationQualification: {
                required: true
            },
            gender: {
                required: true
            }
        },
        messages: {
            edufile: "Please provide Education document",
            panfile: "Please provide Pancard document",
            adhaarfile: "Please provide Adhaar document",
            photofile: "Please provide Profile document",
            adhaarBackfile: "Please provide Aadhaar Card Back document",
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            $('body').removeClass("loaded");
            var data = new FormData();

            var full_name = $('#full_name').val();
            var dob = $('#dob').val();
            var email = $('#email').val();
            var mobile_no = $('#mobile_no').val();
            var alternate_mobile = $('#alternate_mobile').val();
            var address = $('#address').val();
            var pincode = $('#pincode').val();
            var city = $('#city').val();
            var state = $('#state').val();
            var martialStatus = $('#martialStatus').val();
            var gender = $('select[name=gender]').val();
            var idproof = $('select[name=idproof]').val();
            var idProofNo = $("#idProofNo").val();

            //education
            var edufile = document.querySelector("input[name='edufile']").files[0];
            var educationQualification = $('#educationQualification').val();

            //documents
            var pan_card_no = $('#pan_card_no').val();
            var panfile = document.querySelector("input[name='panfile']").files[0];
            var adhaar_no = $('#adhaar_no').val();
            var adhaarfile = document.querySelector("input[name='adhaarfile']").files[0];
            var adhaarBackfile = document.querySelector("input[name='adhaarBackfile']").files[0];
            var photofile = document.querySelector("input[name='photofile']").files[0];

            var gstNo = $("#gstNo").val();
            var gstFile = document.querySelector("input[name='gstFile']").files[0];

            if (gstNo.trim().length > 0 && gstFile) {
                data.append("gstNo", gstNo);
                data.append("gstFile", gstFile);
            } else {
                data.append("gstNo", "");
                data.append("gstFile", "");
            }

            data.append("full_name", full_name);
            data.append("dob", dob);
            data.append("email", email);
            data.append("mobile_no", mobile_no);
            data.append("alternate_mobile", alternate_mobile);
            data.append("address", address);
            data.append("pincode", pincode);
            data.append("city", city);
            data.append("token", token);
            data.append("state", state);
            data.append("martialStatus", martialStatus);
            data.append("gender", gender);
            data.append("idproof", idproof);
            data.append("idProofNo", idProofNo);
            data.append("isNe", isNe);

            data.append('edufile', edufile);
            data.append('educationQualification', educationQualification);

            data.append('pan_card_no', pan_card_no);
            data.append('panfile', panfile);
            data.append('adhaar_no', adhaar_no);
            data.append('adhaarfile', adhaarfile);
            data.append('adhaarBackfile', adhaarBackfile);
            data.append('photofile', photofile);

            //post
            $.ajax({
                type: "POST",
                url: "/profileDocBankSave",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                async: true,
                success: function (data) {
                    try {
                        data = JSON.parse(data);
                        data[0] = JSON.parse(data[0]);
                        data[1] = JSON.parse(data[1]);
                        data[2] = JSON.parse(data[2]);
                    } catch (e) {
                        swal("Alert", "Please check for File Type (Allowed File Types JPG), File Size (Allowed File Size 2Mb)", "error");
                        return;
                    }

                    if (data[0].errorCode == "0" && data[1][0].errorCode == "0" && data[1][1].errorCode == "0" && data[2].errorCode == "0") {

                        if (data[0].path) {
                            $("#profilePreview")
                                .attr("data-href", data[0].path)
                                .css("display", "inline");
                        }

                        $("#panPreview")
                            .attr("data-href", data[1][0].path)
                            .css("display", "inline");

                        $("#adhaarPreview")
                            .attr("data-href", data[1][1].path)
                            .css("display", "inline");

                        $("#adhaarBackPreview")
                            .attr("data-href", data[1][2].path)
                            .css("display", "inline");

                        if (data[1][3].path) {
                            $("#gstPreview")
                                .attr("data-href", data[1][3].path)
                                .css("display", "inline");
                        }

                        $("#eduPreview")
                            .attr("data-href", data[2].path)
                            .css("display", "inline");

                        $("#userStatus").val("1");
                        $("#eduStatus").val("1");
                        $("#docStatus").val("1");

                        swal({
                            title: "Success",
                            text: "Information Successfully Updated",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Ok",
                            closeOnConfirm: true
                        },
                            function () {
                                enableCollapsable();
                            });
                    } else {
                        swal("Alert", "Please check for File Type (Allowed File Types JPG), File Size (Allowed File Size 2Mb)", "error");
                    }
                }
            });
        }
    });

    $('#pincode').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('#bank_ac_no').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('#adhaar_no').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('#ac_holder_name').keyup(function (e) {
        var $th = $(this);
        
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^a-zA-Z]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    function openPesonalAccordian() {
        removeProgressBarActive();
        $("#profileProgress").addClass("class", "active");
        // $('.collapsible').collapsible('open', 0);
        $($(".collapsible-body")[0]).show();
        $($(".collapsible-header")[0]).addClass("active");
        $('#profileList').addClass("active");
    }

    function openBankAccordian() {
        removeProgressBarActive();
        $('#bankProgress').addClass("class", "active");
        // $('.collapsible').collapsible('open', 1);
        $($(".collapsible-body")[1]).show();

        $($(".collapsible-header")[1]).addClass("active");
        $('#bankList').addClass("active");
    }

    function openBussAccordian() {
        removeProgressBarActive();
        $('#bussProgress').addClass("class", "active");
        $('#bussList').addClass("active");

        $($(".collapsible-header")[2]).addClass("active");
        // $('.collapsible').collapsible('open', 2);
        $($(".collapsible-body")[2]).show();
    }

    function enableCollapsable() {
        var userStatus = $("#userStatus").val();
        var bankStatus = $("#bankStatus").val();
        var bussStatus = $("#bussStatus").val();

        openPesonalAccordian();

        if (userStatus && userStatus != 0) {
            openBankAccordian();
        }

        if (bankStatus && bankStatus != 0) {
            openBussAccordian();

        }

        if (userStatus == 1 && bankStatus == 1 && bussStatus == 1) {
            removeProgressBarActive();
        }


    }

    function removeProgressBarActive() {
        var userStatus = $("#userStatus").val();
        var bankStatus = $("#bankStatus").val();
        var bussStatus = $("#bussStatus").val();

        $("#profileProgress").removeClass("active");
        $("#bankProgress").removeClass("active");
        $("#bussProgress").removeClass("active");

        $('#profileList').removeClass("active");
        $('#bankList').removeClass("active");
        $('#bussList').removeClass("active");


        $($(".collapsible-header")[0]).removeClass("active");
        $($(".collapsible-header")[1]).removeClass("active");
        $($(".collapsible-header")[2]).removeClass("active");

        $($(".collapsible-body")[0]).hide();
        $($(".collapsible-body")[1]).hide();
        $($(".collapsible-body")[2]).hide();

        if (userStatus) {
            $("#profileProgress").addClass("visited");
        }

        if (bankStatus) {
            $("#bankProgress").addClass("visited");
        }

        if (bussStatus) {
            $("#bussProgress").addClass("visited");
        }
    }

    $(document).on("keyup", "#full_name", function () {
        if ($(this).val().match(/[^a-zA-Z ]/g)) {
            $(this).val($(this).val().replace(/[^a-zA-Z]/g, ""));
        }
    });

    $.validator.addMethod('valid_mobile', function (value, element, param) {
        var re = new RegExp('^[6-9][0-9]{9}$');
        return this.optional(element) || re.test(value); // Compare with regular expression
    }, 'Enter a valid 10 digit mobile number');

    $('#alternate_mobile').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $(':input').on('focus', function () {
        $(this).attr('autocompvare', 'off');
    });

    $.validator.addMethod('validate_gst', function (value, element, param) {
        value = value.toUpperCase();
        var reg = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/g;
        return this.optional(element) || reg.test(value); // Compare with regular expression
    }, 'Enter a valid GST number');

    $.validator.addMethod('validate_gstFile', function (value, element, param) {
        if($("#gstNo").val().length == 15 && document.querySelector("input[name='gstFile']").files[0].length == 0) {
            return false;

        }
        return true;
    }, 'please Provide Gst No.');

    $.validator.addMethod('validateEmail', function (value, element, param) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        return reg.test(value); // Compare with regular expression
    }, 'Please enter a valid email address.');

});

function hideShow(e) {
    if (e.checked) {
        if (e.value == 1) {
            $('.agencyHealth').removeClass("hidden");
        } else if (e.value == 2) {
            $('.agencyLife').removeClass("hidden");
        } else if (e.value == 3) {
            $('.agencyGeneral').removeClass("hidden");
        } else if (e.value == 4) {
            $('.posLife').removeClass("hidden");
        } else if (e.value == 5) {
            $('.posGeneral').removeClass("hidden");
        } else if (e.value == 6) {
            $('.surveyor').removeClass("hidden");
        } else if (e.value == 7) {
            disableCheckbox(e);
        }
    } else {
        if (e.value == 1) {
            $('.agencyHealth').addClass("hidden");
            $("#agencyHealth").val("");
            $('#agencyHealth').material_select();
        } else if (e.value == 2) {
            $('.agencyLife').addClass("hidden");
            $("#agencyLife").val("");
            $('#agencyLife').material_select();
        } else if (e.value == 3) {
            $('.agencyGeneral').addClass("hidden");
            $("#agencyGeneral").val("");
            $('#agencyGeneral').material_select();
        } else if (e.value == 4) {
            $('.posLife').addClass("hidden");
            $("#posLife").val("");

        } else if (e.value == 5) {
            $('.posGeneral').addClass("hidden");
            $("#posGeneral").val("");

        } else if (e.value == 6) {
            $('.surveyor').addClass("hidden");
            $("#surveyor").val("");
            $('#surveyor').material_select();
        } else if (e.value == 7) {
            disableCheckbox(e);
        }
    }
}

function getFileBrowser(id) {
    $('#' + id).click();
}

function disableCheckbox(e) {
    $('#license').find(':checkbox').each(function () {
        if (e.checked && this != e) {
            this.checked = false;
            $(this).attr("disabled", "disabled");
            hideShow(this);
        } else {
            $(this).prop("disabled", false);
        }
    });
}

function showBankDoc(e) {
    if (e.value == "1") {
        $("#bankStatDiv").show();
        $("#bankStatfile").removeClass("ignore");
    } else {
        $("#bankStatDiv").hide();
        $("#bankStatfile").addClass("ignore");
    }

    if ($("#bankStatPreview").attr("data-href").toString().trim() != "") {
        $("#bankStatfile").addClass("ignore");
    }
}