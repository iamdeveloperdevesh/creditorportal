<?php //echo "<pre>";print_r($user_details);exit;;?>
<div class="page-body">

	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>My profile</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>myprofile/addEdit"><i data-feather="home"></i> </li>
						<li class="breadcrumb-item active">My profile </li></a>
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
						<h5>User Detais</h5>
					</div>
					<div class="card-body">
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade active show"  aria-labelledby="account-tab">
								<form  id="form-validate" method="post" action="#">
									<div class="form-group row">
										<label for="employee_fname" class="col-xl-3 col-md-4"><span>*</span> First Name</label>
										<input class="form-control col-xl-8 col-md-7" name="employee_fname" id="employee_fname" type="text" value="<?php if(!empty($user_details['employee_fname'])){echo $user_details['employee_fname'];}?>" required="">
									</div>
									<div class="form-group row">
										<label for="employee_mname" class="col-xl-3 col-md-4">Middle Name</label>
										<input class="form-control col-xl-8 col-md-7" name="employee_mname" id="employee_mname" type="text" value="<?php if(!empty($user_details['employee_mname'])){echo $user_details['employee_mname'];}?>" >
									</div>
									<div class="form-group row">
										<label for="employee_lname" class="col-xl-3 col-md-4"><span>*</span> Last Name</label>
										<input class="form-control col-xl-8 col-md-7" name="employee_lname" id="employee_lname" type="text" value="<?php if(!empty($user_details['employee_lname'])){echo $user_details['employee_lname'];}?>" required="">
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
										<input class="form-control col-xl-8 col-md-7" name="mobile_number" id="mobile_number" type="text" value="<?php if(!empty($user_details['mobile_number'])){echo $user_details['mobile_number'];}?>" required="">
									</div>
									<div class="form-group row">
										<label for="user_name" class="col-xl-3 col-md-4"><span>*</span> Username</label>
										<input class="form-control col-xl-8 col-md-7" name="user_name" id="user_name" type="text" value="<?php if(!empty($user_details['user_name'])){echo $user_details['user_name'];}?>" required="" readonly>
									</div>
									<br/><input type="checkbox" name="chengepass" id="chengepass" value="1" class="divpass"> Change Password?<br/>
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
									
									<div class="pull-center">
										<button type="submit" class="btn btn-primary">Save</button>
										<a href="<?php echo base_url();?>home" class="btn btn-primary">Cancel</a>
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
	
	$(function() {
		$("#date_of_joining").datepicker({ 
			dateFormat: 'dd-mm-yy',
			changeMonth: true,
			changeYear: true
		});
		
	});

	var vRules = {
		employee_fname:{required:true},
		employee_lname:{required:true},
		email_id:{required:true,email:true},
		mobile_number:{required:true, digits:true},
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


