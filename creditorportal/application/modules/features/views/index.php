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
		.paging_full_numbers span .paginate_button:nth-child(4){
			position: absolute;
    		right: -27px;

		}

		.paging_full_numbers span .paginate_button:nth-child(5){
			position: absolute;
    		right: -67px;

		}

		/* .paging_full_numbers span .paginate_button:nth-child(7){
			position: absolute;
    		right: -122px;
		} */

		.paging_full_numbers .next{
			position: absolute;
    		right: -137px;
		}

		.paging_full_numbers .last{
			position: absolute;
    		right: -205px;
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
						<p>Features - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="firstname" class="col-form-label">Configure product features</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter Configure product features" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-2 col-12 txt-lr">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>products"><button class="btn cnl-btn">Clear Search</button></a>
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
					<?php  if(in_array('FeaturesAdd',$this->RolePermission)){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>features/AddNewView">
								<button class="btn btn-sec add-btn fl-right">
								<span class="partner">Add Feature </span> 
									<span class="display-none-sm">
										<span class="material-icons spn-icon plus-icon">add_circle_outline</span>
									</span>
								</button>
							</a>
						</div>
					<?php  }?>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th>Title</th>
								<th>Short Description</th>
								<th>Description</th>
								<th>Status</th>
								<th>Actions</th>
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
<script>
$( document ).ready(function() {
	
});

function deleteData(id)
{
	var r=confirm("Are you sure you want to delete this record?");
	if (r==true)
	{
		$.ajax({
			url: "<?php echo base_url().$this->router->fetch_module();?>/features/delRecord/"+id,
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
document.title = "Features List";
</script>