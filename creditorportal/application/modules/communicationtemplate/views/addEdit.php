<style>
	.error {
		top: 100%;
	}
</style>
<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Communication Template - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="template_id" name="template_id" value="<?php if(!empty($getDetails[0]['id'])){echo $getDetails[0]['id'];}?>" />
					
					<div class="row">
						<div class="col-md-6 mb-3">
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
						<div class="col-md-6 mb-3">
							<label for="dropout_event" class="col-form-label">Event Name<span class="lbl-star">*</span></label>
							<div class="input-group">

								<select class="form-control" name="dropout_event" id="dropout_event">
										<option value="">Select Event</option>
										<?php foreach ($events as $key => $event) { 
											?>
											<option value="<?php echo $event['id']; ?>" <?php if(!empty($getDetails[0]['dropout_event']) && $getDetails[0]['dropout_event'] == $event['id']){ echo 'selected';}?>><?php echo $event['name']; ?></option>
										<?php } ?>
								</select>							
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="subject" class="col-form-label">Subject<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input type="text" placeholder="Enter Subject" class="form-control" name="subject" autocomplete="off" <?php if(!empty($getDetails[0]['subject'])){ echo 'value="'.$getDetails[0]['subject'].'"'; }?>>
								
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="type" class="col-form-label">Type<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="form-control" name="type" id="type">
									<option value="">Select Type</option>
									<option value="email" <?php if(!empty($getDetails[0]['type']) && $getDetails[0]['type'] == 'email'){?> selected <?php }?>>Email</option>
									<option value="sms" <?php if(!empty($getDetails[0]['type']) && $getDetails[0]['type'] == 'sms'){?> selected <?php }?>>SMS</option>
									<option value="whatsapp" <?php if(!empty($getDetails[0]['type']) && $getDetails[0]['type'] == 'whatsapp'){?> selected <?php }?>>Whatsapp</option>
										
								</select>
								
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="content" class="col-form-label">Content<span class="lbl-star">*</span></label>
							<div class="input-group">
								<textarea class="form-control editor" name="content" id="content"> <?php if(!empty($getDetails[0]['content'])){ echo $getDetails[0]['content']; }?></textarea>
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="isactive" class="col-form-label">Status<span class="lbl-star">*</span></label>
							<div class="input-group">
								<select class="form-control" name="isactive" id="isactive" style="height: calc(2.7rem + 0px);">
									<option value="">Select</option>
									<option value="1" <?php if(!empty($getDetails[0]['isactive']) && $getDetails[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
									<option value="0" <?php if(!empty($getDetails[0]['isactive']) && $getDetails[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
								</div>
							</div>
						</div>
						
					</div>
							
					<div class="row mt-3">
						<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>communicationtemplate"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
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
var config = {enterMode : CKEDITOR.ENTER_BR, height:200, filebrowserBrowseUrl: '../js/ckeditor/filemanager/index.html', scrollbars:'yes',
			toolbar_Full:
			[
						['Source', 'Templates'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
						['Find','Replace','-','Subscript','Superscript'],
						['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],['BidiLtr', 'BidiRtl' ],
						['Maximize', 'ShowBlocks'],['Undo','Redo'],['Bold','Italic','Underline','Strike'],			
						['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],			
						['SelectAll','RemoveFormat'],'/',
						['Styles','Format','Font','FontSize'],
						['TextColor','BGColor'],								
						['Image','Flash','Table','HorizontalRule','Smiley'],
					],
					 width: "620px"
			};
	$('.editor').ckeditor(config);
	

var vRules = {
	creditor_id:{required:true},
	isactive:{required:true},
	subject:{required:true},
	type:{required:true},
	dropout_event:{required:true},
	content: {
        required: function(textarea) {
	       CKEDITOR.instances[textarea.id].updateElement();
	       var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
	       return editorcontent.length === 0;
	    }
    }
};


$("#form-validate").validate({
	ignore: [],
	rules: vRules,
	
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>communicationtemplate/submitForm";
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
						window.location = "<?php echo base_url();?>communicationtemplate";
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