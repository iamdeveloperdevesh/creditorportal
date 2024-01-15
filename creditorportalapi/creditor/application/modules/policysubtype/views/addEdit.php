<?php //echo "<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div class="page-body">
	<div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-header-left">
                                <?php if(empty($getDetails[0]['policy_sub_type_id'])){ ?>
                                <h3>Add Policy Sub Type</h3>
                                <?php } else { ?>
                                <h3>Update Policy Sub Type</h3>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ol class="breadcrumb pull-right">
                                <li class="breadcrumb-item"><a href="<?php echo base_url();?>policysubtype/addedit"><i data-feather="home"></i></a></li>
                                <li class="breadcrumb-item active">
                                <?php if(empty($getDetails[0]['policy_sub_type_id'])){ ?>
                                Add Policy Sub Type
                                <?php } else { ?>
                                Update Policy Sub Type
                                <?php } ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>  
    <div class="card">       
        <div class="card-body">             
            <div class="box-content">
            	<div class="col-sm-8 col-md-4">
               		<form class="form-horizontal" id="form-validate" method="post" enctype="multipart/form-data">
                    	<input type="hidden" id="policy_sub_type_id" name="policy_sub_type_id" value="<?php if(!empty($getDetails[0]['policy_sub_type_id'])){echo $getDetails[0]['policy_sub_type_id'];}?>" />
                        <div class="control-group form-group">
                            <label class="control-label" for="insurer_name">Name*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="policy_sub_type_name" name="policy_sub_type_name" type="text" value="<?php if(!empty($getDetails[0]['policy_sub_type_name'])){echo $getDetails[0]['policy_sub_type_name'];}?>">
                            </div>
                        </div>
                        
						<div class="control-group form-group">
                            <label class="control-label" for="insurer_code">Policy Type*</label>
                            <div class="controls">
                                <select class="input-xlarge form-control" name="policy_type_id" id="policy_type_id">
									<option value="">Select</option>
									<?php foreach($policytypes as $type){ ?>
									<option value="<?php echo $type['policy_type_id']; ?>" <?php if(!empty($getDetails[0]['policy_type_id']) && $getDetails[0]['policy_type_id'] == $type['policy_type_id']){ echo "selected"; }?>><?php echo $type['policy_type_name']; ?></option>
									<?php } ?>
								</select>
                            </div>
                        </div>
						<div class="control-group form-group">
                            <label class="control-label" for="isactive">Status*</label>
                            <div class="controls">
                                <select class="input-xlarge form-control" name="isactive" id="isactive">
									<option value="">Select</option>
									<option value="1" <?php if(!empty($getDetails[0]['isactive']) && $getDetails[0]['isactive'] == 1){?> selected <?php }?>>Active</option>
									<option value="0" <?php if(!empty($getDetails[0]['isactive']) && $getDetails[0]['isactive'] == 0){?> selected <?php }?>>In-Active</option>
								</select>
                            </div>
                        </div>
                        
						<div class="form-actions form-group">
							<button type="submit" class="btn btn-primary">Submit</button>
							<a href="<?php echo base_url();?>insurer" class="btn btn-primary">Cancel</a>
						</div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>              
    </div>
</div>
<!-- end: Content -->			
<script>


var vRules = {
	policy_sub_type_name:{required:true, alphanumericwithspace:true},
	policy_type_id:{required:true},
	isactive:{required:true}
};

var vMessages = {
	policy_sub_type_name:{required:"Please enter name."},
	policy_type_id:{required:"Please select type."},
	isactive:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>policysubtype/submitForm";
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
						window.location = "<?php echo base_url();?>policysubtype";
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

document.title = "Add/Edit Policy Sub Type";
</script>


