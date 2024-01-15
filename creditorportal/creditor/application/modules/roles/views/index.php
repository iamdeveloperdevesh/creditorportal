<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Roles</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>roles"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Roles</li>
					</ol>
				</div>
			</div>
		</div>
	</div>         
    <div class="card"><br>
	<div class="container">
			<div class="row">
			<div class="col-sm-1 col-xs-12" >
				<div class="dataTables_filter searchFilterClass form-group">
					<label for="firstname" class="control-label">Name</label>
				</div>
				</div>
				<div class="col-sm-2 col-xs-12" >
					<div class="dataTables_filter searchFilterClass form-group">
						<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="col-sm-3 col-xs-12" >
				<div class="dataTables_filter searchFilterClass form-group">
						<div class="controls">
							<a href="<?php echo base_url();?>roles"><button class="btn btn-primary">Clear Search</button></a>
						</div>
				</div>
				</div>
				<div class="col-sm-6 col-xs-12" >
				<?php if(in_array('RoleAdd',$this->RolePermission)){?>
					<p align="right"><a href="<?php echo base_url();?>roles/addEdit" class="btn btn-primary icon-btn"><i class="fa fa-plus"></i>Add Role</a></p>
				<?php }?>
				<div class="clearfix"></div>
				</div>

			</div>
	</div>
	
        <div class="clearfix"></div>
		
		<div class="card-body">
			<div class="box-content">
				<div class="table-responsive scroll-table">
					<table class="dynamicTable display table table-bordered non-bootstrap">
						<thead>
							<tr>
								<th>Roles</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
						</tfoot>
					</table>  
				</div>
				<div class="clearfix"></div>           
			</div><!--/span-->
		</div><!--/row-->
	</div><!-- end:  -->
</div><!-- end: Content -->
			
	<script>
	$( document ).ready(function() {
		
	});

	function deleteData(id)
	{
    	var r=confirm("Are you sure you want to delete this record?");
    	if (r==true)
   		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/roles/delRecord/"+id,
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
	document.title = "Roles";
	
</script>