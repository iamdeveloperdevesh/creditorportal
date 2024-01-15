<div class="col-md-10">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Lead Details - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="form-validate" method="post" action="#" enctype="multipart/form-data>
                    <input type="hidden" id="lead_id" name="lead_id" value="<?php if (!empty($user_details['lead_id'])) {
                        echo $user_details['lead_id'];
                    } ?>" />
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label for="lan_id" class="col-form-label">Call Center Process</label>
                            <div class="input-group">
                                <input class="form-control" type="text" value="<?php echo $s_axis_process; ?>" id="axis_process" name="axis_process" style='pointer-events:none'>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="salutation" class="col-form-label">Salutation<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <select class="select2 form-control" name="salutation" id="salutation" onchange="changeGender(this.value);">
                                    <option value="">Select</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="col-form-label">First Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input class="form-control checkvalidInputText" maxlength="50" type="text"
                                       value="" name="first_name" id="first_name" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="col-form-label">Middle Name</label>
                            <div class="input-group">
                                <input class="form-control checkvalidInputText" placeholder="Enter ..." name="middle_name" id="middle_name" type="text" value="" aria-describedby="inputGroupPrepend" />
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input class="form-control checkvalidInputText" placeholder="Enter ..." name="last_name" id="last_name" type="text" value="" aria-describedby="inputGroupPrepend" />
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="col-form-label">Gender<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <select style="pointer-events:none" class="select2 form-control" name="gender" id="gender">
                                        class="form-control ">
                                    <option value="">Select Gender</option>
                                    <option value="Male">MALE</option>
                                    <option value="Female">FEMALE</option>
                                    <!--<option value="Transgender">TRANSGENDER</option>-->
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">person</span></span>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dob" class="col-form-label">Date of Birth<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input class="form-control" placeholder="Enter ..." name="dob" id="dob" type="text" value="" aria-describedby="inputGroupPrepend" autocomplete="off" />
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">calendar_today</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email_id" class="col-form-label">Email Id<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input class="form-control" placeholder="Enter ..." name="email_id" id="email_id" type="text" value="" aria-describedby="inputGroupPrepend" />
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">alternate_email</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="mobile_number" class="col-form-label">Mobile Number<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input class="form-control" placeholder="Enter ..." name="mobile_number" id="mobile_number" type="text" value="" aria-describedby="inputGroupPrepend" maxlength="10" />
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
                                </div>
                            </div>
                        </div>
                        <input id="demo2" class=" form-control details" type="file" placeholder="Drag and drop, or Browser yor file" name="file" style="
