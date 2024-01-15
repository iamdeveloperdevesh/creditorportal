<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Discrepancy Type - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="discrepancy_type_id" placeholder="Enter Discrepancy Type"  name="discrepancy_type_id" value="<?php if(!empty($getDetails[0]['discrepancy_type_id'])){echo $getDetails[0]['discrepancy_type_id'];}?>" />
					
					<div class="row">
						<div class="col-md-3 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Discrepancy Type<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input id="discrepancy_type" name="discrepancy_type" type="text" class="form-control" placeholder="" aria-describedby="inputGroupPrepend" value="<?php if(!empty($getDetails[0]['discrepancy_type'])){echo $getDetails[0]['discrepancy_type'];}?>" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">add_task</span></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>discrepancytype"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
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

$( document ).ready(function() {
});

var vRules = {
	discrepancy_type:{required:true, lettersonlys:true}
};
var vMessages = {
	discrepancy_type:{required:"Please Enter Discrepancy Type"}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>discrepancytype/submitForm";
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
						window.location = "<?php echo base_url();?>discrepancytype";
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

document.title = "Add/Edit Discrepancy Type";

</script>