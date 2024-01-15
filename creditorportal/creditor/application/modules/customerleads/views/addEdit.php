<?php //echo "<pre>";print_r($salutation);exit;;?>
<div class="page-body">
	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Leads</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>customerleads/addEdit"><i data-feather="home"></i> </li>
						<li class="breadcrumb-item active">Leads</li></a>
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
						<h5>Lead Details</h5>
					</div>
					<div class="card-body">
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade active show"  aria-labelledby="account-tab">
								<form  id="form-validate" method="post" action="#">
									<input type="hidden" id="lead_id" name="lead_id" value="<?php if(!empty($user_details['lead_id'])){echo $user_details['lead_id'];}?>" />
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Partner</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="creditor_id" id="creditor_id" onchange="getPlansData(this.value);">
											<option value="">Select</option>
											<?php 
											if(!empty($creditors)){
												for($i=0; $i < sizeof($creditors); $i++){
											?>
												<option value="<?php echo $creditors[$i]['creditor_id']; ?>" ><?php echo $creditors[$i]['creaditor_name']; ?></option>
											<?php 
												}
											}
											?>
										</select>
									</div>
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Plan</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="plan_id" id="plan_id" >
											<option value="">Select</option>
										</select>
									</div>
									<?php if($_SESSION['webpanel']['role_id'] != 3){?>
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>SM</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="sm_id" id="sm_id">
											<option value="">Select</option>
											<?php 
											if(!empty($sm)){
												for($i=0; $i < sizeof($sm); $i++){
											?>
												<option value="<?php echo $sm[$i]['employee_id']; ?>" ><?php echo $sm[$i]['employee_full_name']; ?></option>
											<?php 
												}
											}
											?>
										</select>
									</div>
									<?php }?>
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Salutation</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="salutation" id="salutation" >
											<option value="">Select</option>
											<?php 
											if(!empty($salutation)){
												for($i=0; $i < sizeof($salutation); $i++){
											?>
												<option value="<?php echo $salutation[$i]; ?>" ><?php echo $salutation[$i]; ?></option>
											<?php 
												}
											}
											?>
										</select>
									</div>
									
									<div class="form-group row">
										<label for="lan_id" class="col-xl-3 col-md-4"><span>*</span> LAN ID</label>
										<input class="form-control col-xl-8 col-md-7" name="lan_id" id="lan_id" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="loan_amt" class="col-xl-3 col-md-4"><span>*</span> Loan Amount</label>
										<input class="form-control col-xl-8 col-md-7" name="loan_amt" id="loan_amt" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="tenure" class="col-xl-3 col-md-4"><span>*</span> Tenure</label>
										<input class="form-control col-xl-8 col-md-7" name="tenure" id="tenure" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="first_name" class="col-xl-3 col-md-4"><span>*</span> First Name</label>
										<input class="form-control col-xl-8 col-md-7" name="first_name" id="first_name" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="employee_mname" class="col-xl-3 col-md-4">Middle Name</label>
										<input class="form-control col-xl-8 col-md-7" name="middle_name" id="middle_name" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="employee_lname" class="col-xl-3 col-md-4"><span>*</span> Last Name</label>
										<input class="form-control col-xl-8 col-md-7" name="last_name" id="last_name" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Gender</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="gender" id="gender" >
											<option value="">Select</option>
											<?php 
											if(!empty($gender)){
												for($i=0; $i < sizeof($gender); $i++){
											?>
												<option value="<?php echo $gender[$i]; ?>" ><?php echo $gender[$i]; ?></option>
											<?php 
												}
											}
											?>
										</select>
									</div>
									<div class="form-group row">
										<label for="employee_code" class="col-xl-3 col-md-4"><span>*</span>DOB</label>
										<input class="form-control col-xl-8 col-md-7" name="dob" id="dob" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="email_id" class="col-xl-3 col-md-4"><span>*</span> Email ID</label>
										<input class="form-control col-xl-8 col-md-7" name="email_id" id="email_id" type="text" value="" />
									</div>
									<div class="form-group row">
										<label for="mobile_number" class="col-xl-3 col-md-4"><span>*</span> Mobile</label>
										<input class="form-control col-xl-8 col-md-7" name="mobile_number" id="mobile_number" type="text" value="" />
									</div>
									
									<div class="form-group row">
										<label for="is_coapplicant" class="col-xl-3 col-md-4"><span>*</span>Co-Appicant?</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="is_coapplicant" id="is_coapplicant" onchange="coapplicant(this.value);">
											<option value="N">No</option>
											<option value="Y">Yes</option>
										</select>
									</div>
									
									<div class="form-group row" style="display:none;" id="coapplicant">
										<label for="coapplicant_no" class="col-xl-3 col-md-4"><span>*</span> No. Of Co-Appicant</label>
										<input class="form-control col-xl-8 col-md-7" name="coapplicant_no" id="coapplicant_no" type="text" value="" />
									</div>
									
									<div class="pull-center" >
										<button type="submit" class="btn btn-primary">Save</button>
										<a href="<?php echo base_url();?>customerleads" class="btn btn-primary">Cancel</a>
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

