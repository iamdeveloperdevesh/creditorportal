<?php //echo "<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div class="page-body">
	<div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-header-left">
                                <?php if(empty($getDetails[0]['suminsured_type_id'])){ ?>
                                <h3>Add Sum Insured Type</h3>
                                <?php } else { ?>
                                <h3>Update Sum Insured Type</h3>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ol class="breadcrumb pull-right">
                                <li class="breadcrumb-item"><a href="<?php echo base_url();?>suminsured/addedit"><i data-feather="home"></i></a></li>
                                <li class="breadcrumb-item active">
                                <?php if(empty($getDetails[0]['suminsured_type_id'])){ ?>
                                Add Sum Insured Type
                                <?php } else { ?>
                                Update Sum Insured Type
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
                    	<input type="hidden" id="suminsured_type_id" name="suminsured_type_id" value="<?php if(!empty($getDetails[0]['suminsured_type_id'])){echo $getDetails[0]['suminsured_type_id'];}?>" />
                        <div class="control-group form-group">
                            <label class="control-label" for="insurer_name">Name*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="suminsured_type" name="suminsured_type" type="text" value="<?php if(!empty($getDetails[0]['suminsured_type'])){echo $getDetails[0]['suminsured_type'];}?>">
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
	suminsured_type:{required:true, alphanumericwithspace:true},
	isactive:{required:true}
};

var vMessages = {
	suminsured_type:{required:"Please enter name."},
	isactive:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>suminsured/submitForm";
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
						window.location = "<?php echo base_url();?>suminsured";
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

document.title = "Add/Edit Sum Insured";
</script>


