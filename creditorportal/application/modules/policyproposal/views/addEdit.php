<?php

$child_count = 0;
$adult_count = 0;

/*$self_dob = $leaddetails->customer_details[0]->dob;
$self_dob = date('Ymd', strtotime($self_dob));

$current_date = date('Ymd');

$self_age = $current_date - $self_dob;
$self_age = str_replace(substr($self_age, -4), '', $self_age);*/

//$proposal_member_id = json_decode(json_encode($proposal_member_id), true);

if (!empty($leaddetails->plan_details)) {
	foreach ($leaddetails->plan_details as $key => $value) {
		if ($value->policy_sub_type_id == 1) {
			foreach ($leaddetails->plan_details[0]->family_construct as $key => $value) {
				$arr_member_type_id = array(1, 2, 3, 4);

				if (in_array($value->member_type_id, $arr_member_type_id)) {
					$adult_count++;
				} else {
					$child_count++;
				}
			}
		}
	}
}

$main_tab_customer_arr = [];

if (isset($leaddetails->customer_details) && !empty($leaddetails->customer_details)) {

	for ($i = 0; $i < count($leaddetails->customer_details); $i++) {

		$main_tab_customer_arr['applicant_id_' . $i] = $leaddetails->customer_details[$i]->customer_id;
		$main_tab_customer_arr['assignment_declartaion'] = $leaddetails->customer_details[$i]->assignment_declaration;
		$main_tab_customer_arr['mode_of_payment'] = $leaddetails->customer_details[$i]->mode_of_payment;
	}
}

$member_count = ['adult_count' => $adult_count, 'child_count' => $child_count];
$insured_member_data['member_count'] = $member_count;

if (isset($leaddetails->member_details)) {

	$insured_member_data['member_details'] = $leaddetails->member_details;
}

$coapplicant_count = $leaddetails->customer_details[0]->coapplicant_no;
//exit;

$tab_disabled = 'disabled';

