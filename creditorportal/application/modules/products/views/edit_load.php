<?php
//print_r($datalist);

//echo $datalist->details[0]->premium_type;

//echo $datalist->plan_id;

if (isset($datalist->plan_id)) {
	// echo "<pre>";print_r($datalist);
	//echo $datalist
	//echo $datalist->details[0]->policy_number;
}
?>
<style>
	.select2-container-multi .select2-choices .select2-search-field input {
		padding: 0px !important;
		margin: 1px 0 !important;
		font-family: sans-serif;
		font-size: 100%;
		color: #666;
		outline: 0;
		border: 0;
		-webkit-box-shadow: none;
		box-shadow: none;
		background: transparent !important;
	}

	.agefield.error{
		position: relative;

	}
	.adult-row .col-md-6 .form-group{
		display: inline-block;
	}
	.children-row .col-md-6 .form-group{
		display: inline-block;
	}

	.adult-row .col-md-6 label.error{
		position: relative;
		top:0;
		display: block;
	}

	.children-row .col-md-6 label.error{
		position: relative;
		top:0;
		display: block;
	}


	.error {
		color: red;
	}

	.label-primary {
		padding: 5px;
	}

	.collapse.in {
		display: block;
	}

	.collapse {
		display: none;
		padding: 15px;
	}

	.card .card-header {
		background-color: #e6e6e6;
		padding: 5px;
		/* margin-top: 15px; */
	}

	.select2-container-multi .select2-choices {
		background-image: none !important;
		border: 0 !important;
	}

	.agefield {
		margin-left: 15px;
		width: 120px;
		float: left;
	}

	
</style>

<div id="accordion3" class="according accordion-s2 mt-3">
	<div class="card card-member">
		<div class="card-header card-vif">
			<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion44" aria-expanded="false"> <span class="lbl-card"><?php if (!isset($policyview)) echo 'Edit';?> Plan Details - <i class="ti-file"></i></a>
		</div>
		<div id="accordion44" class="card-vis-mar collapse show" data-parent="#accordion2" style="">

			<?php //echo $datalist->plan_id;
			?>

			<?php
			//if(!isset($datalist->plan_id)) { 
			if (!isset($policyview)) {
			?>
				<!-- Start 1 -->
				<form class="form-horizontal" id="form-plan" method="post" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" name="plan_id" value="<?php echo $datalist->plan_id; ?>">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Select Partner</label>
								<div class="input-group">
									<select class="form-control" name="creditor">
										<?php foreach ($datalist->creditors as $creditor) { ?>
											<option value="<?php echo $creditor->creditor_id; ?>" <?php if ($datalist->details[0]->creditor_id == $creditor->creditor_id) {
																										echo "selected";
																									} ?>><?php echo $creditor->creaditor_name; ?></option>
										<?php } ?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Select Policy Type</label>
								<div class="input-group">
									<select class="select2 form-control" name="policy_sub_type[]" multiple="multiple">
										<?php foreach ($datalist->policysubtypes as $subtype) { ?>
											<option value="<?php echo $subtype->policy_sub_type_id; ?>" <?php if (in_array($subtype->policy_sub_type_id, $subtypes)) echo "selected"; ?>><?php echo $subtype->policy_sub_type_name; ?></option>
										<?php } ?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Plan Name <span class="lbl-star">*</span></label>
								<div class="input-group">
									<input type="text" name="plan_name" value="<?php echo $datalist->details[0]->plan_name; ?>" class="form-control" aria-describedby="inputGroupPrepend" required="">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
									<div class="error"><span class="planerror"></span></div>
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Payment Modes Applicable</label>
								<div class="input-group">
									<select class="select2 form-control" name="payment_modes[]" multiple="multiple">
										<option value="">Select Payment Modes Applicable</option>
										<?php foreach ($datalist->payment_modes as $mode) { ?>
											<option value="<?php echo $mode->payment_mode_id; ?>" <?php if (in_array($mode->payment_mode_id, $planpay)) echo "selected"; ?>><?php echo $mode->payment_mode_name; ?></option>
										<?php } ?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
						</div>

						<div class="row mt-4" id="addplanbtn">
							<div class="col-md-1 col-6 text-left">
								<!-- <button class="btn smt-btn">Save</button> -->
								<a href="javascript:void(0)" class="btn smt-btn" id="continuebtn">Countinue</a>
							</div>

							<div class="col-md-2 col-6 text-right">
								<!-- <button class="btn cnl-btn">Cancel</button> -->
								<button class="btn smt-btn">Save</button>
							</div>
						</div>
					</div>
				</form>
			<?php } else { ?>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Plan Name </label>
							<div class="input-group inp-frame2">
								<?php
								echo $datalist->details[0]->plan_name;

								?>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Policy Sub Types</label>
							<div class="input-group">
								<?php foreach ($datalist->details as $detail) { ?>
									<label class="label label-primary  inp-frame2"><?php echo $detail->policy_sub_type_name; ?> </label><br>
								<?php } ?>
							</div>
						</div>

						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Partner Name</label>
							<div class="input-group">
								<label class="label label-primary  inp-frame2"><?php echo $datalist->details[0]->creditor_name; ?></label>
							</div>
						</div>
					</div>
				</div>
				<?php // } 
				?>
				<!-- end 1 -->
		</div>
	</div>
</div>

<!-- Policy details section -->
<div id="accordion3" class="according accordion-s2 mt-3">
	<div class="card card-member">
		<div class="card-header card-vif">
			<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion45" aria-expanded="false">
				<span class="lbl-card">Policy Details - <i class="ti-file"></i></a>
		</div>
		<div id="accordion45" class="card-vis-mar" data-parent="#accordion2" style="">
			<form class="form-horizontal" id="form-policy" method="post" enctype="multipart/form-data">
				<div class="card-body">
					<div class="row col-md-12">
						<div class="col-md-4 mb-3">
						<input type="hidden" id="policy_type_id" value="<?php echo $datalist->details[0]->policy_type_id; ?>">
						<input type="hidden" id="policy_subtype_idNew" name="policy_subtype_idNew"value="<?php echo $datalist->details[0]->policy_sub_type_id; ?>">
							<label for="validationCustomUsername" class="col-form-label">Policy Sub Type</label>
							<div class="input-group">
								<select id="policySubType" name="policySubType" class="form-control" required>
									<option value="">Select Policy Sub Type</option>
									<?php
									$combocount = 0;
									$mandate = 0;
									$inactivecount = 0;
									foreach ($datalist->details as $detail) {
										if ($detail->is_combo == 1) {
											$combocount++;
										}
										if ($detail->is_optional == 0) {
											$mandate++;
										}
									?>
										<option value="<?php echo $detail->policy_id; ?>" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->policy_id == $detail->policy_id) {
																								echo "selected";
																							} ?>><?php echo $detail->policy_sub_type_name; ?></option>
									<?php } ?>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Master Policy Number<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input class="form-control policyno" placeholder="Enter Master Policy Number" type="text" data-id="<?php echo $detail->policy_id; ?>" value="<?php if (isset($datalist->policydetails)) {
									echo $datalist->policydetails[0]->policy_number;
									} ?>" id="policyNo" name="policyNo">

								<div class="error"><span class="policyerror"></span></div>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>

						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Insurer<span class="lbl-star">*</span></label>
							<div class="form-group input-group">
								<select id="masterInsurance" name="masterInsurance" class="form-control custom-select">
									<option value="">Select Insurer</option>
									<?php foreach ($datalist->insurers as $insurer) { ?>
										<option value="<?php echo $insurer->insurer_id; ?>" <?php
																							if(isset($datalist->policydetails) && $datalist->policydetails[0]->insurer_id == $insurer->insurer_id){
																							// if ($insurer->insurer_id == 1) {
																								echo "selected";
																							}
																							?>><?php echo $insurer->insurer_name; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Plan Code<span class="lbl-star">*</span></label>
							<div class="input-group">
								<input type="text" id="plan_code" name="plan_code" class="form-control" placeholder="Enter Plan Code" aria-describedby="inputGroupPrepend" value="<?php if (isset($datalist->policydetails)) {
																																															echo $datalist->policydetails[0]->plan_code;
																																														} ?>"" >
								<div class=" error"><span class="policyerror"></span>
							</div>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Product Code<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input type="text" id="product_code" name="product_code" class="form-control" placeholder="Enter Product Code" aria-describedby="inputGroupPrepend" value="<?php if (isset($datalist->policydetails)) {
																																																echo $datalist->policydetails[0]->product_code;
																																															} ?>">
							<div class="error"><span class="policyerror"></span></div>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Scheme Code</label>
						<div class="input-group">
							<input type="text" id="scheme_code" name="scheme_code" class="form-control" placeholder="Enter Scheme Code" aria-describedby="inputGroupPrepend" value="<?php if (isset($datalist->policydetails)) {
																																														echo $datalist->policydetails[0]->scheme_code;
																																													} ?>">
							<div class="error"><span class="policyerror"></span></div>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Source Name</label>
						<div class="input-group">
							<input type="text" id="source_name" name="source_name" class="form-control" placeholder="Enter Source Name" aria-describedby="inputGroupPrepend"value="<?php if (isset($datalist->policydetails)) {
																																														echo $datalist->policydetails[0]->source_name;
																																													} ?>">
							<div class="error"><span class="policyerror"></span></div>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
							</div>
						</div>
					</div>
					 <?php
