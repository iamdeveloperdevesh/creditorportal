<style>
	.error {
		position: unset;
		top: unset;
		line-height: 15px;
	}
</style>
<div class="col-lg-12 mt-2">
	<div class="card-body pad-0">
		<ul class="nav nav-tabs mb-scroll" id="myTab" role="tablist">
			<?php
			$i = 1;
			$j = 0;
			$k = 10;
			foreach ($customer_details as $lead_id => $customers) {

				foreach ($customers as $customer_id => $customer_detail) {
					$tab_text = $class = '';
					if ($i == 1) {

						$tab_text = 'Applicant';
						$class = 'active';
					} else {

						$tab_text = 'Co-Applicant ' . ($i - 1);
					}
			?>
					<li class="nav-item">
						<a class="nav-link<?php echo " $class"; ?>" id="app<?= $i ?>-tab" data-toggle="tab" href="#app<?= $i ?>" role="tab" aria-controls="app<?= $i ?>" aria-selected="true"><?= $tab_text ?></a>
					</li>
			<?php
					$i++;
				}
			}

			$payments_tab = $i + 1;
			?>

			<li class="nav-item">
				<a class="nav-link" id="app<?= $payments_tab ?>-tab" data-toggle="tab" href="#app<?= $payments_tab ?>" role="tab" aria-controls="app<?= $payments_tab ?>" aria-selected="true">Assignment Declaration</a>
			</li>
			<li class="nav-item">
				<a class="nav-link payment" id="app<?= $payments_tab + 1 ?>-tab" data-toggle="tab" href="#app<?= $payments_tab + 1 ?>" role="tab" aria-controls="app<?= $payments_tab + 1 ?>" aria-selected="true">Payment Details</a>
			</li>
		</ul>
		<div class="tab-content mt-3" id="myTabContent">
			<?php
			$i = 1;
			foreach ($customer_details as $lead_id => $customers) {

				foreach ($customers as $customer_id => $customer_detail) {

					$tab_text = $class = '';
					$ifsc = $bank_name = $account_number = $mode_of_payment = '';

					if ($i == 1) {

						$class = ' show active';
					}

					if ($customer_detail['mode_of_payment'] == 2) {

						$ifsc = $customer_detail['ifsc_code'];
						$bank_name = $customer_detail['bank_name'];
						$account_number = $customer_detail['account_number'];
					} else {

						$ifsc = 'N/A';
						$bank_name = 'N/A';
						$account_number = 'N/A';
					}

					$mode_of_payment = $customer_detail['mode_of_payment'];
			?>
					<div class="tab-pane fade<?php echo " $class"; ?>" id="app<?= $i; ?>" role="tabpanel" aria-labelledby="app<?= $i; ?>-tab">
						<p>
						<div id="accordion<?= $i; ?>" class="according accordion-s2 mt-3">
							<div class="card card-member">
								<div class="card-header card-vif">
									<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion45<?= $i; ?>" aria-expanded="false"> <span class="lbl-card">Customer Details - <i class="ti-file"></i></a>
								</div>
								<div id="accordion45<?= $i; ?>" class="collapse card-vis-mar show" data-parent="#accordion<?= $i; ?>" style="">
									<?php
									/*<form id="cust_data<?php echo $i; ?>" name="cust_data<?php echo $i; ?>" onsubmit="custDataSubmit(this);return false;">*/

									$attributes = 'id="cust_data' . $i . '" name="cust_data' . $i . '"';
									echo form_open(base_url() . "customer/memberdetailsform", $attributes);
									?>
									<input type="hidden" name="elem_lead" id="elem_lead<?= $i; ?>" value="<?= $lead_id; ?>" />
									<input type="hidden" name="elem_customer" id="elem_customer<?= $i; ?>" value="<?= $customer_id; ?>" />
									<div class="card-body">
										<div class="row mt-3">
											<div class="col-md-6 col-12">
												<div class="table-responsive tbl">
													<table class="table tbl-600 table-bordered">
														<tbody>
															<?php /*<tr>
																<td class="wd-25">Lead Id</td>
																<td class="wd-25"><?php echo $customer_detail['lead_id']; ?></td>
																</tr>*/ ?>
															<tr>
																<td class="wd-25">Salutation</td>
																<td class="wd-25"><?php echo $customer_detail['salutation'] ?></td>
															</tr>
															<tr>
																<td>Last Name</td>
																<td><input type="text" class="form-control valid ps-rel" name="lastname" value="<?php echo $customer_detail['last_name'] ?>" /></td>
															</tr>
															<tr>
																<td>Date Of Birth</td>
																<td><?php echo date('d-m-Y', strtotime($customer_detail['dob'])) ?></td>
															</tr>
															<tr>
																<td>Email</td>
																<td><?php echo $customer_detail['email_id'] ?></td>
															</tr>
															<tr>
																<td>Communication Address Line 2</td>
																<td><textarea class="form-control ps-rel" name="address_line2"><?php echo $customer_detail['address_line2']; ?></textarea></td>
															</tr>
															<tr>
																<td>Pincode</td>
																<td><input type="text" class="form-control valid" name="pin_code" value="<?php echo $customer_detail['pincode'] ?>" /></td>
															</tr>
															<tr>
																<td>City</td>
																<td><input type="text" class="form-control valid" name="city" value="<?php echo $customer_detail['city'] ?>" readonly /></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="table-responsive tbl">
													<table class="table tbl-600 table-bordered">
														<tbody>
															<tr>
																<td>First Name</td>
																<td><input type="text" class="form-control valid  ps-rel" name="firstname" value="<?php echo $customer_detail['first_name']; ?>" /></td>
															</tr>
															<tr>
																<td>Gender</td>
																<td><?php echo $customer_detail['gender']; ?></td>
															</tr>
															<tr>
																<td>Mobile Number</td>
																<td><?php echo $customer_detail['customer_mobile_no']; ?></td>
															</tr>
															<tr>
																<td>Communication Address Line 1</td>
																<td><textarea class="form-control ps-rel" name="address_line1"><?php echo $customer_detail['address_line1']; ?></textarea></td>
															</tr>
															<tr>
																<td>Communication Address Line 3</td>
																<td><textarea class="form-control ps-rel" name="address_line3"><?php echo $customer_detail['address_line3']; ?></textarea></td>
															</tr>
															<tr>
																<td>State</td>
																<td><input type="text" class="form-control valid" name="state" value="<?php echo $customer_detail['state'] ?>" readonly /></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
											<div class="col-md-1 col-6 text-left">
												<button type="submit" class="btn smt-btn customer-details-save">Save</button>
											</div>
										</div>
									</div>
									<?php /*</form>*/
									echo form_close();
									?>
									<script type="text/javascript">
										/* Customer Details form save code starts here */

										$(document).ready(function() {

											var id = "cust_data<?= $i; ?>";

											var vRules = {
												firstname: {
													required: true
												},
												lastname: {
													required: true
												},
												address_line1: {
													required: true,
													minlength: 10
												},
												/*address_line2: {
													required: true,
													minlength: 10
												},
												address_line3: {
													required: true,
													minlength: 10
												},*/
												city: {
													required: true
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
												firstname: {
													required: "First name is required"
												},
												lastname: {
													required: "Last name is required"
												},
												address_line1: {
													required: "Address is required",
													minlength: "Address should be at least 10 character"
												},
												/*address_line2: {
													required: "Address is required",
													minlength: "Address should be at least 10 character"
												},
												address_line3: {
													required: "Address is required",
													minlength: "Address should be at least 10 character"
												},*/
												city: {
													required: "City is required"
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

											var customerFormValidation = {
												rules: vRules,
												messages: vMessages,
												submitHandler: function(form) {
													var act = "<?php echo base_url(); ?>customer/customdetailsform";
													$("#" + id).ajaxSubmit({
														url: act,
														type: 'post',
														dataType: 'json',
														cache: false,
														clearForm: false,
														beforeSubmit: function(arr, $form, options) {

															$(".customer-details-save").text('Saving...').prop('disabled', true);
														},
														success: function(response) {

															$(".customer-details-save").text('Save').prop('disabled', false);
															if (response.response > 0) {

																displayMsg("success", "Record Updated");

															} else {

																displayMsg("error", "No Changes Made");
																return false;
															}
														}
													});
												}
											}

											$("#" + id).validate(customerFormValidation);
										});
										/* Customer Details form save code ends here */
									</script>
								</div>
							</div>
						</div>
						<?php

						foreach ($member_details as $policy_name => $member_detail) {

							if (isset($member_detail[$customer_id])) {

						?>
								<div id="accordion<?= $i + $j + $k; ?>" class="according accordion-s2 mt-3">
									<div class="card card-member">
										<div class="card-header card-vif">
											<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion46<?= $i + $j + $k; ?>" aria-expanded="false"> <span class="lbl-card"><?php echo ucwords($policy_name); ?> - Member Details <i class="ti-files"></i></a>
										</div>
										<div id="accordion46<?= $i + $j + $k; ?>" class="collapse card-vis-mar" data-parent="#accordion<?= $i + $j + $k; ?>" style="">
											<div class="card-body">
												<?php
												foreach ($member_detail[$customer_id] as $key => $member) {

													$family_construct = '';
													$adult_count = $proposal_policy[$member['proposal_policy_id']]['adult_count'];
													$child_count = $proposal_policy[$member['proposal_policy_id']]['child_count'];

													if ($adult_count > 0) {

														$family_construct .= $adult_count . 'A';
													}

													if ($child_count > 0) {

														$family_construct .= ' ' . $child_count . 'K';
													}

													$self_class = strtolower($member['member_type']);
													/*<form id="policy<?= $i + $j + $k; ?>" name="policy<?= $i + $j + $k; ?>">*/
													$attributes = 'id="policy' . ($i + $j + $k) . '" name="policy' . ($i + $j + $k) . '"';
													$policy_form_id = 'policy' . ($i + $j + $k);
													echo form_open(base_url() . "customer/memberdetailsform", $attributes);
												?>
													<input type="hidden" name="elem_lead" value="<?= $lead_id; ?>" />
													<input type="hidden" name="elem_customer" value="<?= $customer_id; ?>" />
													<input type="hidden" name="elem_member" value="<?= $key; ?>" />
													<div class="row mt-3">
														<div class="col-md-6 col-12">
															<div class="table-responsive tbl">
																<table class="table table-bordered tbl-600">
																	<tbody>
																		<tr>
																			<td class="wd-25">Salutation</td>
																			<td class="wd-25"><?php echo $member['policy_member_salutation']; ?></td>
																		</tr>
																		<tr>
																			<td class="wd-25">Last Name</td>
																			<td class="wd-25"><input type="text" class="form-control valid ps-rel" name="lastname" class="<?php echo $self_class; ?> lastname" value="<?php echo $member['policy_member_last_name']; ?>" /></td>
																		</tr>
																		<tr>
																			<td>Family Construct</td>
																			<td><?php echo $family_construct; ?></td>
																		</tr>
																		<tr>
																			<td>Gender</td>
																			<td><?php echo $member['policy_member_gender']; ?></td>
																		</tr>
																		<?php

																		if ($si_type_mapping[$member['policy_id']] == 1) {
																		?>
																			<tr>
																				<td>Premium </td>
																				<td><?php echo $proposal_policy[$member['proposal_policy_id']]['tax_amount']; ?></td>
																			</tr>
																			<?php
																		} else if ($si_type_mapping[$member['policy_id']] == 2) {

																			if ($member['relation_with_proposal'] == 1) {

																			?>
																				<tr>
																					<td>Premium </td>
																					<td><?php echo $proposal_policy[$member['proposal_policy_id']]['tax_amount']; ?></td>
																				</tr>
																		<?php
																			}
																		}

																		/*if ($si_type_mapping[$member['policy_id']] == 1) {
																						?>
																						<tr>
																							<td>Premium </td>
																							<td><?php echo $proposal_policy[$member['proposal_policy_id']]['tax_amount']; ?></td>
																						</tr>
																						<tr>
																							<td>Gender</td>
																							<td><?php echo $member['policy_member_gender']; ?></td>
																						</tr>
																						<?php

																					} else if ($si_type_mapping[$member['policy_id']] == 2) {

																						if ($member['relation_with_proposal'] == 1) {

																							?>
																							<tr>
																								<td>Premium </td>
																								<td><?php echo $proposal_policy[$member['proposal_policy_id']]['tax_amount']; ?></td>
																							</tr>
																							<tr>
																								<td>Gender</td>
																								<td><?php echo $member['policy_member_gender']; ?></td>
																							</tr>
																							<?php
																						}
																						else{

																							?>
																							<tr>
																								<td>Gender</td>
																								<td><?php echo $member['policy_member_gender']; ?></td>
																							</tr>
																							<?php
																						}
																					}*/
																		?>
																	</tbody>
																</table>
															</div>
														</div>
														<div class="col-md-6 col-12">
															<div class="table-responsive tbl">
																<table class="table table-bordered tbl-600">
																	<tbody>
																		<tr>
																			<td class="wd-25">First Name</td>
																			<td class="wd-25"><input type="text" class="form-control valid ps-rel" name="firstname" class="<?php echo $self_class; ?> firstname" value="<?php echo $member['policy_member_first_name']; ?>" /></td>
																		</tr>
																		<tr>
																			<td>Sum Insured</td>
																			<td><?php echo $proposal_policy[$member['proposal_policy_id']]['sum_insured']; ?></td>
																		</tr>
																		<tr>
																			<td>Date of Birth</td>
																			<td><?php echo date('d-m-Y', strtotime($member['policy_member_dob'])); ?></td>
																		</tr>
																		<tr>
																			<td>Relation</td>
																			<td><?php echo $member['member_type']; ?></td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="col-md-1 col-6 text-left">
														<button type="submit" class="btn smt-btn member-policy-save">Save</button>
													</div>
													<?php
													/*</form>*/
													echo form_close();
													?>
													<script type="text/javascript">
														/* Member Details form save code starts here */
														$(document).ready(function() {

															id = "<?= $policy_form_id; ?>";

															var vMemberRules = {
																firstname: {
																	required: true
																},
																lastname: {
																	required: true
																}
															};

															var vMemberMessages = {
																firstname: {
																	required: "First name is required"
																},
																lastname: {
																	required: "Last name is required"
																}
															};

															var memberFormValidation = {
																rules: vMemberRules,
																messages: vMemberMessages,
																submitHandler: function(form) {
																	var act = "<?php echo base_url(); ?>customer/memberdetailsform";
																	$("#" + form.id).ajaxSubmit({
																		url: act,
																		type: 'post',
																		dataType: 'json',
																		cache: false,
																		clearForm: false,
																		beforeSubmit: function(arr, $form, options) {

																			$("#" + form.id + " .member-policy-save").text('Saving...').prop('disabled', true);
																		},
																		success: function(response) {

																			$("#" + form.id + " .member-policy-save").text('Save').prop('disabled', false);
																			if (response.response > 0) {

																				displayMsg("success", "Record Updated");
																				setTimeout(function() {

																					window.location.reload();
																				}, 3000);

																			} else {

																				displayMsg("error", "No Changes Made");
																				return false;
																			}
																		}
																	});
																}
															}

															$("#" + id).validate(memberFormValidation);
														});
														/* Member Details form save ends starts here */
													</script>
												<?php
													$k++;
												}
												?>
											</div>
										</div>
									</div>
								</div>
						<?php

								$j++;
							}
						}
						?>
						<div id="accordion<?= $i + 1; ?>" class="according accordion-s2 mt-3">
							<div class="card card-member">
								<div class="card-header card-vif">
									<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion4550<?= $i + 1; ?>" aria-expanded="false"> <span class="lbl-card"> Nominee Details - <i class="ti-file"></i></a>
								</div>
								<div id="accordion4550<?= $i + 1; ?>" class="collapse card-vis-mar" data-parent="#accordion<?= $i + 1; ?>" style="">
									<div class="card-body">
										<div class="row mt-3">
											<div class="col-md-6 col-12">
												<div class="table-responsive tbl">
													<table class="table table-bordered tbl-600">
														<tbody>
															<tr>
																<td class="wd-25">First Name</td>
																<td class="wd-25"><?php echo $customer_detail['nominee_first_name']; ?></td>
															</tr>
															<tr>
																<td>Relation with Proposer</td>
																<td><?php echo $nominee_relations[$customer_detail['nominee_relation']]; ?></td>
															</tr>
															<tr>
																<td class="wd-25">DOB</td>
																<td class="wd-25"><?php echo $customer_detail['nominee_dob']; ?></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="table-responsive tbl">
													<table class="table table-bordered tbl-600">
														<tbody>
															<tr>
																<td class="wd-25">Last Name</td>
																<td class="wd-25"><?php echo $customer_detail['nominee_last_name']; ?></td>
															</tr>
															<tr>
																<td>Contact Number</td>
																<td><?php echo $customer_detail['nominee_contact'] ?></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
											
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="accordion<?= $i + 2; ?>" class="according accordion-s2 mt-3">
							<div class="card card-member">
								<div class="card-header card-vif">
									<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion676<?= $i + 2; ?>" aria-expanded="false"> <span class="lbl-card"> Health Declaration - <i class="ti-heart"></i></a>
								</div>
								<div id="accordion676<?= $i + 2; ?>" class="collapse card-vis-mar" data-parent="#accordion<?= $i + 2; ?>" style="">
									<form action="#">
										<div class="card-body">
											<?php

											echo html_entity_decode(trim($ghd_declaration[$customer_detail['customer_id']]));

											/*
											<input type="hidden" name="customer_id" value="<?php echo $customer_id ?>">
											<input type="hidden" name="lead_id" value="<?php echo $lead_id ?>">*/
											?>
											<div class="col-md-12 text-center">
												<button type="button" class="btn sub-btn mt-2 mb-2 next">Next</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						</p>
					</div>
			<?php
					$i++;
				}
			}
			?>
			<div class="tab-pane fade " id="app<?= $payments_tab; ?>" role="tabpanel" aria-labelledby="app<?= $payments_tab; ?>-tab">
				<p>
				<div class="card card-member">
					<div class="card-header card-vif">
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
										?>
									</td>

									<td style="width: 150px;">
										<div>
											<div class="custom-control custom-radio" style="float: left;">
												<input name="assignment_declaration" class="custom-control-input radios_out" id="assignment_declaration_agree" type="radio" <?php echo ($assignment_declaration_answer == 'Agree') ? 'checked' : ''; ?> value="Agree">
												<label class="custom-control-label" for="assignment_declaration_agree" name="assignment_declaration_label">Agree </label>
											</div>
										</div>
										<div>
											<div class="custom-control custom-radio" style="float:left;">
												<input name="assignment_declaration" class="custom-control-input radios_out" id="assignment_declaration_disagree" type="radio" <?php echo ($assignment_declaration_answer == 'Disagree') ? 'checked' : ''; ?> value="Disagree">
												<label class="custom-control-label" for="assignment_declaration_disagree" name="assignment_declaration_label">Disagree </label>
											</div>
										</div>
										<div>
											&nbsp;
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="col-md-1 col-6 text-left">
							<button class="btn smt-btn next">Next</button>
						</div>
					</div>
				</div>
				</p>
			</div>
			<div class="tab-pane fade " id="app<?= $payments_tab + 1; ?>" role="tabpanel" aria-labelledby="app<?= $payments_tab + 1; ?>-tab">
				<p>
					<?php

					if ($mode_of_payment == 2) {
					?>
				<div id="accordion<?= $payments_tab + 1; ?>" class="according accordion-s2 mt-3">
					<div class="card card-member">
						<div class="card-body">
							<div class="row mt-3">
								<div class="col-md-6 col-12">
									<div class="table-responsive tbl">
										<table class="table table-bordered tbl-600">
											<tbody>
												<tr>
													<td class="wd-25">IFSC Code</td>
													<td class="wd-25"><?php echo $ifsc; ?></td>
												</tr>
												<tr>
													<td>Account Number</td>
													<td><?php echo $account_number; ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-6 col-12">
									<div class="table-responsive tbl">
										<table class="table table-bordered tbl-600 ">
											<tbody>
												<tr>
													<td class="wd-25">Bank Name
													<td class="wd-25"><?php echo $bank_name; ?></td>
												</tr>
												<tr>
													<td>Payment Mode</td>
													<td><?php echo $payment_modes[$mode_of_payment]; ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-12 text-center">
									<button type="button" class="btn sub-btn mt-2 mb-2 validate-customer">Proceed</button>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="table-responsive tbl">
								<table class="table table-bordered tbl-600 ">
									<tbody>
										<tr>
											<td class="wd-25">Bank Name
											<td class="wd-25"><?php echo $bank_name; ?></td>
										</tr>
										<tr>
											<td>Payment Mode</td>
											<td><?php echo $payment_modes[$mode_of_payment];; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-12 text-center">
							<button type="button" class="btn sub-btn mt-2 mb-2 redirect-customer">Proceed</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
					} else {

	?>
		<div id="accordion<?= $payments_tab + 1; ?>" class="according accordion-s2 mt-3">
			<div class="card card-member">
				<div class="card-body">
					<div class="row mt-3">
						<div class="col-md-6 col-12">
							<div class="table-responsive tbl">
								<table class="table table-bordered tbl-600 ">
									<tbody>
										<tr>
											<td>Payment Mode</td>
											<td><?php echo $payment_modes[$mode_of_payment]; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-12 text-center">
							<button type="button" class="btn sub-btn mt-2 mb-2 redirect-customer">Proceed To Generate OTP</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
					}
	?>
	</p>
	</div>
</div>
</div>
</div>

<script>
	var current_tab = $('.nav-link.active').attr('id');

	$(document).ready(function() {

		$('.next').on('click', function() {

			/*let ghd_result = false;

			let ghd_result_message = '';

			$form = $(this).closest('form');

			let ghd_request_data = $form.serializeArray();

			$.ajax({
				url: "<?php echo base_url(); ?>policyproposal/submitGHDDeclarationFromCustomerEnd",
				data: ghd_request_data,
				dataType: 'JSON',
				method: 'post',
				async: false,
				success: function(response) {
					if (response.status == 200) {
						ghd_result = true;
					} else {
						ghd_result_message = response.message;
					}
				}
			});

			if (!ghd_result) {
				displayMsg("error", ghd_result_message);
				return;
			}*/

			if (!$('.nav-link.active').hasClass('payment')) {

				$('#' + current_tab).parent().next('.nav-item').find('.nav-link').trigger('click');
			}

			current_tab = $('.nav-link.active').attr('id');

			$([document.documentElement, document.body]).animate({

				scrollTop: $("#" + current_tab).offset().top
			}, 800);
		});

		$('.nav-item .nav-link').on('click', function() {

			setTimeout(function() {

				current_tab = $('.nav-link.active').attr('id');
			}, 800);
		});

		$('.redirect-customer').on('click', function() {

			//window.location.href = "<?php echo base_url(); ?>paymentgatewayredirect/<?= $lead_id_enc ?>";
			window.location.href = "<?php echo base_url(); ?>customerotpform/<?php echo $lead_id_enc; ?>";
			/*data = {};
			data.lead_id = "<?php echo $lead_id_enc; ?>";

			$.ajax({

				url: "<?php echo base_url(); ?>policyproposal/docustomervalidation",
				type: 'post',
				dataType: 'json',
				data: data,
				processData: false,
				contentType: false,
				async: false,
				success: function(response){

					if(response.status_code == 200){

						window.location.href = "<?php echo base_url(); ?>policyproposal/customerotpform/?text=<?php echo $lead_id_enc; ?>";
					}
					else{

						displayMsg("error", "Something Went Wrong");
					}
				}
			});*/
		});

		// $('input[type="radio"]').attr('disabled', true);
	});

	/* pin code on change code starts here */
	$(document).change(function(e) {

		let name = e.target.name;
		if (name == "pin_code") {

			let pincode = e.target.value;

			var state_city_url = "<?php echo base_url(); ?>api2/getStateCityFromPincode";

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
	/* pin code on change code ends here */

	$('input[type="radio"]').prop('disabled', true);
</script>