<?php
//print_r($datalist);

// if (isset($datalist->plan_id)) {
	// echo "<pre>";
	// print_r($datalist);
// }
?>
<style>
.addPaymentMode {
		background:none;
		border:none;
		color:#fff;
	}
	.remove {
    border: none;
    background: none;
    color: #ff0000;
	}
	.select2-container-multi .select2-choices .select2-search-field input {
		padding: 0px !important;
		margin: 1px 0 !important;
		font-family: sans-serif;
		font-size: 100%;
		color: #666;
		outline: 0;
		border: 0;
		-webkit-box-shadow: none;
		box-shadow: none;
		background: transparent !important;
	}

	.error {
		color: red;
	}

	.label-primary {
		padding: 5px;
	}

	.collapse.in {
		display: block;
	}

	.collapse {
		display: none;
		padding: 15px;
	}

	.card .card-header {
		background-color: #e6e6e6;
		padding: 5px;
	}

	.select2-container-multi .select2-choices {
		background-image: none !important;
		border: 0 !important;
	}

	.agefield {
		margin-left: 15px;
		width: 120px;
		float: left;
	}
</style>
<script src="<?php echo base_url(); ?>assets/js/products.js"></script>
<div class="col-md-10" id="body">
	<div id="accordion3" class="according accordion-s2 mt-3">
		<div class="card card-member">
			<div class="card-header card-vif"><a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion44" aria-expanded="false"><span class="lbl-card">Plan Details - <i class="ti-file"></i></a></div>
			<div id="accordion44" class="card-vis-mar collapse show" data-parent="#accordion2" style="">
		
			
			<form class="form-horizontal" id="form-plan" method="post" enctype="multipart/form-data" autocomplete="off">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Select Partner<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="form-control" name="creditor" id="creditor">
										<option value="">Select Partner</option>
										<?php foreach ($datalist->creditors as $creditor) { ?>
											<option value="<?php echo $creditor->creditor_id; ?>"><?php echo $creditor->creaditor_name; ?></option>
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
										<?php foreach ($datalist->features as $features) { ?>
											<option value="<?php echo $features->id; ?>"><?php echo $features->name; ?></option>
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
									<input type="text" placeholder="Enter title name" class="form-control" name="title" autocomplete="off">
									
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Short Description</label>
								<div class="input-group">
									<input type="text" placeholder="Enter Short Description" class="form-control" name="short_description" autocomplete="off">

									
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="is_active" class="col-form-label">Status</label>
								<div class="input-group">
									<select class="form-control" name="is_active" id="is_active" style="height: calc(2.7rem + 0px);">
										<option value="">Select</option>
										<option value="1" <?php if(!empty($user_details['is_active']) && $user_details['is_active'] == 1){?> selected <?php }?>>Active</option>
										<option value="0" <?php if(!empty($user_details['is_active']) && $user_details['is_active'] == 0){?> selected <?php }?>>In-Active</option>
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
								<textarea class="form-control editor" name="long_description" id="content"></textarea>
							</div>
						</div>
						<div class="row col-md-6 mt-4" id="addplanbtn">
							<label for="validationCustomUsername" class="col-form-label">File<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input id="form_file" name="form_file" type="file" class="form-control" placeholder="" aria-describedby="inputGroupPrepend" />

								<input id="input_file" value="<?php if(!empty($getDetails[0]['form_file'])){echo $getDetails[0]['form_file'];}?>" name="input_file" type="hidden" >			

								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">cloud_upload</span></span>
								</div>
							</div>
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
			
			<!-- end 1 -->
			</div>
		</div>
	</div>

	

</div>


</div>





<script>
	

	$(document).on("change","#creditor",function(){
		var creditor_id = $(this).val();
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
</script>

<script type="text/javascript">
jQuery.validator.addMethod("lettersonlys", function(value, element) {
	return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Letters only please");
$( document ).ready(function() {
});

var vRules = {
	creditor:{required:true},
	plan_name:{required:true},
	feature:{required:true},
	//title:{required:true},
	//short_description:{required:true},
	is_active:{required:true},
	//long_description:{required:true},
	form_file:{required:true}
};
var vMessages = {
	creditor:{required:"Please select Partner."},
	plan_name:{required:"Please select plan."},
	feature:{required:"Please select feature."},
//	title:{required:"Please enter title."},
	//short_description:{required:"Please enter short description."},
	is_active:{required:"Please select status."},
	//long_description:{required:"Please enter long description."},
	form_file:{required:"Please select image."},
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

document.title = "Add/Edit Enrollment Form";
</script>