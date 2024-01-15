<?php //echo "<pre>";print_r($getDetails);exit;?>
<!-- start: Content -->
<div class="page-body">
	<div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-header-left">
                                <h3>Add Partner</h3>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ol class="breadcrumb pull-right">
                                <li class="breadcrumb-item"><a href="<?php echo base_url();?>creditors/addedit"><i data-feather="home"></i></a></li>
                                <li class="breadcrumb-item active">Add Partner</li>
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
                            <label class="control-label" for="creaditor_name">Partner Name*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="creaditor_name" name="creaditor_name" type="text" value="<?php if(!empty($getDetails[0]['creaditor_name'])){echo $getDetails[0]['creaditor_name'];}?>">
                            </div>
                        </div>
                        
						<div class="control-group form-group">
                            <label class="control-label" for="creditor_code">Partner Code*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="creditor_code" name="creditor_code" type="text" value="<?php if(!empty($getDetails[0]['creditor_code'])){echo $getDetails[0]['creditor_code'];}?>">
                            </div>
                        </div>
						
						<div class="control-group form-group">
                            <label class="control-label" for="ceditor_email">Partner Email*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="ceditor_email" name="ceditor_email" type="text" value="<?php if(!empty($getDetails[0]['ceditor_email'])){echo $getDetails[0]['ceditor_email'];}?>">
                            </div>
                        </div>
						
						<div class="control-group form-group">
                            <label class="control-label" for="creditor_mobile">Partner Mobile*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="creditor_mobile" name="creditor_mobile" type="text" value="<?php if(!empty($getDetails[0]['creditor_mobile'])){echo $getDetails[0]['creditor_mobile'];}?>">
                            </div>
                        </div>
						
						<div class="control-group form-group">
                            <label class="control-label" for="creditor_phone">Partner Phone*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="creditor_phone" name="creditor_phone" type="text" value="<?php if(!empty($getDetails[0]['creditor_phone'])){echo $getDetails[0]['creditor_phone'];}?>">
                            </div>
                        </div>
						
						<div class="control-group form-group">
                            <label class="control-label" for="creditor_pancard">PAN*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="creditor_pancard" name="creditor_pancard" type="text" value="<?php if(!empty($getDetails[0]['creditor_pancard'])){echo $getDetails[0]['creditor_pancard'];}?>">
                            </div>
                        </div>
						
						<div class="control-group form-group">
                            <label class="control-label" for="creditor_gstn">GST Number*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="creditor_gstn" name="creditor_gstn" type="text" value="<?php if(!empty($getDetails[0]['creditor_gstn'])){echo $getDetails[0]['creditor_gstn'];}?>">
                            </div>
                        </div>
						
						<div class="control-group form-group">
                            <label class="control-label" for="address">Address*</label>
                            <div class="controls">
								<textarea class="input-xlarge form-control" name="address" id="address"><?php if(!empty($getDetails[0]['address'])){echo $getDetails[0]['address'];}?></textarea>
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
							<a href="<?php echo base_url();?>creditors" class="btn btn-primary">Cancel</a>
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
	ceditor_email:{required:true, email:true},
	creditor_mobile:{required:true, digits:true},
	creditor_phone:{required:true, digits:true},
	creditor_pancard:{required:true},
	creditor_gstn:{required:true},
	address:{required:true},
	isactive:{required:true}
};

var vMessages = {
	creaditor_name:{required:"Please enter name."},
	creditor_code:{required:"Please enter code."},
	ceditor_email:{required:"Please enter email id.", email:"Please enter valid email id."},
	creditor_mobile:{required:"Please enter mobile number."},
	creditor_phone:{required:"Please enter phone number."},
	creditor_pancard:{required:"please enter pancard number."},
	creditor_gstn:{required:"Please enter GST number."},
	address:{required:"Please enter address."},
	isactive:{required:"Please select status."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>creditors/submitForm";
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
						window.location = "<?php echo base_url();?>creditors";
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

document.title = "Add/Edit Creditor";
</script>


