<?php
//print_r($datalist);
if (isset($datalist->plan_id)) {
	echo "<pre>";
	print_r($datalist);
}
?>
<style>
	.addPaymentMode {
		background: none;
		border: none;
		color: #fff;
	}

	.remove {
		border: none;
		background: none;
		color: #ff0000;
	}

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

	.select2-container::before{
		display: none;
	}
	.policySubTypeContainer2::before {
		right: 16px;
		top: 11px;

	}
</style>
<script src="<?php echo base_url(); ?>assets/js/products.js"></script>
<div class="col-md-10" id="body">
	<div id="accordion3" class="according accordion-s2 mt-3">
		<div class="card card-member">
			<div class="card-header card-vif"><a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion44" aria-expanded="false"><span class="lbl-card">Product Details - <i class="ti-file"></i></a></div>
			<div id="accordion44" class="card-vis-mar collapse show" data-parent="#accordion2" style="">
				<?php //echo $datalist->plan_id;
				?>
				<?php if (!isset($datalist->plan_id)) { ?>
					<form class="form-horizontal" id="form-plan" method="post" enctype="multipart/form-data" autocomplete="off">
						<div class="card-body">
							<div class="row">
								<div class="col-md-3 mb-3">
									<label for="validationCustomUsername" class="col-form-label">Partner<span class="lbl-star">*</span></label>
									<div class="input-group">
										<select class="select2 form-control" name="creditor" id="creditor">
											<option>Select partner</option>
											<?php foreach ($datalist->creditors as $creditor) { ?>
												<option value="<?php echo $creditor->creditor_id; ?>"><?php echo $creditor->creaditor_name; ?></option>
											<?php } ?>
										</select>
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
										</div>
									</div>
								</div>
                            <div class="col-md-3 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy Type<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <select class="select2 form-control" name="policy_type" onchange="getPolicySubtype(this.value)">
                                        <option>Select policy type</option>
                                        <?php foreach ($datalist->policytypes as $subtype) { ?>
                                            <option value="<?php echo $subtype->policy_type_id; ?>"><?php echo $subtype->policy_type_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
									<label for="validationCustomUsername" class="col-form-label">Policy Sub Type<span class="lbl-star">*</span></label>
									<div class="input-group">
										<select class="select2 form-control policySubTypeContainer2" id="policy_sub_type"name="policy_sub_type[]" multiple="multiple">
											<?php /*foreach ($datalist->policysubtypes as $subtype) { */?><!--
												<option value="<?php /*echo $subtype->policy_sub_type_id; */?>"><?php /*echo $subtype->policy_sub_type_name; */?></option>
											--><?php /*} */?>
										</select>
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3">
									<label for="validationCustomUsername" class="col-form-label">Product Name<span class="lbl-star">*</span></label>
									<div class="input-group">
										<input type="text" name="plan_name" class="form-control" placeholder="Enter Product Name" aria-describedby="inputGroupPrepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
										<div class="error"><span class="planerror"></span></div>
									</div>
								</div>
								<!--<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Payment Modes Applicable</label>
								<div class="input-group">
									<select class="select2 form-control" name="payment_modes[]" multiple="multiple">
										<option value="">Select Payment Modes Applicable</option>
										<?php foreach ($datalist->payment_modes as $mode) { ?>
											<option value="<?php echo $mode->payment_mode_id; ?>"><?php echo $mode->payment_mode_name; ?></option>
										<?php } ?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>-->
                            </div>
							<div class="row">


								<div class="control-group form-group col-md-6">
                                    <label class="col-form-label">COI Generation</label>
                                    <select class="form-control" id="coi_type" name="coi_type">
                                        <option value="1" >System Generated</option>
                                        <option value="2">IC Generated</option>
                                    </select>
                                </div>
                                    
                               
                                <div class="control-group form-group col-md-6 mb-3" >
                                    <label class="col-form-label" >Select Gender Specific</label>
	                                <div class="input-group">
	                                    <div class="form-check custom-control custom-checkbox"> 
	                                    	<input type="radio" class="form-check-input custom-control-input" name="gender" value="M" id="gender_m" >
	                                    	<label class="form-check-label custom-control-label" for="gender_m"> Male </label> 
	                                    </div> 
	                                    <div class="form-check custom-control custom-checkbox"> 
	                                    	<input type="radio" class="form-check-input custom-control-input" name="gender" value="F" id="gender_f" >
	                                    	<label class="form-check-label custom-control-label" for="gender_f"> Female </label> 
	                                    </div>
	                                    <div class="form-check custom-control custom-checkbox"> 
	                                    	<input type="radio" class="form-check-input custom-control-input" name="gender" value="B" id="gender_b" checked>
	                                    	<label class="form-check-label custom-control-label" for="gender_b"> Both </label> 
	                                    </div>
	                                </div>
                                    
                                </div>
                                
								
                            </div>
                            <div class="row">
                            	<div class="control-group form-group col-md-4 mb-3">
                                	<div class="form-check custom-control custom-checkbox"> 
                                    	<input type="checkbox" class="form-check-input custom-control-input" name="pan_mandatory" value="1" id="pan_mandatory" checked >
                                    	<label class="form-check-label custom-control-label" for="pan_mandatory"> Is Pan Card Mandatory? </label> 
                                    </div>
                                    <div class="form-check custom-control custom-checkbox"> 
                                    	<input type="checkbox" class="form-check-input custom-control-input" name="nominee_mandatory" value="1" id="nominee_mandatory" checked >
                                    	<label class="form-check-label custom-control-label" for="nominee_mandatory"> Is Nominee Details Mandatory? </label> 
                                    </div> 
                                </div>

                                <div class="control-group form-group col-md-4 mb-3" id="coi_download">
                                	<div class="form-check custom-control custom-checkbox"> 
                                    	<input type="checkbox" class="form-check-input custom-control-input" name="coi_download" value="1" id="coi_download_checkbox"  >
                                    	<label class="form-check-label custom-control-label" for="coi_download_checkbox"> Is COI download from api? </label> 
                                    </div> 
                                </div>
                                <div class="control-group form-group col-md-4">
                                    <label class="col-form-label">Single/Proposal wise COI</label>
                                    <select class="form-control" id="is_single_coi" name="is_single_coi">
                                        <option value="1"  >Single COI</option>
                                        <option value="0">Proposal wise COI</option>
                                    </select>
                                </div>
                            </div>
                                <div class="control-group form-group col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">COI start series<span class="lbl-star coi_type_depend">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="coi_start_series" value="<?php echo $datalist->details[0]->coi_start_series; ?>" class="form-control" aria-describedby="inputGroupPrepend" >
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                        <div class="error"><span class="planerror"></span></div>
                                    </div>
                                </div>
                                <div class="control-group form-group col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Series counter digit<span class="lbl-star coi_type_depend">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="series_digit_count" value="<?php echo $datalist->details[0]->series_digit_count; ?>" class="form-control" aria-describedby="inputGroupPrepend" >
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                        <div class="error"><span class="planerror"></span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="creaditor_name" class="col-form-label">Duplicate COI Number allow</label>

                                    <input id="duplicate_coi_allow"  class="mt-1" name="duplicate_coi_allow" type="checkbox" value="1" aria-describedby="inputGroupPrepend" />

                                </div>

								<div class="control-group form-group col-md-12">
									<label for="validationCustomUsername" class="col-form-label"><span>Payment Modes/Workflow<span class="lbl-star">*</span></span></label>
									<div class="controls">
										<table id="tbl_paymentmodes" class="responsive display table table-bordered">
											<thead>
												<tr>
													<th>Payment Mode</th>
													<th>Workflow</th>
													<th class="text-center"><button type="button" class="addPaymentMode"><i class="fa fa-plus"></i></button></th>
												</tr>
											</thead>
											<tbody>

												<tr class="paymentmode_tr">
													<td>
														<select class="select2 form-control payment_modes" name="payment_modes[]" onchange="changeworkflow(this.value)" placeholder="Select">
															<option value="">Select</option>
															<?php foreach ($datalist->payment_modes as $mode) { ?>
																<option value="<?php echo $mode->payment_mode_id; ?>"><?php echo $mode->payment_mode_name; ?></option>
															<?php } ?>
														</select>
													</td>
													<td>
														<select class="select2 form-control" id="payment_workflow" name="payment_workflow[]" placeholder="Select">
															<!--<option value="">Select Payment Modes Applicable</option>-->
															<option value="">Select</option>
															<?php

                                                            foreach ($datalist->payment_workflows as $workflow) { ?>
																<option value="<?php echo $workflow->payment_workflow_master_id; ?>"><?php echo $workflow->workflow_name; ?></option>
															<?php } ?>
														</select>
													</td>
													<td class="text-center">
														<button type="button" class="remove"><i class="fa fa-remove"></i></button>
													</td>
												</tr>

											</tbody>
										</table>
									</div>
								</div>
								<div class="row">
								
									<div class="control-group form-group col-md-4 mb-3 payment_first">
										<label class="col-form-label" >Is Payment first journey? </label>
		                                <div class="input-group">
		                                    <div class="form-check custom-control custom-checkbox"> 
		                                    	<input type="checkbox" class="form-check-input custom-control-input" name="payment_first" value="1" id="payment_first" >
		                                    	<label class="form-check-label custom-control-label" for="payment_first"> Payment First </label> 
		                                    </div> 
		                                </div>
		                                
		                            </div>

			                        <div class="control-group form-group col-md-6 payment_page">
			                                <label class="col-form-label">Payment Page After</label>
			                                <select class="form-control" id="payment_page" name="payment_page">
			                                    <option value="1" >Generate quote page</option>
			                                    <option value="2">Proposer Page</option>
			                                    <option value="3">Insured Member Page</option>
			                                </select>
			                            </div>
			                        </div>
			                    </div>

							</div>


							<div class="row mt-4" id="addplanbtn">
								<div class="col-md-1 col-6 text-left">
									<button class="btn smt-btn">Save</button>
								</div>
								<div class="col-md-2 col-6 text-right">
									<!-- <button class="btn cnl-btn">Cancel</button> -->
									<a href="<?php echo base_url(); ?>products" class="btn cnl-btn">Cancel</a>
								</div>
							</div>
						</div>
					</form>
				<?php } else { ?>

					<div class="card-body">
						<div class="row col-md-12">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Plan Name</label>
								<div class="input-group">
									<?php echo $datalist->details[0]->plan_name; ?>
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Policy Sub Types</label>
								<div class="input-group">
									<?php foreach ($datalist->details as $detail) { ?>
										<label class="label label-primary"><?php echo $detail->policy_sub_type_name; ?> </label><br>
									<?php } ?>
								</div>
							</div>

							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Partner Name</label>
								<div class="input-group">
									<label class="label label-primary"><?php echo $datalist->details[0]->creditor_name; ?></label>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<!-- end 1 -->
			</div>
		</div>
	</div>

	<div id="accordion3" class="according accordion-s2 mt-3">
		<div class="card card-member">
			<div class="card-header card-vif">
				<?php if (isset($datalist->plan_id)) { ?>
					<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion45" aria-expanded="false"> <span class="lbl-card">Policy Details - <i class="ti-file"></i></a>
				<?php } ?>
			</div>
			<div id="accordion45" class="collapse card-vis-mar" data-parent="#accordion2" style="">
				<form class="form-horizontal" id="form-policy" method="post" enctype="multipart/form-data">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Policy Sub Type</label>
								<div class="input-group">
									<select id="policySubType" name="policySubType" class="form-control">
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
											if (empty($detail->policy_number)) {
												$inactivecount++;
										?>
												<option value="<?php echo $detail->policy_id; ?>"><?php echo $detail->policy_sub_type_name; ?></option>
										<?php }
										} ?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Policy #</label>
								<div class="input-group">
									<input type="text" id="policyNo" name="policyNo" class="form-control" placeholder="Enter Policy Number" aria-describedby="inputGroupPrepend">
									<div class="error"><span class="policyerror"></span></div>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>

							<?php if (!empty($datalist->details) && count($datalist->details) == 1) { ?>
								<div class="col-md-4" style="display:flex;">
									<input type="checkbox" class="form-check-input custom-control-input hidden" name="mandatory" value="1" id="mandatory_option" checked>
								</div>
								<?php } else {
								if ($inactivecount == 1 && $combocount == 1) {
								?>
									<input type="checkbox" class="form-check-input custom-control-input hidden" name="combo" value="1" id="Combo_option" checked>
								<?php }
								if ($inactivecount == 1 && $mandate == 0 && $combocount == 0) { ?>
									<input type="checkbox" class="form-check-input custom-control-input hidden" name="mandatory" value="1" id="mandatory_option" checked>
								<?php }
								if ($inactivecount == 1 && $mandate > 0 && $combocount == 0) { ?>
									<div class="form-check custom-control custom-checkbox" style="margin-top: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" name="mandatory" value="1" id="mandatory_option"> <label class="form-check-label custom-control-label" for="mandatory_option"> Mandatory </label> </div>
								<?php }
								if ($inactivecount == 1 && $combocount > 1) { ?>
									<div class="form-check custom-control custom-checkbox" style="margin-top: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" name="mandatory" value="1" id="mandatory_option"> <label class="form-check-label custom-control-label" for="mandatory_option"> Mandatory </label> </div>
									<div class="form-check custom-control custom-checkbox" id="combo_flag" style="margin-top: 46px; margin-left: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option"> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div>
								<?php }
								if ($inactivecount > 1) { ?>
									<div class="col-md-4" style="display:flex;">
										<div class="form-check custom-control custom-checkbox" style="margin-top: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" name="mandatory" value="1" id="mandatory_option"> <label class="form-check-label custom-control-label" for="mandatory_option"> Mandatory </label> </div>
										<div class="form-check custom-control custom-checkbox" id="combo_flag" style="margin-top: 46px; margin-left: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option"> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div>
									</div>
							<?php }
							} ?>

							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">PDF type</label>
								<div class="input-group">
									<select id="pdf_type" name="pdf_type" class="form-control">
										<option value="">Select</option>
										<option value="1">I</option>
										<option value="2">C1</option>
										<option value="3">C2</option>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>

							<!--
					 <div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Premium Type</label>
						<div class="input-group">
							<select id="premium_type" name="premium_type" class="form-control">
								<option value="1">Absolute</option>
								<option value="0">Per Mile rate</option>
                            </select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
					-->

							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Insurer</label>
								<div class="form-group">
									<select id="masterInsurance" name="masterInsurance" class="form-control">
										<option value="">Select Insurer</option>
										<?php foreach ($datalist->insurers as $insurer) { ?>
											<option value="<?php echo $insurer->insurer_id; ?>" <?php if ($insurer->insurer_id == 1) {
																									echo "selected";
																								} ?>><?php echo $insurer->insurer_name; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Policy Start Date</label>
								<div class="input-group">
									<input class="form-control imgDatepicker datepicker" type="text" value="" id="policyStartDate" name="policyStartDate" autocomplete="off">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>

							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Policy end Date</label>
								<div class="input-group">
									<input class="form-control imgDatepicker datepicker" type="text" value="" id="policyEndDate" name="policyEndDate" autocomplete="off">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Policy Member Count</label>
								<div class="input-group">
									<input type="number" name="membercount" class="form-control membercount" placeholder="Enter Policy Member Count" />
								</div>
							</div>
						</div>

						<div class="row memberlist">
							<!-- row inserted here -->
						</div>

						<div class="row">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Sum Insured</label>
								<div class="input-group">
									<select id="sum_insured_type" name="sum_insured_type" class="form-control valid" aria-describedby="sum_insured_type-error" aria-invalid="false">
										<option value=""> Select Sum Insured Type </option>
										<?php foreach ($datalist->sitypes as $type) { ?>
											<option value="<?php echo $type->suminsured_type_id; ?>"> <?php echo $type->suminsured_type; ?> </option>
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
									<select id="companySubTypePolicy" name="companySubTypePolicy" class="form-control valid" aria-invalid="false">
										<option value="">Select</option>
										<?php foreach ($datalist->sipremiumbasis as $premium) { ?>
											<option value="<?php echo $premium->si_premium_basis_id; ?>"> <?php echo $premium->si_premium_basis; ?> </option>
										<?php } ?>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
						</div>


						<!-- flat -->
						<div class="col-md-12" id="add_si_tbody" style="display: none;">
							<div class="row lbl-body mt-1">
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Sum Insured</label>
									<div class="input-group">
										<input type="number" placeholder="Enter Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Premium</label>
									<div class="input-group">
										<input type="number" placeholder="Enter Premium" class="form-control" name="premium_opt[]" autocomplete="off" step="0.01">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3 col-12">
									<label for="validationCustomUsername" class="col-form-label">Group Code</label>
									<div class="input-group">
										<input type="text" placeholder="Enter Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off">
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
									<div class="custom-control custom-checkbox">
										<input type="checkbox" name="tax_opt[]" class="taxchk" autocomplete="off" value="1">
										<input type="hidden" class="tax_opt" name="tax_opt[]" value="0" />
										<!-- <label class="custom-control-label" for="istax">Is Taxable</label> -->
										<label for="istax"><b>Is Taxable</b></label>
									</div>
								</div>

								<div class="col-md-1 mb-3 col-12 text-right">
									<!-- <label style="visibility: hidden;" class="mt-2">label</label> -->
									<button type="button" class="btn add-btn" id="btn_add_si_flat">Add <i class="ti-plus"></i></button>
								</div>
							</div>
						</div>

						<!-- 2 upload  Upload Family Construct -->
						<div class="col-md-12" id="fileUploadfamilyDiv" style="display: none;">
							<div class=" row lbl-body">
								<div class="col-md-4">
									<label class="col-form-label">Upload Family Construct</label>
									<br>
									<input type="file" name="ageFile" id="familyConstructFile">
								</div>
								<div class="col-md-1">
									<label class="mt-1" style="visibility: hidden;"></label>
									<button class="btn exp-button">
										<a href="../assets/familyExcel.xls" class="btn-22" download="ageExcel.xls">Download Format <i class="ti-download"></i></a>
									</button>
								</div>
							</div>
						</div>

						<!-- 3 Upload family and age Construct -->
						<div class="col-md-12" id="fileUploadfamilyageDiv" style="display: none;">
							<div class=" row lbl-body">
								<div class="col-md-4">
									<label class="col-form-label">Upload by family and age Construct</label>
									<br>
									<input type="file" name="ageFile" id="agefamilyConstructFile">
								</div>
								<div class="col-md-1">
									<label class="mt-1" style="visibility: hidden;"></label>
									<button class="btn exp-button">
										<a href="../assets/agefamilyExcel.xls" class="btn-22" download="ageExcel.xls">Download Format <i class="ti-download"></i> </a>
									</button>
								</div>
							</div>
						</div>

						<!-- Upload by Age -->
						<div class="col-md-12" id="fileUploadAgeDiv" style="display: none;">
							<div class=" row lbl-body">
								<div class="col-md-4">
									<label class="col-form-label">Upload by Age</label>
									<br>
									<input type="file" name="ageFile" id="ageFile" class="">
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
									<input type="file" name="ageFile" id="ageFile" class="">
								</div>
								<div class="col-md-1">
									<label class="mt-1" style="visibility: hidden;"></label>
									<button class="btn exp-button">
										<a href="../assets/per_mile_rate.xlsx" class="btn-22" download="per_mile_rate.xlsx">Download Format <i class="ti-download"></i></a>
									</button>
								</div>
							</div>
						</div>
						<!-- new added -->

						<div class="row mt-4" id="addpolicybtn">
							<div class="col-md-1 col-12 text-left">
								<input type="text" id="plan_id" name="plan_id" value="<?php echo $datalist->plan_id; ?>" />

								<button class="btn smt-btn">Save</button>
							</div>
							<div class="col-md-2 col-12 text-right">
								<!-- <button class="btn cnl-btn">Cancel</button> -->
								<a href="<?php echo base_url(); ?>products" class="btn cnl-btn">Cancel</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>


</div>

<script>
	$(function() {
		$("#coi_download").css("display", "none");
		$(document).on('change', '#coi_type', function() {
			var coi_type = $(this).val();
			if(coi_type=='2'){
				$("#coi_download").css("display", "block");
				$('.coi_type_depend').hide();
			}else{
				$("#coi_download").css("display", "none");
				$('.coi_type_depend').show();
			}
		});

		$('.payment_first').hide();
		$('.payment_page').hide();
		$(".addPaymentMode").click(function() {
			var index = 1;
			$("#tbl_paymentmodes tbody tr.paymentmode_tr").each(function() {
				index = index + 1;
			});
			$html = '<tr class="paymentmode_tr">' +
				'<td>' +
				'<select class="select2 form-control payment_modes" id="payment_modes' + index + '" name="payment_modes[]"  placeholder="Select">' +
				'<option value="">Select</option>' +
				<?php foreach ($datalist->payment_modes as $mode) { ?> '<option value="<?php echo $mode->payment_mode_id; ?>"><?php echo $mode->payment_mode_name; ?></option>' +
				<?php } ?> '</select>' +
				'</td>' +
				'<td>' +
				'<select class="select2 form-control" id="payment_workflow' + index + '" name="payment_workflow[]" placeholder="Select">' +
				'<option value="">Select</option>' +
				<?php foreach ($datalist->payment_workflows as $workflow) { ?> '<option value="<?php echo $workflow->payment_workflow_master_id; ?>"><?php echo $workflow->workflow_name; ?></option>' +
				<?php } ?> '</select>' +
				'</td>' +
				'<td class="text-center">' +
				'<button type="button" class="remove">' +
				'<i class="fa fa-remove"></i>' +
				'</button>' +
				"</td>" +
				"</tr>";
			$('#tbl_paymentmodes').find("tbody").append($html);
			$("#payment_modes" + index).select2();
			$("#payment_workflow" + index).select2();
		});

		$('#tbl_paymentmodes').on('click', '.remove', function() {
			var table_row = $('#tbl_paymentmodes tbody  tr.paymentmode_tr').length;
			
			if (table_row == '1') {
				alert("Atleast one mode is must. ");
			} else {
				$(this).closest('tr').remove();
			}
			$('.payment_first').hide();
			$('select[name="payment_modes[]"]').each(function() {
	            if ($(this).val()==1) {
	                $('.payment_first').show();
	                
	            }
	        });
	        checkPaymentFirst();

		});

	});

	$("#policyStartDate").datepicker({
		numberOfMonths: 1,
		onSelect: function(selected) {
			var dt = new Date(selected);
			dt.setDate(dt.getDate() + 1);
			$("#policyEndDate").datepicker("option", "minDate", dt);
		}
	});
	$("#policyEndDate").datepicker({
		numberOfMonths: 1,
		onSelect: function(selected) {
			var dt = new Date(selected);
			dt.setDate(dt.getDate() - 1);
			$("#policyStartDate").datepicker("option", "maxDate", dt);
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

	$("#form-plan").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) {
			var act = "<?php echo base_url(); ?>products/AddNew";
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
						displayMsg("success", response.msg);
						$("#body").load("<?php echo base_url(); ?>products/addpolicyview/" + response.data);
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
		console.log(name);
		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>products/checkplanname",
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

		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>products/checkpolicynumber",
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
		policyNo: {
			required: true,
			number: true
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
		policyNo: {
			required: "Please enter policy number.",
			number: "Please enter numbers Only"
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
			var act = "<?php echo base_url(); ?>products/AddPolicyNew";
			var inactivecount = "<?php echo $inactivecount; ?>";
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
						if (inactivecount == 1) {
							window.location = "<?php echo base_url(); ?>products";
						} else {
							$("#body").load("<?php echo base_url(); ?>products/addpolicyview/" + response.data);
						}

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
	$('.membercount').on('change', function() {
		var count = $(this).val();
		var html = "";
		var select = "<?php echo $select; ?>";
		for (var i = 0; i < count; i++) {
			html += "<div class='col-sm-4 form-group'><select data-id='" + i + "'name='member[]' class='form-control memberselect'>" + select + "</select></div>";
			html += "<div class='col-sm-8 form-group'><input class='form-control agefield' type='number' placeholder='Min Age' min='1' max='100' name='minage[]'/> <input type='number' placeholder='Max Age'class='form-control agefield' min='1' max='100' name='maxage[]'/></div>";
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
		newRow += '<div class="row lbl-body">';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label"> Sum Insured</label><div class="input-group"><input type="number" placeholder="Enter Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"> <div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Premium</label><div class="input-group"><input type="number" placeholder="Premium" class="form-control" name="premium_opt[]" autocomplete="off" step="0.01"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Group Code</label><div class="input-group"><input type="text" placeholder="Group Code" class="form-control" name="group_code[]" autocomplete="off"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Group Code For Spouse</label><div class="input-group"><input type="text" placeholder="Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off"><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
		newRow += '<div class="col-md-2 mb-3 col-12"><div class="custom-control custom-checkbox form-check-inline"><input type="checkbox" class="taxchk" autocomplete="off"> <input type="hidden" class="tax_opt" name="tax_opt[]" value="0" /><label for="istax">Is Taxable</label></div></div>';
		newRow += '<div class="del_btn_opt"><a>Delete<i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></div>';
		newRow += '</div>';

		$("#add_si_tbody").append(newRow);
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
    function getPolicySubtype(value){
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>products/getPolicySubtype",
            data: {
                value: value
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    html ='';
                    $.each(response.data, function (i) {
                       // console.log(response.data[i].policy_sub_type_id);
                        html += '<option value="'+response.data[i].policy_sub_type_id+'">'+response.data[i].policy_sub_type_name+'</option>';
                    });
                    $('#policy_sub_type').html(html);
                } else {

                }
            }
        });
    }
	$("#companySubTypePolicy").on("change", function() {
		var str = $(this).val();
		if (str == "2") {
			$("#fileUploadfamilyDiv").show();
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display", "none");
		} else if (str == "1") {
			$("#add_si_tbody").css("display", "block");
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#fileUploadAgeDiv").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display", "none");
		} else if (str == "3") {
			$("#fileUploadfamilyDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").hide();
			$("#fileUploadfamilyageDiv").css("display", "block");
		} else if (str == "4") {
			$("#fileUploadfamilyageDiv").css("display", "none");
			$("#add_si_tbody").css("display", "none");
			$("#fileUploadAgeDiv").show();
			$("#fileUploadfamilyDiv").css("display", "none");
		}
	});

	$(document).on('change', '.taxchk', function() {
		if ($(this).is(':checked')) {
			$(this).parent().find(".tax_opt").val(1);
		} else {
			$(this).parent().find(".tax_opt").val(0);
		}
	});



	$(document).on('change', '.payment_modes', function() {
		$('.payment_first').hide();
		$('select[name="payment_modes[]"]').each(function() {
            if ($(this).val()==1) {
                $('.payment_first').show();
                
            }
        });
        checkPaymentFirst();
	});

	function checkPaymentFirst(){
		$('.payment_page').hide();
		if ($('#payment_first').is(':checked') && $('.payment_first').is(':visible')) {
            $('.payment_page').show();
        }
	}

	$(document).on('click', '#payment_first', function() {
		checkPaymentFirst();
	});

	function changeworkflow(value) {
	
    if(value ==2){

       // $("#payment_workflow option[2]").prop('disabled', true);
         var id = $(this).parent('select[name*="payment_workflow"]').prop('id');
         var options = $('select[name*="payment_workflow"]:not(#' + id + ') option[value!=2]');
         options.prop('disabled', 'true');
    }else{
        $("#payment_workflow option").prop('disabled', false);
    }
    }
</script>