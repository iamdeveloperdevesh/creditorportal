<?php //echo "<pre>";print_r($roles);exit;?>
<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Proposals</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>boproposals"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Proposals</li>
					</ol>
				</div>
			</div>
		</div>
	</div> 
    <div class="card">
    	<div class="page-title-border">
            <div class="col-sm-12">
			<?php if(in_array('LeadAdd',$this->RolePermission)){?>
				<!--<p><a href="<?php echo base_url();?>customerleads/addEdit" class="btn btn-primary icon-btn pull-right"><i class="fa fa-plus"></i>Add Lead</a></p>-->
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
						<label for="sm_name" class="control-label">SM Name</label>
						<input id="sm_name" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="customer_name" class="control-label">Customer Name</label>
						<input id="customer_name" type="text" class="searchInput form-control"/>
					</div>
				</div>
				
				<div class="control-group clearFilter form-group" style="margin-left:5px;">
					<div class="controls">
						<a href="<?php echo base_url();?>boproposals">
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
                                <th>SM Name</th>
								<th>Customer Name</th>
								<th>Status</th>
								<th>Updated On</th>
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
	function rejectProposal(id)
	{
		var r=confirm("Are you sure you want to reject this proposal?");
		if (r==true)
		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/boproposals/rejectProposal/"+id,
				async: false,
				type: "POST",
				success: function(data2){
					data2 = $.trim(data2);
					if(data2 == "1")
					{
						displayMsg("success","Record has been Rejected!");
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
	
	function acceptProposal(id)
	{
		var r=confirm("Are you sure you want to accept this record?");
		if (r==true)
		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/boproposals/acceptProposal/"+id,
				async: false,
				type: "POST",
				success: function(data2){
					data2 = $.trim(data2);
					if(data2 == "1")
					{
						displayMsg("success","Record has been Accepted!");
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
	document.title = "Proposals";
</script>