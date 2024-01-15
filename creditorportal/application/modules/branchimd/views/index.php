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
						<p>Branch IMD Mapping - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="policy_number" class="col-form-label">Master Policy No.</label>
						<div class="dataTables_filter input-group">
							<input id="policy_number" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
                    <div class="col-md-3 mb-3">
						<label for="branch_code" class="col-form-label">Branch Code</label>
						<div class="dataTables_filter input-group">
							<input id="branch_code" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
                    <div class="col-md-3 mb-3">
						<label for="imd_code" class="col-form-label">IMD Code</label>
						<div class="dataTables_filter input-group">
							<input id="imd_code" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<?php /*<div class="col-md-3 mb-3">
						<label for="created_date" class="col-form-label">Created Date</label>
						<div class="dataTables_filter input-group">
							<input id="created_date" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div> */ ?>
					<div class="col-md-3 mb-3">
						<label for="status" class="col-form-label">Status</label>
						<div class="dataTables_filter input-group">
							<select class="searchInput form-control" name="isactive" id="isactive"> 
								<option value="" selected>Select Status</option>
								<option value="1">Active</option>
								<option value="0">In-Active</option>							
							</select>
						</div>
					</div>
					<div class="col-md-2 col-12 text-lr">
						<!-- <label style="visibility: hidden;" class="mt-1">For Space</label> -->
						<a href="<?php echo base_url();?>branchimd"><button class="btn cnl-btn">Clear Search</button></a>
					</div>
					<?php /*<div class="col-md-2 col-12 text-center">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>users/importUser"><button class="btn cnl-btn fl-right">Import Mapping Codes</button></a>
					</div>*/ ?>

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
					<?php if(in_array('BranchIMDAdd',$this->RolePermission)){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>branchimd/addbranchimd">
								<button class="btn btn-sec add-btn fl-right">
								<span class="partner">Add Mapping</span>
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
				<div class="col-md-12 table-responsive scroll-table" style="height:500px;">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th>Master Policy No.</th>
                                <th>Branch Code</th>
                                <th>IMD Code</th>
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
				url: "<?php echo base_url(); ?>branchimd/delRecord/"+id,
				async: false,
				type: "POST",
				success: function(data2){
					data2 = $.trim(data2);
					if(data2 == "1")
					{
						displayMsg("success","Record deactivated successfully!");
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
	document.title = "Branch IMD Mapping";
</script>