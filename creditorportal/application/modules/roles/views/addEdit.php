<?php //echo "<pre>";print_r($getDetails);exit;?>
<style>
#chkbtn{
	display: block !important;
}
</style>
<!-- start: Content -->
<div class="col-md-10">
<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
<input type="hidden" id="role_id" name="role_id" value="<?php if(!empty($getDetails[0]['role_id'])){echo $getDetails[0]['role_id'];}?>" />	
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Add Roles - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Role Name<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input id="role_name" name="role_name" type="text" class="form-control" placeholder="Enter role name" aria-describedby="inputGroupPrepend" value="<?php if(!empty($getDetails[0]['role_name'])){echo $getDetails[0]['role_name'];}?>" />
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Role Permission  - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 mb-3">
					  <button type="button" id="chkbtn" class="btn del-btn" onclick="checkUncheck(this)" />Check All </button></div>
					<?php
						if(!empty($permissions) && isset($permissions)){
							for($i=0;$i<sizeof($permissions);$i++){	
					?>
						<!--Repeat checkbox-->
						<div class="col-md-3 mb-1 mt-1">
							<div class="custom-control custom-checkbox">
								<span style="float:left;width:227px;" class="width33">
									<input <?php if(!empty($selpermissions) && in_array($permissions[$i]['perm_id'],$selpermissions)){ echo "checked=checked";} ?> onclick="myFunction(<?php echo $permissions[$i]['perm_id']; ?>)" class="custom-control-input chk permCheck" type="checkbox"  id="<?php echo $permissions[$i]['perm_id']; ?>" name="perm_id[]"  value="<?php echo $permissions[$i]['perm_id']; ?>">
									<label class="custom-control-label" for="<?php echo $permissions[$i]['perm_id']; ?>"><?php echo $permissions[$i]['perm_desc']; ?></label>
								</span>	
							</div>
						</div>
						<!--Repeat checkbox end-->
						<?php }}?>	
					
				</div>
				<div class="row mt-4">
					<div class="col-md-1 col-6 text-left"><button type="submit" class="btn smt-btn btn-primary">Save</button></div>
					<div class="col-md-2 col-6 text-right"><a href="<?php echo base_url();?>roles"><button type="button" class="btn cnl-btn">Cancel</button></a></div>
				</div>
			</div>
		</div>
	</div>
</form>	
</div>
<!-- end: Content -->
<script type="text/javascript">

jQuery.validator.addMethod("lettersonlys", function(value, element) {
    return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Letters only please");

function myFunction(id){
	// alert(id);
	$("#uniform-"+id).removeClass("checker").addClass("chk");	

}
$( document ).ready(function() {
		
});


var vRules = {
	role_name:{required:true, lettersonlys:true}	
};
var vMessages = {
	role_name:{required:"Please enter role name."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>roles/submitForm";
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
						window.location = "<?php echo base_url();?>roles";
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


function checkUncheck(obj)
{
	//alert($(obj).text());	
    if($(obj).text() == "Check All")
    {
		$(".width33").each(function(){
			$(this).find("span").addClass("checked");
			$(this).find("input").prop( 'checked', true );
		});
		/*$(".both").each(function(){
			$(this).find("span").addClass("checked");
			$(this).find("input").attr("checked","checked");
		});*/
        $(obj).text("Uncheck All");
    }
    else
    {
    	$(".width33").each(function(){
			$(this).find("span").removeClass("checked");
			$(this).find("input").prop("checked", false);
		});
		/*$(".both").each(function(){
			$(this).find("span").removeClass("checked");
			$(this).find("input").removeAttr("checked");
		});*/
    	$(obj).text("Check All");
    }
}

document.title = "AddEdit Role";
</script>