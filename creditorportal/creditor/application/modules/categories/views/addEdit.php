<!-- start: Content -->
<div class="page-body">
	<div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-header-left">
                                <h3>Add Category
                                    <small>Chheda Admin panel</small>
                                </h3>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ol class="breadcrumb pull-right">
                                <li class="breadcrumb-item"><a href="<?php echo base_url();?>categories/addedit"><i data-feather="home"></i></a></li>
                                <li class="breadcrumb-item active">Add Category</li>
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
                    	<input type="hidden" id="category_id" name="category_id" value="<?php if(!empty($categories_details[0]->category_id)){echo $categories_details[0]->category_id;}?>" />
                        <div class="control-group form-group">
                            <label class="control-label" for="category_name">Category Name*</label>
                            <div class="controls">
                                <input class="input-xlarge form-control" id="category_name" name="category_name" type="text" value="<?php if(!empty($categories_details[0]->category_name)){echo $categories_details[0]->category_name;}?>">
                            </div>
                        </div>
                        
						<div class="control-group form-group">
							<label class="control-label" for="category_image">Category Image*</label>
							<div class="controls">
								
								<input class="input-xlarge" id="input_category_image" value="<?php if(!empty($categories_details[0]->category_image)){echo $categories_details[0]->category_image;}?>" name="input_category_image" type="hidden" >
								
								<input class="input-xlarge" id="category_image" name="category_image" type="file">
								<?php if(!empty($categories_details[0]->category_image) && file_exists(DOC_ROOT_FRONT."/images/category_images/".$categories_details[0]->category_image))
								{
								?>
									<img style="width: 150px;height:150px;padding-top:5px;" src="<?php echo FRONT_URL; ?>/images/category_images/<?php echo $categories_details[0]->category_image; ?>">
									<!--<i onclick="delete_img();" title="Delete Img" class="fa fa-times-circle-o" style="position: absolute;cursor:pointer;"></i> --></img>
									
									<div style="margin-top:10px;">
										<button class="btn-default deleteImage" type="button" alt="<?php echo $categories_details[0]->category_name;?>" category_id="<?php echo $categories_details[0]->category_id; ?>" category_image = "<?php echo $categories_details[0]->category_image; ?>" >Delete Image</button>
									</div>
								<?php 
								}
								?>
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
	category_name:{required:true, alphanumericwithspace:true},
	/* <?php if(empty($categories_details[0]->category_image))
	{
	?>
	category_image:{required:true}	
	<?php 
	}
	?>	 */
};

var vMessages = {
	category_name:{required:"Please enter name."},
	// <?php if(empty($categories_details[0]->category_image))
	// {
	// ?>
	// category_image:{required:"Please select category image."}	
	// <?php 
	// }
	// ?>	
};

$("#form-validate").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>categories/submitForm";
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
						window.location = "<?php echo base_url();?>categories";
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

$(".deleteImage").click(function()
{
	if(confirm("Are you sure you want to delete image ?"))
	{
		var category_id = $(this).attr("category_id");
		var category_image = $(this).attr("category_image");
		if(category_id !== "")
		{
			$.ajax({
				url: '<?php echo base_url();?>categories/deleteImage',
				data:{"category_id":category_id},
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				success: function (response) 
				{
					if(response.success)
					{
						displayMsg("success", response.msg);
						setTimeout(function(){
						location.reload();
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
	}
});

document.title = "Add/Edit Categories";
</script>


