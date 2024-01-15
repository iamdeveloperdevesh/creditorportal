<?php //echo "<pre>";print_r($roles);exit;
//echo $_GET['cid'];exit;
//$_GET['cid'] = $_GET['cid'];
?>
<script type="text/javascript" src='<?PHP echo base_url('assets/js/html2pdf.bundle.js', PROTOCOL); ?>'></script>
<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Dashboard Details - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Channel Partner</label>
						<div class="dataTables_filter input-group inp-frame">
							<?php echo $creditors[0]['creaditor_name'];//echo "<pre>";print_r($creditors);exit;?>
						</div>
					</div>

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">SM Name</label>
						<div class="dataTables_filter input-group inp-frame">
							<?php echo $sm[0]['employee_full_name'];//echo "<pre>";print_r($creditors);exit;?>
						</div>
					</div>

					<div class="col-md-2 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Status</label>
						<div class="dataTables_filter input-group">
							<select id="sSearch_0" class="searchInput form-control" name="sSearch_0" style="height: calc(2.7rem + 0px);"> 
								<option value="All" selected>All</option>
								<option value="Approved" >Issued</option>
								<option value="Pending">Pending </option>
								<option value="Rejected">Cancelled</option>
								
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-2 col-6 text-left">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>dashboarddetails?cid=<?php echo $_GET['cid'];?>&smid=<?php echo $_GET['smid'];?>"><button class="btn cnl-btn">Clear Search</button></a>
					</div>

					<div class="col-md-2 col-6 txt-lr">
					<label style="visibility: hidden;" class="mt-1 col-md-12">For</label>
						<a href="#" onclick="exportResults();"><button class="btn exp-button">Export <i class="ti-export"></i></button></a>
					</div>
					
					<div class="col-md-1 col-12 txt-lr">
						<!-- <label style="visibility: hidden;" class="mt-1">For purpose</label> -->
						<a href="#" onclick="goBack()"><button class="btn back-btn-1 mar-2">Back <i class="ti-back-left"></i></button></a>
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
						<p>Details</p>
					</div>
						<div class="col-md-4 col-6">
							
						</div>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table" style="height:300px;">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3" callfunction = "<?php echo base_url() ?>dashboarddetails/fetch?cid=<?php echo $_GET['cid'];?>&smid=<?php echo $_GET['smid'];?>">
						<thead class="tbl-cre">
							<tr>
								<th data-bSortable="false">Lead ID/Trace ID</th>
								<th data-bSortable="false">Date</th>
								<th data-bSortable="false">First Name</th>
								<th data-bSortable="false">Last Name</th>
								<th data-bSortable="false">Plan Type</th>
								<th data-bSortable="false">Policy Type</th>
								<th data-bSortable="false">Premium Amount</th>
								<th data-bSortable="false">COI Numbers</th>
								<th data-bSortable="false">Sum Insured</th>
								<th data-bSortable="false">Payment Option</th>
								<th data-bSortable="false">Status</th>
								<th data-bSortable="false">View Details</th>
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

function goBack() {
  window.history.back();
}

function exportResults(){
	var creditor_id = '<?php echo $_GET['cid'];?>';
	var sm_id = '<?php echo $_GET['smid'];?>';
	var status = $("#sSearch_0").val();
	
	
	//alert("creditor_id: "+creditor_id+ "sm_id:"+sm_id+" status:"+status);
	//return false;
	
	$.ajax({
		url: "<?php echo base_url().$this->router->fetch_module();?>/dashboarddetails/exportexcel",
		async: false,
		type: "POST",
		dataType: 'json',
		data: {creditor_id:creditor_id, status:status, sm_id:sm_id},
		success: function (response){
			if(response.success)
			{
				displayMsg("success",response.msg);
				window.open(response.Data,'_blank' );
			}
			else
			{	
				displayMsg("error",response.msg);
				return false;
			}
		}
	});
	
	
}
	document.title = "Dashboard Details";

function DownloadCOI(lead_id) {
    //    ajaxindicatorstart("Downloading...");
    $.ajax({
        url: "/quotes/coidownload",
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