/*                        if($datalist->payment_mode_id == 4){ */?><!--
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">CD Balance</label>
                                <div class="input-group">
                                    <input type="number" id="cd_balance" name="cd_balance" class="form-control" placeholder="Enter CD Balance" value="<?php /*if (isset($datalist->policydetails)) {
																																														echo $datalist->policydetails[0]->cd_balance;
																																													} */?>" aria-describedby="inputGroupPrepend">
                                    <div class="error"><span class="policyerror"></span></div>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Threshold</label>
                                <div class="input-group">
                                    <input type="number" id="threshold" name="threshold" class="form-control" placeholder="Enter Threshold" value="<?php /*if (isset($datalist->policydetails)) {
																																														echo $datalist->policydetails[0]->threshold;
																																													} */?>"aria-describedby="inputGroupPrepend">
                                    <div class="error"><span class="policyerror"></span></div>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
                                    </div>
                                </div>
                            </div>
                        --><?php /*}
                        */?>

					<div class="col-md-4 mb-3 ml-1 row">
						<?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->is_optional == 0) {
							$mandatory_checked = 'checked';
							$optional_checked = '';
						} else {
							$mandatory_checked = '';
							$optional_checked = 'checked';
						}
						?>
						<label style="visibility: hidden;" class="display-sm-lbl col-md-12">space</label>
						<div class="form-check-inline custom-control custom-checkbox">
							<input type="radio" class="custom-control-input" name="mandatory" value="1" id="mandatory_option" <?php echo $mandatory_checked ?>>
							<label class="custom-control-label" for="mandatory_option"> Mandatory </label>
				      	</div>
							<div class="form-check-inline custom-control custom-checkbox">
							<?php if(count($datalist->details)>1){ ?>	
							<input type="radio" class="custom-control-input" name="mandatory" value="0" id="optional_option" <?php echo $optional_checked ?>>
							<label class="custom-control-label" for="optional_option"> Optional </label>
						<?php }?>
						</div>
						<div class="col-md-1">
						<div class="form-check custom-control custom-checkbox" id="combo_flag">
						<?php if(count($datalist->details)>1){ ?>
						 <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->is_combo == 1) {echo "checked";
						} ?>> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> 
					<?php }?>
					</div></div>
					</div>

					<div class="col-md-4">
						<?php if (count($datalist->details) > 1) : ?>
							<div style="display: none;" id="mandatory_if_not_selected_section">
								<label for="mandatoryIfSelectedRules" class="col-form-label">Mandatory If Not Selected </label>
								<select id="mandatory_if_not_selected" name="mandatory_if_not_selected[]" class="select2 form-control" multiple="multiple">

								</select>
							</div>
						<?php endif; ?>
					</div>
					<input type="hidden" name="coi_type" id="coi_type" value="<?php echo $datalist->details[0]->coi_type; ?>">
					<?php if($datalist->details[0]->coi_type == 1 ){ ?>
					    	<div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">COI counter start<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="start_series" name="start_series" class="form-control" placeholder="Enter COI counter start" value="<?php if (isset($datalist->policydetails)) {
																																														echo $datalist->policydetails[0]->start_series_number;
																																													} ?>" aria-describedby="inputGroupPrepend">
                                    <div class="error"><span class="policyerror"></span></div>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">COI counter end<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="end_series" name="end_series" class="form-control" placeholder="Enter COI counter end" value="<?php if (isset($datalist->policydetails)) {
																																														echo $datalist->policydetails[0]->end_series_number;
																																													} ?>" aria-describedby="inputGroupPrepend">
                                    <div class="error"><span class="policyerror"></span></div>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
                                    </div>
                                </div>
                            </div>
					<?php }
                    if($datalist->details[0]->policy_type_id == 3){ ?>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Excess</label>
                            <div class="input-group">
     <textarea class="form-control imgDatepicker "  id="excess" name="excess" autocomplete="off" ><?php if (isset($datalist->policydetails)) {
	echo $datalist->policydetails[0]->excess;} ?></textarea>

                </div>
                </div>
                    <?php
}
 ?>


					<!--
					 <div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">PDF type</label>
						<div class="input-group">
							 <select id="pdf_type" name="pdf_type" class="form-control">
                                            <option value="">Select</option>
                                             <option value="1" <?php //if(isset($datalist->policydetails) && $datalist->policydetails[0]->pdf_type == 1){echo "selected"; } 
																?>>I</option>
                                            <option value="2" <?php //if(isset($datalist->policydetails) && $datalist->policydetails[0]->pdf_type == 2){echo "selected"; } 
																?>>C1</option>
                                            <option value="3" <?php //if(isset($datalist->policydetails) && $datalist->policydetails[0]->pdf_type == 3){echo "selected"; } 
																?>>C2</option>
                              </select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
					-->

					<!-- 
					 <div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Premium Type</label>
						<div class="input-group">
							<select id="premium_type" name="premium_type" class="form-control">
								<option value="1" <?php //if(isset($datalist->policydetails) && $datalist->policydetails[0]->premium_type == 1){echo "selected"; } 
													?>>Absolute</option>
                                <option value="0" <?php //if(isset($datalist->policydetails) && $datalist->policydetails[0]->premium_type == 0){echo "selected"; } 
													?>>Per Mile rate</option>
                            </select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
					-->


				</div>
				<div class="row col-md-12">
					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Policy Start Date</label>
						<div class="input-group">
							<input class="form-control imgDatepicker datepicker" type="text" id="policyStartDate" placeholder="Enter Master Policy Start Date" name="policyStartDate" autocomplete="off" onkeydown="return false" value="<?php if (isset($datalist->policydetails) && !empty($datalist->policydetails[0]->policy_start_date)) {
																																										echo date('d-m-Y', strtotime($datalist->policydetails[0]->policy_start_date));
																																									}

																																									?>">

							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Policy end Date</label>
						<div class="input-group">
							<input class="form-control imgDatepicker datepicker" type="text" id="policyEndDate" placeholder="Enter Master Policy End Date"  name="policyEndDate" autocomplete="off" onkeydown="return false" value="<?php if (isset($datalist->policydetails) && !empty($datalist->policydetails[0]->policy_end_date)) {
																																									echo date('d-m-Y', strtotime($datalist->policydetails[0]->policy_end_date));
																																								} ?>">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
					 <?php
                    if($datalist->details[0]->policy_type_id == 3){ ?>
                        <div class="col-md-4 mb-3 " >
                            <label for="validationCustomUsername" class="col-form-label">Back days allowed</label>
                            <div class="input-group">
                                <input type="text" id="gadget_eligibilty"  name="gadget_eligibilty" value="<?php if (isset($datalist->policydetails) && !empty($datalist->policydetails[0]->gadget_eligibilty)) {
																																									echo $datalist->policydetails[0]->gadget_eligibilty;
																																								} ?>" class="form-control" >
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                </div>
                            </div>
                        </div>
                    <?php      }else{

                    }?>
				</div>

<?php
                  $arr=array(3,4,5);
                  $arr1 = array(5);
                    if(in_array($datalist->details[0]->policy_type_id,$arr)){

                    }else{
                        $this->load->view('member_info') ;
                    }
                    //all risk
                                   if(in_array($datalist->details[0]->policy_type_id,$arr1)){
                    ?>
          <!-- all risk -->

 				<div class="row col-md-12">
<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Policy Tenure Start Date<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control imgDatepicker datepicker" type="text" required id="policyTenureStartDate" name="policyTenureStartDate" autocomplete="off" onkeydown="return false" value="<?php if (isset($datalist->policydetails) && !empty($datalist->policydetails[0]->policy_tenure_start_date)) {
																																									echo $datalist->policydetails[0]->policy_tenure_start_date;
																																								} ?>">

							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Policy Tenure  end Date<span class="lbl-star">*</span></label>
						<div class="input-group">
							<input class="form-control imgDatepicker datepicker" type="text" required id="policyTenureEndDate" name="policyTenureEndDate" autocomplete="off" onkeydown="return false" value="<?php if (isset($datalist->policydetails) && !empty($datalist->policydetails[0]->policy_tenure_end_date)) {
																																									echo $datalist->policydetails[0]->policy_tenure_end_date;
																																								} ?>">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

							</div>
							<div class="row col-md-12">
				
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Coverage Type<span class="lbl-star">*</span></label>
								<div class="input-group">
									<select class="select2 form-control" id = "coverage_type" name="coverage_type" required>
										<option value="">Select Coverage Type </option>
										<option value="single"<?php if(isset($datalist->policydetails) && $datalist->policydetails[0]->coverage_type == 'single'){echo "selected"; }?>>Single </option>
										<option value="multiple"<?php if(isset($datalist->policydetails) && $datalist->policydetails[0]->coverage_type == 'multiple'){echo "selected"; }?>>Multiple </option>
									</select>
									<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
								</div>
								</div>
							</div>
							<div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label"></label>
	<div class="input-group">
                                <button type = "button" class="btn smt-btn add_more" style ="display:<?php if(isset($datalist->policydetails) && $datalist->policydetails[0]->coverage_type == 'multiple'){echo "block"; }else{echo "none";}?>" onclick="add_fields();">Add More</button>
								</div>
							</div>

							</div>

							<div class="row col-md-12 covers_details" id = "cover_field" style ="display:<?php if(isset($datalist->policydetails) && $datalist->policydetails[0]->coverage_type != '')
{ echo 'block';}else{echo 'none';}
?>">


							<hr>
