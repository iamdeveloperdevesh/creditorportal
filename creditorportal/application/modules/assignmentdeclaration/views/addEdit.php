<style>
#cke_content {
	width:100% !important;
}
</style>
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-12 col-12">
						<p>Add Assignment Declaration - <i class="ti-user"></i></p>
					</div>
					<!-- <div class="col-md-2 col-2"></div> -->
				</div>
			</div>
			<div class="card-body">
			<form  id="form-validate" method="post" action="#">
			<input type="hidden" id="assignment_declaration_id" name="assignment_declaration_id" value="<?php if(!empty($user_details['assignment_declaration_id'])){echo $user_details['assignment_declaration_id'];}?>" />
				<div class="row">
					<div class="col-md-4 mb-3">
						<label for="creditor_id" class="col-form-label">Partner<span class="lbl-star">*</span></label>
						<div class="input-group">
							<select class="select2 form-control" name="creditor_id" id="creditor_id" onchange="getPlansData(this.value);" style="padding:7px;">
								<option value="">Select</option>
								<?php 
								if(!empty($creditors)){
									for($i=0; $i < sizeof($creditors); $i++){
								?>
									<option value="<?php echo $creditors[$i]['creditor_id']; ?>" <?php if(!empty($user_details['creditor_id']) && $user_details['creditor_id'] == $creditors[$i]['creditor_id']){?> selected <?php }?> ><?php echo $creditors[$i]['creaditor_name']; ?></option>
								<?php 
									}
								}
								?>
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
					 <div class="col-md-3 mb-3">
						<label for="plan_id" class="col-form-label">Plan<span class="lbl-star">*</span></label>
						<div class="input-group">
							<select class="select2 form-control" name="plan_id" id="plan_id" style="padding:7px;">
								<option value="">Select</option>
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
					 <div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Label<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control" placeholder="Enter ..." name="label" id="label" type="text" value="<?php if(!empty($user_details['label'])){ echo $user_details['label']; }?>" aria-describedby="inputGroupPrepend" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-2 mb-3">
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
					
					<div class="col-md-12 mb-3">
						<label for="content" class="col-form-label">Content<span class="lbl-star">*</span></label>
						<div class="input-group">
							<textarea class="form-control editor" name="content" id="content"><?php if(!empty($user_details['content'])){ echo $user_details['content']; }?></textarea>
						</div>
					</div>
					
				</div>
				<div class="row mt-2">
					<div class="col-md-1 col-6 text-left">
						<button type="submit" class="btn smt-btn btn-primary">Save</button>
					</div>
					<div class="col-md-2 col-6 text-right">
						<a href="<?php echo base_url();?>assignmentdeclaration"><button type="button" class="btn cnl-btn">Cancel</button></a>
					</div>
				</div>
			</form>	
			</div>
		</div>
	</div>
</div>
		  
<script type="text/javascript">

function coapplicant(val){
	if(val == "Y"){
		$("#coapplicant").show();
	}else{
		$("#coapplicant").hide();
	}
}

function getPlansData(creditor_id,plan_id = null)
	{
		//alert(creditor_id);return false;
		if(creditor_id != "" )
		{
			$.ajax({
				url:"<?php echo base_url();?>assignmentdeclaration/getPlans",
				data:{creditor_id:creditor_id, plan_id:plan_id},
				type:'post',
				dataType: 'json',
				success: function(res)
				{
					if(res['status']=="success")
					{
						if(res['option'] != "")
						{
							$("#plan_id").html("<option value=''>Select</option>"+res['option']);
							$("#plan_id").select2();
						}
						else
						{
							$("#plan_id").html("<option value=''>Select</option>");
							$("#plan_id").select2();
						}
					}
					else
					{	
						$("#plan_id").html("<option value=''>Select</option>");
						$("#plan_id").select2();
					}
				}
			});
		}
	}
	
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

	var vRules = {
		creditor_id:{required:true},
		plan_id:{required:true},
		label:{required:true},
		content:{required:true}
	};
	
	var vMessages = {
		creditor_id:{required:"Please select creditor."},
		plan_id:{required:"Please select plan."},
		label:{required:"Please enter label."},
		content:{required:"Please enter contents."}
	};

	$("#form-validate").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) 
		{
			$("#form-validate").ajaxSubmit({
				url: "<?php echo base_url();?>assignmentdeclaration/submitForm", 
				type: 'post',
				dataType: 'JSON',
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
						displayMsg("success", response.msg);
						setTimeout(function(){
							window.location = "<?php echo base_url();?>assignmentdeclaration";
						},2000);
					}
					else
					{	
						displayMsg("error", response.msg);
						$(".btn-primary").show();
						return false;
					}
				}
			});
		}
	});

	document.title = "Assignment Declaration";
	
</script>