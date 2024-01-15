<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Proposals - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="trace_id" class="col-form-label">Trace/Lead Id</label>
						<div class="dataTables_filter input-group">
							<input id="trace_id" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="trace_id" class="col-form-label">Lan Number</label>
						<div class="dataTables_filter input-group">
							<input id="lan_id" name="sSearch_1" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					 <div class="col-md-3 mb-3">
						<label for="plan_name" class="col-form-label">Plan Type</label>
						<div class="dataTables_filter input-group">
							<input id="plan_name" name="sSearch_2" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">chrome_reader_mode</span></span>
							</div>
						</div>
					</div>
					 <div class="col-md-3 mb-3">
						<label for="creditor_name" class="col-form-label">Partner Name</label>
						<div class="dataTables_filter input-group">
							<input id="creditor_name" name="sSearch_3" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="employee_full_name" class="col-form-label">SM</label>
						<div class="dataTables_filter input-group">
							<input id="employee_full_name" name="sSearch_4" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					 <div class="col-md-3 mb-3">
						<label for="full_name" class="col-form-label">Customer Name</label>
						<div class="dataTables_filter input-group">
							<input id="full_name" name="sSearch_5" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					<!--<div class="col-md-3 mb-3">
						<label for="mobile_no" class="col-form-label">Mobile No</label>
						<div class="dataTables_filter input-group">
							<input id="mobile_no" name="sSearch_6" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="email_id" class="col-form-label">Email ID</label>
						<div class="dataTables_filter input-group">
							<input id="email_id" name="sSearch_7" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">alternate_email</span></span>
							</div>
						</div>
					</div>-->
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">From Date</label>
						<div class="dataTables_filter input-group">
							<input id="from_date"  name="sSearch_6" type="text" class="searchInput form-control datepicker" placeholder="Select ..." aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">To Date</label>
						<div class="dataTables_filter input-group">
							<input id="to_date"  name="sSearch_7" type="text" class="searchInput form-control datepicker" placeholder="Select ..." aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-2 col-12 txt-lr">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>boproposals"><button class="btn cnl-btn">Clear Search</button></a>
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
						<p>BO Proposals</p>
					</div>
					<?php if(in_array('LeadAdd',$this->RolePermission)){?>
						<!--<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>customerleads/addEdit">
								<button class="btn btn-sec add-btn fl-right">Add Lead <span class="display-none-sm"><span class="material-icons spn-icon">add_circle_outline</span></span></button>
							</a>
						</div>-->
					<?php }?>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th>Trace/Lead Id</th>
								<th>Lan Number</th>
                                <th>Plan Type</th>
                                <th>Partner Name</th>
								<th>SM</th>
                                <th>Customer Name</th>
                                <!--<th>Mobile</th>
                                <th>Email</th>-->
								<th>Status</th>
								<th  data-bSortable="false">Payment mode</th>
								<th  data-bSortable="false">Remark</th>
								<th  data-bSortable="false">Transaction Number</th>
								<th  data-bSortable="false">Cheque Number</th>
								<th  data-bSortable="false">Last Saved</th>

								<!--<th>Net Premium</th>-->
								<th  data-bSortable="false">Gross Premium</th>
								<th  data-bSortable="false">Loan Amount</th>
								<th  data-bSortable="false">Total Sum Assured</th>
								
								<th  data-bSortable="false">Days Pending</th>
								<!--<th>COI</th>-->
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
	$(".datepicker").datepicker({ 
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		yearRange: "-100:" + new Date('Y'),
		maxDate: new Date()
		
	});

	function getCOI(lead_id) {
		if (lead_id != "") {
			$.ajax({
				url: "<?php echo base_url(); ?>customerproposals/getCOI",
				data: {
					lead_id: lead_id
				},
				type: 'post',
				dataType: 'json',
				success: function(res) {
					if (res['status'] == "success") {
						alert("COI Numbers: "+res.cois_numbers);
					} else {
						alert("COI not found!");
					}
				}
			});
		}
	}
	
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
	
	function moveToUW(id)
	{
		var r=confirm("Are you sure you want to move this record to underwriting?");
		if (r==true)
		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/boproposals/moveToUW/"+id,
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
	document.title = "Proposals";
</script>