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
			<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion44" aria-expanded="false"> <span class="lbl-card">Edit Plan Details - <i class="ti-file"></i></a>
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
							<label for="validationCustomUsername" class="col-form-label">Policy Types</label>
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
							<label for="validationCustomUsername" class="col-form-label">Policy Sub Type</label>
							<div class="input-group">
								<select id="policySubType" name="policySubType" class="form-control" required>
									<option value="">Select</option>
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
							<label for="validationCustomUsername" class="col-form-label">Policy #</label>
							<div class="input-group">
								<input class="form-control policyno" type="text" data-id="<?php echo $detail->policy_id; ?>" value="<?php if (isset($datalist->policydetails)) {
																																		echo $datalist->policydetails[0]->policy_number;
																																	} ?>" id="policyNo" name="policyNo">

								<div class="error"><span class="policyerror"></span></div>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>

						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Insurer</label>
							<div class="form-group">
								<select id="masterInsurance" name="masterInsurance" class="form-control">
									<option value="">Select</option>
									<?php foreach ($datalist->insurers as $insurer) { ?>
										<option value="<?php echo $insurer->insurer_id; ?>" <?php
																							//if(isset($datalist->policydetails) && $datalist->policydetails[0]->insurer_id == $insurer->insurer_id)
																							if ($insurer->insurer_id == 1) {
																								echo "selected";
																							}
																							?>><?php echo $insurer->insurer_name; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Plan Code</label>
							<div class="input-group">
								<input type="text" id="plan_code" name="plan_code" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="" value="<?php if (isset($datalist->policydetails)) {
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
						<label for="validationCustomUsername" class="col-form-label">Product Code</label>
						<div class="input-group">
							<input type="text" id="product_code" name="product_code" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="" value="<?php if (isset($datalist->policydetails)) {
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

					<div class="col-md-6 mb-3 ml-1 row">
						<?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->is_optional == 0) {
							$mandatory_checked = 'checked';
							$optional_checked = '';
						} else {
							$mandatory_checked = '';
							$optional_checked = 'checked';
						}
						?>
						<label style="visibility: hidden;" class="display-sm-lbl col-md-12">space</label>
						<div class="form-check-inline custom-control custom-radio">
							<input type="radio" class="custom-control-input" name="mandatory" value="1" id="mandatory_option" <?php echo $mandatory_checked ?>>
							<label class="custom-control-label" for="mandatory_option"> Mandatory </label>
				      	</div>
							<div class="form-check-inline custom-control custom-radio">	
							<input type="radio" class="custom-control-input" name="mandatory" value="0" id="optional_option" <?php echo $optional_checked ?>>
							<label class="custom-control-label" for="optional_option"> Optional </label>
						</div>
						<div class="col-md-1">
						<div class="form-check custom-control custom-checkbox" id="combo_flag"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->is_combo == 1) {
		      																																																																	echo "checked";
																																																															} ?>> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div></div>
					</div>

					<div class="col-md-6">
						<?php if (count($datalist->details) > 1) : ?>
							<div style="display: none;" id="mandatory_if_not_selected_section">
								<label for="mandatoryIfSelectedRules" class="col-form-label">Mandatory If Not Selected </label>
								<select id="mandatory_if_not_selected" name="mandatory_if_not_selected[]" class="select2 form-control" multiple="multiple">

								</select>
							</div>
						<?php endif; ?>
					</div>
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
							<input class="form-control imgDatepicker datepicker" type="text" id="policyStartDate" name="policyStartDate" autocomplete="off" value="<?php if (isset($datalist->policydetails) && !empty($datalist->policydetails[0]->policy_start_date)) {
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
							<input class="form-control imgDatepicker datepicker" type="text" id="policyEndDate" name="policyEndDate" autocomplete="off" value="<?php if (isset($datalist->policydetails) && !empty($datalist->policydetails[0]->policy_end_date)) {
																																									echo date('d-m-Y', strtotime($datalist->policydetails[0]->policy_end_date));
																																								} ?>">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
				</div>

				<?php $this->load->view('member_info') ?>

				<div class="row col-md-12">
					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Sum Insured Type</label>
						<div class="input-group">
							<?php
							// print "<pre>";
							// print_r($datalist->si_type);
							?>
							<select id="sum_insured_type" name="sum_insured_type" class="form-control">
								<option value=""> Select </option>
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
					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Rater</label>
						<div class="input-group">
							<?php
							//print "<pre>";
							//print_r($datalist->premium_basis[0]); 
							?>
							<select id="companySubTypePolicy" name="companySubTypePolicy" class="form-control valid" aria-invalid="false">

								<option value="">Select</option>
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
									<label for="validationCustomUsername" class="col-form-label">Enter Sum Insured</label>
									<div class="input-group">
										<input type="number" class="form-control" value="<?php if (isset($premium->sum_insured)) echo $premium->sum_insured;  ?>" name="sum_insured_opt1[]" autocomplete="off">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Enter Premium rate</label>
									<div class="input-group">
										<input type="number" placeholder="Premium" class="form-control" value="<?php
																												if (isset($premium->premium_with_tax) && !$premium->is_taxable) {
																													echo $premium->premium_with_tax;
																												} else {
																													echo $premium->premium_rate;
																												}  ?>" name="premium_opt[]" autocomplete="off" step="0.01">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Enter Group Code</label>
									<div class="input-group">
										<input type="text" placeholder="Group Code" class="form-control" name="group_code[]" autocomplete="off" value="<?php if (isset($premium->group_code)) echo $premium->group_code; ?>">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Enter Group Code For Spouse</label>
									<div class="input-group">
										<input type="text" placeholder="Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off" value="<?php if (isset($premium->group_code_spouse)) echo $premium->group_code_spouse; ?>">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
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
								<label for="validationCustomUsername" class="col-form-label">Enter Sum Insured</label>
								<div class="input-group">
									<input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label">Enter Premium rate</label>
								<div class="input-group">
									<input type="number" placeholder="Premium" class="form-control" name="premium_opt[]" autocomplete="off" step="0.01">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label">Enter Group Code</label>
								<div class="input-group">
									<input type="text" placeholder="Group Code" class="form-control" name="group_code[]" autocomplete="off">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-3 mb-3 col-12">
								<label for="validationCustomUsername" class="col-form-label">Enter Group Code For Spouse</label>
								<div class="input-group">
									<input type="text" placeholder="Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
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
						<div class="col-md-1">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/familyExcel.xls" class="btn-22" download="familyExcel.xls">Download Format <i class="ti-download"></i> </a>
							</button>
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
						<div class="col-md-1">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/agefamilyExcel.xls" class="btn-22" download="agefamilyExcel.xls">Download Format <i class="ti-download"></i></a>
							</button>
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
							<input type="file" name="ageFile" id="ageFile">
						</div>
						<div class="col-md-1">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/ageExcel.xls" class="btn-22" download="ageExcel.xls">Download Format <i class="ti-download"></i> </a>
							</button>
						</div>
					</div>
				</div>

				<!-- 5 Upload by Age -->
				<div id="fileUploadPerMileRate" style="<?php if (!empty($datalist->premium_basis) && ($datalist->premium_basis[0]->si_premium_basis_id == 5)) {
															echo "display: block";
														} else {
															echo "display: none";
														} ?>" class="col-md-12">
					<div class=" row lbl-body">
						<div class="col-md-4">
							<label class="col-form-label">Upload Per Mile Rate</label>
							<br>
							<input type="file" name="ageFile" id="ageFile">
						</div>
						<div class="col-md-1">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/per_mile_rate.xlsx" class="btn-22" download="per_mile_rate.xlsx">Download Format <i class="ti-download"></i> </a>
							</button>
						</div>
					</div>
				</div>
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
							<input type="file" name="ageFile" id="ageFile">
						</div>
						<div class="col-md-1">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/familyExcelWithDeductable.xlsx" class="btn-22" download="familyExcelWithDeductable.xlsx">Download Format <i class="ti-download"></i> </a>
							</button>
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
							<input type="file" name="ageFile" id="ageFile">
						</div>
						<div class="col-md-1">
							<label class="mt-1" style="visibility: hidden;"></label>
							<button class="btn exp-button">
								<a href="../assets/per_day_tenure.xlsx" class="btn-22" download="per_day_tenure.xlsx">Download Format <i class="ti-download"></i></a>
							</button>
						</div>
					</div>
				</div>
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

