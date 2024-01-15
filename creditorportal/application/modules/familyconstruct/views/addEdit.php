<?php //echo "<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						        <?php if(empty($getDetails[0]['id'])){ ?>
								<p>Add Family Member - <i class="ti-user"></i></p>
                                <?php } else { ?>
                             	<p>Update Family Member - <i class="ti-user"></i></p>
                                <?php } ?>
                    </div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
			<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
			<input type="hidden" id="family_construct_id" name="family_construct_id" value="<?php if(!empty($getDetails[0]['id'])){echo $getDetails[0]['id'];}?>" />
                <div class="row">
					<div class="col-md-3 mb-3">
						<label for="insurer_name" class="col-form-label">Family Member<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter Family member name" id="member_type" name="member_type" type="text" value="<?php if(!empty($getDetails[0]['member_type'])){echo $getDetails[0]['member_type'];}?>" aria-describedby="inputGroupPrepend" />
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
					<a href="<?php echo base_url();?>familyconstruct/" class="btn cnl-btn">Cancel</a>
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
	member_type:{required:true, lettersonlys:true}
};

var vMessages = {
	member_type:{required:"This feild is required."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>familyconstruct/submitForm";
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
						window.location = "<?php echo base_url();?>familyconstruct";
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

document.title = "Add/Edit Family Construct";
</script>