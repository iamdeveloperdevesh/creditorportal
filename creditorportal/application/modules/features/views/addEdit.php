<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Features - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<?php //echo "<PRE>";print_r($plan_details);exit; ?>
			<div class="card-body">
				<form class="form-horizontal" id="form-plan" method="post" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" value="<?php echo $plan_details[0]['id'];?>" name="id" autocomplete="off">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Select Partner<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="form-control" name="creditor" id="creditor">
										<option value="">Select Partner</option>
										<?php foreach ($datalist->creditors as $creditor) { 
											$sel = '';
											if($plan_details[0]['creditor_id'] ==  $creditor->creditor_id){
												$sel = "selected";
											}?>
											<option value="<?php echo $creditor->creditor_id; ?>" <?php echo $sel;?>><?php echo $creditor->creaditor_name; ?></option>
										<?php } ?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Plan Name<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="form-control" id="plan_name" name="plan_name">
										<option value="">Select Plan</option>
										<?php foreach ($plans as $value) { 
											$sel = '';
											if($plan_details[0]['plan_id'] ==  $value['plan_id']){
												$sel = "selected";
											}?>
											<option value="<?php echo $value['plan_id']; ?>" <?php echo $sel;?>><?php echo $value['plan_name']; ?></option>
										<?php } ?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Feature<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="form-control" name="feature" id="feature">
										<?php foreach ($datalist->features as $features) { 
											$sel = '';
											if($plan_details[0]['feature_id'] ==  $features->id){
												$sel = "selected";
											}
											?>
											<option value="<?php echo $features->id; ?>" <?php echo $sel;?>><?php echo $features->name; ?></option>
										<?php } ?>
									</select>
									<div class="error"><span class="planerror"></span></div>
								</div>
							</div>

						</div>

						<div class="row">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Title</label>
								<div class="input-group">
									<input type="text" placeholder="Title" value="<?php echo $plan_details[0]['title'];?>" class="form-control" name="title" autocomplete="off">
									
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Short Description</label>
								<div class="input-group">
									<input type="text" placeholder="Short Description" value="<?php echo $plan_details[0]['short_description'];?>" class="form-control" name="short_description" autocomplete="off">
									
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="is_active" class="col-form-label">Status</label>
								<div class="input-group">
									<select class="form-control" name="is_active" id="is_active" style="height: calc(2.7rem + 0px);">
										<option value="">Select</option>
										<option value="1" <?php if(!empty($plan_details[0]['isactive']) && $plan_details[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
										<option value="0" <?php if(!empty($plan_details[0]['isactive']) && $plan_details[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
									</div>
								</div>
							</div>

						</div>
						<div class="row col-md-12 mb-3">
							<label for="content" class="col-form-label">Content<span class="lbl-star">*</span></label>
							<div class="input-group">
								<textarea class="form-control editor" name="long_description" id="content"><?php echo $plan_details[0]['long_description'];?></textarea>
							</div>
						</div>
						<div class="row col-md-6 mt-4" id="addplanbtn">
							<label for="validationCustomUsername" class="col-form-label">File<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input id="form_file" name="form_file" type="file" class="form-control" placeholder="" aria-describedby="inputGroupPrepend" />

								<input id="input_file" value="<?php if(!empty($plan_details[0]['file_name'])){echo $plan_details[0]['file_name'];}?>" name="input_file" type="hidden" >			

								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">cloud_upload</span></span>
								</div>
							</div>
							<?php if(!empty($plan_details[0]['file_name'])){?>
									<div class="row col-md-6 mt-4" > 
									<a class="view-btn-1" href="<?php echo FRONT_URL; ?>/assets/features/<?php echo $plan_details[0]['file_name']; ?>" target="_blank">View File <i class="ti-eye"></i></a>
								</div>
								<?php }?>	
						</div>
						<div class="row mt-4" id="addplanbtn">
							<div class="col-md-1 col-6 text-left">
								<button class="btn smt-btn">Save</button>
							</div>
							<div class="col-md-2 col-6 text-right">
								<!-- <button class="btn cnl-btn">Cancel</button> -->
								<a href="<?php echo base_url(); ?>features" class="btn cnl-btn">Cancel</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end: Content -->			
<script type="text/javascript">

	$(document).on("change","#creditor",function(){
		var creditor_id = $(this).val();
		alert(creditor_id);
		$.ajax({
            url: "/features/fetch_plans",
            type: "POST",
            async: false,
            data: {"creditor_id":creditor_id},
            dataType: "json",
            success: function(response) {
            	// debugger;
            	console.log(response);
            	$("#plan_name").html('').append(response.html);
            	
            }
        });
	});

	$( document ).ready(function() 
{		
    
	<?php if(!empty($user_details['assignment_declaration_id'])){ ?>
		getPlansData('<?php echo $user_details["creditor_id"]; ?>', '<?php echo $user_details["plan_id"]; ?>');
	<?php }  ?>
	
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
	
});	


$( document ).ready(function() {
});

var vRules = {
	sum_insured:{required:true, digits:true}
};
var vMessages = {
	sum_insured:{required:"Please UW amount."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>products/submitForm";
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
						//window.location = "<?php echo base_url();?>permission";
						location.reload(true);
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

var vRules = {
	creditor:{required:true},
	plan_name:{required:true},
	feature:{required:true},
	//title:{required:true},
	//short_description:{required:true},
	is_active:{required:true},
	//long_description:{required:true},
	//form_file:{required:true}
};
var vMessages = {
	creditor:{required:"Please select creditor."},
	plan_name:{required:"Please select plan."},
	feature:{required:"Please select feature."},
//	title:{required:"Please enter title."},
//	short_description:{required:"Please enter short description."},
	is_active:{required:"Please select status."},
	//long_description:{required:"Please enter long description."},
	//form_file:{required:"Please select image."},
};

$("#form-plan").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		debugger;
		var act = "<?php echo base_url();?>features/submitForm";
		$("#form-plan").ajaxSubmit({
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
						window.location = "<?php echo base_url();?>features";
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

document.title = "Add/Edit Feature";
</script>