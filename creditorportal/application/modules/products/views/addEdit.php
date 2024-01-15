<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>UW Workflow - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="uw_case_id" name="uw_case_id" value="<?php if(!empty($uwworkflow_details[0]['uw_case_id'])){echo $uwworkflow_details[0]['uw_case_id'];}?>" />
					<input type="hidden" id="creditor_id" name="creditor_id" value="<?php if(!empty($plan_details[0]['creditor_id'])){echo $plan_details[0]['creditor_id'];}?>" />
					<input type="hidden" id="master_plan_id" name="master_plan_id" value="<?php if(!empty($plan_details[0]['plan_id'])){echo $plan_details[0]['plan_id'];}?>" />
					<div class="row">
						<div class="col-md-3 mb-3">
							<label for="validationCustomUsername" class="col-form-label">UW Amount<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input id="sum_insured" name="sum_insured" type="text" class="form-control" placeholder="" aria-describedby="inputGroupPrepend" value="<?php if(!empty($uwworkflow_details[0]['sum_insured'])){echo $uwworkflow_details[0]['sum_insured'];}?>" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">add_task</span></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 col-12 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-12 text-right"><a href="<?php echo base_url();?>products"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
					</div>
				</form>	
			</div>
		</div>
	</div>
</div>
<!-- end: Content -->			
<script type="text/javascript">
$( document ).ready(function() {
});

var vRules = {
	sum_insured:{required:true, digits:true}
};
var vMessages = {
	sum_insured:{required:"Please UW amount."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>products/submitForm";
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
				//$(".btn-primary").show();
				if(response.success)
				{
					displayMsg("success",response.msg);
					setTimeout(function(){
						//window.location = "<?php echo base_url();?>permission";
						location.reload(true);
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

document.title = "Add/Edit NSTP RULE";
</script>