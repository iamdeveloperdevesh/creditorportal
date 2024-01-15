<?php //echo "<pre>";print_r($roles);exit;?>
<!-- start: Content -->

<style>
.collapse.in {
    display: block;
}
.collapse {
    display: none;
    padding:15px;
}
</style>
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
						<li class="breadcrumb-item"><a href="<?php echo base_url()?>coproposals"><i data-feather="home"></i></a></li>
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
				<!--<div class="col-sm-2 col-xs-12">
					<div class="dataTables_filter searchFilterClass form-group">
						<label for="status" class="control-label">Status</label>
						<select name="status" id="status" class="searchInput form-control">
							<option value="">Select</option>
							<option value="Pending">Pending</option>
							<option value="In-Progress">In-Progress</option>
							<option value="Customer-Payment-Awaiting">Customer-Payment-Awaiting</option>
							<option value="BO-Approval-Awaiting">BO-Approval-Awaiting</option>
							<option value="CO-Approval-Awaiting">CO-Approval-Awaiting</option>
							<option value="Client-Approval-Awaiting">Client-Approval-Awaiting</option>
							<option value="Discrepancy">Discrepancy</option>
							<option value="UW-Approval-Awaiting">UW-Approval-Awaiting</option>
							<option value="Rejected">Rejected</option>
							<option value="Approved">Approved</option>
						</select>
					</div>
				</div>-->
				
				
				<div class="control-group clearFilter form-group" style="margin-left:5px;">
					<div class="controls">
						<a href="<?php echo base_url();?>coproposals">
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
				<div class="row">
					<div class="col-sm-3 col-xs-12" >
						<a href="javascript:void(0)" class="checkall btn btn-success" data-value="check">Check All</a>
					</div>
					<div class="col-sm-3 col-xs-12" >
						<a href="javascript:void(0)" class="export btn btn-success" >Export</a>
					</div>
					<div class="col-sm-3 col-xs-12" >
						<a href="javascript:void(0)" class="import btn btn-success" >Import</a>
					</div>
				</div><br><br>
            	<div class="table-responsive scroll-table">
                    <table class="dynamicTable display table table-bordered non-bootstrap">
                        <thead>
                            <tr>
                                <th>Select</th>
								<th>Trace/Lead Id</th>
                                <th>Plan Name</th>
                                <th>Creditor Name</th>
								<th>SM</th>
                                <th>Customer Name</th>
                                <th>Mobile</th>
                                <th>Eamil</th>
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
<div class="modal fade" id="importexcel" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
			<h4 class="modal-title text-center">Import Excel</h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
			<form name="excelimport" id="excelimport" method="post" enctype="multipart/form-data">
				<input type="file" name="importdata" />
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>			
<script>
	function rejectProposal(id)
	{
		var r=confirm("Are you sure you want to reject this proposal?");
		if (r==true)
		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/coproposals/rejectProposal/"+id,
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
				url: "<?php echo base_url().$this->router->fetch_module();?>/coproposals/acceptProposal/"+id,
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
	
	function moveToUW(id)
	{
		var r=confirm("Are you sure you want to move this record to underwriting?");
		if (r==true)
		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/coproposals/moveToUW/"+id,
				async: false,
				type: "POST",
				success: function(data2){
					data2 = $.trim(data2);
					if(data2 == "1")
					{
						displayMsg("success","Record moved to underwriting!");
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
	$('.checkall').click(function(){
		if($(this).data('value') == 'check'){
			$('.check').prop('checked',true);
			$(this).data('value','uncheck');
			$(this).html("Uncheck All");
		}else{
			$('.check').prop('checked',false);
			$(this).data('value','check');
			$(this).html("Check All");
		}
	});
	$('.import').click(function(){
		$('#importexcel').modal('show');
	});
	$('.export').click(function(){
		var count = $('input.check:checked').length;
		if(count <= 0){
			alert("No option selected");
			return false;
		}
		var searchIDs = $(".check:checkbox:checked").map(function(){
		  return $(this).data('id');
		}).get(); 
		
		$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/coproposals/exportexcel",
				async: false,
				type: "POST",
				dataType: 'json',
				data: {id:searchIDs},
				success: function(data2){
					if(response.success)
					{
						displayMsg("success",response.msg);
						var blob=new Blob([response.Data]);
						var link=document.createElement('a');
						link.href=window.URL.createObjectURL(blob);
						link.download=response.Data;
						link.click();
						
					}
					else
					{	
						displayMsg("error",response.msg);
						return false;
					}
				}
			});
		
	});
	document.title = "Proposals";
</script>

<script>

var vRules = {
};

var vMessages = {
};

$("#excelimport").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>coproposals/uploadexcel";
		$("#excelimport").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
				$(".btn-primary").hide();
				//return false;
			},
			success: function (response) 
			{
			    console.log(response);
			}
		});
	}
});

</script>