<?php  } ?>

<script>
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
		}
	};

	$("#form-policy").validate({
		rules: vRules1,
		messages: vMessages1,
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
</script>

<?php
$select = "<option value=''>Select Member</option>";

foreach ($datalist->members as $member) {
	$select .= "<option value='$member->id'>$member->member_type</option>";
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

	$('.membercount').on('change', function() {
		var count = $(this).val();
		var html = "";
		var select = "<?php echo $select; ?>";
		for (var i = 0; i < count; i++) {
			html += "<div class='col-sm-4 form-group'><select data-id='" + i + "'name='member[]' class='form-control memberselect'>" + select + "</select></div>";
			html += "<div class='col-sm-8 form-group'><input class='form-control agefield' type='number' placeholder='Min Age' min='0' max='100' name='minage[]' required/> <input type='number' placeholder='Max Age'class='form-control agefield' min='0' max='100' name='maxage[]' required/></div>";
		}
		$(".memberlist").html(html);
	});

	$("#btn_add_si_flat").click(function() {
		//var cols = "";
		//var newRow = $("<tr>");
		// cols += '<td style="text-align: right;" class="row mt-4"> <div class="col-md-5"><label style="font-size: 13px; float: left;">Enter Sum Insured</label><input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"></div><div class="col-md-5"><label  style="font-size: 13px; float: left;">Enter Premium</label><input type="number" placeholder="premium" class="form-control" name="premium_opt[]" autocomplete="off"> </div><div class="col-md-2"><label  style="font-size: 13px; float: left;">Is Taxable</label>  <input type="checkbox" class="form-control taxchk"><input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /> </div></td> ';
		// cols += '<td name="del_btn_opt" class="del_btn_opt" style="width:15%;text-align: right;" ><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
		// newRow.append(cols);
		var newRow = "";
		newRow += '<div class="row lbl-body mt-1">';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Sum Insured</label><div class="input-group"><input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"> <div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Premium Rate</label><div class="input-group"><input type="number" placeholder="Premium" class="form-control" name="premium_opt[]" autocomplete="off" step="0.01"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Group Code</label><div class="input-group"><input type="text" placeholder="Group Code" class="form-control" name="group_code[]" autocomplete="off"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Group Code For Spouse</label><div class="input-group"><input type="text" placeholder="Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-2 mb-3 col-12"><div class="custom-control custom-checkbox form-check-inline"><input type="checkbox" class="taxchk" autocomplete="off"> <input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /><label for="istax">Is Taxable</label></div></div>';
		newRow += '<div class="del_btn_opt"><a>Delete<i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></div>';
		newRow += '</div>';

		$("#add_si_tbody").append(newRow);
		return false;
	});

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

	$("#companySubTypePolicy").on("change", function() {
		var str = $(this).val();
		if (str == "2") {
			$("#fileUploadfamilyDiv").show();
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");

		} else if (str == "1") {
			$("#add_si_tbody").css("display", "block");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");

		} else if (str == "3") {
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").hide();
			$("#fileUploadfamilyageDiv").css("display", "block");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");

		} else if (str == "4") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").show();
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");

		} else if (str == "5") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").show();
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");

		} else if (str == "6") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#perDayTenureDiv").css("display", "none");
			$("#fileUploadFamilyDeductable").show();
		} else if (str == "7") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadPerMileRate").css("display", "none");
			$("#fileUploadFamilyDeductable").css("display", "none");
			$("#perDayTenureDiv").show();
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