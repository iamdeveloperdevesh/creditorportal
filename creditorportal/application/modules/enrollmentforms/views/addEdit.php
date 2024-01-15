<?php //echo DOC_ROOT_FRONT;exit;?>
<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Enrollment Form - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="enrollmentforms_id" name="enrollmentforms_id" value="<?php if(!empty($getDetails[0]['enrollmentforms_id'])){echo $getDetails[0]['enrollmentforms_id'];}?>" />
					<div class="row">
						<div class="col-md-3 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Title<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input id="form_title" name="form_title" type="text" class="form-control" placeholder="" aria-describedby="inputGroupPrepend" value="<?php if(!empty($getDetails[0]['form_title'])){echo $getDetails[0]['form_title'];}?>" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">add_task</span></span>
								</div>
							</div>
						</div>

						<div class="col-md-5 mb-3">
							<label for="validationCustomUsername" class="col-form-label">File<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input id="form_file" name="form_file" type="file" class="form-control" placeholder="" aria-describedby="inputGroupPrepend" />

								<input id="input_file" value="<?php if(!empty($getDetails[0]['form_file'])){echo $getDetails[0]['form_file'];}?>" name="input_file" type="hidden" >			

								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">cloud_upload</span></span>
								</div>
							</div>
						</div>
								<?php if(!empty($getDetails[0]['form_file'])){?>
									<div class="col-md-2 mb1-20"> <label style="visibility: hidden;" class="col-md-12 mt-3 display-sm-lbl">for space only </label>
									<a class="view-btn-1" href="<?php echo FRONT_URL; ?>/assets/enrollmentforms/<?php echo $getDetails[0]['form_file']; ?>" target="_blank">View File <i class="ti-eye"></i></a>
								</div>
								<?php }?>	
							

					</div>
					<div class="row mt-3">
						<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>enrollmentforms"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
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
	form_title:{required:true},
	form_file:{required:true}
};
var vMessages = {
	form_title:{required:"Please enter title."},
	form_file:{required:"Please select file."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>enrollmentforms/submitForm";
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
						window.location = "<?php echo base_url();?>enrollmentforms";
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

document.title = "Add/Edit Enrollment Form";
</script>