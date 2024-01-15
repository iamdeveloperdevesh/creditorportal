<?php //echo "<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div class="page-body">
	<div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-header-left">
                                <h3>Add Insurer</h3>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ol class="breadcrumb pull-right">
                                <li class="breadcrumb-item"><a href="<?php echo base_url();?>insurer/addedit"><i data-feather="home"></i></a></li>
                                <li class="breadcrumb-item active">Add Insurer</li>
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
                    	<input type="hidden" id="creditor_id" name="creditor_id" value="<?php if(!empty($getDetails[0]['creditor_id'])){echo $getDetails[0]['creditor_id'];}?>" />
                        <div class="control-group form-group">
                            <label class="control-label" for="insurer_name">Insurer Name*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="insurer_name" name="insurer_name" type="text" value="<?php if(!empty($getDetails[0]['insurer_name'])){echo $getDetails[0]['insurer_name'];}?>">
                            </div>
                        </div>
                        
						<div class="control-group form-group">
                            <label class="control-label" for="insurer_code">Insurer Code*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="insurer_code" name="insurer_code" type="text" value="<?php if(!empty($getDetails[0]['insurer_code'])){echo $getDetails[0]['insurer_code'];}?>">
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
	creaditor_name:{required:true, alphanumericwithspace:true},
	creditor_code:{required:true},
	isactive:{required:true}
};

var vMessages = {
	creaditor_name:{required:"Please enter name."},
	creditor_code:{required:"Please enter code."},
	isactive:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>insurer/submitForm";
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
						window.location = "<?php echo base_url();?>insurer";
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

document.title = "Add/Edit Insurer";
</script>


