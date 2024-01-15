<!-- Center Section -->
<style>
    ul.a {
        list-style-type: circle;
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
		.paging_full_numbers .last{
			position: absolute;
		}
	}
</style>
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Policy Sub Type - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">

					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Policy Subtype Name</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-2 col-12 txt-lr">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>policysubtype"><button class="btn cnl-btn">Clear Search</button></a>
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
					<?php if(in_array('InsurerAdd',$this->RolePermission)){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>policysubtype/addEdit">
								<button class="btn btn-sec add-btn fl-right">
                                <span class="partner">Add Policy Sub Type</span> 
                                    <span class="display-none-sm">
                                        <span class="material-icons spn-icon plus-icon">add_circle_outline</span>
                                    </span>
                                </button>
							</a>
						</div>
					<?php }?>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
						<tr>
							<th>Policy Sub Type Name</th>
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
                     <input type="hidden" class="form-control" id="policysubtypeid" name="policysubtypeid">
                     <input type="text" class="form-control" id="feature" name="feature" placeholder="Enter Feature...">
                 </div>
                 <div class="col-md-4 mt-4">
                     <button class="btn btn-success" type="button" onclick="addFeature()">Add</button>
                 </div>
             </div>

             <hr>
            <div id="dataFeature" class="col-md-12"></div>
         </div>
        </div>

    </div>
</div>
<script type="text/javascript">
$( document ).ready(function() {

});

function deleteData(id)
{
	var r=confirm("Are you sure you want to delete this record?");
	if (r==true)
	{
		$.ajax({
			url: "<?php echo base_url().$this->router->fetch_module();?>/policysubtype/delRecord/"+id,
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
function configureComparedata(id){
    $("#myModal").modal('show');
    $("#policysubtypeid").val(id);
    getDataFeatures(id);
}
function addFeature(){
   var policysubtypeid= $("#policysubtypeid").val();
   var feature= $("#feature").val();
    $.ajax({
        url: "<?php echo base_url();?>/policysubtype/addFeature",
        async: false,
        type: "POST",
        dataType: "json",
        data:{policysubtypeid,feature},
        success: function(data2){

            if(data2.status == 200)
            {
                displayMsg("success","Record has been Added!");
                $("#feature").val('');
                getDataFeatures(policysubtypeid);
            }
            else
            {
                displayMsg("error","Oops something went wrong!");
             //   setTimeout("location.reload(true);",1000);
            }
        }
    });
}
function getDataFeatures(id){
    $("#dataFeature").html('');
    $.ajax({
        url: "<?php echo base_url();?>/policysubtype/getFeatures",
        async: false,
        type: "POST",
        dataType: "json",
        data:{id},
        success: function(data2){

            if(data2.status == 200)
            {
                //data2.data
                var html='<ul class="a">';
                $.each(data2.data, function (i,data1) {
                    console.log(data1.feature);
                    html += '<li>'+data1.feature+'<button class="btn btn-link" type="button" onclick="deleteFeature('+ data1.id +')"><i class="ti-trash" style="color: red;"></i></button></li>'
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
function deleteFeature(id){
    var policysubtypeid= $("#policysubtypeid").val();
    $.ajax({
        url: "<?php echo base_url();?>/policysubtype/delRecordfeature/"+id,
        async: false,
        type: "POST",
        success: function(data2){
            data2 = $.trim(data2);
            if(data2 == "1")
            {
                displayMsg("success","Record has been Deleted!");
                getDataFeatures(policysubtypeid);

            }
            else
            {
                displayMsg("error","Oops something went wrong!");
            }
        }
    });
}
document.title = "Policy Sub Type";
</script>