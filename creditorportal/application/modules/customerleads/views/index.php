<style>
	@media (min-width: 768px){
		.col-md-1{
			max-width: none;
		}
	}

</style>
<!-- Center Section -->
<script type="text/javascript" src='<?PHP echo base_url('assets/js/html2pdf.bundle.js', PROTOCOL); ?>'></script>

<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Customer Leads - <i class="ti-user"></i></p>
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
							<input id="trace_id" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter Trace/Lead Id" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="trace_id" class="col-form-label">Lan Number</label>
						<div class="dataTables_filter input-group">
							<input id="lan_id" name="sSearch_1" type="text" class="searchInput form-control" placeholder="Enter Lan Number" aria-describedby="inputGroupPrepend">
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
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
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
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">contact_mail</span></span>
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
							<input id="from_date" name="sSearch_8" type="text" class="searchInput form-control datepicker" placeholder="Select From Date" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">To Date</label>
						<div class="dataTables_filter input-group">
							<input id="to_date" name="sSearch_9" type="text" class="searchInput form-control datepicker" placeholder="Select To Date" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>


					<div class="col-md-2 col-6 txt-lr">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url(); ?>customerleads"><button class="btn cnl-btn">Clear Search</button></a>
					</div>

					<div class="col-md-1 col-4 text-right">
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
						<p>Customer Leads</p>
					</div>
					<?php if (in_array('LeadAdd', $this->RolePermission)) { ?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url(); ?>customerleads/addEdit">
								<button class="btn btn-sec add-btn fl-right">Add Lead <span class="display-none-sm"><span class="material-icons spn-icon">add_circle_outline</span></span></button>
							</a>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th>Trace/Lead Id</th>
								<th>Lan Number</th>
                                <th>Is api Lead</th>
								<th>Plan Type</th>
								<th>Partner Name</th>
								<th>SM</th>
								<th>Customer Name</th>
								<th>Mobile</th>
								<th>Email</th>
								<th>Status</th>
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
        autoclose: true,

        maxDate: new Date()

	});

	function deleteData(id) {
		var r = confirm("Are you sure you want to delete this record?");
		if (r == true) {
			$.ajax({
				url: "<?php echo base_url() . $this->router->fetch_module(); ?>/users/delRecord/" + id,
				async: false,
				type: "POST",
				success: function(data2) {
					data2 = $.trim(data2);
					if (data2 == "1") {
						displayMsg("success", "Record has been Deleted!");
						setTimeout("location.reload(true);", 1000);

					} else {
						displayMsg("error", "Oops something went wrong!");
						setTimeout("location.reload(true);", 1000);
					}
				}
			});
		}
	}

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


		//alert("creditor_id: "+creditor_id);
		//return false;

		$.ajax({
			url: "<?php echo base_url() . $this->router->fetch_module(); ?>/customerleads/exportexcel",
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
				to_date: to_date
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
	function download_allFiles(all_links,coi_num){
        var links=all_links.split(",");
        var coi_num=coi_num.split(",");
        var link = document.createElement('a');

        link.setAttribute('download', null);
        link.style.display = 'none';

        document.body.appendChild(link);
        $.each(links, function (i,lnk) {
            window.open(lnk);
          /*  link.setAttribute('href',lnk);
            link.setAttribute('download',coi_num[i]);
            link.click();*/
            //window.open(lnk);
          //  console.log(lnk)
        });
      //  document.body.removeChild(link);
    }

	document.title = "Customer Leads";

	function DownloadCOI(lead_id,type) {
    //    ajaxindicatorstart("Downloading...");
        if(type == 1){
          //  var url= "/quotes/coidownload";
          //  var lead_id = $("#lead_view").text();
            window.open('/quotes/coidownloadNew?lead_id='+btoa(lead_id)
            );
            return;
        }else{
         //   var url= "/GadgetInsurance/coidownload";
         //   var lead_id = $("#lead_view").text();
            window.open('/GadgetInsurance/coidownloadNew?lead_id='+btoa(lead_id)
            );
            return;
        }
        $.ajax({
            url:url,
            type: "POST",
            data: {
                'lead_id': lead_id,
            },
            dataType: 'html',
            success: function(response) {
                html2pdf()
                    .set({
                        filename: 'coi_' + lead_id + '.pdf'
                    })
                    .from(response)
                    .save();
                setTimeout(function() {
                    ajaxindicatorstop();
                }, 5000);
            }
        });
    }
</script>