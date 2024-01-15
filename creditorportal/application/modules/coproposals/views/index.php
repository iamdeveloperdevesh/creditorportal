<!-- start: Content -->
<style>
	.collapse.in {
		display: block;
	}

	.collapse {
		display: none;
		padding: 15px;
	}
</style>
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
							<input id="trace_id" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="trace_id" class="col-form-label">Lan Number</label>
						<div class="dataTables_filter input-group">
							<input id="lan_id" name="sSearch_1" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
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
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
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
					<div class="col-md-3 mb-3">
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
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">email</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">From Date</label>
						<div class="dataTables_filter input-group">
							<input id="from_date" name="sSearch_8" type="text" class="searchInput form-control datepicker" placeholder="Select ..." aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">To Date</label>
						<div class="dataTables_filter input-group">
							<input id="to_date" name="sSearch_9" type="text" class="searchInput form-control datepicker" placeholder="Select ..." aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>


					<div class="col-md-2 col-6 text-left">
						<label style="visibility: hidden;" class="mt-1 display-sm-lbl">For Space</label>
						<a href="<?php echo base_url(); ?>boproposals"><button class="btn cnl-btn">Clear Search</button></a>
					</div>
					<?php /*<div class="col-md-2 col-6 text-left">	
					<label style="visibility: hidden;" class="mt-2 display-sm-lbl">For Space</label>
						<a href="javascript:void(0)" class="checkall btn del-btn" data-value="check">Check All</a>
					</div>*/ ?>
					<div class="row mt-2 col-md-12">
						<div class="col-md-1 col-5 text-center">
							<a href="javascript:void(0)" class="export btn exp-button">Export <i class="ti-export"></i></a>
						</div>
						<div class="col-md-2 col-6 text-left ml-4">
							<a href="javascript:void(0)" class="import btn imp-button">Import <i class="ti-import"></i></a>
						</div>
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
						<p>CO Proposals</p>
					</div>
					<?php if (in_array('LeadAdd', $this->RolePermission)) { ?>
						<!--<div class="col-md-4 col-6">
							<a href="<?php echo base_url(); ?>customerleads/addEdit">
								<button class="btn btn-sec add-btn fl-right">Add Lead <span class="display-none-sm"><span class="material-icons spn-icon">add_circle_outline</span></span></button>
							</a>
						</div>-->
					<?php } ?>
				</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th data-bSortable="false"><input type="checkbox" id="checkall" class="checkall" /><label>Check All</label></th>
								<th>Trace/Lead Id</th>
								<th>Lan Number</th>
								<th>Plan Type</th>
								<th>Partner Name</th>
								<th>SM</th>
								<th>Customer Name</th>
								<th>Mobile</th>
								<th>Email</th>
								<th>Status</th>
								<th>Updated On</th>


								<th>Net Premium</th>
								<th>Gross Premium</th>
								<th>Loan Amount</th>
								<th>Total Sum Assured</th>
								<th>Payment mode</th>
								<th>Days Pending</th>
								<th>COI</th>

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
						alert("COI Numbers: " + res.cois_numbers);
					} else {
						alert("COI not found!");
					}
				}
			});
		}
	}

	function rejectProposal(id) {
		var r = confirm("Are you sure you want to reject this proposal?");
		if (r == true) {
			$.ajax({
				url: "<?php echo base_url() . $this->router->fetch_module(); ?>/coproposals/rejectProposal/" + id,
				async: false,
				type: "POST",
				success: function(data2) {
					data2 = $.trim(data2);
					if (data2 == "1") {
						displayMsg("success", "Record has been Rejected!");
						setTimeout("location.reload(true);", 1000);

					} else {
						displayMsg("error", "Oops something went wrong!");
						setTimeout("location.reload(true);", 1000);
					}
				}
			});
		}
	}

	function acceptProposal(id) {
		var r = confirm("Are you sure you want to accept this record?");
		if (r == true) {
			$.ajax({
				url: "<?php echo base_url() . $this->router->fetch_module(); ?>/coproposals/acceptProposal/" + id,
				async: false,
				type: "POST",
				success: function(data2) {
					data2 = $.trim(data2);
					if (data2 == "1") {
						displayMsg("success", "Record has been Accepted!");
						setTimeout("location.reload(true);", 1000);

					} else {
						displayMsg("error", "Oops something went wrong!");
						setTimeout("location.reload(true);", 1000);
					}
				}
			});
		}
	}

	function moveToUW(id) {
		var r = confirm("Are you sure you want to move this record to underwriting?");
		if (r == true) {
			$.ajax({
				url: "<?php echo base_url() . $this->router->fetch_module(); ?>/coproposals/moveToUW/" + id,
				async: false,
				type: "POST",
				success: function(data2) {
					data2 = $.trim(data2);
					if (data2 == "1") {
						displayMsg("success", "Record moved to underwriting!");
						setTimeout("location.reload(true);", 1000);

					} else {
						displayMsg("error", "Oops something went wrong!");
						setTimeout("location.reload(true);", 1000);
					}
				}
			});
		}
	}
	$('.checkall').on('change', function() {
		if ($(this).prop('checked') == true) {
			$('.check').prop('checked', true);
			//$(this).data('value','uncheck');
			$(this).next('label').text("Uncheck All");
		} else {
			$('.check').prop('checked', false);
			//$(this).data('value','check');
			$(this).next('label').text("Check All");
		}
	});

	$('.import').click(function() {
		$('#importexcel').modal('show');
	});

	$('.export').click(function() {
		var count = $('input.check:checked').length;
		if (count <= 0) {
			alert("No option selected");
			return false;
		}
		var searchIDs = $(".check:checkbox:checked").map(function() {
			return $(this).data('id');
		}).get();

		$.ajax({
			url: "<?php echo base_url() . $this->router->fetch_module(); ?>/coproposals/exportexcel",
			async: false,
			type: "POST",
			dataType: 'json',
			data: {
				id: searchIDs
			},
			success: function(response) {
				if (response.success) {
					displayMsg("success", response.msg);
					/*var blob=new Blob([response.Data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download=response.Data;
					link.click();*/

					window.open(response.Data, '_blank');

				} else {
					displayMsg("error", response.msg);
					return false;
				}
			}
		});

	});
	document.title = "Proposals";
</script>

<script type="text/javascript">
	var vRules = {
		importdata: {
			required: true
		}
	};

	var vMessages = {
		importdata: {
			required: "Please choose file."
		}
	};

	$("#excelimport").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) {
			var act = "<?php echo base_url(); ?>coproposals/uploadexcel";
			$("#excelimport").ajaxSubmit({
				url: act,
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				beforeSubmit: function(arr, $form, options) {
					$(".btn-primary").hide();
					//return false;
				},
				success: function(response) {
					$(".btn-primary").show();
					if (response.success) {
						displayMsg("success", response.msg);
						//if(myArray.length != 0){
						passLeadsToInsurance(response.data);
						//}

						setTimeout(function() {
							window.location = "<?php echo base_url(); ?>coproposals";
						}, 3000);
					} else {
						displayMsg("error", response.msg);
						$(".btn-primary").show();
						return false;
					}
				}
			});
		}
	});

	function passLeadsToInsurance(leadArr) {
		/*$.each( leadArr, function( key, value ) {
		  alert( key + ": " + value );
		});*/

		//check array not empty.
		$.ajax({
			url: "<?php echo base_url(); ?>coproposals/passLeadsToInsurance",
			data: {
				leadArr: leadArr
			},
			type: 'post',
			dataType: 'json',
			success: function(res) {

			}
		});

	}
</script>