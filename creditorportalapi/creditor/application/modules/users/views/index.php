<?php //echo "<pre>";print_r($roles);exit;?>
<!-- start: Content -->
<div id="content" class="page-body">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-lg-6">
					<div class="page-header-left">
						<h3>Users</h3>
					</div>
				</div>
				<div class="col-lg-6">
					<ol class="breadcrumb pull-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>users"><i data-feather="home"></i></a></li>
						<li class="breadcrumb-item active">Users</li>
					</ol>
				</div>
			</div>
		</div>
	</div> 
    <div class="card">
    	<div class="page-title-border">
            <div class="col-sm-12">
			<?php if(in_array('UserAdd',$this->RolePermission)){?>
				<p><a href="<?php echo base_url();?>users/addEdit" class="btn btn-primary icon-btn pull-right"><i class="fa fa-plus"></i>Add User</a></p>
			<?php }?>
            <div class="clearfix"></div>
            </div>
        </div> <br>
		<div class="container">
			<div class="row">
				<div class="col-sm-2 col-xs-12" >
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="userid" class="control-label">User Id</label>
						<input id="userid" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="firstname" class="control-label">First Name</label>
						<input id="firstname" type="text" class="searchInput form-control"/>
					</div>   
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="lastname" class="control-label">Last Name</label>
						<input id="lastname" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="lastname" class="control-label">Employee Code</label>
						<input id="employeecode" type="text" class="searchInput form-control"/>
					</div>
					
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="emailid" class="control-label">Email Id</label>
						<input id="emailid" type="text" class="searchInput form-control"/>
					</div>  
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="contact" class="control-label">Mobile No</label>
						<input id="contact" type="text" class="searchInput form-control"/>
					</div>
				</div>
				<div class="col-sm-2 col-xs-12">
				<div class="dataTables_filter searchFilterClass form-group">
						<label for="role" class="control-label">Role</label>
							<select class="searchInput form-control">
                            <option value="">All</option>
                            <?php 
							if(!empty($roles)){
								//foreach($roles as $cdrow)
								for($i=0; $i < sizeof($roles); $i++){
                            ?>
                                <option value="<?php echo $roles[$i]['role_id']; ?>"><?php echo $roles[$i]['role_name']; ?></option>
                            <?php 
								}
							}
							?>
                        </select>
					</div>  
				</div>
				
				<div class="control-group clearFilter form-group" style="margin-left:5px;">
					<div class="controls">
						<a href="<?php echo base_url();?>users">
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
                                <th>User Id</th>
                                <th>First Name</th>
                                <th>Last Name</th>
								<th>Employee Code</th>
                                <th>Email Id</th>
                                <th>Mobile</th>
                                <th>Role</th>
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
	document.title = "Users";
</script>