<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] {
  -moz-appearance: textfield;
}
</style><!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Add Branches - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2"></div>
				</div>
			</div>
			<div class="card-body">
			<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
			<input type="hidden" id="branch_id" name="branch_id" value="<?php if(!empty($user_details[0]['branch_id'])){echo $user_details[0]['branch_id'];}?>" />
				<div class="row">
					<div class="col-md-3 mb-3">
						<label for="branch_name" class="col-form-label">Branch Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Branch Name" id="branch_name" name="branch_name" type="text" value="<?php if(!empty($user_details[0]['branch_name'])){echo $user_details[0]['branch_name'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="creditor_id" class="col-form-label">Partners<span class="lbl-star">*</span></label>
						<div class="input-group">
							<select class="form-control" name="creditor_id" id="creditor_id"> 
								<option value="">Select Creditors</option> 
								<?php 
								if(!empty($creditors)){
									for($i=0; $i < sizeof($creditors); $i++){
								?>
									<option value="<?php echo $creditors[$i]['creditor_id']; ?>" <?php if(!empty($user_details[0]['creditor_id']) && $user_details[0]['creditor_id'] == $creditors[$i]['creditor_id']){?> selected <?php }?>><?php echo $creditors[$i]['creaditor_name']; ?></option>
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
					<div class="col-md-3 mb-3">
						<label for="location_id" class="col-form-label">Location<span class="lbl-star">*</span></label>
						<div class="input-group">
							<select class="select2 form-control" name="location_id" id="location_id"> 
								<option value="">Select Location</option> 
								<?php 
								if(!empty($locations)){
									for($i=0; $i < sizeof($locations); $i++){
								?>
									<option value="<?php echo $locations[$i]['location_id']; ?>" <?php if(!empty($user_details[0]['location_id']) && $user_details[0]['location_id'] == $locations[$i]['location_id']){?> selected <?php }?>><?php echo $locations[$i]['location_name']; ?></option>
								<?php 
									}
								}
								?>
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="email_id" class="col-form-label">Partner Email<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Partner Email" id="email_id" name="email_id" type="text" value="<?php if(!empty($user_details[0]['email_id'])){echo $user_details[0]['email_id'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">alternate_email</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="contact_no" class="col-form-label">Partner Mobile<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Partner Mobile" pattern="[6-9][0-9]{9}" maxlength="10" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" id="contact_no" name="contact_no" type="number" value="<?php if(!empty($user_details[0]['contact_no'])){echo $user_details[0]['contact_no'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="isactive" class="col-form-label">Status<span class="lbl-star">*</span></label>
						<div class="input-group">
							<select class="form-control" name="isactive" id="isactive"> 
								<option value="">Select Status</option>
								<option value="1" <?php if(!empty($user_details[0]['isactive']) && $user_details[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
								<option value="0" <?php if(!empty($user_details[0]['isactive']) && $user_details[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
							</div>
						</div>
					</div>
				</div>
				<div class="row mt-4">
					<div class="col-md-1 col-6 text-left">
						<button type="submit" class="btn smt-btn btn-primary">Save</button>
					</div>
					<div class="col-md-2 col-6 text-right">
						<a href="<?php echo base_url();?>creditorbranches"><button type="button" class="btn cnl-btn">Cancel</button></a>
					</div>
				</div>
			</form>	
			</div>
		</div>
	</div>
</div>
<!-- end: Content -->
<script type="text/javascript">
jQuery.validator.addMethod("lettersonlys", function(value, element) {
    return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Letters only please");

jQuery.validator.addMethod("mob", function(value, element) {
    return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
}, "Enter valid 10 digit No. starting with 6 to 9.");

$( document ).ready(function() {
});

var vRules = {
	branch_name:{required:true, lettersonlys:true},
	creditor_id:{required:true},
	location_id:{required:true},
	contact_no:{required:true, mob:true},
	email_id:{required:true,email:true},
	isactive:{required:true}
};

var vMessages = {
	branch_name:{required:"Please enter branch name."},
	creditor_id:{required:"Please select creditor."},
	location_id:{required:"Please select location."},
	contact_no:{required:"Please enter contact number."},
	email_id:{required:"Please enter email id."},
	isactive:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>creditorbranches/submitForm";
		$("#form-validate").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false,
			beforeSubmit : function(arr, $form, options){
				//$(".btn-primary").hide();
				//return false;
			},
			success: function (response) 
			{
				$(".btn-primary").show();
				if(response.success)
				{
					displayMsg("success",response.msg);
					setTimeout(function(){
						window.location = "<?php echo base_url();?>creditorbranches";
					},2000);

				}
				else
				{	
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	}
});

document.title = "Add/Edit Branches";

</script>