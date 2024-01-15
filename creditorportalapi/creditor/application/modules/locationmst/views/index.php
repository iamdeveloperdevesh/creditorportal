<?php  
//session_start();
//print_r($_SESSION["webadmin"]);
?>
<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Cities</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>locationmst"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Cites</li>
					</ol>
				</div>
			</div>
		</div>
	</div>         
    <div class="card"><br>
	<div class="page-title-border">
		<div class="col-sm-12">
		<?php if(in_array('LocationAdd',$this->RolePermission)){?>
			<p><a href="<?php echo base_url();?>locationmst/addEdit" class="btn btn-primary icon-btn pull-right"><i class="fa fa-plus"></i>Add City</a></p>
		<?php }?>
		<div class="clearfix"></div>
		</div>
	</div> <br>
		
	<div class="container">
			<div class="row">
				<div class="col-sm-2 col-xs-12" >
					<div class="dataTables_filter searchFilterClass form-group">
						<label for="sm_name" class="control-label">State Name</label>
						<input id="userid" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="col-sm-2 col-xs-12" >
					<div class="dataTables_filter searchFilterClass form-group">
						<label for="userid" class="control-label">City Name</label>
						<input id="userid" type="text" class="searchInput form-control"/>
					</div>
				</div>
				
				
				<div class="control-group clearFilter form-group" style="margin-left:5px;">
					<div class="controls">
						<a href="<?php echo base_url();?>locationmst">
							<button class="btn btn-primary" style="margin:10px 10px 10px 10px;">Clear Search</button>
						</a>
					</div>
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
								<th>State Name</th>
								<th>City Name</th>
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

	
	document.title = "Locations";
	</script>