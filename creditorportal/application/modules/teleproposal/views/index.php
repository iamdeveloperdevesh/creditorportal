<!-- upendra - maker/checker 30-07-2021 -->
<?php
if(isset($_SESSION['telesales_session']['is_maker_checker'])){
    $is_maker_checker = $_SESSION['telesales_session']['is_maker_checker'];
}else{
    $is_maker_checker = 'no';
}


?>
<style>
    .smt-btn1 {
        background: #18ba59;
        border: 3px solid #c8e6c9;
        color: #fff;
    }
    body {
        font-family: 'Titillium Web', sans-serif !important;
    }
    .table thead th {
        border-bottom: 2px dashed #deff !important;
        text-transform: capitalize;
    }
    table tr th, table tr td {
        border-bottom: none !important;
        font-weight: 600!important;
    }
    table tr th, table tr td {
        border-top: none !important;
    }
    .bor-rr {
        border:1px solid #deff;
    }
    .td-wd-70 {
        width:78% !important;
    }
    .table td, .table th {
        font-size: 11px !important;
        font-weight: 600!important;
        line-height: 17px;
        text-align: left;
        word-break: normal;
    }
    #members_policy_enroll tr .form-group{width:150px!important}table tr th{border-top:none!important}.table td,.table th {
                                                                                                          font-weight: 600!important;
                                                                                                      }
    .p-gender {
        padding: 2px 3px;
        background: #f0f7ff;
        border-radius: 10px;
        font-size: 10px;
        margin-top: 3px;
        letter-spacing: 1px;
        border: 2px;
        line-height: 14px;
        margin-bottom: -13%;
        color: #8c8c8c;
    }
    .dropdown-menu {
        background: #F5F5F5 !important;
    }
    .dis-flex {
        display:flex;
    }

</style>
<style>
    .td-text {
        word-break: break-word;
        text-align: left;
    }

    .yes-no-inline {
        display: inline-block;
    }

    .td-wd-30 {
        width: 25px;
    }
    .td-par-wd {
        width: 750px;
        text-align: center;
    }
    .table-ghd {
        font-size: 12px !important;
        font-weight: 600 !important;
    }
    .icon-chevron-down {
        background-position: -313px -119px;
    }
    .icon-chevron-up{
        background-position: -288px -120px;
    }
    @media only screen and (max-width: 600px) {
        .md-img {
            width: 90% !important;
            max-width: 90% !important;
        }
    }
    .md-img {
        width: 50%;
        max-width: 50%;
    }
    .btn-primary {
        background-color: #da8089 !important;
        border-color: #da8089;
    }
    .btn-primary:hover {
        background-color: #da8089;
        border-color: #da8089;
    }
    .ti-eye {
        cursor: pointer;
    }
    #helathproXlFamilyModal {
        position: absolute !important;
    }
    .btn-default {
        background: #e3e3e3;
    }
    .custom-radio .custom-control-input:checked~.custom-control-label::before {
        border: 1px solid #da8089;
    }
    .md-footer {
        padding: 0.75rem;
        border-top: 1px solid #dee2e6;
        border-bottom-right-radius: calc(0.3rem - 1px);
        border-bottom-left-radius: calc(0.3rem - 1px);
        margin-right: 0px;
        margin-left: 0px;
        display: flex;
    }
    .error {
        font-weight: 600;
        letter-spacing: 0.2px;
        font-size: 12px;
        //position: initial;
    }


</style>
<input type="hidden" name ="hidden_lead_id" id="hidden_lead_id" value="<?php echo $_REQUEST['leadid']; ?>">
<input type="hidden" name ="hidden_editstatus" id="hidden_editstatus" value="<?php echo $_REQUEST['editstatus']; ?>">

<!-- upendra - maker/checker - 30-07-2021 -->
<input type="hidden" name="hidden_is_maker_checker" id="hidden_is_maker_checker" value="<?php echo $is_maker_checker; ?>">
<input type="hidden" name="hidden_base_caller_name" id="hidden_base_caller_name" value="<?php echo $_SESSION['telesales_session']['base_caller_name']; ?>">
<input type="hidden" name="hidden_base_caller_id" id="hidden_base_caller_id" value="<?php echo $_SESSION['telesales_session']['base_agent_id']; ?>">
<input type="hidden" name="hidden_base_caller_lob" id="hidden_base_caller_lob" value="<?php echo $_SESSION['telesales_session']['base_caller_lob']; ?>">
<input type="hidden" name="hidden_base_caller_center" id="hidden_base_caller_center" value="<?php echo $_SESSION['telesales_session']['base_caller_location']; ?>">
<input type="hidden" name="hidden_base_caller_vendor" id="hidden_base_caller_vendor" value="<?php echo $_SESSION['telesales_session']['base_caller_vendor']; ?>">

<input type="hidden" name="hidden_base_caller_tl_id" id="hidden_base_caller_tl_id" value="<?php echo $_SESSION['telesales_session']['base_tl_id']; ?>">
<input type="hidden" name="hidden_base_caller_tl_name" id="hidden_base_caller_tl_name" value="<?php echo $_SESSION['telesales_session']['base_tl_name']; ?>">
<input type="hidden" name="hidden_base_caller_imd" id="hidden_base_caller_imd" value="<?php echo $_SESSION['telesales_session']['base_imd_code']; ?>">

<input type="hidden" value="<?php echo $axis_process; ?>" id="saxis_process" name="saxis_process">

<input type="hidden" value="<?php echo $checker_edit; ?>" id="checker_edit" name="checker_edit">
<div class="col-md-10">
    <input type="hidden" id="leadHidden" value="<?php echo $lead_id; ?>">

    <div class="content-section mt-3">

                <div id="accordion12" class="according accordion-s2 mt-3">
                    <div class="card card-member">
                        <div class="card-header card-vif">
                            <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion451" aria-expanded="false"> <span class="lbl-card">Agent Details - <i class="ti-file"></i></a>
                        </div>
                        <input type="hidden" value="0" id="hidden_agent_section">

                        <div id="accordion451" class="collapse card-vis-mar accord-data show" data-parent="#accordion451" style="">
                            <!-- form start -->
                            <form id="agent_details" class="customer_form" method="post" autocomplete="off">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="validationCustomUsername" class="col-form-label">Call Center Process<span class="lbl-star">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" name="axis_process" id="axis_process">
                                                    <option value="<?php echo $center_process; ?>"><?php echo $center_process; ?></option>

                                                </select>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-4 mb-3">
                                            <label for="validationCustomUsername" class="col-form-label">LOB<span class="lbl-star">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" name="axis_lob" id="axis_lob">
                                                    <option value="">Select LOB</option>

                                                    <!--  <option value= <?php echo $value1['axis_lob_id']; ?>> <?php echo $value1['axis_lob'];?> </option>-->
                                                    <!-- upendra - maker/checker - 30-07-2021 -->

                                                    <?php foreach ($lob as $value1) {

                                                        if ($is_maker_checker == "yes"||$checker_edit=='yes') {
                                                            if ($value1['is_maker_checker'] == 1) {
                                                                ?>
                                                                <option value=<?php echo $value1['axis_lob_id']; ?>> <?php echo $value1['axis_lob']; ?> </option>
                                                                <?php
                                                            }
                                                        } else {

                                                            // 03-02-2022 - SVK005 - add if condition
                                                            if($value1['telesales_journey'] == 1){
                                                                ?>
                                                                <option value=<?php echo $value1['axis_lob_id']; ?>> <?php echo $value1['axis_lob']; ?> </option>
                                                                <?php
                                                            }
                                                        }

                                                        ?>

                                                    <?php } ?>
                                                </select>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="validationCustomUsername" class="col-form-label">Base Caller Id<span class="lbl-star">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" value="" id="agent_id" name="agent_id">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="validationCustomUsername" class="col-form-label">Base Caller Name</label>
                                            <div class="input-group">
                                                <input class="form-control" id="agent_name" name="agent_name" type="text" value="">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="validationCustomUsername" class="col-form-label">Axis Location<span class="lbl-star">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control"  style="pointer-events:none" name="axis_location" id="axis_location">
                                                    <option value="">Select Location</option>

                                                </select>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($is_maker_checker != "yes"||$checker_edit=='yes') {
                                            ?>

                                        <div class="col-md-4 mb-3">
                                            <label for="validationCustomUsername" class="col-form-label">AV Code<span class="lbl-star">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control dis-col" type="text" value="" id="avCode" name="avCode" readonly="">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                                </div>

                                            </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="validationCustomUsername" class="col-form-label">AV Name<span class="lbl-star">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control dis-col" type="text" value="" id="avName" name="avName" readonly="">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                                </div>
                                            </div>
                                        </div>

                                            <?php
                                        } ?>
                                    </div>

                                    <div class="row mt-4 form-buttons">
                                        <input type="hidden" name="lead_id" value="<?php echo $customer->lead_id; ?>" />
                                        <input type="hidden" name="trace_id" value="<?php echo $customer->trace_id; ?>" />
                                        <input type="hidden" class="customer_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="customer_id" value="<?php echo $customer->customer_id; ?>" />
                                        <input type="hidden" name="plan_id" value="<?php echo $customer->plan_id; ?>" />
                                        <div class="col-md-1 col-6 text-left">
                                            <button class="btn smt-btn1">Save</button>
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


        <div id="accordion1" class="according accordion-s2 mt-3">
            <div class="card card-member">
                <div class="card-header card-vif">
                    <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion450" aria-expanded="false"> <span class="lbl-card">Customer Details - <i class="ti-file"></i></a>
                </div>
                <div id="accordion450" class="collapse card-vis-mar accord-data show" data-parent="#accordion1" style="">
                    <!-- form start -->
                    <form id="emp_data" class="customer_form" method="post" autocomplete="off">
                        <input type="hidden" value="0" id="hidden_customer_section">

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
                                        <select id="gender1" name="gender1" class="form-control" style="pointer-events: none">
                                            <option value="">Gender</option>
                                            <option value="Male" >MALE</option>
                                            <option value="Female" >FEMALE</option>
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
                                        <input type="email" class="form-control" id="email_id" name="email" value="<?php echo $customer->email_id; ?>" placeholder="Enter email" <?php if ($customer->email_id) : ?> readonly="" <?php endif ?>>
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
                                        <textarea type="text" id="address_line1" name="comAdd" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo (!empty($customer->address_line1)) ? $customer->address_line1 : ''; ?></textarea>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Address Line 2</label>
                                    <div class="input-group">
                                        <textarea type="text" id="address_line2" name="comAdd2" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo (!empty($customer->address_line2)) ? $customer->address_line2 : ''; ?> </textarea>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Address Line 3</label>
                                    <div class="input-group">
                                        <textarea type="text" id="address_line3" name="comAdd3" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo (!empty($customer->address_line3)) ? $customer->address_line3 : ''; ?></textarea>
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
                                    <button class="btn smt-btn1">Save</button>
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
                    <form id="patForm" name="" method="post" >
                        <input type="hidden" name="edit" value="0">
                        <input type="hidden" id="policy_no" name="policy_no" value="">


                        <div class="card-body">
                            <div class="row">

                                <?php if (!isset($coapplicant_tab_id)) : ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomUsername" class="col-form-label">Plan Name<span class="lbl-star">*</span></label>
                                        <div class="input-group">
                                            <select class="form-control" name="plan_name"  id="plan_name">
                                            </select>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3 ded_tbl" style ="display:none">
                                        <label for="validationCustomUsername" class="col-form-label">Deductable <span class="lbl-star">*</span></label>
                                        <div class="input-group">
                                            <select class="form-control" name="deductable" id="deductable">

                                            </select>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">family_restroom</span></span>
                                            </div>
                                        </div>
                                    </div>

                                <?php endif;  ?>
                                <input type ="hidden" name="hidden_deductable" id = "hidden_deductable" value ="">
                                <input type ="hidden" name="hidden_policy_id" id = "hidden_policy_id" value ="">

                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Family Construct <span class="lbl-star">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control" name="familyConstruct" id="patFamilyConstruct" onchange="FamilyConstruct(this);">
                                            <option value="">Select</option>
                                        </select>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">family_restroom</span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row sum_insured_append">

                                </div>

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

                                <div class="col-md-12 mt-5" style="padding:0px;">
                                    <input type="hidden" value="0" id="hidden_policy_section">

                                    <div id="accordion7" class=" card-vis-mar generate-quote-accordian accord-data">
                                        <div class="card" id="add_btn_view" style="display: block;">
                                            <div class="card-header"> <a class="card-link  card-vis collapsed" data-toggle="collapse" href="#accordion33" aria-expanded="true">
                                                    <div class="row"><div class="col-md-10">Member Details</div>
                                                    </div></a>
                                               </div><div id="accordion33" class="collapse" data-parent="#accordion33" style=""> <div class="card-body" style="background:#fff;"> <div id="add_more" style="padding-left: 2%;"></div> </div>  <div class="col-md-1 col-6 text-left">

                                                    <button class="btn smt-btn1" tabindex="30">Save</button>
                                                </div> </div>




                                        </div>   </div> </div>



                            </div>



                    </form>
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
                    <form id="nominee_data" name="nominee_data" autocomplete="off">
                        <input type="hidden" value="0" id="hidden_nominee_section">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Relation with Proposer<span class="lbl-star">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control nominee_relation" name="nominee_relation" id="nominee_relation" onchange="get_fam_data(this);">
                                            <option value="">Select Relation</option>

                                        </select>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">First Name<span class="lbl-star">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control nominee_fname first_name" type="text" value="" maxlength="50" id="nominee_fname" autocomplete="off" name="nominee_fname">

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
                                        <input class="form-control nominee_lname last_name" type="text" maxlength="50" autocomplete="off" value="" id="nominee_lname" name="nominee_lname">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Date of Birth<span class="lbl-star">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="nominee_dob" id="nomineedob" value="" readonly>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Contact No</label>
                                    <div class="input-group">
                                        <input class="form-control nominee_contact" type="text" autocomplete="off" id="nominee_contact" name="nominee_contact" maxlength="10">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">phone_android</span></span>
                                        </div>
                                    </div>
                                    <div><label class="moberror"></label></div>
                                </div>
                            </div>

                            <div class="row mt-4 form-buttons">
                                <input class="form-control family_id" type="hidden" value="" id="family_id" name="family_id">

                                <div class="col-md-1 col-6 text-left">
                                    <button class="btn smt-btn1">Save</button>
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
        <div id="accordion9" class="according accordion-s2 mt-3">
            <div class="card card-member">
                <div class="card-header card-vif">

                    <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion4551" aria-expanded="false"> <span class="lbl-card">Payment Details - <i class="ti-file"></i></span></a>
                </div>
                <div id="accordion4551" class="collapse card-vis-mar accord-data" data-parent="#accordion4" style="">
                    <!-- form for Nominee Details -->
                    <form id="payment_details" name="payment_details" autocomplete="off">
                        <input type="hidden" value="0" id="hidden_payment_section">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Mode Of Payment<span class="lbl-star">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control dis-col" type="text" value="Razorpay" id="mode_payment" autocomplete="off" readonly name="mode_payment">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment</span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">Preferred contact date<span class="lbl-star">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control dobdate ignore" autocomplete="off" type="text" id="dobdate" name="preferred_contact_date" readonly>
                                        <span id="err_family_date_birthArr" class="error"></span>
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
                                    <label for="validationCustomUsername" class="col-form-label">Preferred contact Time<span style="color:#FF0000" class="follow_up_req"></span></label>
                                    <div class="input-group">
                                        <input class="form-control ignore" type="text" value="" id="preferred_contact_time" autocomplete="off"  name="preferred_contact_time" placeholder = "HH:MM">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="col-form-label">DISPOSITION<span style="color:#FF0000">*</span></label>
                                    <div class="input-group">
                                        <select  <?php echo ($selected_disposition['Open/Close'] == 'Close') ? 'disabled' : '' ;?> class="form-control disposition" name="disposition" id="disposition" onchange="get_sub_disposition(this);"><option value="">Select Disposition</option>
                                            <?php $disposition_exists = [];
                                            foreach($disposition as $value){
                                                if(!in_array($value['Dispositions'],$disposition_exists)){
                                                    $disposition_exists[] = $value['Dispositions'];
                                                    /*<option <?php echo (trim($value['Dispositions'] == $selected_disposition['Dispositions'])) ? 'selected' : '';?>
                                                    value= <?php echo $value['id']; ?>><?php echo trim($value['Dispositions']); ?></option>
                                                    <?php }} ?>*/
                                                    ?>

                                                    <?php if($selected_disposition['Open/Close'] == 'Close'){?>
                                                        <option <?php echo (trim($value['Dispositions'] == $selected_disposition['Dispositions'])) ? 'selected' : '';?>
                                                                value= <?php echo $value['id']; ?>><?php echo trim($value['Dispositions']); ?></option>
                                                    <?php } else{?>
                                                        <option
                                                                value= <?php echo $value['id']; ?>><?php echo trim($value['Dispositions']); ?></option>
                                                    <?php }}}?>



                                        </select>                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername" class="col-form-label">SUB-DISPOSITION</label>
                                    <div class="input-group">
                                        <select class="form-control sub_isposition" name="sub_isposition" id="sub_isposition" onchange="enable_disable_proposal(this);" disabled><option value="">Select Sub Disposition</option>
                                            <?php
                                            /*
                                            <?php echo (trim($value['Sub-dispositions'] == $selected_disposition['Sub-dispositions'])) ? 'selected' : '' ?> */
                                            foreach($disposition as $value){?>
                                                <?php if($selected_disposition['Open/Close'] == 'Close'){?>
                                                    <option
                                                        <?php
                                                        echo (trim($value['Sub-dispositions'] == $selected_disposition['Sub-dispositions'])) ? 'selected' : '' ?>
                                                            name ='show_hide_sub_dispositions'

                                                            class ='<?php echo str_replace(" ","_",$value['Dispositions']); ?>' value= <?php echo $value['id']; ?>>

                                                        <?php echo trim($value['Sub-dispositions']); ?></option>
                                                <?php }else{ ?>

                                                    <option  name ='show_hide_sub_dispositions' class ='<?php echo str_replace(" ","_",$value['Dispositions']); ?>' value= <?php echo $value['id']; ?>>

                                                        <?php echo trim($value['Sub-dispositions']);
                                                        ?>
                                                    </option>
                                                    <?php
                                                }}

                                            ?>

                                        </select>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">phone_android</span></span>
                                        </div>
                                    </div>
                                    <div><label class="moberror"></label></div>

                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="button" style="height: 45px;margin-top: 29px;margin-left: 24px;" class="btn btn-primary" data-toggle="modal" data-target="#paymentdetailsmodal">Audit</button>
                                </div>
                            </div>

                            <div class="row mt-4 form-buttons">
                                <input class="form-control family_id" type="hidden" value="" id="family_id" name="family_id">

                                <div class="col-md-1 col-6 text-left">
                                    <button class="btn smt-btn1">Save</button>
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

        <div id ='show_hide_proposal' style ='display:none'>
            <form id="proposal_data" name="proposal_data" autocomplete="off">
                <div class="col-lg-12 mt-3 pad-0">


                    <div id="accordion9" class="according accordion-s2 mt-3">
                        <div class="card card-member">
                            <div class="card-header card-vif">

                                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion4551" aria-expanded="false"> <span class="lbl-card">Employee Declaration  <i class="ti-file"></i></span></a>
                            </div>
                    <div class="single-table mt-4">

                        <div class="table-responsive" id="policy_declare">

                        </div>

                    </div>
                        </div></div>


                    <div class="row" id="finalConfirmDiv" style="">
                        <div class="col-md-12" style="text-align:center;">


                            <?php
                            if ($checker_sendbackto_do == "yes") {
                                ?>

                                <input type="hidden" name="updateadddispostion" id="updateadddispostion" value="1">
                                <input type="hidden" name="updateaddagentname" id="updateaddagentname" value="<?php echo $agent['agent_name'];?>">
                                <!--<button id="pre_send" type="button" class="btn sub-btn mt-4 pr-4 pl-4 mb-3 mr-2">Send back to DO</button>

                                -->
                                <?php
                            }
                            ?>


                            <!--
<?php
                            if ($is_maker_checker == "yes"||$checker_edit=='yes') {
                                ?>
                            <button type="submit"  id="confr1" class="btn sub-btn mt-3">Proceed For Renewal</button>
<?php
                            }else{
                                ?>
                            <button type="submit"  id="confr1" class="btn sub-btn mt-3">Proceed To Submit.</button>
<?php
                            }
                            ?>
