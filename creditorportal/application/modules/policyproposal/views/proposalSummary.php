<div class="col-lg-10 mt-2">
	<div class="card-body pad-0">
		<ul class="nav nav-tabs mb-scroll" id="myTab" role="tablist">
			<?php
			$i = 1;
			$j = 0;
			$k = 10;
			foreach ($customer_details as $lead_id => $customers) {
			//echo "<PRE>";print_r($customers);exit;
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
			<li class="">
				<a class="edit-pro" href="<?php echo base_url(); ?>policyproposal/addedit?text=<?php echo $lead_id_enc; ?>" title="Edit Proposal"><i class="ti-pencil"></i></a>
			</li>
		</ul>
		<div class="tab-content mt-3" id="myTabContent">
			<?php
			$i = 1;
			$health_declaration = [];
			foreach ($customer_details as $lead_id => $customers) {

				foreach ($customers as $customer_id => $customer_detail) {

					$tab_text = $class = '';
					$ifsc = $bank_name = $account_number = $mode_of_payment = '';

					$health_declaration[$customer_id] = $customer_detail['health_declaration'];

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
									<div class="card-body">
										<div class="row mt-3">
											<div class="col-md-6 col-12">
												<div class="table-responsive tbl">
													<table class="table tbl-600 table-bordered">
														<tbody>
															<tr>
																<td class="wd-25">Lead Id</td>
																<td class="wd-25"><?php echo $customer_detail['trace_id']; ?></td>
															</tr>
															<tr>
																<td>First Name</td>
																<td><?php echo $customer_detail['first_name']; ?></td>
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
																<td>Communication Address</td>
																<td><?php echo $customer_detail['address_line1'] . " " . $customer_detail['address_line2'] . " " . $customer_detail['address_line3']; ?></td>
															</tr>
															<tr>
																<td>City</td>
																<td><?php echo $customer_detail['city'] ?></td>
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
																<td class="wd-25">Salutation</td>
																<td class="wd-25"><?php echo $customer_detail['salutation'] ?></td>
															</tr>
															<tr>
																<td>Last Name</td>
																<td><?php echo $customer_detail['last_name'] ?></td>
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
																<td>Pincode</td>
																<td><?php echo $customer_detail['pincode'] ?></td>
															</tr>
															<tr>
																<td>State</td>
																<td><?php echo $customer_detail['state'] ?></td>
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
						<?php

						foreach ($member_details as $policy_name => $member_detail) {

							if(isset($member_detail[$customer_id])){
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
													?>
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
																				<td class="wd-25"><?php echo $member['policy_member_last_name']; ?></td>
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
																				<td class="wd-25"><?php echo $member['policy_member_first_name']; ?></td>
																			</tr>
																			<tr>
																				<td>Family Construct</td>
																				<td><?php echo $family_construct; ?></td>
																			</tr>
																			<tr>
																				<td>Relation</td>
																				<td><?php echo $member['member_type']; ?></td>
																			</tr>
																			<tr>
																				<td>DOB</td>
																				<td><?php echo $member['policy_member_dob']; ?></td>
																			</tr>
																			<?php /*
																				if($si_type_mapping[$member['policy_id']] == 1){

																					?>
																					<tr>
																						<td>Gender</td>
																						<td><?php echo $member['policy_member_gender']; ?></td>
																					</tr>
																					<?php
																				}
																				else if ($si_type_mapping[$member['policy_id']] == 2) {

																					if ($member['relation_with_proposal'] == 1) {

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
														</div>
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
						<div id="accordion<?= $i+1; ?>" class="according accordion-s2 mt-3">
							<div class="card card-member">
								<div class="card-header card-vif">
									<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion4550<?= $i+1; ?>" aria-expanded="false"> <span class="lbl-card"> Nominee Details - <i class="ti-file"></i></a>
								</div>
								<div id="accordion4550<?= $i+1; ?>" class="collapse card-vis-mar" data-parent="#accordion<?= $i+1; ?>" style="">
									<div class="card-body">
										<div class="row mt-3">
											<div class="col-md-6 col-12">
												<div class="table-responsive tbl">
													<table class="table table-bordered tbl-600">
														<tbody>
															<tr>
																<td class="wd-25">First Name</td>
																<td class="wd-25"><?php echo $customer_detail['nominee_first_name'] ?></td>
															</tr>
															<tr>
																<td>Relation with Proposer</td>
																<td><?php echo $nominee_relations[$customer_detail['nominee_relation']]; ?></td>
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
																<td class="wd-25"><?php echo $customer_detail['nominee_last_name'] ?></td>
															</tr>
															<tr>
																<td>Contact Number</td>
																<td><?php echo $customer_detail['nominee_contact'] ?></td>
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
																<td class="wd-25">Date of Birth</td>
																<td class="wd-25"><?php echo $customer_detail['nominee_dob'] ?></td>
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
						<?php
						/*
				?>
				<div id="accordion7" class="according accordion-s2 mt-3">
					<div class="card card-member">
					<div class="card-header card-vif">
						<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion6886" aria-expanded="false"> <span class="lbl-card"> Auto Renewal  - <i class="ti-reload"></i></a>
					</div>
					<div id="accordion6886" class="collapse card-vis-mar" data-parent="#accordion6" style="">
						<div class="card-body">
							<div class="row mt-3">
								<div class="col-md-6 col-12">
								<div class="table-responsive tbl">
									<table class="table table-bordered tbl-600">
										<tbody>
											<tr>
											<td class="wd-25">Consent to Auto Renew</td>
											<td class="wd-25">Yes</td>
											</tr>
											<tr>
											<td>Account Number</td>
											<td>xxxxxxxxx</td>
											</tr>
											<tr>
											<td>Start Date</td>
											<td>26-04-1998</td>
											</tr>
											<tr>
											<td>Debit Type</td>
											<td>xxxxxx</td>
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
											<td class="wd-25">To be Paid</td>
											<td class="wd-25">xxxxxxxx</td>
											</tr>
											<tr>
											<td>Frequency of Payment</td>
											<td>Yearly</td>
											</tr>
											<tr>
											<td>End Date</td>
											<td>6-8-2020</td>
											</tr>
											<tr>
											<td>Contact Number</td>
											<td>98767565757</td>
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
				<?php
					*/
						?>
						<div id="accordion<?= $i+2; ?>" class="according accordion-s2 mt-3">
							<div class="card card-member">
								<div class="card-header card-vif">
									<a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion676<?= $i+2; ?>" aria-expanded="false"> <span class="lbl-card"> Health Declaration - <i class="ti-heart"></i></a>
								</div>
								<div id="accordion676<?= $i+2; ?>" class="collapse card-vis-mar" data-parent="#accordion<?= $i+2; ?>" style="">
									<div class="card-body">
										<?php /* ?><div class="table-responsive tbl" mt-3>
								<table class="table table-bordered text-center">
								<thead>
									<tr>
										<th scope="col" style="width: 750px; text-align: left; font-weight: 600;">Questionnaire</th>
										<th scope="col" style="font-weight: 600;">Answer</th>
									</tr>
								</thead>
								<tbody id="mydatas">
									<tr>
										<td style="text-align:left;font-size: 13px;
											letter-spacing: 0.2px;"> <input type="hidden" class="mycontent" value="4">Do you and/or any other proposed member ever been diagnosed with or had signs/symptoms or advised/taken treatment or surgery for any of the following -
											<br><br>
											1. Heart Disease, Peripheral Vascular Disease, procedures like Angioplasty/PTCA/By Pass Surgery , Tuberculosis (TB), any Respiratory / Lung disease
											<br>
											2. Disease of Eye, Ear, Nose, Throat, Thyroid, Paralysis, Polio, Cancer, Tumour, lump, cyst, ulcer
											<br>
											3. Disease of Kidney, Liver/Gall Bladder, Pancreas, Digestive tract, Breast, Reproductive /Urinary system, or complications of pregnancy
											<br>
											4. Disease of the Brain/Spine/Nervous System, Epilepsy, Joints/Arthritis, Congenital/ Birth defect, Physical deformity/disability, HIV/AIDS, Sexually Transmitted Disease
											<br>
											5. Any other pre-existing disease/ abnormal test reports (apart from viral fever or common cold, malaria or common diarrhea)
										</td>
										<td style="width: 150px;">
											<div class="custom-control custom-radio" style="float: left;"><input type="radio" name="4" id="4" class="custom-control-input radios_out" value="Yes"> <label class="custom-control-label" for="4"> Yes </label> </div>
											<div class="custom-control custom-radio" style="float:right;"> <input type="radio" name="4" class="custom-control-input radios_out " value="No" id="4_1" checked="">  <label class="custom-control-label" for="4_1"> No </label></div>
										</td>
									</tr>
								</tbody>
								</table>
							</div><?php */ ?>

										<?php

										echo html_entity_decode(trim($ghd_declaration[$customer_detail['customer_id']]));
										?>
										<script type="text/javascript">
											$("#accordion676<?php echo $i; ?> #mydatas tr").append(`
												<td style="width: 150px;">
													<div>
														<div class="custom-control custom-radio" style="float: left;">
															<input name="health-declaration<?php echo $i; ?>" class="custom-control-input radios_out health-check-yes" id="4<?php echo $i; ?>" name="4<?php echo $i; ?>" type="radio" value="Yes" <?php if (isset($health_declaration[$customer_id]) && $health_declaration[$customer_id] == "Yes") { ?>checked<?php } ?> />
															<label class="custom-control-label" for="4<?php echo $i; ?>" name="4<?php echo $i; ?>"> Yes </label></div>
														</div>
														<div>
															<div class="custom-control custom-radio" style="float:right;">
																<input name="health-declaration<?php echo $i; ?>" class="custom-control-input radios_out health-check-no" id="4_1<?php echo $i; ?>" name="4<?php echo $i; ?>" type="radio" value="No" <?php if (isset($health_declaration[$customer_id]) && $health_declaration[$customer_id] == "No") { ?>checked<?php } ?> />&nbsp;
																<label class="custom-control-label" for="4_1<?php echo $i; ?>" name="4<?php echo $i; ?>"> No </label>
															</div>
														</div>
													<div>
														&nbsp;
													</div>
												</td>
											`);
										</script>
										<div class="col-md-12 text-center">
											<button type="button" class="btn sub-btn mt-2 mb-2 next">Next</button>
										</div>
									</div>
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
											?>
										</td>

										<td style="width: 150px;">
											<div>
												<div class="custom-control custom-radio" style="float: left;">
													<input name="assignment_declaration" class="custom-control-input radios_out" id="assignment_declaration_agree" type="radio" <?php echo ($assignment_declaration_answer == 'Agree') ? 'checked' : ''; ?> value="Agree">&nbsp;
													<label class="custom-control-label" for="assignment_declaration_agree" name="assignment_declaration_label"> Agree </label>
												</div>
											</div>
											<div>
												<div class="custom-control custom-radio" style="float:left;">
													<input name="assignment_declaration" class="custom-control-input radios_out" id="assignment_declaration_disagree" type="radio" <?php echo ($assignment_declaration_answer == 'Disagree') ? 'checked' : ''; ?> value="Disagree">&nbsp;
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
							<div class="col-md-1 col-6 text-left">
								<button class="btn smt-btn next">Next</button>
							</div>
						</div>
					</div>
					</p>
				</div>
			</div>
			<?php

				if ($mode_of_payment == 2) {

				?>
					<div class="tab-pane fade " id="app<?= $payments_tab + 1; ?>" role="tabpanel" aria-labelledby="app<?= $payments_tab + 1; ?>-tab">
						<p>
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
										<button type="button" class="btn sub-btn mt-2 mb-2 generate-opt">Submit</button>
									</div>
								</div>
							</div>
						</div>
						</p>
					</div>
				<?php
				} else {

				?>
					<div class="tab-pane fade " id="app<?= $payments_tab + 1; ?>" role="tabpanel" aria-labelledby="app<?= $payments_tab + 1; ?>-tab">
						<p>
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
										<button type="button" class="btn sub-btn mt-2 mb-2 generate-opt">Submit</button>
									</div>
								</div>
							</div>
						</div>
						</p>
					</div>
				<?php
				}
				?>
		</div>
	</div>
			</div>
	<script>
		var current_tab = $('.nav-link.active').attr('id');

		$(document).ready(function() {

			$('.next').on('click', function() {

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

			$('.generate-opt').on('click', function() {

				data = {};
				data.lead_id = "<?php echo $lead_id_enc; ?>";

				$.ajax({

					url: "<?php echo base_url(); ?>policyproposal/submitsummary",
					type: 'post',
					dataType: 'json',
					data: data,
					cache: false,
					async: false,
					beforeSend: function(){

						$('.generate-opt').attr('disabled', 'disabled');
					},
					success: function(response) {

						if(response.success){
                                if(response.code == 205){
                                    window.location = response.link;
                                }
							displayMsg("success", response.msg);
						}
						else{

							displayMsg("error", response.msg);
						}

						$('.generate-opt').removeAttr('disabled');
						return false;
					}
				});
			});

			$('input').prop('disabled', true);
		});
	</script>