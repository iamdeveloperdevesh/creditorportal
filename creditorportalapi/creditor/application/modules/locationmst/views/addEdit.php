<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Add Locations</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>locationmst"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Add Location</li>
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
						<input type="hidden" id="city_id" name="city_id" value="<?php if(!empty($getDetails[0]['city_id'])){echo $getDetails[0]['city_id'];}?>" />
						
						<div class="control-group">
							<label class="control-label" for="branch_name">State*</label>
							<div class="controls">
								<select class="select2 form-control col-xl-8 col-md-7" name="city_state_id" id="city_state_id">
									<option value="">Select</option>
									<?php 
									if(!empty($states)){
										for($i=0; $i < sizeof($states); $i++){
									?>
										<option value="<?php echo $states[$i]['state_id']; ?>" <?php if(!empty($getDetails[0]['city_state_id']) && $getDetails[0]['city_state_id'] == $states[$i]['state_id']){?> selected <?php }?>><?php echo $states[$i]['state_name']; ?></option>
									<?php 
										}
									}
									?>
								</select>
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label" for="city_name">City*</label>
							<div class="controls">
								<input class="input-xlarge form-control" id="city_name" name="city_name" type="text" value="<?php if(!empty($getDetails[0]['city_name'])){echo $getDetails[0]['city_name'];}?>">
							</div>
						</div>
						
						<div class="clearfix" style="height: 10px; width: 100%; float: left; display: inline;">&nbsp;</div>
						
						<div class="form-actions">
							<button type="submit" class="btn btn-primary">Submit</button>
							<a href="<?php echo base_url();?>locationmst"><button class="btn" type="button">Cancel</button></a>
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
	city_state_id:{required:true},
	city_name:{required:true}
	
};
var vMessages = {
	city_state_id:{required:"Please state."},
	city_name:{required:"Please enter citye name."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>locationmst/submitForm";
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
						window.location = "<?php echo base_url();?>locationmst";
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

document.title = "Add/Edit Locations";

</script>