-->


                            <button type="submit"  id="confr1" class="btn sub-btn mt-3">Proceed To Submit.</button>

                        </div>
                    </div>






            </form>
        </div>
    </div>
<div class="modal fade" id="helathFamilyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-premium1">
        <div class="modal-content">
            <div class="modal-header header-title title header-tl-xd product_name">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelhealth"></h4>
            </div>
            <p class="mb-2 col-md-12">Please fill below details.</p>
            <form name="famConSelected" id = "famConSelected1" action="#" method="POST">
                <div class="modal-body" id="helathproFamily">
                    <div class="mb-2 col-md-12 row check_adult">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>is this policy for?</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group adult_data">

                            </div>
                        </div>
                    </div>
                    <div class ="adult_row">

                    </div>
                    <div class ="kids_row">

                    </div>
                    <input type="hidden" name="hiddenpolicyarr" id="hiddenpolicyarr" value="">

                </div>
                <div class="modal-footer text-center">
                    <div class="col-md-12 text-center">
                        <button class="btn sub-btn validateFamCon" type="submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    var payment_details_submit = true;
    var get_premium = '';
    var total_premium = 0;
    var apply_onload = 0;
    var proposal_created = 0;
    var show_popup = 0;
    var show_popup_new = 0;
    var famconsCount =0;
    var memberCount = 0;
    var deletecalled = 0;
    var proposal_payment_done = 0;
    var TELE_HEALTHPROINFINITY_GHI = '473';
    var TELE_HEALTHPROINFINITY_GPA = '474';
    var TELE_HEALTHPROINFINITY_GHI_GPA = '473,474';
    var TELE_HEALTHPROINFINITY_GHI_ST = '475';
    var TELE_HEALTHPROINFINITY_PRODUCT_NAME = 'Tele - Health Pro Infinity';
    var membersCovered = [];
    var is_self_purchased = 0;
    var self_purcased_with_si = 0;



    function annual_income_hide_show(sum_insured)
    {
//debugger;
        if($('#plan_name').val() == 'T03'){
            if($('#hidden_policy_id').val() == TELE_HEALTHPROINFINITY_GHI_GPA){
                $("#annual_income_label").show();
                var sumInsuresValue = $("#sum_insures :selected").val();
                var isSumAssuredMoreThan = 1000000;
                if(sumInsuresValue > isSumAssuredMoreThan)
                {
                    $("#annual_income_label").show();
                    $("#occupation_label").show();
                    $("#occupation").removeClass('ignore');
                    $("#annual_income").removeClass('ignore');
                }
                else
                {
                    $("#annual_income_label").hide();
                    $("#annual_income").addClass('ignore');
                    $("#occupation_label").addClass('ignore');
                    $("#occupation_label").hide();
                }
                //dedupe
                if(is_self_purchased == 1){
                    $("#annual_income_label").show();
                    $("#annual_income").addClass('ignore');
                    $("#occupation").addClass('ignore');
                    $("#occupation_label").show();
                }

            }else{
                $("#annual_income_label").hide();
                $("#annual_income").addClass('ignore');
                $("#occupation_label").hide();
                $("#occupation").addClass('ignore');
            }
        }else{
            var sumInsuresValue = $("#sum_insures :selected").val();
            var isSumAssuredMoreThan = 1000000;
            if(sumInsuresValue > isSumAssuredMoreThan)
            {
                $("#annual_income_label").show();
                $("#occupation_label").show();
                //$("#annual_income").removeClass('ignore');
                $("#occupation").removeClass('ignore');
            }
            else
            {
                $("#annual_income_label").hide();
                $("#occupation_label").hide();
                $("#annual_income").addClass('ignore');
                $("#occupation").addClass('ignore');
                //$("#occupation").hide();
            }
        }


        /*if(annual_income_data != '' && $("#patForm").find("table tbody").children().length != 0)
        {
            //$("#annual_income").attr('readonly','readonly');
        }
        else
        {
            $("#annual_income").val('');
        }*/

    }
    /*$("select[name='familyConstruct']").on("change", function ()
    {alert();
        debugger;
        var plan_name = $("#plan_name").val();
        //alert(plan_name);
        //upendra - 29-06-2021

            if($(this).val()){
                var sumInsuresType = $("#sum_insures :selected").attr("data-type");
                if($('.commonCls').length > 0){
                    apply_button();
                    console.log('---->'+deletecalled);
                    if(deletecalled == 0){
                        if(famconsCount >= memberCount){
                            // swal("Alert", "Below Member Details Section would get modified or deleted. Press OK to Proceed", "warning");

                        }else{
                            famconsCount++;
                        }
                    }else{
                        deletecalled = 0;
                    }


                }

                if (sumInsuresType == "memberAge") {
                    //getPremiumByAge("patFamilyConstruct");
                    //	getRelation();
                }
                var family_construct = $('#patFamilyConstruct option:selected').val();
                var result = family_construct.split('+');

                var add_length = [];
                add_length.push(parseInt(result[0]));
                add_length.push(parseInt(result[1]));

                if(!$('#patFamilyConstruct').is('[disabled=disabled]')){
                    // swal("Alert","Below Member Details Section would get modified or deleted. Press OK to Proceed.");
                    var boxlen = $('.commonCls').length;
                    var icount;
                    if(boxlen == ''){
                        icount = 0
                    }else{
                        icount = 0+boxlen;
                    }
                    //$("#add_more").empty();


                    for (i = icount; i < sum(add_length); i++) {
                        common_append(i);
                    }

                    $("#add_btn_view").show();

                }


            }



        getRelation();


    });*/

    function getRelation(){
        //debugger;
        var fam_rel = $('select[name=familyConstruct]').val().split("+");
        var opt = $(".family_members_id1 option");
        var tempArr = [];
        if($('select[name=familyConstruct]').val() != '1A+1K' || $('select[name=familyConstruct]').val() != '1A+2K'){
            opt.each(function(e) {
                $(this).css("display", "block");
                //remove duplicate options @nkita
                $(this).siblings('[value="'+ this.value +'"]').remove();
                if(this.value != ''){
                    tempArr.push(this.value);
                }

            });
            /*kids & self*/
            if(fam_rel[0].substr(0,1) == '1')
            {
                opt.each(function(e) {
                    if(this.value == 0 || this.value == 1) {
                        $(this).removeAttr("disabled").show();
                    }else {
                        $(this).attr('disabled', 'disabled').hide();
                    }
                });
            }
            if(fam_rel[0].substr(0,1) > '1')
            {
                opt.each(function(e) {
                    if(this.value != 2 && this.value != 3) {
                        $(this).removeAttr("disabled").show();
                    }else {
                        $(this).attr('disabled', 'disabled').hide();
                    }
                });
            }
            if(fam_rel[1] && fam_rel[1].substr(0,1) >= 1) {
                opt.each(function(e) {
                    if(this.value == 2 || this.value == 3) {
                        $(this).removeAttr("disabled").show();
                    }
                });
            }
        }

        //upendra - 29-06-2021
        if($('#plan_name').val() == 'T03' || $('#plan_name').val() == 'T01'){
            if(membersCovered.length !== 0){
                var UniqueTempArr = unique(tempArr);
                //alert(findCommonElements3(UniqueTempArr,membersCovered));
                opt.each(function(e) {

                    if(this.value != ''){

                        //if($.inArray(this.value, membersCovered) !== -1){
                        if(membersCovered.toString().indexOf(this.value) == -1){
                            $(this).css("display", "none");
                        }else{
                            $(this).css("display", "block");
                        }

                        $(this).siblings('[value="'+ this.value +'"]').remove();
                    }

                });

            }
        }

    }

    function apply_button() {
        //console.log("show poup => "+show_popup);
        if(show_popup == 0){
            //if(parent_id == 'test123'){
            var edit = $("#patFamilyConstruct").closest("form").find("input[name='edit']").val();
            /*if(edit !=0)
                {
                    return;
                }*/
            //if($("#patTable tr").length > 0) {
            var GCI_optional;
            var sumInsuresValue = $("#sum_insures :selected").val();
            var annualIncome = $("#annual_income").val();
            var patFamilyConstruct = $("#patFamilyConstruct").val();
            var sum_annual;


            if(sumInsuresValue == '')
            {
                swal("Alert", "Please select Sum Insure");
                $("#patFamilyConstruct").val('');
                return;
            }
            if(sumInsuresValue >= 1500000){
                if(annualIncome != '' && annualIncome != 0)
                {sum_annual = annual_income_logic();if(sum_annual == false){return;} }else{
                    /*swal("Alert", "please select annual Income"); */ return;
                }}else{annual_income_hide_show();}
            /*if(patFamilyConstruct == '' )
                {
                    swal("Alert", "Please select Family Construct");
                    return;
                }*/
            //insert_annual_income(emp_id,annualIncome,'insert');
            $.ajax({
                url: "<?php echo base_url(); ?>teleproposal/apply_changes",
                type: "POST",
                data: {
                    'product_id':$('#plan_name').val(),
                    'family_construct':$('select[name=familyConstruct]').val(),
                    'sum_insured':$('select[name=sum_insure]').val(),
                    'gci' : $('input[name=GCI_optional]:checked').val(),

                },
                async: false,
                dataType: "json",
                success: function (response) {

                    // alert(apply_onload);
                    if(apply_onload > 1)
                    {
                        if(response.message){
                            swal("Alert",response.message);
                        }
                    }
                    else
                    {apply_onload++;}

                    //addDependentForm("getTable",response);


                }
            });
            //}
            //}
        }
    }
    var apply_onload = 0;

    function common_append(j,e =''){
        //console.log("---- j = "+j+"---"+e);
        //debugger;
        var is_maker_checker=$('#checker_edit').val();

        //marked as display none for other products
        var new_fileds = '<div class="col-md-3" style="display:none;"><div class="form-group"><label for="example-text-input" class="col-form-label">Email ID <span style="color:#FF0000">*</span></label><input class="form-control mem_email_id sahil ignore mem_email_id1" type="text" value="" id="mem_email_id'+j+'" name="mem_email_id[]" autocomplete="off" maxlength="50"></div></div><div class="col-md-3" style="display:none;"><div class="form-group"><label for="example-text-input" class="col-form-label">Mobile No <span style="color:#FF0000">*</span></label><input class="form-control mem_mob_no1 sahil ignore" type="text" maxlength="10" value="" id="mem_mob_no'+j+'" name="mem_mob_no[]" autocomplete="off" maxlength="50"></div></div>';
        //adding 2 new fileds for GHI with EW
        if($('#plan_name').val() == 'T03'){
            //alert($('#hidden_policy_id').val());
            //if($('#hidden_policy_id').val() != TELE_HEALTHPROINFINITY_GHI_ST){
            var new_fileds = '<div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">Email ID <span style="color:#FF0000">*</span></label><input class="form-control mem_email_id sahil mem_email_id1" type="text" value="" id="mem_email_id'+j+'" name="mem_email_id[]" autocomplete="off" maxlength="50" required></div></div><div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">Mobile No <span style="color:#FF0000">*</span></label><input class="form-control mem_mob_no1 sahil" maxlength="10" type="text" value="" id="mem_mob_no'+j+'" name="mem_mob_no[]" autocomplete="off" maxlength="50" required></div></div>';
            //}
        }
        /*		console.log(new_fileds);
            var add_div_new = '<div class="row mt-2 mb-2 divmem'+j+'"><div class="col-md-10">Member '+(j+1)+'</div><div class="col-md-2 text-center"><button class="btn sub-btn hide_proposal" style="margin-right: 4px; background: #FB8C00 !important;border: none;padding: 5px 15px; display:none;" id ="edit_btn'+j+'" type="button" onclick="editMember('+j+')">Edit</button><button class="btn sub-btn hide_proposal del_btn_member" style="background: #E53935 !important;border: none;padding: 5px 15px;" id ="delete_btn'+j+'" type="button" data-emp-id=' + e.emp_id + ' data-policy-member-id=' + e.policy_member_id + ' onclick="deleteMember('+j+')">Clear</button></div></div>';
    */


        var is_delete='';
        if(is_maker_checker=='no'){
            is_delete='<button class="btn sub-btn hide_proposal del_btn_member" style="background: #E53935 !important;border: none;padding: 5px 15px;" id ="delete_btn'+j+'" type="button" data-emp-id=' + e.emp_id + ' data-policy-member-id=' + e.policy_member_id + ' onclick="deleteMember('+j+')">Clear</button>';
        }

        // console.log(new_fileds);
        var add_div_new = '<div class="row mt-2 mb-2 divmem'+j+'"><div class="col-md-10">Member '+(j+1)+'</div><div class="col-md-2 text-center"><button class="btn sub-btn hide_proposal" style="margin-right: 4px; background: #FB8C00 !important;border: none;padding: 5px 15px; display:none;" id ="edit_btn'+j+'" type="button" onclick="editMember('+j+')">Edit</button>'+is_delete+'</div></div>';



        var add_div = '<div class="col-md-3"><div class="form-group"><label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label><select class="form-control family_members_id1" id ="family_members_id'+j+'" name="family_members_id[]" onchange="changes(this);"><option value="" >Select</option></select></div></div><div class="col-md-2"><div class="form-group"><label class="col-form-label">Gender<span style="color:#FF0000">*</span></label><input class="form-control family_gender dis-col" type="text" id ="family_gender'+j+'" name="family_gender[]" id="family_gender1" readonly><p class="p-gender">Auto selected basis salutation value opted.</p></div></div><div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">First Name <span style="color:#ff0000">*</span></label><input class="form-control first_name commonCls sahil" type="text" value="" id ="first_name'+j+'" name="first_name[]" autocomplete="off" maxlength = "50" ></div></div><div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">Last Name </label><input class="form-control last_name" type="text" value="" id ="last_name'+j+'" name="last_name[]" maxlength = "50" autocomplete="off" ><span id="err_last_nameArr" class="error"></span></div></div><div class="col-md-3"><div class="form-group"><label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label><input class="form-control family_date_birth" autocomplete="off" type="text" id ="family_date_birth'+j+'" name="family_date_birth[]" readonly="readonly"><span id="err_family_date_birthArr" class="error"></span></div></div><div class="col-md-3" style="display: block" ><div class="form-group"><label for="example-text-input" class="col-form-label">Age (days/years)<span style="color:#FF0000">*</span></label><input class="form-control dis-col age1" type="text" id ="age'+j+'" name="age[]" readonly=""></div></div>'+new_fileds+'<div class="col-md-3" style="display: none"><div class="form-group"><label for="example-text-input" class="col-form-label">Age Type (Year(s)/Day(s)) <span style="color:#FF0000">*</span></label><input type="text" class="form-control age_type1" id ="age_type'+j+'" name="age_type[]" readonly=""></div></div><div class="col-md-12 disease'+j+'" style="display: block"><div class="member_declare_benifit_4s'+j+' col-md-12"></div><div class="tt quest_declare_benifit_4s'+j+' col-md-12"></div><input type="hidden" name="edit_member_id[]" value=' + e.policy_member_id + '></div>';

        var div = $("<div />");
        var div_new = $("<div />");
        div_new.html("<div>"+add_div_new);
        div.html('<div class="col-md-12 bor-rr row mb-2 divmem'+j+' ">'+add_div);

        $("#add_more").append(div_new);
        $("#add_more").append(div);

        selectChange = $("#family_members_id"+j).closest("form");
        var sumInsuresType = selectChange.find('select[name=sum_insure] :selected').attr("data-type");
        var family_construct = selectChange.find('select[name=familyConstruct]');



        $(".family_date_birth").datepicker({
            dateFormat: "dd-mm-yy",
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100Y:+1D",
            maxDate: "-1D",
            minDate: "-56Y +1D",
            onSelect: function (dateText, inst)
            {
                $(this).val(dateText);
                var selectChange=$(this).closest(".row");
                get_age(dateText,selectChange);
            }
        });
        // //debugger;



        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/get_all_policy_data",
            type: "POST",
            data:
                {},
            async: false,
            dataType: "json",
            success: function (response)
            {
                //debugger;

                $("#benifit_4s").css("display", "block");
                var i;
                for (i = 0; i < response.length; i++)
                {
                    //console.log(response[i].product_name+'---'+response[i].master_policy_no);
                    $("#hidden_policy_section").val(1);
                    $("#pr_name").html(response[i].product_name);
                    $("#pr_name").html('Axis Tele Inbound Affinity Portal for ABHI');
                    //$("#plan_name").val(response[i].product_code);
                    $("#master_policy_number").val(response[i].master_policy_no);
                    if (response[i].policy_sub_type_id == 1)
                    {

                        $(".personal_accident").text(response[i].policy_sub_type_name);
                        $("#benifit_4s").css("display", "block");

                        $("#subtype_text").val(response[i].policy_sub_type_id);
                        ////debugger;
                        //console.log(response[i].max_adult+"===="+response[i].fr_id);

                        if ((response[i].max_adult > 1 && response[i].fr_id == 1) ||response[i].fr_id == 0)
                        {	//console.log(response[i].fr_id)
                            $("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="'+response[i].fr_id +'">' +response[i].fr_name +"</option>");
                        }
                        else if (response[i].max_adult == 2 &&response[i].max_child == 0 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7))
                        {
                            $("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
                        }
                        else if (response[i].max_adult == 4 &&response[i].max_child == 0 &&response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7)
                        {
                            $("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
                        }
                        else if(response[i].max_adult > 2 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7 ||response[i].fr_id == 1 ||response[i].fr_id == 0))
                        {
                            $("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
                        }
                        else if (response[i].fr_id == 2 || response[i].fr_id == 3)
                        {
                            $("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
                        }
                    }
                    //console.log($("#family_members_id"+j).html());
                }
            }


        });

        if (sumInsuresType == "family_construct")
        {
            ////debugger;
            getPremium(selectChange);
            if(get_premium != ''){
                $('#premiumModalBody').html(get_premium);

            }

            getRelation();

        } else if (sumInsuresType == "family_construct_age")
        {
            if($('#plan_name').val() != 'T03'){
                getPremiumByAge(family_construct);
            }

        }
        //console.log($('#plan_name').val())
        if($('#plan_name').val() == 'R06'){
            $.ajax({
                url: "<?php echo base_url(); ?>teleproposal/member_declare_data",
                type: "POST",
                dataType: "html",
                data: {member_id:j},
                success: function (response)
                {
                    //console.log(response);
                    $(".member_declare_benifit_4s"+j).html(response)

                }
            });
        }

        if($('#plan_name').val() == 'T03'){
            getRelation();
        }



    }
    function getgender(e) {
        eValue = e.value;
        var family_gender = $(e)
            .closest(".row")
            .find("#family_gender");
        if (eValue == 2 || eValue == 4 || eValue == 7) {
            family_gender.empty();
            family_gender.append('<option value="Male">Male</option>');
        }
        if (eValue == 3 || eValue == 5 || eValue == 6) {
            family_gender.empty();
            family_gender.append('<option value="Female">Female</option>');
        }

        if (eValue == 1) {
            family_gender.empty();
            family_gender.append('<option value="Male">Male</option>');
            family_gender.append('<option value="Female">Female</option>');
            family_gender.append('<option value="Transgender">Transgender</option>');
        }
    }


    $.validator.addMethod("notEqual", function(value, element, param) {
       // alert(value);

        if(value != 2 && value != 3){
            return this.optional(element) || $(param).not(element).get().every(function(item) {
                return $(item).val() != value;
            });
        }else{
        //    alert();
            return true;
        }
    }, "Please select different member");
    function changes(e) {
        debugger;

        selectChange = $(e).closest(".row");
        var mem_id = [];
        getgender(e);
        var parent_id = $(e).val();


        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/get_family_details_from_relationship",
            type: "POST",
            data: {
                relation_id: $(e).val(),
            },
            async: false,
            dataType: "json",
            success: function (response) {
                var dataOpt = selectChange.find("[name='family_members_id[]'] :selected").attr("data-opt");
                selectChange.find("input[name='first_name[]']").val("");
                selectChange.find("input[name='last_name[]']").val("");
                selectChange.find("input[name='family_id[]']").val("");
                selectChange.find("input[type='text'][name='family_date_birth[]']").val("");
                selectChange.find('input[name="age[]"]').val("");
                selectChange.find("input[name='age_type[]']").val("");
                selectChange.find("select[name='family_gender[]']")
                selectChange.find("div .disable").attr("style", "display:none");
                selectChange.find("div .unmarried").attr("style", "display:none");
                var family_detail = response.family_data;
                if (true) {
                    var genders = $("#gender1").val();
                  //  alert(genders);
                    if (family_detail.length != 0) {
                        if (family_detail[0].fr_id == "0") {

                            selectChange.find("input[name='first_name[]']").css("pointer-events","none");
                            selectChange.find("input[name='last_name[]']").css("pointer-events","none");
                            selectChange.find("input[name='family_date_birth[]']").css("pointer-events","none");

                            selectChange.find("input[name='first_name[]']").val(family_detail[0].emp_firstname);
                            selectChange.find("input[name='last_name[]']").val(family_detail[0].emp_lastname);
                            selectChange.find("input[name='family_id[]']").val(family_detail[0].family_id);
                            selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[0].bdate);
                            selectChange.find("input[name='marriage_date[]']").val(family_detail[0].marriage_date);
                            if($("#gender1").val()==''){
                                selectChange.find('[name="family_gender[]"]').val("");
                            }
                            else if(genders!=family_detail[0].gender){
                                selectChange.find("[name='family_gender[]']").val(genders);
                            }
                            else
                            {
                                selectChange.find('[name="family_gender[]"]').val(family_detail[0].gender);

                            }
                            get_age(family_detail[0].bdate,selectChange);
                        }
                        else {
                            $(e).closest("form").find("input[name='first_name[]']").css("pointer-events","auto");
                            $(e).closest("form").find("input[name='last_name[]']").css("pointer-events","auto");
                            $(e).closest("form").find("input[name='family_date_birth[]']").css("pointer-events","auto");
                            var gen = selectChange.find("select[name='family_members_id[]'] :selected").html();
                            var dataOpt = selectChange.find("select[name='family_members_id[]'] :selected").attr("data-opt");
                            if (gen == 'Spouse' || gen == 'Spouse/Partner') {
                                if ($("#gender1").val() == 'Male') {
                                    selectChange.find('[name="family_gender[]"]').val("Female");
                                    selectChange.find("select[name='family_salutation[]']").val("Mrs");
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                }
                                else {
                                    selectChange.find('[name="family_gender[]"]').val("Male");
                                    selectChange.find("select[name='family_salutation[]']").val("Mr");
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                }
                                selectChange.find("input[name='first_name[]']").val(family_detail[0].policy_member_first_name);
                                selectChange.find("input[name='last_name[]']").val(family_detail[0].policy_member_last_name);
                                selectChange.find("input[name='family_id[]']").val(family_detail[0].family_id);
                                selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[0].policy_mem_dob);
                                selectChange.find("input[name='marriage_date[]']").val(family_detail[0].marriage_date);
                                get_age(family_detail[0].policy_mem_dob,selectChange);
                            }
                            else {
                                selectChange.find('[name="family_gender[]"]').val(dataOpt);
selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[0].policy_mem_dob);

get_age(family_detail[0].policy_mem_dob,selectChange);

                            }

                        }
                    }
                    else {
                        $(e).closest("form").find("input[name='first_name[]']").css("pointer-events","auto");
                        $(e).closest("form").find("input[name='last_name[]']").css("pointer-events","auto");
                        $(e).closest("form").find("input[name='family_date_birth[]']").css("pointer-events","auto");
                        var gen = selectChange.find("select[name='family_members_id[]'] :selected").html();
                        var dataOpt = selectChange.find("select[name='family_members_id'] :selected").attr("data-opt");
                        if (gen == 'Spouse') {
                            if ($("#gender1").val() == 'Male') {
                                selectChange.find('[name="family_gender[]"]').val("Female");
                                selectChange.find("select[name='family_salutation[]']").val("Mrs");
                                selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                            }
                            else {
                                selectChange.find('[name="family_gender[]"]').val("Male");
                                selectChange.find("select[name='family_salutation[]']").val("Mr");
                                selectChange.find("select[name='family_salutation[]']").prop('disabled',true);                            }
                        }
                        else {
                            selectChange.find('[name="family_gender[]"]').val(" ");
                            selectChange.find("[name='family_salutation[]']").val("");
                            selectChange.find("select[name='family_salutation[]']").prop('disabled',false);                        }

                    }
                }
            }
        });
    }


    function changes1(e)
    {
        selectChange = $(e).closest(".row");

        set_non_editable(selectChange,1);
        //alert($('#plan_name').val());
        //upendra - 29-06-2021
        if($('#plan_name').val() == 'T03' || $('#plan_name').val() == 'T01'){
            var valueArray = $('.family_members_id1').map(function() {
                if(this.value != 0 && this.value != 1){
                    return this.value;
                }

            }).get();
            //alert(valueArray);
            $.ajax
            ({
                url: "<?php echo base_url(); ?>teleproposal/get_family_details_from_relationship_healthpro_xl",
                type: "POST",
                data: {relation_id: $(e).val(),selectedRelation:valueArray},
                dataType: "json",
                beforeSend: function() {
                   // set_session();
                },
                success: function (response)
                {

                    var dataOpt = selectChange.find("[name='family_members_id[]'] :selected").attr("data-opt");

                    selectChange.find("input[name='first_name[]']").val("");
                    selectChange.find("input[name='middle_name[]']").val("");
                    selectChange.find("input[name='last_name[]']").val("");
                    selectChange.find("input[name='family_id']").val("");
                    selectChange.find("input[type='text'][name='family_date_birth[]']").val("");
                    selectChange.find("input[name='age[]']").val("");
                    selectChange.find("input[name='age_type[]']").val("");
                    selectChange.find("input[name='family_gender[]']").val("");
                    selectChange.find("[name='family_salutation[]']").val("");
                    selectChange.find("input[name='mem_email_id[]']").val("");
                    selectChange.find("input[name='mem_mob_no[]']").val("");
                    var set_blank_variable =  selectChange.find("[name='family_members_id[]'] :selected").val();
                    set_blank(set_blank_variable);

                    var family_detail = response.family_data;
                    var i ;
                    if (true)
                    {debugger;

                        if(family_detail.length != 0)
                        {
                            for(i = 0; i < family_detail.length; i++)
                            {
                                if (family_detail[i].fr_id == "0")
                                {
                                    set_blank(set_blank_variable);
                                    selectChange.find("input[name='first_name[]']").val(family_detail[i].emp_firstname.toUpperCase());
                                    selectChange.find("input[name='last_name[]']").val(family_detail[i].emp_lastname.toUpperCase());
                                    selectChange.find("input[name='middle_name[]']").val(family_detail[i].emp_middlename);
                                    selectChange.find("input[name='family_id']").val(family_detail[i].family_id);
                                    selectChange.find("input[name='family_gender[]']").val(family_detail[i].gender);
                                    selectChange.find("select[name='family_salutation[]']").val(family_detail[i].salutation);
                                    selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].bdate);
                                    selectChange.find("input[name='mem_email_id[]']").val(family_detail[i].email);
                                    selectChange.find("input[name='mem_mob_no[]']").val(family_detail[i].mob_no);
                                    get_age(family_detail[i].bdate,selectChange);
                                    set_blank(set_blank_variable);
                                    set_non_editable(selectChange,0);
                                    //if($('#hidden_policy_id').val() != TELE_HEALTHPROINFINITY_GHI_ST){
                                    if($('#plan_name').val() == 'T03'){
                                        selectChange.find("input[name='mem_email_id[]']").parent().show();
                                        selectChange.find("input[name='mem_email_id[]']").attr('required', true);
                                        selectChange.find("input[name='mem_mob_no[]']").attr('required', true);
                                        selectChange.find("input[name='mem_mob_no[]']").parent().show();
                                    }
                                }
                                else
                                {
                                    var gen = selectChange.find("[name='family_members_id[]'] :selected").html();
                                    if(gen == 'Spouse')
                                    {
                                        set_non_editable(selectChange,1);
                                        if($("#gender1").val() == 'Male')
                                        {

                                            selectChange.find('[name="family_gender[]"]').val("Female");
                                            selectChange.find("select[name='family_salutation[]']").val("Mrs");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',true);

                                        }
                                        else if($("#gender1").val() == 'Female')
                                        {
                                            selectChange.find('[name="family_gender[]"]').val("Male");
                                            selectChange.find("select[name='family_salutation[]']").val("Mr");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                        }
                                        else
                                        {
                                            selectChange.find('[name="family_gender[]"]').val(" ");
                                            selectChange.find("[name='family_salutation[]']").val("");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
                                        }

                                        if(family_detail[i].policy_member_first_name){
                                            selectChange.find("input[name='first_name[]']").val(family_detail[i].policy_member_first_name.toUpperCase());
                                        }
                                        if(family_detail[i].policy_member_middle_name){
                                            selectChange.find("input[name='middle_name[]']").val(family_detail[i].policy_member_middle_name.toUpperCase());
                                        }
                                        if(family_detail[i].policy_member_last_name){
                                            selectChange.find("input[name='last_name[]']").val(family_detail[i].policy_member_last_name.toUpperCase());
                                        }
                                        if(family_detail[i].policy_member_email_id){
                                            selectChange.find("input[name='mem_email_id[]']").val(family_detail[i].policy_member_email_id);
                                        }
                                        if(family_detail[i].policy_member_mob_no){
                                            selectChange.find("input[name='mem_mob_no[]']").val(family_detail[i].policy_member_mob_no);
                                        }

                                        selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
                                        selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);
                                        selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
                                        selectChange.find("input[type='text'][name='age[]']").val(family_detail[i].age);

                                        selectChange.find("input[type='text'][name='age_type[]']").val(family_detail[i].age_type);

                                        get_age(family_detail[i].policy_mem_dob,selectChange);
                                        //if($('#hidden_policy_id').val() != TELE_HEALTHPROINFINITY_GHI_ST){
                                        if($('#plan_name').val() == 'T03'){
                                            selectChange.find("input[name='mem_email_id[]']").parent().show();
                                            selectChange.find("input[name='mem_email_id[]']").attr('required', true);
                                            selectChange.find("input[name='mem_mob_no[]']").attr('required', true);
                                            selectChange.find("input[name='mem_mob_no[]']").parent().show();
                                        }
                                    }
                                    else
                                    {debugger;
                                        //alert("g");
                                        /*selectChange.find("input[name='first_name']").val(family_detail[i].policy_member_first_name);
                                                selectChange.find("input[name='middle_name']").val(family_detail[i].policy_member_middle_name);
                                                selectChange.find("input[name='last_name']").val(family_detail[i].policy_member_last_name);
                                                selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
                                                selectChange.find("input[type='text'][name='family_date_birth']").val(family_detail[i].policy_mem_dob);
                                                selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
                                                selectChange.find("input[type='text'][name='age']").val(family_detail[i].age);
                                                selectChange.find("input[name='family_gender']").val(family_detail[i].policy_mem_gender)
                                                selectChange.find("input[type='text'][name='age_type']").val(family_detail[i].age_type);

                                                get_age_family(family_detail[i].bdate,selectChange);*/

                                        if(gen != 'Spouse' || gen!='Self')
                                        {
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
                                            selectChange.find('[name="family_gender[]"]').val(dataOpt);

                                            if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
                                            {
                                                salutation_hide_show(selectChange,'Male');
                                            }
                                            else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
                                            {
                                                salutation_hide_show(selectChange,'Female');

                                            }
                                            var gen_id = selectChange.find("[name='family_members_id[]'] :selected").val();
                                            if(gen_id == '2' || gen_id == '3')
                                            {

                                                if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
                                                {

                                                    selectChange.find("select[name='family_salutation[]']").val("Master");
                                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                                }
                                                else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
                                                {
                                                    selectChange.find("select[name='family_salutation[]']").val("Ms");
                                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                                }

                                            }

                                            //helathpro change
                                            selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);
                                            get_age(family_detail[i].policy_mem_dob,selectChange);

                                            //remove email and mobile input filed for kids
                                            //debugger;

                                            selectChange.find("input[name='mem_email_id[]']").parent().hide();
                                            selectChange.find("input[name='mem_email_id[]']").attr('required', false);
                                            selectChange.find("input[name='mem_mob_no[]']").attr('required', false);
                                            selectChange.find("input[name='mem_mob_no[]']").parent().hide();




                                        }
                                    }
                                }
                            }
                        }
                        else
                        {

                            var gen = selectChange.find("[name='family_members_id[]'] :selected").html();
                            var gen_id = selectChange.find("[name='family_members_id[]'] :selected").val();
                            if(gen == 'Spouse')
                            {
                                set_non_editable(selectChange,1);
                                if($("#gender1").val() == 'Male')
                                {

                                    selectChange.find('[name="family_gender[]"]').val("Female");
                                    selectChange.find("select[name='family_salutation[]']").val("Mrs");
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                }
                                else if($("#gender1").val() == 'Female')
                                {
                                    selectChange.find('[name="family_gender[]"]').val("Male");
                                    selectChange.find("select[name='family_salutation[]']").val("Mr");
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                }
                                else
                                {
                                    selectChange.find('[name="family_gender[]"]').val(" ");
                                    selectChange.find("[name='family_salutation[]']").val("");
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
                                }
                            }
                            else
                            {
                                if(gen != 'Spouse' || gen!='Self')
                                {
                                    set_non_editable(selectChange,1);
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
                                    selectChange.find('[name="family_gender[]"]').val(dataOpt);
                                    if(gen_id == '2' || gen_id == '3')
                                    {

                                        if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
                                        {

                                            selectChange.find("select[name='family_salutation[]']").val("Master");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                        }
                                        else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
                                        {
                                            selectChange.find("select[name='family_salutation[]']").val("Ms");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                        }

                                    }
                                    else
                                    {
                                        if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
                                        {

                                            selectChange.find('.female_gen'). prop('disabled', true);
                                            selectChange.find('.female_gen').hide();
                                            selectChange.find('.male_gen'). prop('disabled', false);
                                            selectChange.find('.male_gen').show();
                                        }
                                        else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
                                        {
                                            selectChange.find('.female_gen'). prop('disabled', false);
                                            selectChange.find('.female_gen').show();
                                            selectChange.find('.male_gen'). prop('disabled', true);
                                            selectChange.find('.male_gen').hide();
                                        }
                                    }
                                    //helathpro change
                                    selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);

                                }
                            }
                        }

                    }
                }
            });
        }else{
            $.ajax
            ({
                url: "<?php echo base_url(); ?>teleproposal/family_details_relation",
                type: "POST",
                data: {relation_id: $(e).val()},
                dataType: "json",
                beforeSend: function() {
                 //   set_session();
                },
                success: function (response)
                {

                    var dataOpt = selectChange.find("[name='family_members_id[]'] :selected").attr("data-opt");

                    selectChange.find("input[name='first_name[]']").val("");
                    selectChange.find("input[name='middle_name[]']").val("");
                    selectChange.find("input[name='last_name[]']").val("");
                    selectChange.find("input[name='family_id']").val("");
                    selectChange.find("input[type='text'][name='family_date_birth[]']").val("");
                    selectChange.find("input[name='age[]']").val("");
                    selectChange.find("input[name='age_type[]']").val("");
                    selectChange.find("input[name='family_gender[]']").val("");
                    selectChange.find("[name='family_salutation[]']").val("");
                    var set_blank_variable =  selectChange.find("[name='family_members_id[]'] :selected").val();
                    set_blank(set_blank_variable);

                    var family_detail = response.family_data;
                    var i ;
                    if (true)
                    {

                        if(family_detail.length != 0)
                        {
                            for(i = 0; i < family_detail.length; i++)
                            {
                                if (family_detail[i].fr_id == "0")
                                {
                                    set_blank(set_blank_variable);
                                    selectChange.find("input[name='first_name[]']").val(family_detail[i].emp_firstname.toUpperCase());
                                    selectChange.find("input[name='last_name[]']").val(family_detail[i].emp_lastname.toUpperCase());
                                    selectChange.find("input[name='middle_name[]']").val(family_detail[i].emp_middlename);
                                    selectChange.find("input[name='family_id']").val(family_detail[i].family_id);
                                    selectChange.find("input[name='family_gender[]']").val(family_detail[i].gender);
                                    selectChange.find("select[name='family_salutation[]']").val(family_detail[i].salutation);
                                    selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].bdate);
                                    get_age(family_detail[i].bdate,selectChange);
                                    set_blank(set_blank_variable);
                                    set_non_editable(selectChange,0);
                                }
                                else
                                {
                                    var gen = selectChange.find("[name='family_members_id[]'] :selected").html();
                                    if(gen == 'Spouse')
                                    {
                                        set_non_editable(selectChange,1);
                                        if($("#gender1").val() == 'Male')
                                        {

                                            selectChange.find('[name="family_gender[]"]').val("Female");
                                            selectChange.find("select[name='family_salutation[]']").val("Mrs");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',true);

                                        }
                                        else if($("#gender1").val() == 'Female')
                                        {
                                            selectChange.find('[name="family_gender[]"]').val("Male");
                                            selectChange.find("select[name='family_salutation[]']").val("Mr");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                        }
                                        else
                                        {
                                            selectChange.find('[name="family_gender[]"]').val(" ");
                                            selectChange.find("[name='family_salutation[]']").val("");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
                                        }

                                        if(family_detail[i].policy_member_first_name){
                                            selectChange.find("input[name='first_name[]']").val(family_detail[i].policy_member_first_name.toUpperCase());
                                        }
                                        if(family_detail[i].policy_member_middle_name){
                                            selectChange.find("input[name='middle_name[]']").val(family_detail[i].policy_member_middle_name.toUpperCase());
                                        }
                                        if(family_detail[i].policy_member_last_name){
                                            selectChange.find("input[name='last_name[]']").val(family_detail[i].policy_member_last_name.toUpperCase());
                                        }


                                        selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
                                        selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);
                                        selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
                                        selectChange.find("input[type='text'][name='age[]']").val(family_detail[i].age);

                                        selectChange.find("input[type='text'][name='age_type[]']").val(family_detail[i].age_type);

                                        get_age(family_detail[i].policy_mem_dob,selectChange);
                                    }
                                    else
                                    {
                                        /*selectChange.find("input[name='first_name']").val(family_detail[i].policy_member_first_name);
                                                selectChange.find("input[name='middle_name']").val(family_detail[i].policy_member_middle_name);
                                                selectChange.find("input[name='last_name']").val(family_detail[i].policy_member_last_name);
                                                selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
                                                selectChange.find("input[type='text'][name='family_date_birth']").val(family_detail[i].policy_mem_dob);
                                                selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
                                                selectChange.find("input[type='text'][name='age']").val(family_detail[i].age);
                                                selectChange.find("input[name='family_gender']").val(family_detail[i].policy_mem_gender)
                                                selectChange.find("input[type='text'][name='age_type']").val(family_detail[i].age_type);

                                                get_age_family(family_detail[i].bdate,selectChange);*/

                                        if(gen != 'Spouse' || gen!='Self')
                                        {
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
                                            selectChange.find('[name="family_gender[]"]').val(dataOpt);

                                            if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
                                            {
                                                salutation_hide_show(selectChange,'Male');
                                            }
                                            else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
                                            {
                                                salutation_hide_show(selectChange,'Female');

                                            }

                                        }
                                    }
                                }
                            }
                        }
                        else
                        {

                            var gen = selectChange.find("[name='family_members_id[]'] :selected").html();
                            var gen_id = selectChange.find("[name='family_members_id[]'] :selected").val();
                            if(gen == 'Spouse')
                            {
                                set_non_editable(selectChange,1);
                                if($("#gender1").val() == 'Male')
                                {

                                    selectChange.find('[name="family_gender[]"]').val("Female");
                                    selectChange.find("select[name='family_salutation[]']").val("Mrs");
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                }
                                else if($("#gender1").val() == 'Female')
                                {
                                    selectChange.find('[name="family_gender[]"]').val("Male");
                                    selectChange.find("select[name='family_salutation[]']").val("Mr");
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                }
                                else
                                {
                                    selectChange.find('[name="family_gender[]"]').val(" ");
                                    selectChange.find("[name='family_salutation[]']").val("");
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
                                }
                            }
                            else
                            {
                                if(gen != 'Spouse' || gen!='Self')
                                {
                                    set_non_editable(selectChange,1);
                                    selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
                                    selectChange.find('[name="family_gender[]"]').val(dataOpt);
                                    if(gen_id == '2' || gen_id == '3')
                                    {

                                        if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
                                        {

                                            selectChange.find("select[name='family_salutation[]']").val("Master");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                        }
                                        else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
                                        {
                                            selectChange.find("select[name='family_salutation[]']").val("Ms");
                                            selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
                                        }

                                    }
                                    else
                                    {
                                        if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
                                        {

                                            selectChange.find('.female_gen'). prop('disabled', true);
                                            selectChange.find('.female_gen').hide();
                                            selectChange.find('.male_gen'). prop('disabled', false);
                                            selectChange.find('.male_gen').show();
                                        }
                                        else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
                                        {
                                            selectChange.find('.female_gen'). prop('disabled', false);
                                            selectChange.find('.female_gen').show();
                                            selectChange.find('.male_gen'). prop('disabled', true);
                                            selectChange.find('.male_gen').hide();
                                        }
                                    }

                                }
                            }
                        }

                    }
                }
            });


        }

    }

    function set_blank(set_blank_variable)
    {
        if(set_blank_variable == '')
        {
            set_non_editable(selectChange,1);
            selectChange.find("input[name='first_name[]']").val("");
            selectChange.find("input[name='middle_name[]']").val("");
            selectChange.find("input[name='last_name[]']").val("");
            selectChange.find("input[name='family_id']").val("");
            selectChange.find("input[type='text'][name='family_date_birth[]']").val("");
            selectChange.find("input[name='age[]']").val("");
            selectChange.find("[name='family_salutation[]']").val("");

            selectChange.find("input[name='age_type[]']").val("");

            selectChange.find("input[name='family_gender[]']").val("");
        }
    }

    function set_non_editable(selectChange,Is_edit)
    {
        if(Is_edit == 0)
        {
            selectChange.find("input[name='first_name[]']").attr('disabled','disabled');
            selectChange.find("input[name='last_name[]']").attr('disabled','disabled');
            selectChange.find("input[name='middle_name[]']").attr('disabled','disabled');
            selectChange.find("input[name='family_id']").attr('disabled','disabled');
            selectChange.find("input[name='family_gender[]']").attr('disabled','disabled');
            //selectChange.find("input[type='text'][name='family_date_birth[]']").attr('disabled','disabled');
            selectChange.find("select[name='family_salutation[]']").attr('disabled','disabled');
            selectChange.find("input[name='mem_email_id[]']").attr('disabled','disabled');
            selectChange.find("input[name='mem_mob_no[]']").attr('disabled','disabled');
        }
        else
        {
            selectChange.find("input[name='first_name[]']").removeAttr('disabled','disabled');
            selectChange.find("input[name='last_name[]']").removeAttr('disabled','disabled');
            selectChange.find("input[name='middle_name[]']").removeAttr('disabled','disabled');
            selectChange.find("input[name='family_id']").removeAttr('disabled','disabled');
            selectChange.find("input[name='family_gender[]']").removeAttr('disabled','disabled');
            //selectChange.find("input[type='text'][name='family_date_birth[]']").removeAttr('disabled','disabled');
            selectChange.find("select[name='family_salutation[]']").removeAttr('disabled','disabled');
            selectChange.find("input[name='mem_email_id[]']").removeAttr('disabled','disabled');
            selectChange.find("input[name='mem_mob_no[]']").removeAttr('disabled','disabled');
        }
    }

    $(".dobdate").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        minDate : 0,
        yearRange: "-60: +30",
        onSelect: function (dateText, inst)
        {
            $(this).val(dateText);
            /*var selectChange=$(this).closest(".row");
                get_age(dateText);*/
        }
    });
    function convertDate(inputFormat)
    {
       // alert(inputFormat);
        var b = inputFormat.split(/\D/);
        return b.reverse().join('-');
    }


    function get_age(dateStrings,selectChange='')
    {

        var dateString = convertDate(dateStrings);
        var age_type;
        var today = new Date();

        var birthDate = new Date(dateString);
        var age = today.getFullYear() - birthDate.getFullYear() ;

        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;

        }
        if(age != 0)
        {

            age_type = " years";
        }else
        {

            var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
            var firstDate = new Date();
            var secondDate = new Date(dateString);

            var diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));
            //age = diffDays -1 ;
            // commented by ankita
            age = diffDays;
            age_type = "days";
        }


        selectChange.find("input[name='age[]']").val(age);
        selectChange.find("input[name='age_type[]']").val(age_type);
        // $("#age1").val(age);
        // $("#age_type1").val(age_type);
    }
    function get_age_family(e,selectChange)
    {

        var today = new Date();
        var dob = new Date(e);
        var z = e.split("-");
        var dob = new Date(z[2], z[1] - 1, z[0]);
        var today_mon = today.getMonth();
        var dob_mon = dob.getMonth();
        var dob_day = dob.getDate();
        var today_day = today.getDate();

        var age_distance = today.getFullYear() - dob.getFullYear();
        if (today_mon >= dob_mon && today_day >= dob_day)
        {

            if (age_distance > 0)
            {
                selectChange.find("input[name='age[]']").val(age_distance);
                selectChange.find("input[name='age_type[]']").val("years");
            }
            else
            {
                var month = ("0" + (today.getMonth() + 1)).substr(-2);
                var strDate =("0" + today.getDate()).substr("-2") + "-" + ("0" + month).substr("-2") + "-" + today.getFullYear();
                var strDate = today.getDate() + "-" + month + "-" + today.getFullYear();
                var date = strDate.split("-");
                var display_date = date[2] + "-" + date[1] + "-" + date[0];
                var date = e.split("-");
                var date_bday = date[2] + "-" + date[1] + "-" + date[0];
                var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
                var days = Math.floor(day / (1000 * 60 * 60 * 24));
                selectChange.find("input[name='age[]']").val(days);
                selectChange.find("input[name='age_type[]']").val("days");
            }
        }
        else
        {
            if (age_distance > 1)
            {
                selectChange.find("input[name='age[]']").val(age_distance);
                selectChange.find("input[name='age_type[]']").val("years");
                var age_distances = age_distance - 1;
            }
            else if (age_distance == 1)
            {
                var month = ("0" + (today.getMonth() + 1)).substr(-2);

                var strDate = ("0"+today.getDate()).substr(-2)  + "-" + ("0"+month).substr(-2) + "-" + today.getFullYear();

                var date = strDate.split("-");
                var display_date = date[2] + "-" + date[1] + "-" + date[0];
                var date = e.split("-");
                var date_bday = date[2] + "-" + date[1] + "-" + date[0];
                var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
                var days = Math.floor(day / (1000 * 60 * 60 * 24));
                if(days >= 365)
                {
                    selectChange.find("input[name='age[]']").val(1);
                    selectChange.find("input[name='age_type[]']").val("years");
                }
                else
                {
                    selectChange.find("input[name='age[]']").val(days);
                    selectChange.find("input[name='age_type[]']").val("days");
                }

            }
            else
            {
                var month = ("0" + (today.getMonth() + 1)).substr(-2);
                var strDate = ("0" + today.getDate()).substr("-2") + "-" + ("0" + month).substr("-2") + "-" + today.getFullYear();
                var strDate = today.getDate() + "-"  + month + "-" + today.getFullYear();
                var date = strDate.split("-");
                var display_date = date[2] + "-" + date[1] + "-" + date[0];
                var date = e.split("-");
                var date_bday = date[2] + "-" + date[1] + "-" + date[0];
                var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
                var days = Math.floor(day / (1000 * 60 * 60 * 24));
                selectChange.find("input[name='age[]']").val(days);
                selectChange.find("input[name='age_type[]']").val("days");
            }
        }


    }
    function getPremium(selectChange)
    {
        //console.log(get_premium+"/////////"+total_premium+$('input[name=GCI_optional]:checked').val());
        //if your doing changes in this function please do the same changes in above function as well for premium calculation
        $.post("<?php echo base_url(); ?>teleproposal/get_premium", { "sum_insured":  selectChange.find('select[name=sum_insure]').val(), "policy_detail_id":  selectChange.find('select[name=sum_insure] :selected').attr("data-policyno"), "deductable":$('#hidden_deductable').val(),"family_construct":  selectChange.find('select[name=familyConstruct]').val(),"gci_optional":  $('input[name=GCI_optional]:checked').val(),"product_id" : $('#plan_name').val() }, function (e)
        {
            $("#premiumModalHidden").val(e);
            e = JSON.parse(e);

            var premium = 0;
            $("#premiumModalBody").html("");

            e.forEach(function (e1) {

                str = "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
                str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
                str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(e1.PremiumServiceTax).toFixed(2) + "</span></div></div>";

                $("#premiumModalBody").append(str);
                premium += parseFloat(e1.PremiumServiceTax);
            });
            /*if(get_premium != ''){
                    $('#premiumModalBody').html(get_premium);

                }*/
            $("#premium").val(premium.toFixed(2));
            /*if(parseInt(total_premium) != 0 && !isNaN(total_premium)){
                    $('#premium').val(total_premium);

                }*/
        });
    }

    $(document).on("change", "select[name='sum_insure']", function ()
    {




        apply_button();

        selectChange = $(this).closest("form");
        var tax_with_premium = selectChange.find('select[name=sum_insure] option:selected').data("id");
        var select_sum_insured = selectChange.find('select[name=sum_insure] :selected');
        selectChange.find('select[name=familyConstruct]').empty();
        if(select_sum_insured.val() == '')
        {
            selectChange.find('select[name=familyConstruct]').html('<option value="" selected>Select</option>');
        }

        // upendra - 29-06-2021
        if (select_sum_insured.attr("data-type") == "family_construct" || select_sum_insured.attr("data-type") == "family_construct_age" || select_sum_insured.attr("data-type") == "combo_diff_construct")
        {
            $("select[name=familyConstruct]").empty();
            $("select[name=familyConstruct]").html('<option value="" selected>Select</option>');

            $.post("<?php echo base_url(); ?>teleproposal/get_family_construct",
                {
                    "policyNo": select_sum_insured.attr("data-policyno"),
                    "sumInsured": select_sum_insured.val(),
                    "table": select_sum_insured.attr("data-type"),
                }, function (e)
                {
                    selectChange.find('select[name=familyConstruct]').empty();
                    e = JSON.parse(e);
//dedupe
                    if(e.status == 'error'){
                        swal("error",e.msg,"warning");
                    }else{
                        e = e.data;
                        if (e)
                        {
                            $("select[name=familyConstruct]").html('<option value="" selected>Select</option>');
                            e.forEach(function (e1)
                            {

                                selectChange.find('select[name=familyConstruct]').append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
                            });


                        }
                        /*added helathpro changes*/
                        $("#patFamilyConstructDiv").show();
                        /*end*/
                        /*healthpro changes show annualincome if suminsure > 10 lac*/
                        if($('#plan_name').val() != 'T03'){
                            if(select_sum_insured.val() > 1000000){
                                $("#annual_income_label").show();
                                $("#occupation_label").show();
                                $("#annual_income").removeClass("ignore");
                                $("#occupation").removeClass("ignore");

                            }else{
                                $("#annual_income_label").hide();
                                $("#occupation_label").hide();
                                $("#annual_income").addClass("ignore");
                                $("#occupation").addClass("ignore");
                            }
                        }
                    }

                    /*healthpro changes end*/

                });


        }else if(select_sum_insured.attr("data-type") == "memberAge"){
            $("select[name=familyConstruct]").empty();
            $("select[name=familyConstruct]").html('<option value="" selected="">Select</option><option value="1A">1A</option><option value="2A">2A</option>');
            $("#patFamilyConstructDiv").show();
            /*healthpro changes show annualincome if suminsure > 10 lac*/
            if(select_sum_insured.val() > 1000000){
                $("#annual_income_label").show();
                $("#occupation_label").show();
                $("#annual_income").removeClass("ignore");
                $("#occupation").removeClass("ignore");

            }else{$("#occupation_label").show();
                $("#annual_income_label").hide();
                $("#occupation_label").hide();
                $("#annual_income").addClass("ignore");
                $("#occupation").addClass("ignore");
            }
            /*healthpro changes end*/
        }

        if($('select[name=sum_insure]').val() != '' && $('select[name=sum_insure] :selected').attr("data-policyno") != '' && $('select[name=familyConstruct]').val() != '' && $('#plan_name').val() != ''){
            $.post("<?php echo base_url(); ?>teleproposal/get_premium", { "sum_insured":  $('select[name=sum_insure]').val(), "policy_detail_id": $('select[name=sum_insure] :selected').attr("data-policyno"),"deductable":$('#hidden_deductable').val(),"family_construct":  $('select[name=familyConstruct]').val(),"gci_optional":  $('input[name=GCI_optional]:checked').val(),"product_id" : $('#plan_name').val() }, function (e)
            {
                ////debugger;
                $("#premiumModalHidden").val(e);
                e = JSON.parse(e);

                var premium = 0;
                $("#premiumModalBody").html("");
                var str = '';
                e.forEach(function (e1) {
                    //console.log(e1);
                    str += "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
                    str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
                    str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(e1.PremiumServiceTax).toFixed(2) + "</span></div></div>";

                    $("#premiumModalBody").append(str);
                    premium += parseFloat(e1.PremiumServiceTax);
                });
                //console.log(get_premium+']]]]]');
                if(str != ''){
                    get_premium = str;
                    $('#premiumModalBody').html(str);

                }
                $("#premium").val(premium.toFixed(2));
                if(parseInt(premium) != 0 && !isNaN(premium)){
                    total_premium = premium;
                    $('#premium').val(premium);

                }
            });
        }
    });
    $("#plan_name").on("change",function(){
        ////debugger;
        var product_id = $(this).val();

        $.ajax({

            url: "<?php echo base_url(); ?>teleproposal/health_declaration",
            type: "POST",
            data: {product_id : $('#plan_name').val()},

            success: function (data)
            {
//debugger;
                if(data!='')
                {

                    //updated by upendra on 09-04-2021
                    var data = JSON.parse(data);
                    var ghd_content = $('#ghd-table').html();
                    data.ghd.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
                    if(product_id == 'R06' || product_id == 'T01' || product_id == 'T03'){//ankita ped changes
                        $('#policy_declare_new').html(data.ghd);
                        $('.myremark').val('');
                        $('.myremark').hide();
                    }else{
                        $('#ghd-table').html(ghd_content);
                        $('.myremark').show();
                    }
                    $('#health_declare').show();
                    $('#policy_declare').html(data.employee_declaration);

                }

            }
        });
        if($('#plan_name').val() != ''){
            var product_name = $(this).find("option:selected").text();
            //console.log('--->'+product_name+'<------');
            $("#pr_name").html(product_name);
            $("#pr_name").html('Axis Tele Inbound Affinity Portal for ABHI');




        }

        $('#occupation').prop('selectedIndex',0);


        $(".sub_isposition").trigger('change');
        if($(this).val() == 'T01'){


            $('.GCIOptionalDiv').show();
        }else{
            $('.GCIOptionalDiv').hide();
        }
        $.post("<?php echo base_url(); ?>teleproposal/get_family_construct",
            {
                product_id:product_id
            }, function (e)
            {
                debugger;
                $('[name=familyConstruct]').empty();
                e = JSON.parse(e);
//dedupe
                if(e.status == 'error'){
                    swal("error",e.msg,"warning");
                }else{
                    e = e.data;
                    if (e)
                    {
                        $("select[name=familyConstruct]").html('<option value="" selected>Select</option>');

                        e.forEach(function (e1)
                        {
                            $("#patFamilyConstruct").append('<option value="'+e1+'">'+e1+'</option>');

                            // $('#patFamilyConstruct').append("<option value='" + e1 + ">" + e1 + "</option>");
                        });


                    }

                }

                /*healthpro changes end*/

            });

    })
