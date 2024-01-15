<?php //echo "<pre>";print_r($roles);exit;?>
<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Customer Leads</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>customerleads"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Customer Leads</li>
					</ol>
				</div>
			</div>
		</div>
	</div> 
    <div class="card">
    	<div class="page-title-border">
            <div class="col-sm-12">
			<?php if(in_array('LeadAdd',$this->RolePermission)){?>
				<p><a href="<?php echo base_url();?>customerleads/addEdit" class="btn btn-primary icon-btn pull-right"><i class="fa fa-plus"></i>Add Lead</a></p>
			<?php }?>
            <div class="clearfix"></div>
            </div>
        </div> <br>
		<div class="container">
			<div class="row">
				<div class="col-sm-2 col-xs-12" >
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="trace_id" class="control-label">Trace/Lead Id</label>
						<input id="trace_id" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="plan_name" class="control-label">Plan Name</label>
						<input id="plan_name" type="text" class="searchInput form-control"/>
					</div>   
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="creditor_name" class="control-label">Creditor Name</label>
						<input id="creditor_name" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="employee_full_name" class="control-label">SM</label>
						<input id="employee_full_name" type="text" class="searchInput form-control"/>
					</div>
					
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="full_name" class="control-label">Customer Name</label>
						<input id="full_name" type="text" class="searchInput form-control"/>
					</div>  
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="mobile_no" class="control-label">Mobile No</label>
						<input id="mobile_no" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="email_id" class="control-label">Email ID</label>
						<input id="email_id" type="text" class="searchInput form-control"/>
					</div>
				</div>
				
				
				<div class="control-group clearFilter form-group" style="margin-left:5px;">
					<div class="controls">
						<a href="<?php echo base_url();?>customerleads">
							<button class="btn btn-primary" style="margin:10px 10px 10px 10px;">Clear Search</button>
						</a>
					</div>
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
                                <th>Trace/Lead Id</th>
                                <th>Plan Name</th>
                                <th>Creditor Name</th>
								<th>SM</th>
                                <th>Customer Name</th>
                                <th>Mobile</th>
                                <th>Eamil</th>
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
        <div class="clearfix"></div>
    </div>
</div><!-- end: Content -->
			
<script>
	function deleteData(id)
	{
		var r=confirm("Are you sure you want to delete this record?");
		if (r==true)
		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/users/delRecord/"+id,
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
	document.title = "Customer Leads";
</script>