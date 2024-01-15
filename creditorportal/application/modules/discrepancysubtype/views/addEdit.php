<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Discrepancy Subtype - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="discrepancy_subtype_id" name="discrepancy_subtype_id" value="<?php if(!empty($getDetails[0]['discrepancy_subtype_id'])){echo $getDetails[0]['discrepancy_subtype_id'];}?>" />
					
					<div class="row">
						<div class="col-md-3 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Discrepancy Type<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="select2 form-control" name="discrepancy_type_id" id="discrepancy_type_id" style="padding: 7px;">
								<option value="">Select</option>
								<?php 
								if(!empty($discrepancytype)){
									for($i=0; $i < sizeof($discrepancytype); $i++){
										$sel = (!empty($getDetails[0]['discrepancy_type_id']) && $getDetails[0]['discrepancy_type_id'] == $discrepancytype[$i]['discrepancy_type_id']) ? 'selected' : '';
								?>
									<option value="<?php echo $discrepancytype[$i]['discrepancy_type_id']; ?>" <?php echo $sel;?>><?php echo $discrepancytype[$i]['discrepancy_type']; ?></option>
								<?php 
									}
								}
								?>
							</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">add_task</span></span>
								</div>
							</div>
						</div>
						
						<div class="col-md-3 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Discrepancy Subtype<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input id="discrepancy_subtype" name="discrepancy_subtype" type="text" class="form-control" placeholder="" aria-describedby="inputGroupPrepend" value="<?php if(!empty($getDetails[0]['discrepancy_subtype'])){echo $getDetails[0]['discrepancy_subtype'];}?>" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">add_task</span></span>
								</div>
							</div>
						</div>
						
					</div>
					<div class="row mt-3">
						<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>discrepancysubtype"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
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
	discrepancy_type_id:{required:true},
	discrepancy_subtype:{required:true, lettersonlys:true}
};
var vMessages = {
	discrepancy_type_id:{required:"Please select type."},
	discrepancy_subtype:{required:"Please enter subtype."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>discrepancysubtype/submitForm";
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
						window.location = "<?php echo base_url();?>discrepancysubtype";
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

document.title = "Add/Edit Discrepancy Subtype";

</script>