<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Add Branches</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>creditorbranches"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Add Branch</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
		
	<div class="card">
		<div class="card-body">
			<div class="box-content">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<fieldset>
						<input type="hidden" id="branch_id" name="branch_id" value="<?php if(!empty($user_details[0]['branch_id'])){echo $user_details[0]['branch_id'];}?>" />
						
						<div class="control-group">
							<label class="control-label" for="branch_name">Branch Name*</label>
							<div class="controls">
								<input class="input-xlarge form-control" id="branch_name" name="branch_name" type="text" value="<?php if(!empty($user_details[0]['branch_name'])){echo $user_details[0]['branch_name'];}?>">
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label" for="branch_name">Creditor*</label>
							<div class="controls">
								<select class="select2 form-control col-xl-8 col-md-7" name="creditor_id" id="creditor_id">
									<option value="">Select</option>
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
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label" for="branch_name">Location*</label>
							<div class="controls">
								<select class="select2 form-control col-xl-8 col-md-7" name="location_id" id="location_id">
									<option value="">Select</option>
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
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label" for="contact_no">Contact No.*</label>
							<div class="controls">
								<input class="input-xlarge form-control" id="contact_no" name="contact_no" type="text" value="<?php if(!empty($user_details[0]['contact_no'])){echo $user_details[0]['contact_no'];}?>">
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label" for="email_id">Email ID*</label>
							<div class="controls">
								<input class="input-xlarge form-control" id="email_id" name="email_id" type="text" value="<?php if(!empty($user_details[0]['email_id'])){echo $user_details[0]['email_id'];}?>">
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label" for="email_id">Status*</label>
							<div class="controls">
								<select class="form-control col-xl-8 col-md-7" name="isactive" id="isactive">
									<option value="">Select</option>
									<option value="1" <?php if(!empty($user_details[0]['isactive']) && $user_details[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
									<option value="0" <?php if(!empty($user_details[0]['isactive']) && $user_details[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
								</select>
							</div>
						</div>
						
						<div class="clearfix" style="height: 10px; width: 100%; float: left; display: inline;">&nbsp;</div>
						
						<div class="form-actions">
							<button type="submit" class="btn btn-primary">Submit</button>
							<a href="<?php echo base_url();?>creditorbranches"><button class="btn" type="button">Cancel</button></a>
						</div>
					</fieldset>
				</form>
			</div>
		</div><!--/span-->
	</div><!--/row-->
</div><!-- end: Content -->
			
<script>

$( document ).ready(function() {
});

var vRules = {
	branch_name:{required:true},
	creditor_id:{required:true},
	location_id:{required:true},
	contact_no:{required:true, digits:true},
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
				$(".btn-primary").hide();
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