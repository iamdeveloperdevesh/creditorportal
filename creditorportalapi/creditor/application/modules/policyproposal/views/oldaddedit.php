<?php //echo "<pre>";print_r($leaddetails);exit;?>
<!-- start: Content -->
<style>
    .select2-container-multi .select2-choices .select2-search-field input {
    padding: 0px!important; 
     margin: 1px 0!important; 
    font-family: sans-serif;
    font-size: 100%;
    color: #666;
    outline: 0;
    border: 0;
    -webkit-box-shadow: none;
    box-shadow: none;
    background: transparent !important;
}
.totalpremium{
	cursor: pointer;
    font-size: 20px;
    line-height: 40px;
    margin-right: 15px;
    color: red;
}

.error,.moberror{color:red;}
.label-primary {
    padding: 5px;
}
.collapse.in {
    display: block;
}
.collapse {
    display: none;
    padding:15px;
}
.card .card-header {
    background-color: #e6e6e6;
    padding: 5px;
    margin-top: 15px;
}
.select2-container-multi .select2-choices {
    background-image: none !important;
    border: 0!important;
}
.agefield{
    margin-left: 15px;
    width: 120px;
    float: left;
}
</style>
<div class="page-body">
	<div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-header-left">
                                <?php if(empty($leaddetails->proposal_policy_id)){ ?>
                                <h3>Add Policy</h3>
                                <?php } else { ?>
                                <h3>Update Policy</h3>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ol class="breadcrumb pull-right">
                                <li class="breadcrumb-item"><a href="<?php echo base_url();?>proposalpolicy/addedit"><i data-feather="home"></i></a></li>
                                <li class="breadcrumb-item active">
                                <?php if(empty($leaddetails->proposal_policy_id)){ ?>
                                Add Policy
                                <?php } else { ?>
                                Update Policy
                                <?php } ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
			<div class="accordion" id="accordionExample">
				<div class="card">       
					<div class="card-body">             
						<div class="box-content">
							<div class="card-header" id="headingOne">
								<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									Applicant Details
								</button>
							</div>
								<div id="collapseOne" class="collapse in" aria-labelledby="headingOne" data-parent="#accordionExample">
									<form id="cust_data" method="post" autocomplete="off">
									   <div class="row mb-5">
										  <div class="col-md-2">
											 <div class="form-group">
												<label for="example-text-input" class="col-form-label">Salutation</label> 
												<select class="form-control" name="salutation" id="salutation" disabled="">
												   <option value="">Select salutation</option>
												   <option value="Mr" <?php if($leaddetails->customer_details[0]->salutation == "Mr"){echo "selected";} ?>>Mr</option>
												   <option value="Mrs" <?php if($leaddetails->customer_details[0]->salutation == "Mrs"){echo "selected";} ?>>Mrs</option>
												   <option value="Ms" <?php if($leaddetails->customer_details[0]->salutation == "Ms"){echo "selected";} ?>>Ms</option>
												</select>
											 </div>
										  </div>
										  <div class="col-md-3">
											 <div class="form-group"> <label for="example-text-input" class="col-form-label">Customer First Name</label>  <input class="form-control" id="firstname" name="firstname" type="text" value="<?php echo $leaddetails->customer_details[0]->first_name; ?>" readonly=""> </div>
										  </div>
										  <div class="col-md-3">
											 <div class="form-group">  <label for="example-text-input" class="col-form-label">Customer Middle Name</label>  <input class="form-control" id="middlename" name="middlename" type="text" value="<?php echo $leaddetails->customer_details[0]->middle_name; ?>" readonly="">  </div>
										  </div>
										  <div class="col-md-3">
											 <div class="form-group">  <label for="example-text-input" class="col-form-label">Customer Last Name</label>  <input class="form-control" id="lastname" name="lastname" type="text" value="<?php echo $leaddetails->customer_details[0]->last_name; ?>" readonly="">  </div>
										  </div>
										  <div class="col-md-3">
											 <div class="form-group">
												<label for="example-tel-input" class="col-form-label">Gender</label> 
												<select id="gender1" name="gender1" class="form-control dis-col" disabled="true">
												   <option value="">Select Gender</option>
												   <option value="Male" <?php if($leaddetails->customer_details[0]->gender == "Male"){echo "selected";} ?>>MALE</option>
												   <option value="Female" <?php if($leaddetails->customer_details[0]->gender == "Female"){echo "selected";} ?>>FEMALE</option>
											   </select>
											 </div>
										  </div>
										  <div class="col-md-2">
											 <div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth</label>  <input class="form-control" type="text" name="dob" id="dob1" value="<?php echo $leaddetails->customer_details[0]->dob; ?>" readonly="" disabled="disabled"> </div>
										  </div>
										  <div class="col-md-3">
											 <div class="form-group"> <label for="example-tel-input" class="col-form-label">Mobile Number</label>  <input class="form-control dis-col" type="text" id="mob_no" name="mob_no" value="<?php echo $leaddetails->customer_details[0]->mobile_no; ?>" maxlength="10" readonly=""> </div>
										  </div>
										  <div class="col-md-3">
											 <div class="form-group">  <label for="example-text-input" class="col-form-label">Mobile Number 2</label> <input class="form-control" type="text" value="<?php echo (!empty($leaddetails->customer_details[0]->mobile_no2))?$leaddetails->customer_details[0]->mobile_no2:''; ?>" id="mobile_no2" name="mobile_no2" maxlength="10" ><div><label class="moberror"></label></div> </div>
										  </div>
										  <div class="col-md-6">
											 <div class="form-group"> <label for="example-email-input" class="col-form-label">Email Id<span style="color:#FF0000">*</span></label>  <input type="email" class="form-control valid dis-col" id="email_id" name="email_id" value="<?php echo $leaddetails->customer_details[0]->email_id; ?>" placeholder="Enter email">  </div>
										  </div>
										  <div class="col-md-6">
											 <div class="form-group"> <label for="example-text-input" class="col-form-label"> Address Line 1 <span style="color:#FF0000">*</span></label> <input class="form-control" type="text" value="<?php echo (!empty($leaddetails->customer_details[0]->address_line1))?$leaddetails->customer_details[0]->address_line1:''; ?>" id="address_line1" name="address_line1"> </div>
										  </div>
										  <div class="col-md-6">
											 <div class="form-group"> <label for="example-text-input" class="col-form-label">Address Line 2</label> <input class="form-control" type="text" value="<?php echo (!empty($leaddetails->customer_details[0]->address_line2))?$leaddetails->customer_details[0]->address_line2:''; ?>" id="address_line2" name="address_line2"> </div>
										  </div>
										  <div class="col-md-6">
											 <div class="form-group"> <label for="example-text-input" class="col-form-label">Address Line 3</label> <input class="form-control" type="text" value="<?php echo (!empty($leaddetails->customer_details[0]->address_line3))?$leaddetails->customer_details[0]->address_line3:''; ?>" id="address_line3" name="address_line3"> </div>
										  </div>
										  <div class="col-md-2">
											 <div class="form-group"> <label for="example-text-input" class="col-form-label">Pin Code<span style="color:#FF0000">*</span></label> <input class="form-control valid" type="text" value="<?php echo (!empty($leaddetails->customer_details[0]->pin_code))?$leaddetails->customer_details[0]->pin_code:''; ?>" name="pin_code" id="pin_code" maxlength="6"><div><label class="error pinerror"></label></div>  </div>
										  </div>
										  <div class="col-md-3">
											 <div class="form-group">  <label for="example-text-input" class="col-form-label">City<span style="color:#FF0000">*</span></label>  <input class="form-control valid dis-col" type="text" value="<?php echo (!empty($leaddetails->customer_details[0]->city))?$leaddetails->customer_details[0]->city:''; ?>" name="city" id="city">  </div>
										  </div>
										  <div class="col-md-3">
											 <div class="form-group">  <label for="example-text-input" class="col-form-label">State<span style="color:#FF0000">*</span></label> <input class="form-control valid dis-col" type="text" value="<?php echo (!empty($leaddetails->customer_details[0]->state))?$leaddetails->customer_details[0]->state:''; ?>" name="state" id="state">  </div>
										  </div>
										  <div class="col-md-12">
											  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
											  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
											  <input type="hidden" name="customer_id" value="<?php echo $leaddetails->customer_details[0]->customer_id; ?>" />
											 <button type="submit" class="btn btn-success btn-lg" id="addcustomerbtn">Submit</button>
										  </div>
									   </div>
									</form>
									
								</div>
								<div class="clearfix"></div>
							</div>
							<?php 
							$i = 0; 
							$combocount = 0;
							$comboids = array();
							$combonames = array();
							$combo_sum_insured = array();
							$si_types = array();
							$si_basis = array();
							$maxmember=0;
							
								foreach($leaddetails->plan_details as $plandetails){ 
									
									if($plandetails->is_combo == 1){
										if($maxmember < $plandetails->max_member_count){
											$maxmember = $plandetails->max_member_count;
										}
										$combocount++;
										array_push($comboids,$plandetails->policy_id);
										array_push($si_types,$plandetails->sitype_id);
										array_push($si_basis,$plandetails->basis_id);
										array_push($combonames,$plandetails->policy_sub_type_name);
										foreach($plandetails->policy_premium as $premium){
											array_push($combo_sum_insured,$premium->sum_insured);
										}
									} 
									$i++;
									array_unique($combo_sum_insured);
									sort($combo_sum_insured);
								}
								$membersrelation = array();
								$member_details = array();
								if(!empty($leaddetails->proposal_policy_details)){
									$allmembers = array();
									$amount = array();
									$tax = array();
									$totalamount = 0;
									$preid = 0;
									$proposal_sum_insured = "";
									$proposal_child_count = "";
									$proposal_adult_count = "";
									
									foreach($leaddetails->proposal_policy_details as $policy_details){
										if(in_array($policy_details->master_policy_id,$comboids)){
											
											$proposal_sum_insured = $policy_details->sum_insured;
											$proposal_child_count = $policy_details->child_count;
											$proposal_adult_count = $policy_details->adult_count;
											$amount[$policy_details->master_policy_id] = 0;
											$tax[$policy_details->master_policy_id] = 0;
											$amount[$policy_details->master_policy_id] = $policy_details->premium_amount;
											$tax[$policy_details->master_policy_id] = $policy_details->tax_amount;
											$totalamount += $policy_details->premium_amount + $policy_details->tax_amount;
											$members = array();
											$allmembers[$policy_details->master_policy_id] = array();
											foreach($policy_details->policy_members as $member){
												$members['relation'] = $member->relation_with_proposal;
												$members['gender'] = $member->policy_member_gender;
												$members['first_name'] = $member->policy_member_first_name;
												$members['last_name'] = $member->policy_member_last_name;
												$members['dob'] = $member->policy_member_dob;
												$members['age'] = $member->policy_member_age;
												$members['id'] = $member->member_unique_id;
												array_push($allmembers[$policy_details->master_policy_id],$members);
												
											}
											
											if($preid != 0){
												if(count($allmembers[$policy_details->master_policy_id]) > count($allmembers[$preid])){
													$member_details = $allmembers[$policy_details->master_policy_id];
												}
											}else{
												$member_details = $allmembers[$policy_details->master_policy_id];
											}
											$preid = $policy_details->master_policy_id;
										}
									}
									
									foreach($member_details as $member){
										array_push($membersrelation,$member['relation']);
									}
									
								}
								
							?>
							<?php if($combocount > 0){ ?>
							<div class="box-content combo">
								<div class="card-header" id="comboproposalheading">
									<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#comboproposalcollapse" aria-expanded="false" aria-controls="comboproposalcollapse">
										
										<?php $k = 1;
										foreach($combonames as $comboname){
											if(count($combonames) == $k){
											echo $comboname; }else{
											echo $comboname." + ";	
											}
											$k++;
										}
										?>
										
									</button>
									<div class="pull-right totalpremium"><?php if(!empty($totalamount) && $totalamount != 0){echo $totalamount;} ?></div>
								</div>
								<form class="form-horizontal" id="comboform" method="post" enctype="multipart/form-data">
								<div id="comboproposalcollapse" class="collapse" aria-labelledby="comboproposalheading" data-parent="#accordionExample">
									<div class="row">
											<input type="hidden" class="maxmember" name="maxmebercount" value="<?php echo $maxmember; ?>">
											<?php $j=0;foreach($comboids as $id) {?>
											<input type="hidden" name="policy_nos[]" value="<?php echo $id; ?>">
											<input type="hidden" name="sitypes[]" value="<?php echo $si_types[$j]; ?>">
											<input type="hidden" name="policy_subtype_names[]" value="<?php echo $combonames[$j]; ?>">
											<input type="hidden" name="sibasis[]" value="<?php echo $si_basis[$j]; ?>">
											<input type="hidden" class="pamount" data-policyamount="<?php echo $id; ?>" name="policy_amount[]" value="<?php if(!empty($amount)){echo $amount[$id]; } ?>">
											<input type="hidden" class="ptax" data-policytax="<?php echo $id; ?>" name="policy_tax[]" value="<?php if(!empty($tax)){echo $tax[$id]; } ?>">
											<?php $j++;} ?>
											<input type="hidden" class="totalpremiumamount" value="" />
										   <div class="col-md-3">
											  <div class="form-group">
												 <label class="col-form-label">Select Sum Insured<span style="color:#FF0000">*</span></label> 
												 <select class="form-control sum_insured" name="sum_insured" <?php if(!empty($proposal_sum_insured)){echo "disabled"; } ?>>
													<option value="">Select Sum insured</option>
													<?php foreach($combo_sum_insured as $premium){ ?>
													<option value="<?php echo $premium; ?>" <?php if(!empty($proposal_sum_insured) && ($proposal_sum_insured == $premium)){echo "selected"; } ?>><?php echo $premium; ?></option>
													<?php } ?>
												 </select>
											  </div>
										   </div>
										   <div class="col-md-3">
											  <div class="form-group">
												 <label class="col-form-label">Select Adult Count<span style="color:#FF0000">*</span></label> 
												 <select data-parent="comboproposalcollapse" class="form-control adultcount" name="adultcount" <?php if(!empty($proposal_adult_count)){echo "disabled"; } ?>>
													<option value="">Select</option>
													<?php for($m = 1;$m <= $maxmember;$m++){ ?>
													<option value="<?php echo $m; ?>" <?php if(!empty($proposal_adult_count) && ($proposal_adult_count == $m)){echo "selected"; } ?>><?php echo $m; ?></option>
													<?php } ?>
												 </select>
											  </div>
										   </div>
										   <div class="col-md-3">
											  <div class="form-group">
												 <label class="col-form-label">Select Child Count<span style="color:#FF0000">*</span></label> 
												 <select data-parent="comboproposalcollapse" class="form-control childcount" name="childcount" <?php if(!empty($proposal_child_count)){echo "disabled"; } ?>>
													<option value="">Select</option>
													<?php if(!empty($proposal_child_count)){echo "<option value='$proposal_child_count' selected>$proposal_child_count</option>"; } ?>
												 </select>
											  </div>
										   </div>
										   
										   <div class="row col-md-12" style="padding:0px; margin-left:0px;">
											  <div class="col-md-3">
												 <div class="form-group">
													<label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
													<select data-parent="comboproposalcollapse" class="form-control relation" name="family_members_id">
													   <option value="">Select</option>
													   <?php foreach($leaddetails->family_members as $member) { ?>
													   <?php if($member->id == 1 || $member->id == 2) { ?>
													   <option value="<?php echo $member->id ?>" <?php if(in_array($member->id,$membersrelation)){?> style="display:none;" <?php } ?>><?php echo $member->member_type ?></option>
													   <?php }else{ ?>
													   <option value="<?php echo $member->id ?>"><?php echo $member->member_type ?></option>
													   <?php }} ?>
													</select>
													<input type="hidden" name="family_members_name" value="" class="family_members_name" />
												 </div>
											  </div>
											  <div class="col-md-1">
												 <div class="form-group">
													<label class="col-form-label">Salutation<span style="color:#FF0000">*</span></label> 
													<select data-parent="comboproposalcollapse" class="form-control family_salutation" name="family_salutation" style="padding: 5px 0px; font-size: 13px !important;">
													   <option value="">Select</option>
													   <option value="Mr" class="male_gen">Mr</option>
													   <option value="Mrs" class="female_gen">Mrs</option>
													   <option value="Ms" class="female_gen">Ms</option>
													</select>
												 </div>
											  </div>
											  <div class="col-md-2">
												 <div class="form-group">
													<label class="col-form-label">Gender<span style="color:#FF0000">*</span></label> <input data-parent="comboproposalcollapse" class="form-control family_gender dis-col" type="text" name="family_gender">
												 </div>
											  </div>
											  <div class="col-md-3">
												 <div class="form-group"> <label for="example-text-input" class="col-form-label">First Name <span style="color:#FF0000">*</span></label> <input class="form-control first_name sahil" type="text" value="" name="first_name" autocomplete="off" maxlength="50"> </div>
											  </div>
											  <div class="col-md-3">
												 <div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name <span style="color:#FF0000">*</span></label> <input class="form-control last_name" type="text" value="" name="last_name" maxlength="50" autocomplete="off"> <span id="err_last_nameArr" class="error"></span> </div>
											  </div>
											  <div class="col-md-3">
												 <div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label> <input class="form-control dobdatepicker" data-parent="comboproposalcollapse" onchange="calculateage(this.value,comboproposalcollapse)" autocomplete="off" type="text" id="combo_family_date_birth" name="family_date_birth" readonly="readonly"> <span id="err_family_date_birthArr" class="error"></span> </div>
											  </div>
											  <div class="col-md-3" style="display: block">
												 <div class="form-group"> <label for="example-text-input" class="col-form-label">Age <span style="color:#FF0000">*</span></label> <input class="form-control dis-col age" data-parent="comboproposalcollapse" type="text" id="combo_age" name="age" readonly> </div>
											  </div>
											  <div class="col-md-12">
												  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
												  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
												  <input type="hidden" name="proposal_details_id" value="<?php echo $leaddetails->proposal_details[0]->proposal_details_id; ?>" />
												  <?php if($maxmember > count($member_details)){ ?>
												  <button type="submit" id="ComboFormSubmit" class="btn add-dep-xd">Add Insured Member</button><br><br>
												  <?php } ?>
											  </div>
										   </div>
										   <div class="table-responsive col-md-12">
											  <span style="color:#FF0000; font-size:12px;">*To modify Sum Insured/Family Construct, Remove below members.</span> 
											  <table class="table table-hover progress-table text-center" style="font-size:12px;border: 1px solid silver;">
												 <thead class="text-uppercase table-col-1">
													<tr>
													   <th scope="col">Relation</th>
													   <th scope="col">First Name</th>
													   <th scope="col">Last Name</th>
													   <th scope="col">Gender</th>
													   <th scope="col">Date Of Birth</th>
													   <th scope="col">Age</th>
													   <th scope="col">Delete</th>
													</tr>
												 </thead>
												 <tbody id="patTable" data-parent="comboproposalcollapse" style="pointer-events: auto;">
												 <?php if(!empty($member_details)){ 
												 foreach($member_details as $details){
												 ?>
													<tr>
														<td>
														<?php 
														foreach($leaddetails->family_members as $member) { 
														 if($member->id == $details['relation']){echo $member->member_type;}
														} 
														?>
														</td>
														<td><?php echo $details['first_name']; ?></td>
														<td><?php echo $details['last_name']; ?></td>
														<td><?php echo $details['gender']; ?></td>
														<td><?php echo $details['dob']; ?></td>
														<td><?php echo $details['age']; ?></td>
														<td><a href='javascript:void(0);' class='btn btn-sm removemember' data-member="<?php echo $details['relation']; ?>" data-key="<?php echo $details['id']; ?>">Remove</a></td>
													</tr>
												 <?php }} ?>
												 </tbody>
											  </table>
										   </div>
										</div>
								</div>
								</form>
							</div>
							<?php } ?>
							<?php $i = 1; foreach($leaddetails->plan_details as $plandetails){ 
							if($plandetails->is_combo != 1){
							$membersrelation = array();
							$member_details = array();
							if(!empty($leaddetails->proposal_policy_details)){
									$allmembers = array();
									$amount = array();
									$tax = array();
									$totalamount = 0;
									$preid = 0;
									$proposal_sum_insured = "";
									$proposal_child_count = "";
									$proposal_adult_count = "";
									
									foreach($leaddetails->proposal_policy_details as $policy_details){
										if($policy_details->master_policy_id == $plandetails->policy_id){
											
											$proposal_sum_insured = $policy_details->sum_insured;
											$proposal_child_count = $policy_details->child_count;
											$proposal_adult_count = $policy_details->adult_count;
											$amount[$policy_details->master_policy_id] = 0;
											$tax[$policy_details->master_policy_id] = 0;
											$amount[$policy_details->master_policy_id] = $policy_details->premium_amount;
											$tax[$policy_details->master_policy_id] = $policy_details->tax_amount;
											$totalamount += $policy_details->premium_amount + $policy_details->tax_amount;
											$members = array();
											$allmembers[$policy_details->master_policy_id] = array();
											foreach($policy_details->policy_members as $member){
												$members['relation'] = $member->relation_with_proposal;
												$members['gender'] = $member->policy_member_gender;
												$members['first_name'] = $member->policy_member_first_name;
												$members['last_name'] = $member->policy_member_last_name;
												$members['dob'] = $member->policy_member_dob;
												$members['age'] = $member->policy_member_age;
												$members['id'] = $member->member_unique_id;
												array_push($allmembers[$policy_details->master_policy_id],$members);
												
											}
											$member_details = $allmembers[$policy_details->master_policy_id];
											
										}
									}
									
									foreach($member_details as $member){
										array_push($membersrelation,$member['relation']);
									}
									
								}
							?>
							<div class="box-content <?php if($plandetails->is_optional == 1){echo 'optional';}else{echo 'mandate';} ?>">
								<div class="card-header" id="proposalheading<?php echo $i;?>">
									<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#proposalcollapse<?php echo $i;?>" aria-expanded="false" aria-controls="proposalcollapse<?php echo $i;?>">
										<?php echo $plandetails->policy_sub_type_name; ?>
									</button>
									<div class="pull-right totalpremium"><?php if(!empty($totalamount) && $totalamount != 0){echo $totalamount;} ?></div>
								</div>
								<form class="form-horizontal" id="memberform<?php echo $i;?>" method="post" enctype="multipart/form-data">
								<div id="proposalcollapse<?php echo $i;?>" class="collapse" aria-labelledby="proposalheading<?php echo $i;?>" data-parent="#accordionExample">
									<div class="row">
											<input type="hidden" class="maxmember" name="maxmebercount" value="<?php echo $plandetails->max_member_count; ?>">
											<input type="hidden" name="policy_nos[]" value="<?php echo $plandetails->policy_id; ?>">
											<input type="hidden" name="policy_subtype_names[]" value="<?php echo $plandetails->policy_sub_type_name; ?>">
											<input type="hidden" name="sitypes[]" value="<?php echo $plandetails->sitype_id; ?>">
											<input type="hidden" name="sibasis[]" value="<?php echo $plandetails->basis_id; ?>">
											<input type="hidden" class="pamount" data-policyamount="<?php echo $plandetails->policy_id; ?>" name="policy_amount[]" value="<?php if(!empty($amount)){echo $amount[$plandetails->policy_id]; } ?>">
											<input type="hidden" class="ptax" data-policytax="<?php echo $plandetails->policy_id; ?>" name="policy_tax[]" value="<?php if(!empty($tax)){echo $tax[$plandetails->policy_id]; } ?>">
											<div class="col-md-3">
											  <div class="form-group">
												 <label class="col-form-label">Select Sum Insured<span style="color:#FF0000">*</span></label> 
												 <select class="form-control sum_insured" data-parent="proposalcollapse<?php echo $i;?>" name="sum_insured" id="sum_insured<?php echo $i; ?>" <?php if(!empty($proposal_sum_insured)){echo "disabled"; } ?>>
													<option value="">Select Sum insured</option>
													<?php 
													$sum_insured = array();
													foreach($plandetails->policy_premium as $premium){
													array_push($sum_insured,$premium->sum_insured);}
													$sum_insured = array_unique($sum_insured);
													sort($sum_insured);
													foreach($sum_insured as $premium){ ?>
													<option value="<?php echo $premium; ?>" <?php if(!empty($proposal_sum_insured) && ($proposal_sum_insured == $premium)){echo "selected"; } ?>><?php echo $premium; ?></option>
													<?php } ?>
												 </select>
											  </div>
										   </div>
										   <div class="col-md-3">
											  <div class="form-group">
												 <label class="col-form-label">Select Adult Count<span style="color:#FF0000">*</span></label> 
												 <select class="form-control adultcount" data-parent="proposalcollapse<?php echo $i;?>" name="adultcount" <?php if(!empty($proposal_adult_count)){echo "disabled"; } ?>>
													<option value="">Select</option>
													<?php for($m = 1;$m <= $plandetails->max_member_count;$m++){ ?>
													<option value="<?php echo $m; ?>" <?php if(!empty($proposal_adult_count) && ($proposal_adult_count == $m)){echo "selected"; } ?>><?php echo $m; ?></option>
													<?php } ?>
												 </select>
											  </div>
										   </div>
										   <div class="col-md-3">
											  <div class="form-group">
												 <label class="col-form-label">Select Child Count<span style="color:#FF0000">*</span></label> 
												 <select class="form-control childcount" data-parent="proposalcollapse<?php echo $i;?>" name="childcount" <?php if(!empty($proposal_child_count)){echo "disabled"; } ?>>
													<option value="">Select</option>
													<?php if(!empty($proposal_child_count)){echo "<option value='$proposal_child_count' selected>$proposal_child_count</option>"; } ?>
												 </select>
											  </div>
										   </div>
										   
										   <div class="row col-md-12" style="padding:0px; margin-left:0px;">
											  <div class="col-md-3">
												 <div class="form-group">
													<label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
													<select class="form-control relation" data-parent="proposalcollapse<?php echo $i;?>" name="family_members_id">
													   <option value="">Select</option>
													   <?php foreach($leaddetails->family_members as $member) { ?>
													   <?php if($member->id == 1 || $member->id == 2) { ?>
													   <option value="<?php echo $member->id ?>" <?php if(in_array($member->id,$membersrelation)){?> style="display:none;" <?php } ?>><?php echo $member->member_type ?></option>
													   <?php }else{ ?>
													   <option value="<?php echo $member->id ?>"><?php echo $member->member_type ?></option>
													   <?php }} ?>
													</select>
													<input type="hidden" name="family_members_name" value="" class="family_members_name" />
												 </div>
											  </div>
											  <div class="col-md-1">
												 <div class="form-group">
													<label class="col-form-label">Salutation<span style="color:#FF0000">*</span></label> 
													<select class="form-control family_salutation" data-parent="proposalcollapse<?php echo $i;?>" name="family_salutation" style="padding: 5px 0px; font-size: 13px !important;">
													   <option value="">Select</option>
													   <option value="Mr" class="male_gen">Mr</option>
													   <option value="Mrs" class="female_gen">Mrs</option>
													   <option value="Ms" class="female_gen">Ms</option>
													</select>
												 </div>
											  </div>
											  <div class="col-md-2">
												 <div class="form-group">
													<label class="col-form-label">Gender<span style="color:#FF0000">*</span></label> <input class="form-control family_gender dis-col" type="text" name="family_gender">
												 </div>
											  </div>
											  <div class="col-md-3">
												 <div class="form-group"> <label for="example-text-input" class="col-form-label">First Name <span style="color:#FF0000">*</span></label> <input class="form-control first_name" type="text" value="" name="first_name" autocomplete="off" maxlength="50"> </div>
											  </div>
											  <div class="col-md-3">
												 <div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name <span style="color:#FF0000">*</span></label> <input class="form-control last_name" type="text" value="" name="last_name" maxlength="50" autocomplete="off"> <span id="err_last_nameArr error"></span> </div>
											  </div>
											  <div class="col-md-3">
												 <div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label> <input class="form-control dobdatepicker" data-parent="proposalcollapse<?php echo $i;?>" autocomplete="off" type="text" onchange="calculateage(this.value,proposalcollapse<?php echo $i;?>)" id="family_date_birth<?php echo $i; ?>" name="family_date_birth" readonly="readonly"> <span class="err_family_date_birthArr"></span> </div>
											  </div>
											  <div class="col-md-3" style="display: block">
												 <div class="form-group"> <label for="example-text-input" class="col-form-label">Age <span style="color:#FF0000">*</span></label> <input class="form-control dis-col age" type="text" id="age<?php echo $i; ?>" name="age" readonly> </div>
											  </div>
											  <div class="col-md-12">
												  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
												  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
												  <input type="hidden" name="proposal_details_id" value="<?php echo $leaddetails->proposal_details[0]->proposal_details_id; ?>" />
												  <?php if($plandetails->max_member_count > count($member_details)){ ?>
												  <button type="submit" id="FormSubmit<?php echo $i; ?>" class="btn add-dep-xd">Add Insured Member</button><br><br>
												  <?php } ?>
											  </div>
										   </div>
										   <div class="table-responsive col-md-12">
											  <span style="color:#FF0000; font-size:12px;">*To modify Sum Insured/Family Construct, Remove below members.</span> 
											  <table class="table table-hover progress-table text-center" style="font-size:12px;border: 1px solid silver;">
												 <thead class="text-uppercase table-col-1">
													<tr>
													   <th scope="col">Relation</th>
													   <th scope="col">First Name</th>
													   <th scope="col">Last Name</th>
													   <th scope="col">Gender</th>
													   <th scope="col">Date Of Birth</th>
													   <th scope="col">Age</th>
													   <th scope="col">Delete</th>
													</tr>
												 </thead>
												 <tbody id="patTable" data-parent="proposalcollapse<?php echo $i;?>" style="pointer-events: auto;">
													<?php if(!empty($member_details)){ 
												 foreach($member_details as $details){
												 ?>
													<tr>
														<td>
														<?php 
														foreach($leaddetails->family_members as $member) { 
														 if($member->id == $details['relation']){echo $member->member_type;}
														} 
														?>
														</td>
														<td><?php echo $details['first_name']; ?></td>
														<td><?php echo $details['last_name']; ?></td>
														<td><?php echo $details['gender']; ?></td>
														<td><?php echo $details['dob']; ?></td>
														<td><?php echo $details['age']; ?></td>
														<td><a href='javascript:void(0);' class='btn btn-sm removemember' data-member="<?php echo $details['relation']; ?>" data-key="<?php echo $details['id']; ?>">Remove</a></td>
													</tr>
												 <?php }} ?>
												 </tbody>
											  </table>
										   </div>
										</div>
								</div>
								</form>
							</div>
							<script>
								var memberRules<?php echo $i; ?> = {
									sum_insured:{required:true},
									adultcount:{required:true},
									first_name:{required:true},
									last_name:{required:true},
									age:{required:true}
								};

								var memberMessages<?php echo $i; ?> = {
									sum_insured:{required:"Field is required"},
									adultcount:{required:"Field is required"},
									first_name:{required:"Field is required"},
									last_name:{required:"Field is required"},
									age:{required:"Field is required"}
								};

								$("#memberform<?php echo $i; ?>").validate({
									rules: memberRules<?php echo $i; ?>,
									messages: memberMessages<?php echo $i; ?>,
									submitHandler: function(form) 
									{
										var act = "<?php echo base_url();?>policyproposal/submitmemberForm";
										var form = "#memberform<?php echo $i; ?>";
										$(form).find('.sum_insured').attr('disabled',false);
										$(form).find('.adultcount').attr('disabled',false);
										$(form).find('.childcount').attr('disabled',false);
										$("#memberform<?php echo $i; ?>").ajaxSubmit({
											url: act, 
											type: 'post',
											dataType: 'json',
											cache: false,
											clearForm: false, 
											beforeSubmit : function(arr, $form, options){
												$(form).find(".btn").hide();
												
											},
											success: function (response) 
											{
												
												$(form).find('.sum_insured').attr('disabled',true);
												$(form).find('.adultcount').attr('disabled',true);
												$(form).find('.childcount').attr('disabled',true);
												$(form).find(".btn").show();
												if(response.success)
												{
													console.log(response);
													$.each(response.data, function(index, element) {
														$(form).find("[data-policyamount='" + index + "']").val(element.amount);
														$(form).find("[data-policytax='" + index + "']").val(element.tax);
													});
													calculatepremium(form);
													var relation = $(form).find('.relation').val();
													if(relation == 1){
														$(form).find('.relation option[value="1"]').hide();
													}
													if(relation == 2){
														$(form).find('.relation option[value="2"]').hide();
													}
													$(form).find(".sum_insured").attr('disabled',true);
													$(form).find(".adultcount").attr('disabled',true);
													$(form).find(".childcount").attr('disabled',true);
													$(form).find('.family_salutation').val('');
													$(form).find('.relation').val('');
													$(form).find('.family_gender').val('');
													$(form).find('.first_name').val('');
													$(form).find('.last_name').val('');
													$(form).find('#combo_family_date_birth').val('');
													$(form).find('.age').val('');
													$(form).find('.family_gender').attr('readonly',false);
													$(form).find('.first_name').attr('readonly',false);
													$(form).find('.last_name').attr('readonly',false);
													
													$(form).find("#patTable").append(response.html);
													displayMsg("success",response.msg);
												}
												else
												{	
													displayMsg("error",response.msg);
													return false;
												}
											}
										});
									}
								});
								function calculatepremium(parent){
									var amount = 0;
									$( parent+" .pamount" ).each( function(){
									  amount += parseFloat( $( this ).val() ) || 0;
									});
									var tax = 0;
									$( parent+" .ptax" ).each( function(){
									  tax += parseFloat( $( this ).val() ) || 0;
									});
									var total = amount+tax;
									$(parent).parent().find('.totalpremium').html(total);
									$(parent).find('.totalpremiumamount').val(total);
								}
								</script>
							<?php $i++;}} ?>
							 
							<div class="box-content">
							<div class="card-header" id="headingTwo">
								<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
									Nominee Details
								</button>
							</div>
								<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
									<form id="nominee_data" name="nominee_data" autocomplete="off">
									   <div class="row wrapper">
											 <div class="col-lg-3">
												<div class="form-group">
												   <label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
												   <select class="form-control nominee_relation" name="nominee_relation" id="nominee_relation">
													  <option value="">Select Nominee</option>
													  <option data-opt="Female" value="1" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 1){echo "selected";} ?>>Spouse</option>
													  <option data-opt="Male" value="2" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 2){echo "selected";} ?>>Son</option>
													  <option data-opt="Female" value="3" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 3){echo "selected";} ?>>Daughter</option>
													  <option data-opt="Female" value="4" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 4){echo "selected";} ?>>Mother</option>
													  <option data-opt="Male" value="5" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 5){echo "selected";} ?>>Father</option>
													  <option data-opt="Male" value="6" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 6){echo "selected";} ?>>Father-in-law</option>
													  <option data-opt="Female" value="7" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 7){echo "selected";} ?>>Mother-in-law</option>
													  <option data-opt="Male" value="8" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 8){echo "selected";} ?>>Brother</option>
													  <option data-opt="Female" value="9" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 9){echo "selected";} ?>>Sister</option>
													  <option data-opt="Male" value="10" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 10){echo "selected";} ?>>Grandfather</option>
													  <option data-opt="Female" value="11" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 11){echo "selected";} ?>>Grandmother</option>
													  <option data-opt="Male" value="12" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 12){echo "selected";} ?>>Grandson</option>
													  <option data-opt="Female" value="13" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 13){echo "selected";} ?>>Granddaughter</option>
													  <option data-opt="Male" value="14" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 14){echo "selected";} ?>>Son-in-law</option>
													  <option data-opt="Female" value="15" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 15){echo "selected";} ?>>Daughter-in-law</option>
													  <option data-opt="Male" value="16" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 16){echo "selected";} ?>>Brother-in-law</option>
													  <option data-opt="Female" value="17" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 17){echo "selected";} ?>>Sister-in-law</option>
													  <option data-opt="Male" value="18" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 18){echo "selected";} ?>>Nephew</option>
													  <option data-opt="Female" value="19" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[0]->nominee_relation == 19){echo "selected";} ?>>Niece</option>
												   </select>
												</div>
											 </div>
											 <div class="col-lg-3">
												<div class="form-group"> <label for="example-text-input" class="col-form-label">First Name<span style="color:#FF0000">*</span></label> <input class="form-control nominee_fname first_name" type="text" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->nominee_first_name;} ?>" maxlength="50" id="nominee_first_name" autocomplete="off" name="nominee_first_name"> </div>
											 </div>
											 <div class="col-lg-3">
												<div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name<span style="color:#FF0000">*</span></label> <input class="form-control nominee_lname last_name" type="text" maxlength="50" autocomplete="off" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->nominee_last_name;} ?>" id="nominee_last_name" name="nominee_last_name"> </div>
											 </div>
											 <div class="col-md-1">
												<div class="form-group">
												   <label class="col-form-label">Salutation<span style="color:#FF0000">*</span></label> 
												   <select class="form-control nominee_salutation" name="nominee_salutation" id="nominee_salutation" style="padding: 5px 0px; font-size: 13px !important;">
													  <option value="">Select</option>
													  <option value="Mr" class="nominee_male_gen" <?php if(!empty($leaddetails->proposal_details) && ($leaddetails->proposal_details[0]->nominee_salutation == "Mr")){echo "selected";} ?>>Mr</option>
													  <option value="Mrs" class="nominee_female_gen" <?php if(!empty($leaddetails->proposal_details) && ($leaddetails->proposal_details[0]->nominee_salutation == "Mrs")){echo "selected";} ?>>Mrs</option>
													  <option value="Ms" class="nominee_female_gen" <?php if(!empty($leaddetails->proposal_details) && ($leaddetails->proposal_details[0]->nominee_salutation == "Ms")){echo "selected";} ?>>Ms</option>
												   </select>
												</div>
											 </div>
											 <div class="col-md-2">
												<div class="form-group"> <label for="example-text-input" class="col-form-label">Gender<span style="color:#FF0000">*</span></label> <input class="form-control nominee_gender" type="text" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->nominee_gender;} ?>" name="nominee_gender" id="nominee_gender" readonly=""> </div>
											 </div>
											 <div class="col-lg-3">
												<div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth</label><span style="color:#FF0000">*</span>  <input class="form-control datepicker" type="text" name="nominee_dob" id="nominee_dob" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->nominee_dob;} ?>" readonly=""> </div>
											 </div>
											 <div class="col-lg-3">
												<div class="form-group"> <label for="example-date-input" class="col-form-label">Contact No</label> <input class="form-control nominee_contact" type="text" autocomplete="off" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->nominee_contact;} ?>" id="nominee_contact" name="nominee_contact" maxlength="10"><div><label class="moberror"></label></div> </div>
											 </div>
											 <div class="col-lg-3">
												<div class="form-group"> <label for="example-date-input" class="col-form-label">Email</label> <input class="form-control" type="email" name="nominee_email" id="nominee_email" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->nominee_email;} ?>"> </div>
											 </div>
											 <div class="col-md-12">
											  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
											  <input type="hidden" name="plan_id" value="<?php echo $leaddetails->customer_details[0]->plan_id; ?>" />
											  <input type="hidden" name="customer_id" value="<?php echo $leaddetails->customer_details[0]->customer_id; ?>" />
											  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
											 <button type="submit" class="btn btn-success btn-lg" id="addnomineebtn">Submit</button>
										  </div>
									   </div>
									</form>
									
								</div>
								<div class="clearfix"></div>
							</div>
							<form class="form-horizontal" id="finalform" method="post" enctype="multipart/form-data">
							<div class="box-content">
							<div class="card-header" id="headingThree">
								<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
									Payment Details
								</button>
							</div>
								<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
									<div class="row wrapper">
										<div class="col-lg-3"> 
											<div class="form-group"> 
												<label for="example-text-input" class="col-form-label">Mode Of Payment</label> 
												<select class="form-control" name="mode_of_payment" id="mode_of_payment">
													<option value="">Select</option>
													<?php foreach($leaddetails->payment_modes as $paymode){ ?>
													<option value="<?php echo $paymode->payment_mode_name; ?>" <?php if(!empty($leaddetails->proposal_details) && ($leaddetails->proposal_details[0]->mode_of_payment ==$paymode->payment_mode_name)){echo "selected";} ?>><?php echo $paymode->payment_mode_name; ?></option>
													<?php } ?>
												</select>
											</div> 
										</div>
										<div class="col-lg-3"> 
											<div class="form-group"> 
												<label for="example-date-input" class="col-form-label">Preferred contact date</label> 
												<input class="form-control predatepicker" autocomplete="off" type="text" val="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->preffered_contact_date;} ?>" id="preffered_contact_date" name="preffered_contact_date" readonly=""> 
											</div> 
										</div> 
										<div class="col-md-3  bootstrap-timepicker timepicker"> 
											<div class="form-group"> 
												<label for="example-text-input" class="col-form-label">Preferred contact Time</label> 
												<input class="form-control" type="time" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->preffered_contact_time;} ?>" id="preffered_contact_time" autocomplete="off" name="preffered_contact_time" placeholder="HH:MM"> 
											</div> 
										</div> 
										<div class="col-md-3" style="display: block"> 
											<div class="form-group"> 
											<label for="example-text-input" class="col-form-label">Remark</label> 
											<input type="text" maxlength="30" class="form-control" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->remark;} ?>" name="remark" id="remark"> 
											</div> 
										</div> 
									</div>
									<div class="chequedetails" <?php if(!empty($leaddetails->proposal_details) && ($leaddetails->proposal_details[0]->mode_of_payment == "Cheque")){ ?>style="display:block" <?php }else{ ?>style="display:none" <?php } ?>>
									<div class="row">
										<div class="col-md-3"> 
											<div class="form-group"> 
											<label for="example-text-input" class="col-form-label">Bank Name<span style="color:#FF0000">*</span></label> 
											<input type="text" class="form-control" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->bank_name;} ?>" name="bank_name" id="bank_name"><div><span class="error bank_name_error"></span></div> 
											</div> 
										</div>
										<div class="col-md-3"> 
											<div class="form-group"> 
											<label for="example-text-input" class="col-form-label">Branch Name<span style="color:#FF0000">*</span></label> 
											<input type="text" class="form-control" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->bank_branch;} ?>" name="bank_branch" id="bank_branch"><div><span class="error bank_branch_error"></span></div> 
											</div> 
										</div>
										<div class="col-md-3"> 
											<div class="form-group"> 
											<label for="example-text-input" class="col-form-label">Branch City<span style="color:#FF0000">*</span></label> 
											<input type="text" class="form-control" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->bank_city;} ?>" name="bank_city" id="bank_city"><div><span class="error bank_city_error"></span></div>
											</div> 
										</div>
										<div class="col-md-3"> 
											<div class="form-group"> 
											<label for="example-text-input" class="col-form-label">IFSC Code<span style="color:#FF0000">*</span></label> 
											<input type="text" class="form-control" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->ifsc_code;} ?>" name="ifsc_code" id="ifsc_code"><div><span class="error ifsc_code_error"></span></div>
											</div> 
										</div> 
										<div class="col-md-3"> 
											<div class="form-group"> 
											<label for="example-text-input" class="col-form-label">Account Number<span style="color:#FF0000">*</span></label> 
											<input type="text" class="form-control" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->account_number;} ?>" name="account_number" id="account_number"><div><span class="error account_number_error"></span></div> 
											</div> 
										</div>
										<div class="col-md-3"> 
											<div class="form-group"> 
											<label for="example-text-input" class="col-form-label">Cheque Number<span style="color:#FF0000">*</span></label> 
											<input type="text" class="form-control" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->cheque_number;} ?>" name="cheque_number" id="cheque_number"><div><span class="error cheque_number_error"></span></div>
											</div> 
										</div>
										<div class="col-md-3"> 
											<div class="form-group"> 
											<label for="example-text-input" class="col-form-label">Cheque Date<span style="color:#FF0000">*</span></label> 
											<input type="text" class="form-control predatepicker" value="<?php if(!empty($leaddetails->proposal_details)){echo $leaddetails->proposal_details[0]->cheque_date;} ?>" name="cheque_date" id="cheque_date" readonly><div><span class="error cheque_date_error"></span></div>
											</div> 
										</div>
										<div class="col-md-12">
											<input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
											<input type="hidden" name="proposal_id" value="<?php echo $leaddetails->proposal_details[0]->proposal_details_id; ?>" />
											<button type="submit" class="btn btn-success btn-lg" id="addfinalbtn">Submit</button>
										</div>
									</div>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							
						</form>
					</div>              
				</div>
				
			</div>