<?php if(isset($datalist->policydetails) && $datalist->policydetails[0]->coverage_type != '')
{
    foreach($datalist->coverage_details as $key => $coverage)
        {

            ?>
            <div class="row col-md-12">
            <div class="col-md-4 mb-3">
            <label for="validationCustomUsername" class="col-form-label">Coverage Name <?php if($datalist->policydetails[0]->coverage_type == 'multiple'){echo $key+1;}?><span class="lbl-star">*</span></label>
            <div class="input-group">
            <input type="text" id="coverage_name_0" name= cover_det[<?php echo $key+1;?>][cover_name] class="form-control" required placeholder="Enter Cover Name" aria-describedby="inputGroupPrepend"value="<?php echo $coverage->coverage_name;?>">
            <div class="error"><span class="policyerror"></span></div>
            <div class="input-group-prepend">
             <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
            </div>
            </div>
            </div>
            <?php if($datalist->policydetails[0]->coverage_type == 'multiple'){?>
            <div class="col-md-4 mb-3">
            <label for="validationCustomUsername" class="col-form-label">Coverage Rate Type <?php if($datalist->policydetails[0]->coverage_type == 'multiple'){echo $key+1;}?><span class="lbl-star">*</span></label>
            <div class="input-group">
            <select class="select2 form-control " required id = "premium_rate_type_0" name= cover_det[<?php echo $key+1;?>][coverage_rate_type] ><option value="">Select Coverage Rate Type</option><option value="flat" <?php if(isset($datalist->coverage_details) && $coverage->coverage_type == 'flat'){echo "selected"; }?>>Flat </option><option value="percentage"<?php if(isset($datalist->coverage_details) && $coverage->coverage_type == 'percentage'){echo "selected"; }?>>Percentage </option></select>
            </div>
            </div>
            <div class="col-md-4 mb-3">
            <label for="validationCustomUsername" class="col-form-label">Coverage Rate <?php if($datalist->policydetails[0]->coverage_type == 'multiple'){echo $key+1;}?><span class="lbl-star">*</span></label>
            <div class="input-group">
            <input type="text" id="coverage_rate_0" required name= cover_det[<?php echo $key+1;?>][coverage_rate] class="form-control premium_rate" placeholder="Enter Coverage Rate" aria-describedby="inputGroupPrepend"value="<?php echo $coverage->coverage_rate;?>">
            <div class="error"><span class="policyerror"></span></div>
            <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
            </div>
            </div>
            </div>
            <?php }
            ?>
            <div class="col-md-4 mb-3">
            <label for="validationCustomUsername" class="col-form-label">Premium Rate Type <?php if($datalist->policydetails[0]->coverage_type == 'multiple'){echo $key+1;}?><span class="lbl-star">*</span></label>
            <div class="input-group">
            <select class="select2 form-control" id = "premium_rate_type_0" name= cover_det[1][premium_rate_type] required><option value="">Select Premium Rate Type</option><option value="flat" <?php if(isset($datalist->coverage_details) && $coverage->premium_type == 'flat'){echo "selected"; }?>>Flat </option><option value="percentage" <?php if(isset($datalist->coverage_details) && $coverage->premium_type == 'percentage'){echo "selected"; }?>>Percentage </option></select>
                </div>
            </div>
            <div class="col-md-4 mb-3">
            <label for="validationCustomUsername" class="col-form-label">Premium Rate<?php if($datalist->policydetails[0]->coverage_type == 'multiple'){echo $key+1;}?><span class="lbl-star">*</span></label>
            <div class="input-group">
            <input type="text" id="premium_rate_0" name= cover_det[<?php echo $key+1;?>][premium_rate] class="form-control premium_rate" placeholder="Enter Premium Rate" aria-describedby="inputGroupPrepend"value="<?php echo $coverage->premium_rate;?>" required>
            <div class="error"><span class="policyerror"></span></div>
             <div class="input-group-prepend">
             <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>
            </div>
            </div>
            </div><div class="col-md-4 mb-3"></div>
            </div>
            <?php
        }
    ?>

    <input type="hidden" id="coverage_key" name= "coverage_key" class="form-control" placeholder="Enter Premium Rate" aria-describedby="inputGroupPrepend"value="<?php echo $key+1;?>">
<?php
}
?>
							</div>
							<?php
}
 ?>



				<div class="row col-md-12">
				 <?php
                       if(in_array($datalist->details[0]->policy_type_id,$arr)){

                        }else{ ?>
					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Sum Insured Type<span class="lbl-star">*</span></label>
						<div class="input-group">
							<?php
							// print "<pre>";
							// print_r($datalist->si_type);
							?>
							<select id="sum_insured_type" name="sum_insured_type" class="form-control">
								<option value=""> Select Sum Insured Type </option>
								<?php foreach ($datalist->sitypes as $type) { ?>
									<option value="<?php echo $type->suminsured_type_id; ?>" <?php if (!empty($datalist->si_type[0]->suminsured_type_id) && ($datalist->si_type[0]->suminsured_type_id == $type->suminsured_type_id)) {
																									echo "selected";
																								} ?>>
										<?php echo $type->suminsured_type; ?>
									</option>
								<?php } ?>
							</select>


							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
					    <?php  }

                    if($datalist->details[0]->policy_type_id == 3){ ?>

                            <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Business Type</label>
                            <div class="input-group">
     <input class="form-control imgDatepicker datepicker" type="text" id="b2b_type" name="business_type" autocomplete="off" value="B2B">

                </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustomUsername" class="col-form-label">Per Sending Limit</label>
                    <div class="input-group">

                        <input class="form-control imgDatepicker datepicker" type="text" id="per_sending_limit" name="per_sending_limit" autocomplete="off" value="<?php if (isset($datalist->details[0])) {
                            echo $datalist->details[0]->per_sending_limit;
                        } ?>">

                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustomUsername" class="col-form-label">Per Location Limit</label>
                    <div class="input-group">

                        <input class="form-control imgDatepicker datepicker" type="text" id="per_location_limit" name="per_location_limit" autocomplete="off" value="<?php if (isset($datalist->details[0])) {
                            echo $datalist->details[0]->per_location_limit;
                        } ?>">

                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                        </div>
                    </div>
                </div>


                                        <div class="col-md-4 mb-3">
                            <div class="input-group">
     <input class="form-control imgDatepicker datepicker" type="text" id="b2c_type" name="b2c_type" autocomplete="off" value="B2C">

                </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="input-group">

                        <input class="form-control imgDatepicker datepicker" type="text" id="per_sending_limit_b2c" name="per_sending_limit_b2c" autocomplete="off" value="<?php if (isset($datalist->details[0])) {
                            echo $datalist->details[0]->per_sending_limit_b2c;
                        } ?>">

                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="input-group">

                        <input class="form-control imgDatepicker datepicker" type="text" id="per_location_limit_b2c" name="per_location_limit_b2c" autocomplete="off" value="<?php if (isset($datalist->details[0])) {
                            echo $datalist->details[0]->per_location_limit_b2c;
                        } ?>">

                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                        </div>
                    </div>
                </div>

