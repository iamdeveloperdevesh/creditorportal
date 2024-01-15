<?php //echo "<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div class="page-body">
	<div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-header-left">
                                <?php if(empty($getDetails[0]['id'])){ ?>
                                <h3>Add Family Member</h3>
                                <?php } else { ?>
                                <h3>Update Family Member</h3>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ol class="breadcrumb pull-right">
                                <li class="breadcrumb-item"><a href="<?php echo base_url();?>familyconstruct/addedit"><i data-feather="home"></i></a></li>
                                <li class="breadcrumb-item active">
                                <?php if(empty($getDetails[0]['policy_sub_type_id'])){ ?>
                                Add Family Member
                                <?php } else { ?>
                                Update Family Member
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
                    	<input type="hidden" id="policy_sub_type_id" name="policy_sub_type_id" value="<?php if(!empty($getDetails[0]['id'])){echo $getDetails[0]['id'];}?>" />
                        <div class="control-group form-group">
                            <label class="control-label" for="insurer_name">Name*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="member_type" name="member_type" type="text" value="<?php if(!empty($getDetails[0]['member_type'])){echo $getDetails[0]['member_type'];}?>">
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
	member_type:{required:true, alphanumericwithspace:true}
};

var vMessages = {
	member_type:{required:"Please enter name."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>familyconstruct/submitForm";
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
						window.location = "<?php echo base_url();?>familyconstruct";
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

document.title = "Add/Edit Family Construct";
</script>