let abc =[];
    function myFunction(e)
    {
        selectChange = $(e).closest("form");
        var arr = [];
        var i = 0;
        var ded = 0;
        var ab = $(e).data("policy");
        if (!abc.includes(ab)) {
            abc.push(ab);
        }
        //console.log(abc.join(','));
        var imp = abc.join(',');
$("#policy_no").val(imp);

        var deductable = $(e).data("deductable");
        if(deductable == 'yes')
        {
            $('.ded_tbl').show();
            var id_det = $(e).attr("id");
            var policy_no = $(e).data("policy");
            var sum_insure = $("#"+id_det).val();
          //  alert(sum_insure);
            $.ajax({
                url: "<?php echo base_url(); ?>teleproposal/get_deductable_amount",
                type: "POST",
                async: false,
                data:{policy_no:policy_no,sum_insure:sum_insure},
                dataType: "json",
                success: function (e)
                {debugger;


                   // e = JSON.parse(e);
                    if (e)
                    {
                        e = e.data
                      //  $("select[name=deductable]").html('<option value="" selected>Select</option>');

                        e.forEach(function (e1)
                        {
                            $("#deductable").append('<option value="'+e1+'">'+e1+'</option>');

                            // $('#patFamilyConstruct').append("<option value='" + e1 + ">" + e1 + "</option>");
                        });


                    }

                }
            });

        }
        else
        {
            $('.ded_tbl').hide();

        }
        $('.sum_insured_data').each(function(){
            if($(this).val()!='') {
                if ($(this).data('deductable') == 'yes') {
                    ded = $("#deductable").val();
                }
                arr[i] =
                    {
                        "policy_no": $(this).data('policy'),
                        "sum_insured": $(this).val(),
                        "deductable": ded,
                    }

                i++;
            }
        });
        console.log(arr);
        var get_arr = JSON.stringify(arr);
        var form = "hiddenpolicyarr=" + get_arr + "&family_construct="+$("#patFamilyConstruct").val();
        $.post("<?php echo base_url(); ?>teleproposal/get_premium_plan", form, function (e) {
            debugger;
            e = JSON.parse(e);
           // $("#helathFamilyModal").modal('hide');

            if(e.message != 'true'){ //error in validation
                //  swal("Alert", e.message);
            }else{
                var getPremium = e.get_premium;
                if(getPremium){
                    get_age_wise_premium(getPremium);
                }




            }
        });

      //  alert(abc);


    }