<?php
                    }
						  if(!in_array($datalist->details[0]->policy_type_id,$arr1)){

                        ?>


                               <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Cover  Limit</label>
                            <div class="input-group">

                                <input class="form-control imgDatepicker datepicker" placeholder="Enter Cover Limit" type="text" id="cover_initial" name="cover_initial" autocomplete="off" value="<?php if (isset($datalist->details[0]->initial_cover)) {
                                    echo $datalist->details[0]->initial_cover;
                                } ?>">

                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Cover Threshold</label>
                                <div class="input-group">
                                    <input class="form-control imgDatepicker datepicker" placeholder="Enter Cover Threshold" type="text" id="cover_limit" name="cover_limit" autocomplete="off" value="<?php if (isset($datalist->details[0]->cover_limit)) {
                                        echo $datalist->details[0]->cover_limit;
                                    } ?>">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>
					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Rater<span class="lbl-star">*</span></label>
						<div class="input-group">
							<?php
							//print "<pre>";
							//print_r($datalist->premium_basis[0]); 
							?>
							<select id="companySubTypePolicy" name="companySubTypePolicy" onchange="showDef(this.value)" class="form-control valid" aria-invalid="false">

								<option value="">Select Rater</option>
								<?php foreach ($datalist->sipremiumbasis as $premium) { ?>
									<option value="<?php echo $premium->si_premium_basis_id; ?>" <?php if (!empty($datalist->premium_basis[0]->si_premium_basis_id) && ($datalist->premium_basis[0]->si_premium_basis_id == $premium->si_premium_basis_id)) {
																										echo "selected";
																									} ?>> <?php echo $premium->si_premium_basis; ?> </option>
								<?php } ?>
								<!-- <option value="5">Absolute</option>
					<option value="6">Per Mile Rate</option> -->
							</select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>

					</div>
					<?php
					//echo $datalist->premium_basis[0]->si_premium_basis_id;exit;
					if($datalist->premium_basis[0]->si_premium_basis_id == 5){ ?>
					    <div class="col-md-4 mb-3 default_sumInsuredDiv" id="default_sumInsuredDiv" >
						<label for="validationCustomUsername" class="col-form-label">Default Sum Insured</label>
						<div class="input-group">
                            <input type="number" id="default_sumInsured" value="<?php echo $datalist->policy_premium[0]->sum_insured; ?>"  name="default_sumInsured" class="form-control" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
				<?php 	} else{ ?>
					  <div class="col-md-4 mb-3 default_sumInsuredDiv" id="default_sumInsuredDiv" style="display: none">
						<label for="validationCustomUsername" class="col-form-label">Default Sum Insured</label>
						<div class="input-group">
                            <input type="number" id="default_sumInsured"  name="default_sumInsured" class="form-control" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
			<?php	}
					?>
					<?php
					//echo $datalist->premium_basis[0]->si_premium_basis_id;exit;
					if($datalist->premium_basis[0]->si_premium_basis_id == 5){ ?>
					    <?php
                        if($datalist->details[0]->policy_type_id == 3){

                            ?>
                            <div class="col-md-4 mb-3 default_sumInsuredDiv" style="display: block">
                                <label for="validationCustomUsername" class="col-form-label">Rate</label>
                                <div class="input-group">
                                    <input type="text" id="default_rate" value="<?php echo $datalist->rate; ?>"  name="default_rate" class="form-control" >
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                    </div>
                                </div>
                            </div>
                  <?php      }else{

                        } 	} else{ ?>
                        <?php
                        if($datalist->details[0]->policy_type_id == 3){

                            ?>
					    <div class="col-md-4 mb-3 default_sumInsuredDiv" style="display: none">
                                <label for="validationCustomUsername" class="col-form-label">Rate</label>
                                <div class="input-group">
                                    <input type="text" id="default_rate" value="<?php echo $datalist->rate; ?>"  name="default_rate" class="form-control" >
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                    </div>
                                </div>
                            </div>

			     <?php  	} } ?>


				</div>


				<!-- flat -->

				<?php
				//echo "<pre>";
				//print_r($datalist); 
				?>

				<div class="col-md-12" id="add_si_tbody" style="<?php if (!empty($datalist->policy_premium) && ($datalist->premium_basis[0]->si_premium_basis_id == 1)) {
																	echo "display: block";
																} else {
																	echo "display: none";
																} ?> ">

					<?php if (!empty($datalist->policy_premium) && ($datalist->premium_basis[0]->si_premium_basis_id == 1) && !empty($datalist->policy_premium)) {
						$i = 1;
						foreach ($datalist->policy_premium as $premium) {
					?>
							<div class="row lbl-body mt-1">
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Sum Insured</label>
									<div class="input-group">
										<input type="number" class="form-control sum_insured_opt" placeholder="Enter Sum Insured" value="<?php if (isset($premium->sum_insured)) echo $premium->sum_insured;  ?>" name="sum_insured_opt1[]" autocomplete="off" required min="1"> 
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Premium rate</label>
									<div class="input-group">
										<input type="number" placeholder="Enter Premium" class="form-control premium_opt" value="<?php
																												if (isset($premium->premium_with_tax) && !$premium->is_taxable) {
																													echo $premium->premium_with_tax;
																												} else {
																													echo $premium->premium_rate;
																												}  ?>" name="premium_opt[]" autocomplete="off" step="0.01" min="0.01" required>
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<?php
                        if($datalist->details[0]->policy_type_id == 3){

                        }else{ ?>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Group Code</label>
									<div class="input-group">
										<input type="text" placeholder="Enter Group Code" class="form-control" name="group_code[]" autocomplete="off" value="<?php if (isset($premium->group_code)) echo $premium->group_code; ?>">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label"> Group Code For Spouse</label>
									<div class="input-group">
										<input type="text" placeholder="Enter Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off" value="<?php if (isset($premium->group_code_spouse)) echo $premium->group_code_spouse; ?>">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<?php } ?>
								<div class="col-md-2 mb-3 col-12">
									<!-- <label style="visibility: hidden;" class="mt-3">label</label> -->

									<div class="custom-control custom-checkbox form-check-inline">
										<input type="checkbox" class="taxchk" <?php if ($premium->is_taxable == 1) echo "checked";  ?> autocomplete="off">
										<input type="hidden" class="tax_opt" name="tax_opt[]" value="<?php echo $premium->is_taxable; ?>" />
										<label for="istax">Is Taxable</label>
									</div>
								</div>

								<?php if ($i == 1) { ?>
									<div class="col-md-1 mb-3 col-12 text-right">
										<!-- <label style="visibility: hidden;" class="mt-2">label</label> -->
										<button class="btn add-btn" id="btn_add_si_flat" type="button">Add <i class="ti-plus"></i></button>
									</div>
								<?php } else { ?>
									<div class="del_btn_opt"><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></div>
								<?php } ?>
							</div>
						<?php $i++;
						}
					} else { ?>
						<div class="row lbl-body">
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label"> Sum Insured</label>
								<div class="input-group">
									<input type="number" placeholder="Enter Sum Insured" class="form-control sum_insured_opt" name="sum_insured_opt1[]" autocomplete="off" min="1">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label">Premium rate</label>
								<div class="input-group">
									<input type="number" placeholder="Enter Premium" class="form-control premium_opt" name="premium_opt[]" autocomplete="off" step="0.01" min="0.01">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							<?php
                        if($datalist->details[0]->policy_type_id == 3){

                        }else{ ?>
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label">Group Code</label>
								<div class="input-group">
									<input type="text" placeholder="Enter Group Code" class="form-control" name="group_code[]" autocomplete="off">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label">Group Code For Spouse</label>
								<div class="input-group">
									<input type="text" placeholder="Enter Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							<?php } ?>
							<div class="col-md-2 mb-3 col-12">
								<!-- <label style="visibility: hidden;" class="mt-3">label</label> -->
								<div class="custom-control custom-checkbox form-check-inline">
									<input type="checkbox" class="taxchk" autocomplete="off">
									<input type="hidden" class="tax_opt" name="tax_opt[]" value="0" />
									<label for="istax">Is Taxable</label>
								</div>
							</div>

							<div class="col-md-1 mb-3 col-12 text-right">
								<!-- <label style="visibility: hidden;" class="mt-2">label</label> -->
								<button class="btn add-btn" id="btn_add_si_flat" type="button">Add <i class="ti-plus"></i></button>
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="col-md-12" id="add_per_tbody" style="<?php if (!empty($datalist->policy_premium) && ($datalist->premium_basis[0]->si_premium_basis_id == 8)) {
																	echo "display: block";
																} else {
																	echo "display: none";
																} ?> ">

					<?php if ($datalist->premium_basis[0]->si_premium_basis_id == 8) {
						
                        if($datalist->details[0]->policy_sub_type_id == 19){ 
                        	$selected_type=[];
                        	
                        	foreach ($datalist->policy_premium as $key => $value) {
                        		$value = (array) $value;
                        		$premium_type=(int) filter_var($value['premium_type'], FILTER_SANITIZE_NUMBER_INT);
                        		//$selected_type=[];
                        		if( strpos($value['premium_type'], 'inter') !== false){
                        			$selected_type[$premium_type]['inter_rate'] = $value['premium_rate']; 
                        			$selected_type[$premium_type]['inter_rate_gst'] = $value['premium_with_tax']; 
                        		}else{
                        			$selected_type[$premium_type]['intra_rate'] = $value['premium_rate']; 
                        			$selected_type[$premium_type]['intra_rate_gst'] = $value['premium_with_tax'];
                        		}
                        		 
                        	}

                        	echo '<div class="row">';

                        	echo '<div class="col-md-6">
									<label for="validationCustomUsername" class="col-form-label">Select Subject Matter Type<span class="lbl-star">*</span></label>
									<div class="input-group">
										<select class="select2 form-control" id="premium_type" name="premium_type[]" multiple="multiple">
										<option value="" disabled>Select Subject Matter Type</option>';
									
							foreach ($datalist->premium_type_list as $key => $value) {
								echo '<option value="'.$value->premium_type_id.'" ';
								if(array_key_exists($value->premium_type_id, $selected_type)){
										echo 'selected';
								} 
								echo '>'.$value->premium_type_name.'</option>';
							}
							echo		'</select>
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
										</div>
									</div>
								</div>
								
								
                            </div>';
                        	
							foreach ($datalist->premium_type_list as $key => $value) {
								$chk_sel = ''; 
								$inter_rate = ''; 
								$inter_rate_gst = ''; 
								$intra_rate = ''; 
								$intra_rate_gst = ''; 
								$style = 'style="display:none;"';
								if(array_key_exists($value->premium_type_id, $selected_type)){
									
									$inter_rate = $selected_type[$value->premium_type_id]['inter_rate']; 
									$inter_rate_gst = $selected_type[$value->premium_type_id]['inter_rate_gst'];; 
									$intra_rate = $selected_type[$value->premium_type_id]['intra_rate'];; 
									$intra_rate_gst = $selected_type[$value->premium_type_id]['intra_rate_gst'];
									$style= 'style="display:flex;"';
								}
								?>
                        		
                            <div class="row lbl-body premium_type_div" <?php echo $style;?> id="premium_type_div_<?php echo $value->premium_type_id; ?>">
                                <div class="col-md-2 mb-3 col-12">
                                    
                                	<label for="validationCustomUsername" class="col-form-label">Type</label>
                                    <div class="input-group">
                                    	<?php echo $value->premium_type_name; ?>
                                       
                                    </div>
                                </div>
                                <div class="col-md-5 mb-3 col-12">
                                    <label for="validationCustomUsername" class="col-form-label">IntraCity</label>
                                    <div class="input-group">
                                        <input type="number" placeholder="Rate" class="form-control"  step="any" id="intra_<?php echo $value->premium_type_id; ?>" name="intra_<?php echo $value->premium_type_id; ?>" autocomplete="off" value="<?php echo $intra_rate;?>">
                                        <div class="input-group-prepend mr-2">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                        <input type="number" placeholder="Rate(with GST)" class="form-control" step="any"  id="intra_gst_<?php echo $value->premium_type_id; ?>" name="intra_gst_<?php echo $value->premium_type_id; ?>" autocomplete="off" value="<?php echo $intra_rate_gst;?>">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mb-3 col-12">
                                    <label for="validationCustomUsername" class="col-form-label">InterCity</label>
                                    <div class="input-group">
                                        <input type="number" placeholder="Rate" class="form-control" step="any"  id="inter_<?php echo $value->premium_type_id; ?>" name="inter_<?php echo $value->premium_type_id; ?>" autocomplete="off"  value="<?php echo $inter_rate;?>">
                                        <div class="input-group-prepend mr-2">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                        <input type="number" placeholder="Rate(with GST)" step="any" class="form-control"  id="inter_gst_<?php echo $value->premium_type_id; ?>" name="inter_gst_<?php echo $value->premium_type_id; ?>" autocomplete="off"  value="<?php echo $inter_rate_gst;?>">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                    </div>
                                </div>
                            




                            </div>
                           <?php }?>

                    <?php }else{
                    if (!empty($datalist->policy_premium)) {
						$i = 1;
                    	foreach ($datalist->policy_premium as $premium) {
                    	?>
                            <div class="row lbl-body mt-1">
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Sum Insured</label>
									<div class="input-group">
										<input type="number" class="form-control" placeholder="Enter Sum Insured" id="sum_insured_per_opt<?php echo $i;?>" value="<?php if (isset($premium->sum_insured)) echo $premium->sum_insured;  ?>" name="sum_insured_per_opt1[]" autocomplete="off">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Percentage rate</label>
									<div class="input-group">
										<input type="number" placeholder="Enter Percentage"  id="premium_Per_opt<?php echo $i;?>" class="form-control" value="<?php
																												if (isset($premium->premium_with_tax) && !$premium->is_taxable) {
																													echo $premium->premium_with_tax;
																												} else {
																													echo $premium->premium_rate;
																												}  ?>" name="premium_Per_opt[]" autocomplete="off" step="0.01">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								</div>
                   <?php }
                        ?>


						<?php $i++;
						}
					 else { ?>

						
                            <div class="row lbl-body">
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label">Sum Insured</label>
								<div class="input-group">
									<input type="number" placeholder=" Enter Sum Insured" class="form-control"  id="sum_insured_per_opt1" name="sum_insured_per_opt1[]" autocomplete="off">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label">Premium rate</label>
								<div class="input-group">
									<input type="number" placeholder="Enter Premium" class="form-control"  id="premium_Per_opt1" name="premium_Per_opt[]" autocomplete="off" step="0.01">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>




						</div>
                   <?php } }
                        ?>
					<?php } ?>
				</div>

				<!-- 2 upload  Upload Family Construct -->
				<div id="fileUploadfamilyDiv" style="<?php if (!empty($datalist->policy_premium) && ($datalist->premium_basis[0]->si_premium_basis_id == 2)) {
															echo "display: block";
														} else {
															echo "display: none";
														} ?>" class="col-md-12">
					<div class=" row lbl-body">
						<div class="col-md-4">
							<label class="col-form-label">Upload Family Construct</label>
							<br>
							<input type="file" name="ageFile" id="familyConstructFile">
						</div>
						<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/familyExcel.xls" type="button" class="btn-22" download="familyExcel.xls">Download Format <i class="ti-download"></i> </a>
							</button>
						</div>
						<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button type="button" onclick="openraterview()" class="btn btn-link"><i class="fa fa-eye"></i>View Uploaded data</button>
						</div>
					</div>
				</div>

				<!-- 3 Upload family and age Construct -->
				<div id="fileUploadfamilyageDiv" style="<?php if (!empty($datalist->policy_premium) && ($datalist->premium_basis[0]->si_premium_basis_id == 3)) {
															echo "display: block";
														} else {
															echo "display: none";
														} ?>" class="col-md-12">

					<div class=" row lbl-body">
						<div class="col-md-4">
							<label class="col-form-label">Upload by family and age Construct</label>
							<br>
							<input type="file" name="ageFile" id="agefamilyConstructFile">
						</div>
						<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/agefamilyExcel.xls" type="button" class="btn-22" download="agefamilyExcel.xls">Download Format <i class="ti-download"></i></a>
							</button>
						</div>
						<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button type="button" onclick="openraterview()"class="btn btn-link"><i class="fa fa-eye"></i>View Uploaded data</button>
						</div>
					</div>
				</div>

				<!-- 4 Upload by Age -->
				<div id="fileUploadAgeDiv" style="<?php if (!empty($datalist->premium_basis) && ($datalist->premium_basis[0]->si_premium_basis_id == 4)) {
														echo "display: block";
													} else {
														echo "display: none";
													} ?>" class="col-md-12">
					<div class=" row lbl-body">
						<div class="col-md-4">
							<label class="col-form-label">Upload by Age</label>
							<br>
							<input type="file" name="ageFile" id="ageFile" class="div4">
						</div>
						<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/ageExcel.xls" type="button" class="btn-22" download="ageExcel.xls">Download Format <i class="ti-download"></i> </a>
							</button>
						</div>
							<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button type="button" onclick="openraterview()"class="btn btn-link"><i class="fa fa-eye"></i>View Uploaded data</button>
						</div>
					</div>
				</div>

				<!-- 5 Upload by Age -->
				 <?php
                        if($datalist->details[0]->policy_type_id == 3){ }else{ ?>
				<div id="fileUploadPerMileRate" style="<?php if (!empty($datalist->premium_basis) && ($datalist->premium_basis[0]->si_premium_basis_id == 5)) {
															echo "display: block";
														} else {
															echo "display: none";
														} ?>" class="col-md-12">
					<div class=" row lbl-body">
						<div class="col-md-4">
							<label class="col-form-label">Upload Per Mile Rate</label>
							<br>
							<input type="file" name="ageFile" id="ageFile" class="div5">
						</div>
						<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/per_mile_rate.xlsx" type="button" class="btn-22" download="per_mile_rate.xlsx">Download Format <i class="ti-download"></i> </a>
							</button>
						</div>
							<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button type="button" onclick="openraterview()"class="btn btn-link"><i class="fa fa-eye"></i>View Uploaded data</button>
						</div>
					</div>
				</div>
				<?php } ?>
				<!-- new added -->

				<!-- 6 Upload by Age -->
				<div id="fileUploadFamilyDeductable" style="<?php if (!empty($datalist->premium_basis) && ($datalist->premium_basis[0]->si_premium_basis_id == 6)) {
																echo "display: block";
															} else {
																echo "display: none";
															} ?>" class="col-md-12">
					<div class=" row lbl-body">
						<div class="col-md-4">
							<label class="col-form-label">Family Construct Deductable</label>
							<br>
							<input type="file" name="ageFile" id="ageFile" class="div6">
						</div>
						<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/familyExcelWithDeductable.xlsx" type="button" class="btn-22" download="familyExcelWithDeductable.xlsx">Download Format <i class="ti-download"></i> </a>
							</button>
						</div>
							<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button type="button" onclick="openraterview()"class="btn btn-link"><i class="fa fa-eye"></i>View Uploaded data</button>
						</div>
					</div>
				</div>

				<div id="perDayTenureDiv" style="<?php
													if (!empty($datalist->premium_basis) && ($datalist->premium_basis[0]->si_premium_basis_id == 7)) {
														echo "display: block";
													} else {
														echo "display: none";
													} ?>" class="col-md-12">
					<div class=" row lbl-body">
						<div class="col-md-4">
							<label class="col-form-label">Per Day Tenure</label>
							<br>
							<input type="file" name="ageFile" id="ageFile" class="div7">
						</div>
						<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/per_day_tenure.xlsx" type="button" class="btn-22" download="per_day_tenure.xlsx">Download Format <i class="ti-download"></i></a>
							</button>
						</div>
							<div class="col-md-3">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button type="button" onclick="openraterview()"class="btn btn-link"><i class="fa fa-eye"></i>View Uploaded data</button>
						</div>
					</div>
				</div>
				<?php
            }



    ?>
				<!-- new added -->

				<div class="row mt-3 col-md-12" id="addpolicybtn">
					<div class="col-md-1 col-6 text-left">
						<input type="hidden" id="plan_id" name="plan_id" value="<?php echo $datalist->plan_id; ?>" />
						<input type="hidden" id="creditor_id" name="creditor_id" value="<?php echo $datalist->details[0]->creditor_id; ?>" />

						<button class="btn smt-btn">Save</button>
					</div>
					<div class="col-md-2 col-6 text-right">
						<!-- <button class="btn cnl-btn">Cancel</button> -->
						<a href="<?php echo base_url(); ?>products" class="btn cnl-btn">Cancel</a>
					</div>
				</div>
		</div>
		</form>
	</div>
