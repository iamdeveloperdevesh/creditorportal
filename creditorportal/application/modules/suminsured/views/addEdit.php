<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<?php if(empty($getDetails[0]['suminsured_type_id'])){ ?>
                               <p>Add Sum Insured Type - <i class="ti-user"></i></p>
							    <?php } else { ?>
                                <p>Update Sum Insured Type - <i class="ti-user"></i></p>
					    <?php } ?>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
			<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
			<input type="hidden" id="suminsured_type_id" name="suminsured_type_id" value="<?php if(!empty($getDetails[0]['suminsured_type_id'])){echo $getDetails[0]['suminsured_type_id'];}?>" />
				<div class="row">
					<div class="col-md-3 mb-3">
						<label class="col-form-label" for="insurer_name">Sum Insured Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Sum Insured Name" id="suminsured_type" name="suminsured_type" type="text" value="<?php if(!empty($getDetails[0]['suminsured_type'])){echo $getDetails[0]['suminsured_type'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="isactive" class="col-form-label">Status<span class="lbl-star">*</span></label>
						<div class="controls input-group">
								<select class="input-xlarge form-control" name="isactive" id="isactive" style="height: calc(2.7rem + 0px);">
									<option value="">Select</option>
									<option value="1" <?php if(!empty($getDetails[0]['isactive']) && $getDetails[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
									<option value="0" <?php if(!empty($getDetails[0]['isactive']) && $getDetails[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
								</select>
<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
						</div>
					</div>
				</div>
				
				<div class="row mt-4">
					<div class="col-md-1 col-6 text-left">
					<button class="btn smt-btn">Save</button>
					</div>
					<div class="col-md-2 col-6 text-right">
						<a href="<?php echo base_url();?>suminsured" class="btn cnl-btn">Cancel</a>
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

var vRules = {
	suminsured_type:{required:true, lettersonlys:true},
	isactive:{required:true}
};

var vMessages = {
	suminsured_type:{required:"Enter sum insured type name."},
	isactive:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>suminsured/submitForm";
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
						window.location = "<?php echo base_url();?>suminsured";
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

document.title = "Add/Edit Sum Insured";
</script>