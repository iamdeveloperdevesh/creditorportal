<?php
//print_r($datalist);
//echo $datalist->details[0]->premium_type;
//echo $datalist->plan_id;
//echo "<pre>";print_r($planmodes);exit;
/*foreach($planmodes as $key => $value){
	//echo $value->payment_mode_id;
	echo $value->workflow_id;
}
exit;
*/
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
    .remove {
        border: none;
        background: none;
        color: #ff0000;
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
    .addPaymentMode {
        background:none;
        border:none;
        color:#fff;
    }

</style>
<script src="<?php echo base_url(); ?>assets/js/products.js"></script>
<div class="col-md-10" id="body">
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
                                    <label for="validationCustomUsername" class="col-form-label">Plan Name</label>
                                    <div class="input-group">
                                        <input type="text" name="plan_name" value="<?php echo $datalist->details[0]->plan_name; ?>" class="form-control" aria-describedby="inputGroupPrepend" required="">
                                        <div class="input-group-prepend">
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
												<option value="<?php echo $mode->payment_mode_id; ?>" <?php if (in_array($mode->payment_mode_id, $planpay)) echo "selected"; ?>><?php echo $mode->payment_mode_name; ?></option>
											<?php } ?>
										</select>
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
										</div>
									</div>
								</div>-->

                                <div class="control-group form-group col-md-4 table-responsive">
                                    <label for="validationCustomUsername" class="col-form-label"><span>Payment Modes/Workflow*</span></label>
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
                                            <?php
                                            if(!empty($planmodes)){
                                                foreach($planmodes as $key => $value){
                                                    ?>
                                                    <tr class="paymentmode_tr">
                                                        <td>
                                                            <select class="select2 form-control" name="payment_modes[]" placeholder="Select">
                                                                <option value="">Select</option>
                                                                <?php foreach ($datalist->payment_modes as $mode) {
                                                                    $sel_mode = ($value->payment_mode_id == $mode->payment_mode_id) ? 'selected' : '';
                                                                    ?>
                                                                    <option value="<?php echo $mode->payment_mode_id; ?>" <?php echo $sel_mode; ?>><?php echo $mode->payment_mode_name; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="select2 form-control" name="payment_workflow[]" placeholder="Select">
                                                                <option value="">Select</option>
                                                                <?php foreach ($datalist->payment_workflows as $workflow) {
                                                                    $sel_workflow = ($value->workflow_id == $workflow->payment_workflow_master_id) ? 'selected' : '';
                                                                    ?>
                                                                    <option value="<?php echo $workflow->payment_workflow_master_id; ?>" <?php echo $sel_workflow; ?>><?php echo $workflow->workflow_name; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class=" remove"><i class="fa fa-remove"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php }}?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <div class="row mt-4" id="addplanbtn">
                                <div class="col-md-1 col-6 text-left">
                                    <!-- <button class="btn smt-btn" id="continuebtn">Continue</button> -->
                                    <a href="javascript:void(0)" class="btn cnt-btn" id="continuebtn">Continue</a>
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
                            <label for="validationCustomUsername" class="col-form-label">Plan Name</label>
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
            <div id="accordion45" class="collapse card-vis-mar" data-parent="#accordion2" style="">
                <form class="form-horizontal" id="form-policy" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy Sub Type</label>
                                <div class="input-group">
                                    <select id="policySubType" name="policySubType" class="form-control">
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
                                    <input class="form-control policyno" type="number" data-id="<?php echo $detail->policy_id; ?>" value="<?php if (isset($datalist->policydetails)) {
                                        echo $datalist->policydetails[0]->policy_number;
                                    } ?>" id="policyNo" name="policyNo">

                                    <div class="error"><span class="policyerror"></span></div>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3" style="display:flex;">
                                <div class="form-check custom-control custom-checkbox" style="margin-top: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" name="mandatory" value="1" id="mandatory_option" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->is_optional == 0) {
                                        echo "checked";
                                    } ?>> <label class="form-check-label custom-control-label" for="mandatory_option"> Mandatory </label> </div>
                                <div class="form-check custom-control custom-checkbox" id="combo_flag" style="margin-top: 46px; margin-left: 46px;"> <input type="checkbox" class="form-check-input custom-control-input" value="1" name="combo" id="Combo_option" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->is_combo == 1) {
                                        echo "checked";
                                    } ?>> <label class="form-check-label custom-control-label" for="Combo_option"> Combo </label> </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">PDF type</label>
                                <div class="input-group">
                                    <select id="pdf_type" name="pdf_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->pdf_type == 1) {
                                            echo "selected";
                                        } ?>>I</option>
                                        <option value="2" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->pdf_type == 2) {
                                            echo "selected";
                                        } ?>>C1</option>
                                        <option value="3" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->pdf_type == 3) {
                                            echo "selected";
                                        } ?>>C2</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Premium Type</label>
                                <div class="input-group">
                                    <select id="premium_type" name="premium_type" class="form-control">
                                        <option value="1" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->premium_type == 1) {
                                            echo "selected";
                                        } ?>>Absolute</option>
                                        <option value="0" <?php if (isset($datalist->policydetails) && $datalist->policydetails[0]->premium_type == 0) {
                                            echo "selected";
                                        } ?>>Per Mile rate</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
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
                                            //if(isset($datalist->policydetails) && $datalist->policydetails[0]->insurer_id == $insurer->insurer_id){echo "selected"; }
                                            if ($insurer->insurer_id == 1) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $insurer->insurer_name; ?></option>


                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy Start Date</label>
                                <div class="input-group">
                                    <input class="form-control imgDatepicker datepicker" type="text" id="policyStartDate" name="policyStartDate" autocomplete="off" value="<?php if (isset($datalist->policydetails)) {
                                        echo $datalist->policydetails[0]->policy_start_date;
                                    } ?>">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Policy end Date</label>
                                <div class="input-group">
                                    <input class="form-control imgDatepicker datepicker" type="text" id="policyEndDate" name="policyEndDate" autocomplete="off" value="<?php if (isset($datalist->policydetails)) {
                                        echo $datalist->policydetails[0]->policy_end_date;
                                    } ?>">
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

                                    <!--	<input type="number" name="membercount" class="form-control membercount"
							value="<?php // if(isset($datalist->details)){echo $datalist->details[0]->max_member_count; } 
                                    ?>" />
							-->
                                    <input class="form-control membercount" type="number" name="membercount" id="name-field" value="<?php if (isset($datalist->policydetails)) {
                                        echo $datalist->policydetails[0]->max_member_count;
                                    } ?>" style="border-right: 1px solid #e3e4e8;">
                                </div>

                            </div>
                        </div>

                        <div class="row memberlist">
                            <!-- row inserted here -->

                            <?php if (isset($datalist->family_construct) && count($datalist->family_construct) > 0) {
                                $i = 0;
                                foreach ($datalist->family_construct as $family) {
                                    ?>
                                    <div class='col-sm-4 form-group'>
                                        <select data-id="<?php echo $i; ?>" name='member[]' class='form-control memberselect'>
                                            <?php foreach ($datalist->members as $member) {
                                                if ($family->member_type_id == $member->id) {
                                                    echo "<option value='$member->id' selected>$member->member_type</option>";
                                                } else {
                                                    echo "<option value='$member->id'>$member->member_type</option>";
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                    <div class='col-sm-8 form-group'>
                                        <input class='form-control agefield' type='number' value="<?php echo $family->member_min_age; ?>" placeholder='Min Age' min='1' max='100' name='minage[]' />
                                        <input type='number' value="<?php echo $family->member_max_age; ?>" placeholder='Max Age' class='form-control agefield' min='1' max='100' name='maxage[]' />
                                    </div>

                                    <?php $i++;
                                }
                            } ?>
                        </div>

                        <div class="row">
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
                                <label for="validationCustomUsername" class="col-form-label">Rater
                                    <!--SI Basis -->
                                </label>
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
                                    <div class="row lbl-body">
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
                                            <label for="validationCustomUsername" class="col-form-label">Enter Premium</label>
                                            <div class="input-group">
                                                <input type="number" placeholder="Premium" class="form-control" value="<?php if (isset($premium->premium_rate)) echo $premium->premium_rate;  ?>" name="premium_opt[]" autocomplete="off" step="0.01">
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
                                            <div class="del_btn_opt"><a>Delete<i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></div>
                                        <?php } ?>
                                    </div>
                                    <?php $i++;
                                }
                            } else { ?>
                                <div class="row lbl-body mt-1">
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
                                        <label for="validationCustomUsername" class="col-form-label">Enter Premium</label>
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
                                            <input type="checkbox" class="form-control taxchk" autocomplete="off">
                                            <input type="hidden" class="tax_opt" name="tax_opt[]" value="0" />
                                            <label class="form-check-input custom-control-input" for="istax">Is Taxable</label>
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
                                        <a href="../assets/familyExcel.xls" class="btn-22" download="ageExcel.xls">Download Format <i class="ti-download"></i></a>
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
                                        <a href="../assets/agefamilyExcel.xls" class="btn-22" download="ageExcel.xls">Download Format <i class="ti-download"></i></a>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload by Age -->
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

                        <div class="row mt-4" id="addpolicybtn">
                            <div class="col-md-1 col-12 text-left">
                                <input type="hidden" id="plan_id" name="plan_id" value="<?php echo $datalist->plan_id; ?>" />

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


        <?php  } ?>

    </div>

