<!-- start: Content -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Location - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
					<input type="hidden" id="location_id" name="location_id" value="<?php if(!empty($getDetails[0]['location_id'])){echo $getDetails[0]['location_id'];}?>" />
					
					<div class="row">
						<div class="col-md-3 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Location<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input id="location_name" name="location_name" type="text" class="form-control" placeholder="Enter location" aria-describedby="inputGroupPrepend" value="<?php if(!empty($getDetails[0]['location_name'])){echo $getDetails[0]['location_name'];}?>" />
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">add_location</span></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
						<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>locationmst"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
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
	location_name:{required:true, lettersonlys:true}	
};
var vMessages = {
	location_name:{required:"Please enter location."}
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

document.title = "Add/Edit Location";

</script>