</div>
<div class="modal fade " id="raterViewModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Rater View</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div class="row">

                   <?php
                   if(isset($premiumData) && count($premiumData) > 0){ ?>
                      <table class="table table-border"  style="display:block;overflow: auto;">
                      <thead><tr>
                    <?php
                      $i=0;
                      foreach ($premiumData[0] as $key=>$rowPrem){
                        // if($i==0){
                             echo "<th>".$key."</th>";
                       //  }
                          $i++;
                       }
                       ?>
                       </tr>
                       </thead>
                       <tbody>
                         <?php
                        $tbody='';
                      foreach ($premiumData as $key=>$rowPrem){
                            $tbody .="<tr>";
                                foreach ($rowPrem as $r){
                                    $tbody .="<td>".$r."</td>";
                                }
                             $tbody .="</tr>";
                       }
                      echo $tbody;
                       ?>
                       </tbody>
                       </table>
                 <?php  }
                   ?>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <button class="btn btn-success" type="button" onclick="mapwithpartner()">Save</button>
            </div> -->
        </div>

    </div>
</div>
<?php  } ?>

<script>
	$("#premium_type").select2();
	$("#premium_type").trigger('change');
	
	$("#premium_type").on("change", function(e) {
		var values = $("#premium_type").val();
		$('.premium_type_div').css('display','none');
		values.forEach(function(item) {
		    $('#premium_type_div_'+item).css('display','flex');
		})
	    
	});

	var adult_members = JSON.parse('<?php echo json_encode($datalist->adult_members); ?>');
	$(document).ready(function() {
		// $('.ms-account-wrapper').eq(1).remove();
		// alert("hello...");
		//$("#body:not(:last)").unwrap();
		// ​$('#body').not(':first')​.unwrap();​​​​​​​​​​​​​
		// $('.col-md-10').not(':last').unwrap();
		// $(".col-md-10:not(:first)").unwrap();
		//$("#body").unwrap();


	});

	$("#continuebtn").click(function() {

		$("#body").load("<?php echo base_url(); ?>products/updatepolicyview/<?php echo $datalist->plan_id; ?>");

		//window.location = "<?php echo base_url(); ?>products/updatepolicyview/<?php echo $datalist->plan_id; ?>";

		//$("#body").remove();
		//$(this).parents('#body').remove();
	});

	$("#policyStartDate").datepicker({
		numberOfMonths: 1,
		dateFormat: 'dd-mm-yy',
		onSelect: function(selected) {
			let date_segments = selected.split('-');
			var dt = new Date(+date_segments[2], date_segments[1] - 1, +date_segments[0]);
			dt.setDate(dt.getDate() + 1);
			$("#policyEndDate").datepicker("option", "minDate", dt);
		}
	});

	if (!$('#policyStartDate').val()) {
		$("#policyStartDate").datepicker('setDate', '<?php echo $default_policy_start_date ?>');
	}

	$("#policyEndDate").datepicker({
		numberOfMonths: 1,
		dateFormat: 'dd-mm-yy',
		onSelect: function(selected) {
			let date_segments = selected.split('-');
			var dt = new Date(+date_segments[2], date_segments[1] - 1, +date_segments[0]);
			dt.setDate(dt.getDate() - 1);
			$("#policyStartDate").datepicker("option", "maxDate", dt);
		}
	});

	if (!$('#policyEndDate').val()) {
		$("#policyEndDate").datepicker('setDate', '<?php echo $default_policy_end_date ?>');
	}
	//All risk
    $("#policyTenureStartDate").datepicker({
 dateFormat: "dd/mm/yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                autoClose:true,
                minDate: new Date(),
        maxDate: new Date(new Date().setMonth(new Date().getMonth() + 1)),
onSelect: function(selected) {
     debugger;
			let date_segments = selected.split('/');
			var dt = new Date(+date_segments[2], date_segments[1] - 1, +date_segments[0]);
			//dt.setDate(dt.getDate() + 1);
			var lastDay = new Date(dt.setMonth(dt.getMonth() + 2))
			$("#policyTenureEndDate").datepicker("option", "minDate", lastDay);
		}

    });
    if ($('#policyTenureStartDate').val() !='') {
		//alert();
		var policyTenureStartDate = $('#policyTenureStartDate').val();
		
	}
