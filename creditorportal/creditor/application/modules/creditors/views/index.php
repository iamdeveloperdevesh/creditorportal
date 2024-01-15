<div class="page-body">

	<!-- Container-fluid starts-->
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Partners</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>home"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Partners</li>
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
				<?php if(in_array('CreditorAdd',$this->RolePermission)){?>
					<p><a href="<?php echo base_url();?>creditors/addEdit" class="btn btn-primary icon-btn"><i class="fa fa-plus"></i>Add Partner</a></p>
				<?php }?>	
					<!-- <a href="create-user.html" class="btn btn-secondary">Create User</a> -->
				</div>
				<div class="row">
					<div class="col-sm-2 col-xs-12">
						<div class="dataTables_filter searchFilterClass form-group">
							<label for="firstname" class="control-label">Partner Name</label>
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" tabindex="46">
						</div>
					</div>
				
					<div class="control-group clearFilter">
						<div class="controls">
							<a href="creditors" tabindex="50"><button class="btn" style="margin:32px 10px 10px 10px;" tabindex="51">Clear Search</button></a>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="card-body">
					<div class="box-content">
						<div class="table-responsive scroll-table">
							<table cellpadding="0" cellspacing="0" border="0"
								class="responsive dynamicTable display table table-bordered" width="100%">
								<thead>
									<tr>
										<th>Partner Name</th>
										<th>Partner Code</th>
										<th>Email</th>
										<th>Mobile</th>
										<th>Phone</th>
										<th>Pancard</th>
										<th>GST No.</th>
										<th>Status</th>
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
$( document ).ready(function() {
	
});

function deleteData(id)
{
	var r=confirm("Are you sure you want to delete this record?");
	if (r==true)
	{
		$.ajax({
			url: "<?php echo base_url().$this->router->fetch_module();?>/creditors/delRecord/"+id,
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
document.title = "Creditors";
</script>