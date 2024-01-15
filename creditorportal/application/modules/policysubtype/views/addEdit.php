<?php //echo "ddddd<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<?php if(empty($getDetails[0]['policy_sub_type_id'])){ ?>
									<p>Add Policy Sub Type - <i class="ti-user"></i></p>
						        <?php } else { ?>
									<p>Update Policy Sub Type - <i class="ti-user"></i></p>
						<?php } ?>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
			<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
			<input type="hidden" id="policy_sub_type_id" name="policy_sub_type_id" value="<?php if(!empty($getDetails[0]['policy_sub_type_id'])){echo $getDetails[0]['policy_sub_type_id'];}?>" />
				<div class="row">
					<div class="col-md-3 mb-3">
						<label class="col-form-label" for="policy_sub_type_name">Policy Sub Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." id="policy_sub_type_name" name="policy_sub_type_name" type="text" value="<?php if(!empty($getDetails[0]['policy_sub_type_name'])){echo $getDetails[0]['policy_sub_type_name'];}?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="creditor_code" class="col-form-label">Policy Type<span class="lbl-star">*</span></label>
						<div class="controls input-group">
                                <select class="input-xlarge form-control" name="policy_type_id" id="policy_type_id" style="height: calc(2.7rem + 0px);">
									<option value="">Select</option>
									<?php foreach($policytypes as $type){ ?>
									<option value="<?php echo $type['policy_type_id']; ?>" <?php if(!empty($getDetails[0]['policy_type_id']) && $getDetails[0]['policy_type_id'] == $type['policy_type_id']){ echo "selected"; }?>><?php echo $type['policy_type_name']; ?></option>
									<?php } ?>
								</select>
<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">insert_drive_file</span></span>
                        </div>
					</div> 
					
					<div class="col-md-3 mb-3">
						<label for="creditor_code" class="col-form-label">Status<span class="lbl-star">*</span></label>
						<div class="controls input-group">
                                <select class="input-xlarge form-control" name="isactive" id="isactive">
									<option value="">Select</option>
									<option value="1" <?php if(!empty($getDetails[0]['isactive']) && $getDetails[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
									<option value="0" <?php if(!empty($getDetails[0]['isactive']) && $getDetails[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
								</select>
<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
                        </div>
					</div>
                    <div class="col-md-3 mb-3">
                        <div class="col-sm-6 form-group">
                            <label class="col-form-label">Upload Logo</label>
                            <input id="demo2" class=" details" type="file"  placeholder="Drag and drop, or Browser yor file" name="demo2" />
                            <!-- <input type="text" class="form-control" placeholder="Enter Full Name"> -->
                        </div>
					</div>
					
				</div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="col-form-label">Description</label>
                        <textarea class="form-control" id="description"name="description"><?php if(!empty($getDetails[0]['description'])){echo $getDetails[0]['description'];}?></textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="col-sm-6 form-group">
                            <label class="col-form-label">Description Image</label>
                            <input id="demo3" class=" details" type="file"  placeholder="Drag and drop, or Browser yor file" name="demo3" />
                            <!-- <input type="text" class="form-control" placeholder="Enter Full Name"> -->
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="col-form-label" for="policy_sub_type_name">Policy Name</label>
                        <div class="input-group">
                            <input class="form-control" placeholder="Enter ..." id="gadget_name" name="gadget_name" type="text" value="<?php if(!empty($getDetails[0]['gadget_name'])){echo $getDetails[0]['gadget_name'];}?>" aria-describedby="inputGroupPrepend" />
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="col-form-label" for="policy_sub_type_name">Short Name</label>
                        <div class="input-group">
                            <input class="form-control" placeholder="Enter ..." id="short_name" name="short_name" type="text" value="<?php if(!empty($getDetails[0]['code'])){echo $getDetails[0]['code'];}?>" aria-describedby="inputGroupPrepend" />
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                </div>
				
				<div class="row mt-4">
					<div class="col-md-1 col-6 text-left">
					<button class="btn smt-btn">Save</button>
					</div>
					<div class="col-md-2 col-6 text-right">
						<a href="<?php echo base_url();?>policysubtype" class="btn cnl-btn">Cancel</a>
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
	policy_sub_type_name:{required:true, lettersonlys:true},
	policy_type_id:{required:true},
	isactive:{required:true}
};

var vMessages = {
	policy_sub_type_name:{required:"Please enter name."},
	policy_type_id:{required:"Please select type."},
	isactive:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>policysubtype/submitForm";
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
						window.location = "<?php echo base_url();?>policysubtype";
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



document.title = "Add/Edit Policy Sub Type";
</script>