if (isset($is_only_previewable) && $is_only_previewable) {

	$tab_disabled = '';
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

	.totalpremium {
		cursor: pointer;
		font-size: 20px;
		line-height: 40px;
		margin-right: 15px;
		color: red;
	}

	.error,
	.moberror {
		color: red;
	}

	.error,
	.moberror_customer,
	.moberror_gender {
		color: red;
	}

	.moberror_customer,
	.moberror_gender {
		position: absolute;
		width: 100%;
		left: 0;
		top: 100%;
	}

	.label-primary {
		padding: 5px;
	}

	.collapse.in {
		display: block;
	}

	.collapse {
		display: none;
		/* padding: 15px; */
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

	/* for jqueryui autocomplete starts here*/
	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
		/* add padding to account for vertical scrollbar */
		padding-right: 20px;
	}

	/* IE 6 doesn't support max-height
	* we use height instead, but this forces the menu to always be this tall
	*/
	* html .ui-autocomplete {
		height: 100px;
	}

	/* for jqueryui autocomplete ends here*/
</style>
<div class="col-lg-10 mt-2">
	<input type="hidden" id="coapplicant_tab_id" value="<?php echo $coapplicant_tab_id ?? '' ?>" />
	<input type="hidden" id="is_spouse_age_required" value="<?php echo ($is_spouse_age_required == true) ? 1 : 0; ?>" />
	<input type="hidden" class="pan_added" value="<?php echo (isset($customer->pan) && $customer->pan != NULL) ? 'y' : ''; ?>">
	<div class="card-body pad-0">
		<ul class="nav nav-tabs mb-scroll" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link main-tab active mb-link" id="app1-tab" data-toggle="tab" href="#app1" role="tab" aria-controls="app1" aria-selected="true" data_id="">Applicant </a>
			</li>

			<?php $tab = 0;
			if ($coapplicant_count > 0) {
				for ($cocount = 1; $cocount <= $coapplicant_count; $cocount++) {
					$tab = $cocount + 1;

					if (isset($main_tab_customer_arr['applicant_id_' . $cocount]) && $main_tab_customer_arr['applicant_id_' . $cocount] != '') {

			?>
						<li class="nav-item" data_id="<?php echo $tab; ?>">
							<a class="nav-link main-tab mb-link co-applicant-tab" id="app<?php echo $tab; ?>-tab" data_id="<?php echo $tab; ?>" data-toggle="tab" href="#app<?php echo $tab; ?>" role="tab" aria-controls="app<?php echo $tab; ?>" aria-selected="false">Co-Applicant <?php echo $cocount; ?> </a>
						</li>
					<?php
					} else {

					?>
						<li class="nav-item" data_id="<?php echo $tab; ?>">
							<a class="nav-link main-tab mb-link co-applicant-tab <?php echo $tab_disabled; ?>" id="app<?php echo $tab; ?>-tab" data_id="<?php echo $tab; ?>" data-toggle="tab" href="#app<?php echo $tab; ?>" role="tab" aria-controls="app<?php echo $tab; ?>" aria-selected="false">Co-Applicant <?php echo $cocount; ?> </a>
						</li>
					<?php
					}
					?>
			<?php }
			}
			?>
			<!--   
									<li class="nav-item">
                                        <a class="nav-link" id="app3-tab" data-toggle="tab" href="#app3" role="tab" aria-controls="app3" aria-selected="false">Co-Applicant 2 </a>
                                    </li>
                                     -->

			<li class="nav-item">
				<?php
				if ($tab == 0) {
					$tab++;
				}

				if (isset($main_tab_customer_arr['assignment_declartaion']) && $main_tab_customer_arr['assignment_declartaion'] != '') {

				?>
					<a class="nav-link main-tab assignment-declaration mb-link" id="app<?php echo $tab + 1; ?>-tab" data_id="" data-toggle="tab" href="#app<?php echo $tab + 1; ?>" role="tab" aria-controls="app<?php echo $tab + 1; ?>" aria-selected="false">Assignment Declaration</a>
				<?php
				} else {

				?>
					<a class="nav-link main-tab mb-link assignment-declaration <?php echo $tab_disabled; ?>" id="app<?php echo $tab + 1; ?>-tab" data_id="" data-toggle="tab" href="#app<?php echo $tab + 1; ?>" role="tab" aria-controls="app<?php echo $tab + 1; ?>" aria-selected="false">Assignment Declaration</a>
				<?php
				}
				?>
			</li>
			<li class="nav-item">
				<?php

				if (isset($main_tab_customer_arr['mode_of_payment']) && $main_tab_customer_arr['mode_of_payment']) {

				?>
					<a class="nav-link main-tab mb-link payment-tab" id="app<?php echo $tab + 2; ?>-tab" data_id="" data-toggle="tab" href="#app<?php echo $tab + 2; ?>" role="tab" aria-controls="app<?php echo $tab + 2; ?>" aria-selected="false">Payment</a>
				<?php
				} else {

				?>
					<a class="nav-link main-tab mb-link payment-tab <?php echo $tab_disabled; ?>" id="app<?php echo $tab + 2; ?>-tab" data_id="" data-toggle="tab" href="#app<?php echo $tab + 2; ?>" role="tab" aria-controls="app<?php echo $tab + 2; ?>" aria-selected="false">Payment</a>
				<?php
				}
				?>
			</li>
			<?php if ($is_only_previewable) : ?>
				<?php
				if (($_SESSION['webpanel']['role_id'] == 5 || $_SESSION['webpanel']['role_id'] == 6 || $_SESSION['webpanel']['role_id'] == 7)) {
				?>
					<li class="nav-item">
						<a class="nav-link mb-link" id="actions-tab" data-toggle="tab" href="#actions-tab-content" role="tab" aria-selected="false">Actions</a>
					</li>
				<?php
				}
				?>
			<?php endif; ?>
		</ul>

		<div class="tab-content mt-3" id="myTabContent">
			<?php $this->load->view('addEdit_load', $insured_member_data) ?>

			<!-- here -->
			<?php
			$tab = 0;
			if ($coapplicant_count > 0) {
				for ($cocount = 1; $cocount <= $coapplicant_count; $cocount++) {
					$tab = 	$cocount + 1;
			?>

					<div class="tab-pane fade" id="app<?php echo $tab; ?>" role="tabpanel" aria-labelledby="app<?php echo $tab; ?>-tab"></div>

					<!-- end form -->

			<?php
				}
			}

			if ($tab == 0) {

				$tab++;
			}
			?>
			<div class="tab-pane fade" id="app<?php echo $tab + 1; ?>" role="tabpanel" aria-labelledby="app<?php echo $tab + 1; ?>-tab">
				<p>
				<div class="card card-member">
					<div class="card-header card-vif">
						<div class="cre-head card-vis">
							<div class="row">
								<div class="col-md-12 col-12">
									<p>Assignment Declaration - <i class="ti-user"></i></p>
								</div>
							</div>
						</div>
					</div>
						<div class="card-body">
							<table class="table table-bordered text-center">
								<thead>
									<tr>
										<th scope="col" style="width: 750px; text-align: left; font-weight: 600;">
											Questionnaire</th>
										<th scope="col" style="font-weight: 600;">
											Answer</th>
									</tr>
								</thead>
								<tbody id="assignment_declaration">
									<tr>
										<td style="text-align:left;font-size: 13px;">

											<?php

											echo html_entity_decode($assigment_declaration);
                                            $checked='';
											$assignment_declaration_answer = '';
											if (isset($leaddetails->customer_details[0]->assignment_declaration)) {

												$assignment_declaration_answer = $leaddetails->customer_details[0]->assignment_declaration;

											}
                                            if($assignment_declaration_answer == ""){
                                                $checked='checked';
                                            }

											?>
										</td>

										<td style="width: 150px;">
											<div>
												<div class="custom-control custom-radio custom-control-inline" style="float: left;">
													<input name="assignment_declaration" class="custom-control-input radios_out" id="assignment_declaration_agree" type="radio" <?php echo ($assignment_declaration_answer == 'Agree') ? 'checked' : ''; echo $checked; ?> value="Agree">
													<label class="custom-control-label" for="assignment_declaration_agree" name="assignment_declaration_label"> Agree </label>&nbsp;
												</div>
											</div>
											<br>
											<div>
												<div class="custom-control custom-radio custom-control-inline" style="float: left;">
													<input name="assignment_declaration" class="custom-control-input radios_out" id="assignment_declaration_disagree" type="radio" <?php echo ($assignment_declaration_answer == 'Disagree') ? 'checked' : ''; ?> value="Disagree">
													<label class="custom-control-label" for="assignment_declaration_disagree" name="assignment_declaration_label"> Disagree </label>
												</div>
											</div>
											<div>
												&nbsp;
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="col-md-1 col-6 text-left mb-2 ml-2">
							<?php if (!$is_only_previewable) { ?>
								<button class="btn smt-btn save-assignment-declaration">Save</button>
							<?php } ?>
						</div>
					
				</div>
				</p>
			</div>
			<div class="tab-pane fade" id="app<?php echo $tab + 2; ?>" role="tabpanel" aria-labelledby="app<?php echo $tab + 2; ?>-tab">
				<p>
				<div id="accordion8" class="according accordion-s2 mt-3">
					<div class="card card-member">
						<div class="card-header card-vif">
							<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion77" aria-expanded="false"> <span class="lbl-card"> Premium Summary - <i class="ti-credit-card"></i></a>
						</div>
						<div id="accordion77" class="collapse card-vis-mar show premium-summary" data-parent="#accordion8">

						</div>
					</div>
				</div>
				<div id="accordion3" class="according accordion-s2 mt-3">
					<div class="card card-member">
						<div class="card-header card-vif">
							<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion55" aria-expanded="false"> <span class="lbl-card"> Payment - <i class="ti-credit-card"></i></a>
						</div>
						<div id="accordion55" class="collapse card-vis-mar show" data-parent="#accordion3">
							<form class="form-horizontal" id="finalform" method="post" enctype="multipart/form-data">
								<div class="card-body">
									<div class="row">
										<p class="col-md-12 col-12 mb-2 le-space"><b class="b-txt">Select Payment Mode</b></p>

										<?php foreach ($leaddetails->payment_modes as $paymode) {
											$payment_mode_id = $paymode->payment_mode_id;

											if (!empty($leaddetails->proposal_details) && ($leaddetails->proposal_details[0]->mode_of_payment == $payment_mode_id)) {
												$chk_payment = "checked";
											} else {
												$chk_payment = "";
											}
										?>
											<div class="col-md-2 mb-1 mt-1">
												<div class="custom-control custom-radio">
													<input type="radio" name="mode_of_payment" id="mode_of_payment<?php echo $payment_mode_id; ?>" class="custom-control-input" <?php echo $chk_payment; ?> value="<?php echo $payment_mode_id; ?>" <?php if ($is_only_previewable) { ?>disabled<?php } ?>>
													<label class="custom-control-label" for="mode_of_payment<?php echo $payment_mode_id; ?>">
														<?php echo $paymode->payment_mode_name; ?>
													</label>
												</div>
											</div>
										<?php }

										$style = "display:none";
										$go_green_style = " d-none";

										if (isset($leaddetails->proposal_details[0]->mode_of_payment) && $leaddetails->proposal_details[0]->mode_of_payment > 0) {

											if ($leaddetails->proposal_details[0]->mode_of_payment == 2) {
												$style = "display:block";
												$go_green_style = " d-none";
											} else {
												$style = "display:none";
												$go_green_style = "";
											}
										}

										?>
										<!--
								<div class="col-md-2 mb-1 mt-1">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="mode_of_payment" id="mode_of_payment" class="custom-control-input">
                                        <label class="custom-control-label" for="NEFT">NEFT</label>
                                    </div>
                                </div>
                               <div class="col-md-4 mb-1 mt-1">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="mode_of_payment" id="mode_of_payment" class="custom-control-input">
                                        <label class="custom-control-label" for="onlpay">Online Payment</label>
                                    </div>
                                </div>
								-->
									</div>
									<br>
									<div class="row">
										<p class="col-md-12 col-12 mb-2 le-space go-green-p<?= $go_green_style; ?>"><input type="checkbox" value="1" name="go-green" <?php if (isset($leaddetails->proposal_payment_documents->go_green) && $leaddetails->proposal_payment_documents->go_green == 'Y') {
																																											echo "checked";
																																										} ?> <?php if ($is_only_previewable) { ?>disabled<?php } ?> />&nbsp;<b>

                                                <?php
                                                if(!is_null($leaddetails->tc_text) && $leaddetails->tc_text !=""){
                                                    echo $leaddetails->tc_text;
                                                }else{
                                                    echo "  I want to opt for GO-GREEN and receive all my policy related document(s) and communications on the e-mail ID provided in this enrolment form. I/We hereby authorize Insurance Company Limited to mail all service related communications to the email id as mentioned in the application form (applicable only if email id provided)";
                                                }
                                                ?></b></p>
										<div>

											<!-- bank details -->


											<div id="chequedetails" style="<?php echo $style; ?>;">
												<div class="row col-md-12">
													<?php
													//print "<pre>";
													//print_r($leaddetails); 
													?>
													<?php
													$read_only = '';
													if (isset($is_only_previewable) && $is_only_previewable) :
														$read_only = ' readonly';
													endif;
													?>
													<p class="col-md-12 col-12 mb-2 le-space"><b class="b-txt">Bank Details</b></p>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">IFSC Code<span class="lbl-star">*</span></label>
														<div><span class="error ifsc_code_error"></span></div>
														<div class="input-group">
															<input type="text" class="form-control" value="<?php if (!empty($leaddetails->proposal_details)) {
																												echo $leaddetails->proposal_details[0]->ifsc_code;
																											} ?>" name="ifsc_code" id="ifsc_code" <?= $read_only ?>>
															<div class="input-group-prepend">
																<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">credit_card</span></span>
															</div>
														</div>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Bank Name<span class="lbl-star">*</span></label>
														<div><span class="error bank_name_error"></span></div>
														<div class="input-group">
															<input type="text" class="form-control" value="<?php if (!empty($leaddetails->proposal_details)) {
																												echo $leaddetails->proposal_details[0]->bank_name;
																											} ?>" name="bank_name" id="bank_name" readonly>
															<div class="input-group-prepend">
																<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">credit_card</span></span>
															</div>
														</div>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Branch Name<span class="lbl-star">*</span></label>
														<div><span class="error bank_branch_error"></span></div>
														<div class="input-group">
															<input type="text" class="form-control" value="<?php if (!empty($leaddetails->proposal_details)) {
																												echo $leaddetails->proposal_details[0]->bank_branch;
																											} ?>" name="bank_branch" id="bank_branch" readonly>
															<div class="input-group-prepend">
																<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">credit_card</span></span>
															</div>
														</div>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Branch City<span class="lbl-star">*</span></label>
														<div><span class="error bank_city_error"></span></div>
														<div class="input-group">
															<input type="text" class="form-control" value="<?php if (!empty($leaddetails->proposal_details)) {
																												echo $leaddetails->proposal_details[0]->bank_city;
																											} ?>" name="bank_city" id="bank_city" <?= $read_only ?>>
															<div class="input-group-prepend">
																<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_city</span></span>
															</div>
														</div>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">A/c Number<span class="lbl-star">*</span></label>
														<div><span class="error account_number_error"></span></div>
														<div class="input-group">
															<input type="text" class="form-control" value="<?php if (!empty($leaddetails->proposal_details)) {
																												echo $leaddetails->proposal_details[0]->account_number;
																											} ?>" name="account_number" id="account_number" <?= $read_only ?>>
															<div class="input-group-prepend">
																<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">credit_card</span></span>
															</div>
														</div>
													</div>
													<!--
								 <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Payment Type</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">credit_card</span></span>
                                        </div>
                                    </div>
                                </div>  
								-->
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Cheque Number<span class="lbl-star">*</span></label>
														<div><span class="error cheque_number_error"></span></div>
														<div class="input-group">
															<input type="text" class="form-control" value="<?php if (!empty($leaddetails->proposal_details)) {
																												echo $leaddetails->proposal_details[0]->cheque_number;
																											} ?>" name="cheque_number" id="cheque_number" <?= $read_only ?>>
															<div class="input-group-prepend">
																<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">phone_android</span></span>
															</div>
														</div>
													</div>

													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Cheque Date<span class="lbl-star">*</span></label>
														<div><span class="error cheque_date_error"></span></div>
														<div class="input-group">
															<input type="text" autocomplete="off" class="form-control predatepicker" value="<?php if (isset($leaddetails->proposal_details[0]->cheque_date) && !empty($leaddetails->proposal_details)) {
																																				echo date('d-m-Y', strtotime($leaddetails->proposal_details[0]->cheque_date));
																																			} ?>" name="cheque_date" id="cheque_date" <?= $read_only ? ' disabled' : ''; ?>>

															<div class="input-group-prepend">
																<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">phone_android</span></span>
															</div>
														</div>
													</div>

													<!--
                                 <div class="col-md-4 mb-3">
                                     <label for="validationCustomUsername" class="col-form-label">Document Type</label>
                                    <div class="input-group">
                                      <select class="form-control" name="salutation" id="salutation"> <option value="">Select Document Type</option>  </select>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">credit_card</span></span>
                                        </div>
                                    </div>
                                </div>
								-->
													<p class="col-md-12 col-12 mb-2 le-space mt-2"><b class="b-txt">Upload Documents</b></p>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Enrollment Form<span class="lbl-star">*</span></label>
														<?php

														if (isset($leaddetails->proposal_payment_documents->selected_payment_docs->enrollment_form) && !empty($leaddetails->proposal_payment_documents->selected_payment_docs->enrollment_form)) {
														?>
															<div class="input-group-prepend">
																<a href="<?= $leaddetails->proposal_payment_documents->selected_payment_docs->enrollment_form ?>" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;
																<?php
																if (!$read_only) {
																?>
																	<a href="javascript:void(0);" class="file-change f-btn" data-file="enrollment_form">Change <i class="ti-pencil"></i></a>
																<?php
																}
																?>
															</div>
														<?php
														} else {

														?>
															<input type="file" id="enrollment_form" name="enrollment_form" accept="image/jpg, image/jpeg, image/png, application/pdf" <?= $read_only ? ' disabled' : ''; ?> />
															<div><span class="error enrollment_form_error"></span></div>
														<?php
														}
														?>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Cheque Copy<span class="lbl-star">*</span></label>
														<?php

														if (isset($leaddetails->proposal_payment_documents->selected_payment_docs->cheque_copy) && !empty($leaddetails->proposal_payment_documents->selected_payment_docs->cheque_copy)) {
														?>
															<div class="input-group-prepend">
																<a href="<?= $leaddetails->proposal_payment_documents->selected_payment_docs->cheque_copy ?>" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;
																<?php
																if (!$read_only) {
																?>
																	<a href="javascript:void(0);" class="file-change f-btn" data-file="cheque_copy">Change <i class="ti-pencil"></i></a>
																<?php
																}
																?>
															</div>
														<?php
														} else {

														?>
															<input type="file" id="cheque_copy" name="cheque_copy" accept="image/jpg, image/jpeg, image/png, application/pdf" <?= $read_only ? ' disabled' : ''; ?> />
															<div><span class="error cheque_copy_error"></span></div>
														<?php
														}
														?>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">ITR Form </label><br>
														<?php

														if (isset($leaddetails->proposal_payment_documents->selected_payment_docs->itr) && !empty($leaddetails->proposal_payment_documents->selected_payment_docs->itr)) {
														?>
															<div class="input-group-prepend">
																<a href="<?= $leaddetails->proposal_payment_documents->selected_payment_docs->itr ?>" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;
																<?php
																if (!$read_only) {
																?>
																	<a href="javascript:void(0);" class=" f-btn" data-file="itr">Change <i class="ti-pencil"></i></a>
																<?php
																}
																?>
															</div>
														<?php
														} else {

														?>
															<input type="file" id="itr" name="itr" accept="image/jpg, image/jpeg, image/png, application/pdf" <?= $read_only ? ' disabled' : ''; ?> />
															<div><span class="error itr_error"></span></div>
														<?php
														}
														?>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">CAM Report</label><br>
														<?php

														if (isset($leaddetails->proposal_payment_documents->selected_payment_docs->cam) && !empty($leaddetails->proposal_payment_documents->selected_payment_docs->cam)) {
														?>
															<div class="input-group-prepend">
																<a href="<?= $leaddetails->proposal_payment_documents->selected_payment_docs->cam ?>" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;
																<?php
																if (!$read_only) {
																?>
																	<a href="javascript:void(0);" class="file-change f-btn" data-file="cam">Change <i class="ti-pencil"></i></a>
																<?php
																}
																?>
															</div>
														<?php
														} else {

														?>
															<input type="file" id="cam" name="cam" accept="image/jpg, image/jpeg, image/png, application/pdf" <?= $read_only ? ' disabled' : ''; ?> />
															<div><span class="error cam_error"></span></div>
														<?php
														}
														?>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Medical Report</label>
														<?php

														if (isset($leaddetails->proposal_payment_documents->selected_payment_docs->medical) && !empty($leaddetails->proposal_payment_documents->selected_payment_docs->medical)) {
														?>
															<div class="input-group-prepend">
																<a href="<?= $leaddetails->proposal_payment_documents->selected_payment_docs->medical ?>" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;
																<?php
																if (!$read_only) {
																?>
																	<a href="javascript:void(0);" class="file-change f-btn" data-file="medical">Change <i class="ti-pencil"></i></a>
																<?php
																}
																?>
															</div>
														<?php
														} else {

														?>
															<input type="file" id="medical" name="medical" accept="image/jpg, image/jpeg, image/png, application/pdf" <?= $read_only ? ' disabled' : ''; ?> />
															<div><span class="error medical_error"></span></div>
														<?php
														}
														?>
													</div>
													<div class="col-md-4 mb-3">
														<label for="validationCustomUsername" class="col-form-label">Document Type</label>
														<div class="input-group">
															<?php

															$id_doc_type = '';
															if (isset($leaddetails->proposal_payment_documents->id_document_type)) {
																$id_doc_type = $leaddetails->proposal_payment_documents->id_document_type;
															}
															?>
															<select id="id_document_type" name="id_document_type" class="form-control" <?php if ($read_only) {
																																			echo ' disabled';
																																		} ?>>
																<option value="">Select Document Type</option>
																<option value="1" <?php if ($id_doc_type == 1) {
																						echo " selected";
																					} ?>>Birth Certificate</option>
																<option value="2" <?php if ($id_doc_type == 2) {
																						echo " selected";
																					} ?>>Document issued by Central Govt. with age mentioned in it</option>
																<option value="3" <?php if ($id_doc_type == 3) {
																						echo " selected";
																					} ?>>Domicile Certificate</option>
																<option value="4" <?php if ($id_doc_type == 4) {
																						echo " selected";
																					} ?>>PAN Card</option>
																<option value="5" <?php if ($id_doc_type == 5) {
																						echo " selected";
																					} ?>>Passport</option>
																<option value="6" <?php if ($id_doc_type == 6) {
																						echo " selected";
																					} ?>>School Leaving Certificate</option>
																<option value="7" <?php if ($id_doc_type == 7) {
																						echo " selected";
																					} ?>>Aadhaar Card</option>
																<option value="8" <?php if ($id_doc_type == 8) {
																						echo " selected";
																					} ?>>Ration / PDS Photo Card</option>
																<option value="9" <?php if ($id_doc_type == 9) {
																						echo " selected";
																					} ?>>Voter ID</option>
																<option value="10" <?php if ($id_doc_type == 10) {
																						echo " selected";
																					} ?>>Driving License</option>
																<option value="11" <?php if ($id_doc_type == 11) {
																						echo " selected";
																					} ?>>Government Photo ID Card</option>
																<option value="12" <?php if ($id_doc_type == 12) {
																						echo " selected";
																					} ?>>NREGS Job Card</option>
																<option value="13" <?php if ($id_doc_type == 13) {
																						echo " selected";
																					} ?>>Photo ID issued by recognized Educational Institute</option>
																<option value="14" <?php if ($id_doc_type == 14) {
																						echo " selected";
																					} ?>>Bank ATM or Credit Card bearing Photo of Account Holder and duly issued by Nationalized or Public Sector Bank</option>
																<option value="15" <?php if ($id_doc_type == 15) {
																						echo " selected";
																					} ?>>Birth Certificate if it has clear photo</option>
																<option value="16" <?php if ($id_doc_type == 16) {
																						echo " selected";
																					} ?>>Kisan Photo Passbook</option>
																<option value="17" <?php if ($id_doc_type == 17) {
																						echo " selected";
																					} ?>>CGHS / ECHS Photo Card</option>
																<option value="18" <?php if ($id_doc_type == 18) {
																						echo " selected";
																					} ?>>Address card having name photo issued by department of post</option>
																<option value="19" <?php if ($id_doc_type == 19) {
																						echo " selected";
																					} ?>>Employee State Insurance card with photograph supported by latest month&rsquo;s payslip</option>
																<option value="20" <?php if ($id_doc_type == 20) {
																						echo " selected";
																					} ?>>Photo identity issued by any public authority having proper records of issuance of identity proof which is verifiable from records</option>
																<option value="21" <?php if ($id_doc_type == 21) {
																						echo " selected";
																					} ?>>Ex-serviceman (forces) Cards with Photograph</option>
																<option value="22" <?php if ($id_doc_type == 22) {
																						echo " selected";
																					} ?>>Ex-serviceman (forces) Cards with Others</option>
															</select>
															<div class="input-group-prepend">
																<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
															</div>
														</div>
													</div>
													<div class="col-md-4 mb-3 file-type<?php if ($id_doc_type == '') {
																							echo " d-none";
																						} ?>">
														<label for="validationCustomUsername" class="col-form-label">File Type</label><br>
														<?php

														if (isset($leaddetails->proposal_payment_documents->selected_payment_docs->file_type) && !empty($leaddetails->proposal_payment_documents->selected_payment_docs->file_type)) {
														?>
															<div class="input-group-prepend">
																<a href="<?= $leaddetails->proposal_payment_documents->selected_payment_docs->file_type ?>" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;
																<?php
																if (!$read_only) {
																?>
																	<a href="javascript:void(0);" class="file-change f-btn" data-file="file_type">Change <i class="ti-pencil"></i></a>
																<?php
																}
																?>
															</div>
														<?php
														} else {

														?>
															<input type="file" id="file_type" name="file_type" accept="image/jpg, image/jpeg, image/png, application/pdf" <?= $read_only ? ' disabled' : ''; ?> />
															<div><span class="error medical_error"></span></div>
														<?php
														}
														?>
													</div>
												</div>
											</div>
											<!-- -->
											<div class="row mt-4">
												<input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
												<input type="hidden" name="proposal_id" value="<?php echo $leaddetails->proposal_details[0]->proposal_details_id; ?>" />
												<input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
												<input type="hidden" name="plan_id" value="<?php echo $leaddetails->customer_details[0]->plan_id; ?>" />
												<?php if (!$is_only_previewable) { ?>
													<div class="col-md-1 col-6 ml-2">
														<button class="btn smt-btn">Save</button>
													</div>
												<?php } ?>
												<!-- <div class="col-md-2 col-6 text-right">
											<button class="btn cnl-btn">Cancel</button>
										</div> -->
											</div>
										</div>
							</form>
							<!-- end bank details form -->
						</div>
					</div>
				</div>
				</p>
			</div>
		</div>
	</div>
	<?php if ($is_only_previewable) : ?>
		<?php if (!empty($_SESSION['webpanel']['role_id']) && ($_SESSION['webpanel']['role_id'] == 5 || $_SESSION['webpanel']['role_id'] == 7 || $_SESSION['webpanel']['role_id'] == 6)) { ?>
			<div class="tab-pane fade" id="actions-tab-content" role="tabpanel">
				<?php if (($_SESSION['webpanel']['role_id'] == 5 || $_SESSION['webpanel']['role_id'] == 6) && $leaddetails->customer_details[0]->status == 'BO-Approval-Awaiting') {
					//echo $leaddetails->customer_details[0]->status;
				?>
					<div class="col-md-12">
						<div class="content-section mt-3">
							<div class="card">
								<div class="cre-head">
									<div class="row">
										<div class="col-md-10 col-10">
											<p>Actions - <i class="ti-user"></i></p>
										</div>
										<div class="col-md-2 col-2"></div>
									</div>
								</div>
								<div class="card-body">
									<a href="<?php echo base_url(); ?>/boproposals/addEdit?text=<?php echo base64_encode("id=" . $leaddetails->customer_details[0]->lead_id); ?>" title="Edit" class="col-md-4">
										<button type="button" class="btn dis-btn">Add Discrepancy <i class="ti-plus acc-check"></i></button>
									</a>
									<a href="javascript:void(0);" onclick="acceptProposal(<?php echo $leaddetails->customer_details[0]->lead_id; ?>);" title="Accept" class="col-md-4">
										<button type="button" class="btn acc-btn">Approve <i class="ti-check acc-check"></i></button>
									</a>
									<!--<a href="javascript:void(0);" onclick="rejectProposal(<?php echo $leaddetails->customer_details[0]->lead_id; ?>);" title="Reject" class="col-md-4">-->
									<a href="<?php echo base_url(); ?>/boproposals/rejectProposalView?text=<?php echo base64_encode("id=" . $leaddetails->customer_details[0]->lead_id); ?>" title="Edit" class="col-md-4">
										<button type="button" class="btn rej-btn">Reject <i class="ti-close acc-check"></i></button>
									</a>

								</div>
							</div>
						</div>
					</div>


				<?php } else if ($_SESSION['webpanel']['role_id'] == 7 && $leaddetails->customer_details[0]->status == 'UW-Approval-Awaiting') { ?>
					<div class="col-md-12">
						<div class="content-section mt-3">
							<div class="card">
								<div class="cre-head">
									<div class="row">
										<div class="col-md-10 col-10">
											<p>Actions - <i class="ti-user"></i></p>
										</div>
										<div class="col-md-2 col-2"></div>
									</div>
								</div>
								<div class="card-body">
									<a href="javascript:void(0);" onclick="acceptProposal(<?php echo $leaddetails->customer_details[0]->lead_id; ?>);" title="Accept" class="col-md-4">
										<button type="button" class="btn acc-btn">Approve <i class="ti-check acc-check"></i></button>
									</a>
									<!--<a href="javascript:void(0);" onclick="rejectProposal(<?php echo $leaddetails->customer_details[0]->lead_id; ?>);" title="Reject" class="col-md-4">-->
									<a href="<?php echo base_url(); ?>/boproposals/rejectProposalView?text=<?php echo base64_encode("id=" . $leaddetails->customer_details[0]->lead_id); ?>" title="Edit" class="col-md-4">
										<button type="button" class="btn rej-btn">Reject <i class="ti-close acc-check"></i></button>
									</a>
								</div>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<p>Currently no actions to perform.</p>
				<?php } ?>
			</div>
		<?php } ?>
	<?php endif; ?>
