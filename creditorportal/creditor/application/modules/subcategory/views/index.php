<div class="page-body">

	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Sub Categories
							<small>Chheda Admin panel</small>
						</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>home"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Categories</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<!-- Container-fluid Ends-->
	
	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="card">
			<div class="card-body">
				<div class="btn-popup pull-right">
					<p><a href="<?php echo base_url();?>subcategory/addEdit" class="btn btn-primary icon-btn"><i
								class="fa fa-plus"></i>Add Sub-Category</a></p>
					<!-- <a href="create-user.html" class="btn btn-secondary">Create User</a> -->
				</div>
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-sm-3 col-xs-12">
						<div class="dataTables_filter searchFilterClass form-group">
							<label class="control-label" for="category_id">Category</label>
							<div class="controls">
									<select id="sSearch_0" name="sSearch_0" class=" searchInput form-control" style="width:160px;" tabindex="46">
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
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="dataTables_filter searchFilterClass form-group">
							<label for="firstname" class="control-label">Sub-Category Name </label>
							<input id="sSearch_1" name="sSearch_1" type="text" class="searchInput form-control" tabindex="47">
						</div>
					</div>

					<div class="col-sm-3 col-xs-12">
						<div class="dataTables_filter searchFilterClass form-group">
							<label class="control-label" for="category_id">Status</label>
                            <div class="controls">
                                <select id="sSearch_2" name="sSearch_2" class=" searchInput form-control" style="width:160px;" tabindex="46">
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="In-active">In-active</option>
									</select>
							</div>
						</div>
					</div>
				
					<div class="control-group clearFilter">
						<div class="controls">
							<a href="subcategory" tabindex="50"><button class="btn" style="margin:32px 10px 10px 10px;" tabindex="51">Clear Search</button></a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="box-content">
						<div class="table-responsive scroll-table">
							<table cellpadding="0" cellspacing="0"class="responsive dynamicTable display table table-bordered" width="100%" >
								<thead>
									<tr>
										<th>Category Name</th>
										<th>Sub Category Name</th>
										<th>Status</th>
										<th data-bSortable="false">Change Status</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Container-fluid Ends-->

</div>
<script>
	document.title="Add Sub Category";

	function changestatus(id)
	{
    	var r=confirm("Are you sure you want to change status for this record?");
    	if (r==true)
   		{
			$.ajax({
				url: "<?php echo base_url();?>subcategory/changestatus/"+id,
				async: false,
				type: "POST",
				dataType: "json",
				success: function (response) 
				{
					if(response.success)
					{
						displayMsg("success","Record has been updated!");
					}
					else
					{	
						displayMsg("error","Oops something went wrong!");
					}
					setTimeout("location.reload(true);",1000);
				}
			});
    	}
    }
</script>