</div>
<div class="modal fade" id="totalpremiummodal" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
			<h4 class="modal-title text-center">Total Premium</h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

<!-- end: Content -->			
<script>

$(".totalpremium").click(function(){
	var policy_nos = $(this).parent().parent().find('input[name="policy_subtype_names[]"]').map(function(){return $(this).val();}).get();
	var policy_amounts = $(this).parent().parent().find('input[name="policy_amount[]"]').map(function(){return $(this).val();}).get();
	var policy_taxes = $(this).parent().parent().find('input[name="policy_tax[]"]').map(function(){return $(this).val();}).get();
	var i = 0;
	var total = 0;
	console.log(policy_nos.length);
	var html = "<table class='table'><tr><th>Product Name</th><th>Amount</th><th>Tax</th><th>Total</th></tr>";
	for(i = 0;i < policy_nos.length;i++){
		html+="<tr>";
		html+="<td>"+policy_nos[i]+"</td>";
		html+="<td>"+policy_amounts[i]+"</td>";
		html+="<td>"+policy_taxes[i]+"</td>";
		html+="<td>"+(parseFloat(policy_amounts[i])+parseFloat(policy_taxes[i]))+"</td>";
		html+="</tr>";
		total = parseFloat(total)+parseFloat(policy_amounts[i])+parseFloat(policy_taxes[i]);
	}
	html += "<tr><th colspan='3'>Total Amount</th><th>"+total+"</th></tr></table>";
	console.log(html);
	$("#totalpremiummodal .modal-body").html(html);
	$("#totalpremiummodal").modal("show");
});
function calculateage(dob,parent){
	dob = new Date(dob);
	var today = new Date();
	var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
	$(parent).find('.age').val(age);
}
$("#mode_of_payment").change(function(){
	var val = $(this).val();
	if(val == "Cheque"){$(".chequedetails").show();}else{$(".chequedetails").hide();}
});
$('.relation').change(function(){
	var val = $(this).val();
	var parent = $(this).attr('data-parent');
	var text = $("#"+parent).find( ".relation option:selected" ).text();
	$("#"+parent).find('.family_members_name').val(text);
	if(val == 1){
		var dob = new Date('<?php echo $leaddetails->customer_details[0]->dob ?>');
		var today = new Date();
		var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
		$("#"+parent).find('.family_salutation').val('<?php echo $leaddetails->customer_details[0]->salutation ?>');
		$("#"+parent).find('.family_gender').val('<?php echo $leaddetails->customer_details[0]->gender ?>');
		$("#"+parent).find('.first_name').val('<?php echo $leaddetails->customer_details[0]->first_name ?>');
		$("#"+parent).find('.last_name').val('<?php echo $leaddetails->customer_details[0]->last_name ?>');
		$("#"+parent).find('input[name="family_date_birth"]').val('<?php echo $leaddetails->customer_details[0]->dob ?>');
		$("#"+parent).find('.age').val(age);
		$("#"+parent).find('.family_salutation').attr('readonly',true);
		$("#"+parent).find('.family_gender').attr('readonly',true);
		$("#"+parent).find('.first_name').attr('readonly',true);
		$("#"+parent).find('.last_name').attr('readonly',true);
	}else{
		$("#"+parent).find('.first_name').val('');
		$("#"+parent).find('.last_name').val('');
		$("#"+parent).find('input[name="family_date_birth"]').val('');
		$("#"+parent).find('.age').val('');
		$("#"+parent).find('.family_salutation').attr('readonly',false);
		$("#"+parent).find('.first_name').attr('readonly',false);
		$("#"+parent).find('.last_name').attr('readonly',false);
	}
}); 
$('.adultcount').change(function(){
	var val = $(this).val();
	var parent = $(this).attr('data-parent');
	
	var max = $("#"+parent).find('.maxmember').val();
	var total = max-val;
	console.log(total);
	html="<option value=''>Select</option>";
	if(total != 0){
		
		for(var i = 1;i<=total;i++){
			html+="<option value='"+i+"'>"+i+"</option>";
		}
	}
	$("#"+parent).find('.childcount').html(html);
});
$(".predatepicker" ).datepicker({  
		changeMonth: true,
		changeYear: true,
		minDate: new Date() });