</div>
</div>
</div>
<div class="modal age-diff-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn smt-btn" onclick="return false;" data-dismiss="modal">Yes</button>
				<button type="button" class="btn cnl-btn change-age-dob">No</button>
			</div>
		</div>
	</div>
</div>
<!-- end here -->
<script>
	var panForm = '';

	function enableNextAccordion(id) {

		elem = $(id).closest('.according').nextAll('.according:first');
		elem.find('a.no-collapsable').removeClass('no-collapsable');
		elem.find('.accord-data').collapse('show');
	}

	function rejectProposal(id) {
		var r = confirm("Are you sure you want to reject this proposal?");
		if (r == true) {
			$.ajax({
				url: "<?php echo base_url() ?>/boproposals/rejectProposal/" + id,
				async: false,
				type: "POST",
				success: function(data2) {
					data2 = $.trim(data2);
					if (data2 == "1") {
						displayMsg("success", "Record has been Rejected!");
						setTimeout("location.reload(true);", 1000);

					} else {
						displayMsg("error", "Oops something went wrong!");
						setTimeout("location.reload(true);", 1000);
					}
				}
			});
		}
	}

	function acceptProposal(id) {
		var r = confirm("Are you sure you want to accept this record?");
		if (r == true) {
			$.ajax({
				url: "<?php echo base_url(); ?>/boproposals/acceptProposal/" + id,
				async: false,
				type: "POST",
				dataType: 'json',
				success: function(response) {
					//console.log(response);
					//console.log(response['success']);
					//return false;
					//data2 = $.trim(data2);
					if (response['success'] == "1") {
						displayMsg("success", "Record has been Accepted!");
						//setTimeout("location.reload(true);",1000);
						passLeadsToInsurance(id);

						setTimeout("location.reload(true);", 5000);

					} else if (response['success'] == "3") {
						displayMsg("success", "Record has been moved to UW approval!");
						setTimeout("location.reload(true);", 2000);
					} else {
						displayMsg("error", "Oops something went wrong!");
						setTimeout("location.reload(true);", 1000);
					}
				}
			});
		}
	}

	function passLeadsToInsurance(lead_id) {
		//alert(lead_id);return false;		
		//check array not empty.
		$.ajax({
			url: "<?php echo base_url(); ?>boproposals/passLeadsToInsurance",
			data: {
				lead_id: lead_id
			},
			type: 'post',
			dataType: 'json',
			success: function(res) {

			}
		});

	}

	$(document).ready(function() {
		fillTraceId();
		populateQuoteData(<?php echo json_encode($generated_premium) ?>);

		let master_quote_ids = <?php echo json_encode($master_quote_ids) ?>;

		$('[name="master_quote_id<?php echo $coapplicant_tab_id ?? '' ?>"]').val(master_quote_ids.join());

		$('.age-diff-modal').on('hidden.bs.modal', function(e) {

			premium = 0;
			$('.pan_added').val('c');
		});
	});

	$('body').on('submit', '#pan-form', function() {

		lead_id = $('input[name="lead_id"]').val();
		formArr = $(this).serializeArray();
		formAction = $(this).attr('action');
		customer_pan_data = {};
		$('.age-diff-modal .msg').removeClass('text-danger').text('');

		for (var i = 0; i < formArr.length; i++) {

			name = formArr[i]['name'];
			customer_id = $('input[name="' + name + '"]').attr('data-customer-id');
			pan = formArr[i]['value'];

			if ($.trim(pan) != '') {

				if (!(/^[A-Z]{5}\d{4}[A-Z]{1}$/.test(pan))) {

					$('.' + name + '.msg').addClass('text-danger').text('Invalid PAN');
					customer_pan_data = {};
					break;
				}

				customer_pan_data[i] = customer_id + ':' + pan;
			}
		}

		if (Object.keys(customer_pan_data).length) {

			$.ajax({

				url: formAction,
				data: {
					'customer_data': JSON.stringify(customer_pan_data),
					'lead_id': lead_id
				},
				dataType: 'JSON',
				method: 'post',
				async: false,
				beforeSend: function() {

					$('.submit-btn-pan').attr('disabled', true);
				},
				success: function(response) {

					$('.age-diff-modal').modal('hide');
					$('.submit-btn-pan').attr('disabled', false);
					if (response.success) {

						$('.pan_added').val('y');
						displayMsg('success', 'PAN added in records');

					} else {

						displayMsg('error', 'Something went wrong. Please try again');
					}
				}
			});
		}

		return false;
	});

	/*function submitPan(formObj) {

		/*proposer_pan = $('input[name="proposer_pan"]').val();
		customer_id = $('input[name="customer_id"]').val();
		lead_id = $('input[name="lead_id"]').val();

		if ($.trim(proposer_pan) != '') {

			if(!(/^[A-Z]{5}\d{4}[A-Z]{1}$/.test(proposer_pan))){

				$('.age-diff-modal .msg').removeClass('text-success').addClass('text-danger').text('Invalid PAN');
				return false;
			}

			$.ajax({

				url: formObj.action,
				data: {
					'proposer_pan': proposer_pan,
					'customer_id': customer_id,
					'lead_id': lead_id
				},
				dataType: 'JSON',
				method: 'post',
				async: false,
				beforeSend: function(){

					$('.submit-btn-pan').attr('disabled', true);
					$('.age-diff-modal .msg').removeClass('text-danger').text('');
				},
				success: function(response) {

					if (response.success) {

						$('.age-diff-modal .msg').removeClass('text-danger').addClass('text-success').text(response.msg);
						$('.pan_added').val('y');
						setTimeout(function() {
							$('.age-diff-modal').modal('hide');
						}, 3000);
					} else {

						$('.age-diff-modal .msg').removeClass('text-success').addClass('text-danger').text(response.msg);
					}
				}
			});

			$('.submit-btn-pan').attr('disabled', false);
		} else {

			$('.age-diff-modal .msg').removeClass('text-success').addClass('text-danger').text('Field Required')
		}
	}*/

	function fillTraceId() {
		$("#unique_trace_id").text($("[name='trace_id']").val());
	}
	$("#mode_of_payment2").click(function() {
		var val = $(this).val();
		if (val == 2) {
			$("#chequedetails").show();
		} else {
			$("#chequedetails").hide();
		}
	});

	$("#mode_of_payment1").click(function() {
		$("#chequedetails").hide();
	});
	$("#mode_of_payment3").click(function() {
		$("#chequedetails").hide();
	});

	$.validator.addMethod('regex', function(value, element, param) {
		return this.optional(element) || /^[6789]\d{9}$/.test(value);
	});

	var vRules = {
		address_line1: {
			required: true,
			minlength: 10
		},
		city: {
			required: true
		},
		email_id: {
			required: true
		},
		salutation: {
			required: true
		},
		firstname: {
			required: true,
			firstnamelettersonly: true
		},
		middlename: {
			firstnamelettersonly: true
		},
		lastname: {
			required: true,
			lastnamevalidate: true
		},
		dob: {
			required: true
		},
		mob_no: {
			required: true,
			regex: true
		},
		state: {
			required: true
		},
		pin_code: {
			required: true,
			number: true,
			minlength: 6,
			maxlength: 6
		}
	};

	var vMessages = {
		address_line1: {
			required: "Address is required",
			minlength: "Address should be at least 10 character"
		},
		city: {
			required: "City is required"
		},
		firstname: {
			required: "This field is required"
		},
		lastname: {
			required: "This field is required"
		},
		dob: {
			required: "This field is required"
		},
		mob_no: {
			required: "This field is required",
			regex: "Please enter valid phone number"
		},
		state: {
			required: "State is required"
		},
		pin_code: {
			required: "Pincode is required",
			number: "Pincode should be numeric and 6 digit",
			minlength: "Pincode should be numeric and 6 digit"
		}
	};

	$(document).on('change', function(e) {
		if (e.target.classList.contains('quote_generation_fields')) {
			generateQuote(e.target.closest('form').id);
		}
	});

	function generateQuote(form_id) {
		let mapping = {
			'family_members_ac_count': "family_members_ac_count",
			'ghi_cover': "sum_insured1",
			'pa_cover': "sum_insured2",
			'ci_cover': "sum_insured3",
			'hospi_cash': "sum_insured6",
			'spouse_age': "spouse_age",
			'tenure': "tenure",
			'super_top_up_cover': "sum_insured5_1",
			'deductable': "deductable",
			'numbers_of_ci': 'numbers_of_ci'
		};

		let requestData = {
			plan_id: $("#" + form_id + " [name='plan_id']").val(),
			lead_id: $("#" + form_id + " [name='lead_id']").val(),
			trace_id: $("#" + form_id + " [name='trace_id']").val(),
			customer_id: $("#" + form_id + " [name='customer_id']").val(),
		};

		$spouseDob = $("#" + form_id + " [name='spouse_dob']");

		if (!$spouseDob.prop('disabled')) {
			requestData.spouse_age = $("#" + form_id + " [name='spouse_age']").val();
			requestData.spouse_dob = $("#" + form_id + " [name='spouse_dob']").val();
		}

		let $current_generate_quote_acc = $("#" + form_id).parent();

		if (!requestData.customer_id && $current_generate_quote_acc.hasClass('show')) {
			displayMsg('error', 'Please fill customer details first');
			return;
		}

		$('#' + form_id + ' .quote_generation_fields').each(function(i, obj) {
			let current_id = obj.id;
			if (Object.values(mapping).indexOf(current_id) > -1) {
				let key = Object.keys(mapping).find(key => mapping[key] === current_id);
				requestData[key] = obj.value;
			}
		});

		var quote_action_url = "<?php echo base_url(); ?>policyproposal/generateQuote";

		$.ajax({
			url: quote_action_url,
			data: requestData,
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false,
			success: function(response) {
				//if (response.success) {
				let data = response.data;
				populateQuoteData(data);
				//}
			}
		});
	}

	function populateQuoteData(data) {
		let applicant_header_html = "";
		let net_premium = 0;
		for (let applicant_key in data) {

			if (applicant_key !== "net_premium") {

				let policy_count = 0;

				if (data[applicant_key]['policies']) {
					policy_count = Object.keys(data[applicant_key]['policies']).length;
				}

				if (!policy_count) {
					continue;
				}

				applicant_header_html += `
							<div class="head-lbl-2 mt-1" id="${applicant_key}_premium">
								<p class="head-lbl-1">${applicant_key}</p>
							</div>`;
			} else if (applicant_key == "net_premium") {
				net_premium = data["net_premium"];
			}
		}
		$('#total_premium').html(net_premium);
		//app_coapp_premium = net_premium
		$('#premium_calculations_data .head-lbl-2').remove();

		$("#premium_calculations_data").append(applicant_header_html);

		for (let applicant_key in data) {

			let policy_data = data[applicant_key];

			let policies = policy_data.policies;

			for (let policy_name in policies) {
				if (policies.hasOwnProperty(policy_name)) {

					$("#" + applicant_key + "_premium").append(
						`<p>${policy_name}<span class="fl-right"><i class="fa fa-inr"></i> ${policies[policy_name]}</span></p>`
					);
				}
			}
		}
	}

	var applicant_validation_object = {
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) {
			var act = "<?php echo base_url(); ?>policyproposal/submitForm";
			$("#cust_data").ajaxSubmit({
				url: act,
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				beforeSubmit: function(arr, $form, options) {
					debugger;
					var mob = $("#cust_data" + coapplicant_tab_id + " #mobile_no2").val();
					var gender = $("#cust_data" + coapplicant_tab_id + " select[name='gender1']").find('option:selected').val();
					if (mob != '') {
						var filter = /^[6789]\d{9}$/;
						if (filter.test(mob)) {
							$(".moberror_customer").html("").css('display', 'none');
						} else {
							$(".moberror_customer").html("Please enter valid phone number").removeAttr('style');
							return false;
						}
					}

					if (gender == '') {

						$(".moberror_gender").html("Please select gender").removeAttr('style');
						return false;
					} else {

						$(".moberror_gender").html("").css('display', 'none');
					}
					$(".btn-primary").hide();
					//return false;
				},
				success: function(response) {
					$(".btn-primary").show();
					if (response.success) {

						$("#self_age_" + coapplicant_tab_id).val(response.self_age);
						displayMsg("success", response.msg);
						enableNextAccordion("#cust_data");

					} else {
						displayMsg("error", response.msg);
						return false;
					}
				}
			});

		}
	}

	$("#cust_data").validate(applicant_validation_object);
	document.title = "Add/Edit Policy Proposal";


	/* for bank details form **/
	/*var finalRules = {
		//mode_of_payment:{required:true}
	};

	var finalMessages = {
		//mode_of_payment:{required:"Field is required"}
	};

	$("#finalform").validate({
		rules: finalRules,
		messages: finalMessages,
		submitHandler: function(form) {
			var act = "<?php echo base_url(); ?>policyproposal/submitfinalForm";
			var form = "#finalform";

			/*
		 if($("#mode_of_payment2").is(':checked')) {
            alert("Allot Thai Gayo Bhai");
          }
		***/

	/*if ($('input[name="mode_of_payment"]').is(':checked') && ($('input[name="mode_of_payment"]').val() == 'Cheque' || $('input[name="mode_of_payment"]').val() == 'NEFT')) {
				var cheque_date = $("#cheque_date").val();
				var cheque_number = $("#cheque_number").val();
				var account_number = $("#account_number").val();
				var ifsc_code = $("#ifsc_code").val();
				var bank_city = $("#bank_city").val();
				var bank_branch = $("#bank_branch").val();
				var bank_name = $("#bank_name").val();
				var error = 0;
				if (cheque_date == '') {
					$(".cheque_date_error").html("*This field is required");
					error = 1;
				} else {
					$(".cheque_date_error").html("");
				}
				if (account_number == '') {
					$(".account_number_error").html("*This field is required");
					error = 1;
				} else {
					$(".account_number_error").html("");
				}
				if (cheque_number == '') {
					$(".cheque_number_error").html("*This field is required");
					error = 1;
				} else {
					$(".cheque_number_error").html("");
				}
				if (ifsc_code == '') {
					$(".ifsc_code_error").html("*This field is required");
					error = 1;
				} else {
					$(".ifsc_code_error").html("");
				}
				if (bank_city == '') {
					$(".bank_city_error").html("*This field is required");
					error = 1;
				} else {
					$(".bank_city_error").html("");
				}
				if (bank_branch == '') {
					$(".bank_branch_error").html("*This field is required");
					error = 1;
				} else {
					$(".bank_branch_error").html("");
				}
				if (bank_name == '') {
					$(".bank_name_error").html("*This field is required");
					error = 1;
				} else {
					$(".bank_name_error").html("");
				}
				if (error == 1) {
					return false;
				}
			}

			$("#finalform").ajaxSubmit({
				url: act,
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				beforeSubmit: function(arr, $form, options) {

					// /$(".btn").hide();
					//return false;
				},
				success: function(response) {

					// /$(".btn-primary").show();
					if (response.success) {
						displayMsg("success", response.msg);
						//$('#collapseTwo').addClass('in');
						//$('#collapseOne').removeClass('in');

					} else {
						displayMsg("error", response.msg);
						return false;
					}
				}
			});
		}
	});*/

	$('#id_document_type').on('change', function() {

		if ($('option:selected', this).attr('value') != '') {

			$('.file-type').removeClass('d-none');
		} else {

			$('.file-type').addClass('d-none');
		}
	});

	var cache = {};
	var bankDetails = {};

	$('#ifsc_code').autocomplete({

		minLength: 5,
		delay: 100,
		source: function(request, response) {

			var term = request.term;
			if (term in cache) {

				response(cache[term]);
				return;
			}

			$.ajax({

				url: '<?php echo base_url(); ?>policyproposal/getBankDetails',
				method: 'POST',
				data: request,
				dataType: 'JSON',
				async: false,
				success: function(data) {

					if (data.success) {

						message = data.msg;
						response($.map(message, function(item) {

							obj = {
								label: item.ifsc_code,
								value: item.ifsc_code
							}

							bankDetails[item.ifsc_code] = item;
							cache[term] = obj;
							return obj;
						}));
					}
				}
			});
		},
		select: function(event, ui) {

			$('#bank_name').val(bankDetails[ui.item.value]['bank_name']);
			$('#bank_branch').val(bankDetails[ui.item.value]['branch']);
			$('#bank_city').val();
		}
	});

	var prevHtml = [];

	$('body').on('click', '.file-change', function(e) {

		e.preventDefault();
		attr = $(this).attr('data-file');
		prevHtml[attr] = $(this).closest('div').html();
		$(this).closest('div').html(`
			<input type="file" name="${attr}" id="${attr}" accept="image/jpg, image/jpeg, image/png, application/pdf" />
			<a href="javascript:void(0);" class="file-cancel" data-key="${attr}" alt="Cancel"><i class="ti-close"></i></a>
			<span class="error ${attr}_error"></span>
		`);
	});

	$('body').on('click', '.file-cancel', function(e) {

		e.preventDefault();
		attr = $(this).attr('data-key');
		$(this).closest('div').html(prevHtml[attr]);
	});

	$("#finalform").on('submit', function() {

		var error = 0;
		if ($('input[name="mode_of_payment"]').is(':checked')) {

			if (($('input[name="mode_of_payment"]:checked').val() == 2)) {

				var cheque_date = $("#cheque_date").val();
				var cheque_number = $("#cheque_number").val();
				var account_number = $("#account_number").val();
				var ifsc_code = $("#ifsc_code").val();
				var bank_city = $("#bank_city").val();
				var bank_branch = $("#bank_branch").val();
				var bank_name = $("#bank_name").val();
				//var error = 0;
				if (cheque_date == '') {
					$(".cheque_date_error").html("*This field is required");
					error = 1;
				} else {
					$(".cheque_date_error").html("");
				}
				if (account_number == '') {
					$(".account_number_error").html("*This field is required");
					error = 1;
				} else {
					$(".account_number_error").html("");
				}
				if (cheque_number == '') {
					$(".cheque_number_error").html("*This field is required");
					error = 1;
				} else {
					$(".cheque_number_error").html("");
				}
				if (ifsc_code == '') {
					$(".ifsc_code_error").html("*This field is required");
					error = 1;
				} else {
					$(".ifsc_code_error").html("");
				}
				if (bank_city == '') {
					$(".bank_city_error").html("*This field is required");
					error = 1;
				} else {
					$(".bank_city_error").html("");
				}
				if (bank_branch == '') {
					$(".bank_branch_error").html("*This field is required");
					error = 1;
				} else {
					$(".bank_branch_error").html("");
				}
				if (bank_name == '') {
					$(".bank_name_error").html("*This field is required");
					error = 1;
				} else {
					$(".bank_name_error").html("");
				}

				if ($('#enrollment_form').length) {

					if ($('#enrollment_form').val() == "") {

						$('.enrollment_form_error').html("*This field is required");
						error = 1;
					} else {

						$('.enrollment_form_error').html("");
						error = 0;
					}
				}

				if ($('#cheque_copy').length) {

					if ($('#cheque_copy').val() == "") {

						$('.cheque_copy_error').html("*This field is required");
						error = 1;
					} else {
						$('.cheque_copy_error').html("");
						error = 0;
					}
				}
			}

			if (!error) {

				form = $(this)[0];
				formData = new FormData(form);

				$.ajax({

					url: "<?php echo base_url(); ?>policyproposal/submitfinalForm",
					type: 'post',
					dataType: 'json',
					data: formData,
					processData: false,
					contentType: false,
					beforeSend: function() {

						//$(".btn").hide();
						//return false;
						if ($('.pan_added').val() == '') {

							$('.age-diff-modal .modal-title').text('Proposer PAN Required');
							/*$('.age-diff-modal .modal-body')
							.html('<form onsubmit="submitPan(this);return false;" action="<?php echo base_url(); ?>policyproposal/capturecustomerpan"><input type="text" name="proposer_pan">&nbsp;&nbsp;<span class="msg">&nbsp;&nbsp;</span><button type="submit" class="submit-btn-pan btn smt-btn">Save</button></form>');*/
							$('.age-diff-modal .modal-body').html(panForm);
							$('.age-diff-modal .modal-footer').addClass('d-none');
							$('.age-diff-modal').modal('show');

							return false;
						}
					},
					success: function(response) {

						//$(".btn-primary").show();
						/*if (response.success) {
							displayMsg("success", response.msg);
							//$('#collapseTwo').addClass('in');
							//$('#collapseOne').removeClass('in');

						} else {
							displayMsg("error", response.msg);
							return false;
						}*/

						if (response.success) {
							displayMsg("success", response.msg);
							setTimeout(function() {

								var url = new URL(window.location.href);
								var text = url.searchParams.get("text");
								window.location.href = "<?php echo base_url(); ?>policyproposal/proposalSummary/?text=" + text;
							}, 3000);
						} else {
							displayMsg("error", response.msg);
							return false;
						}
					}
				});
			}
		} else {

			displayMsg("error", "Payment mode is required");
		}

		return false;
	});