//var lastDay = new Date(selected.getFullYear(), selected.getMonth() + 2, 0);
	$("#policyTenureEndDate").datepicker({
	 dateFormat: "dd/mm/yy",
	 
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                autoClose:true,
                changeYear: true,
               // minDate: new Date(tenure_start_date.setMonth(tenure_start_date.getMonth() + 2)),
                maxDate: "+2Y",
				beforeShow: function (dateText, inst) {
					debugger;

					var policyTenureStartDate = $('#policyTenureStartDate').val();
			let date_segments = policyTenureStartDate.split('/');
			var dt = new Date(+date_segments[2], date_segments[1] - 1, +date_segments[0]);
			//dt.setDate(dt.getDate() + 1);
			var lastDay = new Date(dt.setMonth(dt.getMonth() + 2))
			$("#policyTenureEndDate").datepicker("option", "minDate", lastDay);

      }

	});

	var vRules = {
		plan_name: {
			required: true,
			alphanumericwithspace: true
		}

	};

	var vMessages = {
		plan_name: {
			required: "Please enter plan name."
		}

	};
//All risk
$(document).on('change', '#business_type', function() {
    		var business_type = $('#business_type').val();


		        		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>products/getbusinessType",
			data: {
				business_type: business_type,
				policy_id : '<?php echo $datalist->policydetails[0]->policy_id; ?>'
			},
			dataType: "json",
			success: function(response) {
			    debugger;
			if(response.business_details != '' && response.business_details != null){

                        $('#per_location_limit').val(response.business_details.per_location_limit);
                         $('#per_sending_limit').val(response.business_details.per_sending_limit);




				}
			else{
			    $('#per_location_limit').val('');
                $('#per_sending_limit').val('');

			}

			}
		});

});

$(document).on('change', '#coverage_type', function() {
		var cover_val = $('#coverage_type').val();
		var cover = '';
		if(cover_val == 'single')
		    {

		        cover = '<div class="row col-md-12"><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Coverage Name<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<input type="text" required id="coverage_name" name= cover_det[1][cover_name] class="form-control" placeholder="Enter Coverage Name" aria-describedby="inputGroupPrepend"value="">\n'+
'\t\t\t\t\t\t\t<div class="error"><span class="policyerror"></span></div>\n'+
'\t\t\t\t\t\t\t<div class="input-group-prepend">\n'+
'\t\t\t\t\t\t\t\t<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>\n'+
'\t\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Premium Rate Type<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<select class="select2 form-control" required id = "premium_rate_type" name= cover_det[1][premium_rate_type]><option value="">Select </option><option value="flat">Flat </option><option value="percentage">Percentage </option></select>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Premium Rate<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<input type="hidden" id="coverage_key" required name= "coverage_key" class="form-control" placeholder="Enter Coverage Rate" aria-describedby="inputGroupPrepend"value="1"><input type="text" id="premium_rate" name= cover_det[1]["premium_rate"]" class="form-control premium_rate" placeholder="Enter Premium Rate" aria-describedby="inputGroupPrepend"value="">\n'+
'\t\t\t\t\t\t\t<div class="error"><span class="policyerror"></span></div>\n'+
'\t\t\t\t\t\t\t<div class="input-group-prepend">\n'+
'\t\t\t\t\t\t\t\t<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>\n'+
'\t\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3"></div></div>';
		      $('.add_more').hide();
		        $('.covers_details').show();
$('.covers_details').html(cover);


		    }else if(cover_val == 'multiple'){
		      cover = '<div class="row col-md-12"><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Coverage Name 1<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<input type="text" id="coverage_name" required name= cover_det[1][cover_name] class="form-control" placeholder="Enter Cover Name" aria-describedby="inputGroupPrepend"value="">\n'+
'\t\t\t\t\t\t\t<div class="error"><span class="policyerror"></span></div>\n'+
'\t\t\t\t\t\t\t<div class="input-group-prepend">\n'+
'\t\t\t\t\t\t\t\t<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>\n'+
'\t\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Coverage Rate Type 1<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<select class="select2 form-control" required id = "premium_rate_type_0" name= cover_det[1][coverage_rate_type] ><option value="">Select </option><option value="flat">Flat </option><option value="percentage">Percentage </option></select>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Coverage Rate 1<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<input type="text" id="coverage_rate" required name= cover_det[1][coverage_rate] class="form-control premium_rate" placeholder="Enter Coverage Rate" aria-describedby="inputGroupPrepend"value="">\n'+
'\t\t\t\t\t\t\t<div class="error"><span class="policyerror"></span></div>\n'+
'\t\t\t\t\t\t\t<div class="input-group-prepend">\n'+
'\t\t\t\t\t\t\t\t<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>\n'+
'\t\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Premium Rate Type 1<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<select class="select2 form-control" required id = "premium_rate_type_0" name= cover_det[1][premium_rate_type]><option value="">Select </option><option value="flat">Flat </option><option value="percentage">Percentage </option></select>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Premium Rate 1<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<input type="hidden" id="coverage_key" name= "coverage_key" class="form-control" placeholder="Enter Coverage Rate" aria-describedby="inputGroupPrepend"value="1"><input type="text" required id="premium_rate_0" name= cover_det[1][premium_rate] class="form-control premium_rate" placeholder="Enter Premium Rate" aria-describedby="inputGroupPrepend"value="">\n'+
'\t\t\t\t\t\t\t<div class="error"><span class="policyerror"></span></div>\n'+
'\t\t\t\t\t\t\t<div class="input-group-prepend">\n'+
'\t\t\t\t\t\t\t\t<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>\n'+
'\t\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3"></div></div>';
		      cover += ''
		      $('.add_more').show();
		        $('.covers_details').show();
$('.covers_details').html(cover);

		    }
			else
			{
				$('.covers_details').html('');
			}
		var policy_id;
		
			$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>products/getCoverageType",
			data: {
				coverage_type: cover_val,
				policy_id : '<?php echo $datalist->policydetails[0]->policy_id; ?>'
			},
			dataType: "json",
			success: function(response) {
			    debugger;
			if(response.coverage_details != ''){
                if(cover_val == 'single')
                    {
                        $('#coverage_name').val(response.coverage_details[0].coverage_name);
                         $('#premium_rate_type').val(response.coverage_details[0].premium_type);
                         $('#premium_rate').val(response.coverage_details[0].premium_rate);

                    }else{

                    }
				}

			}
		});
		});