$("#nominee_dob" ).datepicker({  
		changeMonth: true,
		changeYear: true,
		maxDate: new Date() });
$(".dobdatepicker" ).datepicker({  changeMonth: true,
		changeYear: true,
		maxDate: new Date() });
$("#nominee_relation").change(function(){
    var val = $(this).find(':selected').attr('data-opt');
    if(val != ""){
        if(val == "Male"){
            $("#nominee_salutation").val("Mr");
            $("#nominee_gender").val("Male");
            $(".nominee_female_gen").css('display','none');
            $(".nominee_male_gen").css('display','block');
        }else{
            $("#nominee_gender").val("Female");
            $("#nominee_salutation").val("");
            $("#nominee_salutation").attr("readonly",false);
            $(".nominee_female_gen").css('display','block');
            $(".nominee_male_gen").css('display','none');
        }
    }else{
        $("#nominee_gender").val("");
        $("#nominee_salutation").val("");
        $("#nominee_salutation").attr("readonly",false);
        $(".nominee_female_gen").css('display','block');
        $(".nominee_male_gen").css('display','block');
    }
    
});
$("#nominee_salutation").change(function(){
    var val = $(this).val();
    if(val != ""){
        if(val == "Mr"){
            $("#nominee_gender").val("Male");
        }else{
            $("#nominee_gender").val("Female");
        }
    }else{
        $("#nominee_gender").val("");
    }
    
});
var vRules = {
	address_line1:{required:true,minlength:10},
	city:{required:true},
	state:{required:true},
	pin_code:{required:true,number:true,minlength:6,maxlength:6}
};

