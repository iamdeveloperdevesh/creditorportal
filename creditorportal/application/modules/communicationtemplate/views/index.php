<style>
	table.display tbody:last-child td{
		white-space: nowrap;
	}
</style>

<!-- Center Section -->
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Communication Template - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Name</label>
						<div class="dataTables_filter input-group"style="float: none;">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Enter Partner Name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
                    
					
					<div class="col-md-2 col-12 text-center">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>communicationtemplate"><button class="btn cnl-btn fl-right">Clear Search</button></a>
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
					<?php if(in_array('CommunicationTemplateAdd',$this->RolePermission)){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>communicationtemplate/addEdit">
								<button class="btn btn-sec add-btn fl-right">Add Template <span class="display-none-sm"><span class="material-icons spn-icon">add_circle_outline</span></span></button>
							</a>
						</div>
					<?php }?>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table" style="height:500px;">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead>
							<tr  class="tbl-cre">
								<th>Partner Name</th>
								<th>Event</th>
								<th>Subject</th>
								<th>Content</th>
								<th>Type</th>
								<!--<th>URL(Gadget insurance)</th>-->
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
$( document ).ready(function() {
	
});
function deleteData(id)
{
	var r=confirm("Are you sure you want to delete this record?");
	if (r==true)
	{
		$.ajax({
			url: "<?php echo base_url().$this->router->fetch_module();?>/delRecord/"+id,
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false,
			beforeSubmit : function(arr, $form, options){
				//$(".btn-primary").hide();
				//return false;
			},
			success: function(response){
				if(response.success)
				{
					displayMsg("success",response.msg);
					setTimeout(function(){
						window.location = "<?php echo base_url();?>communicationtemplate";
					},2000);

				}
				else
				{	
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	}
}
document.title = "Communication Template";
</script>