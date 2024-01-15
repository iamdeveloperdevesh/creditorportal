<?php //echo "ddddd<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Add Insurer - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
			<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
			<input type="hidden" id="insurer_id" name="insurer_id" value="<?php if(!empty($getDetails[0]['insurer_id'])){echo $getDetails[0]['insurer_id'];}?>" />
				<div class="row">
					<div class="col-md-3 mb-3">
						<label class="col-form-label" for="insurer_name">Insurer Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Insurer Name" id="insurer_name" name="insurer_name" type="text" value="<?php if(!empty($getDetails[0]['insurer_name'])){echo $getDetails[0]['insurer_name'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="creditor_code" class="col-form-label">Insurer Code<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Insurer Code" id="insurer_code" name="insurer_code" type="text" value="<?php if(!empty($getDetails[0]['insurer_code'])){echo $getDetails[0]['insurer_code'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
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
						<a href="<?php echo base_url();?>insurer" class="btn cnl-btn">Cancel</a>
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
	insurer_name:{required:true, lettersonlys:true},
	insurer_code:{required:true},
	isactive:{required:true}
};

var vMessages = {
	insurer_name:{required:"Please enter name."},
	insurer_code:{required:"Please enter code."},
	isactive:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>insurer/submitForm";
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
						window.location = "<?php echo base_url();?>insurer";
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

document.title = "Add/Edit Insurer";
</script>


