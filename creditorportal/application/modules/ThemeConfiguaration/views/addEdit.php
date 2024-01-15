<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>My profile - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2"></div>
				</div>
			</div>
			<div class="card-body">
			<form  id="form-validate" method="post" action="#">
				<div class="row">
					<div class="col-md-3 mb-3">
						<label for="employee_fname" class="col-form-label">First Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="employee_fname" id="employee_fname" type="text" value="<?php if(!empty($user_details['employee_fname'])){echo $user_details['employee_fname'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="employee_mname" class="col-form-label">Middle Name</label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="employee_mname" id="employee_mname" type="text" value="<?php if(!empty($user_details['employee_mname'])){echo $user_details['employee_mname'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="employee_lname" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="employee_lname" id="employee_lname" type="text" value="<?php if(!empty($user_details['employee_lname'])){echo $user_details['employee_lname'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="employee_code" class="col-form-label">Employee ID</label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="employee_code" id="employee_code" type="text" value="<?php if(!empty($user_details['employee_code'])){echo $user_details['employee_code'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="email_id" class="col-form-label">Email Id<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="email_id" id="email_id" type="text" value="<?php if(!empty($user_details['email_id'])){echo $user_details['email_id'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">alternate_email</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="mobile_number" class="col-form-label">Mobile No<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="mobile_number" id="mobile_number" type="text" value="<?php if(!empty($user_details['mobile_number'])){echo $user_details['mobile_number'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="user_name" class="col-form-label">Username<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="user_name" id="user_name" type="text" value="<?php if(!empty($user_details['user_name'])){echo $user_details['user_name'];}?>" aria-describedby="inputGroupPrepend" readonly />
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
					 <!--  <input type="checkbox" name="chengepass" id="chengepass" value="1" class="divpass"> Change Password? -->
                                       <div class="col-md-3 mb-3"> <label style="visibility: hidden;" class="mt-2 display-sm-lbl">For Space</label><div class="custom-control custom-checkbox">
                      			<input class="custom-control-input divpass" type="checkbox" id="chengepass" name="chengepass">
					<label class="custom-control-label" for="chengepass">Change Password?</label>
					</div></div>
					<div class="passDiv col-md-6 col-12 row-mob" id="passDiv" style="display:none; padding:0px;">
					
						<div class="col-md-6 mb-3 col-12">
							<label for="password" class="col-form-label">Password*</label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter ..." name="password" id="password" type="password" value="<?php if(!empty($user_details['password'])){echo $user_details['password'];}?>" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-6 mb-3 col-12">
							<label for="confirm_password" class="col-form-label">Confirm Password</label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter ..." name="confirm_password" id="confirm_password" type="password" value="<?php if(!empty($user_details['confirm_password'])){echo $user_details['confirm_password'];}?>" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="date_of_joining" class="col-form-label">Joining Date</label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="date_of_joining" id="date_of_joining" type="text" value="<?php if(!empty($user_details['date_of_joining'])){echo date('d-m-Y', strtotime($user_details['date_of_joining']));}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">calendar_today</span></span>
							</div>
						</div>
					</div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Logo<span class="lbl-star">*</span></label>
                        <?php

                        if (!empty($user_details['creditor_logo'])) {
                            ?>
                            <div class="input-group-prepend">
                                <a href="<?= $user_details['creditor_logo']; ?>" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;
                                <a href="javascript:void(0);" class="file-change f-btn" data-file="creditor_logo">Change <i class="ti-pencil"></i></a>
                            </div>
                            <?php
                        } else {

                            ?>
                            <input type="file" id="creditor_logo" name="creditor_logo" accept="image/jpg, image/jpeg, image/png, application/pdf" />

                            <?php
                        }
                        ?>
                    </div>
					<!--<div class="col-md-3 mb-3">
						<label for="isactive" class="col-form-label">Status</label>
						<div class="input-group">
							<select class="form-control" name="isactive" id="isactive"> 
								<option value="">Select Status</option>
								<option value="1" <?php if(!empty($user_details['isactive']) && $user_details['isactive'] == 1){?> selected <?php }?>>Active</option>
								<option value="0" <?php if(!empty($user_details['isactive']) && $user_details['isactive'] == 0){?> selected <?php }?>>In-Active</option>							
							</select>
							
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
							</div>
						</div>
					</div>-->
				</div>
				
				
				<div class="row mt-4">
					<div class="col-md-1 col-6 text-left">
						<button type="submit" class="btn smt-btn btn-primary">Save</button>
					</div>
					<div class="col-md-2 col-6 text-right">
						<a href="<?php echo base_url();?>myprofile"><button type="button" class="btn cnl-btn">Cancel</button></a>
					</div>
				</div>
			</form>	
			</div>
		</div>
	</div>
</div>			
<script type="text/javascript">

jQuery.validator.addMethod("lettersonlys", function(value, element) {
    return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Letters only please");

jQuery.validator.addMethod("mob", function(value, element) {
    return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
}, "Enter valid 10 digit No. starting with 6 to 9.");

    $("#chengepass").on("click",function() {
		$(".passDiv").toggle(this.checked);
	});
	
	$(function() {
		$("#date_of_joining").datepicker({ 
			dateFormat: 'dd-mm-yy',
			changeMonth: true,
			changeYear: true,
			maxDate: new Date()
		});
		
	});

	var vRules = {
		employee_fname:{required:true, lettersonlys:true},
		employee_mname:{lettersonlys:true},
		employee_lname:{required:true, lettersonlys:true},
		email_id:{required:true,email:true},
		mobile_number:{required:true, mob:true},
		password:{required:true},
		confirm_password:{required:true,equalTo:password},
	};
	
	var vMessages = {
		employee_fname:{required:"Please enter first name."},
		employee_lname:{required:"Please enter last name."},
		email_id:{required:"Please enter email id."},
		mobile_number:{required:"Please enter mobile."},
		password:{required:"Please enter password."},
		confirm_password:{required:"Please enter confirm password",equalTo:"Password does not match."},
	};

	$("#form-validate").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) 
		{
			$("#form-validate").ajaxSubmit({
				url: "<?php echo base_url();?>myprofile/submitForm", 
				type: 'post',
				dataType: 'JSON',
				cache: false,
				clearForm: false, 
				beforeSubmit : function(arr, $form, options){
					$(".btn-primary").hide();
					//return false;
				},
				success: function (response) 
				{
					$(".btn-primary").show();
					if(response.success)
					{
						displayMsg("success", response.msg);
						setTimeout(function(){
							window.location = "<?php echo base_url();?>myprofile/addEdit";
						},2000);
					}
					else
					{	
						displayMsg("error", response.msg);
						$(".btn-primary").show();
						return false;
					}
				}
			});
		}
	});

	document.title = "My Profile";
	
</script>
</body>
</html>


