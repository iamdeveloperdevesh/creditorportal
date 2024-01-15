<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Payment WorkFlow Master - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="payment_workflow_master_id" name="payment_workflow_master_id" value="<?php if(!empty($getDetails[0]['payment_workflow_master_id'])){echo $getDetails[0]['payment_workflow_master_id'];}?>" />
					<div class="row">
						<div class="col-md-3 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Work Flow<span class="lbl-star">*</span></label>
							<div class="input-group">
								<!-- <input id="workflow_name" name="workflow_name" type="text" class="form-control" placeholder="Enter Workflow" aria-describedby="inputGroupPrepend" value="<?php if(!empty($getDetails[0]['workflow_name'])){echo $getDetails[0]['workflow_name'];}?>" /> -->
								<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter Workflow" aria-describedby="inputGroupPrepend" >
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">add_task</span></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>paymentworkflowmaster"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
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
	workflow_name:{required:true, lettersonly:true}
};
var vMessages = {
	workflow_name:{required:"Please enter value."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>paymentworkflowmaster/submitForm";
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
						window.location = "<?php echo base_url();?>paymentworkflowmaster";
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

document.title = "Add/Edit Payment Workflow";
</script>