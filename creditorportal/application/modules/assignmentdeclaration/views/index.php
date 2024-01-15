<style>
	@media (max-width:768px){
        .partner{
            position: relative;
            right: 8px;
        }

        .plus-icon{
            position: absolute;
            right: 7px;
        }
    }

</style>
<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Assignment Declarations - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="plan_name" class="col-form-label">Plan Name</label>
						<div class="dataTables_filter input-group">
							<input id="plan_name" name="plan_name" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="creditor_name" class="col-form-label">Partner Name</label>
						<div class="dataTables_filter input-group">
							<input id="creditor_name" name="creditor_name" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="label" class="col-form-label">Label</label>
						<div class="dataTables_filter input-group">
							<input id="label" name="label" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-2 col-12 txt-lr">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>assignmentdeclaration"><button class="btn cnl-btn">Clear Search</button></a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-8 col-6">
						<p>Details</p>
					</div>
					<?php if(in_array('AssignmentDeclarationAdd',$this->RolePermission)){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>assignmentdeclaration/addEdit">
								<button class="btn btn-sec add-btn fl-right">
								<span class="partner">Add Declaration</span> 
									<span class="display-none-sm">
										<span class="material-icons spn-icon plus-icon">add_circle_outline</span>
									</span>
								</button>
							</a>
						</div>
					<?php }?>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th>Plan Name</th>
                                <th>Partner Name</th>
								<th>Label</th>
								<th>Status</th>
								<th class="text-center">Actions</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Center Section End-->		
<script type="text/javascript">
	function deleteData(id)
	{
		var r=confirm("Are you sure you want to delete this record?");
		if (r==true)
		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/assignmentdeclaration/delRecord/"+id,
				async: false,
				type: "POST",
				success: function(data2){
					data2 = $.trim(data2);
					if(data2 == "1")
					{
						displayMsg("success","Record has been Deleted!");
						setTimeout("location.reload(true);",1000);
						
					}
					else
					{
						displayMsg("error","Oops something went wrong!");
						setTimeout("location.reload(true);",1000);
					}
				}
			});
		}
	}
	document.title = "Assignment Declaration";
</script>