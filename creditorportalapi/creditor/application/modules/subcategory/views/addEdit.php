<!-- start: Content -->
<div class="page-body">
	<div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-header-left">
                                <h3>Add Sub-Category
                                    <small>Chheda Admin panel</small>
                                </h3>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ol class="breadcrumb pull-right">
                                <li class="breadcrumb-item"><a href="<?php echo base_url();?>categories/addedit"><i data-feather="home"></i></a></li>
                                <li class="breadcrumb-item active">Add Sub-Category</li>
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
                    	<input type="hidden" id="sub_category_id" name="sub_category_id" value="<?php if(!empty($subcategory_details[0]->subcategory_id)){echo $subcategory_details[0]->subcategory_id;}?>" />
                        <div class="control-group form-group">
							<label class="control-label" for="category_id">Category</label>
								<div class="controls">
									<select id="category_id	" name="category_id" class=" searchInput form-control" style="width:160px;" tabindex="46">
										<option value="">Select Category</option>
										<?php 
										if(!empty($categories)){
											$sel="";
											foreach ($categories as $key => $value) {
												$sel = ($value->category_id == $subcategory_details[0]->category_id)?"selected":"";?>
												<option value="<?= $value->category_id?>"<?=$sel;?> ><?= $value->category_name?></option>
											<?php }
										} ?>
									</select>
							</div>
                        </div>
						
				
						<div class="control-group form-group">
                            <label class="control-label" for="category_name">Sub-Category Name*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="subcategory_name" name="subcategory_name" type="text" value="<?php if(!empty($subcategory_details[0]->subcategory_name)){echo $subcategory_details[0]->subcategory_name;}?>">
                            </div>
                        </div>

						<div class="form-actions form-group">
							<button type="submit" class="btn btn-primary">Submit</button>
							<a href="<?php echo base_url();?>categories" class="btn btn-primary">Cancel</a>
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
	category_id:{required:true},
	subcategory_name:{required:true, alphanumericwithspace:true}
};
var vMessages = {
	category_id:{required:"Please select category."},
	subcategory_name:{required:"Please enter subcategory."}
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>subcategory/submitForm";
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
						window.location = "<?php echo base_url();?>subcategory";
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

document.title = "Add/Edit Sub Categories";
</script>