var covers_type = $("#coverage_key").val();
function add_fields() {
    covers_type++;
    var objTo = document.getElementById('cover_field')
    var divtest = document.createElement("div");
  divtest.className = 'row col-md-12';
    divtest.innerHTML = '</hr><div class="row col-md-12"><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Coverage Name'+ covers_type +'<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<input type="text" required id="coverage_name_"+covers_type+ name= cover_det['+covers_type+'][cover_name] class="form-control" placeholder="Enter Cover Name" aria-describedby="inputGroupPrepend"value="">\n'+
'\t\t\t\t\t\t\t<div class="error"><span class="policyerror"></span></div>\n'+
'\t\t\t\t\t\t\t<div class="input-group-prepend">\n'+
'\t\t\t\t\t\t\t\t<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>\n'+
'\t\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Coverage Rate Type'+ covers_type +'<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<select class="select2 form-control" required id = "premium_rate_type" name=cover_det['+covers_type+'][coverage_rate_type]><option value="">Select </option><option value="flat">Flat </option><option value="percentage">Percentage </option></select>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Coverage Rate '+ covers_type +'<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<input type="text" required id="coverage_rate_"+covers_type + name= cover_det['+covers_type+'][coverage_rate] class="form-control premium_rate" placeholder="Enter Coverage Rate" aria-describedby="inputGroupPrepend"value="">\n'+
'\t\t\t\t\t\t\t<div class="error"><span class="policyerror"></span></div>\n'+
'\t\t\t\t\t\t\t<div class="input-group-prepend">\n'+
'\t\t\t\t\t\t\t\t<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>\n'+
'\t\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<label for="validationCustomUsername" class="col-form-label">Premium Rate Type '+ covers_type +'<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<select class="select2 form-control" required id = "premium_rate_type" +covers_type + name=cover_det['+covers_type+'][premium_rate_type]><option value="">Select </option><option value="flat">Flat </option><option value="percentage">Percentage </option></select>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div><div class="col-md-4 mb-3">'+
'<input type="hidden" id="coverage_key" name= "coverage_key" class="form-control" placeholder="Enter Coverage Rate" aria-describedby="inputGroupPrepend"value="'+covers_type+'"><label for="validationCustomUsername" class="col-form-label">Premium Rate '+ covers_type +'<span class="lbl-star">*</span></label>\n'+
'<div class="input-group">\n'+
'<input type="text" required id="premium_rate"+covers_type +  name= cover_det['+covers_type+'][premium_rate] class="form-control premium_rate" placeholder="Enter Premium Rate" aria-describedby="inputGroupPrepend"value="">\n'+
'\t\t\t\t\t\t\t<div class="error"><span class="policyerror"></span></div>\n'+
'\t\t\t\t\t\t\t<div class="input-group-prepend">\n'+
'\t\t\t\t\t\t\t\t<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">code</span></span>\n'+
'\t\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t\t</div>\n'+
'\t\t\t\t\t</div></div>';

    objTo.appendChild(divtest)
}
	$("#form-plan").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) {
			var act = "<?php echo base_url(); ?>products/update";
			$("#form-plan").ajaxSubmit({
				url: act,
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				beforeSubmit: function(arr, $form, options) {
					$("#addplanbtn").hide();
				},
				success: function(response) {

					if (response.success) {
						console.log(response.data);
						displayMsg("success", response.msg);
						$("#body").load("<?php echo base_url(); ?>products/updatepolicyview/" + response.data);

					} else {
						$("#addplanbtn").show();
						displayMsg("error", response.msg);
						return false;
					}
				}
			});
		}
	});

	$(document).on('change', '.planname', function() {
		var name = $(this).val();
		var id = $(this).attr('data-id');

		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>products/checkplanname/" + id,
			data: {
				name: name
			},
			dataType: "json",
			success: function(response) {
				console.log(response);
				if (response.success) {
					$('.planerror').html("");
				} else {
					$('.planname').val('');
					$('.planerror').html(response.msg);
				}
			}
		});
	});
	$(document).on('change', '.policyno', function() {
		var name = $(this).val();
		var id = $(this).attr('data-id');
		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>products/checkpolicynumber" + id,
			data: {
				name: name
			},
			dataType: "json",
			success: function(response) {

				if (response.success) {
					$('.policyerror').html("");
				} else {
					$('.policyno').val('');
					$('.policyerror').html(response.msg);
				}
			}
		});
	});
</script>

<script>
	var vRules1 = {
		//policyNo:{required:true, number:true},
		policyNo: {
			required: true
		},
		pdf_type: {
			required: true
		},
		masterInsurance: {
			required: true
		},
		sum_insured_type: {
			required: true
		},
		companySubTypePolicy: {
			required: true
		},
        start_series: {
			required: true
		},
        end_series: {
            required: true
        },
		policyTenureStartDate: {
			required: true
		},
		policyTenureEndDate: {
			required: true
		},
		coverage_type:{
			required: true
	
		},
        "mandatory_if_not_selected[]": {
            required: true
         },
		plan_code: {
			required: true
		},
		product_code: {
			required: true
		},
		membercount: {
			required: true
		},
		coi_start_series: {
			required: true
		},
		series_digit_count: {
			required: true
		}
	};

	var vMessages1 = {
		//policyNo:{required:"Please enter policy number.",number:"Please enter numbers Only"},
		policyNo: {
			required: "Please enter policy number."
		},
		pdf_type: {
			required: "Please select PDF type."
		},
		masterInsurance: {
			required: "Please select insurer."
		},
		sum_insured_type: {
			required: "Please select insured type."
		},
		companySubTypePolicy: {
			required: "Please select SI basis."
		},
		policyTenureStartDate: {
			required: "Please select Policy Tenure Start Date."
		},
		policyTenureEndDate: {
			required: "Please select Policy Tenure End Date."
		},
		coverage_type:{
			required: "Please select Coverage Type."
	
		},
		"mandatory_if_not_selected[]": {
			required: "This field is required."
		},
		plan_code: {
			required: "Please enter plan code"
		},
		product_code: {
			required: "please enter product code"
		},
		membercount: {
			required: "please enter policy member count"
		},
		coi_start_series: {
			required: "please enter COI start series"
		},
		series_digit_count: {
			required: "please enter series counter digit"
		}
	};

	$("body").on("keyup", ".agefield,#cover_initial,#cover_limit,#max_mi,.membercount,#end_series,#start_series", function (e) {
        var $th = $(this);


        $th.val(
            $th.val().replace(/[^0-9]/g, function(str) {
                return "";
            })
        );

    });
    $(".sum_insured_opt,.premium_opt,.premium_rate").keyup(function() {

	   	var position = this.selectionStart - 1;
	    //remove all but number and .
	    var fixed = this.value.replace(/[^0-9\.]/g, "");	
	    this.value =this.value.replace(/[e\+\-]/gi, "");
	    if (fixed.charAt(0) === ".")
	      //can't start with .
	      fixed = fixed.slice(1);

	    var pos = fixed.indexOf(".") + 1;
	    if (pos >= 0)
	      //avoid more than one .
	      fixed = fixed.substr(0, pos) + fixed.slice(pos).replace(".", "");

	    if (this.value !== fixed) {
	      this.value = fixed;
	      this.selectionStart = position;
	      this.selectionEnd = position;
	    }     
	});

    jQuery.validator.addMethod(
            "maxage",
            function(value, element, arg) { 

               var minage = $(element).parent().find('.minfield').val();
               var min_age_type = $(element).parent().find('.min_age_type').val();

               if(parseInt(minage) > parseInt(value) && min_age_type!='days'){
               	return false;
               }else{

               	return true;
               }

            }, function() {return 'Max age must be greater than min age. '}
        );


	$("#form-policy").validate({
		rules: vRules1,
		messages: vMessages1,
		errorPlacement: function(label, element) {
		    if (element.hasClass('select2')) {
			//	alert();
		       label.insertAfter(element.parents().find('.select2-container')).addClass('error');
		      label.css('display','block')
		    } else if(element.hasClass('agefield')){
		    	 label.insertAfter(element.parent())

		    }else {
		      label.insertAfter(element);
		    }
		  },
		submitHandler: function(form) {

			var act = "<?php echo base_url(); ?>products/UpdatePolicyNew";
			$("#form-policy").ajaxSubmit({
				url: act,
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				beforeSubmit: function(arr, $form, options) {
					$("#addpolicybtn").hide();
				},
				success: function(response) {
					console.log(response);
					if (response.success) {
						console.log(response.data);
						$("#addpolicybtn").show();
						displayMsg("success", response.msg);
						$("#body").load("<?php echo base_url(); ?>products/updatepolicyview/" + response.data);

					} else {
						$("#addpolicybtn").show();
						displayMsg("error", response.msg);
						return false;
					}
				}
			});
		}
	});

	function openraterview() {
	  $('#raterViewModal').modal('show');
	}
</script>

<?php
$select = "<option value=''>Select Member</option>";

foreach ($datalist->members as $member) {
	$select .= "<option value='$member->id'>$member->member_type</option>";
}
$fileset = 0;
if (!empty($datalist->policy_premium) && !empty($datalist->premium_basis[0]->si_premium_basis_id)) { 
	$fileset = $datalist->premium_basis[0]->si_premium_basis_id;
}
?>