</script>

<script>
	// forNominee data 

	/*		
	$(".nominee_dob").datepicker({  
			changeMonth: true,
			changeYear: true,
			minDate: new Date() });
	***/


	$(".nominee_dob").datepicker({
		changeMonth: true,
		changeYear: true,
		maxDate: new Date()
	});

	$("#cheque_date").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'dd-mm-yy',
	});

	$(document).ready(function() {

		$("#cheque_date").datepicker('setDate', "<?php if (isset($leaddetails->proposal_details[0]->cheque_date) && !empty($leaddetails->proposal_details)) {
														echo date('d-m-Y', strtotime($leaddetails->proposal_details[0]->cheque_date));
													} ?>");

	});
</script>

<script>
	$("li").each(function() {
		var $thistab = $(this);
		var id = $(this).attr("data_id");

		if (!$(this).hasClass('disabled')) {

			$thistab.click(function() {
				let coapplicant_tab_id = $(this).attr("data_id") - 1;
				let lead_id = '<?php echo $leaddetails->customer_details[0]->lead_id; ?>';

				// Dont reload views if there is already existing html;
				if ($("#app" + id).html() == "") {

					var load_url = "<?php echo base_url(); ?>policyproposal/addPolicyProposalView/" + lead_id +
						"/?coapplicant_tab_id=" + coapplicant_tab_id;

					<?php if (isset($is_only_previewable) && $is_only_previewable) : ?>
						load_url += "&is_only_previewable=true";
					<?php endif; ?>
					$("#app" + id).load(load_url,
						function(response, statusTxt, xhr) {

							/*$('#leadform' + coapplicant_tab_id + ' #family_members_ac_count').trigger('change', {
								page_load: true
							});*/
							cust_id = $('#cust_data' + coapplicant_tab_id + ' .customer_id_hidden' + coapplicant_tab_id).val();
							$('body #accordion480' + coapplicant_tab_id + ' input[name="customer_id"]').val(cust_id);
							loadGHDDeclaration(coapplicant_tab_id);

							/*if (!$('body #accordion480' + coapplicant_tab_id + ' .nav-link').attr('data-member')) {

								$('body #accordion480' + coapplicant_tab_id + ' .nav-link').attr('data-member', 0)
								$('#accordion480' + coapplicant_tab_id + ' form')[0].reset();
								if ($('#cust_data' + coapplicant_tab_id + ' .customer_id_hidden' + coapplicant_tab_id).val() != '') {

									data = {};
									data.lead_id = lead_id,
										data.customer_id = $('#cust_data' + coapplicant_tab_id + ' .customer_id_hidden' + coapplicant_tab_id).val();

									$.ajax({

										url: '<?php echo base_url(); ?>policyproposal/getMemberID',
										method: 'post',
										data: data,
										dataType: 'JSON',
										cache: false,
										async: false,
										success: function(response) {

											if (response.success) {

												if (response.data) {
													$(response.data).each(function(i, value) {

														if (value.relation_with_proposal == 1) {

															$('body #accordion480' + coapplicant_tab_id + ' #self-tab').attr('data-member', value.member_id).removeClass('disabled');
														} else if (value.relation_with_proposal == 2) {

															$('body #accordion480' + coapplicant_tab_id + ' #spouse-tab').attr('data-member', value.member_id).removeClass('disabled');
														} else if (value.relation_with_proposal == 5) {

															$('body #accordion480' + coapplicant_tab_id + ' #kid1-tab').attr('data-member', value.member_id).removeClass('disabled');
														} else if (value.relation_with_proposal == 6) {

															$('body #accordion480' + coapplicant_tab_id + ' #kid2-tab').attr('data-member', value.member_id).removeClass('disabled');
														}
													});
												}
											}
										}
									});
								}
							}

							$('body #accordion480' + coapplicant_tab_id + ' .nav-link:first').trigger('click');*/
						});
				}

				// $("#app" + id).load("<?php echo base_url(); ?>policyproposal/addPolicyProposalView/" + lead_id +
				// 	"/?coapplicant_tab_id=" + coapplicant_tab_id);
			});
		}
	});
