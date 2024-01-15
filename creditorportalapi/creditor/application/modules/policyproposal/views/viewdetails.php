<?php //echo "<pre>";print_r($leaddetails);exit;?>
<!-- start: Content -->
<?php $memberformcount = 1;$nomineecount = 1; $coapplicant_count = $leaddetails->customer_details[0]->coapplicant_no; ?>
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
.form-control {
    border-radius: 0;
    background: white!important;
    border: 0px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
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
	<div id="applicantcontainer">
	<div id="applicant">
		<div class="card-header">
				Applicant Details
		</div>
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
			if(!empty($leaddetails->proposal_details[0]->proposal_policy_details)){
				$allmembers = array();
				$amount = array();
				$tax = array();
				$totalamount = 0;
				$preid = 0;
				$proposal_sum_insured = "";
				$proposal_child_count = "";
				$proposal_adult_count = "";
				
				foreach($leaddetails->proposal_details[0]->proposal_policy_details as $policy_details){
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
		<div class="combo" id="applicantmember<?php echo $memberformcount; ?>">
			<div class="card-header">
					<?php $k = 1;
					foreach($combonames as $comboname){
						if(count($combonames) == $k){
						echo $comboname; }else{
						echo $comboname." + ";	
						}
						$k++;
					}
					?>
				<div class="pull-right totalpremium"><?php if(!empty($totalamount) && $totalamount != 0){echo $totalamount;} ?></div>
			</div>
			<form class="form-horizontal memberform" data-sub-parent="applicant" data-parent="applicantcontainer" id="memberform<?php echo $memberformcount;?>" method="post" enctype="multipart/form-data">
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
						 <select data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control adultcount" name="adultcount" <?php if(!empty($proposal_adult_count)){echo "disabled"; } ?>>
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
						 <select data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control childcount" name="childcount" <?php if(!empty($proposal_child_count)){echo "disabled"; } ?>>
							<option value="">Select</option>
							<?php if(!empty($proposal_child_count)){echo "<option value='$proposal_child_count' selected>$proposal_child_count</option>"; } ?>
						 </select>
					  </div>
				   </div>
				   
				   <div class="row col-md-12" style="padding:0px; margin-left:0px;">
					  <div class="col-md-3">
						 <div class="form-group">
							<label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
							<select data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control relation" name="family_members_id">
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
							<select data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control family_salutation" name="family_salutation" style="padding: 5px 0px; font-size: 13px !important;">
							   <option value="">Select</option>
							   <option value="Mr" class="male_gen">Mr</option>
							   <option value="Mrs" class="female_gen">Mrs</option>
							   <option value="Ms" class="female_gen">Ms</option>
							</select>
						 </div>
					  </div>
					  <div class="col-md-2">
						 <div class="form-group">
							<label class="col-form-label">Gender<span style="color:#FF0000">*</span></label> <input data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control family_gender dis-col" type="text" name="family_gender">
						 </div>
					  </div>
					  <div class="col-md-3">
						 <div class="form-group"> <label for="example-text-input" class="col-form-label">First Name <span style="color:#FF0000">*</span></label> <input class="form-control first_name sahil" type="text" value="" name="first_name" autocomplete="off" maxlength="50"> </div>
					  </div>
					  <div class="col-md-3">
						 <div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name <span style="color:#FF0000">*</span></label> <input class="form-control last_name" type="text" value="" name="last_name" maxlength="50" autocomplete="off"> <span id="err_last_nameArr" class="error"></span> </div>
					  </div>
					  <div class="col-md-3">
						 <div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label> <input class="form-control dobdatepicker" data-parent="applicantmember<?php echo $memberformcount; ?>" onchange="calculateage(this.value,applicantmember<?php echo $memberformcount; ?>)" autocomplete="off" type="text" name="family_date_birth" readonly="readonly"> <span class="err_family_date_birthArr error"></span> </div>
					  </div>
					  <div class="col-md-3" style="display: block">
						 <div class="form-group"> <label for="example-text-input" class="col-form-label">Age <span style="color:#FF0000">*</span></label> <input class="form-control dis-col age" data-parent="applicantmember<?php echo $memberformcount; ?>" type="text" name="age" readonly> </div>
					  </div>
					  <div class="col-md-12">
						  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
						  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
						  <input type="hidden" name="proposal_details_id" value="<?php echo $leaddetails->proposal_details[0]->proposal_details_id; ?>" />
						  <?php if($maxmember > count($member_details)){ ?>
						  <a href="javascript:void(0)" data-form="memberform<?php echo $memberformcount;?>" class="btn add-dep-xd memberformsubmit">Add Insured Member</a><br><br>
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
							 <tbody class="patTable" id="applicantmember<?php echo $memberformcount; ?>patTable" data-parent="applicantmember<?php echo $memberformcount; ?>" style="pointer-events: auto;">
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
			</form>
		</div>
		<?php $memberformcount++;} ?>
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
				
				foreach($leaddetails->proposal_details[0]->proposal_policy_details as $policy_details){
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
		<div class="box-content <?php if($plandetails->is_optional == 1){echo 'optional';}else{echo 'mandate';} ?>" id="applicantmember<?php echo $memberformcount; ?>">
			<div class="card-header">
				<?php echo $plandetails->policy_sub_type_name; ?>
				<div class="pull-right totalpremium"><?php if(!empty($totalamount) && $totalamount != 0){echo $totalamount;} ?></div>
			</div>
			<form class="form-horizontal memberform" data-sub-parent="applicant" data-parent="applicantcontainer" id="memberform<?php echo $memberformcount; ?>" method="post" enctype="multipart/form-data">
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
							 <select class="form-control sum_insured" data-parent="applicantmember<?php echo $memberformcount; ?>" name="sum_insured" id="sum_insured<?php echo $i; ?>" <?php if(!empty($proposal_sum_insured)){echo "disabled"; } ?>>
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
							 <select class="form-control adultcount" data-parent="applicantmember<?php echo $memberformcount; ?>" name="adultcount" <?php if(!empty($proposal_adult_count)){echo "disabled"; } ?>>
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
							 <select class="form-control childcount" data-parent="applicantmember<?php echo $memberformcount; ?>" name="childcount" <?php if(!empty($proposal_child_count)){echo "disabled"; } ?>>
								<option value="">Select</option>
								<?php if(!empty($proposal_child_count)){echo "<option value='$proposal_child_count' selected>$proposal_child_count</option>"; } ?>
							 </select>
						  </div>
					   </div>
					   
					   <div class="row col-md-12" style="padding:0px; margin-left:0px;">
						  <div class="col-md-3">
							 <div class="form-group">
								<label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
								<select class="form-control relation" data-parent="applicantmember<?php echo $memberformcount; ?>" name="family_members_id">
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
								<select class="form-control family_salutation" data-parent="applicantmember<?php echo $memberformcount; ?>" name="family_salutation" style="padding: 5px 0px; font-size: 13px !important;">
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
							 <div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name <span style="color:#FF0000">*</span></label> <input class="form-control last_name" type="text" value="" name="last_name" maxlength="50" autocomplete="off"> <span class="err_last_nameArr error"></span> </div>
						  </div>
						  <div class="col-md-3">
							 <div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label> <input class="form-control memberdob dobdatepicker" data-parent="applicantmember<?php echo $memberformcount; ?>" autocomplete="off" type="text" onchange="calculateage(this.value,applicantmember<?php echo $memberformcount; ?>)" name="family_date_birth" readonly="readonly"> <span class="err_family_date_birthArr error"></span> </div>
						  </div>
						  <div class="col-md-3" style="display: block">
							 <div class="form-group"> <label for="example-text-input" class="col-form-label">Age <span style="color:#FF0000">*</span></label> <input class="form-control dis-col age" type="text" name="age" readonly> </div>
						  </div>
						  <div class="col-md-12">
							  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
							  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
							  <input type="hidden" name="proposal_details_id" value="<?php echo $leaddetails->proposal_details[0]->proposal_details_id; ?>" />
							  <?php if($plandetails->max_member_count > count($member_details)){ ?>
							  <a href="javascript:void(0)" data-form="memberform<?php echo $memberformcount;?>" class="btn add-dep-xd memberformsubmit">Add Insured Member</a><br><br>
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
							 <tbody class="patTable" id="applicantmember<?php echo $memberformcount; ?>patTable" data-parent="applicantmember<?php echo $memberformcount; ?>" style="pointer-events: auto;">
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
			</form>
		</div>
		<?php $i++;$memberformcount++;}} ?>
		<div id="nomineecontainer<?php echo $nomineecount; ?>">
		<div class="box-content" id="nominee<?php echo $nomineecount; ?>">
			<div class="card-header">
					Nominee Details
			</div>
			<form class="nominee_data" name="nominee_data" id="nominee_data<?php echo $nomineecount; ?>" autocomplete="off">
			   <div class="row wrapper">
					 <div class="col-lg-3">
						<div class="form-group">
						   <label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
						   <select class="form-control nominee_relation" name="nominee_relation">
							  <option value="">Select Nominee</option>
							  <option data-opt="Female" value="1" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 1){echo "selected";} ?>>Spouse</option>
							  <option data-opt="Male" value="2" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 2){echo "selected";} ?>>Son</option>
							  <option data-opt="Female" value="3" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 3){echo "selected";} ?>>Daughter</option>
							  <option data-opt="Female" value="4" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 4){echo "selected";} ?>>Mother</option>
							  <option data-opt="Male" value="5" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 5){echo "selected";} ?>>Father</option>
							  <option data-opt="Male" value="6" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 6){echo "selected";} ?>>Father-in-law</option>
							  <option data-opt="Female" value="7" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 7){echo "selected";} ?>>Mother-in-law</option>
							  <option data-opt="Male" value="8" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 8){echo "selected";} ?>>Brother</option>
							  <option data-opt="Female" value="9" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 9){echo "selected";} ?>>Sister</option>
							  <option data-opt="Male" value="10" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 10){echo "selected";} ?>>Grandfather</option>
							  <option data-opt="Female" value="11" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 11){echo "selected";} ?>>Grandmother</option>
							  <option data-opt="Male" value="12" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 12){echo "selected";} ?>>Grandson</option>
							  <option data-opt="Female" value="13" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 13){echo "selected";} ?>>Granddaughter</option>
							  <option data-opt="Male" value="14" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 14){echo "selected";} ?>>Son-in-law</option>
							  <option data-opt="Female" value="15" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 15){echo "selected";} ?>>Daughter-in-law</option>
							  <option data-opt="Male" value="16" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 16){echo "selected";} ?>>Brother-in-law</option>
							  <option data-opt="Female" value="17" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 17){echo "selected";} ?>>Sister-in-law</option>
							  <option data-opt="Male" value="18" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 18){echo "selected";} ?>>Nephew</option>
							  <option data-opt="Female" value="19" <?php if(!empty($leaddetails->proposal_details[0]) && $leaddetails->proposal_details[0]->nominee_relation == 19){echo "selected";} ?>>Niece</option>
						   </select>
						</div>
					 </div>
					 <div class="col-lg-3">
						<div class="form-group"> <label for="example-text-input" class="col-form-label">First Name<span style="color:#FF0000">*</span></label> <input class="form-control nominee_first_name first_name" type="text" value="<?php if(!empty($leaddetails->proposal_details[0])){echo $leaddetails->proposal_details[0]->nominee_first_name;} ?>" maxlength="50" autocomplete="off" name="nominee_first_name"> </div>
					 </div>
					 <div class="col-lg-3">
						<div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name<span style="color:#FF0000">*</span></label> <input class="form-control nominee_last_name last_name" type="text" maxlength="50" autocomplete="off" value="<?php if(!empty($leaddetails->proposal_details[0])){echo $leaddetails->proposal_details[0]->nominee_last_name;} ?>" name="nominee_last_name"> </div>
					 </div>
					 <div class="col-md-1">
						<div class="form-group">
						   <label class="col-form-label">Salutation<span style="color:#FF0000">*</span></label> 
						   <select class="form-control nominee_salutation" name="nominee_salutation" style="padding: 5px 0px; font-size: 13px !important;">
							  <option value="">Select</option>
							  <option value="Mr" class="nominee_male_gen" <?php if(!empty($leaddetails->proposal_details[0]) && ($leaddetails->proposal_details[0]->nominee_salutation == "Mr")){echo "selected";} ?>>Mr</option>
							  <option value="Mrs" class="nominee_female_gen" <?php if(!empty($leaddetails->proposal_details) && ($leaddetails->proposal_details[0]->nominee_salutation == "Mrs")){echo "selected";} ?>>Mrs</option>
							  <option value="Ms" class="nominee_female_gen" <?php if(!empty($leaddetails->proposal_details) && ($leaddetails->proposal_details[0]->nominee_salutation == "Ms")){echo "selected";} ?>>Ms</option>
						   </select>
						</div>
					 </div>
					 <div class="col-md-2">
						<div class="form-group"> <label for="example-text-input" class="col-form-label">Gender<span style="color:#FF0000">*</span></label> <input class="form-control nominee_gender" type="text" value="<?php if(!empty($leaddetails->proposal_details[0])){echo $leaddetails->proposal_details[0]->nominee_gender;} ?>" name="nominee_gender" readonly=""> </div>
					 </div>
					 <div class="col-lg-3">
						<div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth</label><span style="color:#FF0000">*</span>  <input class="form-control nominee_dob datepicker" type="text" name="nominee_dob" value="<?php if(!empty($leaddetails->proposal_details[0])){echo $leaddetails->proposal_details[0]->nominee_dob;} ?>" readonly=""> </div>
					 </div>
					 <div class="col-lg-3">
						<div class="form-group"> <label for="example-date-input" class="col-form-label">Contact No</label> <input class="form-control nominee_contact" type="text" autocomplete="off" value="<?php if(!empty($leaddetails->proposal_details[0])){echo $leaddetails->proposal_details[0]->nominee_contact;} ?>" name="nominee_contact" maxlength="10"><div><label class="moberror"></label></div> </div>
					 </div>
					 <div class="col-lg-3">
						<div class="form-group"> <label for="example-date-input" class="col-form-label">Email</label> <input class="form-control nominee_email" type="email" name="nominee_email" value="<?php if(!empty($leaddetails->proposal_details[0])){echo $leaddetails->proposal_details[0]->nominee_email;} ?>"> </div>
					 </div>
			   </div>
			   <div class="ghd_declaration">
					<div class="box-content">
						<div class="card-header">
								<?php echo $leaddetails->policy_declaration->label; ?>
						</div>
						<table class="table">
							<thead>
								<tr>
									<th>Declaration</th>
									<th>Self</th>
									<th>Spouse</th>
									<th>Son</th>
									<th>Doughter</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo $leaddetails->policy_declaration->content; ?></td>
									<td><label><input type="checkbox" name="ghd_chk_self" value="1" /> Agree </label></td>
									<td><label><input type="checkbox" name="ghd_chk_spouse" value="1" /> Agree </label></td>
									<td><label><input type="checkbox" name="ghd_chk_son" value="1" /> Agree </label></td>
									<td><label><input type="checkbox" name="ghd_chk_doughter" value="1" /> Agree </label></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="assignment_declaration">
					<div class="box-content">
						<div class="card-header">
								<?php echo $leaddetails->assignment_declaration->label; ?>
						</div>
						<table class="table">
							<thead>
								<tr>
									<th>Declaration</th>
									<th>Self</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo $leaddetails->assignment_declaration->content; ?></td>
									<td><label><input type="checkbox" name="assignment_chk" value="1" /> Agree </label></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
					  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
					  <input type="hidden" name="plan_id" value="<?php echo $leaddetails->customer_details[0]->plan_id; ?>" />
					  <input type="hidden" name="customer_id" value="<?php echo $leaddetails->customer_details[0]->customer_id; ?>" />
					  <input type="hidden" name="proposal_details_id" value="<?php echo $leaddetails->proposal_details[0]->proposal_details_id; ?>" />
					  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
					 <button type="submit" class="btn btn-success btn-lg addnomineebtn">Submit</button>
				  </div>
				</div>
			</form>
			<script>
				var vRules = {
					nominee_relation:{required:true},
					nominee_first_name:{required:true},
					nominee_last_name:{required:true},
					nominee_dob:{required:true},
					nominee_salutation:{required:true},
					nominee_gender:{required:true}
				};

				var vMessages = {
					nominee_relation:{required:"Field is required"},
					nominee_first_name:{required:"Field is required"},
					nominee_last_name:{required:"Field is required"},
					nominee_dob:{required:"Field is required"},
					nominee_salutation:{required:"Field is required"},
					nominee_gender:{required:"Field is required"}
				};

				$("#nominee_data<?php echo $nomineecount; ?>").validate({
					rules: vRules,
					messages: vMessages,
					submitHandler: function(form) 
					{
						var act = "<?php echo base_url();?>policyproposal/submitForm1";
						$("#nominee_data<?php echo $nomineecount; ?>").ajaxSubmit({
							url: act, 
							type: 'post',
							dataType: 'json',
							cache: false,
							clearForm: false, 
							beforeSubmit : function(arr, $form, options){
								var mob = $(form).find(".nominee_contact").val();
								if(mob != ''){
									var filter = /^[6789]\d{9}$/;
									if (filter.test(mob)) {
										 $(form).find(".moberror").html("");
									}
									else {
										$(form).find(".moberror").html("Please enter valid phone number");
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
									$('#nomineecontainer<?php echo $nomineecount; ?>').load(document.URL +  ' #nominee<?php echo $nomineecount; ?>');
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
			</script>
			<?php $nomineecount++; ?>
			<div class="clearfix"></div>
		</div>
		</div>
	</div>
	</div>

	<?php if($coapplicant_count > 0){for($cocount = 1;$cocount <= $coapplicant_count;$cocount++){ ?>
	<div id="coapplicantcontainer<?php echo $cocount; ?>">
	<div id="coapplicant<?php echo $cocount; ?>">
		<div class="card-header">
				Co-Applicant Details
		</div>
		<form class="coapplicant_data" data-container="coapplicantcontainer<?php echo $cocount; ?>" id="coapplicant<?php echo $cocount; ?>_data" method="post" autocomplete="off">
		   <div class="row mb-5">
			  <div class="col-md-2">
				 <div class="form-group">
					<label for="example-text-input" class="col-form-label">Salutation</label> 
					<select class="form-control salutation" name="salutation">
					   <option value="">Select salutation</option>
					   <option value="Mr" <?php if(!empty($leaddetails->customer_details[$cocount]->salutation) && ($leaddetails->customer_details[$cocount]->salutation == "Mr")){echo "selected";} ?>>Mr</option>
					   <option value="Mrs" <?php if(!empty($leaddetails->customer_details[$cocount]->salutation) && ($leaddetails->customer_details[$cocount]->salutation == "Mrs")){echo "selected";} ?>>Mrs</option>
					   <option value="Ms" <?php if(!empty($leaddetails->customer_details[$cocount]->salutation) && ($leaddetails->customer_details[$cocount]->salutation == "Ms")){echo "selected";} ?>>Ms</option>
					</select>
				 </div>
			  </div>
			  <div class="col-md-3">
				 <div class="form-group"> <label for="example-text-input" class="col-form-label">Customer First Name</label>  <input class="form-control firstname" name="firstname" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->first_name))echo $leaddetails->customer_details[$cocount]->first_name; ?>"> </div>
			  </div>
			  <div class="col-md-3">
				 <div class="form-group">  <label for="example-text-input" class="col-form-label">Customer Middle Name</label>  <input class="form-control middlename" name="middlename" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->middle_name)) echo $leaddetails->customer_details[$cocount]->middle_name; ?>">  </div>
			  </div>
			  <div class="col-md-3">
				 <div class="form-group">  <label for="example-text-input" class="col-form-label">Customer Last Name</label>  <input class="form-control lastname" name="lastname" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->last_name)) echo $leaddetails->customer_details[$cocount]->last_name; ?>">  </div>
			  </div>
			  <div class="col-md-3">
				 <div class="form-group">
					<label for="example-tel-input" class="col-form-label">Gender</label> 
					<select name="gender1" class="gender1 form-control dis-col">
					   <option value="">Select Gender</option>
					   <option value="Male" <?php if(!empty($leaddetails->customer_details[$cocount]->gender) && ($leaddetails->customer_details[$cocount]->gender == "Male")){echo "selected";} ?>>MALE</option>
					   <option value="Female" <?php if(!empty($leaddetails->customer_details[$cocount]->gender) && ($leaddetails->customer_details[$cocount]->gender == "Female")){echo "selected";} ?>>FEMALE</option>
				   </select>
				 </div>
			  </div>
			  <div class="col-md-2">
				 <div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth</label>  <input class="form-control dobdatepicker" type="text" name="dob" value="<?php if(!empty($leaddetails->customer_details[$cocount]->dob)) echo $leaddetails->customer_details[$cocount]->dob; ?>" readonly=""> </div>
			  </div>
			  <div class="col-md-3">
				 <div class="form-group"> <label for="example-tel-input" class="col-form-label">Mobile Number</label>  <input class="mob_no form-control dis-col" type="text" name="mob_no" value="<?php if(!empty($leaddetails->customer_details[$cocount]->mobile_no)) echo $leaddetails->customer_details[$cocount]->mobile_no; ?>" maxlength="10"> </div>
			  </div>
			  <div class="col-md-3">
				 <div class="form-group">  <label for="example-text-input" class="col-form-label">Mobile Number 2</label> <input class="mobile_no2 form-control" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->mobile_no2)) echo $leaddetails->customer_details[$cocount]->mobile_no2; ?>" name="mobile_no2" maxlength="10" ><div><label class="moberror"></label></div> </div>
			  </div>
			  <div class="col-md-6">
				 <div class="form-group"> <label for="example-email-input" class="col-form-label">Email Id<span style="color:#FF0000">*</span></label>  <input type="email" class="email_id form-control valid dis-col" name="email_id" value="<?php if(!empty($leaddetails->customer_details[$cocount]->email_id)) echo $leaddetails->customer_details[$cocount]->email_id; ?>" placeholder="Enter email">  </div>
			  </div>
			  <div class="col-md-6">
				 <div class="form-group"> <label for="example-text-input" class="col-form-label"> Address Line 1 <span style="color:#FF0000">*</span></label> <input class="address_line1 form-control" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->address_line1)) echo $leaddetails->customer_details[$cocount]->address_line1; ?>" name="address_line1"> </div>
			  </div>
			  <div class="col-md-6">
				 <div class="form-group"> <label for="example-text-input" class="col-form-label">Address Line 2</label> <input class="address_line2 form-control" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->address_line2)) echo $leaddetails->customer_details[$cocount]->address_line2; ?>" name="address_line2"> </div>
			  </div>
			  <div class="col-md-6">
				 <div class="form-group"> <label for="example-text-input" class="col-form-label">Address Line 3</label> <input class="address_line3 form-control" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->address_line3)) echo $leaddetails->customer_details[$cocount]->address_line3; ?>" name="address_line3"> </div>
			  </div>
			  <div class="col-md-2">
				 <div class="form-group"> <label for="example-text-input" class="col-form-label">Pin Code<span style="color:#FF0000">*</span></label> <input class="form-control pin_code" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->pincode)) echo $leaddetails->customer_details[$cocount]->pincode; ?>" name="pin_code" maxlength="6"><div><label class="error pinerror"></label></div>  </div>
			  </div>
			  <div class="col-md-3">
				 <div class="form-group">  <label for="example-text-input" class="col-form-label">City<span style="color:#FF0000">*</span></label>  <input class="form-control city dis-col" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->city)) echo $leaddetails->customer_details[$cocount]->city; ?>" name="city">  </div>
			  </div>
			  <div class="col-md-3">
				 <div class="form-group">  <label for="example-text-input" class="col-form-label">State<span style="color:#FF0000">*</span></label> <input class="form-control state dis-col" type="text" value="<?php if(!empty($leaddetails->customer_details[$cocount]->state)) echo $leaddetails->customer_details[$cocount]->state; ?>" name="state">  </div>
			  </div>
			  <div class="col-md-12">
				  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
				  <input type="hidden" name="plan_id" value="<?php echo $leaddetails->customer_details[0]->plan_id; ?>" />
				  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
				  <input type="hidden" class="customer_id" name="customer_id" value="<?php if(!empty($leaddetails->customer_details[$cocount]->customer_id)) echo $leaddetails->customer_details[$cocount]->customer_id; ?>" />
				 <button type="submit" class="btn btn-success btn-lg" id="addcoapplicant<?php echo $cocount; ?>btn">Submit</button>
			  </div>
		   </div>
		</form>
		<script>
			var vRules = {
				address_line1:{required:true,minlength:10},
				city:{required:true},
				firstname:{required:true},
				lastname:{required:true},
				dob:{required:true},
				mob_no:{required:true},
				state:{required:true},
				pin_code:{required:true,number:true,minlength:6,maxlength:6}
			};


			var vMessages = {
				address_line1:{required:"Address is required",minlength:"Address should be at least 10 character"},
				city:{required:"City is required"},
				firstname:{required:"This fiels is required"},
				lastname:{required:"This fiels is required"},
				dob:{required:"This fiels is required"},
				mob_no:{required:"This fiels is required"},
				state:{required:"State is required"},
				pin_code:{required:"Pincode is required",number:"Pincode should be numeric and 6 digit",minlength:"Pincode should be numeric and 6 digit"}
			};

			$("#coapplicant<?php echo $cocount; ?>_data").validate({
					rules: vRules,
					messages: vMessages,
					submitHandler: function(form) 
					{
						var id = $(form).attr("id");
						var container = $(form).attr("data-container");
						var act = "<?php echo base_url();?>policyproposal/coapplicantsubmitForm";
						$("#coapplicant<?php echo $cocount; ?>_data").ajaxSubmit({
							url: act, 
							type: 'post',
							dataType: 'json',
							cache: false,
							clearForm: false, 
							beforeSubmit : function(arr, $form, options){
								var mob = $(form).find(".mobile_no2").val();
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
									$('#coapplicantcontainer<?php echo $cocount; ?>').load(document.URL +  ' #coapplicant<?php echo $cocount; ?>');
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
				if(!empty($leaddetails->proposal_details[$cocount]) && !empty($leaddetails->proposal_details[$cocount]->proposal_policy_details)){
					$allmembers = array();
					$amount = array();
					$tax = array();
					$totalamount = 0;
					$preid = 0;
					$proposal_sum_insured = "";
					$proposal_child_count = "";
					$proposal_adult_count = "";
					
					foreach($leaddetails->proposal_details[$cocount]->proposal_policy_details as $policy_details){
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
			<div class="combo" id="applicantmember<?php echo $memberformcount; ?>">
				<div class="card-header">
						<?php $k = 1;
						foreach($combonames as $comboname){
							if(count($combonames) == $k){
							echo $comboname; }else{
							echo $comboname." + ";	
							}
							$k++;
						}
						?>
					<div class="pull-right totalpremium"><?php if(!empty($totalamount) && $totalamount != 0){echo $totalamount;} ?></div>
				</div>
				<form class="form-horizontal memberform" data-sub-parent="coapplicant<?php echo $cocount; ?>" data-parent="coapplicantcontainer<?php echo $cocount; ?>" id="memberform<?php echo $memberformcount;?>" method="post" enctype="multipart/form-data">
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
								 <select data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control adultcount" name="adultcount" <?php if(!empty($proposal_adult_count)){echo "disabled"; } ?>>
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
								 <select data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control childcount" name="childcount" <?php if(!empty($proposal_child_count)){echo "disabled"; } ?>>
									<option value="">Select</option>
									<?php if(!empty($proposal_child_count)){echo "<option value='$proposal_child_count' selected>$proposal_child_count</option>"; } ?>
								 </select>
							  </div>
						   </div>
						   
						   <div class="row col-md-12" style="padding:0px; margin-left:0px;">
							  <div class="col-md-3">
								 <div class="form-group">
									<label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
									<select data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control relation" name="family_members_id">
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
									<select data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control family_salutation" name="family_salutation" style="padding: 5px 0px; font-size: 13px !important;">
									   <option value="">Select</option>
									   <option value="Mr" class="male_gen">Mr</option>
									   <option value="Mrs" class="female_gen">Mrs</option>
									   <option value="Ms" class="female_gen">Ms</option>
									</select>
								 </div>
							  </div>
							  <div class="col-md-2">
								 <div class="form-group">
									<label class="col-form-label">Gender<span style="color:#FF0000">*</span></label> <input data-parent="applicantmember<?php echo $memberformcount; ?>" class="form-control family_gender dis-col" type="text" name="family_gender">
								 </div>
							  </div>
							  <div class="col-md-3">
								 <div class="form-group"> <label for="example-text-input" class="col-form-label">First Name <span style="color:#FF0000">*</span></label> <input class="form-control first_name" type="text" value="" name="first_name" autocomplete="off" maxlength="50"> </div>
							  </div>
							  <div class="col-md-3">
								 <div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name <span style="color:#FF0000">*</span></label> <input class="form-control last_name" type="text" value="" name="last_name" maxlength="50" autocomplete="off"> <span class="err_last_nameArr" class="error"></span> </div>
							  </div>
							  <div class="col-md-3">
								 <div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label> <input class="form-control dobdatepicker" data-parent="applicantmember<?php echo $memberformcount; ?>" onchange="calculateage(this.value,applicantmember<?php echo $memberformcount; ?>)" autocomplete="off" type="text" name="family_date_birth" readonly="readonly"> <span class="err_family_date_birthArr" class="error"></span> </div>
							  </div>
							  <div class="col-md-3" style="display: block">
								 <div class="form-group"> <label for="example-text-input" class="col-form-label">Age <span style="color:#FF0000">*</span></label> <input class="form-control dis-col age" data-parent="applicantmember<?php echo $memberformcount; ?>" type="text" name="age" readonly> </div>
							  </div>
							  <div class="col-md-12">
								  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
								  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
								  <input type="hidden" class="proposal_details_id" name="proposal_details_id" value="<?php if(!empty($leaddetails->proposal_details[$cocount]->proposal_details_id)) echo $leaddetails->proposal_details[$cocount]->proposal_details_id; ?>" />
								  <?php if($maxmember > count($member_details)){ ?>
								  <a href="javascript:void(0)" data-form="memberform<?php echo $memberformcount;?>" class="btn add-dep-xd memberformsubmit">Add Insured Member</a><br><br>
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
								 <tbody class="patTable" id="applicantmember<?php echo $memberformcount; ?>patTable" data-parent="applicantmember<?php echo $memberformcount; ?>" style="pointer-events: auto;">
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
				</form>
			</div>
			<?php $memberformcount++; } ?>
			<?php $i = 1; foreach($leaddetails->plan_details as $plandetails){ 
			if($plandetails->is_combo != 1){
			$membersrelation = array();
			$member_details = array();
			if(!empty($leaddetails->proposal_details[$cocount]->proposal_policy_details)){
					$allmembers = array();
					$amount = array();
					$tax = array();
					$totalamount = 0;
					$preid = 0;
					$proposal_sum_insured = "";
					$proposal_child_count = "";
					$proposal_adult_count = "";
					
					foreach($leaddetails->proposal_details[$cocount]->proposal_policy_details as $policy_details){
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
			<div class="<?php if($plandetails->is_optional == 1){echo 'optional';}else{echo 'mandate';} ?>" id="applicantmember<?php echo $memberformcount; ?>">
				<div class="card-header">
					<?php echo $plandetails->policy_sub_type_name; ?>
					<div class="pull-right totalpremium"><?php if(!empty($totalamount) && $totalamount != 0){echo $totalamount;} ?></div>
				</div>
				<form class="form-horizontal memberform" data-sub-parent="coapplicant<?php echo $cocount; ?>" data-parent="coapplicantcontainer<?php echo $cocount; ?>" id="memberform<?php echo $memberformcount;?>" method="post" enctype="multipart/form-data">
				
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
								 <select class="form-control sum_insured" data-parent="applicantmember<?php echo $memberformcount; ?>" name="sum_insured" id="sum_insured<?php echo $i; ?>" <?php if(!empty($proposal_sum_insured)){echo "disabled"; } ?>>
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
								 <select class="form-control adultcount" data-parent="applicantmember<?php echo $memberformcount; ?>" name="adultcount" <?php if(!empty($proposal_adult_count)){echo "disabled"; } ?>>
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
								 <select class="form-control childcount" data-parent="applicantmember<?php echo $memberformcount; ?>" name="childcount" <?php if(!empty($proposal_child_count)){echo "disabled"; } ?>>
									<option value="">Select</option>
									<?php if(!empty($proposal_child_count)){echo "<option value='$proposal_child_count' selected>$proposal_child_count</option>"; } ?>
								 </select>
							  </div>
						   </div>
						   
						   <div class="row col-md-12" style="padding:0px; margin-left:0px;">
							  <div class="col-md-3">
								 <div class="form-group">
									<label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
									<select class="form-control relation" data-parent="applicantmember<?php echo $memberformcount; ?>" name="family_members_id">
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
									<select class="form-control family_salutation" data-parent="applicantmember<?php echo $memberformcount; ?>" name="family_salutation" style="padding: 5px 0px; font-size: 13px !important;">
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
								 <div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name <span style="color:#FF0000">*</span></label> <input class="form-control last_name" type="text" value="" name="last_name" maxlength="50" autocomplete="off"> <span class="err_last_nameArr error"></span> </div>
							  </div>
							  <div class="col-md-3">
								 <div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label> <input class="form-control memberdob dobdatepicker" data-parent="applicantmember<?php echo $memberformcount; ?>" autocomplete="off" type="text" onchange="calculateage(this.value,applicantmember<?php echo $memberformcount; ?>)" name="family_date_birth" readonly="readonly"> <span class="err_family_date_birthArr"></span> </div>
							  </div>
							  <div class="col-md-3" style="display: block">
								 <div class="form-group"> <label for="example-text-input" class="col-form-label">Age <span style="color:#FF0000">*</span></label> <input class="form-control dis-col age" type="text" id="applicantmember<?php echo $memberformcount; ?>" name="age" readonly> </div>
							  </div>
							  <div class="col-md-12">
								  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
								  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
								  <input type="hidden" name="proposal_details_id" value="<?php if(!empty($leaddetails->proposal_details[$cocount]->proposal_details_id)) echo $leaddetails->proposal_details[$cocount]->proposal_details_id; ?>" />
								  <?php if($plandetails->max_member_count > count($member_details)){ ?>
								  <a href="javascript:void(0)" data-form="memberform<?php echo $memberformcount;?>" class="btn add-dep-xd memberformsubmit">Add Insured Member</a><br><br>
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
								 <tbody class="patTable" id="applicantmember<?php echo $memberformcount; ?>patTable" data-parent="applicantmember<?php echo $memberformcount; ?>" style="pointer-events: auto;">
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
				</form>
			</div>
			<?php $i++;$memberformcount++;}} ?>
			<div id="nomineecontainer<?php echo $nomineecount; ?>">
			<div class="box-content" id="nominee<?php echo $nomineecount; ?>">
				<div class="card-header">
						Nominee Details
				</div>
				<form class="nominee_data" name="nominee_data" id="nominee_data<?php echo $nomineecount; ?>" autocomplete="off">
				
				   <div class="row wrapper">
						 <div class="col-lg-3">
							<div class="form-group">
							   <label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label> 
							   <select class="form-control nominee_relation" name="nominee_relation">
								  <option value="">Select Nominee</option>
								  <option data-opt="Female" value="1" <?php if(!empty($leaddetails->proposal_details[$cocount]) && $leaddetails->proposal_details[$cocount]->nominee_relation == 1){echo "selected";} ?>>Spouse</option>
								  <option data-opt="Male" value="2" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 2){echo "selected";} ?>>Son</option>
								  <option data-opt="Female" value="3" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 3){echo "selected";} ?>>Daughter</option>
								  <option data-opt="Female" value="4" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 4){echo "selected";} ?>>Mother</option>
								  <option data-opt="Male" value="5" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 5){echo "selected";} ?>>Father</option>
								  <option data-opt="Male" value="6" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 6){echo "selected";} ?>>Father-in-law</option>
								  <option data-opt="Female" value="7" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 7){echo "selected";} ?>>Mother-in-law</option>
								  <option data-opt="Male" value="8" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 8){echo "selected";} ?>>Brother</option>
								  <option data-opt="Female" value="9" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 9){echo "selected";} ?>>Sister</option>
								  <option data-opt="Male" value="10" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 10){echo "selected";} ?>>Grandfather</option>
								  <option data-opt="Female" value="11" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 11){echo "selected";} ?>>Grandmother</option>
								  <option data-opt="Male" value="12" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 12){echo "selected";} ?>>Grandson</option>
								  <option data-opt="Female" value="13" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 13){echo "selected";} ?>>Granddaughter</option>
								  <option data-opt="Male" value="14" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 14){echo "selected";} ?>>Son-in-law</option>
								  <option data-opt="Female" value="15" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 15){echo "selected";} ?>>Daughter-in-law</option>
								  <option data-opt="Male" value="16" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 16){echo "selected";} ?>>Brother-in-law</option>
								  <option data-opt="Female" value="17" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 17){echo "selected";} ?>>Sister-in-law</option>
								  <option data-opt="Male" value="18" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 18){echo "selected";} ?>>Nephew</option>
								  <option data-opt="Female" value="19" <?php if(!empty($leaddetails->proposal_details) && $leaddetails->proposal_details[$cocount]->nominee_relation == 19){echo "selected";} ?>>Niece</option>
							   </select>
							</div>
						 </div>
						 <div class="col-lg-3">
							<div class="form-group"> <label for="example-text-input" class="col-form-label">First Name<span style="color:#FF0000">*</span></label> <input class="form-control nominee_first_name first_name" type="text" value="<?php if(!empty($leaddetails->proposal_details[$cocount])){echo $leaddetails->proposal_details[$cocount]->nominee_first_name;} ?>" maxlength="50" autocomplete="off" name="nominee_first_name"> </div>
						 </div>
						 <div class="col-lg-3">
							<div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name<span style="color:#FF0000">*</span></label> <input class="form-control nominee_last_name last_name" type="text" maxlength="50" autocomplete="off" value="<?php if(!empty($leaddetails->proposal_details[$cocount])){echo $leaddetails->proposal_details[$cocount]->nominee_last_name;} ?>" name="nominee_last_name"> </div>
						 </div>
						 <div class="col-md-1">
							<div class="form-group">
							   <label class="col-form-label">Salutation<span style="color:#FF0000">*</span></label> 
							   <select class="form-control nominee_salutation" name="nominee_salutation" style="padding: 5px 0px; font-size: 13px !important;">
								  <option value="">Select</option>
								  <option value="Mr" class="nominee_male_gen" <?php if(!empty($leaddetails->proposal_details[$cocount]) && ($leaddetails->proposal_details[$cocount]->nominee_salutation == "Mr")){echo "selected";} ?>>Mr</option>
								  <option value="Mrs" class="nominee_female_gen" <?php if(!empty($leaddetails->proposal_details[$cocount]) && ($leaddetails->proposal_details[$cocount]->nominee_salutation == "Mrs")){echo "selected";} ?>>Mrs</option>
								  <option value="Ms" class="nominee_female_gen" <?php if(!empty($leaddetails->proposal_details[$cocount]) && ($leaddetails->proposal_details[$cocount]->nominee_salutation == "Ms")){echo "selected";} ?>>Ms</option>
							   </select>
							</div>
						 </div>
						 <div class="col-md-2">
							<div class="form-group"> <label for="example-text-input" class="col-form-label">Gender<span style="color:#FF0000">*</span></label> <input class="form-control nominee_gender" type="text" value="<?php if(!empty($leaddetails->proposal_details[$cocount])){echo $leaddetails->proposal_details[$cocount]->nominee_gender;} ?>" name="nominee_gender" readonly=""> </div>
						 </div>
						 <div class="col-lg-3">
							<div class="form-group"> <label for="example-date-input" class="col-form-label">Date of Birth</label><span style="color:#FF0000">*</span>  <input class="form-control nominee_dob datepicker" type="text" name="nominee_dob" value="<?php if(!empty($leaddetails->proposal_details[$cocount])){echo $leaddetails->proposal_details[$cocount]->nominee_dob;} ?>" readonly=""> </div>
						 </div>
						 <div class="col-lg-3">
							<div class="form-group"> <label for="example-date-input" class="col-form-label">Contact No</label> <input class="form-control nominee_contact" type="text" autocomplete="off" value="<?php if(!empty($leaddetails->proposal_details[$cocount])){echo $leaddetails->proposal_details[$cocount]->nominee_contact;} ?>" name="nominee_contact" maxlength="10"><div><label class="moberror"></label></div> </div>
						 </div>
						 <div class="col-lg-3">
							<div class="form-group"> <label for="example-date-input" class="col-form-label">Email</label> <input class="form-control nominee_email" type="email" name="nominee_email" value="<?php if(!empty($leaddetails->proposal_details[$cocount])){echo $leaddetails->proposal_details[$cocount]->nominee_email;} ?>"> </div>
						 </div>
				   </div>
				   <div class="ghd_declaration">
						<div class="box-content">
							<div class="card-header">
									<?php echo $leaddetails->policy_declaration->label; ?>
							</div>
							<table class="table">
								<thead>
									<tr>
										<th>Declaration</th>
										<th>Self</th>
										<th>Spouse</th>
										<th>Son</th>
										<th>Doughter</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?php echo $leaddetails->policy_declaration->content; ?></td>
										<td><label><input type="checkbox" name="ghd_chk_self" value="1" /> Agree </label></td>
										<td><label><input type="checkbox" name="ghd_chk_spouse" value="1" /> Agree </label></td>
										<td><label><input type="checkbox" name="ghd_chk_son" value="1" /> Agree </label></td>
										<td><label><input type="checkbox" name="ghd_chk_doughter" value="1" /> Agree </label></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="assignment_declaration">
						<div class="box-content">
							<div class="card-header">
									<?php echo $leaddetails->assignment_declaration->label; ?>
							</div>
							<table class="table">
								<thead>
									<tr>
										<th>Declaration</th>
										<th>Self</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?php echo $leaddetails->assignment_declaration->content; ?></td>
										<td><label><input type="checkbox" name="assignment_chk" value="1" /> Agree </label></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							  <input type="hidden" name="lead_id" value="<?php echo $leaddetails->customer_details[0]->lead_id; ?>" />
							  <input type="hidden" name="plan_id" value="<?php echo $leaddetails->customer_details[0]->plan_id; ?>" />
							  <input type="hidden" name="customer_id" value="<?php if(!empty($leaddetails->customer_details[$cocount]->customer_id)) echo $leaddetails->customer_details[$cocount]->customer_id; ?>" />
							  <input type="hidden" name="trace_id" value="<?php echo $leaddetails->customer_details[0]->trace_id; ?>" />
							 <button type="submit" class="btn btn-success btn-lg addnomineebtn" id="addnomineebtn<?php echo $cocount; ?>">Submit</button>
						</div>
					</div>
				</form>
				
				<script>
				var vRules = {
					nominee_relation:{required:true},
					nominee_first_name:{required:true},
					nominee_last_name:{required:true},
					nominee_dob:{required:true},
					nominee_salutation:{required:true},
					nominee_gender:{required:true}
				};

				var vMessages = {
					nominee_relation:{required:"Field is required"},
					nominee_first_name:{required:"Field is required"},
					nominee_last_name:{required:"Field is required"},
					nominee_dob:{required:"Field is required"},
					nominee_salutation:{required:"Field is required"},
					nominee_gender:{required:"Field is required"}
				};

				$("#nominee_data<?php echo $nomineecount; ?>").validate({
					rules: vRules,
					messages: vMessages,
					submitHandler: function(form) 
					{
						var act = "<?php echo base_url();?>policyproposal/submitForm1";
						$("#nominee_data<?php echo $nomineecount; ?>").ajaxSubmit({
							url: act, 
							type: 'post',
							dataType: 'json',
							cache: false,
							clearForm: false, 
							beforeSubmit : function(arr, $form, options){
								var mob = $(form).find(".nominee_contact").val();
								if(mob != ''){
									var filter = /^[6789]\d{9}$/;
									if (filter.test(mob)) {
										 $(form).find(".moberror").html("");
									}
									else {
										$(form).find(".moberror").html("Please enter valid phone number");
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
									$('#nomineecontainer<?php echo $nomineecount; ?>').load(document.URL +  ' #nominee<?php echo $nomineecount; ?>');
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
			</script>
			<?php $nomineecount++; ?>
			<div class="clearfix"></div>
		</div>
		</div>
	</div>
	</div>
	<?php }} ?>
			
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


		
