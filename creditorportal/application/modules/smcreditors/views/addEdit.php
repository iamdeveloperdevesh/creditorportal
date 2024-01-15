<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>SM Partner Mapping - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="sm_creditor_id" name="sm_creditor_id" value="<?php if(!empty($user_details[0]['sm_creditor_id'])){echo $user_details[0]['sm_creditor_id'];}?>" />
					
					<div class="row">
						<?php if(empty($user_details[0]['sm_creditor_id'])){?>
							<div class="col-md-3 mb-3">
								<label for="creditor_id" class="col-form-label">Partner<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="select2 form-control" name="creditor_id" id="creditor_id" onchange="getSMData(this.value);" >
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
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							
							<div class="col-md-3 mb-3">
								<label for="sm_id" class="col-form-label">SM<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="select2 form-control" name="sm_id[]" id="sm_id" multiple style="padding: 3px;">
										<!--<option value="">Select</option>
										<?php 
										if(!empty($sm)){
											for($i=0; $i < sizeof($sm); $i++){
										?>
											<option value="<?php echo $sm[$i]['employee_id']; ?>" <?php if(!empty($user_details[0]['sm_id']) && $user_details[0]['sm_id'] == $sm[$i]['employee_id']){?> selected <?php }?>><?php echo $sm[$i]['employee_full_name']; ?></option>
										<?php 
											}
										}
										?>
										-->
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							
							
						<?php }else{?>
							<div class="col-md-3 mb-3">
								<label for="creditor_id" class="col-form-label">Partner<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="select2 form-control" name="creditor_id" id="creditor_id">
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
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
						
							<div class="col-md-3 mb-3">
								<label for="sm_id" class="col-form-label">SM<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="select2 form-control" name="sm_id" id="sm_id" style="padding: 3px;">
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
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							
							
						<?php }?>
						
						<div class="col-md-3 mb-3">
							<label for="isactive" class="col-form-label">Status<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="form-control" name="isactive" id="isactive" style="height: calc(2.7rem + -2px);">
									<option value="">Select</option>
									<option value="1" <?php if(!empty($user_details[0]['isactive']) && $user_details[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
									<option value="0" <?php if(!empty($user_details[0]['isactive']) && $user_details[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
								</div>
							</div>
						</div>
						
					</div>
					<div class="row mt-3">
						<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>smcreditors"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
					</div>
				</form>	
			</div>
		</div>
	</div>
</div>
<!-- end: Content -->
<script type="text/javascript">

function getSMData(creditor_id)
{
	//alert(creditor_id);return false;
	if(creditor_id != "" )
	{
		$.ajax({
			url:"<?php echo base_url();?>smcreditors/getSMData",
			data:{creditor_id:creditor_id},
			type:'post',
			dataType: 'json',
			success: function(res)
			{
				if(res['status']=="success")
				{
					if(res['option'] != "")
					{
						$("#sm_id").html(res['option']);
						// $("#sm_id").select2();
					}
					else
					{
						$("#sm_id").html("");
						// $("#sm_id").select2();
					}
				}
				else
				{	
					$("#sm_id").html("");
					// $("#sm_id").select2();
				}
			}
		});
	}
}

$( document ).ready(function() {
	
	$.validator.addMethod("multiSelection", function (value, element) {
		var count = $(element).find('option:selected').length;
		return count > 0;
	  });

	$.validator.messages.multiSelection = 'Select Atleast One College';
	
});

var vRules = {
	creditor_id:{required:true},
	isactive:{required:true}
};
var vMessages = {
	creditor_id:{required:"Please select creditor."},
	isactive:{required:"Please select status."}
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