var vMessages = {
	address_line1:{required:"Address is required",minlength:"Address should be at least 10 character"},
	city:{required:"City is required"},
	state:{required:"State is required"},
	pin_code:{required:"Pincode is required",number:"Pincode should be numeric and 6 digit",minlength:"Pincode should be numeric and 6 digit"}
};

$("#cust_data").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
	    
		var act = "<?php echo base_url();?>policyproposal/submitForm";
		$("#cust_data").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
			    var mob = $("#mobile_no2").val();
        	    if(mob != ''){
        	        var filter = /^[6789]\d{9}$/;
                	if (filter.test(mob)) {
                	     $(".moberror").html("");
                    }
                    else {
                        $(".moberror").html("Please enter valid phone number");
                        return false;
                    }
        	    }
				$(".btn-primary").hide();
				//return false;
			},
			success: function (response) 
			{
			    
				$(".btn-primary").show();
				if(response.success)
				{
					displayMsg("success",response.msg);
					$('#collapseTwo').addClass('in');
					$('#collapseOne').removeClass('in');
					
				}
				else
				{	
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	}
});


var vRules1 = {
	nominee_relation:{required:true},
	nominee_first_name:{required:true},
	nominee_last_name:{required:true},
	nominee_dob:{required:true},
	nominee_salutation:{required:true},
	nominee_gender:{required:true}
};

