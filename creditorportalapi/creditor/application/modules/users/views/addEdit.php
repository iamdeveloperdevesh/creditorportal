<?php //echo "<pre>";print_r($user_details);exit;;?>
<div class="page-body">

	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Users</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>users/addEdit"><i data-feather="home"></i> </li>
						<li class="breadcrumb-item active">Users</li></a>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<!-- Container-fluid Ends-->

	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="card tab2-card">
					<div class="card-header">
						<h5>User Details</h5>
					</div>
					<div class="card-body">
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade active show"  aria-labelledby="account-tab">
								<form  id="form-validate" method="post" action="#">
									<input type="hidden" id="employee_id" name="employee_id" value="<?php if(!empty($user_details['employee_id'])){echo $user_details['employee_id'];}?>" />
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Roles</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="role_id" id="role_id" onchange="chkRole(this.value);" >
											<option value="">Select</option>
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
									</div>
									<div class="form-group row">
										<label for="employee_fname" class="col-xl-3 col-md-4"><span>*</span> First Name</label>
										<input class="form-control col-xl-8 col-md-7" name="employee_fname" id="employee_fname" type="text" value="<?php if(!empty($user_details['employee_fname'])){echo $user_details['employee_fname'];}?>" >
									</div>
									<div class="form-group row">
										<label for="employee_mname" class="col-xl-3 col-md-4">Middle Name</label>
										<input class="form-control col-xl-8 col-md-7" name="employee_mname" id="employee_mname" type="text" value="<?php if(!empty($user_details['employee_mname'])){echo $user_details['employee_mname'];}?>" >
									</div>
									<div class="form-group row">
										<label for="employee_lname" class="col-xl-3 col-md-4"><span>*</span> Last Name</label>
										<input class="form-control col-xl-8 col-md-7" name="employee_lname" id="employee_lname" type="text" value="<?php if(!empty($user_details['employee_lname'])){echo $user_details['employee_lname'];}?>" >
									</div>
									<div class="form-group row">
										<label for="employee_code" class="col-xl-3 col-md-4">Employee Code</label>
										<input class="form-control col-xl-8 col-md-7" name="employee_code" id="employee_code" type="text" value="<?php if(!empty($user_details['employee_code'])){echo $user_details['employee_code'];}?>" >
									</div>
									<div class="form-group row">
										<label for="email_id" class="col-xl-3 col-md-4"><span>*</span> Email ID</label>
										<input class="form-control col-xl-8 col-md-7" name="email_id" id="email_id" type="text" value="<?php if(!empty($user_details['email_id'])){echo $user_details['email_id'];}?>" >
									</div>
									<div class="form-group row">
										<label for="mobile_number" class="col-xl-3 col-md-4"><span>*</span> Mobile</label>
										<input class="form-control col-xl-8 col-md-7" name="mobile_number" id="mobile_number" type="text" value="<?php if(!empty($user_details['mobile_number'])){echo $user_details['mobile_number'];}?>" >
									</div>
									<div class="form-group row">
										<label for="user_name" class="col-xl-3 col-md-4"><span>*</span> Username</label>
										<input class="form-control col-xl-8 col-md-7" name="user_name" id="user_name" type="text" value="<?php if(!empty($user_details['user_name'])){echo $user_details['user_name'];}?>" >
									</div>
									<?php if(!empty($user_details['employee_id'])){?>
										<br/><input type="checkbox" name="chengepass" id="chengepass" value="1" class="divpass"> Change Password?<br/>
									<?php }?>
									<div class="passDiv" id="passDiv" style="display:none;">
										<div class="form-group row">
											<label for="password" class="col-xl-3 col-md-4"><span>*</span> Password </label>
											<input class="form-control col-xl-8 col-md-7" name="password" id="password" type="password" value="<?php if(!empty($user_details['password'])){echo $user_details['password'];}?>" >
											
										</div>
										
										<div class="form-group row">
											<label for="confirm_password" class="col-xl-3 col-md-4"><span>*</span> Confirm Password</label>
											<input class="form-control col-xl-8 col-md-7" name="confirm_password" id="confirm_password" type="password" value="<?php if(!empty($user_details['confirm_password'])){echo $user_details['confirm_password'];}?>" >
										</div>
									</div>
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4">Joining Date</label>
										<input class="form-control col-xl-8 col-md-7" name="date_of_joining" id="date_of_joining" type="text" value="<?php if(!empty($user_details['date_of_joining'])){echo $user_details['date_of_joining'];}?>" >
									</div>
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Status</label>
										<select class="form-control col-xl-8 col-md-7" name="isactive" id="isactive" >
											<option value="">Select</option>
											<option value="1" <?php if(!empty($user_details['isactive']) && $user_details['isactive'] == 1){?> selected <?php }?>>Active</option>
											<option value="0" <?php if(!empty($user_details['isactive']) && $user_details['isactive'] == 0){?> selected <?php }?>>In-Active</option>
										</select>
									</div>
									
									
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
														<input type="text" id="zone<?php echo $i;?>" name="zone[]" class="form-control " value="<?php echo $value['zone'];?>" required />
													</td>
													<td>
														<input type="text" id="state<?php echo $i;?>" name="state[]" class="form-control " value="<?php echo $value['state'];?>" required />
													</td>
													<td>
														<input type="text" id="city<?php echo $i;?>" name="city[]" class="form-control " value="<?php echo $value['city'];?>" required />
													</td>
													<td>
														<button type="button" class="btn-primary remove"><i class="fa fa-remove"></i></button>
													</td>
												</tr>
												
												<?php }}else{ ?>
												<tr class="location_tr">
													<td>
														<input type="text" id="zone1" name="zone[]" class="form-control " required />
													</td>
													<td>
														<input type="text" id="state1" name="state[]" class="form-control " required />
													</td>
													<td>
														<input type="text" id="city1" name="city[]" class="form-control " required />
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
									
									
									<div class="pull-center">
										<button type="submit" class="btn btn-primary">Save</button>
										<a href="<?php echo base_url();?>users" class="btn btn-primary">Cancel</a>
									</div>
								</form>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Container-fluid Ends-->

</div>		
<script type="text/javascript">
	
    $("#chengepass").on("click",function() {
		$(".passDiv").toggle(this.checked);
	});
	
	function chkRole(role_id){
		//alert(role_id);return false;
		if(role_id == 3){
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
			changeYear: true
		});
		
		$(".addLocation").click(function()
		{
			var index = 1;
			$("#tbl_locations tbody tr.location_tr").each(function(){
				index = index + 1;
			});
			$html = '<tr class="location_tr">'+
						'<td>'+
							'<input type="text" id="zone'+index+'" name="zone[]" class="form-control " required />'+
						'</td>'+
						'<td>'+
							'<input type="text" id="state'+index+'" name="state[]" class="form-control " required />'+
						'</td>'+
						'<td>'+
							'<input type="text" id="city'+index+'" name="city[]" class="form-control " required />'+
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
		employee_fname:{required:true},
		employee_lname:{required:true},
		email_id:{required:true,email:true},
		mobile_number:{required:true, digits:true},
		password:{required:true},
		confirm_password:{required:true,equalTo:password},
	};
	
	var vMessages = {
		role_id:{required:"Please select."},
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