</div>
<!-- extra -->
</div>
</div>
<!-- end -->


<script>

    $(function() {

        $(".addPaymentMode").click(function()
        {
            var index = 1;
            $("#tbl_paymentmodes tbody tr.paymentmode_tr").each(function(){
                index = index + 1;
            });
            $html = '<tr class="paymentmode_tr">'+
                '<td>'+
                '<select class="select2 form-control" id="payment_modes'+index+'" name="payment_modes[]"  placeholder="Select">'+
                '<option value="">Select</option>'+
                <?php foreach ($datalist->payment_modes as $mode) { ?>
                '<option value="<?php echo $mode->payment_mode_id; ?>"><?php echo $mode->payment_mode_name; ?></option>'+
                <?php } ?>
                '</select>'+
                '</td>'+
                '<td>'+
                '<select class="select2 form-control" id="payment_workflow'+index+'" name="payment_workflow[]" placeholder="Select">'+
                '<option value="">Select</option>'+
                <?php foreach ($datalist->payment_workflows as $workflow) { ?>
                '<option value="<?php echo $workflow->payment_workflow_master_id; ?>"><?php echo $workflow->workflow_name; ?></option>'+
                <?php } ?>
                '</select>'+
                '</td>'+
                '<td class="text-center">'+
                '<button type="button" class="remove">'+
                '<i class="fa fa-remove"></i>'+
                '</button>'+
                "</td>"+
                "</tr>";
            $('#tbl_paymentmodes').find("tbody").append($html);
            $("#payment_modes"+index).select2();
            $("#payment_workflow"+index).select2();
        });

        $('#tbl_paymentmodes').on('click', '.remove', function () {
            var table_row = $('#tbl_paymentmodes tbody  tr.paymentmode_tr').length;
            if(table_row == '1'){
                alert("Atleast one mode is must. ");
            }else{
                $(this).closest('tr').remove();
            }
        });

    });

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
        //#accordion45

    });

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
        newRow += '<div class="row lbl-body mt-1">';
        newRow += '<div class="col-md-3 mb-3 col-12"><label for="validationCustomUsername" class="col-form-label">Enter Sum Insured</label><div class="input-group"><input type="number" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1[]" autocomplete="off"> <div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div></div></div>';
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
</script>