<?php

	if($Data){

	?>
	<!-- Center Section -->
	<div class="col-md-10">
		<form id="editbranchimd">
			<input type="hidden" id="branch_imd_map_id" name="branch_imd_map_id" value="<?=encrypt_decrypt_password($Data[0]['branch_imd_map_id']); ?>" />
			<div class="content-section mt-3">
				<div class="card">
					<div class="cre-head">
						<div class="row">
							<div class="col-md-10 col-10">
								<p>Branch IMD Mapping</p>
							</div>
							<div class="col-md-2 col-2">
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-3 mb-3">
								<label for="policy_number" class="col-form-label">Master Policy No.<span class="lbl-star">*</span></label>
								<div class="dataTables_filter input-group">
									<input id="policy_number" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" value="<?=$Data[0]['policy_number']; ?>">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
								<span class="lbl-star error-msg d-none">Required</span>
							</div>
							<div class="col-md-3 mb-3">
								<label for="branch_code" class="col-form-label">Branch Code<span class="lbl-star">*</span></label>
								<div class="dataTables_filter input-group">
									<input id="branch_code" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" value="<?=$Data[0]['branch_code']; ?>">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
								<span class="lbl-star error-msg d-none">Required</span>
							</div>
							<div class="col-md-3 mb-3">
								<label for="imd_code" class="col-form-label">IMD Code<span class="lbl-star">*</span></label>
								<div class="dataTables_filter input-group">
									<input id="imd_code" type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" value="<?=$Data[0]['imd_code']; ?>">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
								<span class="lbl-star error-msg d-none">Required</span>
							</div>
							<?php /*<div class="col-md-3 mb-3">
							<label for="status" class="col-form-label">Status</label>
							<select class="form-control" name="isactive" id="isactive"> 
								<option value="" selected>Select Status</option>
								<option value="1">Active</option>
								<option value="0">In-Active</option>							
							</select>
						</div>*/ ?>
							<?php /*<div class="col-md-2 col-12 text-center">
							<label style="visibility: hidden;" class="mt-1">For Space</label>
							<a href="<?php echo base_url();?>users/importUser"><button class="btn cnl-btn fl-right">Import Mapping Codes</button></a>
						</div>*/ ?>

						</div>
						<div class="row mt-4">
							<div class="col-md-1 col-6 text-left">
								<a href="javascript:void(0);"><button type="submit" class="btn btn-success">Save</button></a>
							</div>
							<div class="col-md-2 col-6 text-right">
								<a href="javascript:void(0);"><button onclick="window.history.back();" class="btn btn-danger cancel">Cancel</button></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<script type="text/javascript">

		$('#editbranchimd').on('submit', function(){

			$("span.error-msg").addClass('d-none');

			hasError = 0;

			if($.trim($("#policy_number").val()) == ''){

				$("#policy_number").closest("div").next("span.error-msg").removeClass('d-none');
				hasError = 1;
			}
			if($.trim($("#branch_code").val()) == ''){

				$("#branch_code").closest("div").next("span.error-msg").removeClass('d-none');
				hasError = 1;
			}
			if($.trim($("#imd_code").val()) == ''){

				$("#imd_code").closest("div").next("span.error-msg").removeClass('d-none');
				hasError = 1;
			}

			if(!hasError){
				
				data = {};
				data.policy_number = $("#policy_number").val();
				data.branch_code = $("#branch_code").val();
				data.imd_code = $("#imd_code").val();
				data.branch_imd_map_id = $("#branch_imd_map_id").val();
				
				$.ajax({

					url: "<?php echo base_url(); ?>branchimd/updatebranchimd",
					type: 'post',
					dataType: 'json',
					data: data,
					cache: false,
					success: function(response) {

						if(response.success){

							displayMsg("success", response.msg);
						}
						else{

							displayMsg("error", response.msg);
						}
					}
				});
			}

			return false;
		});
	</script>
	<!-- Center Section End-->
	<?php
	
	}
?>