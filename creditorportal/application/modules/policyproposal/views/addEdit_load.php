<?php

//$insured_form_count = 0;
//$proposal_member_id = json_decode(json_encode($proposal_member_id), true);

function return_editable($element, $is_only_previewable){

    $no_collapsable = 'no-collapsable';

    if (!isset($is_only_previewable) || $is_only_previewable == '') {

        if(isset($element) && !empty($element)){

            return '';
        }
        else{

            return $no_collapsable;
        }
    }
    else{

        return '';
    }
}

$no_collapsable = 'no-collapsable';

$mandatory_if_not_selected = [];

if(isset($leaddetails->mandatory_if_not_selected)){

    $mandatory_if_not_selected = json_decode(json_encode($leaddetails->mandatory_if_not_selected), true);
}

$policy_sub_type_id_map = [];

if(isset($leaddetails->policy_sub_type_id_map)){

    $policy_sub_type_id_map = json_decode(json_encode($leaddetails->policy_sub_type_id_map), true);
}

$is_one_adult_policy = false;

if(count($family_constructs) == 1){

    foreach($family_constructs as $key => $value){

        if($key == '1-0'){

            $is_one_adult_policy = true;
        }
    }
}

?>
<input type="hidden" id="self_age_<?php echo $coapplicant_tab_id ?? '' ?>" value="<?=$self_age ?? '0';?>">
<div class="tab-pane fade show active" id="app1" role="tabpanel" aria-labelledby="app1-tab">
    <p>
    <div id="accordion1" class="according accordion-s2 mt-3">
        <div class="card card-member">
            <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion450<?php echo $coapplicant_tab_id ?? '' ?>" aria-expanded="false"> <span class="lbl-card">Customer Details - <i class="ti-file"></i></a>
            </div>
            <div id="accordion450<?php echo $coapplicant_tab_id ?? '' ?>" class="collapse card-vis-mar accord-data show" data-parent="#accordion1" style="">
                <!-- form start -->
                <form id="cust_data<?php echo $coapplicant_tab_id ?? '' ?>" class="customer_form" method="post" autocomplete="off">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Salutation<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <select class="form-control" name="salutation" id="salutation">
                                        <option value="">Select salutation</option>
                                        <option data-gender="Male" value="Mr" <?php if ($customer->salutation == "Mr") {
                                                                                    echo "selected";
                                                                                } ?>>Mr</option>
                                        <option data-gender="Female" value="Mrs" <?php if ($customer->salutation == "Mrs") {
                                                                                        echo "selected";
                                                                                    } ?>>Mrs</option>
                                        <option data-gender="Female" value="Ms" <?php if ($customer->salutation == "Ms") {
                                                                                    echo "selected";
                                                                                } ?>>Ms</option>
                                        <option data-gender="" value="Dr" <?php if ($customer->salutation == "Dr") {
                                                                                    echo "selected";
                                                                                } ?>>Dr</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Customer First Name<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control" id="firstname" name="firstname" type="text" value="<?php echo $customer->first_name; ?>">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Customer Middle Name</label>
                                <div class="input-group">
                                    <input class="form-control" id="middlename" name="middlename" type="text" value="<?php echo $customer->middle_name; ?>">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Customer Last Name<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control" id="lastname" name="lastname" type="text" value="<?php echo $customer->last_name; ?>">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Gender<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control" id="gender_hidden" name="gender1" type="hidden" value="<?php echo $customer->gender; ?>">
                                    <select id="gender1" name="gender1" class="form-control" disabled>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php if ($customer->gender == "Male") {
                                                                    echo "selected";
                                                                } ?>>MALE</option>
                                        <option value="Female" <?php if ($customer->gender == "Female") {
                                                                    echo "selected";
                                                                } ?>>FEMALE</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                    <div><label class="moberror_gender error"></label></div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Date of Birth<span class="lbl-star">*</span></label>
                                <div class="input-group">

                                    <input class="form-control predatepicker" type="text" name="dob" id="dob1<?php echo $coapplicant_tab_id ?? '' ?>" value="<?php if (isset($customer->dob)) {
                                                                                                                                                                                                echo date('d-m-Y', strtotime($customer->dob));
                                                                                                                                                                                            } ?>" autocomplete="off">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Mobile No<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control" type="text" id="mob_no" name="mob_no" value="<?php echo $customer->customer_mobile_no; ?>" maxlength="10" <?php if ($customer->customer_mobile_no) : ?> readonly="" <?php endif ?>>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">phone_android</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Mobile No2</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" value="<?php echo (!empty($customer->customer_mobile_no2)) ? $customer->customer_mobile_no2 : ''; ?>" id="mobile_no2" name="mobile_no2" maxlength="10">
                                    <div><label class="moberror_customer error"></label></div>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">phone_android</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Email Id<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email_id" name="email_id" value="<?php echo $customer->email_id; ?>" placeholder="Enter email" <?php if ($customer->email_id) : ?> readonly="" <?php endif ?>>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">alternate_email</span></span>
                                    </div>
                                </div>
                            </div>
                            <!-- remove pancard  
                                 <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">PAN Card</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_box</span></span>
                                        </div>
                                    </div>
                                </div>
								-->

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Address Line 1<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <textarea type="text" id="address_line1" name="address_line1" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo (!empty($customer->address_line1)) ? $customer->address_line1 : ''; ?></textarea>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Address Line 2</label>
                                <div class="input-group">
                                    <textarea type="text" id="address_line2" name="address_line2" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo (!empty($customer->address_line2)) ? $customer->address_line2 : ''; ?> </textarea>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Address Line 3</label>
                                <div class="input-group">
                                    <textarea type="text" id="address_line3" name="address_line3" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo (!empty($customer->address_line3)) ? $customer->address_line3 : ''; ?></textarea>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Pincode<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control valid" type="text" value="<?php echo (!empty($customer->pincode)) ? $customer->pincode : ''; ?>" name="pin_code" id="pin_code" maxlength="6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">City<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control valid dis-col" type="text" value="<?php echo (!empty($customer->city)) ? $customer->city : ''; ?>" name="city" id="city" readonly>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_city</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">State<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control valid dis-col" type="text" value="<?php echo (!empty($customer->state)) ? $customer->state : ''; ?>" name="state" id="state" readonly>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_city</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4 form-buttons">
                            <input type="hidden" name="lead_id" value="<?php echo $customer->lead_id; ?>" />
                            <input type="hidden" name="trace_id" value="<?php echo $customer->trace_id; ?>" />
                            <input type="hidden" class="customer_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="customer_id" value="<?php echo $customer->customer_id; ?>" />
                            <input type="hidden" name="plan_id" value="<?php echo $customer->plan_id; ?>" />
                            <div class="col-md-1 col-6 text-left">
                                <button class="btn smt-btn">Save</button>
                            </div>
                            <!-- <div class="col-md-2 col-6 text-right">
                                <button class="btn cnl-btn">Cancel</button>
                            </div> -->
                        </div>
                    </div>
                </form>
                <!-- end form -->
            </div>
        </div>
    </div>

    <div id="accordion2" class="according accordion-s2 mt-3">
        <div class="card card-member">
            <div class="card-header card-vif">
                <?php 
                    $no_collapsable = 'no-collapsable';
                    if(isset($generated_quote->family_construct)){

                        $no_collapsable = return_editable($generated_quote->family_construct, $is_only_previewable);
                    }
                ?>
                <a class="card-link collapsed card-vis <?=$no_collapsable;?>" data-toggle="collapse" href="#accordion460<?php echo $coapplicant_tab_id ?? '' ?>" aria-expanded="false"> <span class="lbl-card">Generate Quote - <i class="ti-files"></i></a>
            </div>
            <div id="accordion460<?php echo $coapplicant_tab_id ?? '' ?>" class="collapse card-vis-mar generate-quote-accordian accord-data" data-parent="#accordion2" style="">
                <form id="leadform<?php echo $coapplicant_tab_id ?? '' ?>" name="leadform" method="post" enctype="multipart/form-data" novalidate>
                    <div class="card-body">
                        <div class="row">

                            <?php if (!isset($coapplicant_tab_id)) : ?>
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">LAN ID</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?php echo $customer->lan_id; ?>" name="lan_id" id="lan_id" disabled>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Loan Amount (&#x20b9;)</label>
                                    <div class="input-group">
                                        <input class="form-control" placeholder="Enter ..." name="loan_amt" id="loan_amt" type="text" value="<?php echo $customer->loan_amt; ?>" aria-describedby="inputGroupPrepend" disabled />

                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif;  ?>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Family Construct <span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <select class="form-control quote_generation_fields" name="family_members_ac_count" id="family_members_ac_count">
                                        <option value="">Select Family Construct</option>

                                        <?php foreach ($family_constructs as $value => $display_text) : ?>
                                            <?php $selected = $value == $generated_quote->family_construct ? "selected" : ""; ?>

                                            <option value="<?php echo $value ?>" <?php echo $selected ?>><?php echo $display_text ?></option>
                                        <?php endforeach;

                                        ?>

                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">family_restroom</span></span>
                                    </div>
                                </div>
                            </div>

                            <?php foreach ($leaddetails->plan_details as $policy) : ?>

                                <?php /* if ($policy->basis_id == 3 || $policy->basis_id == 6 || $policy->basis_id == 5) : ?>
                                    <div class="col-md-4 mb-3 spouse_age_input_box" <?php if (empty($generated_quote->spouse_age)) : ?>style="display: none;" <?php endif; ?>>
                                        <label for="validationCustomUsername" class="col-form-label">Spouse Age</label>
                                        <div class="input-group">
                                            <input type="number" min="<?php echo $validations['minSpouseAge'] ?>" max="<?php echo $validations['maxSpouseAge'] ?>" class="form-control quote_generation_fields" name="spouse_age" id="spouse_age" value="<?php echo $generated_quote->spouse_age ?? "" ?>">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">person</span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php break; ?>
                                <?php endif;  */ ?>

                                <?php if ($is_spouse_age_required && !$is_one_adult_policy) : ?>
                                    <div class="col-md-4 mb-3 spouse_age_input_box" <?php if (empty($generated_quote->spouse_dob) ||$generated_quote->spouse_dob == '0000-00-00') : ?>style="display: none;" <?php endif; ?>>
                                        <label for="validationCustomUsername" class="col-form-label">Spouse DOB <span class="lbl-star">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control quote_generation_fields spouse_dob" name="spouse_dob" value="<?php if (!empty($generated_quote->spouse_dob) && $generated_quote->spouse_dob != '0000-00-00') {
                                                                                                                                                    echo date('d-m-Y', strtotime($generated_quote->spouse_dob));
                                                                                                                                                } ?>" autocomplete="off">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">person</span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php break; ?>
                                <?php endif; ?>

                            <?php endforeach; ?>

                            <?php foreach ($leaddetails->master_policy_details as $key => $value) { ?>
                                <?php if ($value->policy_sub_type_id == 1) { ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomUsername" class="col-form-label">GHI Cover

                                            <?php if (!$value->is_optional) : ?><span class="lbl-star">*</span><?php endif; ?>
                                        </label>
                                        <div class="input-group">

                                            <?php if (!empty($leaddetails->sum_insured_type_1) && $value->basis_id != 5) { ?>
                                                <select class="form-control quote_generation_fields" name="sum_insured1" id="sum_insured1">
                                                    <option value="">Select GHI Cover</option>
                                                    <?php foreach ($leaddetails->sum_insured_type_1 as $key1 => $value1) { ?>
                                                        <?php $selected = $value1->sum_insured == $generated_quote->ghi_cover ? "selected" : ""; ?>
                                                        <option value="<?php echo $value1->sum_insured; ?>" <?php echo $selected ?>><?php echo $value1->sum_insured; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else { ?>
                                                <input class="form-control" name="sum_insured1" id="sum_insured1" type="text" value="" aria-describedby="inputGroupPrepend" />
                                            <?php } ?>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
                                            </div>
                                        </div>
                                        <?php
                                        
                                        if(isset($mandatory_if_not_selected[$value->policy_id][0])){

                                            $dependent_on_policy_id = $mandatory_if_not_selected[$value->policy_id][0]['dependent_on_policy_id'];

                                            if(isset($policy_sub_type_id_map[$dependent_on_policy_id])){
                                                ?>
                                                <span class="text-danger">
                                                    Mandatory if no <?=$policy_sub_type_id_map[$dependent_on_policy_id];?> Cover selected
                                                </span>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($value->policy_sub_type_id == 5) { ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomUsername" class="col-form-label">Super Top-Up Cover
                                            <?php if (!$value->is_optional) : ?><span class="lbl-star">*</span><?php endif; ?>
                                        </label>
                                        <div class="input-group">
                                            <?php if (!empty($leaddetails->sum_insured_type_5_1) && $value->basis_id != 5) { ?>
                                                <select class="form-control quote_generation_fields" name="sum_insured5_1" id="sum_insured5_1">
                                                    <option value="">Select Super Top-Up Cover</option>
                                                    <?php foreach ($leaddetails->sum_insured_type_5_1 as $key1 => $value5_1) { ?>
                                                        <?php $selected = $value5_1->sum_insured == $generated_quote->super_top_up_cover ? "selected" : ""; ?>
                                                        <option value="<?php echo $value5_1->sum_insured; ?>" <?php echo $selected ?>><?php echo $value5_1->sum_insured; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else { ?>
                                                <input class="form-control quote_generation_fields" name="sum_insured5_1" id="sum_insured5_1" type="text" value="<?php echo $generated_quote->super_top_up_cover; ?>" aria-describedby="inputGroupPrepend" />
                                            <?php } ?>

                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
                                            </div>
                                        </div>
                                        <?php
                                        
                                            if(isset($mandatory_if_not_selected[$value->policy_id][0])){

                                                $dependent_on_policy_id = $mandatory_if_not_selected[$value->policy_id][0]['dependent_on_policy_id'];

                                                if(isset($policy_sub_type_id_map[$dependent_on_policy_id])){
                                                    ?>
                                                    <span class="text-danger">
                                                        Mandatory if no <?=$policy_sub_type_id_map[$dependent_on_policy_id];?> Cover selected
                                                    </span>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </div>


                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomUsername" class="col-form-label">Super Topup Deductible </label>
                                        <div class="input-group">

                                            <?php if (!empty($leaddetails->sum_insured_type_5_2) && $value->basis_id != 5) { ?>
                                                <select class="form-control quote_generation_fields" name="deductable" id="deductable">
                                                    <?php foreach ($leaddetails->sum_insured_type_5_2 as $key1 => $value5_2) { ?>
                                                        <?php $selected = $value5_2->deductable == $generated_quote->deductable ? "selected" : ""; ?>
                                                        <option value="<?php echo $value5_2->deductable; ?>" <?php echo $selected ?>><?php echo $value5_2->deductable; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else { ?>
                                                <input class="form-control quote_generation_fields" name="sum_insured5_2" id="sum_insured5_2" type="text" value="" aria-describedby="inputGroupPrepend" />
                                            <?php } ?>

                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($value->policy_sub_type_id == 2) { ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomUsername" class="col-form-label">PA Cover
                                            <?php if (!$value->is_optional) : ?><span class="lbl-star">*</span><?php endif; ?></label>

                                        </label>
                                        <div class="input-group">
                                            <?php if (!empty($leaddetails->sum_insured_type_2) && $value->basis_id != 5) { ?>
                                                <select class="form-control quote_generation_fields" name="sum_insured2" id="sum_insured2">
                                                    <option value="">Select PA Cover</option>
                                                    <?php foreach ($leaddetails->sum_insured_type_2 as $key1 => $value2) { ?>
                                                        <?php $selected = $generated_quote->pa_cover == $value2->sum_insured ? 'selected' : "" ?>
                                                        <option value="<?php echo $value2->sum_insured; ?> " <?php echo $selected ?>><?php echo $value2->sum_insured; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else { ?>
                                                <input class="form-control quote_generation_fields" name="sum_insured2" id="sum_insured2" max="5000000" type="text" pattern="\d*" maxlength="9" value="<?php echo $generated_quote->pa_cover ?? "" ?>" aria-describedby="inputGroupPrepend" />
                                            <?php } ?>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">text_snippet</span></span>
                                            </div>
                                        </div>
                                        <?php
                                        /*$product_type_id = $leaddetails->plan_details[0]->product_type_id;

                                        if ($product_type_id == 1 || $product_type_id == 3) :
                                        ?>
                                            <span class="text-danger">
                                                Mandatory if no GCI Cover selected
                                            </span>
                                        <?php endif;*/ ?>
                                        <?php
                                        
                                        if(isset($mandatory_if_not_selected[$value->policy_id][0])){

                                            $dependent_on_policy_id = $mandatory_if_not_selected[$value->policy_id][0]['dependent_on_policy_id'];

                                            if(isset($policy_sub_type_id_map[$dependent_on_policy_id])){
                                                ?>
                                                <span class="text-danger">
                                                    Mandatory if no <?=$policy_sub_type_id_map[$dependent_on_policy_id];?> Cover selected
                                                </span>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php } ?>

                                <?php if ($value->policy_sub_type_id == 3) { ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomUsername" class="col-form-label">CI Cover
                                            <?php if (!$value->is_optional) : ?><span class="lbl-star">*</span><?php endif; ?></label>

                                        <div class="input-group">

                                            <?php if (!empty($leaddetails->sum_insured_type_3) && $value->basis_id != 5) { ?>
                                                <select class="form-control quote_generation_fields" name="sum_insured3" id="sum_insured3">
                                                    <option value="">Select CI Cover</option>
                                                    <?php foreach ($leaddetails->sum_insured_type_3 as $key1 => $value2) { ?>
                                                        <?php $selected = $generated_quote->ci_cover == $value2->sum_insured ? 'selected' : "" ?>
                                                        <option value="<?php echo $value2->sum_insured; ?>" <?php echo $selected ?>><?php echo $value2->sum_insured; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else { ?>
                                                <input class="form-control quote_generation_fields" name="sum_insured3" id="sum_insured3" type="text" type="text" pattern="\d*" maxlength="9" value="<?php echo $generated_quote->ci_cover ?? "" ?>" aria-describedby="inputGroupPrepend" />
                                            <?php } ?>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">text_snippet</span></span>
                                            </div>
                                        </div>
                                        <?php
                                        /*$product_type_id = $leaddetails->plan_details[0]->product_type_id;

                                        if ($product_type_id == 1 || $product_type_id == 3) :
                                        ?>
                                            <span class="text-danger">
                                                Mandatory if no GPA Cover selected
                                            </span>
                                        <?php endif;*/ ?>
                                        <?php
                                        
                                        if(isset($mandatory_if_not_selected[$value->policy_id][0])){

                                            $dependent_on_policy_id = $mandatory_if_not_selected[$value->policy_id][0]['dependent_on_policy_id'];

                                            if(isset($policy_sub_type_id_map[$dependent_on_policy_id])){
                                                ?>
                                                <span class="text-danger">
                                                    Mandatory if no <?=$policy_sub_type_id_map[$dependent_on_policy_id];?> Cover selected
                                                </span>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php if (!empty($leaddetails->numbers_of_ci) && $leaddetails->numbers_of_ci[0]->numbers_of_ci != 0) { ?>
                                        <div class="col-md-4 mb-3">
                                            <label for="validationCustomUsername" class="col-form-label">No. of CI</label>
                                            <div class="input-group">

                                                <select class="form-control quote_generation_fields" name="numbers_of_ci" id="numbers_of_ci">
                                                    <?php foreach ($leaddetails->numbers_of_ci as $key3 => $value3) { ?>
                                                        <?php $selected = $generated_quote->number_of_ci == $value3->numbers_of_ci ? 'selected' : '' ?>
                                                        <option value="<?php echo $value3->numbers_of_ci; ?>" <?php echo $selected ?>><?php echo $value3->numbers_of_ci; ?></option>
                                                    <?php } ?>
                                                </select>



                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">text_snippet</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                                <!--
                                 <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Date of Birth</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">calendar_today</span></span>
                                        </div>
                                    </div>
                                </div> 
								-->
                                <?php if ($value->policy_sub_type_id == 6) {
                                  //  print_r($generated_quote);
                                    ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomUsername" class="col-form-label">Hospi Cash
                                            <?php if (!$value->is_optional) : ?><span class="lbl-star">*</span><?php endif; ?></label>
                                        <div class="input-group">
                                            <?php if (!empty($leaddetails->sum_insured_type_6) && $value->basis_id != 5) { ?>
                                                <select class="form-control quote_generation_fields" name="sum_insured6" id="sum_insured6">
                                                    <option value="">Select Hospi Cash</option>

                                                    <?php foreach ($leaddetails->sum_insured_type_6 as $key6 => $value6) { ?>
                                                        <?php $selected = $value6->sum_insured == $generated_quote->hospi_cash ? "selected" : ""; ?>
                                                        <option value="<?php echo $value6->sum_insured; ?>" <?php echo $selected ?>><?php echo $value6->sum_insured; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else { ?>
                                                <input class="form-control quote_generation_fields" name="sum_insured6" id="sum_insured6" type="text" value="<?php echo $generated_quote->hospi_cash ?? "" ?>" aria-describedby="inputGroupPrepend" />
                                            <?php } ?>

                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">credit_card</span></span>
                                            </div>
                                        </div>
                                        <?php
                                        
                                            if(isset($mandatory_if_not_selected[$value->policy_id][0])){

                                                $dependent_on_policy_id = $mandatory_if_not_selected[$value->policy_id][0]['dependent_on_policy_id'];

                                                if(isset($policy_sub_type_id_map[$dependent_on_policy_id])){
                                                    ?>
                                                    <span class="text-danger">
                                                        Mandatory if no <?=$policy_sub_type_id_map[$dependent_on_policy_id];?> Cover selected
                                                    </span>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>

                            <?php if (!empty($options['tenure'])) : ?>
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Tenure</label>
                                    <div class="input-group">
                                        <select class="form-control quote_generation_fields" name="tenure" id="tenure">
                                            <?php foreach ($options['tenure'] as $tenure) : ?>
                                                <?php $selected = $tenure->tenure == $generated_quote->tenure ? 'selected' : '' ?>
                                                <option value="<?php echo $tenure->tenure ?>" <?php echo $selected ?>><?php echo $tenure->tenure ?> Years</option>
                                            <?php endforeach; ?>

                                        </select>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
                                        </div>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Tenure (In Year)</label>
                                    <div class="input-group">
                                        <input type="text" name="tenure" class="form-control" id="tenure" readonly value="1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row mt-4 form-buttons">
                            <input type="hidden" name="lead_id" value="<?php echo $customer->lead_id; ?>" />
                            <input type="hidden" name="trace_id" value="<?php echo $customer->trace_id; ?>" />
                            <input type="hidden" class="customer_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="customer_id" value="<?php echo $customer->customer_id; ?>" />
                            <input type="hidden" name="plan_id" value="<?php echo $customer->plan_id; ?>" />
                            <input type="hidden" class="proposal_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="proposal_id" value="<?php echo $current_proposal_details->proposal_details_id ?? ''; ?>" />
                            <input type="hidden" id="master_quote_id<?php echo $coapplicant_tab_id ?? '' ?>" name="master_quote_id<?php echo $coapplicant_tab_id ?? '' ?>" value="" />
                            <div class="col-md-1 col-6 text-left">
                                <button type="submit" class="btn smt-btn lead-submit-btn">Save</button>
                            </div>
                            <!-- <div class="col-md-2 col-6 text-right">
                                <button class="btn cnl-btn">Cancel</button>
                            </div> -->
                        </div>
                    </div>
                    <div class="modal fade bd-example-modal-sm get_insured_member_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-body">
                                    Is Applicant Purchasing Policy for Self?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success btn-sm" onclick="onInsuredMemberCheckAccept(this)">Yes</button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="onInsuredMemberCheckReject(this)">No</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div id="accordion3" class="according accordion-s2 mt-3">
        <div class="card card-member">
            <div class="card-header card-vif">
                <?php 
                    
                    if(isset($applicant_member_details->member_details)){

                        $no_collapsable = return_editable($applicant_member_details->member_details, $is_only_previewable);
                    }
                    if(isset($applicant_member_details->member_details)){

                        $no_collapsable = return_editable($applicant_member_details->member_details, $is_only_previewable);
                    }
                ?>
                <a class="card-link collapsed card-vis <?=$no_collapsable;?>" data-toggle="collapse" href="#accordion480<?php echo $coapplicant_tab_id ?? '' ?>" aria-expanded="false"> <span class="lbl-card"> Insured Member - <i class="ti-user"></i></a>
            </div>
            <div id="accordion480<?php echo $coapplicant_tab_id ?? '' ?>" class="collapse card-vis-mar accord-data" data-parent="#accordion3" style="">
                <!-- form start Insured Member -->
                <?php

                if (isset($co_applicant_member_details->member_details) && !empty($co_applicant_member_details->member_details)) {

                    if (isset($generated_quote->family_construct) && $generated_quote->family_construct != '') {

                        $co_applicant_member_details = json_decode(json_encode($co_applicant_member_details), true);
                        $co_applicant_member_details = array_values($co_applicant_member_details['member_details'][$customer->customer_id]);

                        $adult_child_count_arr = explode('-', $generated_quote->family_construct);
                        $member_count = ['adult_count' => $adult_child_count_arr[0], 'child_count' => $adult_child_count_arr[1]];

                        $insured_member_form_data = [
                            'coapplicant_tab_id' => $coapplicant_tab_id ?? '',
                            'member_count' => $member_count,
                            'current_customer_id' => $customer->customer_id,
                            'proposal_details_id' => $current_proposal_details->proposal_details_id,
                            'member_details' => $co_applicant_member_details,
                            'plan_id' => $customer->plan_id,
                            'lead_id' => $customer->lead_id,
                            'criterias' => $insured_member_criterias,
                            'family_constuct_relation_map' => $family_constuct_relation_map
                        ];

                        $this->load->view('insuredMemberSectionFilter', $insured_member_form_data);
                    } else {

                        $insured_member_form_data = [
                            'coapplicant_tab_id' => $coapplicant_tab_id ?? '',
                            'current_customer_id' => $customer->customer_id,
                            'proposal_details_id' => $current_proposal_details->proposal_details_id,
                            'member_details' => $co_applicant_member_details,
                            'plan_id' => $customer->plan_id,
                            'lead_id' => $customer->lead_id,
                            'criterias' => $insured_member_criterias,
                            'family_constuct_relation_map' => $family_constuct_relation_map
                        ];

                        $this->load->view('insuredMemberSectionEdit', $insured_member_form_data);
                    }
                } else if (isset($applicant_member_details->member_details) && !empty($applicant_member_details->member_details)) {

                    if (isset($generated_quote->family_construct) && $generated_quote->family_construct != '') {

                        $applicant_member_details = json_decode(json_encode($applicant_member_details), true);
                        $applicant_member_details = array_values($applicant_member_details['member_details'][$customer->customer_id]);

                        $adult_child_count_arr = explode('-', $generated_quote->family_construct);
                        $member_count = ['adult_count' => $adult_child_count_arr[0], 'child_count' => $adult_child_count_arr[1]];

                        $insured_member_form_data = [
                            'coapplicant_tab_id' => $coapplicant_tab_id ?? '',
                            'member_count' => $member_count,
                            'current_customer_id' => $customer->customer_id,
                            'proposal_details_id' => $current_proposal_details->proposal_details_id,
                            'member_details' => $applicant_member_details,
                            'plan_id' => $customer->plan_id,
                            'lead_id' => $customer->lead_id,
                            'criterias' => $insured_member_criterias,
                            'family_constuct_relation_map' => $family_constuct_relation_map
                        ];

                        $this->load->view('insuredMemberSectionFilter', $insured_member_form_data);
                    } else {

                        $insured_member_form_data = [
                            'coapplicant_tab_id' => $coapplicant_tab_id ?? '',
                            'current_customer_id' => $customer->customer_id,
                            'proposal_details_id' => $current_proposal_details->proposal_details_id,
                            'member_details' => $applicant_member_details,
                            'plan_id' => $customer->plan_id,
                            'lead_id' => $customer->lead_id,
                            'criterias' => $insured_member_criterias,
                            'family_constuct_relation_map' => $family_constuct_relation_map
                        ];

                        $this->load->view('insuredMemberSectionEdit', $insured_member_form_data);
                    }
                } else {

                    if (isset($generated_quote->family_construct) && $generated_quote->family_construct != '') {

                        $adult_child_count_arr = explode('-', $generated_quote->family_construct);
                        $member_count = ['adult_count' => $adult_child_count_arr[0], 'child_count' => $adult_child_count_arr[1]];


                        $insured_member_form_data = [
                            'coapplicant_tab_id' => $coapplicant_tab_id ?? '',
                            'customer' => $customer,
                            'member_count' => $member_count,
                            'criterias' => $insured_member_criterias,
                            'family_constuct_relation_map' => $family_constuct_relation_map
                        ];

                        $this->load->view('insuredMemberSection', $insured_member_form_data);
                    } else {

                ?>
                        <div class="alert alert-warning fade show" role="alert">
                            <strong>Please fill the Generate Quote section first!</strong>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div id="accordion4" class="according accordion-s2 mt-3">
        <div class="card card-member">
            <div class="card-header card-vif">
                <?php 
                    $no_collapsable = 'no-collapsable';
                    if(isset($current_proposal_details->nominee_relation)){

                        $no_collapsable = return_editable($current_proposal_details->nominee_relation, $is_only_previewable);
                    }
                ?>
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion4550<?php echo $coapplicant_tab_id ?? '' ?>" aria-expanded="false"> <span class="lbl-card">Nominee Details - <i class="ti-file"></i></span></a>
            </div>
            <div id="accordion4550<?php echo $coapplicant_tab_id ?? '' ?>" class="collapse card-vis-mar accord-data" data-parent="#accordion4" style="">
                <!-- form for Nominee Details -->
                <form class="form-horizontal" id="nominee_data<?php echo $coapplicant_tab_id ?? '' ?>" method="post" enctype="multipart/form-data">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Relation with Proposer<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <select class="form-control nominee_relation" name="nominee_relation" id="nominee_relation">
                                        <option value="">Select Nominee</option>
                                        <?php foreach ($nominee_relations as $relationKey => $relation) : ?>
                                            <?php $selected = $relation->id == $current_proposal_details->nominee_relation ? "selected" : ""; ?>
                                            <option data-opt="<?php echo $relation->gender ?>" value="<?php echo $relation->id ?>" <?php echo $selected ?>><?php echo $relation->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">First Name<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control nominee_first_name first_name" type="text" value="<?php if (!empty($current_proposal_details)) {
                                                                                                                        echo $current_proposal_details->nominee_first_name;
                                                                                                                    } ?>" maxlength="50" autocomplete="off" name="nominee_first_name" id="nominee_first_name">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>
                            <!--
								<div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Middle Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend" required="">
										
										<input class="form-control" type="text" value="" name="middle_name" maxlength="50" autocomplete="off">
										
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                    </div>
                                </div>
								-->
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control nominee_last_name last_name" type="text" maxlength="50" autocomplete="off" value="<?php if (!empty($current_proposal_details)) {
                                                                                                                                                        echo $current_proposal_details->nominee_last_name;
                                                                                                                                                    } ?>" name="nominee_last_name" id="nominee_last_name">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Date of Birth<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input class="form-control nominee_dob" type="text" name="nominee_dob" value="<?php if (isset($current_proposal_details->nominee_dob)) {
                                                                                                                        echo date('d-m-Y', strtotime($current_proposal_details->nominee_dob));
                                                                                                                    } ?>" autocomplete="off">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Contact No</label>
                                <div class="input-group">
                                    <input class="form-control nominee_contact" type="text" autocomplete="off" value="<?php if (!empty($current_proposal_details)) {
                                                                                                                            echo $current_proposal_details->nominee_contact;
                                                                                                                        } ?>" name="nominee_contact" id="nominee_contact" maxlength="10">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">phone_android</span></span>
                                    </div>
                                </div>
                                <div><label class="moberror"></label></div>
                            </div>
                        </div>

                        <div class="row mt-4 form-buttons">
                            <input type="hidden" name="lead_id" value="<?php echo $customer->lead_id; ?>" />
                            <input type="hidden" name="plan_id" value="<?php echo $customer->plan_id; ?>" />
                            <input type="hidden" class="customer_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="customer_id" value="<?php echo $customer->customer_id; ?>" />
                            <input type="hidden" class="proposal_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="proposal_id" value="<?php echo $customer->proposal_details_id; ?>" />
                            <input type="hidden" name="trace_id" value="<?php echo $customer->trace_id; ?>" />
                            <div class="col-md-1 col-6 text-left">
                                <button class="btn smt-btn">Save</button>
                            </div>
                            <!-- <div class="col-md-2 col-6 text-right">
                                <button class="btn cnl-btn">Cancel</button>
                            </div> -->
                        </div>
                    </div>
                </form>
                <!-- end form Nominee Details -->
            </div>
        </div>
    </div>

    <div id="accordion6" class="according accordion-s2 mt-3">
        <div class="card card-member">

            <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion676<?php echo $coapplicant_tab_id ?? '' ?>" aria-expanded="false"> <span class="lbl-card">Health Declaration - <i class="ti-heart"></i></a>
            </div>
            <div id="accordion676<?php echo $coapplicant_tab_id ?? '' ?>" class="collapse card-vis-mar accord-data" data-parent="#accordion6" style="">
                <div id="ghd-declaration<?php echo $coapplicant_tab_id ?? '' ?>">
                </div>
                <div class="row mt-4 form-buttons">
                    <div class="col-md-1 col-12 text-left ml-2 mr-2">
                        <button class="btn smt-btn proposal-save<?php echo $coapplicant_tab_id ?? ''; ?>">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </p>
</div>

<div class="modal" tabindex="-1" role="dialog" id="generate-quote-delete-modal<?php echo $coapplicant_tab_id ?? ''; ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Are you sure?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Changing the family construct will delete all insured members. Do you still want to continue?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generate-quote-delete-modal-btn<?php echo $coapplicant_tab_id ?? ''; ?>">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('.quote_generation_fields').change(function() {
        
        //if($(this).find('option:selected').val() != ''){
            
            let data = {};
            data.lead_id = "<?php echo $customer->lead_id; ?>";
            data.customer_id = "<?php echo $customer->customer_id; ?>";
            data.proposal_details_id = $(this).closest('form').find('input[name="proposal_id"]').val(); //$('#accordion460<?php echo $coapplicant_tab_id ?? '' ?> .proposal_id_hidden').val();

            $.ajax({
                url: "<?php echo base_url(); ?>policyproposal/checkInsuredMembersExist",
                method: "POST",
                dataType: "JSON",
                data: data,
                async: false,
                cache: false,
                success: function(response) {
                    if (response.success) {
                        $('#generate-quote-delete-modal').modal('show');
                    }
                }
            });
        //}
    });

    $("body").on('click', '#generate-quote-delete-modal-btn<?php echo $coapplicant_tab_id ?? ''; ?>', function() {
        let data = {};
        data.lead_id = "<?php echo $customer->lead_id; ?>";
        data.customer_id = $('#accordion460' + coapplicant_tab_id + ' .customer_id_hidden' + coapplicant_tab_id).val();
        data.proposal_details_id = $('#accordion460' + coapplicant_tab_id + ' .proposal_id_hidden' + coapplicant_tab_id).val();

        $.ajax({
            url: "<?php echo base_url(); ?>policyproposal/deleteInsuredMembers",
            method: "POST",
            dataType: "JSON",
            data: data,
            async: false,
            cache: false,
            success: function(response) {

                $('#generate-quote-delete-modal').modal('hide');
                if (response.status) {

                    displayMsg("success", response.msg);

                    formID = '#accordion480';
                    if ($('#myTab .nav-link.active').attr('data_id')) {

                        if (($('#myTab .nav-link.active').attr('data_id') - 1) > 0) {

                            accordCount = $('#myTab .nav-link.active').attr('data_id') - 1;
                            formID = '#accordion480' + accordCount.toString();
                        }
                    }

                    parentFormID = $('#accordion460' + coapplicant_tab_id).find('form').attr('id');

                    displayInsuredMember(parentFormID, formID);

                    msg = "Please save Generate Quote Section and then save all the member details";
                    if(response.msg.indexOf('Nominee') != -1){

                        msg += " and then nominee details as well";
                    }
                    displayMsg("error", msg);

                } else {

                    displayMsg("error", response.msg);
                }
            }
        });
        
        return false;
    });
</script>

<?php if (isset($is_only_previewable) && $is_only_previewable) : ?>
    <script>
        $(":input").prop("disabled", true);
        $(".form-buttons").hide();
    </script>
<?php endif; ?>

<?php if (isset($coapplicant_tab_id)) : ?>
    <script>
        $(document).ready(function() {
            let master_quote_ids = <?php echo json_encode($master_quote_ids) ?>;
            $('#leadform<?php echo $coapplicant_tab_id ?> [name="master_quote_id"]').val(master_quote_ids.join());


        });
        var coapplicant_validation = {
            rules: vRules,
            messages: vMessages,
            submitHandler: function(form) {
                var act = "<?php echo base_url(); ?>policyproposal/coapplicantsubmitForm";
                $("#cust_data<?php echo $coapplicant_tab_id ?>").ajaxSubmit({
                    url: act,
                    type: 'post',
                    dataType: 'json',
                    cache: false,
                    clearForm: false,
                    beforeSubmit: function(arr, $form, options) {
                        var mob = $("#cust_data<?php echo $coapplicant_tab_id ?> #mobile_no2").val();
                        var gender = $("#cust_data<?php echo $coapplicant_tab_id ?> select[name='gender1']").find('option:selected').val();

                        if (mob != '') {
                            var filter = /^[6789]\d{9}$/;
                            if (filter.test(mob)) {
                                $(".moberror_customer").html("").css('display', 'none');
                            } else {
                                $(".moberror_customer").html("Please enter valid phone number").removeAttr('style');
                                return false;
                            }
                        }

                        if(gender == ''){

                            $(".moberror_gender").html("Please select gender").removeAttr('style');
                            return false;
                        }
                        else{

                            $(".moberror_gender").html("").css('display', 'none');
                        }
                        $(".btn-primary").hide();
                        //return false;
                    },
                    success: function(response) {
                        $(".btn-primary").show();
                        if (response.success) {

                            $("#self_age_<?php echo $coapplicant_tab_id ?? ''; ?>").val(response.self_age);

                            let customer_id = response.Data.cust_id;
                            let proposal_id = response.Data.prop_id;

                            $(".customer_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>").val(customer_id);
                            $(".proposal_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>").val(proposal_id);
                            displayMsg("success", response.msg);

                            enableNextAccordion("#cust_data<?php echo $coapplicant_tab_id ?>");

                        } else {
                            displayMsg("error", response.msg);
                            return false;
                        }
                    }
                });

            }
        }
        $("#cust_data<?php echo $coapplicant_tab_id ?>").validate(coapplicant_validation);
    </script>
<?php endif; ?>

<script>
    //var app_coapp_premium = 0;
    function onInsuredMemberCheckAccept(element) {
        $closestForm = $(element).closest('form');
        $(element).closest('.get_insured_member_modal').modal('hide');
        $closestForm.find('.spouse_age_input_box').hide();
        $closestForm.find("[name=spouse_age]").attr('disabled', true);
        $closestForm.find("[name=spouse_dob]").attr('disabled', true);
        generateQuote($closestForm.attr('id'));

        formID = '#accordion480';
        if ($('#myTab .nav-link.active').attr('data_id')) {

            if (($('#myTab .nav-link.active').attr('data_id') - 1) > 0) {

                accordCount = $('#myTab .nav-link.active').attr('data_id') - 1;
                formID = '#accordion480' + accordCount.toString();
            }
        }
        displayInsuredMember($closestForm.attr('id'), formID, 0);
    }

    function onInsuredMemberCheckReject(element) {
        $closestForm = $(element).closest('form');
        $(element).closest('.get_insured_member_modal').modal('hide');
        $closestForm.find('.spouse_age_input_box').show();
        $closestForm.find("[name=spouse_age]").attr('disabled', false);
        $closestForm.find("[name=spouse_dob]").attr('disabled', false);
        //resetInsuredForm();

        formID = '#accordion480';
        if ($('#myTab .nav-link.active').attr('data_id')) {

            if (($('#myTab .nav-link.active').attr('data_id') - 1) > 0) {

                accordCount = $('#myTab .nav-link.active').attr('data_id') - 1;
                formID = '#accordion480' + accordCount.toString();
            }
        }
        displayInsuredMember($closestForm.attr('id'), formID, 1);
    }
    $("#leadform<?php echo $coapplicant_tab_id ?? '' ?>").validate({
        rules: [],
        messages: [],
        submitHandler: function(form) {
            var act = "<?php echo base_url(); ?>policyproposal/submitLeadForm";
            var form_id = "#leadform<?php echo $coapplicant_tab_id ?? '' ?>";

            $(form_id).ajaxSubmit({
                url: act,
                type: 'post',
                dataType: 'json',
                cache: false,
                clearForm: false,

                beforeSubmit: function(arr, $form, options) {

                    /*if(app_coapp_premium == 0){

                        displayMsg("error", "Quote cannot be saved as some policy/policies might not have sum insured for a particular age group");
                        return false;
                    }*/

                    let customer_id = null;
                    arr.forEach(function(element) {
                        if (element.name == 'customer_id' && element.value) {
                            customer_id = element.value;
                        }
                    });

                    if (!customer_id) {
                        displayMsg("error", "Please save customer details first!");
                        return false;
                    }
                },
                success: function(response) {
                    generateQuote(form_id.replace('#', ''));
                    if (response.success) {
                        let quote_ids = response.data.quote_ids;
                        $(form_id).find("[name='master_quote_id']").val(quote_ids.join());
                        if (changeInDOB) {
                            displayMsg("success", "Your premium has been recalculated");
                            changeInDOB = false;
                        } 
                        else if(response.policy_errors){

                            displayMsg("error", response.policy_errors, 8000);
                            return false;
                        }
                        else {
                            displayMsg("success", response.msg);
                        }

                        enableNextAccordion("#leadform<?php echo $coapplicant_tab_id ?? '' ?>");
                    } else {

                        if(response.policy_errors){

                            displayMsg("error", response.policy_errors, 8000);
                        }
                        else {

                            displayMsg("error", response.msg);
                        }
                        return false;
                    }
                }
            });
        }
    });
</script>
<script>
    var current_tab;
    var quote_details;
    var self_age = 0;
    var quote_age = 0;
    var changeInDOB = false;
    var coapplicant_tab_id = "<?php echo $coapplicant_tab_id ?? ''; ?>"; //$('#coapplicant_tab_id').val();
    var family_construct;

    $(".predatepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        maxDate: new Date("<?php echo $validations['minSelfDob'] ?>"),
        minDate: new Date("<?php echo $validations['maxSelfDob'] ?>"),
        dateFormat: 'dd-mm-yy',
        yearRange: "-100:" + new Date('Y'),
        onSelect: function(dateText) {

            //self_age = calculateAge(dateText);

            if (accord_count != '' && accord_count != 0) {

                dob = $('#accordion450' + accord_count + ' .predatepicker').val();

                $('#accordion480' + accord_count + ' input[name="insured_member_dob"]').val(dob);
            }

            selfBirthDate = $(this).val();

            insuredSelfDob = ".selfDob<?php echo $coapplicant_tab_id ?? '' ?>";
            dobObj = $("#dob1<?php echo $coapplicant_tab_id ?? '' ?>").datepicker('getDate');

            if(dobObj){

                //dobObj = dobObj.split('-');
                selfNewDob = dobObj.getDate()+'-'+(dobObj.getMonth()+1)+'-'+dobObj.getFullYear();
                $(insuredSelfDob).datepicker('setDate', dobObj);
            }
        }
    });

    $(".nominee_dob").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        maxDate: new Date(),
        yearRange: "-100:" + new Date('Y')
    });

    $(".spouse_dob").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        maxDate: new Date("<?php echo $validations['minSpouseDob'] ?>"),
        minDate: new Date("<?php echo $validations['maxSpouseDob'] ?>"),
        yearRange: "-100:" + new Date('Y')
    });

    $('body').on('focus', '.insured_member_dob', function() {

        $(this).datepicker({
            changeMonth: true,
            changeYear: true,
            maxDate: new Date(),
            dateFormat: 'dd-mm-yy',
            yearRange: "-100:" + new Date('Y'),
            /*onSelect: function(dateText) {

                if ($(this).closest('form').find('select[name="member_type_id"]').val() == 2) {

                    checkRelation(dateText);
                }
            }*/
        });
    });

    /*$('body').on('change', '#accordion480' + coapplicant_tab_id + ' select[name="member_type_id"]', function() {

        if ($("#accordion480" + coapplicant_tab_id + " .insured_member_dob").val() != '' && $(this).val() == 2) {

            checkRelation($("#accordion480" + coapplicant_tab_id + " .insured_member_dob").val());
        }
    });*/

    /*function checkRelation(dateText) {

        if ($('#master_quote_id').val() != '') {

            if (quote_age == 0 && premium == 0) {

                quote = getQuoteDetails();
                if (quote) {

                    $.each(quote, function(key, value) {

                        if (value.premium_with_tax != null) {
                            premium += parseFloat(value.premium_with_tax);
                        }
                        if (key == 0) {
                            quote_age = value.age;
                        }
                    });
                }
            }

            if ($('#accordion480' + coapplicant_tab_id + ' select[name="member_type_id"]').val() == '2') {

                self_age = $("#self_age_<?php echo $coapplicant_tab_id ?? ''; ?>").val();
                spouseAge = calculateAge(dateText);

                spouseAge = calculateAge(dateText);
                if (spouseAge > self_age) {

                    if (spouseAge != quote_age) {

                        $('.age-diff-modal .modal-title').text('Age Mismatch');
                        $('.age-diff-modal .modal-body')
                            .html('<p>Age does not match with the birthdate provided, do you want to rectify it?</p>');
                        $('.age-diff-modal .modal-footer').removeClass('d-none');
                        $('.age-diff-modal').modal('show');
                    }
                }
            }
        }
    }*/

    $('.nominee_relation').change(function() {
        $closestForm = $(this).closest('form');
        let formData = $closestForm.serializeArray();
        $.ajax({
            url: "<?php echo base_url(); ?>policyproposal/populateNomineeRelation",
            type: 'post',
            dataType: 'json',
            data: formData,
            cache: false,
            clearForm: false,
            async: false,
            success: function(response) {
                if (!response) {
                    return;
                }

                $closestForm.find('[name="nominee_first_name"]').val(response.policy_member_first_name);
                $closestForm.find('[name="nominee_last_name"]').val(response.policy_member_last_name);
                $closestForm.find('[name="nominee_dob"]').datepicker('setDate', response.policy_member_dob);
            }
        });
    });


    /*$('body').on('click', '.change-age-dob', function() {

        var new_age = spouseAge; //calculateAge($(".insured_member_dob").val());
        var minSpouseAge = 0;
        var maxSpouseAge = 0;

        <?php
        
            if(isset($validations['minSpouseAge'])){
                ?>
                minSpouseAge = <?=$validations['minSpouseAge']; ?>;
                <?php
            }

            if(isset($validations['maxSpouseAge'])){
                ?>
                maxSpouseAge = <?=$validations['maxSpouseAge']; ?>;
                <?php
            }
        ?>

        if(minSpouseAge && maxSpouseAge){
            
            if (!(new_age >= minSpouseAge && new_age <= maxSpouseAge)) {

                displayMsg("error", "Age should be between 18 to 55 for adults");
            } else {
                changeInDOB = true;
                /* $('#spouse_age').val(new_age);
                form_id = $('#spouse_age').closest('form').attr('id');
                $('#' + form_id + ' .lead-submit-btn').trigger('click');*/

                /*$("#leadform<?php echo $coapplicant_tab_id ?? '' ?> #spouse_age").val(new_age);
                $("#leadform<?php echo $coapplicant_tab_id ?? '' ?>").submit();
            }
            $('.age-diff-modal').modal('hide');
        }
        return false;
    });*/

    /*$('#accordion480' + coapplicant_tab_id + ' select[name="member_type_id"]').on('change', function() {

        if ($(".insured_member_dob").val() != '') {

            checkRelation($(".insured_member_dob").val());
        }
    });*/

    $(document).ready(function() {

        $('#coapplicant_tab_id').val("<?php echo $coapplicant_tab_id ?? '' ?>");
        current_tab = $('#accordion480' + coapplicant_tab_id + ' .nav-link:first').attr('id');

        /*$('body').on('change', '#accordion480' + coapplicant_tab_id + ' select[name="gender"]', function() {

            sal_gender = $(this).find('option:selected').val();
            if (sal_gender != '') {

                $('#accordion480' + coapplicant_tab_id + ' select[name="insured_member_gender"]').val(sal_gender);
            }
        });*/

        $('body').on('change', '#accordion450<?php echo $coapplicant_tab_id ?? '' ?> [name="salutation"]', function() {

            sal_gender = $(this).find('option:selected').data('gender');
            if (sal_gender != '') {
                $('#accordion450<?php echo $coapplicant_tab_id ?? '' ?> select[name="gender1"]').val(sal_gender).attr('disabled', true);
                $('#accordion450<?php echo $coapplicant_tab_id ?? '' ?> input[name="gender1"]').val(sal_gender);
            }
            else{

                $('#accordion450<?php echo $coapplicant_tab_id ?? '' ?> select[name="gender1"]').val(sal_gender).removeAttr('disabled');
                $('#accordion450<?php echo $coapplicant_tab_id ?? '' ?> input[name="gender1"]').val(sal_gender);
            }
        });

        $('body').on('change', '#accordion450<?php echo $coapplicant_tab_id ?? '' ?> select[name="gender1"]', function() {

            gender = $(this).find('option:selected').val();
            if (gender != '') {
                
                $('#accordion450<?php echo $coapplicant_tab_id ?? '' ?> input[name="gender1"]').val(sal_gender);
            }
            else{

                $('#accordion450<?php echo $coapplicant_tab_id ?? '' ?> input[name="gender1"]').val(sal_gender);
            }
        });


        //$(".insured_member_dob").datepicker('setDate', '<?php if (isset($customer->dob)) {
                                                                echo date('d-m-Y', strtotime($customer->dob));
                                                            } ?>');
        $(".nominee_dob").datepicker('setDate', '<?php if (isset($current_proposal_details->nominee_dob)) {
                                                        echo date('d-m-Y', strtotime($current_proposal_details->nominee_dob));
                                                    } ?>');
        $(".predatepicker").datepicker('setDate', '<?php if (isset($customer->dob)) {
                                                        echo date('d-m-Y', strtotime($customer->dob));
                                                    } ?>');
    });

    $('body').on('click', '.nav-link.insured-member', function() {

        current_tab = $(this).attr('id');
        <?php /*
         if ($(this).hasClass('disabled')) {

            $('#accordion480' + coapplicant_tab_id + ' .member-tab-error-msg').removeClass('d-none');
            setTimeout(function() {

                $('#accordion480' + coapplicant_tab_id + ' .member-tab-error-msg').addClass('d-none')
            }, 5000);

        } else {

            tab = $(this).attr('href');
            member_relation = $(tab + ' select[name="member_type_id"] option:selected').val();

            if (member_relation == 1) {

                $('#accordion480' + insuredFormCount + ' input[name="insured_member_dob"]').datepicker('setDate', "<?php if (isset($customer->dob)) {
                                                                                                                        echo date('d-m-Y', strtotime($customer->dob));
                                                                                                                    } ?>");
            } else {

                $('#accordion480' + insuredFormCount + ' input[name="insured_member_dob"]').datepicker('setDate', "");
            }
            current_tab = $(this).attr('id');
            member_id = $(this).attr('data-member');
            member_added = $(this).attr('data-member-added');
            lead_id = $('#accordion480' + coapplicant_tab_id + ' input[name="lead_id"]').val();
            trace_id = $('#accordion480' + coapplicant_tab_id + ' input[name="trace_id"]').val();
            plan_id = $('#accordion480' + coapplicant_tab_id + ' input[name="plan_id"]').val();
            customer_id = $('#accordion480' + coapplicant_tab_id + ' input[name="customer_id"]').val();
            proposal_id = $('#accordion480' + coapplicant_tab_id + ' input[name="proposal_id"]').val();

            data = {};
            data.member_id = member_id;
            data.member_added = member_added;
            data.lead_id = lead_id;
            data.trace_id = trace_id;
            data.plan_id = plan_id;
            data.customer_id = customer_id;
            data.proposal_id = proposal_id;
            data.insuredFormCount = insuredFormCount;
            data.coapplicant_tab_id = coapplicant_tab_id;
            data.current_tab = current_tab;

            $.ajax({

                url: "<?php echo base_url(); ?>policyproposal/getmemberdetails",
                type: 'post',
                dataType: 'html',
                data: {
                    'info': data
                },
                cache: false,
                clearForm: false,
                async: false,
                success: function(response) {

                    if (response) {

                        $('#accordion480' + coapplicant_tab_id + ' .insured-member-section').html(response);
                        //$(".insured_member_dob").datepicker('setDate', $('#member_dob').val());
                    }
                }
            });
             }
         */
        ?>

    });

    $("#accordion676<?php echo $coapplicant_tab_id ?? '' ?> #mydatas tr").append(`
        <td style="width: 150px;">
            <div>
                <div class="custom-control custom-radio" style="float: left;">
                    <input name="health-declaration" class="custom-control-input radios_out health-check-yes" id="4<?php echo $coapplicant_tab_id ?? '' ?>" name="4<?php echo $coapplicant_tab_id ?? '' ?>" type="radio" value="Yes" />
                    <label class="custom-control-label" for="4<?php echo $coapplicant_tab_id ?? '' ?>" name="4<?php echo $coapplicant_tab_id ?? '' ?>"> Yes </label></div>
                </div>
                <div>
                    <div class="custom-control custom-radio" style="float:right;">
                        <input name="health-declaration" class="custom-control-input radios_out health-check-no" id="4_1<?php echo $coapplicant_tab_id ?? '' ?>" name="4<?php echo $coapplicant_tab_id ?? '' ?>" type="radio" value="No" />&nbsp;
                        <label class="custom-control-label" for="4_1<?php echo $coapplicant_tab_id ?? '' ?>" name="4<?php echo $coapplicant_tab_id ?? '' ?>"> No </label>
                    </div>
                </div>
            <div>
                &nbsp;
            </div>
        </td>
    `);

    <?php

    $tab = $coapplicant_tab_id ?? '';
    if (isset($leaddetails->proposal_details[$tab]->health_declaration) && $leaddetails->proposal_details[$tab]->health_declaration != '') {

        $health_declaration = $leaddetails->proposal_details[$tab]->health_declaration;
    ?>
        $("#accordion676<?php echo ($tab == 0) ? '' : $tab; ?> .health-check-" + "<?php echo strtolower($health_declaration); ?>").attr('checked', true);
    <?php
    }
    ?>

    function getQuoteDetails() {

        data = {};
        //data.quote_ids = $('#master_quote_id'+ coapplicant_tab_id).val();
        //data.customer_id = $("#leadform" + coapplicant_tab_id + " [name=customer_id]").val(),
        data.lead_id = $("#leadform" + coapplicant_tab_id + " [name=lead_id]").val(),
        quote_details = '';

        $.ajax({

            url: "<?php echo base_url(); ?>policyproposal/getquotedetails",
            type: 'post',
            dataType: 'json',
            /*data: {
                'quote_ids': data
            },*/
            data: data,
            cache: false,
            clearForm: false,
            async: false,
            success: function(response) {

                if (response.success) {

                    if (response.data) {

                        quote_details = response.data;
                    }
                }
            }
        });

        return quote_details;
    }

    /*function calculateAge(dob) {

        dob = dob.split("-")
        var dob = new Date(dob[2], dob[1] - 1, dob[0])
        var today = new Date();

        var month = today.getMonth() + 1;
        if (month < 10) {
            month = '0' + month;
        }
        var day = today.getDate();
        if (day < 10) {
            day = '0' + day;
        }
        var year = today.getFullYear();

        today = year + '' + month + '' + day;

        var dob_month = dob.getMonth() + 1;
        if (dob_month < 10) {
            dob_month = '0' + dob_month;
        }
        var dob_day = dob.getDate();
        if (dob_day < 10) {
            dob_day = '0' + dob_day;
        }
        var dob_year = dob.getFullYear();

        dob = dob_year + '' + dob_month + '' + dob_day;

        age = '' + (today - dob);

        if (age.length > 4) {
            age = age.replace(age.substr(-4), '');
        }

        return age;
    }*/
</script>
<script>
    $(document).ready(function() {
        loadGHDDeclaration();

        <?php //if($no_collapsable){ ?>

        $('body').on('click', '.no-collapsable', function (e) {

                e.stopPropagation();
                return false;
            });

        <?php //} ?>
    });

    function loadGHDDeclaration(coapplicant_tab_id = "") {
        let url = '<?php echo base_url() . "policyproposal/ghddeclaration" ?>';
        $("#ghd-declaration" + coapplicant_tab_id).load(url, {
            customer_id: $("#leadform" + coapplicant_tab_id + " [name=customer_id]").val(),
            lead_id: $("#leadform" + coapplicant_tab_id + " [name=lead_id]").val(),
        });
    }
</script>

<script>
    var isSignleAdultPolicy = Boolean("<?=$is_one_adult_policy; ?>");
    /* for memberform Insured  form **/

    $("body").on('change', "#accordion460<?php echo $coapplicant_tab_id ?? '' ?> #family_members_ac_count", function(e, params = null) {

        parentFormID = e.target.closest('form').id;
        formID = '#accordion480';

        if ($('#myTab .nav-link.active').attr('data_id')) {

            if (($('#myTab .nav-link.active').attr('data_id') - 1) > 0) {

                accordCount = $('#myTab .nav-link.active').attr('data_id') - 1;
                formID = '#accordion480' + accordCount.toString();
            }
        }

        if ($("#" + parentFormID + " #family_members_ac_count").val() == '1-0') {
            // $(formID + " #self-tab").show();
            // $(formID + " #spouse-tab").hide();
            if ($("#" + parentFormID + " .spouse_age_input_box")) {
                if (params == null) {
                    displayPopupToGetInsuredMember(parentFormID);
                }
            }
            // $(formID + " #kid1-tab").hide();
            // $(formID + " #kid2-tab").hide();
        } else if ($("#" + parentFormID + " #family_members_ac_count").val() == '2-0') {
            // $(formID + " #spouse-tab").show();
            // $(formID + " #self-tab").show();
            // $(formID + " #kid1-tab").hide();
            // $(formID + " #kid2-tab").hide();
            if ($("#" + parentFormID + " .spouse_age_input_box")) {
                $("#" + parentFormID + " .spouse_age_input_box").show();
                $('#' + parentFormID + ' [name=spouse_dob]').attr('disabled', false);
            }
        } else if ($("#" + parentFormID + " #family_members_ac_count").val() == '2-1') {
            // $(formID + " #self-tab").show();
            // $(formID + " #spouse-tab").show();
            if ($("#" + parentFormID + " .spouse_age_input_box")) {
                $("#" + parentFormID + " .spouse_age_input_box").show();
                $('#' + parentFormID + ' [name=spouse_dob]').attr('disabled', false);
            }
            // $(formID + " #kid1-tab").show();
            // $(formID + " #kid2-tab").hide();
        } else if ($("#" + parentFormID + " #family_members_ac_count").val() == '2-2') {
            // $(formID + " #spouse-tab").show();
            if ($("#" + parentFormID + " .spouse_age_input_box")) {
                $("#" + parentFormID + " .spouse_age_input_box").show();
                $('#' + parentFormID + ' [name=spouse_dob]').attr('disabled', false);
            }

            // $(formID + " #kid1-tab").show();
            // $(formID + " #kid2-tab").show();
            // $(formID + " #self-tab").show();
        }

        if (!$("#" + parentFormID + " #family_members_ac_count").val()) {
            $("#" + parentFormID + " .spouse_age_input_box").hide();
            $("#" + parentFormID + " [name='spouse_age']").val("");
        }

        if ($("#" + parentFormID + " #family_members_ac_count").val() != '1-0' || isSignleAdultPolicy) {

            displayInsuredMember(parentFormID, formID);
        }

        return false;
    });

    function displayInsuredMember(parentFormID, formID, notSelf = 0) {

        family_construct = $("#" + parentFormID + " #family_members_ac_count option:selected").val();

        if (family_construct != '') {

            generateQuote(parentFormID);
            getQuoteDetails();
            current_tab = 'tab-1';
            lead_id = $("#" + parentFormID + ' input[name="lead_id"]').val();
            customer_id = $("#" + parentFormID + ' input[name="customer_id"]').val();
            plan_id = $("#" + parentFormID + ' input[name="plan_id"]').val();
            proposal_details_id = $("#accordion460<?php echo $coapplicant_tab_id ?? '' ?>" + ' .proposal_id_hidden' + "<?php echo $coapplicant_tab_id ?? '' ?>").val();

            $.ajax({

                url: "<?php echo base_url(); ?>policyproposal/changeFamilyConstruct",
                method: 'post',
                dataType: 'HTML',
                data: {
                    member_count: $("#" + parentFormID + " #family_members_ac_count").val(),
                    co_applicant_tab_id: "<?php echo $coapplicant_tab_id ?? '' ?>",
                    lead_id: lead_id,
                    customer_id: customer_id,
                    plan_id: plan_id,
                    proposal_details_id: proposal_details_id,
                    not_self: notSelf
                },
                success: function(response) {

                    $(formID).html(response);
                }
            });
        } else {

            $('#accordion480<?php echo $coapplicant_tab_id ?? '' ?>').html(`
                <div class="alert alert-warning fade show" role="alert">
                    <strong>Please fill the Generate Quote section first!</strong>
                </div>`);
        }

        return false
    }

    function displayPopupToGetInsuredMember(formId) {

        if(!isSignleAdultPolicy){

            $("#" + formId + " .get_insured_member_modal").modal('show');
        }
    }

    /* for memberform Insured  form **/

    /*var vRules_member_data = {
        member_type_id: {
            required: true
        },
        first_name: {
            required: true,
            firstnamelettersonly: true
        },
        gender: {
            required: true
        },
        last_name: {
            required: true,
            lastnamevalidate: true
        },
        insured_member_dob: {
            required: true
        }
    };

    var vMessages_member_data = {
        member_type_id: {
            required: "Field is required"
        },
        first_name: {
            required: "Field is required"
        },
        gender: {
            required: "Field is required"
        },
        last_name: {
            required: "Field is required"
        },
        insured_member_dob: {
            required: "Field is required"
        }
    };

    $.validator.addMethod("firstnamelettersonly", function(value, element) {

        return this.optional(element) || /^[a-z\s]+$/i.test(value);
    }, "Only alphabetical characters");

    $.validator.addMethod("lastnamevalidate", function(value, element) {

        return this.optional(element) || /^[a-z\s]+$/i.test(value) || /^\.$/i.test(value);
    }, "Only alphabetical characters or a single period allowed");

    $("#accordion480 form").validate({
        rules: vRules_member_data,
        messages: vMessages_member_data,
        submitHandler: function(form) {
            var act = "<?php echo base_url(); ?>policyproposal/submitInsuredMemberForm";
            var form = "#accordion480 form";

            if ($(form + ' #' + current_tab).attr('data-member') != '') {

                $(this).find('input[name="member_id"]').val($(form + ' #' + current_tab).attr('data-member'));
            }
            gender = $(form + ' select[name="gender"] option:selected').text();
            $(form + ' input[name="member_salutation"]').val(gender);

            if (family_construct == '1-0') {

                member_type = $(form + ' select[name="member_type_id"]').val();
                if (member_type == 1) {

                    if ($("#accordion460 form input[name='spouse_age']").val() != '') {

                        $("#accordion460 form input[name='spouse_age']").val('');
                        $("#accordion460 form").submit();
                    }
                }
            }

            $("#accordion480 form").ajaxSubmit({
                url: act,
                type: 'post',
                dataType: 'json',
                cache: false,
                clearForm: false,
                async: false,
                beforeSubmit: function(arr, $form, options) {

                    if ($('#accordion480 input[name="member_type_id"]').val() == 1) {
                        checkRelation($('#accordion480 .insured_member_dob').val());
                    }

                    if (premium >= 50000) {
                        if ($('.pan_added').val() != 'y') {
                            $('.age-diff-modal .modal-title').text('Proposer PAN Required');
                            $('.age-diff-modal .modal-body')
                                .html('<form onsubmit="submitPan(this);return false;" action="<?php echo base_url(); ?>policyproposal/capturecustomerpan"><input type="text" name="proposer_pan">&nbsp;&nbsp;<span class="msg">&nbsp;&nbsp;</span><button type="submit" class="submit-btn-pan btn smt-btn">Save</button></form>');
                            $('.age-diff-modal .modal-footer').addClass('d-none');
                            $('.age-diff-modal').modal('show');

                            premium = 0;
                            return false;
                        }
                    }
                },
                success: function(response) {

                    if (response.success) {

                        $('#accordion480 form #' + current_tab).attr('data-member', response.member_id);

                        if ($('#accordion480 form #' + current_tab).next('.nav-link:visible').hasClass('disabled') == true) {

                            id = $('#accordion480 form #' + current_tab).next('.nav-link:visible').attr('id');
                            $('#accordion480 form #' + id).attr('data-member-added', response.data_added);
                            $('#accordion480 form #' + id).removeClass('disabled').trigger('click');

                            if ($('#accordion480 form #' + id).next('.nav-link:visible').length) {

                                $('#accordion480 form')[0].reset();
                            }

                            current_tab = id;
                        } else {

                            if (family_construct != '1-0') {

                                $('#accordion480 form #' + current_tab).trigger('click');
                            }
                        }

                        displayMsg("success", response.msg);

                    } else {
                        //alert("false");
                        displayMsg("error", response.msg);
                        return false;
                    }
                }
            });
        }
    });*/

    /*function resetInsuredForm() {

        $('body #accordion480' + "<?php echo $coapplicant_tab_id ?? '' ?>" + ' form select option').removeAttr('selected');
        $('body #accordion480' + "<?php echo $coapplicant_tab_id ?? '' ?>" + ' form input[type="text"]').val('');
    }*/

    current_main_tab_id = $('.main-tab.active').attr('id');
    summaryLoaded = false;

    $('.main-tab').on('click', function() {

        setTimeout(function() {

            current_main_tab_id = $('.main-tab.active').attr('id');

            if ($('.main-tab.active').hasClass('payment-tab')) {

                data = {};
                data.record = "<?php echo $customer->lead_id; ?>"
                $.ajax({

                    url: "<?php echo base_url(); ?>policyproposal/getPremiumSummary",
                    method: "POST",
                    dataType: 'html',
                    data: data,
                    cache: false,
                    success: function(response) {

                        $('.premium-summary').html(response);
                        summaryLoaded = true;
                    }
                });
            }
        }, 0);

        /*$.get( "<?php echo base_url(); ?>policyproposal/getProposalPolicySumInsured", function( response ) {
            
            if(response.status == 200){

                $("#pp_sum_insured").text(response.sum_insured);
            }
            else{

                $("#pp_sum_insured").text(0);
            }
        });*/

        if ($(this).hasClass('assignment-declaration')) {

            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>policyproposal/getProposalPolicySumInsured",
                data: {
                    'lead_id': "<?php echo $customer->lead_id; ?>"
                },
                dataType: 'JSON',
                success: function(response) {

                    if (response.success) {

                        $("#pp_sum_insured").text(response.sum_insured+' Rs');
                    } else {

                        $("#pp_sum_insured").text(0+' Rs');
                    }
                }
            });
        }
    });

    $('body').on('click', ".proposal-save<?php echo $coapplicant_tab_id ?? ''; ?>", function() {

        let ghd_request_data = $('#accordion676' + coapplicant_tab_id).find('form').serializeArray();

        let ghd_result = false;
        let ghd_result_message = '';

        ghd_request_data.push({
            name: 'customer_id',
            value: $("#leadform" + coapplicant_tab_id + " [name=customer_id]").val()
        });

        ghd_request_data.push({
            name: 'lead_id',
            value: $("#leadform" + coapplicant_tab_id + " [name=lead_id]").val()
        });

        $.ajax({
            url: "<?php echo base_url(); ?>policyproposal/submitGHDDeclaration",
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
        }

        data = {};
        data.health_declaration = $('#accordion676' + coapplicant_tab_id + ' input[name="health-declaration"]:checked').val();
        data.proposal_details_id = $('#accordion480' + coapplicant_tab_id + ' .proposal_id_hidden' + coapplicant_tab_id).val();

        $.ajax({

            url: "<?php echo base_url(); ?>policyproposal/submitProposal",
            data: data,
            dataType: 'JSON',
            method: 'post',
            async: false,
            success: function(response) {

                if (response.success) {

                    displayMsg("success", "Details Saved");

                    if (!$('.payment-tab').hasClass('active')) {

                        $('#' + current_main_tab_id).closest('.nav-item').nextAll('.nav-item:first').children('.main-tab').removeClass('disabled').trigger('click');
                    }

                    current_main_tab_id = $('.main-tab.active').attr('id');
                    $([document.documentElement, document.body]).animate({

                        scrollTop: $("#" + current_main_tab_id).offset().top
                    }, 800);

                } else {

                    displayMsg("error", "Something went wrong");
                }
            }
        });
        return false;
    });

    $.validator.addMethod("lettersonly", function(value, element) {
		return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
	}, "Letters only");

    $.validator.addMethod("mobile", function(value, element) {
		return this.optional(element) || /^[6789]\d{9}$/.test(value);
	}, "Please enter valid phone number");

    var vRules_nominee_data = {
        nominee_relation: {
            required: true
        },
        nominee_first_name: {
            required: true,
            lettersonly: true
        },
        nominee_last_name: {
            required: true,
            lettersonly: true
        },
        nominee_dob: {
            required: true
        },
        nominee_contact: {
            mobile: true
        }
        //nominee_salutation:{required:true},
        //nominee_gender:{required:true}
    };

    var vMessages_nominee_data = {
        nominee_relation: {
            required: "Field is required"
        },
        nominee_first_name: {
            required: "Field is required"
        },
        nominee_last_name: {
            required: "Field is required"
        },
        nominee_dob: {
            required: "Field is required"
        }
        //nominee_salutation:{required:"Field is required"},
        //nominee_gender:{required:"Field is required"}
    };

    $("#nominee_data<?php echo $coapplicant_tab_id ?? '' ?>").validate({
        rules: vRules_nominee_data,
        messages: vMessages_nominee_data,
        submitHandler: function(form) {
            var act = "<?php echo base_url(); ?>policyproposal/submitForm1";
            var form = "#nominee_data<?php echo $coapplicant_tab_id ?? '' ?>";
debugger;
            $("#nominee_data<?php echo $coapplicant_tab_id ?? '' ?>").ajaxSubmit({
                url: act,
                type: 'post',
                dataType: 'json',
                cache: false,
                clearForm: false,
                beforeSubmit: function(arr, $form, options) {},
                success: function(response) {
                    //alert(response);
                    if (response.success) {
                        displayMsg("success", response.msg);
                        loadGHDDeclaration(<?php echo $coapplicant_tab_id ?? '' ?>);
                        enableNextAccordion("#nominee_data<?php echo $coapplicant_tab_id ?? '' ?>");
                    } else {
                       // alert("false");
                        displayMsg("error", response.msg);
                        return false;
                    }
                }
            });
        }
    });

    /* Pre-populating the Insured member self form for co-applicant starts here */

    accord_count = "<?php echo $coapplicant_tab_id ?? ''; ?>";

    $('#accordion450' + accord_count + ' select[name="salutation"]').on('change', function() {

        if (accord_count != '' && accord_count != 0) {

            salutation = $('option:selected', this).val();
            var salutataion_gender_mapping = new Object();
            salutataion_gender_mapping['Mr'] = 'Male';
            salutataion_gender_mapping['Master'] = 'Male';
            salutataion_gender_mapping['Ms'] = 'Female';
            salutataion_gender_mapping['Mrs'] = 'Female';

            $('#accordion480' + accord_count + ' option[value="' + salutataion_gender_mapping[salutation] + '"]').attr('selected', true);
        }
    });

    $('#accordion450' + accord_count + ' input[name="firstname"]').on('change', function() {

        if (accord_count != '' && accord_count != 0) {

            firstname = $(this).val();

            $('#accordion480' + accord_count + ' input[name="first_name"]').val(firstname);

            $('#accordion480' + accord_count + ' select[name="member_type_id"] option[value="1"]').attr('selected', true);
        }
    });

    $('#accordion450' + accord_count + ' input[name="lastname"]').on('change', function() {

        if (accord_count != '' && accord_count != 0) {

            firstname = $(this).val();

            $('#accordion480' + accord_count + ' input[name="last_name"]').val(firstname);
        }
    });

    $('#accordion450' + accord_count + ' input[name="dob"]').on('change', function() {

        if (accord_count != '' && accord_count != 0) {

            dob = $(this).val();

            $('#accordion480' + accord_count + ' input[name="insured_member_dob"]').val(dob);
        }
    });

    $("#leadform<?php echo $coapplicant_tab_id ?? '' ?> [name=spouse_dob]").change(function() {
        $('#accordion480<?php echo $coapplicant_tab_id ?? '' ?> .spouse_age<?php echo $coapplicant_tab_id ?? '' ?>').val($(this).val());
    });
    /* Pre-populating the Insured member self form for co-applicant ends here */
</script>