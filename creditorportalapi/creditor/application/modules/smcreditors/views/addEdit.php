<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>SM Partner Mapping</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>smcreditors"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Add SM Partner Mapping</li>
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
						<input type="hidden" id="sm_creditor_id" name="sm_creditor_id" value="<?php if(!empty($user_details[0]['sm_creditor_id'])){echo $user_details[0]['sm_creditor_id'];}?>" />
						
						<?php if(empty($user_details[0]['sm_creditor_id'])){?>
							
							<div class="control-group">
								<label class="control-label" for="sm_id">SM*</label>
								<div class="controls">
									<select class="select2 form-control col-xl-8 col-md-7" name="sm_id" id="sm_id">
										<option value="">Select</option>
										<?php 
										if(!empty($sm)){
											for($i=0; $i < sizeof($sm); $i++){
										?>
											<option value="<?php echo $sm[$i]['employee_id']; ?>" <?php if(!empty($user_details[0]['sm_id']) && $user_details[0]['sm_id'] == $sm[$i]['employee_id']){?> selected <?php }?>><?php echo $sm[$i]['employee_full_name']; ?></option>
										<?php 
											}
										}
										?>
									</select>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="branch_name">Creditor*</label>
								<div class="controls">
									<select class="select2 form-control col-xl-8 col-md-7" name="creditor_id[]" id="creditor_id" multiple>
										<option value="">Select</option>
										<?php 
										if(!empty($creditors)){
											for($i=0; $i < sizeof($creditors); $i++){
										?>
											<option value="<?php echo $creditors[$i]['creditor_id']; ?>"><?php echo $creditors[$i]['creaditor_name']; ?></option>
										<?php 
											}
										}
										?>
									</select>
								</div>
							</div>
							
							
						<?php }else{?>
							
							<div class="control-group">
								<label class="control-label" for="sm_id">SM*</label>
								<div class="controls">
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
								<label class="control-label" for="email_id">Status*</label>
								<div class="controls">
									<select class="form-control col-xl-8 col-md-7" name="isactive" id="isactive">
										<option value="">Select</option>
										<option value="1" <?php if(!empty($user_details[0]['isactive']) && $user_details[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
										<option value="0" <?php if(!empty($user_details[0]['isactive']) && $user_details[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
									</select>
								</div>
							</div>
						<?php }?>
						
						
						
						<div class="clearfix" style="height: 10px; width: 100%; float: left; display: inline;">&nbsp;</div>
						
						<div class="form-actions">
							<button type="submit" class="btn btn-primary">Submit</button>
							<a href="<?php echo base_url();?>smcreditors"><button class="btn" type="button">Cancel</button></a>
						</div>
					</fieldset>
				</form>
			</div>
		</div><!--/span-->
	</div><!--/row-->
</div><!-- end: Content -->
			
<script>

$( document ).ready(function() {
	
	$.validator.addMethod("multiSelection", function (value, element) {
		var count = $(element).find('option:selected').length;
		return count > 0;
	  });

	$.validator.messages.multiSelection = 'Select Atleast One College';
	
});

var vRules = {
	<?php if(empty($user_details[0]['sm_creditor_id'])){?>
				'creditor_id[]':{required:true},	
	<?php }else{?>
		creditor_id:{required:true},
	<?php }?>
	
	sm_id:{required:true}
	
	
};
var vMessages = {
	<?php if(empty($user_details[0]['sm_creditor_id'])){?>
		'creditor_id[]':{required:"Please select creditors."},
	<?php }else{?>	
		creditor_id:{required:true},
	<?php }?>
	sm_id:{required:"Please select SM."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>smcreditors/submitForm";
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
						window.location = "<?php echo base_url();?>smcreditors";
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

document.title = "Add/Edit SM Partner Mapping";

</script>