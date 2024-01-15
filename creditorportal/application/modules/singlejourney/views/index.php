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

	@media(max-width:425px){
		.paging_full_numbers .last{
			position: absolute;
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
						<p>Single Journey - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Partner Name</label>
						<div class="dataTables_filter input-group"style="float: none;">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter Partner Name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Insurance Type</label>
                        <div class="dataTables_filter input-group"style="float: none;">
                            <select id="sSearch_1" name="sSearch_1"  class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
                            <option value="1">Group Policy</option>
                            <option value="2">General Insurance</option>
                            </select>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
					
					<div class="col-md-2 col-12 text-center">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>singlejourney"><button class="btn cnl-btn fl-right">Clear Search</button></a>
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
					<?php if(in_array('CompanyAdd',$this->RolePermission)){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>singlejourney/addEdit">
								<button class="btn btn-sec add-btn fl-right"><span class="partner">Add URL </span>
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
						<thead>
							<tr  class="tbl-cre">
								<th>Partner Name</th>
								<th>URL</th>
								<!--<th>URL(Gadget insurance)</th>-->
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
$( document ).ready(function() {
	
});
document.title = "Single Journey";
</script>