</script>

<script>
	/* for bank details form **/
	var vRules_lead_data = {
		//mode_of_payment:{required:true}
	};

	var vMessages_lead_data = {
		//mode_of_payment:{required:"Field is required"}
	};

	$(document).change(function(e) {
		let name = e.target.name;
		if (name == "pin_code") {

			let pincode = e.target.value;

			var state_city_url = "<?php echo base_url(); ?>policyproposal/getStateCity";

			let closest_form_id = e.target.closest("form").id;

			$.ajax({
				url: state_city_url,
				data: {
					pincode: pincode
				},
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				success: function(response) {
					if (response.success) {
						let data = response.data;
						$("#" + closest_form_id + " [name='city']").val(data.CITY);
						$("#" + closest_form_id + " [name='state']").val(data.STATE);
					} else {
						displayMsg("error", "Please enter correct pincode");
						$("#" + closest_form_id + " [name='city']").val("");
						$("#" + closest_form_id + " [name='state']").val("");
					}
				}
			});
		}
	});

	$('body').on('click', 'input[name="mode_of_payment"]', function() {

		if ($('input[name="mode_of_payment"]:checked').val() == 2) {

			$('#chequedetails').show();
			$('.go-green-p').addClass('d-none');

		} else {

			$('#chequedetails').hide();
			$('.go-green-p').removeClass('d-none');
		}
	});

	$('.save-assignment-declaration').on('click', function() {

		data = {};
		data.value = $('input[name="assignment_declaration"]:checked').val();
		data.lead_id = "<?php echo $leaddetails->customer_details[0]->lead_id; ?>";

		$.ajax({

			url: "<?php echo base_url(); ?>policyproposal/saveAssignmentDeclaration",
			method: "POST",
			data: data,
			dataType: 'JSON',
			cache: false,
			async: false,
			success: function(response) {

				if (response.success) {

					if (data.value == 'Agree') {

						displayMsg("success", response.msg);

						if (!$('.payment-tab').hasClass('active')) {

							$('#' + current_main_tab_id).closest('.nav-item').nextAll('.nav-item:first').children('.main-tab.disabled').removeClass('disabled').trigger('click');
						}

						current_main_tab_id = $('.main-tab.active').attr('id');
						$([document.documentElement, document.body]).animate({

							scrollTop: $("#" + current_main_tab_id).offset().top
						}, 800);
						$('#app4-tab').trigger("click");
					} else {

						displayMsg("error", "Please agree to the terms to proceed");
					}
				} else {

					displayMsg("error", "Something went wrong");
				}
			}
		});

		return false;
	});
    $('#bank_city').keypress(function(e) {
        var regex = new RegExp(/^[a-zA-Z\s]+$/);
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });
</script>