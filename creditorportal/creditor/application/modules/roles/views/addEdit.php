<?php //echo "<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div id="content" class="page-body">

    <!-- <div class="page-title">
		<div>
			<h1><i class=" "></i> Add Roles</h1>            
		</div>
		<div>
			<ul class="breadcrumb">
				<li><a href="<?php echo base_url();?>home"><i class="fa fa-home fa-lg"></i></a></li>
				<li><a href="<?php echo base_url();?>roles">Roles</a></li>
				<li><a href="#">Add Roles</a></li>
			</ul>
		</div>
    </div>                 -->
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Add Roles</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>roles"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Add Roles</li>
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
						<div class="col-sm-8 col-md-4">
							<input type="hidden" id="role_id" name="role_id" value="<?php if(!empty($getDetails[0]['role_id'])){echo $getDetails[0]['role_id'];}?>" />
							<div class="control-group form-group">
								<label class="control-label" for="role_name">Role Name*</label>
								<div class="controls">
									<input class="input-xlarge form-control" id="role_name" name="role_name" type="text" value="<?php if(!empty($getDetails[0]['role_name'])){echo $getDetails[0]['role_name'];}?>">
								</div>
							</div>
						</div> 
						<div class="col-sm-4 col-md-8"></div>
						<div class="col-sm-12">     
							<div class="control-group">
								<div class="chk">
									<fieldset id="fieldUserRoles">
										<div class="page-header">
											<h4>Role Permissions</h4>
										</div>
										<?php
										if(!empty($permissions) && isset($permissions))
										{
											for($i=0;$i<sizeof($permissions);$i++)
											{	
											?>
											<span style="float:left;width:227px;" class="width33">
												<input <?php if(!empty($selpermissions) && in_array($permissions[$i]['perm_id'],$selpermissions)){ echo "checked=checked";} ?> onclick="myFunction(<?php echo $permissions[$i]['perm_id']; ?>)" class="chk permCheck" type="checkbox"  id="<?php echo $permissions[$i]['perm_id']; ?>" name="perm_id[]"  value="<?php echo $permissions[$i]['perm_id']; ?>"/><?php echo $permissions[$i]['perm_desc']; ?>
											</span>
											<?php 
											}
										}?>
										<div class="clearfix" style="height: 10px; width: 100%; float: left; display: inline;">&nbsp;</div>
										<button id="chkAll" style="float:left;" class="btn btn-info marginR10" type="button" onclick="checkUncheck(this)">Check All</button>
									</fieldset>
								</div>
							</div>
							<div class="clearfix" style="height: 10px; width: 100%; float: left; display: inline;">&nbsp;</div>
							<div class="form-actions">
								<button type="submit" class="btn btn-primary">Submit</button>
								<a href="<?php echo base_url();?>roles"><button class="btn" type="button">Cancel</button></a>
							</div>
						</div>
					</fieldset>
				</form>
				<div class="clearfix"></div>
			</div>
		</div><!--/span-->
	</div><!--/row-->
</div><!-- end: Content -->
			
<script>

function myFunction(id){
	// alert(id);
	$("#uniform-"+id).removeClass("checker").addClass("chk");	

	}
$( document ).ready(function() {
	//alert('here');
	//$("#uniform-7").removeClass("checker").addClass("chk");	
});

var vRules = {
	role_name:{required:true}
	
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
    if($(obj).text() == "Check All")
    {
		$(".width33").each(function(){
			$(this).find("span").addClass("checked");
			$(this).find("input").attr("checked","checked");
		});
		$(".both").each(function(){
			$(this).find("span").addClass("checked");
			$(this).find("input").attr("checked","checked");
		});
        $(obj).text("Uncheck All");
    }
    else
    {
    	$(".width33").each(function(){
			$(this).find("span").removeClass("checked");
			$(this).find("input").removeAttr("checked");
		});
		$(".both").each(function(){
			$(this).find("span").removeClass("checked");
			$(this).find("input").removeAttr("checked");
		});
    	$(obj).text("Check All");
    }
}


document.title = "AddEdit Role";
</script>