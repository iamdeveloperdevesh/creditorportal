<style>
    /* @media screen and (max-width: 767px){ */
        .dynamicTable tbody:last-child td {
            white-space: nowrap;
        }
    /* } */


    @media (max-width:425px){
        .dataTables_paginate .paginate_button.next{
            position: absolute;
        }

        .dataTables_paginate .paginate_button.last{
            position: absolute;
            margin-left: 56px;
        }
    }

    @media (max-width:768px){
        .partner{
            position: relative;
            right: 8px;
        }

        .plus-icon{
            position: absolute;
            right: 7px;
        }
    }

    @media(max-width:425px){
		.paging_full_numbers span .paginate_button:nth-child(4){
			position: absolute;
    		right: -35px;
		}

		.paging_full_numbers span .paginate_button:nth-child(5){
			position: absolute;
    		right: -81px;
		}

        .paging_full_numbers span .paginate_button:nth-child(6){
            position: absolute;
            right: -124px;
    
		}

		.paging_full_numbers span .paginate_button:nth-child(7){
			position: absolute;
    		right: -172px;
		}

      

		.paging_full_numbers .next{
			position: absolute;
    		right: -239px;
		}

		.paging_full_numbers .last{
			position: absolute;
    		right: -301px;
		}

	}
</style>

<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Products - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="firstname" class="col-form-label">Product Name</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter Product name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-2 col-12 txt-lr">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>products"><button class="btn cnl-btn">Clear Search</button></a>
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
					<?php  if(in_array('ProductsAdd',$this->RolePermission)){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>products/AddNewView">
								<button class="btn btn-sec add-btn fl-right">
                                <span class="partner">Add Product</span> 
                                    <span class="display-none-sm">
                                        <span class="material-icons spn-icon plus-icon">add_circle_outline</span>
                                    </span>
                                </button>
							</a>
						</div>
					<?php  }?>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th>Product Name</th>
								<th>Partner</th>
								<th>Policy Type</th>
								<th>Status</th>
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
<div class=" modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-res">

        <div class="modal-content">

            <div class="modal-header header-title title  header-tl-xd">
                <h4>Configure Features</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <label>Feature</label>
                        <input type="hidden" class="form-control" id="plan_id" name="plan_id">
                        <input type="hidden" class="form-control" id="feature_id" name="feature_id">
                        <select class="form-control" id="policysubtype" name="policysubtype" onchange="getDataFeatures(this.value)"></select>
                    </div>

                </div>

                <hr>
                <div id="dataFeature" class="col-md-12"></div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade " id="APImodal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">API Request to generate Token</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12" id="apirequest"></div>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade " id="MappModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Map with new partner</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Select Partner</label>
                        <div class="input-group">
                            <input type="hidden" id="plan_id" name="plan_id">
                            <select class="form-control" name="creditor" id="creditor">
                                <option value="" selected disabled>Select Partner</option>
                                <?php foreach ($creditors as $creditor) { ?>
                                    <option value="<?php echo $creditor['creditor_id']; ?>"><?php echo $creditor['creaditor_name']; ?></option>
                                <?php } ?>
                            </select>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="button" onclick="mapwithpartner()">Save</button>
            </div>
        </div>

    </div>
</div>
<!-- Center Section End-->
<script>
$( document ).ready(function() {
	
});
function MappedModal(plan_id){
    $("#MappModal").modal('show');
    $("#plan_id").val(plan_id);
}
function deleteData(id)
{
	var r=confirm("Are you sure you want to delete this record?");
	if (r==true)
	{
		$.ajax({
			url: "<?php echo base_url().$this->router->fetch_module();?>/products/delRecord/"+id,
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
function configureComparedata(id,policy_subtype_id){
    $("#myModal").modal('show');
    $("#plan_id").val(id);
   getPolicysubtypeName(policy_subtype_id);
    $("#dataFeature").html('');
}
function getPolicysubtypeName(policy_subtype_id){
    $.ajax({
        url: "<?php echo base_url();?>/products/getpolicysubtypeName",
        async: false,
        type: "POST",
        dataType: "json",
        data:{policy_subtype_id},
        success: function(data2){

                var html='<option>Select</option>';
                $.each(data2.data, function (i,data1) {
                    html += '<option value="'+data1.policy_sub_type_id+'">'+data1.policy_sub_type_name+'</option>'
                });
                //html += '</option>';
                $("#policysubtype").html(html);

        }
    });
}
function getDataFeatures(id){

    $("#dataFeature").html('');
    var plan_id=  $("#plan_id").val();
    $.ajax({
        url: "<?php echo base_url();?>/products/getFeatures",
        async: false,
        type: "POST",
        dataType: "json",
        data:{id,plan_id},
        success: function(data2){

            if(data2.status == 200)
            {
            var feature_id=    data2.feature_id;
                arr=[];
            if(feature_id != null){
                arr=feature_id.split(',');
            }

                //data2.data
                var html='<ul class="a">';
                $.each(data2.data, function (i,data1) {
                    //console.log(arr);
                    if ($.inArray(data1.id, arr) != -1)
                    {
                        html += '<input type="checkbox" value="'+data1.id+'" checked id="features" name="features[]" onchange="updatepolicysubtyefeature()"> '+data1.feature+'<br>';
                    }else{
                        html += '<input type="checkbox" value="'+data1.id+'" id="features" name="features[]" onchange="updatepolicysubtyefeature()"> '+data1.feature+'<br>';
                    }

                });
                html += '</ul>';
                $("#dataFeature").html(html);
            }
            else
            {
                //displayMsg("error","Oops something went wrong!");
            }
        }
    });
}
function updatepolicysubtyefeature(){
  var plan_id=  $("#plan_id").val();
  var policysubtypeid=  $("#policysubtype").val();
 // var features=    $.trim($('input[name="features[]"]:checked').val());
    var checked = []
    $("input[name='features[]']:checked").each(function ()
    {
        checked.push(parseInt($(this).val()));
    });
    $.ajax({
        url: "<?php echo base_url();?>/products/updatepolicysubtyefeature",
        async: false,
        type: "POST",
        dataType: "json",
        data:{plan_id,checked,policysubtypeid},
        success: function(data2){

            var html='<option>Select</option>';
            $.each(data2.data, function (i,data1) {
                html += '<option value="'+data1.policy_sub_type_id+'">'+data1.policy_sub_type_name+'</option>'
            });
            //html += '</option>';
            $("#policysubtype").html(html);

        }
    });
}
function openModalAPIrequest(plan_id){
    $('#APImodal').modal('show');

    $.ajax({
        url: "<?php echo base_url();?>/products/getProductIDapi",
        async: false,
        type: "POST",
        dataType: "json",
        data:{plan_id},
        success: function(data2){

            $('#apirequest').html(data2.api);
          
        }
    });
 
}
function mapwithpartner(){
    var creditor=$("#creditor").val();
    var plan_id=$("#plan_id").val();
    $.ajax({
        url: "<?php echo base_url();?>/products/mappedWithNewPartner",
        async: false,
        type: "POST",
        dataType: "json",
        data:{creditor,plan_id},
        success: function(){

            displayMsg("success","Mapped with new partner Successfully!");
            setTimeout("location.reload(true);",1000);
        }
    });
}
document.title = "Product List";
</script>