function FamilyConstruct(e)
{
    var product_id = $('#plan_name').val();

    //alert($(e).val())
 var famConstruct = $(e).val();
    var arr = [];
    var i = 0;
    var ded = 0;

    $.ajax({
        url: "<?php echo base_url(); ?>teleproposal/get_suminsured_data",
        type: "POST",
        data: {"product_id":product_id,"family_construct":famConstruct},
        async: false,
        dataType: "json",
        success: function (response)
        {
            // var data = JSON.parse(response);
            debugger;
            console.log(response);
            if (response.length != "") {

                var sum_ins;
                var sum_insured;
                var sort_sum_insured;
                $('.sum_insured_append').html('');


                var arr = [];
                for (i = 0; i < response.length; i++) {
                    if(response[i].permile == 'Yes')
                    {
                        sum_ins = '<div class="col-md-4 mb-3"><label for="validationCustomUsername" class="col-form-label">' + response[i].short_code + ' cover<span class="lbl-star">*</span></label><div class="input-group"> <input class="form-control dis-col sum_insured_data" type="text" value="" onchange="myFunction(this);" name="sum_insure_'+response[i].policy_id+'" id="sum_insures_'+i+'" data-policy ='+response[i].policy_id+' data-deductable = '+response[i].deductable+'><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span></div>\n</div></div>';
                        $('.sum_insured_append').append(sum_ins);

                    }else {
                        sum_ins = '<div class="col-md-4 mb-3"><label for="validationCustomUsername" class="col-form-label">' + response[i].short_code + ' cover<span class="lbl-star">*</span></label><div class="input-group"><select class="form-control sum_insured_data" onchange="myFunction(this);" name="sum_insure_' + response[i].policy_id + '" id="sum_insures_' + i + '" data-policy =' + response[i].policy_id + ' data-deductable = ' + response[i].deductable + '><option value="">Select</option></select><div class="input-group-prepend"><span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span> </div></div></div>';
                        $('.sum_insured_append').append(sum_ins);
                        sort_sum_insured = response[i].sum_insured.sort(function (a, b) {
                            return a - b
                        });
                        sort_sum_insured.forEach(function (e) {

                            sum_insured = '<option> ' + e + '</option>';
                            $('#sum_insures_' + i).append(sum_insured);


                        });
                    }
                }


            }

        }
    });

    $('.sum_insured_data').each(function(){
        if($(this).data('deductable') == 'yes')
        {
            ded= $("#deductable").val();
        }
        arr[i] =
            {
                "policy_no": $(this).data('policy'),
                "sum_insured": $(this).val(),
                "deductable" : ded,
            }

    i++;
    });
console.log(arr);
var get_arr = JSON.stringify(arr)

    combo_premium(get_arr,famConstruct);
return;


}

    function combo_premium(get_arr,famConstruct)
    {
       // var modal_age_cal = $("#patFamilyConstruct :selected").attr("modal-ag-cal");
        var fam_const = famConstruct.split("+");;
        var adult = 'A';
        var kid = 'K';
        var adult_concat;
        var kid_concat;
        var max_adult;
        var child;
        var str ='';
        var str_1 ='';
        var str1 = '';
        var arr = [];
        var i;
        var policy_no =  get_arr;




            $.post("<?php echo base_url(); ?>teleproposal/get_combo_rel", {
                "policy_no": policy_no,
            }, function (e) {
debugger;
                res = JSON.parse(e);

                if (res) {
                    $('.adult_data').html('');
                    $('.adult_row').html('');

                    res.forEach(function (e1) {
                        max_adult = e1.max_adult;
                        adult_concat = max_adult.concat(adult);

                        if(e1.multiple_allowed == 'N')
                        {

                            if(fam_const[0] < adult_concat)
                            {
                                $(".check_adult").removeAttr("style", "display:none");
                                str = "<div class='custom-control custom-radio'><input type='radio' class='custom-control-input' name='adult_fr_id' id='adult_fr_id_"+e1.fr_id+"'  value='"+e1.fr_id+"'><label class='custom-control-label' for='adult_fr_id_"+e1.fr_id+"'>"+e1.fr_name+"</label></div>";
                                $('.adult_data').append(str);


                            }
                            else
                            {

                                $(".check_adult").attr("style", "display:none");
                                if(e1.fr_id !=0){
                                    $(".adult_row").removeAttr("style", "display:none");
                                    other_than_self_dropdown(e1.fr_id,e1.fr_name);
                                }
                            }

                        }
                        if(fam_const[1] != '')
                        {
                            if(e1.multiple_allowed == 'Y'){

                                arr.push([e1.fr_name,e1.fr_id,e1.gender_option]);

                            }
                        }

                   
                            //$(".product_name").text(product_name);





                    });

                    $('.kids_row').html('');
                    if(fam_const[1] != '' && fam_const[1] != undefined)
                    {
                        var fam_rel = fam_const[1].substr(0,1);
                        for(i = 1;i<=fam_rel;i++)
                        {

                            str1 = "<div class='mb-2 col-md-12 row kid"+i+"_div'><div class='col-md-6'><div class='form-group'><label class='col-form-label'>Relation With Proposer<span style='color:#FF0000'>*</span></label> <select class='form-control' name='kid_rel[kid_"+i+"][kid_"+i+"_rel]' id='kid_"+i+"_rel'><option value=''>Select Relation</option>";
                            arr.forEach(function(arrs)
                            { str1 += "<option data-opt='"+arrs[2]+"' value='"+arrs[1]+"'>"+arrs[0]+"</option>";
                            });
                            str1 += "</select> </div> </div><div class='col-md-6'> <div class='form-group'> <label class='col-form-label'>Date of Birth<span style='color:#FF0000'>*</span></label> <input class='form-control kid"+i+"_date_birth modal_dob ad_valid' autocomplete='off' type='text' id='kid_"+i+"_date_birth' name='kid_rel[kid_"+i+"][kid_"+i+"_date_birth]'></div> </div></div>";
                            $('.kids_row').append(str1);
                            combo_set_premium_data();
                            $("#kid_"+i+"_date_birth").datepicker({
                                dateFormat: "dd-mm-yy",
                                prevText: '<i class="fa fa-angle-left"></i>',
                                nextText: '<i class="fa fa-angle-right"></i>',
                                changeMonth: true,
                                changeYear: true,
                                maxDate: 0,
                                yearRange: "-100: +0",
                                onSelect: function (dateText, inst) {
                                }
                            });


                        }}
$('#hiddenpolicyarr').val(policy_no);
                    $("#helathFamilyModal").modal({backdrop: 'static'});



                }

            });





    }

    $("#famConSelected1").validate({
        ignore: ".ignore",
        rules: {

        },
        messages: {

        },
        submitHandler: function (form) {
debugger;


            //alert($("#patFamilyConstruct").val());
            var form = $("#famConSelected1").serialize() + "&family_construct="+$("#patFamilyConstruct").val();
            $.post("<?php echo base_url(); ?>teleproposal/familyconst_dob", form, function (e) {
                e = JSON.parse(e);
                $("#helathFamilyModal").modal('hide');

                if(e.message != 'true'){ //error in validation
                  //  swal("Alert", e.message);
                }else{
                    var getPremium = e.get_premium;
                    if(getPremium){
                       //get_age_wise_premium(getPremium);
                    }


                    var opt = e.memArr;
                    var rel_allowed = e.rel_allowed;



                    console.log(rel_allowed);

$("#add_more").html('');

                    var family_construct = $('#patFamilyConstruct option:selected').val();
                    var result = family_construct.split('+');

                    var add_length = [];
                    add_length.push(parseInt(result[0]));
                    add_length.push(parseInt(result[1]));

                    if(!$('#patFamilyConstruct').is('[disabled=disabled]')){
                        // swal("Alert","Below Member Details Section would get modified or deleted. Press OK to Proceed.");
                        var boxlen = $('.commonCls').length;
                        var icount;
                        if(boxlen == ''){
                            icount = 0
                        }else{
                            icount = 0+boxlen;
                        }
                        //$("#add_more").empty();


                        for (i = icount; i < sum(add_length); i++) {
                            common_append(i);
                            debugger;
                            $.each(rel_allowed, function( index, value ) {
                                if(opt.toString().indexOf(value) == -1){
                                    console.log(opt);
                                    //remove from relation with proposer dropdown
                                    $("#family_members_id"+i+" option[value='"+value+"']").hide();
                                }else{
                                    console.log(opt);
                                    $("#family_members_id"+i+" option[value='"+value+"']").show();
                                }
                            });
                        }

                        $("#add_btn_view").show();

                    }
                }
            });
        }
    });

    function get_age_wise_premium(e){
        console.log(e);
        var premium = 0;
        $("#premium_calculations_data").html("");
        var PremiumServiceTax;
        e.forEach(function (e1) {


            PremiumServiceTax = e1.premium;
            str = "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_name + "</span></div>";
            str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sumInsured + "</span></div>";
            str += "<div class='mr-3'><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(PremiumServiceTax).toFixed(2) + "</span></div></div>";
            $("#premium_calculations_data").append(str);

            premium += parseFloat(PremiumServiceTax);
          //  $("#premiumBif").css("pointer-events","auto");
        });


        if(PremiumServiceTax == undefined)
        {

            $('#patFamilyConstruct').val('');
            $('#premium').val('');
            $("#premiumModalBody").html('');
        }
        else
        {
            $("#total_premium").text(premium.toFixed(2));
        }


    }
    $(document).ready(function () {
        debugger;
        var lead = $('#leadHidden').val();
$("#unique_trace_id").text(lead);
        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/get_agent_details",
            type: "POST",
            async: false,
            dataType: "json",
            success: function (response)
            {


                $("#avCode").val(response.agent_id);
                $("#avName").val(response.agent_name.toUpperCase());

            }
        });
        $.ajax({

            url: "<?php echo base_url(); ?>teleproposal/health_declaration",
            type: "POST",
            data: {product_id : $('#plan_name').val()},

            success: function (data)
            {//debugger;

                if(data!='')
                {

                    //updated by upendra on 06-04-2021
                    var data = JSON.parse(data);
                    var ghd_content = $('#ghd-table').html();
                    if(product_id == 'R06' || product_id == 'T01' || product_id == 'T03'){//ankita ped changes
                        data.ghd.replace('/&lt;/g', '<').replace('/&gt;/g', '>');
                        $('#policy_declare_new').html(data.ghd);
                        $('.myremark').val('');
                        // alert(data.ghd);
                        $('.myremark').hide();
                    }else{
                        $('#ghd-table').html(ghd_content);
                        $('.myremark').show();
                    }
                    $('#health_declare').show();
                    $('#policy_declare').html(data.employee_declaration);

                }

            }
        });

        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/master_nominee",
            type: "POST",
            async: false,
            dataType: "json",
            success: function (response)
            {

                $("#nominee_relation").empty();
                $("#nominee_relation").append("<option value = ''>Select Nominee</option>");
                for (i = 0; i < response.length; i++)
                {

                    $("#nominee_relation").append("<option  data-opt = "+ response[i]['gender'] +" value =" + response[i]['nominee_id'] + ">" + response[i]['nominee_type'] + "</option>");


                }
            }
        });

        var product_id = $('#plan_name').find(":selected").val();
        ;
        //alert(product_id);
        if (product_id != '' || product_id != null) {
            $.ajax({
                url: "<?php echo base_url(); ?>teleproposal/get_suminsured_data",
                type: "POST",
                data: {product_id: product_id},
                async: false,
                dataType: "json",
                success: function (response) {

                    if (response.length != "") {

                        $("#sum_insure").empty();
                        $("#sum_insures").empty();

                        $("#sum_insure").append(
                            "<option value = ''>Select Sum insured</option>"
                        );
                        $("#sum_insures").append(
                            "<option value = ''>Select Sum insured</option>"
                        );


                        var arr = [];
                        for (i = 0; i < response.length; ++i) {


                            if (response[i].flate) {

                                response[i].flate.forEach(function (e) {
                                    var sumInsured = e["sum_insured"].split(",");
                                    var PremiumServiceTax = e["PremiumServiceTax"].split(",");

                                    setMasPolicy("flate", e);
                                });
                            }

                            if (response[i].family_construct) {


                                response[i].family_construct.forEach(function (e) {
                                    if (e['combo_flag'] == 'Y') {
                                        if (!arr.includes(e['sum_insured'])) {
                                            arr.push(e['sum_insured']);
                                            setMasPolicy("family_construct", e);
                                        } else {

                                        }
                                    } else {
                                        setMasPolicy("family_construct", e);
                                    }
                                });
                            }

                            if (response[i].family_construct_age) {

                                response[i].family_construct_age.forEach(function (e) {
                                    if (e['combo_flag'] == 'Y') {
                                        if (!arr.includes(e['sum_insured'])) {
                                            arr.push(e['sum_insured']);
                                            setMasPolicy("family_construct_age", e);
                                        } else {

                                        }
                                    } else {
                                        setMasPolicy("family_construct_age", e);
                                    }
                                });
                            }

                            /*healthpro changes */
                            var policyid = [];
                            if (response[i].combo_diff_construct) {
                                response[i].combo_diff_construct.forEach(function (e) {
                                    if (product_id != "T01") {
                                        /*if (e['combo_flag'] == 'Y') {*/
                                        if (!arr.includes(e['sum_insured'])) {
                                            arr.push(e['sum_insured']);
                                            policyid.push(e['policy_sub_type_id']);
                                            setMasPolicy("family_construct", e);
                                        } else {
                                            if (!policyid.includes(e['policy_sub_type_id'])) {
                                                setMasPolicy("combo_diff_construct", e);
                                            }
                                        }
                                        /*else {
                                                    }.
                                                    }*/
                                    } else {

                                        if (e['combo_flag'] == 'Y') {
                                            if (!arr.includes(e['sum_insured'])) {
                                                arr.push(e['sum_insured']);
                                                policyid.push(e['policy_sub_type_id']);
                                                setMasPolicy("combo_diff_construct", e);
                                            } else {
                                                if (!policyid.includes(e['policy_sub_type_id'])) {
                                                    setMasPolicy("combo_diff_construct", e);
                                                }
                                            }
                                        }


                                    }
                                    /*if (e['combo_flag'] == 'Y') {*/
                                    /*if (!arr.includes(e['sum_insured'])) { commented by sonal
                                        arr.push(e['sum_insured']);
                                        policyid.push(e['policy_sub_type_id']);
                                        setMasPolicy("family_construct", e);
                                        } else{
                                            if (!policyid.includes(e['policy_sub_type_id'])){
                                                setMasPolicy("combo_diff_construct", e);
                                            }
                                        }*/
                                    /*else {
                                        }.
                                        }*/

                                });
                            }
                            /*healthpro chnages ends here*/

                            if (response[i].memberAge) {


                                response[i].memberAge.forEach(function (e) {
                                    if (e['combo_flag'] == 'Y') {
                                        if (!arr.includes(e['sum_insured'])) {
                                            arr.push(e['sum_insured']);
                                            setMasPolicy("memberAge", e);
                                        } else {

                                        }
                                    } else {
                                        setMasPolicy("memberAge", e);
                                    }
                                });
                            }
                            /*end*/
                        }


                    }


                }
            });
        }
        proposal_create_check();
    });
    $(document).on("click","input[name='adult_fr_id']",function() {
debugger;
        var cons = $("#patFamilyConstruct").val().split("+");
        if(cons[0] == '1A'){
            if($('input[name="adult_fr_id"]:checked').val() != 0){
                var fr_id = $('input[name="adult_fr_id"]:checked').val();

                var fr_name = $('input[name="adult_fr_id"]:checked').next('label').text();

                $('.adult_row').html('');
                other_than_self_dropdown(fr_id,fr_name);
                $(".adult_row").removeAttr("style", "display:none");
            }else{
                $('.adult_row').html('');

                $('.adult_row').attr("style", "display:none");
            }

        }else{
            //$(".check_adult").Attr("style", "display:none");
        }
    });
    function other_than_self_dropdown(fr_id,fr_name)
    {

        str = "<div class='mb-2 col-md-12 row spouse_div'><div class='col-md-6'><div class='form-group'><label class='col-form-label'>Relation With Proposer<span style='color:#FF0000'>*</span></label> <select class='form-control ' name='rel_name["+fr_name+"]["+fr_name+"_rel]' id='"+fr_name+"_rel'><option data-opt='null' value='"+fr_id+"'>"+fr_name+"</option></select> </div> </div> <div class='col-md-6'> <label class='col-form-label'>Date of Birth<span style='color:#FF0000'>*</span></label> <input class='form-control "+fr_name+"_date_birth modal_dob  ad_valid' autocomplete='off' type='text'  id='"+fr_name+"_date_birth' name=rel_name["+fr_name+"]["+fr_name+"_datebirth]' ><input type ='hidden' name = 'rel_name["+fr_name+"]["+fr_name+"_adult]' value ='A'></div> </div></div>";
        $('.adult_row').append(str);
        $("#"+fr_name+"_date_birth").datepicker({
            dateFormat: "dd-mm-yy",
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            maxDate: 0,
            yearRange: "-100: +0",
            onSelect: function (dateText, inst) {
            }
        });
        combo_set_premium_data();
    }
    function sum(input) {
        if (toString.call(input) !== "[object Array]")
            return false;

        var total = 0;
        for(var i=0;i<input.length;i++) {
            if(isNaN(input[i])) {
                continue;
            }

            total += Number(input[i]);
        }
        return total;
    }

    function setMasPolicy(type, e1){
        var policy =
            e1["policy_sub_type_id"] == 1 ?
                "policy_no" :
                e1["policy_sub_type_id"] == 2 ?
                    "policy_no2" :
                    "policy_no3";
        var id =
            e1["policy_sub_type_id"] == 1 ?
                "sum_insures" :
                e1["policy_sub_type_id"] == 2 ?
                    "sum_insure" :
                    "sum_insures1";
        $("#" + policy).val(e1["policy_detail_id"]);
        if (type == "flate") {
            var sumInsured = e1["sum_insured"].split(",");
            var PremiumServiceTax = e1["PremiumServiceTax"].split(",");
            for (k = 0; k < sumInsured.length; ++k) {
                $("#" + id).append(
                    "<option data-type='" + type + "' data-policyNo='" + e1["policy_detail_id"] + "' data-customer = '" +
                    premium[k] +
                    "'  data-id = '" +
                    PremiumServiceTax[k] +
                    "' family_child= '" +
                    e1.child +
                    "' family_adult= '" +
                    e1.adult +
                    "' value =" +
                    sumInsured[k] +
                    ">" +
                    sumInsured[k] +
                    "</option>"
                );
            }
        } else {

            if (type == "memberAge") {
                $("#patFamilyConstructDiv").hide();
                e1["PremiumServiceTax"] = e1["premium"];
            }
            $("#" + id).append("<option data-type='" + type + "' data-policyNo='" + e1["policy_detail_id"] + "' data-customer = '" + e1.premium + "'  data-id = '" + e1.PremiumServiceTax + "' family_child= '" + e1.child + "' family_adult= '" + e1.adult + "' value =" + e1.sum_insured + ">" + e1.sum_insured + "</option>");
        }
    }

    $.ajax({
        url: "<?php echo base_url(); ?>teleproposal/get_all_policy_data_new",
        type: "POST",
        data: {},
        async: false,
        dataType: "json",
        success: function(response) {

            $("#benifit_4s").css("display", "block");
            $("#plan_name").html(response);
        }
    });

    $.ajax({
        url: "<?php echo base_url(); ?>teleproposal/employee_data",
        type: "POST",
        async: false,
        data: {},
        dataType: "json",
        success: function (response)
        {

            if(response.email == '')
            {
                $("#email_id").attr("readonly", false);
            }
            if(response.emp_pincode == '' || response.emp_pincode == '.')
            {
                $("#pin_code").attr("readonly", false);
            }
            $("#firstname").val(response.emp_firstname.toUpperCase());
            $("#lastname").val(response.emp_lastname.toUpperCase());
            $("#panCard").val(response.pancard);
            $("#annual_income").val(response.annual_income);
            $("#occupation").val(response.occupation);

            try {
                $("#addCard").val(response.adhar);
            }catch(e) {

            }
            $("#lead_id_product").html(response.lead_id);
            $("#lead_id").val(response.lead_id);
            $("#address_line1").val(response.address);
            $("#address_line2").val(response.comm_address);
            $("#address_line3").val(response.comm_address1);

            $("#hidden_policy_id").val(response.pid);
            $("#hidden_deductable").val(response.deductable);
            $("#gender1").val(response.gender).prop("selected", true);
            $("#dob1").val(response.bdate);
            $("#dob1").attr('disabled', 'disabled');
            $("#mob_no").val(response.mob_no);
            $("#mobile_no2").val(response.emg_cno);

            $("#email_id").val(response.email);
            $("#saksham_id").val(response.saksham_id);
            $("#salutation option").filter(function(){
                return $(this).text() === response.salutation ? $(this).prop("selected", true) : false;
            });
            $("#pin_code").val(response.emp_pincode);
            $("#city").val(response.emp_city);
            $("#state").val(response.emp_state);
            if (response.ISNRI == 'Y')
            {

                $("#comAdd").attr("readonly", false);

            }
            // alert(response.address+'----'+response.emp_pincode);
            if(response.address != null && response.emp_pincode != null){
                $('#hidden_customer_section').val(1);
            }

        }
    });
    $.validator.addMethod
    (
        "validate_pincode",
        function (value, element, param)
        {
            var regs = /^\d{6}$/g;
            return this.optional(element) || regs.test(value);
        },
        "Enter a valid Pin Code"
    );
    $.ajax({
        url: "<?php echo base_url(); ?>teleproposal/master_axis_location",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response)
        {

            $("#axis_location").empty();
            $("#axis_location").append("<option value = ''>Select Axis Location</option>");
            for (i = 0; i < response.length; i++)
            {

                $("#axis_location").append("<option  value =" + response[i]['axis_loc_id'] + ">" + response[i]['axis_location'] + "</option>");


            }
        }
    });
    $("#pin_code").keyup(function (e)
    {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        )
        {
            $th.val(
                $th.val().replace(/[^0-9]/g, function (str) {
                    return "";
                })
            );
        }
        $("#city").val('');
        $("#state").val('');
        if($("#pin_code").val().length != 6){
            return;
        }
        var pincode = $(this).val();
        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/get_city_state",
            type: "POST",
            async: false,
            data:{'pincode':pincode},
            dataType: "json",
            beforeSend: function() {
              //  set_session();
            },
            success: function (response)
            {
                if(response != null){

                    if(response.city!=null && response.state!=null)
                    {
                        $("#city").val(response.city);
                        $("#state").val(response.state);
                    }
                }else{
                    if($("#pin_code").val().length == 6){
                        swal("","Invalid Pincode","warning");
                        $("#pin_code").val('');
                    }

                }



            }
        });

    });
    $.validator.addMethod(
        "validateEmail",
        function (value, element, param) {
            if (value.length == 0) {
                return true;
            }
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            return reg.test(value); // Compare with regular expression
        },
        "Please enter a valid Email ID. Correct Customer Email ID is mandatory to process further."
    );

    $("#emp_data").validate({
        ignore: ".ignore",
        focusInvalid: true,
        rules:  {
            salutation:
                {
                    required: true
                },
            firstname:
                {
                    required: true
                },
            /*lastname:
                            {
                                required: true
                            },*/

            dob:
                {
                    required: true
                },
            mob_no:
                {
                    required: true
                },
            comAdd:
                {
                    required: true
                },
            mobile_no2:
                {
                    valid_mobile: true,
                },
            pin_code:
                {
                    required: true,
                    validate_pincode: true,
                },
            city:
                {
                    required: true,
                },
            state:
                {
                    required: true,
                },
            email:
                {
                    required: true,
                    validateEmail: true,
                },
        },
        messages: {
        },
        invalidHandler: function(form, validator)
        {

            validator.focusInvalid();
        },
        submitHandler: function (form)
        {//debugger;
            var agent_details = $("#hidden_agent_section").val();

            if($('#disposition').val()==45||$('#disposition').val()==46){

                if(!$("#emp_agent_data").valid())
                {
                    displayMsg("error", "Please FILL Agent Details!");

                    return;
                }
                else if(!$("#emp_data").valid())
                {

                    displayMsg("error", "Please FILL Customer Details!");


                    return;
                }
            }
            /* end  */
            else if(agent_details == 0){
                displayMsg("error", "Please SAVE the values entered so far to proceed with new Section!");


                return false;
            }
            //var all_data = $("#emp_data").serialize();
            var all_data = $("#emp_data").serialize()+ "&self_edit_clicked="+$("#self_edit_clicked").val()+"&gender1="+$("#gender1").val()+"&dob="+$("#dob1").val()+"&salutation="+$('#salutation').val();

            $.ajax({
                url: "<?php echo base_url(); ?>teleproposal/addCustomer",
                type: "POST",
                data: all_data,
                async: false,
                dataType: "json",
                beforeSend: function() {
                 //   set_session();
                },
                success: function (response)
                {
                    if (response.status == true) {
                        displayMsg("success", response.message);
                        $("#hidden_customer_section").val(1);


                    } else {
                        displayMsg("error", response.msg);
                        $(".btn-primary").show();
                        return false;
                    }
                }
            });
        }
    });

    $(document).on("blur", "#agent_id", function()
    {

        $("#tl_id").val('');
        $("#tl_name").val('');

        $("#agent_name").val('');
        $("#imd_code").val('');

        $("#axis_lob option:selected").removeAttr("selected");

        $("#axis_location option:selected").removeAttr("selected");

        $("#axis_vendor option:selected").removeAttr("selected");
        var agent_code = $(this).val();
        var axis_process=$('#saxis_process').val();

        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/get_agent_details",
            type: "POST",
            async: false,
            data: {agent_id : agent_code,axis_process:axis_process},
            dataType: "json",

            success: function (response)
            {
                if(response !== null && response !== '')
                {
                    if(response.tl_name != null){
                        var tl_name = response.tl_name.toUpperCase();
                    }else{
                        var tl_name = '';
                    }

                    //$("#axis_location option:contains("+response.center+")").attr("selected", true);

                    $("#tl_id").val(response.tl_emp_id);
                    $("#tl_name").val(tl_name);
                    $("#tl_id").val(response.tl_emp_id);
                    $("#agent_name").val(response.base_agent_name.toUpperCase());

                    //$("#axis_process").html("<option value="+response.axis_process+" selected>"+response.axis_process+"</option>");

                    // $("#axis_vendor").html("<option value="+response.vendor+" selected>"+response.vendor+"</option>");
                    // $("#axis_location").html("<option value="+response.center+" selected>"+response.center+"</option>");

                    $("#imd_code").val(response.imd_code);

                    //$("#axis_lob").val(response.axis_lob_id);
                    setTimeout(function(){ $("#axis_lob option:contains("+response.lob+")").attr("selected", true); });


                    $('#axis_location option:contains(' + response.center + ')').each(function(){
                        if ($(this).text() == response.center) {
                            $(this).prop('selected', 'selected');
                            return false;
                        }
                        return true;
                    });

                    $("#axis_location").trigger('change');

                    setTimeout(function(){ $("#axis_vendor option:contains("+response.vendor+")").attr("selected", true);

                        $("#axis_vendor").trigger('change');}, 2000);


                }else{
                    $("#tl_id").val('');
                    $("#tl_name").val('');

                    $("#agent_name").val('');
                    $("#imd_code").val('');

                    $("#axis_lob option:selected").removeAttr("selected");

                    $("#axis_location option:selected").removeAttr("selected");

                    $("#axis_vendor option:selected").removeAttr("selected");


                }




            }
        });

    });
    jQuery.validator.addMethod("mob", function(value, element) {
        return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
    }, "Enter valid 10 digit No. starting with 6 to 9.");

    jQuery.validator.addMethod("lettersonlys", function(value, element) {
        return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
    }, "Letters only please");

    function getPlanDetails(plan_id) {

        data = {};
        if (plan_id != '') {

            data.plan_id = plan_id;
        }

        if ($("#creditor_id").val() != '') {

            data.creditor_id = $("#creditor_id").val();
        }

        $.ajax({

            url: "<?php echo base_url('customerleads/getPlanDetailsForLead'); ?>",
            method: "POST",
            data: data,
            dataType: 'json',
            success: function(response) {

                if (response.success) {

                    if (response.min_age > 0 && response.max_age > 0) {

                        $("#dob").removeAttr('disabled');
                        $("#dob").datepicker("option", "yearRange", response.max_age + ':' + response.min_age);

                        var date = new Date();
                        var maxDate = date.getDate() + '-' + (date.getMonth() + 1) + '-' + response.min_age;
                        $("#dob").datepicker("option", "maxDate", maxDate);

                        var minDate = date.getDate() + '-' + (date.getMonth() + 1) + '-' + response.max_age;
                        $("#dob").datepicker("option", "minDate", minDate);

                        $("#dob").datepicker("refresh");
                    }
                }
            }
        });
    }

    function changeGender(salutation) {
        //alert(salutation);return false;
        var optionval = "";
        if (salutation == "Mr" || salutation == "Master") {
            optionval = '<option value="Male">Male</option>';
        } else if (salutation == "Dr") {
            optionval = '<option value="Male">Male</option><option value="Female">Female</option>';
        } else {
            optionval = '<option value="Female">Female</option>';
        }

        $("#gender").html(optionval);
        $("#gender").select2();
    }

    function coapplicant(val) {
        if (val == "Y") {
            $("#coapplicant").show();
        } else {
            $("#coapplicant").hide();
        }
    }

    function getPlansData(creditor_id) {
        //alert(creditor_id);return false;
        if (creditor_id != "") {
            //$("#plan_id").html("<option value=''>Select</option>");
            //$("#plan_id").select2();
            $.ajax({
                url: "<?php echo base_url(); ?>customerleads/getPlans",
                data: {
                    creditor_id: creditor_id
                },
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    if (res['status'] == "success") {
                        if (res['option'] != "") {
                            $("#plan_id").html("<option value=''>Select</option>" + res['option']);
                            $("#plan_id").select2();
                        } else {
                            $("#plan_id").html("<option value=''>Select</option>");
                            $("#plan_id").select2();
                        }
                    } else {
                        $("#plan_id").html("<option value=''>Select</option>");
                        $("#plan_id").select2();
                    }
                }
            });
        }
    }

    $(document).ready(function() {

        $("#dob").datepicker({
            dateFormat: 'dd-mm-yy',
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100Y:-18Y",
            maxDate: "-18Y",
            minDate: "-56Y +1D"
        });

    });


    var vRules = {
        creditor_id: {
            required: true
        },
        plan_id: {
            required: true
        },
        sm_id: {
            required: true
        },
        salutation: {
            required: true
        },
        first_name: {
            required: true,
            lettersonlys: true
        },
        //middle_name: { required: true, lettersonlys: true },
        last_name: {
            required: true,
            lettersonlys: true
        },
        gender: {
            required: true
        },
        dob: {
            required: true
        },
        email_id: {
            required: true,
            email: true
        },
        mobile_number: {
            required: true,
            mob: true
        },
        // lan_id: {
        // 	required: true
        // },
        // loan_amt: {
        // 	required: true,
        // 	number: true
        // },
        // tenure: {
        // 	required: true,
        // 	digits: true,
        // 	minlength: 1,
        // 	maxlength: 2
        // },
        // coapplicant_no: {
        // 	required: true,
        // 	digits: true,
        // 	max: 9
        // },
        location_id: {
            required: true
        }
    };

    var vMessages = {
        creditor_id: {
            required: "Please select creditor."
        },
        plan_id: {
            required: "Please select plan."
        },
        sm_id: {
            required: "Please select sm."
        },
        salutation: {
            required: "Please select salutation."
        },
        first_name: {
            required: "Please enter first name."
        },
        //middle_name: { required: "Please enter middle name." },
        last_name: {
            required: "Please enter last name."
        },
        gender: {
            required: "Please select gender."
        },
        dob: {
            required: "Please enter DOB."
        },
        email_id: {
            required: "Please enter valid email id."
        },
        mobile_number: {
            required: "Please enter mobile number."
        },
        lan_id: {
            required: "Plese enter Lan ID."
        },
        loan_amt: {
            required: "Please enter loan amount."
        },
        tenure: {
            required: "Please enter tenure."
        },
        coapplicant_no: {
            required: "Please enter no. of co-applicants."
        },
        location_id: {
            required: "Please select location."
        }
    };
    $.validator.addMethod(
        "validate_agent",
        function (value, element, param) {

            var count = 1;
            if($("#agent_id").val().length >= 1) {
                var agent_id = $("#agent_id").val();
                var axis_process=$('#saxis_process').val();

                $.ajax({
                    url: "<?php echo base_url(); ?>teleproposal/get_agent_details",
                    type: "POST",
                    async: false,
                    data: {agent_id : agent_id,axis_process:axis_process},
                    dataType: "json",
                    success: function (response) {

                        if(response != null && response != '' ) {
                            count = 0;
                        }else{
                            count = 1;
                        }
                    }
                });
            }
            else
            {
                count = 1;
            }
            if(count == 1){

                $("#tl_id").val('');
                $("#tl_name").val('');
                $("#tl_id").val('');
                $("#am_id").val('');
                $("#am_name").val('');
                $("#om_id").val('');
                $("#om_name").val('');
                $("#agent_name").val('');
                return false;}else{return true;}
        },
        "Base Agent Id Is not available in master."
    );
    $("#agent_details").validate({
        rules:  {

            axis_location:
                {
                    required: true,
                },
            axis_vendor:
                {
                    required: true,
                },
            axis_lob:
                {
                    required: true,
                },
            axis_process:
                {
                    required: true,
                },
            agent_id:
                {
                    required: true,
                    validate_agent: true,

                },

        },
        messages: {
        },
        submitHandler: function(form) {
            $("#agent_details").ajaxSubmit({
                url: "<?php echo base_url(); ?>teleproposal/addAgent",
                type: 'post',
                dataType: 'JSON',
                cache: false,
                clearForm: false,
                beforeSubmit: function(arr, $form, options) {
                    //$(".btn-primary").hide();
                    //return false;
                },
                success: function(response) {
                    //$(".btn-primary").show();
                    if (response.status == true) {
                        displayMsg("success", response.message);
                        $("#hidden_agent_section").val(1);

                    } else {
                        displayMsg("error", response.msg);
                        $(".btn-primary").show();
                        return false;
                    }
                }
            });
        }
    });


    //checkvalidInput
    $("body").on("keyup", ".checkvalidInput", function(e) {

        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^0-9]/g, function(str) {
                    return "";
                })
            );
        }
        return;
    });
    $('.checkvalidInputText').keypress(function(e) {
        var regex = new RegExp(/^[a-zA-Z\s]+$/);
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });

    document.title = "Customer Lead";


    $("#patForm").validate({
        ignore: ".ignore",
        focusInvalid: true,
        rules: {
            sum_insure: {
                required: true
            },
            anuual_income:{
                required: true,
                //valid_annual_income : true,
            },
            occupation:{
                required: true
            },
            familyConstruct: {
                required: true
            },
            premium: {
                required: true
            },
            'businessType': {
                required: true
            },
            'family_members_id[]': {
                notEqual: ".family_members_id1"
            },
            'family_gender[]': {
                required: true
            },
            'first_name[]': {
                required: true
            },
            /*'last_name[]': {
        required: true
        },*/
            'family_date_birth[]': {
                required: true
            },
            'age[]': {
                required: true
            },
            'age_type[]': {
                required: true
            },
            /*'mem_email_id[]': {
        required: true,
        validateEmail: true,
        },
        'mem_mob_no[]': {
        required: true,
        valid_mobile: true
        },*/


        },

        messages: {
            valid_annual_income : 'SI '
        },
        submitHandler: function (form) {
            debugger;
            //form.preventDefault();
           // set_session();
            //alert("form validated"+$("#premium").val());
            if (!Number($("#total_premium").text())) {

              //  swal("Alert", "Invalid Premium", "warning");
              //  return;
            }
            if($("#total_premium").text() == 'undefined')
            {
               // swal("Alert", "Invalid Premium", "warning");
               // return;
            }

            var family_construct = $('#patFamilyConstruct option:selected').val();
            var result = family_construct.split('+');

            var add_length = [];
            add_length.push(parseInt(result[0]));
            add_length.push(parseInt(result[1]));


            var i;
            var TableData2 = [];
            var TableData3 = [];
            var TableData5 = [];
            var TableData6 = [];
            for (i = 0; i < sum(add_length); i++) {

                var TableData = [];
                var TableData4 = [];

                $(".disease"+i).find('input:checkbox').each(function () {
                    if($(this).is(":checked")){
                        TableData4.push($(this).val());
                    }
                });

                TableData3[i] = TableData4;

                if(TableData4.length > 0){
                    TableData6.push(1);
                }

                $(".disease"+i).find('#mydatasmember tr').each(function (row, tr)
                {
                    ////debugger;
                    var content = $(tr).find('.mycontent').val();

                    if($(tr).find('input[name="' + content + '"]:checked').val() == 'Yes'){
                        TableData5.push(1);
                    }

                    TableData[row] =
                        {
                            "question": content,
                            "format": $(tr).find('input[name="' + content + '"]:checked').val(),
                        }


                });
                TableData2[i] = TableData;

            }

            if(TableData6.length > 1){
                displayMsg("error", "Only one member is allow with Chronic Disease!");


                //return;
            }

            if(TableData5.length > 0){
              //  text: "Cannot proceed with Chronic Disease Yes",

                displayMsg("error", "Cannot proceed with Chronic Disease Yes!");
return;
            }


            /*var TableData = [];

            var LabelData = [];
            $('#mydatasmember tr').each(function (row, tr)
            {
                var LabelData = {};

                var label_content;
                var tds = $(tr).find('td:eq(0)').text();
                var label = $(tr).find('.label_id').text();
                var content = $(tr).find('.mycontent').val();

                TableData[row] =
                {
                    "question": content,
                    "format": $(tr).find('input[name="' + content + '"]:checked').val(),
                }

            });*/

            $('[name="family_date_birth[]"]').each(function(){
                //code
                $(this).prop("disabled", false);
            });
            //debugger;
            selectChange = $('#patForm').closest("form");
            set_non_editable(selectChange,1);
            var policyNo = $("#sum_insures :selected").attr("data-policyno");
            var plan_name = $('#plan_name').val();//$("#pr_name").html();
            var premium_pat = $('#patFamilyConstruct option:selected').data('premium');
            if(premium_pat == '' || premium_pat == undefined){
                var premium_pat = $("#total_premium").text();
            }
            selectChange.find("select[name='family_salutation[]']").removeAttr('disabled', 'disabled');
            var familyDataType = $("#sum_insures :selected").attr("data-type");
            //$("#patFamilyConstruct").removeAttr('disabled', 'disabled');
            //$("#plan_name").removeAttr('disabled', 'disabled');
            //$("#sum_insures").removeAttr('disabled', 'disabled');
            $(".family_members_id1").prop('disabled',false);
            var family_members_id = $(".family_members_id1").val();

                var finalPremium = premium_pat;

            var form = $("#patForm").serialize() + "&plan_name="+$('#plan_name').val()+ "&GCI_optional="+$("input[name='GCI_optional']:checked").val()+"&premium=" + finalPremium + "&policyNo=" + $("#policy_no").val() + "&familyDataType=" + familyDataType + "&declare=" + JSON.stringify(TableData2)+ "&chronic=" + JSON.stringify(TableData3);

            var master_policy = $("#master_policy_number").val();
            var GCI_optional = $("input[name='GCI_optional']:checked").val();
            /*if($('#annual_income').val() != '' || $('#annual_income').val() != undefined){
                var annualIncome = $('#annual_income').val();
                insert_annual_income(emp_id,annualIncome,'insert');
            }*/

            var agent_details = $("#hidden_agent_section").val();
            var customer_details = $("#hidden_customer_section").val();
            if(agent_details == 0 || customer_details == 0){
                //text:"Please SAVE the values entered so far to proceed with new Section!",
                displayMsg("error", "Please SAVE the values entered so far to proceed with new Section!");



                return false;

            }
            //debugger;


            form = form +"&hidden_policy_id="+$('#hidden_policy_id').val()+"&hidden_deductable="+$('#hidden_deductable').val()+"&sum_insure="+$("#sum_insures").val()+"&familyConstruct="+$("#patFamilyConstruct").val();

            /*if($("#plan_name").val() == 'T03'){
                alert(premium)
                form = form + "&premium="+$("#premium").val();
            }*/
            //alert(is_self_purchased);
            if(($("#plan_name").val() == 'T03' && $('#hidden_policy_id').val() == TELE_HEALTHPROINFINITY_GHI_GPA && is_self_purchased == 1) || (is_self_purchased == 1 && $("#plan_name").val() == 'T01')){
                if(parseInt(self_purcased_with_si) < parseInt($('#sum_insures').val())){
                   // swal("Alert", 'SI should be less or equal to '+self_purcased_with_si+' !', "warning");
                    displayMsg("error", 'SI should be less or equal to '+self_purcased_with_si+' !');

                    return false;
                }
            }
            //console.log(form);
         //   ajaxindicatorstart("Please wait ... ");
            $.post("<?php echo base_url(); ?>teleproposal/family_details_insert", form, function (e)
            {
                $('[name="family_date_birth[]"]').each(function(){
                    //code
                    //$(this).prop("disabled", true);
                });
           //     ajaxindicatorstop();



                var data = JSON.parse(e);

                if (!data.status)
                {


                    if (data.check == "declaration")
                    {
                        $('#confr').hide();
                    }
                    displayMsg("error", data.message);



                    return;

                }
                if(family_members_id == 0)
                {
                    set_non_editable(selectChange,0)
                }
                displayMsg("success", data.message);
                $("#hidden_policy_section").val(1);

              //  swal("Alert", data.message, "success");
                $("#B_data").show();
                $("#C_data").hide();
                $("#D_data").hide();
                $("#E_data").hide();
                $("#B_remark").hide();
                $(".B").hide();
                $('#chronic th input[type="checkbox"]').prop('checked',false);
                $(".tt").empty();
                $("#patFormSubmit").closest("form").find("select[name='family_members_id[]']").css("pointer-events",'auto');
                $('#confr').show();
                $("#patForm").find("input[name='edit']").val("0");
                //$("#sum_insures").attr('disabled', 'disabled');
                //$("#patFamilyConstruct").attr('disabled', 'disabled');
                //$('input[name=GCI_optional]').attr("disabled",true);
                $("#premium").attr('disabled', 'disabled');
                $("#plan_name").attr('disabled', 'disabled');
                if($('#plan_name').val() == 'T03'){
                    $("#sum_insures").attr('disabled', 'disabled');
                    $("#patFamilyConstruct").attr('disabled', 'disabled');
                }
                var sum_insures = $("#sum_insures").val();
                var patFamilyConstruct = $("#patFamilyConstruct").val();
                var premium = $("#premium").val();

                document.getElementById("patForm").reset();
                $("#master_policy_number").val(master_policy);
                $("#sum_insures").val(sum_insures);
                $("#patFamilyConstruct").val(patFamilyConstruct);
                $("#premium").val(premium);
                // alert(plan_name);
                $("#plan_name").val(plan_name);

                //$("#patFormSubmit").hide();
                if(GCI_optional == 'No'){
                    $('#GCI_no').prop('checked', true);
                }else{
                    $('#GCI_yes').prop('checked', true);
                }
                $("#hidden_policy_section").val(1);
                addDependentForm("patTable", JSON.parse(e));
                //$("#family_members_id1").trigger("change");
                proposal_create_check();
            });

            //upendra - 29-06-2021
            if($("#plan_name").val() == "T01" || $("#plan_name").val() == "T03"  ){
               // getPremiumNew();
            }

        }
    });

    function proposal_create_check()
    {debugger;

        /*already Proposal Created data show*/
        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/check_proposal",

            //url: "/tele_proposal_created",
            type: "POST",
            async: false,
            dataType: "json",
            success: function (response)
            {
                ////debugger;

                if(response.proposal_status == 'Yes'){
                    $("#emp_agent_data").css('pointer-events','none');
                    $("#firstname").attr("readonly", false);
                    $("#lastname").attr("readonly", false);
                    $("#plan_name").css('pointer-events','none');
                    $("#sum_insures").css('pointer-events','auto');
                    $("#occupation").css('pointer-events','none');
                    $("#patFamilyConstruct").css('pointer-events','auto');
                    $("#annual_income").attr("readonly", false);
                    $('input[name=GCI_optional]').attr("disabled",true);
                    $(".del_btn_member").hide();
                    proposal_created = 1;
                    show_popup_new = 0;
                }else{
                    $("#emp_agent_data").css('pointer-events','auto');
                    $("#firstname").attr("readonly", true);
                    $("#lastname").attr("readonly", true);
                    $("#plan_name").css('pointer-events','auto');
                    $("#sum_insures").css('pointer-events','auto');
                    $("#occupation").css('pointer-events','auto');
                    $("#patFamilyConstruct").css('pointer-events','auto');
                    $("#annual_income").attr("readonly", false);
                    $('input[name=GCI_optional]').attr("disabled",false);
                    $(".hide_proposal").show();
                    proposal_created = 0;
                }

                if(response.status == 'Yes')
                {
                    proposal_payment_done = 1;
                    $("#edit_btn_custsection").hide();
                    $("#edit_nominee").hide();
                    $("#edit_payment").hide();
                    $("#patFormSubmit").hide();
                    $(".hide_proposal").hide();
                    $("#av_remark").val(response.audit_data.remarks);
                    $(".del_p").hide();
                    $("#agent_detail").hide();
                    $("#update_emp").hide();
                    $("#submit_nominee").hide();
                    $("#payment_submit").hide();
                    $("#disposition option:contains('Payment pending')").attr("selected", true);
                    $("#disposition").trigger('change');
                    $("#sub_isposition").val(45);
                    $("#sub_isposition").trigger('change');
                    $("#av_remark").addClass('ignore');
                    //$('#edit_payment').show();
                    payment_details_submit = false;



                    /*nominee data*/



                    agent_cust_prefilled(response);
                    /*axis data*/
                    // var emp = response.base_agent_details;
                    // var axis_det = response.axis_details;
                    // $('#axis_lob').html('<option value ="'+emp.axis_lob+'" selected>'+axis_det.axis_lob+'</option>');

                    // $('#axis_vendor').html('<option value ="'+emp.axis_vendor+'" selected>'+axis_det.axis_vendor+'</option>');
                    // /*employee data*/

                    // $('#axis_location').val(emp.axis_location);
                    // $('#agent_id').val(emp.base_agent_id);
                    // $('#agent_name').val(emp.base_agent_name);
                    // $("#comAdd2").val(emp.comm_address);
                    // $("#dobdate").val(emp.preferred_contact_date);
                    // $("#preferred_contact_time").val(emp.preferred_contact_time);
                    // $("#av_remark").val(emp.av_remark);
                    // $("#comAdd3").val(emp.comm_address1);
                    // $("#mobile_no2").val(emp.emg_cno);
                    // $('#tl_id').val(emp.tl_emp_id);
                    // $('#tl_name').val(emp.tl_name);
                    // $('#om_name').val(emp.om_name);
                    // $('#om_id').val(emp.om_emp_id);
                    // $('#am_id').val(emp.am_emp_id);
                    // $('#am_name').val(emp.am_name);
                    $('#confr1').hide();
                }
                else
                {
                    proposal_payment_done = 0;
                    //	$("#edit_btn_custsection").show();
                    // $("#edit_nominee").show();
                    // $("#edit_payment").show();
                    $("#av_remark").val('');
                    $(".hide_proposal").show();
                    if(response.proposal_status == 'Yes'){
                        $(".del_btn_member").hide();
                    }
                    // console.log(JSON.stringify(response));
                    agent_cust_prefilled(response);
                    $("#patFormSubmit").show();
                    $(".del_p").show();
                    $('#confr1').show();
                    $("#agent_detail").show();
                    $("#submit_nominee").show();
                  //  hideEditButtons();
                    $("#payment_submit").show();
                   // hideEditButtons();
                    $("#av_remark").removeClass('ignore');
                }

                if(response.emp_details.GCI_optional == 'No'){
                    $('#GCI_no').prop('checked', true);
                }else{
                    $('#GCI_yes').prop('checked', true);
                }

            }
        });
        var boxlen = $('.commonCls').length;
        if(boxlen > 0){
            $("#plan_name").attr('disabled', 'disabled');
            if($('#plan_name').val() == 'T03'){
                $("#sum_insures").attr("disabled", "disabled");
                $("#patFamilyConstruct").attr("disabled", "disabled");
            }
        }
        if($("#plan_name").val() == 'T01'){


            $('.GCIOptionalDiv').show();
        }else{
            $('.GCIOptionalDiv').hide();
        }


    }

    jQuery.validator.addMethod("checkspace_nominee", function(value, element, params) {
        var z = value.trim();
        if(!z){
            return false;
        }


        return true;
    });
    $("#nominee_data").validate({
        ignore: ".ignore",
        focusInvalid: true,
        rules:  {

            nominee_fname:
                {
                    required: true,
                    checkspace_nominee: true
                },
            nominee_lname:
                            {
                                required: true,
                                checkspace_nominee: true
                            },

            nominee_dob:
                {
                    required: true
                },
            nominee_relation:
                {
                    required: true
                },
            nominee_contact:
                {
                    //required: true,
                    valid_mobile: true
                },


        },
        messages: {
        },
        invalidHandler: function(form, validator)
        {

            validator.focusInvalid();
        },
        submitHandler: function (form)
        {
debugger;
            var agent_details = $("#hidden_agent_section").val();
            var customer_details = $("#hidden_customer_section").val();
            var policy_details = $("#hidden_policy_section").val();
            //alert(agent_details +"====="+customer_details+"====="+policy_details);

            if($('#disposition').val()==45||$('#disposition').val()==46){

                if(!$("#emp_agent_data").valid())
                {
                    displayMsg("error", "Please FILL Agent Details!");

                    return;
                }
                else if(!$("#emp_data").valid())
                {

                    displayMsg("error", "Please FILL Customer Details!");


                    return;
                }
            }
            /* end  */
            else if(agent_details == 0 || customer_details == 0 || policy_details == 0){
                displayMsg("error", "Please SAVE the values entered so far to proceed with new Section!");


                return false;
            }


            var all_data = $("#nominee_data").serialize()+"&nominee_relation="+$("#nominee_relation").val()+"&nominee_dob="+$('#nomineedob').val()+"&nominee_contact="+$('#nominee_contact').val();
            $.ajax({
                url: "<?php echo base_url(); ?>teleproposal/tele_nominee_data_insert",
                type: "POST",
                data: all_data,
                async: false,
                dataType: "json",
                beforeSend: function() {
                 //   set_session();
                },
                success: function (response)
                {
                    var res = response;
                    if (res.status == false)
                    {


                        displayMsg("error", res.message);
                        return;
                    }
                    if (res.status == true)
                    {

                        displayMsg("success", res.message);
                        $("#hidden_nominee_section").val(1);


                    }
                }
            });
        }
    });
    $.validator.addMethod
    (

        "valid_mobile",
        function (value, element, param)
        {

            var re = new RegExp("^[6-9][0-9]{9}$");
            return this.optional(element) || re.test(value);
        },
        "Enter a valid 10 digit mobile number and starting from 6 to 9"
    );
    function get_fam_data(elem)
    {


        var gen = $("#nominee_relation").val();

        if(gen == 1)
        {
            if($("#gender1").val() == 'Male')
            {

                $("#nominee_gender").val("Female");
                $(".nominee_female_gen").css('display','block');
                $(".nominee_male_gen").css('display','none');
                $("#nominee_salutation").val("Mrs");

            }
            else if($("#gender1").val() == 'Female')
            {

                $("#nominee_gender").val("Male");
                $("#nominee_salutation").val("Mr");
                $(".nominee_female_gen").css('display','none');
                $(".nominee_male_gen").css('display','block');

            }
        }
        else
        {
            var dataOpt = $("select[name='nominee_relation'] :selected").attr("data-opt");
            if(dataOpt == 'Male')
            {$(".nominee_female_gen").css('display','none');
                $("#nominee_salutation").val("Mr");
                $(".nominee_male_gen").css('display','block');}else if(dataOpt ==  'Female'){$("#nominee_salutation").val("Ms");$(".nominee_female_gen").css('display','block');
                $(".nominee_male_gen").css('display','none');}
            $("#nominee_gender").val(dataOpt);
        }
        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/family_details_relation",
            type: "POST",
            data: {
                relation_id: elem.value,

            },
            async: false,
            dataType: "json",
            beforeSend: function() {
               // set_session();
            },
            success: function (response)
            {


                $("#nominee_fname").val("");
                $("#nominee_lname").val("");
                $("input[type='text'][name='nominee_dob']").val("");
                $('#nominee_contact').val("");
                var family_detail = response.family_data;


                if (family_detail.length != 0)
                {
                    if (family_detail[0].fr_id == "2" || family_detail[0].fr_id == "3")
                    {
                        $("#body_modal").html("");
                        for ($i = 0; $i < family_detail.length; $i++)
                        {

                            $("#body_modal").append('<input type="radio" name ="radio_option" value= ' +family_detail[$i]["policy_member_id"] +"> " +family_detail[$i].policy_member_first_name +"<br>");
                        }
                        $("#myModal").modal();


                    }
                    else if (family_detail[0].fr_id == "0")
                    {

                        $("#nominee_fname").val(family_detail[0].policy_member_first_name.toUpperCase());
                        $("#nominee_lname").val(family_detail[0].policy_member_last_name.toUpperCase());
                        $("#nominee_gender").val(family_detail[0].policy_mem_gender);
                        $("#nominee_email").val(family_detail[0].policy_member_email_id);
                        $("#nominee_contact").val(family_detail[0].policy_member_mob_no);
                        $("#dob").val(family_detail[0].policy_mem_dob);


                    } else
                    {

                        $('#nominee_salutation').attr('disabled',true);
                        if(family_detail[0].policy_member_first_name.toUpperCase()){
                            $("#nominee_fname").val(family_detail[0].policy_member_first_name.toUpperCase());
                            $("#nominee_fname").attr('readonly',true);
                        }
                        if(family_detail[0].policy_member_last_name.toUpperCase()){
                            $("#nominee_lname").val(family_detail[0].policy_member_last_name.toUpperCase());
                            $('#nominee_lname').attr('readonly',true);
                        }
                        if(family_detail[0].policy_mem_gender){
                            $("#nominee_gender").val(family_detail[0].policy_mem_gender);
                            $('#nominee_gender').attr('readonly',true);
                        }
                        if(family_detail[0].policy_mem_dob){
                            $("input[type='text'][name='nominee_dob']").val(family_detail[0].policy_mem_dob);
                            $('#nomineedob').attr('disabled',true);
                        }
                        if(family_detail[0].policy_member_mob_no){
                            $("input[type='text'][name='nominee_contact']").val(family_detail[0].policy_member_mob_no);
                            $('#nominee_contact').attr('disabled',true);
                        }
                        if(family_detail[0].policy_member_email_id){
                            $("input[type='text'][name='nominee_email']").val(family_detail[0].policy_member_email_id);
                            $('#nominee_email').attr('disabled',true);
                        }


                    }

                }
            }
        });
    }
    $(document).on("change","#sub_isposition",function(){
        var z = $(this);
        enable_disable_proposal(z);

    });

    function enable_disable_proposal(data){
        if($('#plan_name').val()){


            //updated by upendra on 09-04-2021
            if($('#plan_name').val() == 'T03' || $("#plan_name").val() == 'T01'){
                $("#policy_declare_new").show();
                $("#myGhdMember").hide();

                if( $('#hpi_ghd_td').length ) {
                    // alert('hi');

                    //updated by upendra on 06-04-2021
                    // var emp_id_1 = "625157";

                    var lead_id_1 = $('#hidden_lead_id').val();

                    // alert(emp_id_1);
                    // $('#hpi_ghd_td').html(emp_id_1);

                    $.ajax({
                        url: "/tele_get_member_dropdown",
                        type: "POST",
                        data: {
                            lead_id_1:lead_id_1
                        },
                        async: false,
                        // dataType: "json",
                        success: function (response)
                        {
                            // alert(response);
                            $('#hpi_ghd_td').html(response);
                            if(proposal_payment_done == 0){
                                proposal_create_check();
                            }
                        }
                    });
                }





            }else if($('#plan_name').val() == 'R06'){ //ankita ghd changes
                if( $('#hpi_ghd_td').length ) {
                    // alert('hi');

                    //updated by upendra on 06-04-2021
                    // var emp_id_1 = "625157";

                    var lead_id_1 = $('#hidden_lead_id').val();

                    // alert(emp_id_1);
                    // $('#hpi_ghd_td').html(emp_id_1);

                    $.ajax({
                        url: "/tele_get_member_dropdown",
                        type: "POST",
                        data: {
                            lead_id_1:lead_id_1
                        },
                        async: false,
                        // dataType: "json",
                        success: function (response)
                        {
                            // alert(response);
                            $('#hpi_ghd_td').html(response);
                            if(proposal_payment_done == 0){
                                proposal_create_check();
                            }

                        }
                    });
                }
                $("#policy_declare_new").hide();
                $("#myGhdMember").show();

            }
            else if($('#plan_name').val() == 'T01'){

                $("#policy_declare_new").show();
                $("#myGhdMember").hide();
            }else{
                $("#policy_declare_new").hide();
                $("#myGhdMember").show();
            }
            $("#show_hide_proposal").show();
            if($(data).val() == 45 || $(data).val() == 5){
                $("#show_hide_proposal").show();
            }
                //upendra - maker/checker - 30-07-2021
            // else if($(data).val() == 55 && $("#hidden_is_maker_checker").val() == "yes"){
            else if ($(data).val() == 55 || $(data).val() == 5 && $("#hidden_is_maker_checker").val() == "yes") {

                $("#show_hide_proposal").show();
            }
            else{
                $("#show_hide_proposal").hide();
            }
            if($('#disposition').val() == 29){
                $('#dobdate').removeClass('ignore');
                $('#preferred_contact_time').removeClass('ignore');
            }else{
                $('#dobdate').addClass('ignore');
                $('#preferred_contact_time').addClass('ignore');
            }
        }
    }

    $("#payment_details").validate({
        ignore: ".ignore",
        focusInvalid: true,
        rules:
            {
                disposition:
                    {
                        required: true,

                    },
                sub_isposition:{
                    required:true,
                },
                preferred_contact_date:
                    {
                        required: true,

                    },
                preferred_contact_time:
                    {
                        required: true,
                        time_validate:true,
                    },
                /*av_remark:
                {
                    required: true,

                },*/
            },
        messages: {

        },
        submitHandler: function (form)
        {
            var agent_details = $("#hidden_agent_section").val();
            var customer_details = $("#hidden_customer_section").val();
            var policy_details = $("#hidden_policy_section").val();
            var nominee_details = $("#hidden_nominee_section").val();

            if($('#disposition').val()==45||$('#disposition').val()==46){

                if(!$("#emp_agent_data").valid())
                {
                    displayMsg("error", "Please FILL Agent Details!");

                    return;
                }
                else if(!$("#emp_data").valid())
                {

                    displayMsg("error", "Please FILL Customer Details!");


                    return;
                }
                else if($('#plan_name').val()==''||$('#sum_insures').val()==''||$('#familyConstruct').val()==''){
                    displayMsg("error", "Please FILL Policy Details!");

                    return;


                }
                else if(!$("#nominee_data").valid())
                {                    displayMsg("error", "Please FILL Nominee Details!");


                    return;
                }
                else if(!$("#payment_details").valid()){
                                    displayMsg("error", "Please FILL Payment Details!");

                    return;
                }

            }
            /* end  */
            if(agent_details == 0 || customer_details == 0 || policy_details == 0 || nominee_details == 0){
                displayMsg("error", "Please SAVE the values entered so far to proceed with new Section!");


                return false;
            }
            var all_data = $("#payment_details").serialize();
            $.ajax({
                url: "<?php echo base_url(); ?>teleproposal/tele_payment_details_insert",
                type: "POST",
                data: all_data,
                async: false,
                dataType: "json",
                success: function (response)
                {
                    debugger;
                    if(response.disabled == 'Close'){
                        $("#sub_isposition").attr("disabled", true);
                        $("#disposition").attr("disabled", true);

                    }

                    $("#payment_details_tbody").html('');
                    var str = '';
                    //var type_dis = '';
                    $.each(response.data, function(index, item) {
                        //	if(item.type == 'DO'){type_dis = 'DO'}else{type_dis = 'AV'}

                        str+= '<tr><td>'+item.Dispositions+'</td><td> '+(item["Sub-dispositions"])+'</td><td>'+item.date+'</td><td>'+item.agent_name+'</td><td>'+((item.remarks) ? item.remarks : '')+'</td><td>'+((item.type) ? item.type : 'AV')+'</td><</tr>';
                    });
                    $("#payment_details_tbody").html(str);
                    if(proposal_payment_done == 0){
                        //	$('#edit_payment').show();
                        //ankita junk dedupe changes hide edit button
                        var not_allowed = ["Junk","Not Interested","Not Eligible"];
                        if (not_allowed.indexOf($("#disposition option:selected").text()) > -1) {
                            $('#edit_payment').hide();
                        } else {
                            $('#edit_payment').show();
                        }
                    }
                    displayMsg("success", "Payment Details Saved");
                    //swal("Success", "Payment Details Saved", "success");
                    $('#hidden_payment_section').val('1');

                    payment_details_submit = false;
                }
            });


        }

    });
    function get_sub_disposition(data){

        var sub_disposition = $('option:selected', data).text().replace(/ /g,"_");
        $("#sub_isposition").prop("disabled", false);$("#sub_isposition").val('');
        $('[name=show_hide_sub_dispositions]').hide();
        $("."+sub_disposition).show();

        /* updated on 09-02-2021 by Akash_Chawan */
        if(sub_disposition=='Payment_pending'||sub_disposition=='Payment_done'){
            $('#pre_send').hide();
            if(proposal_payment_done == 0){
                proposal_create_check();
            }
        }else{
            $('#hidden_agent_section').val('1');
            $('#hidden_customer_section').val('1');
            $('#hidden_nominee_section').val('1');
            $('#hidden_payment_section').val('1');
            $('#hidden_policy_section').val('1');
            $('#hidden_payment_section').val('1');
            $('#pre_send').show();
        }

        if(($("#disposition").val())=='29'){
            // alert("fu");
            $(".follow_up_req").html('*');
        }else{
            // alert("nfu");
            $(".follow_up_req").html('');
        }
        /* end */
    }
    $("#proposal_data").validate({
        ignore: ".ignore",
        focusInvalid: true,
        rules:
            {

                auto_renewal:
                    {
                        required: true,

                    },

            },
        messages: {

        },
        submitHandler: function (form)
        {
            debugger;
          //  set_session();
         //   ajaxindicatorstart();

            /*if(!$("#payment_details").valid()){
                ajaxindicatorstop();
                return;
            }
            if(!$("#emp_agent_data").valid())
            {

                ajaxindicatorstop();
                return;
            }
            if(!$("#emp_data").valid())
            {

                ajaxindicatorstop();
                return;
            }
            if(!$("#nominee_data").valid())
            {

                ajaxindicatorstop();
                return;
            }
*/

            //var mydeclare = $("#mydatas input[type='radio']:checked").val();
            // 03-02-2022 - SVK005
            var mydeclare = $("#mydatas input[type='checkbox']:checked").val();
           // alert(mydeclare);

            var mydeclare2 = $("#mydatas1 input[type='radio']:checked").val();
            if (mydeclare2 == 'Yes')
            {
               // ajaxindicatorstop();
                swal("Alert","As the Group Health Declaration Question is Yes, the proposal cannot be processed", "warning");
                $("#myModal1").modal("hide");
                $("#sms_body").html('');
                return false;

            }

            if (mydeclare == 'No')
            {
             //   ajaxindicatorstop();
                //swal("Alert","As the Employee Declaration Question is No, the proposal cannot be processed", "warning");
              //  swal("Alert","In order to proceed further please check the disclaimer", "warning");
                $("#myModal1").modal("hide");
                $("#sms_body").html('');
                return false;

            }
            //otp_generate();




            proposal_submit_data();
        }

    });
    function proposal_submit_data()
    {

        var emp_agent_data = $("#emp_agent_data").serializeArray();

        var TableData = [];

        var LabelData = [];


        $('#mydatas tr').each(function (row, tr)
        {
            var LabelData = {};

            var label_content;
            var tds = $(tr).find('td:eq(0)').text();



            var label = $(tr).find('.label_id').text();
            var content = $(tr).find('.mycontent').val();

            var mylabel = $(tr).find('.mycontents').val();

            var mylabval = $(tr).find('.mylabval').val();

            if (label != '')
            {
                LabelData = mylabel + ':' + mylabval;
            }
            else
            {
                LabelData = mylabval;
            }


            TableData[row] = {
                "question": content,
                //"format": $("#mydatas input[type='radio']:checked").val(),
                //// 03-02-2022 - SVK005
                "format": $("#mydatas input[type='checkbox']:checked").val(),
                "label": LabelData
            }

        });
        //var form = $("#emp_agent_data").serialize() +'&' +$("#proposal_data").serialize() + "&declares=" +JSON.stringify(TableData);
        var remarks = '';
        if($("#plan_name").val() == 'T01'){
            remarks = $(".myremark").val();
        }


        //updated by upendra on 09-04-2021
        if($("#plan_name").val() == 'T03' || $("#plan_name").val() == 'T01' || $("#plan_name").val() == 'R06'){
            remarks = '';
            var select_mem_length=$('.count_sr_ghd_infi').length;
            var remark_arr = [];


            for(var lp=0;lp<select_mem_length;lp++){
                var ghd_hfi_select_val = $("input[type='radio'][name='ghd_mem_radio_"+lp+"']:checked").attr("data");
                var ghd_hfi_relation_code = $("input[type='radio'][name='ghd_mem_radio_"+lp+"']:checked").attr("data-rc");
                var ghd_hfi_select_val_ans = $("input[type='radio'][name='ghd_mem_radio_"+lp+"']:checked").val();
                var ghd_hfi_text_val = $("#ghd_mem_text_"+lp).val();
                remark_arr.push({
                    member : ghd_hfi_select_val,
                    ans : ghd_hfi_select_val_ans,
                    remark : ghd_hfi_text_val,
                    relation_code : ghd_hfi_relation_code
                });
            }

            remarks = 	JSON.stringify(remark_arr);
            remarks =remarks.replace(/"/g, '\\"');

        }
        // alert(remarks);return false;
        var form = $("#proposal_data").serialize() + "&declares=" +JSON.stringify(TableData) + "&product_id=" +$("#plan_name").val() + "&remarks_new=" +remarks;
       // set_session();
        $.post("<?php echo base_url(); ?>teleproposal/proposal_validation", form, function (e)
        {debugger;
            var res = JSON.parse(e);
            if (res.status == false)
            {  displayMsg("error", res.message);

            return;
            }
            if (res.status == true)
            {

                //updated by upendra on 09-04-2021
                if($("#plan_name").val() == 'T01' || $("#plan_name").val() == 'T03'){
                    var plan_name_pass = $("#plan_name").val();
                    $('#summaryForm').attr('action', "/tele_summary?product_id="+plan_name_pass+"&leadid="+$('#hidden_lead_id').val()).submit();
                }else{
                   // alert();
                    setTimeout(function() {
                        var lead = $('#leadHidden').val();

                        var url = new URL(window.location.href);
                        var text = url.searchParams.get("text");
                        window.location.href = "<?php echo base_url(); ?>teleproposal/proposalSummary/?text=" + lead;
                    }, 3000);
                    //$("#summaryForm").submit();
                }



            }

            // $.ajax({
            // url: "/tls_payment_url_send",
            // type: "POST",
            // async: false,
            // data: {},
            // success: function(response) {
            // }
            // });

            // ajaxindicatorstop();
            // swal({
            // title: "Success",
            // text: "Proposal Submitted Successfully",
            // type: "success",
            // showCancelButton: false,
            // confirmButtonText: "Ok!",
            // closeOnConfirm: true,
            // allowOutsideClick: false,
            // closeOnClickOutside: false,
            // closeOnEsc: false,
            // dangerMode: true,
            // allowEscapeKey: false
            // },
            // function () {
            // tele_thank_you();

            // });


        });
    }
    function agent_cust_prefilled(response)
    {
        // console.log(response);
        // alert("check1");
        var emp = response.base_agent_details;
        var axis_det = response.axis_details;
        if(axis_det!= null && emp!= null)
        {
            /*$('#axis_lob').html('<option value ="'+emp.axis_lob+'" selected>'+axis_det.axis_lob+'</option>');*/

            // alert("check3");
            // alert(emp.new_remarks);

            $('#axis_lob').val(emp.axis_lob);

            $('#axis_vendor').html('<option value ="'+emp.axis_vendor+'" selected>'+axis_det.axis_vendor+'</option>');
            /*employee data*/
            if(emp.new_remarks != 0){$(".myremark").val(emp.new_remarks);}else{$(".myremark").val('');}

            // alert("check4");
            // alert(emp.new_remarks);

            //updated by upendra on 09-04-2021
            var product_id = response.emp_details.product_id;

//	if(product_id == "T03" && emp.new_remarks != null){
            if((product_id == "T03" || product_id == "T01" || product_id == 'R06') && (emp.new_remarks)){//ankita ped changes
                var new_remarks = emp.new_remarks;

                var new_remarks = new_remarks.replace(/\\/g, "");
                // alert(new_remarks);
                $.each(JSON.parse(new_remarks), function(index, value) {
                    // alert("doneeee");
                    var m = value['member'];
                    var a = value['ans'];
                    var r = value['remark'];

                    //   alert(a);
                    //   alert('ghd_mem_radio_'+index+'');return false;

                    $('input:radio[name="ghd_mem_radio_'+index+'"]').filter('[value="'+a+'"]').attr('checked', true);

                    if(a == 'yes'){
                        $('#ghd_mem_text_'+index).show();
                    }

                    $('#ghd_mem_text_'+index).val(r);



                });


                // $("#ghd_mem_text_0").val("testing");
            }
            //	$("#edit_btn_custsection").show();
            $('#imd_code').val(emp.imd_code);
            $('#axis_location').val(emp.axis_location);
            $('#agent_id').val(emp.base_agent_id);
            $('#agent_name').val(emp.base_agent_name.toUpperCase());
            $("#axis_process").html("<option value="+emp.axis_process+" seleccted>"+emp.axis_process+"</option>");
            $("#comAdd2").val(emp.comm_address);
            $("#dobdate").val(emp.preferred_contact_date);
            $("#preferred_contact_time").val(emp.preferred_contact_time);
            $("#av_remark").val(emp.av_remark);
            $("#comAdd3").val(emp.comm_address1);
            $("#mobile_no2").val(emp.emg_cno);
            $('#tl_id').val(emp.tl_emp_id);
            $('#tl_name').val(emp.tl_name.toUpperCase());
            if(emp.base_agent_id != ''){
                $("#hidden_agent_section").val(1);
            }

            /*$('#om_name').val(emp.om_name.toUpperCase());
        $('#om_id').val(emp.om_emp_id);
        $('#am_id').val(emp.am_emp_id);
        $('#am_name').val(emp.am_name.toUpperCase());*/
            $('#imd_code').val(emp.imd_code);
        }
        var nominee = response.nominee_data;
        if(nominee != null){
            $('#nominee_relation').val(nominee.fr_id);
            $('#nominee_fname').val(nominee.nominee_fname.toUpperCase());
            $('#nominee_lname').val(nominee.nominee_lname.toUpperCase());
            $('#nominee_gender').val(nominee.nominee_gender);
            $('#nominee_contact').val(nominee.nominee_contact);
            $('#nominee_email').val(nominee.nominee_email);
            $('#nomineedob').val(nominee.nominee_dob);
            $('#nominee_salutation').val(nominee.nominee_salutation);
            $("#hidden_nominee_section").val(1);
            if(proposal_payment_done == 0){
                $('#edit_nominee').show();
               // hideEditButtons();
            }

            $('#nominee_relation').attr('disabled',true);
            $('#nominee_fname').attr('readonly',true);
            $('#nominee_lname').attr('readonly',true);
            $('#nominee_salutation').attr('disabled',true);
            $('#nominee_gender').attr('readonly',true);
            $('#nomineedob').attr('readonly',true);
            $('#nomineedob').attr('disabled',true);
            $('#nominee_contact').attr('readonly',true);
            $('#nominee_email').attr('readonly',true);
        }

        /*proposer level good health declarartion*/
        var ghd = response.ghd_proposer;
        var len = ghd.length;
        var i;
        for (i = 0; i < len; i++)
        {
            var member = ghd[i];
            if(member.type == 'C1')
            {
                $("#C_data").show();
            }
            if(member.type == 'D1')
            {
                $("#D_data").show();
            }
            if(member.type != '')
            {
                $("#"+member.type+"_data").show();
                if(member.format == 'Yes')
                {
                    $("#" + member.type+"_1").prop("checked", true);
                    $("#"+member.type+"_remark").show();
                    $("#"+member.type+"_remark").val(member.remark);

                }
                else
                {
                    $("#" + member.type+"_2").prop("checked", true);
                    $("#"+member.remark+"_remark").css('display','none');
                }
            }


        }
        var declare_data = response.emp_declare;
        var lens = declare_data.length;
        for (i = 0; i < lens; i++)
        {
            var declare = declare_data[i];
            //console.log(declare);
            $('input:radio[name="' + declare.p_declare_id + '"][value="' + declare.format + '"]').attr('checked', true);
        }
    }
    function addDependentForm(tbody, elem)
    {
        ////debugger;
        var famrelarr = [];
        var addDependent = null;
        var premium_ghi = premium_gpa = premium_vtl = ind_premium =0;
        $("#add_more").empty();
        var i = 0;
        //console.log(elem);
        var length = 0;
        var members_arr = [];
        elem.data.forEach(function (e)
        {
            debugger;
            if(e['policy_sub_type_id'] == 1){
                members_arr.push(e['fr_id']);

            }
            ////debugger;
            //alert(e.policy_sub_type_id);

            if(e.policy_sub_type_id == 1)
            {
                premium_ghi = parseFloat(e.policy_mem_sum_premium);

            }
            if(e.policy_sub_type_id == 2)
            {
                premium_gpa += parseFloat(e.policy_mem_sum_premium);

            }
            if(e.policy_sub_type_id == 3)
            {

                premium_vtl += parseFloat(e.policy_mem_sum_premium);
                ind_premium = e.policy_mem_sum_premium;

            }

            //alert(e['family_relation_id']);
            if (!famrelarr.includes(e['family_relation_id'])) {
                famrelarr.push(e['family_relation_id']);
                common_append(i,e);
                if (e.message)
                {
                    addDependent = {
                        "message": e.message,
                        "premium": e.new_premium,
                    }
                }
                var family_members_id = $("#family_members_id"+i)
                var family_salutation = $("#family_salutation"+i)
                var family_gender = $("#family_gender"+i)
                var first_name = $("#first_name"+i)
                var last_name = $("#last_name"+i)
                var family_date_birth = $("#family_date_birth"+i)
                var age = $("#age"+i)
                var age_type = $("#age_type"+i)
                var mem_email_id = $("#mem_email_id"+i)
                var mem_mob_no = $("#mem_mob_no"+i)

                if(e.firstname == '' || e.firstname == undefined){
                    e.firstname = e.policy_member_first_name;
                }
                if(e.lastname == '' || e.lastname == undefined){
                    e.lastname = e.policy_member_last_name;
                }
                if(e.gender == '' || e.gender == undefined){
                    e.gender = e.policy_mem_gender;
                }
                if(e.dob == '' || e.dob == undefined){
                    e.dob = e.policy_mem_dob;
                }
                family_members_id.val(e.fr_id);
                family_members_id.css("pointer-events",'none');
                family_gender.val(e.gender);
                first_name.val(e.firstname.toUpperCase()).prop('disabled',true);
                if(e.lastname != undefined){
                    last_name.val(e.lastname.toUpperCase()).prop('disabled',true);
                }else{
                    last_name.val(e.lastname).prop('disabled',true);
                }

                family_date_birth.val(e.dob).prop('disabled',true);
                age.val(e.age);

                if(e.fr_id != 1 && e.fr_id != 0 && e.fr_id != 2 && e.fr_id != 3)
                {
                    salutation_hide_show(this,e.policy_mem_gender);
                    family_salutation.prop('disabled',false);
                }
                else
                {
                    family_salutation.prop('disabled',true);
                }

                set_salutation(family_salutation,e.fr_id);
                age_type.val(e.age_type);
                mem_email_id.val(e.policy_member_email_id);
                mem_mob_no.val(e.policy_member_mob_no);
                //change
                mem_email_id.val(e.policy_member_email_id).prop('disabled',true);
                mem_mob_no.val(e.policy_member_mob_no).prop('disabled',true);
               // declarepopoulate(e.emp_id, e.policy_member_id,i);


                if(e.fr_id != '0'){
                    $("#edit_btn"+i).show();
                }
                //remove email and mob no div for kids
                if(e.fr_id == 2 || e.fr_id == 3){

                    $("#mem_email_id"+i).parent().hide();
                    $("#mem_mob_no"+i).parent().hide();
                    $("#mem_email_id"+i).attr('required', false);
                    $("#mem_mob_no"+i).attr('required', false);
                }

                if($("#plan_name").val() == 'R06'){
                    $("#edit_btn"+i).show();
                }

                $("#delete_btn"+i).show();
                $("#delete_btn"+i).html('Delete');

                $(".disease"+i).css("pointer-events","none");

                i++;

            }



        });

        var family_construct = $('#patFamilyConstruct option:selected').val();
        if(family_construct != ''){
            var result = family_construct.split('+');

            var add_length = [];
            add_length.push(parseInt(result[0]));
            add_length.push(parseInt(result[1]));
            debugger;
            if(sum(add_length) > members_arr.length){
                var check_count = (sum(add_length) - members_arr.length);
                var i = 0;
                var x = members_arr.length;
                for (i = 0; i < check_count; i++) {
                    //common_append(elem.data.length);

                    common_append(x++);

                }
            }

            $("#add_btn_view").show();

            if(sum(add_length) == elem.data.length){
                //$("#patFormSubmit").hide();
            }

            if(elem.data.length == 0)
            {
                $("#add_more").empty();
                $("#add_btn_view").hide();
                $("#sum_insures").val('');
                $("#patFamilyConstruct").val('');
                $("select[name=familyConstruct]").html('<option value="" selected>Select</option>');

                $("#premium").val('');
                //$("#sum_insures").removeAttr('disabled', 'disabled');
                $("#plan_name").removeAttr('disabled', 'disabled');
                if($('#plan_name').val() == 'T03'){
                    $("#sum_insures").removeAttr("disabled", "disabled");
                    $("#patFamilyConstruct").removeAttr("disabled", "disabled");
                }
                //$("#patFamilyConstruct").prop('disabled', false);
                //$('input[name=GCI_optional]').attr("disabled",false);

            }

        }

        if (addDependent && addDependent.message && addDependent.premium)
        {
            swal("Alert", addDependent.message, "warning");
            $("#premium").val(addDependent.premium)
        }

        //change premium after insertion
        //console.log($('#plan_name').val()+"= here ="+$("#sum_insures").val());
        if($('#plan_name').val() != '' && $("#sum_insures").val() != ''){
            var premHtml = '';
            //console.log(premium_ghi+ '----' +premium_gpa+ '----'+premium_vtl+'----'+ind_premium+$("#plan_name").val());
            if(premium_ghi != 0){
                premHtml += '<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;"> Name:  <br></span><span style="color:#000;">Group Health Insurance</span></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+$("#sum_insures").val()+'</span></div><div class="mr-3"><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+premium_ghi+'</span></div></div>';
            }
            if(premium_gpa != 0){
                premHtml += '<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;"> Name:  <br></span><span style="color:#000;">Group Personal Accident</span></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+$("#sum_insures").val()+'</span></div><div class="mr-3"><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+premium_gpa+'</span></div></div>';
            }
            //alert(premium_vtl);
            if(premium_vtl != 0){
                if($('#plan_name').val() == 'T01'){
                    premHtml += '<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;"> Name:  <br></span><span style="color:#000;">Group Critical Insurance</span></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+$("#sum_insures").val()+'</span></div><div class="mr-3"><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+premium_vtl+'</span></div></div>';
                    var gci_premium = premium_vtl;
                }else{
                    premHtml += '<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;"> Name:  <br></span><span style="color:#000;">Group Critical Insurance</span></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+$("#sum_insures").val()+'</span></div><div class="mr-3"><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+premium_vtl+'</span></div></div>';
                    var gci_premium = premium_vtl;
                }
            }
            ////debugger;
            if(premHtml != ''){
                //console.log(premHtml);
                ////debugger;
                get_premium = premHtml;
                $('#premiumModalBody').html(premHtml);
            }
            var totalPremium = parseInt(premium_ghi) + parseInt(premium_gpa) + parseInt(gci_premium);
            //alert(totalPremium+'===='+premHtml);
            if(parseInt(totalPremium) != 0 && !isNaN(totalPremium)){
                ////debugger;
                //alert("i am here");
                total_premium = totalPremium;
                $('#premium').val(totalPremium);
                $('#premium').text(totalPremium);
            }

        }else{

            ////debugger;
            get_premium = '';
            total_premium = 0;
            $("#premium").val('0');
            $("#premiumModalBody").html("");
            $("#sum_insures").val();

        }

    }
    function set_salutation(elem,val){
        var salutation_set = '';
        $.ajax
        ({
            url: "<?php echo base_url(); ?>teleproposal/family_details_relation",
            type: "POST",
            data: {relation_id: 0},
            dataType: "json",
            async:false,
            beforeSend: function() {
                //set_session();
            },
            success: function (response)
            {
                salutation_set = response.family_data[0].salutation;
                ////debugger;
                if(response.family_data[0].product_id == 'T01'){
                    $(".GCIOptionalDiv").show();
                    if(response.family_data[0].GCI_optional == 'No'){
                        $('#GCI_no').prop('checked', true);
                    }else{
                        $('#GCI_yes').prop('checked', true);
                    }
                }
                $('#annual_income').val(response.family_data[0].annual_income);
                $('#occupation').val(response.family_data[0].occupation);

            }
        });

        if(val == 0){
            elem.val(salutation_set);
        }else{

            if(val == 1){
                if(salutation_set == 'Mr'){
                    elem.val('Mrs');
                }else{
                    elem.val('Mr');
                }
            }else{
                if(val == 2){
                    elem.val('Master');
                }else{
                    elem.val('Ms');
                }
            }
        }
    }

    $("#nomineedob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:-0Y",
        maxDate: "-0Y",
        //minDate: "-55Y +1D"
    });

    function combo_set_premium_data()
    {
        $.post("<?php echo base_url(); ?>teleproposal/combo_set_data",function (e) {

            e = JSON.parse(e);
            e.forEach(function (e1) {
                var date_birth = e1.rel_name+'_date_birth';
                var rel_name = e1.rel_name+'_rel';
                console.log(date_birth);
                $("#"+date_birth).val(e1.dob);
                $("#"+rel_name).val(e1.fr_id);


            });



        });
    }
</script>
</body>

</html>