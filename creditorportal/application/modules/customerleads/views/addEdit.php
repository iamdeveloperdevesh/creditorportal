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
				<form id="form-validate" method="post" action="#">
					<input type="hidden" id="lead_id" name="lead_id" value="<?php if (!empty($user_details['lead_id'])) {
																				echo $user_details['lead_id'];
																			} ?>" />
					<div class="row">
						<div class="col-md-4 mb-3">
							<label for="creditor_id" class="col-form-label">Partner <span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="select2 form-control" name="creditor_id" id="creditor_id" onchange="getPlansData(this.value);">
									<option value="">Select Partner</option>
									<?php
									if (!empty($creditors)) {
										for ($i = 0; $i < sizeof($creditors); $i++) {
									?>
											<option value="<?php echo $creditors[$i]['creditor_id']; ?>"><?php echo $creditors[$i]['creaditor_name']; ?></option>
									<?php
										}
									}
									?>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="plan_id" class="col-form-label">Plan<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="select2 form-control" name="plan_id" id="plan_id" onchange="getPlanDetails(this.value)">
									<option value="">Select Plan</option>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
								</div>
							</div>
						</div>
						<?php if ($_SESSION['webpanel']['role_id'] != 3) { ?>
							<div class="col-md-4 mb-3">
								<label for="sm_id" class="col-form-label">SM*</label>
								<div class="input-group">
									<select class="select2 form-control" name="sm_id" id="sm_id">
										<option value="">Select</option>
										<?php
										if (!empty($sm)) {
											for ($i = 0; $i < sizeof($sm); $i++) {
										?>
												<option value="<?php echo $sm[$i]['employee_id']; ?>"><?php echo $sm[$i]['employee_full_name']; ?></option>
										<?php
											}
										}
										?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
						<?php } ?>
						<div class="col-md-4 mb-3">
							<label for="salutation" class="col-form-label">Salutation<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="select2 form-control" name="salutation" id="salutation" onchange="changeGender(this.value);">
									<option value="">Select Salutation</option>
									<?php
									if (!empty($salutation)) {
										for ($i = 0; $i < sizeof($salutation); $i++) {
									?>
											<option value="<?php echo $salutation[$i]; ?>"><?php echo $salutation[$i]; ?></option>
									<?php
										}
									}
									?>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="lan_id" class="col-form-label">LAN Number</label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter LAN Number" name="lan_id" id="lan_id" type="text" value="" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="loan_amt" class="col-form-label">Loan Amount (&#x20b9;)</label>
							<div class="input-group">
								<input class="form-control checkvalidInput" placeholder="Enter Loan Amount" name="loan_amt" id="loan_amt" type="text" value="" aria-describedby="inputGroupPrepend" maxlength="10" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">receipt_long</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="tenure" class="col-form-label">Loan Tenure</label>
							<div class="input-group">
								<input class="form-control checkvalidInput" placeholder="Enter Loan Tenure" name="tenure" id="tenure" type="text" value="" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="first_name" class="col-form-label">First Name<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control checkvalidInputText" placeholder="Enter First Name" name="first_name" id="first_name" type="text" value="" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="middle_name" class="col-form-label">Middle Name</label>
							<div class="input-group">
								<input class="form-control checkvalidInputText" placeholder="Enter Middle Name" name="middle_name" id="middle_name" type="text" value="" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="last_name" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control checkvalidInputText" placeholder="Enter Last Name" name="last_name" id="last_name" type="text" value="" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="gender" class="col-form-label">Gender<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="select2 form-control" name="gender" id="gender">
									<!--<option value="">Select</option>-->
									<?php
									//if(!empty($gender)){
									//for($i=0; $i < sizeof($gender); $i++){
									?>
									<!--<option value="<?php echo $gender[$i]; ?>" ><?php echo $gender[$i]; ?></option>-->
									<?php
									//}
									//}
									?>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">person</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="dob" class="col-form-label">Date of Birth<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter Date of Birth" name="dob" id="dob" type="text" value="" aria-describedby="inputGroupPrepend" autocomplete="off" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">calendar_today</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="email_id" class="col-form-label">Email Id<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter Email Id" name="email_id" id="email_id" type="text" value="" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">alternate_email</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="mobile_number" class="col-form-label">Mobile Number<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter Mobile Number" name="mobile_number" id="mobile_number" type="text" value="" aria-describedby="inputGroupPrepend" maxlength="10" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
								</div>
							</div>
						</div>

						<div class="col-md-4 mb-3">
							<label for="location_id" class="col-form-label">SM Location<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="select2 form-control" name="location_id" id="location_id">
									<option value=""> Select SM Location</option>
									<?php
									if (!empty($locations)) {
										for ($i = 0; $i < sizeof($locations); $i++) {
									?>
											<option value="<?php echo $locations[$i]['location_id']; ?>"><?php echo $locations[$i]['location_name']; ?></option>
									<?php
										}
									}
									?>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
								</div>
							</div>
						</div>

						<div class="col-md-4 mb-3">
							<label for="is_coapplicant" class="col-form-label">Co-Applicant?</label>
							<div class="input-group">
								<select class="select2 form-control" name="is_coapplicant" id="is_coapplicant" onchange="coapplicant(this.value);">
									<option value="N">No</option>
									<option value="Y">Yes</option>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
								</div>
							</div>
						</div>

						<div class="col-md-4 mb-3" style="display:none;" id="coapplicant">
							<label for="is_coapplicant" class="col-form-label">No. Of Co-Applicant<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter No. Of Co-Applicant" name="coapplicant_no" id="coapplicant_no" type="text" value="" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>

					</div>
					<div class="row mt-4">
						<div class="col-md-1 text-left col-6">
							<button type="submit" class="btn smt-btn btn-primary">Save</button>
						</div>
						<div class="col-md-2 text-right col-6">
							<a href="<?php echo base_url(); ?>customerleads"><button type="button" class="btn cnl-btn">Cancel</button></a>
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
        checkCDbalance(plan_id).then(e => {
            if(e !== 'true'){
                displayMsg("error", e);
                $("#plan_id").select2("val", "");
                return;
            }else{
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
                                $("#dob").val('');
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

        });

	}
    function checkCDbalance(plan_id) {

        return new Promise(function (resolve, reject) {
            $.ajax({
                url: "/quotes/checkCDbalanceThreshold",
                type: "POST",
                //async: false,
                dataType: "json",
                data:{plan_id},
                success: function(response) {
                    if(response.status == 200){
                        resolve(response.msg);
                    }else{
                        resolve(response.msg);

                    }
                }


            });
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
			changeMonth: true,
			changeYear: true,
			//yearRange: '-55:-18',
			//yearRange: "-100:" + new Date('Y'),
			//maxDate: new Date()
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
				url: "<?php echo base_url(); ?>customerleads/submitForm",
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
					if (response.success) {
						displayMsg("success", response.msg);
						setTimeout(function() {
							window.location = "<?php echo base_url(); ?>customerleads";
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