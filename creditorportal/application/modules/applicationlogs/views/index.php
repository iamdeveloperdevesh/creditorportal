<style>


	@media(max-width:425px){
		.paging_full_numbers span .paginate_button:nth-child(4){
			position: absolute;
    		right: -27px;

		}

		.paging_full_numbers span .paginate_button:nth-child(5){
			position: absolute;
    		right: -67px;

		}

		.paging_full_numbers span .paginate_button:nth-child(7){
			position: absolute;
    		right: -122px;
		}

		.paging_full_numbers .next{
			position: absolute;
    		right: -191px;
		}

		.paging_full_numbers .last{
			position: absolute;
    		right: -264px;
		}

	}

/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
.form-control[readonly] {
	background-color: #fff;
}
.ellipsis {
	display: none;
}
.buttonMTop {
	margin-top: 34px;
}
.buttonMTop .cnl-btn {
	margin-left: 14px;
}
.buttonMTop .exp-button {
	margin-left: 25px;
}
</style>

<?php //echo "<pre>";print_r($roles);exit;?>
<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Application Logs - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Trace ID</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter Trace ID" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Customer Name</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_1" name="sSearch_1" type="text" class="searchInput form-control fullname" placeholder="Enter Customer Name" pattern="^[a-zA-Z ]+$" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Customer Mobile</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_2" name="sSearch_2" type="number" pattern="[6-9][0-9]{9}" maxlength="10" class="searchInput form-control" placeholder="Enter Customer Mobile" aria-describedby="inputGroupPrepend" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); $(this).val($(this).val().replace(/[^0-9]/g, ''))">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">From Date</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_3" name="sSearch_3" type="text" class="searchInput form-control" placeholder="Select ..." aria-describedby="inputGroupPrepend" readonly >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">To Date</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_4" name="sSearch_4" type="text" class="searchInput form-control" readonly  placeholder="DD-MM-YYYY" aria-describedby="inputGroupPrepend">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Action/Type</label>
						<div class="dataTables_filter input-group">
							<select name="sSearch_5" id="sSearch_5" class="searchInput form-control datepicker" placeholder="Select ..." aria-describedby="inputGroupPrepend">
								<option value="">Select</option>
								<?php
								if (!empty($actions)) {
									for ($i = 0; $i < sizeof($actions); $i++) {
								?>
										<option value="<?php echo $actions[$i]['action']; ?>"><?php echo $actions[$i]['action']; ?></option>
								<?php
									}
								}
								?>
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
							</div>
						</div>
					</div>
										
					<div class="col-md-2 col-6 text-left">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>applicationlogs"><button class="btn cnl-btn">Clear Search</button></a>
					</div>
					
					<div class="col-md-2 col-6">
						
						<label style="visibility: hidden;" class="mt-1 col-md-12">For</label>
						<a href="#" onclick="exportResults();"><button class="btn exp-button mar-2">Export <i class="ti-export"></i></button></a>
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
								<th>Trace ID</th>
								<th>Date</th>
								<th>Action/Type</th>
								<th data-bSortable="false">Request</th>
								<th data-bSortable="false">Response</th>
								<th>Created By</th>

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
	maxDate: 0,
	
});
//$('.datepicker').datetimepicker({});

$("#sSearch_3").datepicker({ 
	dateFormat: 'dd-mm-yy',
	changeMonth: true,
	changeYear: true,
	yearRange: "-100:" + new Date('Y'),
	maxDate: 0,
	//minDate: new Date(),
	onSelect: function(selected) {
		$(this).change();
		$("#sSearch_4").datepicker("option","minDate", selected)
	}
	
});

$("#sSearch_4").datepicker({ 
	dateFormat: 'dd-mm-yy',
	changeMonth: true,
	changeYear: true,
	yearRange: "-100:" + new Date('Y'),
	//maxDate: new Date(),
	onSelect: function(selected) {
		$(this).change();
		$("#sSearch_3").datepicker("option","maxDate", selected)
	}
	
});

function exportResults(){
	var trace_id = $("#sSearch_0").val();
	var customer_name = $("#sSearch_1").val();
	var customer_mob = $("#sSearch_2").val();
	var date_from = $("#sSearch_3").val();
	var date_to = $("#sSearch_4").val();
	var action_val = $("#sSearch_5").val();
	
	//alert("trace_id: "+trace_id + " customer_name: " + customer_name + " customer_mob: " + customer_mob + "date_from: " + date_from + " date_to: "+ date_to);
	//return false;
	
	$.ajax({
		url: "<?php echo base_url().$this->router->fetch_module();?>/applicationlogs/exportexcel",
		async: false,
		type: "POST",
		dataType: 'json',
		data: {trace_id:trace_id, customer_name:customer_name, customer_mob:customer_mob, date_from:date_from, date_to:date_to, action_val:action_val },
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

$("body").on("keyup", "#sSearch_0", function(e) {
    var $th = $(this);
    if (
        e.keyCode != 46 &&
        e.keyCode != 8 &&
        e.keyCode != 37 &&
        e.keyCode != 38 &&
        e.keyCode != 39 &&
        e.keyCode != 40
    ) {
        $th.val(
            $th.val().replace(/[^0-9]/g, function(str) {
                return "";
            })
        );
    }
    return;
});
$('.fullname').keypress(function(e) {
    var regex = new RegExp(/^[a-zA-Z\s]+$/);
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    } else {
        e.preventDefault();
        return false;
    }
});

(function() {
    var input = document.getElementById('sSearch_2');
    var pattern = /^[6-9][0-9]{0,8}$/;
    var value = input.value;
    !pattern.test(value) && (input.value = value = '');
    input.addEventListener('input', function() {
        var currentValue = this.value;
        if(currentValue && !pattern.test(currentValue)) this.value = value;
        else value = currentValue;
    });
})();
document.title = "Application Logs";
</script>