<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-12 col-12">
						<p>SM Dashboard - <i class="ti-user"></i></p>
					</div>
					<!-- <div class="col-md-2 col-2">
					</div> -->
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Channel Partner Name</label>
						<div class="dataTables_filter input-group">
							<select id="sSearch_0" class="searchInput form-control" name="sSearch_0"> 
								<option value="">All</option>
								<?php 
								if(!empty($creditors)){
									for($i=0; $i < sizeof($creditors); $i++){
								?>
									<option value="<?php echo $creditors[$i]['creditor_id']; ?>" ><?php echo $creditors[$i]['creaditor_name']; ?></option>
								<?php 
									}
								}
								?> 
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">From Date</label>
						<div class="dataTables_filter input-group">
							<input id="from_date" name="sSearch_1" type="text" class="searchInput form-control datepicker" placeholder="Select ..." aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">To Date</label>
						<div class="dataTables_filter input-group">
							<input id="end_date" name="sSearch_2" type="text" class="searchInput form-control datepicker" placeholder="Select ..." aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Status</label>
						<div class="dataTables_filter input-group">
							<select id="sSearch_3" name="sSearch_3" class="searchInput form-control" name="sSearch_3"> 
								<option value="">All</option>
								<option value="Approved" selected>Issued</option>
								<option value="Pending">Pending </option>
								<option value="Rejected">Cancelled</option>
								
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-2 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Sort By Asc/Desc</label>
						<div class="dataTables_filter input-group">
							<select id="sSearch_4" name="sSearch_4" class="searchInput form-control"> 
								<option value="desc" selected>Descending</option>
								<option value="asc">Ascending </option>								
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">toggle_off</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">SM Name</label>
						<div class="dataTables_filter input-group inp-frame">
							<?php echo $_SESSION['webpanel']['employee_full_name'];?>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">SM ID</label>
						<div class="dataTables_filter input-group inp-frame">
							<?php echo $_SESSION['webpanel']['employee_code'];?>
						</div>
					</div>
					
					
					
					<div class="col-md-2 col-6 text-left">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>smdashboard"><button class="btn cnl-btn">Clear Search</button></a>
					</div>
					
					<div class="col-md-1 col-6 text-right">
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
						<p>Details</p>
					</div>
						<div class="col-md-4 col-6">
							
						</div>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table" style="height:300px;">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th data-bSortable="false">Current Ranking</th>
								<th data-bSortable="false">Channel Partner Name</th>
								<th data-bSortable="false">This Week(Net)</th>
								<th data-bSortable="false">This Week(Gross)</th>
								<th data-bSortable="false">MTD(Net)</th>
								<th data-bSortable="false">MTD(Gross)</th>
								<th data-bSortable="false">YTD(Net)</th>
								<th data-bSortable="false">YTD(Gross)</th>
								<th data-bSortable="false">Date Range Total(Net)</th>
								<th data-bSortable="false">Date Range Total(Gross)</th>
								<th data-bSortable="false">Date From</th>
								<th data-bSortable="false">Date To</th>
								<th data-bSortable="false">Details</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
						<tfoot>
							<tr>
							<td colspan="2">Total</td>
							<td class="weeklyNetTot"></td>
							<td class="weeklyGrossTot"></td>
							<td class="mothlyNetTot"></td>
							<td class="mothlyGrossTot"></td>
							<td class="yearlyNetTot"></td>
							<td class="yearlyGrossTot"></td>
							<td class="dateRangeNetTot"></td>
							<td class="dateRangeGrossTot" colspan="4"></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Center Section End-->		
<script type="text/javascript">
$(".datepicker1").datepicker({ 
	dateFormat: 'dd-mm-yy',
	changeMonth: true,
	changeYear: true,
	yearRange: "-100:" + new Date('Y'),
	maxDate: new Date()
	
});
//$('.datepicker').datetimepicker({});

$("#from_date").datepicker({ 
	dateFormat: 'dd-mm-yy',
	changeMonth: true,
	changeYear: true,
	//yearRange: "-100:" + new Date('Y'),
	//maxDate: new Date(),
	//minDate: new Date(),
	onSelect: function(selected) {
		$(this).change();
		$("#end_date").datepicker("option","minDate", selected)
	}
	
});

$("#end_date").datepicker({ 
	dateFormat: 'dd-mm-yy',
	changeMonth: true,
	changeYear: true,
	//yearRange: "-100:" + new Date('Y'),
	//maxDate: new Date(),
	onSelect: function(selected) {
		$(this).change();
		$("#from_date").datepicker("option","maxDate", selected)
	}
	
});

function exportResults(){
	var creditor_id = $("#sSearch_0").val();
	var date_from = $("#from_date").val();
	var date_to = $("#end_date").val();
	var status = $("#sSearch_3").val();
	
	
	//alert("creditor_id: "+creditor_id);
	//return false;
	
	$.ajax({
		url: "<?php echo base_url().$this->router->fetch_module();?>/smdashboard/exportexcel",
		async: false,
		type: "POST",
		dataType: 'json',
		data: {creditor_id:creditor_id, status:status, date_from:date_from, date_to:date_to },
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

document.title = "Dashboard";
</script>