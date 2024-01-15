<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>SM Partner Mapping - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">SM Name</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Partner Name</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_1" name="sSearch_1" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-2 col-6 txt-lr">
						<label style="visibility: hidden;" class="mt-1 display-none-sm">For Space</label>
						<a href="<?php echo base_url(); ?>smcreditors"><button class="btn cnl-btn">Clear Search</button></a>
					</div>

					<div class="col-md-1 col-6 txt-lr">
						<label style="visibility: hidden;" class="mt-1 col-md-12 display-none-sm">For</label>
						<a href="#" onclick="exportResults();"><button class="btn exp-button" style="margin-top: 2%;">Export <i class="ti-export"></i></button></a>
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
					<?php if (in_array('SMCreditorMappingAdd', $this->RolePermission)) { ?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url(); ?>smcreditors/addEdit">
								<button class="btn btn-sec add-btn fl-right">Add Mapping <span class="display-none-sm"><span class="material-icons spn-icon">add_circle_outline</span></span></button>
							</a>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table" style="height:300px;">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th>Partner Name</th>
								<th>SM Name</th>
								<!--<th>Status</th>-->
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
	function deleteData(id) {
		var r = confirm("Are you sure you want to delete this record?");
		if (r == true) {
			$.ajax({
				url: "<?php echo base_url() . $this->router->fetch_module(); ?>/smcreditors/delRecord/" + id,
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
		var sm_name = $("#sSearch_0").val();
		var creditor_name = $("#sSearch_1").val();

		//alert("creditor_id: "+creditor_id+" sm_id: "+sm_id+" location_id: "+location_id+" status: "+" date_from: "+date_from+" date_to: "+date_to);
		//return false;

		$.ajax({
			url: "<?php echo base_url() . $this->router->fetch_module(); ?>/smcreditors/exportexcel",
			async: false,
			type: "POST",
			dataType: 'json',
			data: {
				sm_name: sm_name,
				creditor_name: creditor_name
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

	document.title = "SM Partner Mapping";
</script>