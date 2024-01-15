<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Single Journey - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="single_journey_id" name="single_journey_id" value="<?php if(!empty($getDetails[0]['id'])){echo $getDetails[0]['id'];}?>" />
					
					<div class="row">
						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Partner Name<span class="lbl-star">*</span></label>
							<div class="input-group">

								<select class="form-control" name="creditor_id" id="creditor_id">
										<option value="">Select Partner</option>
										<?php foreach ($getCreditorDetails as $creditor) { 
											$selected = "";
											if($getDetails[0]['creditor_id'] == $creditor['creditor_id']){
												$selected = "selected";
											}?>
											<option value="<?php echo $creditor['creditor_id']; ?>" <?php echo $selected?>><?php echo $creditor['creaditor_name']; ?></option>
										<?php } ?>
									</select>
								
							</div>
						</div>
						<div class="col-md-4 mb-3">
								<label for="is_active" class="col-form-label">Status</label>
								<div class="input-group">
									<select class="form-control" name="is_active" id="is_active" style="height: calc(2.7rem + 0px);">
										<option value="">Select</option>
										<option value="1" <?php if(!empty($getDetails[0]['is_active']) && $getDetails[0]['is_active'] == 1){?> selected <?php }?>>Active</option>
										<option value="0" <?php if(!empty($getDetails[0]['is_active']) && $getDetails[0]['is_active'] == 0){?> selected <?php }?>>In-Active</option>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
									</div>
								</div>
							</div>
					</div>
					<div class="row mt-3">
						<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>singlejourney"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
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
	creditor_id:{required:true},
	is_active:{required:true}
};
var vMessages = {
	creditor_id:{required:"Please select partner."},
	is_active:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>singlejourney/submitForm";
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
						window.location = "<?php echo base_url();?>singlejourney";
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

document.title = "Add/Edit Company";

</script>