var vMessages1 = {
	nominee_relation:{required:"Field is required"},
	nominee_first_name:{required:"Field is required"},
	nominee_last_name:{required:"Field is required"},
	nominee_dob:{required:"Field is required"},
	nominee_salutation:{required:"Field is required"},
	nominee_gender:{required:"Field is required"}
};

$("#nominee_data").validate({
	rules: vRules1,
	messages: vMessages1,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>policyproposal/submitForm1";
		$("#nominee_data").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
			    var mob = $("#nominee_contact").val();
        	    if(mob != ''){
        	        var filter = /^[6789]\d{9}$/;
                	if (filter.test(mob)) {
                	     $(".moberror").html("");
                    }
                    else {
                        $(".moberror").html("Please enter valid phone number");
                        return false;
                    }
        	    }
				$(".btn-primary").hide();
				//return false;
			},
			success: function (response) 
			{
			    
				$(".btn-primary").show();
				if(response.success)
				{
					displayMsg("success",response.msg);
					$('#collapseTwo').addClass('in');
					$('#collapseOne').removeClass('in');
					
				}
				else
				{	
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	}
});
document.title = "Add/Edit Policy Proposal";
</script>
<script>
var comboRules = {
	sum_insured:{required:true},
	adultcount:{required:true},
	first_name:{required:true},
	last_name:{required:true},
	age:{required:true}
};

