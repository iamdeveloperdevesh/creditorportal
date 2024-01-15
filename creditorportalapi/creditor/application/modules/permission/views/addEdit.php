<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Add Permissions</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>permission"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Add Permissions</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
		
	<div class="card">
		<div class="card-body">
			<div class="box-content">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<fieldset>
						<input type="hidden" id="perm_id" name="perm_id" value="<?php if(!empty($getDetails[0]['perm_id'])){echo $getDetails[0]['perm_id'];}?>" />
						
						<div class="control-group">
							<label class="control-label" for="perm_desc">Permission*</label>
							<div class="controls">
								<input class="input-xlarge form-control" id="perm_desc" name="perm_desc" type="text" value="<?php if(!empty($getDetails[0]['perm_desc'])){echo $getDetails[0]['perm_desc'];}?>">
							</div>
						</div>
						
						<div class="clearfix" style="height: 10px; width: 100%; float: left; display: inline;">&nbsp;</div>
						
						<div class="form-actions">
							<button type="submit" class="btn btn-primary">Submit</button>
							<a href="<?php echo base_url();?>permission"><button class="btn" type="button">Cancel</button></a>
						</div>
					</fieldset>
				</form>
			</div>
		</div><!--/span-->
	</div><!--/row-->
</div><!-- end: Content -->
			
<script>

$( document ).ready(function() {
});

var vRules = {
	perm_desc:{required:true}
	
};
var vMessages = {
	perm_desc:{required:"Please enter permission."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>permission/submitForm";
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
						window.location = "<?php echo base_url();?>permission";
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

document.title = "Add/Edit Permissions";

</script>