">


                    </div>
                    <div class="row mt-4">
                        <div class="col-md-1 text-left col-6">
                            <button type="submit" class="btn smt-btn btn-primary">Save</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery.validator.addMethod("mob", function(value, element) {
        return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
    }, "Enter valid 10 digit No. starting with 6 to 9.");

    jQuery.validator.addMethod("lettersonlys", function(value, element) {
        return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
    }, "Letters only please");

    function getPlanDetails(plan_id) {

        data = {};
        if (plan_id != '') {

            data.plan_id = plan_id;
        }

        if ($("#creditor_id").val() != '') {

            data.creditor_id = $("#creditor_id").val();
        }

        $.ajax({

            url: "<?php echo base_url('customerleads/getPlanDetailsForLead'); ?>",
            method: "POST",
            data: data,
            dataType: 'json',
            success: function(response) {

                if (response.success) {

                    if (response.min_age > 0 && response.max_age > 0) {

                        $("#dob").removeAttr('disabled');
                        $("#dob").datepicker("option", "yearRange", response.max_age + ':' + response.min_age);

                        var date = new Date();
                        var maxDate = date.getDate() + '-' + (date.getMonth() + 1) + '-' + response.min_age;
                        $("#dob").datepicker("option", "maxDate", maxDate);

                        var minDate = date.getDate() + '-' + (date.getMonth() + 1) + '-' + response.max_age;
                        $("#dob").datepicker("option", "minDate", minDate);

                        $("#dob").datepicker("refresh");
                    }
                }
            }
        });
    }

    function changeGender(salutation) {
        //alert(salutation);return false;
        var optionval = "";
        if (salutation == "Mr" || salutation == "Master") {
            optionval = '<option value="Male">Male</option>';
        } else if (salutation == "Dr") {
            optionval = '<option value="Male">Male</option><option value="Female">Female</option>';
        } else {
            optionval = '<option value="Female">Female</option>';
        }

        $("#gender").html(optionval);
        $("#gender").select2();
    }

    function coapplicant(val) {
        if (val == "Y") {
            $("#coapplicant").show();
        } else {
            $("#coapplicant").hide();
        }
    }

    function getPlansData(creditor_id) {
        //alert(creditor_id);return false;
        if (creditor_id != "") {
            //$("#plan_id").html("<option value=''>Select</option>");
            //$("#plan_id").select2();
            $.ajax({
                url: "<?php echo base_url(); ?>customerleads/getPlans",
                data: {
                    creditor_id: creditor_id
                },
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    if (res['status'] == "success") {
                        if (res['option'] != "") {
                            $("#plan_id").html("<option value=''>Select</option>" + res['option']);
                            $("#plan_id").select2();
                        } else {
                            $("#plan_id").html("<option value=''>Select</option>");
                            $("#plan_id").select2();
                        }
                    } else {
                        $("#plan_id").html("<option value=''>Select</option>");
                        $("#plan_id").select2();
                    }
                }
            });
        }
    }

    $(document).ready(function() {

        $("#dob").datepicker({
            dateFormat: 'dd-mm-yy',
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100Y:-18Y",
            maxDate: "-18Y",
            minDate: "-56Y +1D"
        });

    });


    var vRules = {
        creditor_id: {
            required: true
        },
        plan_id: {
            required: true
        },
        sm_id: {
            required: true
        },
        salutation: {
            required: true
        },
        first_name: {
            required: true,
            lettersonlys: true
        },
        //middle_name: { required: true, lettersonlys: true },
        last_name: {
            required: true,
            lettersonlys: true
        },
        gender: {
            required: true
        },
        dob: {
            required: true
        },
        email_id: {
            required: true,
            email: true
        },
        mobile_number: {
            required: true,
            mob: true
        },
        // lan_id: {
        // 	required: true
        // },
        // loan_amt: {
        // 	required: true,
        // 	number: true
        // },
        // tenure: {
        // 	required: true,
        // 	digits: true,
        // 	minlength: 1,
        // 	maxlength: 2
        // },
        // coapplicant_no: {
        // 	required: true,
        // 	digits: true,
        // 	max: 9
        // },
        location_id: {
            required: true
        }
    };

    var vMessages = {
        creditor_id: {
            required: "Please select creditor."
        },
        plan_id: {
            required: "Please select plan."
        },
        sm_id: {
            required: "Please select sm."
        },
        salutation: {
            required: "Please select salutation."
        },
        first_name: {
            required: "Please enter first name."
        },
        //middle_name: { required: "Please enter middle name." },
        last_name: {
            required: "Please enter last name."
        },
        gender: {
            required: "Please select gender."
        },
        dob: {
            required: "Please enter DOB."
        },
        email_id: {
            required: "Please enter valid email id."
        },
        mobile_number: {
            required: "Please enter mobile number."
        },
        lan_id: {
            required: "Plese enter Lan ID."
        },
        loan_amt: {
            required: "Please enter loan amount."
        },
        tenure: {
            required: "Please enter tenure."
        },
        coapplicant_no: {
            required: "Please enter no. of co-applicants."
        },
        location_id: {
            required: "Please select location."
        }
    };

    $("#form-validate").validate({
        rules: vRules,
        messages: vMessages,
        submitHandler: function(form) {
            $("#form-validate").ajaxSubmit({
                url: "<?php echo base_url(); ?>telelead/addLead",
                type: 'post',
                dataType: 'JSON',
                cache: false,
                clearForm: false,
                beforeSubmit: function(arr, $form, options) {
                    //$(".btn-primary").hide();
                    //return false;
                },
                success: function(response) {
                    //$(".btn-primary").show();
                    if (response.status == true) {
                        displayMsg("success", response.message);
                        setTimeout(function() {
                            window.location = '<?php echo base_url(); ?>teleproposal?leadid='+response.lead_id;
                        }, 2000);
                    } else {
                        displayMsg("error", response.msg);
                        $(".btn-primary").show();
                        return false;
                    }
                }
            });
        }
    });
    //checkvalidInput
    $("body").on("keyup", ".checkvalidInput", function(e) {

        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^0-9]/g, function(str) {
                    return "";
                })
            );
        }
        return;
    });
    $('.checkvalidInputText').keypress(function(e) {
        var regex = new RegExp(/^[a-zA-Z\s]+$/);
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });

    document.title = "Customer Lead";
</script>
</body>

</html>