<script>
	$('#policySubType').on('change', function() {
		var val = $(this).val();
		var plan_id = <?php echo $datalist->plan_id; ?>;
		if (val != '') {
			$("#body").load("<?php echo base_url(); ?>products/updatepolicyview/" + plan_id + "/" + val);
		}

		//$("#accordion45").attr("aria-expanded","true");

	});

	$(document).on('change','.membercount', function() {
		var count = $(this).val();
		var html = "";
		var select = "<?php echo $select; ?>";
		for (var i = 0; i < count; i++) {
			html += "<div class='col-sm-4 form-group'><select data-id='" + i + "'name='member[]' class='form-control memberselect'>" + select + "</select></div>";
			html += "<div class='col-sm-8 form-group'><input class='form-control agefield' type='number' placeholder='Min Age' min='0' max='100' name='minage[]' required/> <input type='number' placeholder='Max Age'class='form-control agefield' min='0' max='100' name='maxage[]' required/></div>";
		}
		$(".memberlist").html(html);
		if($('#adult-members-list').is(":visible")){
	        $(".adult-row").find(':input:not([type=hidden])').attr('required',true);
	    }else{
	         $(".adult-row").find(':input:not([type=hidden])').attr('required',false);
	    }
	    if($('#children-members-list').is(":visible")){
	        $(".children-row").find(':input:not([type=hidden])').attr('required',true);
	    }else{
	         $(".children-row").find(':input:not([type=hidden])').attr('required',false);
	    }
		$("#max_mi").attr('max',count);
	});

	$("#btn_add_si_flat").click(function() {
	    var policy_type_id=$("#policy_type_id").val();
		//var cols = "";
		//var newRow = $("<tr>");
		// cols += '<td style="text-align: right;" class="row mt-4"> <div class="col-md-5"><label style="font-size: 13px; float: left;">Enter Sum Insured</label><input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"></div><div class="col-md-5"><label  style="font-size: 13px; float: left;">Enter Premium</label><input type="number" placeholder="premium" class="form-control" name="premium_opt[]" autocomplete="off"> </div><div class="col-md-2"><label  style="font-size: 13px; float: left;">Is Taxable</label>  <input type="checkbox" class="form-control taxchk"><input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /> </div></td> ';
		// cols += '<td name="del_btn_opt" class="del_btn_opt" style="width:15%;text-align: right;" ><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
		// newRow.append(cols);
		var newRow = "";
		newRow += '<div class="row lbl-body mt-1">';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label"> Sum Insured</label><div class="input-group"><input type="number" placeholder=" Enter Sum Insured" class="form-control sum_insured_opt" name="sum_insured_opt1[]" autocomplete="off" required min="1"> <div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label"> Premium Rate</label><div class="input-group"><input type="number" placeholder="Enter Premium" class="form-control premium_opt" name="premium_opt[]" autocomplete="off" required step="0.01" min="0.01"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		if(policy_type_id != 3){
		    newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label"> Group Code</label><div class="input-group"><input type="text" placeholder="Enter Group Code" class="form-control" name="group_code[]" autocomplete="off"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label"> Group Code For Spouse</label><div class="input-group"><input type="text" placeholder="Enter Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		}

		newRow += '<div class="col-md-2 mb-3 col-12"><div class="custom-control custom-checkbox form-check-inline"><input type="checkbox" class="taxchk" autocomplete="off"> <input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /><label for="istax">Is Taxable</label></div></div>';
		newRow += '<div class="del_btn_opt"><a>Delete<i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></div>';
		newRow += '</div>';

		$("#add_si_tbody").append(newRow);
		return false;
	});
       var count=1;
	$("#btn_add_per_flat").click(function() {
		//var cols = "";
		//var newRow = $("<tr>");
		// cols += '<td style="text-align: right;" class="row mt-4"> <div class="col-md-5"><label style="font-size: 13px; float: left;">Enter Sum Insured</label><input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"></div><div class="col-md-5"><label  style="font-size: 13px; float: left;">Enter Premium</label><input type="number" placeholder="premium" class="form-control" name="premium_opt[]" autocomplete="off"> </div><div class="col-md-2"><label  style="font-size: 13px; float: left;">Is Taxable</label>  <input type="checkbox" class="form-control taxchk"><input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /> </div></td> ';
		// cols += '<td name="del_btn_opt" class="del_btn_opt" style="width:15%;text-align: right;" ><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
		// newRow.append(cols);
		var newRow = "";
		count++;
		newRow += '<div class="row lbl-body mt-1">';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label"> Sum Insured</label><div class="input-group"><input type="number" placeholder="Enter Sum Insured" class="form-control" onkeyup="changePrem('+count+')" id="sum_insured_per_opt'+count+'"name="sum_insured_per_opt1[]" autocomplete="off"> <div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label"> Premium Rate</label><div class="input-group"><input type="number" placeholder="Enter Premium" class="form-control" onkeyup="changePrem('+count+')" id="premium_Per_opt'+count+'"name="premium_Per_opt[]" autocomplete="off" step="0.01"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
        newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Premium</label><div class="input-group"><input type="number" class="form-control" value="" readonly id="prem'+count+'" autocomplete="off"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>'
		newRow += '<div class="del_btn_opt"><a>Delete<i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></div>';
		newRow += '</div>';

		$("#add_per_tbody").append(newRow);
		return false;
	});
	function changePrem(cnt){
	    var sumInsure=$("#sum_insured_per_opt"+cnt).val();
	    var percentage=$("#premium_Per_opt"+cnt).val();
	    $("#prem"+cnt).val((sumInsure*percentage)/100)
	}

	$("body").on('click', ".del_btn_opt", function() {
		this.parentNode.remove();
	});

	$(document).on('change', '.memberselect', function() {
		var val = $(this).val();
		var ele = $(this).attr('data-id');
		console.log(ele);
		$('.memberselect').each(function() {
			var ele2 = $(this).attr('data-id');
			var ele3 = $(this);
			if (ele != ele2) {
				if (ele3.val() == val) {
					ele3.val('');
				}
				ele3.find('[value="' + val + '"]').remove();
			}
		});

	});
	var fileset='<?php echo $fileset ?>';

	$("#companySubTypePolicy").on("change", function() {

		var str = $(this).val();
		$(".default_sumInsuredDiv").css("display", "none");
		 $("#add_per_tbody").css("display", "none");
		 $('[name="ageFile"]').attr("required", false);
		 $('[name="sum_insured_opt1[]"]').attr("required", false);
		 $('[name="premium_opt[]"]').attr("required", false);
		if (str == "2") {
			$("#fileUploadfamilyDiv").show();
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");
			if(fileset != 2){
				$("#familyConstructFile").attr("required", true);
			}

		} else if (str == "1") {
			$("#add_si_tbody").css("display", "block");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");

			$('[name="sum_insured_opt1[]"]').attr("required", true);
		 	$('[name="premium_opt[]"]').attr("required", true);

		} else if (str == "3") {
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").hide();
			$("#fileUploadfamilyageDiv").css("display", "block");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");
			if(fileset != 3){
				$("#agefamilyConstructFile").attr("required", true);
			}

		} else if (str == "4") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").show();
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");
			if(fileset != 4){
				$(".div4").attr("required", true);
			}

		} else if (str == "5") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").show();
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");
$(".default_sumInsuredDiv").css("display", "block");
			if(fileset != 5){
				$(".div5").attr("required", true);
			}
		} else if (str == "6") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");

			$("#fileUploadFamilyDeductable").show();
			if(fileset != 6){
				$(".div6").attr("required", true);
			}
		} else if (str == "7") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").show();
			if(fileset != 7){
				$(".div7").attr("required", true);
			}
		}else if (str == "8") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");
            $("#add_per_tbody").css("display", "block");
            if(fileset != 8){
	            $(".div8").attr("required", true);
	        }
		}
	});

	$(document).on('change', '.taxchk', function() {

		if ($(this).is(':checked')) {
			//  alert("hello...");
			$(this).parent().find(".tax_opt").val(1);
		} else {
			$(this).parent().find(".tax_opt").val(0);
		}
	});
	$('#cd_balance').change(function(){
		   $("#threshold").val(0);
        });
	$('#threshold').change(function(){
		   var cd_balance=$("#cd_balance").val();
		   if(this.value >= cd_balance){
		       alert('Threshold should less than CD balance.');
		        $("#threshold").val(0);
		   }
        });
</script>

<?php if (count($datalist->details) > 1) : ?>
	<script>
		var selected_policies = JSON.parse('<?php echo json_encode($datalist->details); ?>');
		var selected_mandatory_if_not_selected = JSON.parse('<?php echo isset($datalist->mandatory_if_not_selections) ? json_encode($datalist->mandatory_if_not_selections) : "{}"  ?>');
		$('#mandatory_if_not_selected').select2({
			'placeholder': 'Select Policy Types'
		});
		populateMandatoryIfNotSelectedOptions();

		$('#policySubType').change(function() {
			populateMandatoryIfNotSelectedOptions();

		});
	/*if($("#sum_insured_type").val() == 5){
		    alert();
		    $("#default_sumInsuredDiv").css("display", "block");
		}*/

		function populateMandatoryIfNotSelectedOptions() {
			let current_selection = $('#policySubType').val();
			let mandatory_if_not_selected_html = '';

			selected_policies.forEach(policy => {
				if (policy.policy_id != current_selection) {
					mandatory_if_not_selected_html += `<option value="${policy.policy_id}">${policy.policy_sub_type_name}</option>`
				}
			});

			$('#mandatory_if_not_selected').html(mandatory_if_not_selected_html);

			if (selected_mandatory_if_not_selected.length > 0) {
				let selected_values_id = selected_mandatory_if_not_selected.map(obj => obj.dependent_on_policy_id);
				$("#mandatory_if_not_selected").select2('val', selected_values_id);
			}
		}

		$('[name="mandatory"]').change(function(event) {
			let mandatory_option_selected = $("#mandatory_option").is(':checked');

			if (mandatory_option_selected) {
				$('#mandatory_if_not_selected_section').hide();
				disableInnerFields('mandatory_if_not_selected_section');
			} else {
				$('#mandatory_if_not_selected_section').show();
				enableInnerFields('mandatory_if_not_selected_section');
			}
		});

		$('[name="mandatory"]').trigger('change');
	</script>
<?php endif; ?>