function coapplicant(val){
	if(val == "Y"){
		$("#coapplicant").show();
	}else{
		$("#coapplicant").hide();
	}
}

function getPlansData(creditor_id)
	{
		//alert(creditor_id);return false;
		if(creditor_id != "" )
		{
			$.ajax({
				url:"<?php echo base_url();?>customerleads/getPlans",
				data:{creditor_id:creditor_id},
				type:'post',
				dataType: 'json',
				success: function(res)
				{
					if(res['status']=="success")
					{
						if(res['option'] != "")
						{
							$("#plan_id").html("<option value=''>Select</option>"+res['option']);
							// $("#subcategory_id").select2();
						}
						else
						{
							$("#plan_id").html("<option value=''>Select</option>");
							// $("#subcategory_id").select2();
						}
					}
					else
					{	
						$("#plan_id").html("<option value=''>Select</option>");
						// $("#subcategory_id").select2();
					}
				}
			});
		}
	}
	
$( document ).ready(function() 
{		
    
	$("#dob").datepicker({ 
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true
	});	
	
});	

	var vRules = {
		creditor_id:{required:true},
		plan_id:{required:true},
		sm_id:{required:true},
		salutation:{required:true},
		first_name:{required:true},
		middle_name:{required:true},
		last_name:{required:true},
		gender:{required:true},
		dob:{required:true},
		email_id:{required:true,email:true},
		mobile_number:{required:true, digits:true},
		lan_id:{required:true},
		loan_amt:{required:true, number:true},
		tenure:{required:true, digits:true},
		coapplicant_no:{required:true, digits:true}
	};
	
	var vMessages = {
		creditor_id:{required:"Please select creditor."},
		plan_id:{required:"Please select plan."},
		sm_id:{required:"Please select sm."},
		salutation:{required:"Please select salutation."},
		first_name:{required:"Please enter first name."},
		middle_name:{required:"Please enter middle name."},
		last_name:{required:"Please enter last name."},
		gender:{required:"Please select gender."},
		dob:{required:"Please enter DOB."},
		email_id:{required:"Please enter valid email id."},
		mobile_number:{required:"Please enter mobile number."},
		lan_id:{required:"Plese enter Lan ID."},
		loan_amt:{required:"Please enter loan amount."},
		tenure:{required:"Please enter tenure."},
		coapplicant_no:{required:"Please enter no. of co-applicants."}
	};

	$("#form-validate").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) 
		{
			$("#form-validate").ajaxSubmit({
				url: "<?php echo base_url();?>customerleads/submitForm", 
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
							window.location = "<?php echo base_url();?>customerleads";
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

	document.title = "Customer Lead";
	
</script>
</body>
</html>


