<!-- Center Section -->
<?php //echo "<pre>";print_r($_SESSION);exit;?>
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Enrollment Forms - <i class="ti-user"></i></p>
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Title</label>
						<div class="dataTables_filter input-group"style="float: none;">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					
					<div class="col-md-2 col-12 txt-lr">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>enrollmentforms"><button class="btn cnl-btn">Clear Search</button></a>
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
					<?php if(in_array('EnrollmentFormAdd',$this->RolePermission)){?>
					<?php if($_SESSION['webpanel']['role_id'] == 1){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>enrollmentforms/addEdit">
								<button class="btn btn-sec add-btn fl-right">Add Form <span class="display-none-sm"><span class="material-icons spn-icon">add_circle_outline</span></span></button>
							</a>
						</div>
					<?php }?>	
					<?php }?>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table" style="height:500px;">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead>
							<tr  class="tbl-cre">
								<th>Title</th>
								<th>Download Form</th>
								<?php if($_SESSION['webpanel']['role_id'] == 1){?>
									<th class="text-center">Actions</th>
								<?php }?>	
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
document.title = "Enrollment Forms";
</script>