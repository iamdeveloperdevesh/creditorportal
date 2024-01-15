<?php
//print_r($datalist['subtype_array']);

//echo $datalist->details[0]->premium_type;

/*echo 123;die;
if (isset($datalist->plan_id)) {
	echo "<pre>";print_r($datalist);
	//echo $datalist
	//echo $datalist->details[0]->policy_number;
}*/
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
<div class="col-md-10 scroll" id="body">
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
					<input type="hidden" name="plan_id" id="plan_id" value="<?php echo $id; ?>">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Select Partner</label>
								<div class="input-group">
									<select class="form-control" name="creditor">
										 <option value="" selected disabled>Fyntune</option>
									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
							  <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy Type</label>
                                <div class="input-group">
                                    <select class="form-control"  id='policy_type'name="policy_type">

                                    </select>

                                </div>
                            </div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Select Sub Policy Type</label>
								<div class="input-group">
									<select class="select2 form-control" id="policy_sub_type" name="policy_sub_type[]" multiple="multiple">

									</select>
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
									</div>
								</div>
							</div>
							<div class="col-md-3 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Plan Name <span class="lbl-star">*</span></label>
								<div class="input-group">
									<input type="text" name="plan_name" id="plan_name" value="" class="form-control" aria-describedby="inputGroupPrepend" required="">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
									</div>
									<div class="error"><span class="planerror"></span></div>
								</div>
							</div>
							 <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Product Code</label>
                                    <input class="form-control" type="text" value="" id="product_code" name="product_code" autocomplete="off" >
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Group Codes</label>
                                    <input type="file" name="GroupCodeFile" id="GroupCodeFile" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" require>
                                </div>
                            </div>

                            <div class="col-md-3" style="margin-top: 20px;">
                                <a href="../assets/group_code_template.xlsx" class="btn download-btn" download="group_code_template.xlsx">Download Format </a>
                            </div>
							<div class="col-md-4 mb-3">
								<label for="validationCustomUsername" class="col-form-label">Payment Modes Applicable</label>
								<div class="input-group">
									<select class="select2 form-control" id="payment_modes" name="payment_modes[]" multiple="multiple">
										<option value="">Select Payment Modes Applicable</option>
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
					<input type="hidden" name="plan_id_edit" id="plan_id_edit" value="<?php echo $id; ?>">
						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Plan Name </label>
							<div class="input-group inp-frame2">
								<?php
								echo $datalist['product_name'];

								?>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Policy Type</label>
							<div class="input-group">

									<label class="label label-primary  inp-frame2"><?php echo $datalist['policy_name']; ?> </label><br>

							</div>
						</div>
						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Policy Sub Type</label>
							<div class="input-group">

									<label class="label label-primary  inp-frame2"><?php echo $datalist['policy_subtype_name']; ?> </label><br>

							</div>
						</div>

						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Partner Name</label>
							<div class="input-group">
								<label class="label label-primary  inp-frame2">Fyntune</label>
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
								<select id="policySubType" name="policySubType" class="form-control" required onchange="getPlanDeatilsSubtype()">
									<option value="">Select</option>
									<?php
									$combocount = 0;
									$mandate = 0;
									$inactivecount = 0;

									foreach ($datalist['subtype_id_array'] as $key=> $detail) {

									?>
										<option value="<?php echo $detail; ?>"><?php echo $datalist['subtype_name_array'][$key]; ?></option>
									<?php } ?>
								</select>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
								</div>
							</div>
						</div>
						<input class="form-control " type="hidden" id="policy_subType_id" name="policy_subType_id">
						<input class="form-control " type="hidden" id="policy_parent_id" name="policy_parent_id">

						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Policy #</label>
							<div class="input-group">
								<input class="form-control policyno" type="text" id="policyNo" name="policyNo">

								<div class="error"><span class="policyerror"></span></div>
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
								</div>
							</div>
						</div>
						  <div class="col-md-4" style="display:flex;">
                                    <div style="margin-top: 46px;"> <input type="radio" name="mandatory_optional" value="1" id="mandatory_option"> <label  for="mandatory_option"> Mandatory </label> </div>
                                    <div style="margin-top: 46px;"> <input type="radio"  name="mandatory_optional" value="0" id="optional_option"> <label  for="opttional_option"> Optional </label> </div>
                                    <div class="form-check custom-control custom-checkbox" id="combo_flag" style="margin-top: 46px; margin-left: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option"> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div>
                                </div>

  <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">PDF type</label>
                                <div class="input-group">
                                    <select id="pdf_type" name="pdf_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1">I</option>
                                        <option value="2">C1</option>
                                        <option value="3">C2</option>
                                    </select>

                                </div>
                            </div>
						<div class="col-md-4 mb-3">
							<label for="validationCustomUsername" class="col-form-label">Insurer</label>
							<div class="form-group">
								 <select id="masterInsurance" name="masterInsurance" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1">Apollo</option>
                                        <option value="2">Tata</option>
                                        <option value="3">Religare</option>
                                        <option value="4">TATA- AIG</option>
                                    </select>
							</div>
						</div>



					<!--<div class="col-md-6 mb-3 ml-1 row">
						<?php /*if (isset($datalist->policydetails) && $datalist->policydetails[0]->is_optional == 0) {
							$mandatory_checked = 'checked';
							$optional_checked = '';
						} else {
							$mandatory_checked = '';
							$optional_checked = 'checked';
						}
						*/?>
						<label style="visibility: hidden;" class="display-sm-lbl col-md-12">space</label>
						<div class="form-check-inline custom-control custom-radio">
							<input type="radio" class="custom-control-input" name="mandatory" value="1" id="mandatory_option" <?php /*echo $mandatory_checked */?>>
							<label class="custom-control-label" for="mandatory_option"> Mandatory </label>
				      	</div>
							<div class="form-check-inline custom-control custom-radio">
							<input type="radio" class="custom-control-input" name="mandatory" value="0" id="optional_option" <?php /*echo $optional_checked */?>>
							<label class="custom-control-label" for="optional_option"> Optional </label>
						</div>
						<div class="col-md-1">
						<div class="form-check custom-control custom-checkbox" id="combo_flag"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option" <?php /*if (isset($datalist->policydetails) && $datalist->policydetails[0]->is_combo == 1) {
		      																																																																	echo "checked";
																																																															} */?>> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div></div>
					</div>-->

					<!--<div class="col-md-6">
						<?php /*if (count($datalist->details) > 1) : */?>
							<div style="display: none;" id="mandatory_if_not_selected_section">
								<label for="mandatoryIfSelectedRules" class="col-form-label">Mandatory If Not Selected </label>
								<select id="mandatory_if_not_selected" name="mandatory_if_not_selected[]" class="select2 form-control" multiple="multiple">

								</select>
							</div>
						<?php /*endif; */?>
					</div>-->
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
<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Policy Start Date</label>
						<div class="input-group">
							<input class="form-control  " type="date" id="policyStartDate" name="policyStartDate" autocomplete="off" >

							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>

					<div class="col-md-4 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Policy end Date</label>
						<div class="input-group">
							<input class="form-control  " type="date" id="policyEndDate" name="policyEndDate" autocomplete="off" >
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
							 <select id="sum_insured_type" name="sum_insured_type" class="form-control valid" aria-describedby="sum_insured_type-error" aria-invalid="false">
                                        <option value=""> Select </option>
                                        <option value="individual">Individual</option>
                                        <option value="familyGroup">Family Cover</option>
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
						 <select id="companySubTypePolicy" name="companySubTypePolicy" onchange="companySubTypePolicyOnchange()" class="form-control valid" aria-invalid="false">
                                        <option value="">Select</option>
                                        <option value="flate">Flat Wise</option>
                                        <option value="family_construct">Family Construct Wise</option>
                                        <option value="family_construct_age">Family Construct and Age Wise</option>
                                        <option value="memberAge">Member Age Wise</option>
                                        <option value="permilerate" > Per Mile Rate </option>
                                        <option value="deductable"> Family Construct Deductable </option>
                                        <option value="perdaytenure"> Per Day Tenure  </option>

                                    </select>
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
					<?php
					//echo $datalist->premium_basis[0]->si_premium_basis_id;exit;
					if($datalist->premium_basis[0]->si_premium_basis_id == 5){ ?>
					    <div class="col-md-4 mb-3" id="default_sumInsuredDiv" >
						<label for="validationCustomUsername" class="col-form-label">Default Sum Insured</label>
						<div class="input-group">
                            <input type="number" id="default_sumInsured" value="<?php echo $datalist->policy_premium[0]->sum_insured; ?>"  name="default_sumInsured" class="form-control" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
							</div>
						</div>
					</div>
				<?php 	} else{ ?>
					  <div class="col-md-4 mb-3" id="default_sumInsuredDiv" style="display: none">
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


							<div class="row lbl-body mt-1 editDetails">

							</div>
						<div class="row lbl-body addDetails">
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
								<a href="../assets/familyExcel.xls" type="button" class="btn-22" download="familyExcel.xls">Download Format <i class="ti-download"></i> </a>
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
								<a href="../assets/agefamilyExcel.xls" type="button" class="btn-22" download="agefamilyExcel.xls">Download Format <i class="ti-download"></i></a>
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
								<a href="../assets/ageExcel.xls" type="button" class="btn-22" download="ageExcel.xls">Download Format <i class="ti-download"></i> </a>
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
								<a href="../assets/per_mile_rate.xlsx" type="button" class="btn-22" download="per_mile_rate.xlsx">Download Format <i class="ti-download"></i> </a>
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
								<a href="../assets/familyExcelWithDeductable.xlsx" type="button" class="btn-22" download="familyExcelWithDeductable.xlsx">Download Format <i class="ti-download"></i> </a>
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
								<a href="../assets/per_day_tenure.xlsx" type="button" class="btn-22" download="per_day_tenure.xlsx">Download Format <i class="ti-download"></i></a>
							</button>
						</div>
					</div>
				</div>
				<!-- new added -->

				<div class="row mt-3 col-md-12" id="addpolicybtn">
					<div class="col-md-1 col-6 text-left">

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
<?php
$select = "<option value=''>Select Member</option>";

foreach ($members as $member) {
	$select .= "<option value='$member->id'>$member->member_type</option>";
}
?>
<script>
	var adult_members = JSON.parse('<?php echo json_encode($adult_members); ?>');
	$(document).ready(function() {
	  getPolicyType();
getPaymentMode();
	});
	function getPolicyType() {
	     $.ajax({
            type: "POST",
            url: "get_policy_creation_det",
            success: function (e) {
                e = JSON.parse(e);
                if (e.policyType) {
                    var policyType = e.policyType;
                    $("#policy_type").empty();
                    $("#policy_type").html("<option value=''> Select </option>");
                    policyType.forEach(function (e) {
                        if (e.policy_type_id != 3)
                            $("#policy_type").append("<option value='" + e.policy_type_id + "'>" + e.policy_name + "</option>");
                    });

                    getPlanDetails();

                }
            },
            error: function () {

            }
        });

	}
	function getPlanDeatilsSubtype() {
	    var parent_id=$("#plan_id_edit").val();
	    var subtype_id=$("#policySubType").val();
	  var  bs_url='<?php echo base_url(); ?>';
	  $.ajax({
            type: "POST",
            url: bs_url+"tele_product_config/getPlanDeatilsSubtype",
            dataType:'json',
            data:{subtype_id,parent_id},
            success: function (res) {
                var data=res.data;
                var data2=res.data2;
                $('.smt-btn').attr('disabled',false);
                $('#policyNo').val(data.policy_no);
                $('#policy_subType_id').val(data.policy_detail_id);
                $('#policy_parent_id').val(data.parent_policy_id);
                $('#pdf_type').val(data.pdf_status);
                $('#masterInsurance').val(data.insurer_id);
                $('#policyStartDate').val(data.start_date);
                $('#policyEndDate').val(data.end_date);
                $('#sum_insured_type').val(data.sum_insured_type);
                $('#companySubTypePolicy').val(data.suminsured_type);
                if(data.is_optional == 0){
                $('#mandatory_option').prop('checked',true);
                }else{
                   $('#optional_option').prop('checked',true);
                }
                 if(data.is_combo == 0){
                      $('#Combo_option').prop('checked',false);
                 }else{
                      $('#Combo_option').prop('checked',true);
                 }
                companySubTypePolicyOnchange();
                var max_count=data.max_count;
                   var arr=max_count.split(",");
                   var adult=arr[0];
                   var child=arr[1];
                   var total=(adult)*1+(child)*1;

                   $(".membercount").val(total);
                   $("#adult-count-display").val(adult);
                   $("#kids-count-display").val(child);
                  // memberCountChange();
                   generateMemberSelects(data2);
                   getsumInsuredDataSubtype(data);
            },
            error: function () {

            }
        });
	}
	function getsumInsuredDataSubtype(data) {
	    var sum_insuredString=data.sum_insured;
	    var premiumString=data.premium;

	    var arr=sum_insuredString.split(",");
	    var arr2=premiumString.split(",");
	    var count=arr.length;
	  $('.editDetails').show();
	  $('.addDetails').hide();
	    html='';
	    for(var i=0;i<count;i++){

          html += `<div class="row "><div class="col-md-3 mb-3 col-12">
<label for="validationCustomUsername" class="col-form-label">Enter Sum Insured</label>
<div class="input-group">
<input type="number" class="form-control" value="${arr[i]}" name="sum_insured_opt1[]" autocomplete="off">
<div class="input-group-prepend">
<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
</div>
</div>
</div>
<div class="col-md-3 mb-3 col-12">
<label for="validationCustomUsername" class="col-form-label">Enter Premium rate</label>
<div class="input-group">
<input type="number" placeholder="Premium" class="form-control" value="${arr2[i]}" name="premium_opt[]" autocomplete="off" step="0.01">
<div class="input-group-prepend">
<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
</div>
</div>
</div>
<div class="col-md-3 mb-3 col-12">
<label for="validationCustomUsername" class="col-form-label">Enter Group Code</label>
<div class="input-group">
<input type="text" placeholder="Group Code" class="form-control" name="group_code[]" autocomplete="off" value="">
<div class="input-group-prepend">
<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
</div>
</div>
</div>
<div class="col-md-3 mb-3 col-12">
<label for="validationCustomUsername" class="col-form-label">Enter Group Code For Spouse</label>
<div class="input-group">
<input type="text" placeholder="Group Code For Spouse" class="form-control" name="group_code_spouse[]" autocomplete="off" value="">
<div class="input-group-prepend">
<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
</div>
</div>
</div>
<div class="col-md-2 mb-3 col-12">
<!-- <label style="visibility: hidden;" class="mt-3">label</label> -->

<div class="custom-control custom-checkbox form-check-inline">
<input type="checkbox" class="taxchk"  autocomplete="off">
<input type="hidden" class="tax_opt" name="tax_opt[]" value="" />
<label for="istax">Is Taxable</label>
</div>
</div>`;

if(i ==0){
    html += `<div class="col-md-1 mb-3 col-12 text-right">
<!-- <label style="visibility: hidden;" class="mt-2">label</label> -->
<button class="btn add-btn" id="btn_add_si_flat1" onclick="addBtn()" type="button">Add <i class="ti-plus"></i></button>
</div>`;
}else{
    html +=`<div class="del_btn_opt"><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></div>`

}
html +=`</div>`;

	    }

$('.editDetails').html(html);
	}
	function addBtn(){
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
	}
	
	  function memberCountChange(){
	  //  alert();
         let member_count = $('.membercount').val();
        let current_adult_row_count = $("#adult-members-list .adult-row").length;

        if (current_adult_row_count > member_count) {
            $("#adult-members-list .adult-row:gt(" + (member_count - 1) + ")").remove();
        }

        if (!parseInt(member_count)) {
            $("#adult-members-list").hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
            return;
        }
        $("#adult-members-list").show();
        populateOptionsForEmptyAdults();
        if (canAddMoreAdultMembers()) {
            $('#add-another-member').show();
            $('#children-members-list').show();
            enableInnerFields('children-members-list');
        } else {
            $('#add-another-member').hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
        }
        updateCounters();
    }
function companySubTypePolicyOnchange(){
     var str = $('#companySubTypePolicy').val();
        $("#fileUploadPerMileRate").css("display", "none");
        $("#fileUploadFamilyDeductable").css("display", "none");
        $("#perDayTenureDiv").css("display", "none");
        if (str == "family_construct") {
            $("#fileUploadfamilyDiv").show();
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadfamilyageDiv").css("display", "none");

        } else if (str == "flate") {
            $("#add_si_tbody").css("display", "block");
            $("#fileUploadfamilyDiv").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadfamilyageDiv").css("display", "none");
        } else if (str == "family_construct_age") {
            $("#fileUploadfamilyDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").hide();
            $("#fileUploadfamilyageDiv").css("display", "block");
        } else if (str == "memberAge") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "permilerate") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadPerMileRate").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "deductable") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadFamilyDeductable").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "perdaytenure") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#perDayTenureDiv").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        }
}
    $(document).on('change', '.membercount', function () {
        let member_count = $(this).val();
        let current_adult_row_count = $("#adult-members-list .adult-row").length;

        if (current_adult_row_count > member_count) {
            $("#adult-members-list .adult-row:gt(" + (member_count - 1) + ")").remove();
        }

        if (!parseInt(member_count)) {
            $("#adult-members-list").hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
            return;
        }
        $("#adult-members-list").show();
        populateOptionsForEmptyAdults();
        if (canAddMoreAdultMembers()) {
            $('#add-another-member').show();
            $('#children-members-list').show();
            enableInnerFields('children-members-list');
        } else {
            $('#add-another-member').hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
        }
        updateCounters();
    });
	  $(document).on('click', '#add-another-member', function () {
        let adult_row_html = $('.adultmembers .adult-row').html();
        $('.adultmembers').append(`<div class="row adult-row">${adult_row_html}</div>`);
        $('.adult-row').last().find(':input:not([type=hidden])').removeAttr('value');
        $('.adult-row').last().find('.adult-member-select').html('');
        populateOptionsForEmptyAdults();

        if (!canAddMoreAdultMembers()) {
            $(this).hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
        }
        updateCounters();
    });

    $(document).on('click', '.delete-member-btn', function () {
        let adult_row_count = $('.adult-row').length;

        if (adult_row_count <= 1) {
            alert("Policy should have atleast one member type");

            return;
        }
        $(this).closest('.adult-row').remove();
        if (canAddMoreAdultMembers()) {
            $('#add-another-member').show();
            $('#children-members-list').show();
            enableInnerFields('children-members-list');
        } else {
            $('#add-another-member').hide();
            $('#children-members-list').hide();
            disableInnerFields('children-members-list');
        }
        updateCounters();
    });

    function canAddMoreAdultMembers() {
        let adult_row_count = $('.adult-row').length;
        let member_count = parseInt($('[name="membercount"]').val());

        return adult_row_count < member_count;
    }

    function updateCounters() {
        let adult_rows = $('.adult-row').length;
        $('#adult-count-display').val(adult_rows);

        let member_count = parseInt($('[name="membercount"]').val());
        let child_count = member_count - adult_rows;
        $('#kids-count-display').val(child_count);
    }

    function populateOptionsForEmptyAdults() {
        let selected_members = [];

        $('.adult-row .adult-member-select').each(function (index, element) {
            let value = $(this).val();
            if (selected_members.indexOf(value) === -1) {
                selected_members.push(value);
            }
        });

        let unselected_adult_members = adult_members.filter(function (member) {
            return selected_members.includes(member.id) == false;
        });

        $('.adult-row .adult-member-select').each(function (index, element) {

            let option_length = $(this).find('option').length;

            if (option_length != 0) {
                return;
            }

            let options_html = "";

            if (unselected_adult_members.length <= 0) {
                $('.adult-row').last().remove();
                displayMsg('error', 'No more member types available');
            }
            unselected_adult_members.forEach(member => {
                options_html += `<option value="${member.fr_id}">${member.fr_name}</option>`;
            });

            $(this).html(options_html);
        });
    }
	function getSubPolicyType(){

	    $.post("get_policy_subType_fyntune", { "policy_type_id": $("#policy_type").val() }, function (e) {

            e = JSON.parse(e);
            var policySubType = e.policySubType;
            $("#policy_sub_type").empty();
            $("#policy_sub_type").html("<option value=''>Select</option>");
            policySubType.forEach(function (e) {

                $("#policy_sub_type").append("<option value='" + e.policy_sub_type_id + "'>" + e.policy_sub_type_name + "</option>");
            });
             $('#policy_sub_type').val(policySubtype.split(",")).change();
           // getPlanDetails();
            $("#policy_sub_type").select2( {
                    columns: 1,
                    placeholder: 'Select',
                    search: true
                }

            );
        });
	}
	 $("#policy_type").change(function () {

        $.post("tele_product_config/get_policy_subType_fyntune", { "policy_type_id": $("#policy_type").val() }, function (e) {

            e = JSON.parse(e);
            var policySubType = e.policySubType;
            $("#policy_sub_type").empty();
            $("#policy_sub_type").html("<option value=''>Select</option>");
            policySubType.forEach(function (e) {

                $("#policy_sub_type").append("<option value='" + e.policy_sub_type_id + "'>" + e.policy_sub_type_name + "</option>");
            });


            $("#policy_sub_type").select2( {
                    columns: 1,
                    placeholder: 'Select',
                    search: true
                }

            );
        });
    });
	let policySubtype='';
	let policy_payment_modes='';
	function getPlanDetails(){
	    var plan_id=$('#plan_id').val();
	     $.ajax({
                url: "getPlanDetails",
                async: false,
                type: "POST",
                data:{plan_id},
                dataType: 'json',
                success: function(res){
                    console.log(res);
                    $("#policy_type").val(res['policy_type_id']);
                    $("#plan_name").val(res['product_name']);
                    $("#product_code").val(res['product_code']);
                    policySubtype=res['policy_subtype_id'];
                    policy_payment_modes=res['policy_payment_modes'];
                     getPaymentMode();
                     getSubPolicyType();

                }
            });
	}
  function getPaymentMode(){
	    console.log('pooja');
	    console.log(policy_payment_modes);
        $.ajax({
            type: "POST",
            url: "getPaymentModes",
            success: function (e) {
                e = JSON.parse(e);
                var PayM = e;
                $("#payment_modes").empty();
                $("#payment_modes").html("<option value=''> Select </option>");
                PayM.forEach(function (e) {
                    $("#payment_modes").append("<option value='" + e.id + "'>" + e.payment_mode_name + "</option>");
                });
                $('#payment_modes').val(policy_payment_modes.split(",")).change();
            },
            error: function () {

            }
        });
    }
	$("#continuebtn").click(function() {

	//	$("#body").load("<?php echo base_url(); ?>products/updatepolicyview/<?php echo $datalist->plan_id; ?>");

		//window.location = "<?php echo base_url(); ?>products/updatepolicyview/<?php echo $datalist->plan_id; ?>";

		//$("#body").remove();
		//$(this).parents('#body').remove();
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
			var act = "update_product_detail";
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

					if (response.result == true) {
					//	console.log(response.data);
						displayMsg("success", response.msg);
						window.location.replace("<?php echo base_url(); ?>tele_product_config/edit_details_after/" + response.parent_id);

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
		     var  bs_url='<?php echo base_url(); ?>';
			var act = bs_url+"tele_product_config/UpdatePolicyNew";
			$("#form-policy").ajaxSubmit({
				url: act,
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				beforeSubmit: function(arr, $form, options) {
					//$("#addpolicybtn").hide();
				},
				success: function(response) {
					console.log(response);
					if (response.status_code == 200) {
						console.log(response.data);
						//$("#addpolicybtn").show();
						displayMsg("success", response.Metadata.Message);
					//	$("#body").load("<?php echo base_url(); ?>products/updatepolicyview/" + response.data);

					} else {
						//$("#addpolicybtn").show();
						displayMsg("error", response.Metadata.Message);
						return false;
					}
				}
			});
		}
	});
	$("#btn_add_si_flat").click(function() {
	    console.log(123);
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

</script>



<script>


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
        $("#fileUploadPerMileRate").css("display", "none");
        $("#fileUploadFamilyDeductable").css("display", "none");
        $("#perDayTenureDiv").css("display", "none");
        if (str == "family_construct") {
            $("#fileUploadfamilyDiv").show();
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadfamilyageDiv").css("display", "none");

        } else if (str == "flate") {
            $("#add_si_tbody").css("display", "block");
            $("#fileUploadfamilyDiv").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadfamilyageDiv").css("display", "none");
        } else if (str == "family_construct_age") {
            $("#fileUploadfamilyDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").hide();
            $("#fileUploadfamilyageDiv").css("display", "block");
        } else if (str == "memberAge") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "permilerate") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadPerMileRate").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "deductable") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#fileUploadFamilyDeductable").show();
            $("#fileUploadfamilyDiv").css("display", "none");
        } else if (str == "perdaytenure") {
            $("#fileUploadfamilyageDiv").css("display", "none");
            $("#add_si_tbody").css("display", "none");
            $("#fileUploadAgeDiv").css("display", "none");
            $("#perDayTenureDiv").show();
            $("#fileUploadfamilyDiv").css("display", "none");
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