var comboMessages = {
	sum_insured:{required:"Field is required"},
	adultcount:{required:"Field is required"},
	first_name:{required:"Field is required"},
	last_name:{required:"Field is required"},
	age:{required:"Field is required"}
};

$("#comboform").validate({
	rules: comboRules,
	messages: comboMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>policyproposal/submitmemberForm";
		var form = "#comboform";
		$(form).find('.sum_insured').attr('disabled',false);
		$(form).find('.adultcount').attr('disabled',false);
		$(form).find('.childcount').attr('disabled',false);
		$("#comboform").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
			   
				$(form).find(".btn").hide();
				//return false;
			},
			success: function (response) 
			{
			    $(form).find('.sum_insured').attr('disabled',true);
				$(form).find('.adultcount').attr('disabled',true);
				$(form).find('.childcount').attr('disabled',true);
				$(form).find(".btn").show();
				if(response.success)
				{
					console.log(response);
					$.each(response.data, function(index, element) {
						console.log(index);
						console.log(element.tax);
						$(form).find("[data-policyamount='" + index + "']").val(element.amount);
						$(form).find("[data-policytax='" + index + "']").val(element.tax);
					});
					calculatepremium(form);
					var relation = $(form).find('.relation').val();
					if(relation == 1){
						$(form).find('.relation option[value="1"]').hide();
					}
					if(relation == 2){
						$(form).find('.relation option[value="2"]').hide();
					}
					$(form).find(".sum_insured").attr('disabled',true);
					$(form).find(".adultcount").attr('disabled',true);
					$(form).find(".childcount").attr('disabled',true);
					$(form).find('.family_salutation').val('');
					$(form).find('.relation').val('');
					$(form).find('.family_gender').val('');
					$(form).find('.first_name').val('');
					$(form).find('.last_name').val('');
					$(form).find('#combo_family_date_birth').val('');
					$(form).find('.age').val('');
					$(form).find('.family_gender').attr('readonly',false);
					$(form).find('.first_name').attr('readonly',false);
					$(form).find('.last_name').attr('readonly',false);
					
					$(form).find("#patTable").append(response.html);
					displayMsg("success",response.msg);
				}
				else
				{	
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	}
});

