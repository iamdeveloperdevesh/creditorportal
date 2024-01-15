<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] {
  -moz-appearance: textfield;
}
.form-control[readonly] {
	background-color: #fff;
}
</style>
<?php //echo "<pre>";print_r($user_details);exit;?>
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Add Users - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2"></div>
				</div>
			</div>
			<div class="card-body">
			<form  id="form-validate" method="post" action="#">
			<input type="hidden" id="employee_id" name="employee_id" value="<?php if(!empty($user_details['employee_id'])){echo $user_details['employee_id'];}?>" />
				<div class="row">
					<div class="col-md-3 mb-3">
						<label for="role_id" class="col-form-label">Role<span class="lbl-star">*</span></label>
						<div class="input-group">
							<select class="select2 form-control" name="role_id" id="role_id" onchange="chkRole(this.value);"> 
								<option value="">Select Role</option>
								<?php 
								if(!empty($roles)){
									//foreach($roles as $cdrow)
									for($i=0; $i < sizeof($roles); $i++){
								?>
									<option value="<?php echo $roles[$i]['role_id']; ?>" <?php if(!empty($user_details['role_id']) && $user_details['role_id'] == $roles[$i]['role_id']){?> selected <?php }?>><?php echo $roles[$i]['role_name']; ?></option>
								<?php 
									}
								}
								?>  
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">admin_panel_settings</span></span>
							</div>

						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="role_id" class="col-form-label">Company<span class="lbl-star">*</span></label>
						<div class="input-group">
							<select class="select2 form-control" name="company_id" id="company_id" > 
								<option value="">Select Company</option>
								<?php 
								if(!empty($companies)){
									for($i=0; $i < sizeof($companies); $i++){
								?>
									<option value="<?php echo $companies[$i]['company_id']; ?>" <?php if(!empty($user_details['company_id']) && $user_details['company_id'] == $companies[$i]['company_id']){?> selected <?php }?>><?php echo $companies[$i]['company_name']; ?></option>
								<?php 
									}
								}
								?>  
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">admin_panel_settings</span></span>
							</div>

						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="employee_fname" class="col-form-label">First Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter First Name" name="employee_fname" id="employee_fname" type="text" value="<?php if(!empty($user_details['employee_fname'])){echo $user_details['employee_fname'];}?>" aria-describedby="inputGroupPrepend"  onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode === 32" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="employee_mname" class="col-form-label">Middle Name</label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Middle Name" name="employee_mname" id="employee_mname" type="text" value="<?php if(!empty($user_details['employee_mname'])){echo $user_details['employee_mname'];}?>" aria-describedby="inputGroupPrepend"  onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode === 32" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="employee_lname" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Last Name" name="employee_lname" id="employee_lname" type="text" value="<?php if(!empty($user_details['employee_lname'])){echo $user_details['employee_lname'];}?>" aria-describedby="inputGroupPrepend"  onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode === 32"/>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="employee_code" class="col-form-label">Employee ID</label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Employee ID" name="employee_code" id="employee_code" type="text" value="<?php if(!empty($user_details['employee_code'])){echo $user_details['employee_code'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="email_id" class="col-form-label">Email Id<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Email Id" name="email_id" id="email_id" type="text" value="<?php if(!empty($user_details['email_id'])){echo $user_details['email_id'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">alternate_email</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="mobile_number" class="col-form-label">Mobile No<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Mobile No" name="mobile_number" id="mobile_number" type="text" value="<?php if(!empty($user_details['mobile_number'])){echo $user_details['mobile_number'];}?>" aria-describedby="inputGroupPrepend" maxlength="10" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); $(this).val($(this).val().replace(/[^0-9]/g, ''));" />
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="user_name" class="col-form-label">Username<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Username" name="user_name" id="user_name" type="text" value="<?php if(!empty($user_details['user_name'])){echo $user_details['user_name'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
							</div>
						</div>
					</div>
					<div class="passDiv col-md-6 col-12 row-mob" id="passDiv" style="display:none; padding:0px;">
						<div class="col-md-6 mb-3 col-12">
							<label for="password" class="col-form-label">Password<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter Password" name="password" id="password" type="password" value="<?php if(!empty($user_details['password'])){echo $user_details['password'];}?>" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">lock</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-6 mb-3 col-12">
							<label for="confirm_password" class="col-form-label">Confirm Password<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control" placeholder="Enter Confirm Password" name="confirm_password" id="confirm_password" type="password" value="<?php if(!empty($user_details['confirm_password'])){echo $user_details['confirm_password'];}?>" aria-describedby="inputGroupPrepend" />
								<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">lock</span></span>
								</div>
							</div>
						</div>
					</div>
					<?php if(!empty($user_details['employee_id'])){?>
						<div class="col-md-3 mb-3"> <label style="visibility: hidden;" class="mt-2">For Space</label><div class="custom-control custom-checkbox">
                      			<input class="custom-control-input divpass" type="checkbox" id="chengepass" name="chengepass" value="1" >
					<label class="custom-control-label" for="chengepass">Change Password?</label>
					</div></div>
					<?php }?>
					
					<div class="col-md-3 mb-3">
						<label for="date_of_joining" class="col-form-label">Joining Date</label>
						<div class="input-group">
							<input class="form-control" readonly placeholder="DD-MM-YYYY" name="date_of_joining" id="date_of_joining" type="text" value="<?php if(!empty($user_details['date_of_joining'])){echo $user_details['date_of_joining'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">calendar_today</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
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
					</div>
					<div  class="col-md-6 col-12 row-mob" id="locations" style="display:none;">
						<div class="col-md-6" style="padding-left:0px;">
							<label for="location_id" class="col-form-label">Locations<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="form-control select2" name="location_id[]" id="location_id" multiple>
										<?php 
										if(!empty($locations)){
											for($i=0; $i < sizeof($locations); $i++){
												$sel = (in_array($locations[$i]['location_id'], $user_locations)) ? "selected" : '';
										?>
											<option value="<?php echo $locations[$i]['location_id']; ?>" <?php echo $sel; ?>><?php echo $locations[$i]['location_name']; ?></option>
										<?php 
											}
										}
										?>
								</select>
								
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-6" style="padding-left:0px;">
							<label for="location_id" class="col-form-label">Partner<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="form-control" name="creditor_id" id="creditor_id">
									<option value="">Select</option>
									<?php 
									if(!empty($creditors)){
										for($i=0; $i < sizeof($creditors); $i++){

											$selected = '';
											if($creditors[$i]['creditor_id'] == $sm_creditor_mapping_data[0]['creditor_id']){

												$selected = ' selected';
											}
									?>
										<option value="<?php echo $creditors[$i]['creditor_id']; ?>" <?=$selected;?>><?php echo $creditors[$i]['creaditor_name']; ?></option>
									<?php 
										}
									}
									?>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--
				<div class="row">
					<div class="control-group form-group" id="locations" style="display:none;">
						<label class="control-label"><span>User Locations*</span></label>
						<div class="controls">
							<table id="tbl_locations" class="responsive display table table-bordered">
								<thead>
									<tr>
										<th style="" >Zone</th>
										<th style="" >State</th>
										<th style="" >City</th>
										<th><button type="button" class="addLocation btn-primary" tabindex="66"><i class="fa fa-plus"></i></button></th>
									</tr>
								</thead>
								<tbody>
								<?php
								if(!empty($user_locations) && count($user_locations) > 0){
									$i = 0;
									foreach($user_locations as $key => $value){
										$i = ++$i;
								?>	
										
								<tr class="location_tr">
									<td>
										<input type="text" id="zone<?php echo $i;?>" name="zone[]" class="form-control " value="<?php echo $value['zone'];?>" required="" />
									</td>
									<td>
										<input type="text" id="state<?php echo $i;?>" name="state[]" class="form-control " value="<?php echo $value['state'];?>" required="" />
									</td>
									<td>
										<input type="text" id="city<?php echo $i;?>" name="city[]" class="form-control " value="<?php echo $value['city'];?>" required="" />
									</td>
									<td>
										<button type="button" class="btn-primary remove"><i class="fa fa-remove"></i></button>
									</td>
								</tr>
								
								<?php }}else{ ?>
								<tr class="location_tr">
									<td>
										<input type="text" id="zone1" name="zone[]" class="form-control" />
									</td>
									<td>
										<input type="text" id="state1" name="state[]" class="form-control" />
									</td>
									<td>
										<input type="text" id="city1" name="city[]" class="form-control" />
									</td>
									<td>
										<button type="button" class="btn-primary remove"><i class="fa fa-remove"></i></button>
									</td>
								</tr>
								
								<?php }?>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
				-->
				<div class="row mt-4">
					<div class="col-md-1 col-6 text-left">
						<button type="submit" class="btn smt-btn btn-primary">Save</button>
					</div>
					<div class="col-md-2 col-6 text-right">
						<a href="<?php echo base_url();?>users"><button type="button" class="btn cnl-btn">Cancel</button></a>
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
	
	function chkRole(role_id){
		//alert(role_id);return false;
		if(role_id == 3 || role_id == 10 || role_id == 12){
			$("#locations").show();
		}else{
			$("#locations").hide();
		}
	}

	$(function() {
		
		<?php if(!empty($user_details['employee_id'])){?>
			chkRole('<?php echo $user_details["role_id"]; ?>');
		<?php }?>
		
		if($("#employee_id").val() == ""){
			$("#passDiv").show();
		}
		
		$("#date_of_joining").datepicker({ 
			dateFormat: 'dd-mm-yy',
			changeMonth: true,
			changeYear: true,
			yearRange: "-100:" + new Date('Y'),
			maxDate: new Date()
		});
		
		$(".addLocation").click(function()
		{
			var index = 1;
			$("#tbl_locations tbody tr.location_tr").each(function(){
				index = index + 1;
			});
			$html = '<tr class="location_tr">'+
						'<td>'+
							'<input type="text" id="zone'+index+'" name="zone[]" class="form-control " required="" />'+
						'</td>'+
						'<td>'+
							'<input type="text" id="state'+index+'" name="state[]" class="form-control " required="" />'+
						'</td>'+
						'<td>'+
							'<input type="text" id="city'+index+'" name="city[]" class="form-control " required="" />'+
						'</td>'+
						'<td>'+
							'<button type="button" class="btn-primary remove">'+
								'<i class="fa fa-remove"></i>'+
							'</button>'+
						"</td>"+	
					"</tr>";
			$('#tbl_locations').find("tbody").append($html);
		});
		
		$('#tbl_locations').on('click', '.remove', function () {
			var table_row = $('#tbl_locations tbody  tr.location_tr').length;
			if(table_row == '1'){
				alert("Atleast one location is must ");
			}else{
				$(this).closest('tr').remove();
			}
		});
		
	});

	var vRules = {
		role_id:{required:true},
		employee_fname:{required:true, lettersonlys:true},
		employee_mname:{lettersonlys:true},
		employee_lname:{required:true, lettersonlys:true},
		email_id:{required:true,email:true},
		mobile_number:{required:true, mob:true},
		user_name:{required:true},
		password:{required:true},
		confirm_password:{required:true,equalTo:password},
		company_id:{required:true}
	};
	
	var vMessages = {
		role_id:{required:"Please select Role."},
		employee_fname:{required:"Please enter first name."},
		employee_lname:{required:"Please enter last name."},
		email_id:{required:"Please enter email id."},
		mobile_number:{required:"Please enter mobile.", minlength:"Minimum 10 digits required.", maxlength:"maximum 10 digits allowed."},
		user_name:{required:"Please enter username."},
		password:{required:"Please enter password."},
		confirm_password:{required:"Please enter confirm password",equalTo:"Password does not match."},
		company_id:{required:"Please select company."}
	};

	$("#form-validate").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) 
		{
			$("#form-validate").ajaxSubmit({
				url: "<?php echo base_url();?>users/submitForm", 
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
							window.location = "<?php echo base_url();?>users";
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

	document.title = "Users";
	
</script>
</body>
</html>


