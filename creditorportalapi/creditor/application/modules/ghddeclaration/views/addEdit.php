<?php //echo "<pre>";print_r($salutation);exit;;?>
<div class="page-body">
	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>GHD Declaration</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>ghddeclaration/addEdit"><i data-feather="home"></i> </li>
						<li class="breadcrumb-item active">GHD Declaration</li></a>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<!-- Container-fluid Ends-->

	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="card tab2-card">
					<div class="card-header">
						<h5>GHD Declaration</h5>
					</div>
					<div class="card-body">
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade active show"  aria-labelledby="account-tab">
								<form  id="form-validate" method="post" action="#">
									<input type="hidden" id="declaration_id" name="declaration_id" value="<?php if(!empty($user_details['declaration_id'])){echo $user_details['declaration_id'];}?>" />
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Creditor</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="creditor_id" id="creditor_id" onchange="getPlansData(this.value);">
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
									</div>
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Plan</label>
										<select class="select2 form-control col-xl-8 col-md-7" name="plan_id" id="plan_id" >
											<option value="">Select</option>
										</select>
									</div>
									
									<div class="form-group row">
										<label for="label" class="col-xl-3 col-md-4"><span>*</span> Label</label>
										<input class="form-control col-xl-8 col-md-7" name="label" id="label" type="text" value="<?php if(!empty($user_details['label'])){ echo $user_details['label']; }?>" />
									</div>
									<div class="form-group row">
										<label for="loan_amt" class="col-xl-3 col-md-4"><span>*</span> Content</label>
										<textarea class="form-control col-xl-8 col-md-7 editor" name="content" id="content"><?php if(!empty($user_details['content'])){ echo $user_details['content']; }?></textarea>
									</div>
									
									<div class="form-group row">
										<label for="date_of_joining" class="col-xl-3 col-md-4"><span>*</span>Status</label>
										<select class="form-control col-xl-8 col-md-7" name="is_active" id="is_active" required="">
											<option value="">Select</option>
											<option value="1" <?php if(!empty($user_details['is_active']) && $user_details['is_active'] == 1){?> selected <?php }?>>Active</option>
											<option value="0" <?php if(!empty($user_details['is_active']) && $user_details['is_active'] == 0){?> selected <?php }?>>In-Active</option>
										</select>
									</div>
									
									<div class="pull-center" >
										<button type="submit" class="btn btn-primary">Save</button>
										<a href="<?php echo base_url();?>ghddeclaration" class="btn btn-primary">Cancel</a>
									</div>
								</form>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Container-fluid Ends-->

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
				url:"<?php echo base_url();?>ghddeclaration/getPlans",
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
    
	<?php if(!empty($user_details['declaration_id'])){ ?>
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
				url: "<?php echo base_url();?>ghddeclaration/submitForm", 
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
							window.location = "<?php echo base_url();?>ghddeclaration";
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
</body>
</html>