$(document).on('click','.removemember',function(){
	var parent = "#"+$(this).parent().parent().parent().data('parent');
	var element = $(this);
	var id = $(this).data('key');
	var relation = $(this).data('member');
	var formid = "#"+$(parent).parent().attr('id');
	var lead_id = $(parent).find('input[name="lead_id"]').val();
	var policy_nos = $(parent).find('input[name="policy_nos[]"]').map(function(){return $(this).val();}).get();
	var sibasis = $(parent).find('input[name="sibasis[]"]').map(function(){return $(this).val();}).get();
	var act = "<?php echo base_url();?>policyproposal/deletemember";
	$.ajax({
			url: act, 
			type: 'post',
			dataType: 'json',
			data:{id:id,policy_nos:policy_nos,sibasis:sibasis,lead_id:lead_id},
			cache: false,
			success: function (response) 
			{
				console.log(response);
				if(response.success)
				{
					element.parent().parent().remove();
					$.each(response.data, function(index, element) {
						$(parent).find("[data-policyamount='" + index + "']").val(element.amount);
						$(parent).find("[data-policytax='" + index + "']").val(element.tax);
					});
					if(relation == 1){
						$(parent).find('.relation option[value="1"]').show();
						
					}
					if(relation == 2){
						$(parent).find('.relation option[value="2"]').show();
						
					}
					calculatepremium(formid);
					displayMsg("success",response.msg);
				}
				else
				{	
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
});
function calculatepremium(parent){
	var amount = 0;
	$( parent+" .pamount" ).each( function(){
	  amount += parseFloat( $( this ).val() ) || 0;
	});
	var tax = 0;
	$( parent+" .ptax" ).each( function(){
	  tax += parseFloat( $( this ).val() ) || 0;
	});
	var total = amount+tax;
	if(total == 0){$(parent).parent().find('.totalpremium').html("");}else{
	$(parent).parent().find('.totalpremium').html(total);}
	$(parent).find('.totalpremiumamount').val(total);
}
</script>


<script>
var finalRules = {
	mode_of_payment:{required:true}
};

var finalMessages = {
	mode_of_payment:{required:"Field is required"}
};

$("#finalform").validate({
	rules: finalRules,
	messages: finalMessages,
	submitHandler: function(form) 
	{
		var act = "<?php echo base_url();?>policyproposal/submitfinalForm";
		var form = "#finalform";
		var cheque_date = $("#cheque_date").val();
		var cheque_number = $("#cheque_number").val();
		var account_number = $("#account_number").val();
		var ifsc_code = $("#ifsc_code").val();
		var bank_city = $("#bank_city").val();
		var bank_branch = $("#bank_branch").val();
		var bank_name = $("#bank_name").val();
		var error = 0;
		if(cheque_date == ''){$(".cheque_date_error").html("*This field is required"); error = 1;}else{$(".cheque_date_error").html("");}
		if(account_number == ''){$(".account_number_error").html("*This field is required"); error = 1;}else{$(".account_number_error").html("");}
		if(cheque_number == ''){$(".cheque_number_error").html("*This field is required"); error = 1;}else{$(".cheque_date_error").html("");}
		if(ifsc_code == ''){$(".ifsc_code_error").html("*This field is required"); error = 1;}else{$(".ifsc_code_error").html("");}
		if(bank_city == ''){$(".bank_city_error").html("*This field is required"); error = 1;}else{$(".bank_city_error").html("");}
		if(bank_branch == ''){$(".bank_branch_error").html("*This field is required"); error = 1;}else{$(".bank_branch_error").html("");}
		if(bank_name == ''){$(".bank_name_error").html("*This field is required"); error = 1;}else{$(".bank_name_error").html("");}
		if(error == 1){return false;}
		$("#finalform").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
			    
				$(".btn").hide();
				//return false;
			},
			success: function (response) 
			{
			    
				$(".btn-primary").show();
				if(response.success)
				{
					displayMsg("success",response.msg);
					$('#collapseTwo').addClass('in');
					$('#collapseOne').removeClass('in');
					
				}
				else
				{	
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	
	
	}
});


</script>

