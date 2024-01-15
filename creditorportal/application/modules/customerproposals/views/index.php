<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Customer Proposals - <i class="ti-user"></i></p>
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
							<input id="trace_id" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter Trace/Lead Id" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="trace_id" class="col-form-label">Lan Number</label>
						<div class="dataTables_filter input-group">
							<input id="lan_id" name="sSearch_1" type="text" class="searchInput form-control" placeholder="Enter Lan Number" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					 <div class="col-md-3 mb-3">
						<label for="plan_name" class="col-form-label">Plan Type</label>
						<div class="dataTables_filter input-group">
							<input id="plan_name" name="sSearch_2" type="text" class="searchInput form-control" placeholder="Enter Plan Type" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
							</div>
						</div>
					</div>
					 <div class="col-md-3 mb-3">
						<label for="creditor_name" class="col-form-label">Partner Name</label>
						<div class="dataTables_filter input-group">
							<input id="creditor_name" name="sSearch_3" type="text" class="searchInput form-control" placeholder="Enter Partner Name" aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="employee_full_name" class="col-form-label">SM</label>
						<div class="dataTables_filter input-group">
							<input id="employee_full_name" name="sSearch_4" type="text" class="searchInput form-control" placeholder="Enter SM" aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					 <div class="col-md-3 mb-3">
						<label for="full_name" class="col-form-label">Customer Name</label>
						<div class="dataTables_filter input-group">
							<input id="full_name" name="sSearch_5" type="text" class="searchInput form-control" placeholder="Enter Customer Name" aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div> 
					<div class="col-md-3 mb-3">
						<label for="mobile_no" class="col-form-label">Mobile No</label>
						<div class="dataTables_filter input-group">
							<input id="mobile_no" name="sSearch_6" type="text" class="searchInput form-control" placeholder="Enter Mobile No" aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="email_id" class="col-form-label">Email ID</label>
						<div class="dataTables_filter input-group">
							<input id="email_id" name="sSearch_7" type="text" class="searchInput form-control" placeholder="Enter Email ID" aria-describedby="inputGroupPrepend" required="">
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">alternate_email</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">From Date</label>
						<div class="dataTables_filter input-group">
							<input id="from_date"  name="sSearch_8" type="text" class="searchInput form-control datepicker" placeholder="Select From Date" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">To Date</label>
						<div class="dataTables_filter input-group">
							<input id="to_date"  name="sSearch_9" type="text" class="searchInput form-control datepicker" placeholder="Select To Date" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="status" class="col-form-label">Status</label>
						<div class="dataTables_filter input-group">
							<select name="status" id="status" class="searchInput form-control">
								<option value="">Select Status</option>
								<option value="Pending">Proposal Creation</option>
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
							<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
							</div>
						</div>
					</div>
					
					
					<div class="col-md-2 col-12 txt-lr">
						<a href="<?php echo base_url();?>customerproposals">
						<label style="visibility: hidden;" class="mt-1">For Spacing only</label>
						<button class="btn cnl-btn">Clear Search</button></a>
					</div>

				</div>
                <div class="row">
                    <div class="col-md-4">
                        <select name="type_download" id="type_download" class="searchInput form-control">
                            <option value="1">Group Policy</option>
                            <option value="2">Marine Insurance</option>

                        </select>
                    </div>
                    <div class="col-md-1 col-12 ">
                        <label style="visibility: hidden;" class="mt-1 col-md-12">For</label>
                        <a href="#" onclick="exportResults();"><button class="btn exp-button">Export <i class="ti-export"></i></button></a>
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
						<p>Proposals</p>
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
                                <th>Mobile</th>
                                <th>Email</th>
								<th>Status</th>
								<th  data-bSortable="false">Payment mode</th>
								<th  data-bSortable="false">Remark</th>
								<th  data-bSortable="false">Location</th>

								<th  data-bSortable="false">Net Premium</th>
								<th  data-bSortable="false">Gross Premium</th>
								<th  data-bSortable="false">Loan Amount</th>
								<th  data-bSortable="false">Total Sum Assured</th>
								
								<th  data-bSortable="false">Days Pending</th>
								<th  data-bSortable="false">COI</th>
								<th  data-bSortable="false">Last Saved</th>

								<th  data-bSortable="false">Transaction Number</th>
								<th  data-bSortable="false">Expiry Date</th>
								<th  data-bSortable="false">Renewal Status</th>

								<th  data-bSortable="false" class="text-center">Actions</th>
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


	function deleteData(id)
	{
		var r=confirm("Are you sure you want to delete this record?");
		if (r==true)
		{
			$.ajax({
				url: "<?php echo base_url().$this->router->fetch_module();?>/customerproposals/delRecord/"+id,
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

	$('body').on('click', '.retrigger-link', function(){

		var data = {};
		data.lead_id = $(this).attr('data-lead-id');

		$.ajax({

			'url' : '<?php echo base_url(); ?>customerproposals/retriggerLink',
			'method' :'POST',
			'async' : false,
			'cache' : false,
			'data' : data,
			'success': function(response){

				if(response.success){

					display.success(response.msg);
				}
				else{

					display.error(response.msg);
				}
			}
		});
	});
    function exportResults() {
        var trace_id = $("#trace_id").val();
        var lan_id = $("#lan_id").val();
        var plan_name = $("#plan_name").val();
        var creditor_name = $("#creditor_name").val();
        var employee_full_name = $("#employee_full_name").val();
        var full_name = $("#full_name").val();
        var mobile_no = $("#mobile_no").val();
        var email_id = $("#email_id").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var type_download = $("#type_download").val();


        //alert("creditor_id: "+creditor_id);
        //return false;

        $.ajax({
            url: "<?php echo base_url() . $this->router->fetch_module(); ?>/customerproposals/exportexcel",
            async: false,
            type: "POST",
            dataType: 'json',
            data: {
                trace_id: trace_id,
                lan_id: lan_id,
                plan_name: plan_name,
                creditor_name: creditor_name,
                employee_full_name: employee_full_name,
                full_name: full_name,
                mobile_no: mobile_no,
                email_id: email_id,
                from_date: from_date,
                to_date: to_date,
                type_download: type_download
            },
            success: function(response) {
                if (response.success) {
                    displayMsg("success", response.msg);
                    window.open(response.Data, '_blank');
                } else {
                    displayMsg("error", response.msg);
                    return false;
                }
            }
        });


    }
	document.title = "Customer Proposals";
</script>