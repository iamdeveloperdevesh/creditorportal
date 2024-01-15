<?php
// upendra - maker/checker 30-07-2021

$is_maker_checker = $_SESSION['telesales_session']['is_maker_checker'];
//echo $emp_detail['lead_id'].'--'.$lead_id;exit;
// print_pre($parent_id);exit;
//$parent_id = 'test123';

$redirect_email = $redirectFrom_email;

// print_pre($policy_detail);exit;
//updated by upendra on 12-04-2021
if (!isset($_GET['product_id'])) {
    $_GET['product_id'] = $emp_details['product_id'];

    $_GET['text']= $emp_details['lead_id'];
}


// print_pre($emp_detail);
// exit;
?>

<style>
    .accordion .card-header:after {
        font-family: 'FontAwesome';
        content: "\f068";
        float: right;
        font-size: 17px;
        cursor: pointer;
    }

    .accordion .card-header.collapsed:after {
        /* symbol for "collapsed" panels */
        content: "\f067";
        cursor: pointer;
    }

    @media only screen and (max-device-width: 480px) {
        .wd-edit {
            right: 0px !important;
        }

        .bx-shw {
            margin-top: 6%;
            max-width: 85%;
            border-radius: 15px;
            box-shadow: 0px 10px 40px 0px rgb(150 57 57 / 25%) !important;
        }

        .plan_ic_name_y {
            font-size: 20px;
        }
    }

    .card-header {
        color: #fff;
        border-color: #fff !important;
        border-bottom: 1px dashed;
    }

    .total_p_text_proposal_summary {
        font-size: 13px;
    }

    .theme-sidebar-widget .list-item li a span {
        font-size: 23px !important;
    }
    .plan_right_member_e_c_proposal_form{
        display: flex;
        align-items: center;
    }
</style>
<form id="dummyForm" method="post" action="<?php echo base_url(); ?>teleproposal/thankyou">

    <input type="hidden" name ="hiddenleadId"value="<?php echo  $emp_details['lead_id']; ?>" id="hiddenleadid">


</form>


<div class="col-md-10">
    <div class="content-section mt-3">
        <div id="accordion12" class="according accordion-s2 mt-3">

        <div class="card card-member">
            <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion451" aria-expanded="false"> <span class="lbl-card">Agent Details - <i class="ti-file"></i></a>
            </div>
            <div id="accordion451" class="collapse card-vis-mar accord-data show" data-parent="#accordion451" style="">
                <!-- form start -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">AV Code</label>
                                <div class="input-group">
                                    <p class="font_sub_p_s"><?php echo $agent_details['agent_id']; ?></p>

                                </div>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">AV Name</label>
                                <div class="input-group">
                                    <p class="font_sub_p_s"><?php echo $agent_details['agent_name']; ?></p>

                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Axis Location<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <p class="font_sub_p_s"><?php echo $axis_details['axis_location']; ?></p>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Base Caller Id</label>
                                <div class="input-group">
                                    <p class="font_sub_p_s"><?php echo $base_agent_details['base_agent_id']; ?></p>

                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Base Caller Name</label>
                                <div class="input-group">
                                    <p class="font_sub_p_s"><?php echo $base_agent_details['base_agent_name']; ?></p>

                                </div>
                            </div>



                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Axis LOB<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <p class="font_sub_p_s"><?php echo $axis_details['axis_lob']; ?></p>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername" class="col-form-label">Axis Process<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <p class="font_sub_p_s"><?php echo $axis_details['axis_process']; ?></p>
                                </div>
                            </div>
                            </div>


                        </div>


                            <!-- <div class="col-md-2 col-6 text-right">
                                <button class="btn cnl-btn">Cancel</button>
                            </div> -->
                        </div>

            <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion452" aria-expanded="false"> <span class="lbl-card">Customer Details - <i class="ti-file"></i></a>
            </div>
            <div id="accordion452" class="collapse card-vis-mar accord-data show" data-parent="#accordion452" style="">
                <!-- form start -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Salutation<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['salutation']; ?></p>

                            </div>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">First Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['emp_firstname']; ?></p>

                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['emp_lastname']; ?></p>

                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Gender</label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['gender']; ?></p>

                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Date Of Birth</label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['bdate']; ?></p>

                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Email<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['email']; ?></p>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Address Line1<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['address']; ?></p>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Address Line2<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['comm_address']; ?></p>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Address Line3<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['comm_address1']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Mobile No.1<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['mob_no']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Mobile No.2<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['emg_cno']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">

                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Pincode<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['emp_pincode']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">City<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['emp_city']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">State<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['emp_state']; ?></p>
                            </div>
                        </div>
                    </div>


                </div>


                <!-- <div class="col-md-2 col-6 text-right">
                    <button class="btn cnl-btn">Cancel</button>
                </div> -->
            </div>

            <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion453" aria-expanded="false"> <span class="lbl-card">Policy Details - <i class="ti-file"></i></a>
            </div>
            <div id="accordion453" class="collapse card-vis-mar accord-data show" data-parent="#accordion453" style="">
                <!-- form start -->
                <div class="card-body">
                    <div class="row">
                        <?php echo combo_indivisual_member($policy_details); ?>

                    </div>
                </div>
            </div>
            <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion454" aria-expanded="false"> <span class="lbl-card">Nominee Details - <i class="ti-file"></i></a>
            </div>
            <div id="accordion454" class="collapse card-vis-mar accord-data show" data-parent="#accordion454" style="">
                <!-- form start -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Releation With Proposer<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $nominee_data['nominee_type']; ?></p>

                            </div>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">First Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $nominee_data['nominee_fname']; ?></p>

                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $nominee_data['nominee_lname']; ?></p>

                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">DOB</label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $nominee_data['nominee_dob']; ?></p>

                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Contact No.</label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $nominee_data['nominee_contact']; ?></p>

                            </div>
                        </div>

                    </div>


                </div>


                <!-- <div class="col-md-2 col-6 text-right">
                    <button class="btn cnl-btn">Cancel</button>
                </div> -->
            </div>
            <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion455" aria-expanded="false"> <span class="lbl-card">Payment Details - <i class="ti-file"></i></a>
            </div>
            <div id="accordion455" class="collapse card-vis-mar accord-data show" data-parent="#accordion455" style="">
                <!-- form start -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Mode Of Payment<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['payment_mode']; ?></p>

                            </div>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Preferred Contact Date(DD-MM-YYYY)	<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['preferred_contact_date']; ?></p>

                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Preferred Contact Time	<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $emp_details['preferred_contact_time']; ?></p>

                            </div>
                        </div>

                        <div class="col-md-4 mb-3">

                            <label for="validationCustomUsername" class="col-form-label">

                                <?php
                                if($emp_details['is_makerchecker_journey']!='yes'){
                                    ?>

                                    AV

                                    <?php
                                }
                                ?>

                                Remarks</th>
                            </label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $disposition['remarks']; ?></p>

                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Disposition</label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo $disposition['Dispositions']; ?></p>

                            </div>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="validationCustomUsername" class="col-form-label">Sub Disposition</label>
                            <div class="input-group">
                                <p class="font_sub_p_s"><?php echo  $disposition['Sub-dispositions']; ?></p>

                            </div>
                        </div>



                    </div>

            </div>


            </div>
          <!--  <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion456" aria-expanded="false"> <span class="lbl-card">Good Health Declaration- <i class="ti-file"></i></a>
            </div>-->

            <div class="card-header card-vif">
                <a class="card-link collapsed card-vis" data-toggle="collapse" href="#accordion457" aria-expanded="false"> <span class="lbl-card">Employee Declaration - <i class="ti-file"></i></a>
            </div>
            <div id="accordion454" class="collapse card-vis-mar accord-data show" data-parent="#accordion454" style="">
                <!-- form start -->
                <div class="card-body">
                    <div class="row">
                        <table class="table table-bordered text-center">
                            <?php
                            $arr = array();

                            foreach ($emp_declare as $key => $check_header) {
                                //print_R($policy_declarration_data);
                                $arr['is_remark'][] = $check_header['is_remark'];
                            }
                            ?>
                            <thead class="text-uppercase">
                            <tr>

                                <?php
                                if ($arr['is_remark'][0] == 1 && $arr['is_remark'][1] == 1) {
                                    $status = 1;
                                    // echo '<th scope="col" style="font-weight: 600;">Remark</th>';
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody id="mydatas">
                            <!-- new telesales CR - 15-11-2021 - add if else condition -->

<?php

                            echo    $emp_declares .= '<tr>
	<td><input type="checkbox" checked name="customer_declaration_checkbox" id="customer_declaration_checkbox"  class="" value="Yes"></td>
	<td style="text-align:left; font-weight:600 !important;">I hereby declare that I have understood the contents of this enrolment form along with product benefits, terms/conditions and exclusions have been clearly explained to me in the vernacular understood by me. I confirm to abide by the policy terms & conditions.</td></tr>';


                            ?>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center row">
                <div class="col-md-6">
                    <button id="edit_emp" type="button" class="btn can-btn mt-4 pr-4 pl-4 mb-2" style="
    float: left;"><a href="/tele_create_proposal?leadid=<?php echo $lead_id; ?>" style="color:#fff !important;">Back</a></button>
                </div>
                <div class="col-md-6">
                    <?php
//print_r($_GET);die;
                    //print_pre($emp_detail);exit;

                    if(!empty($_GET['text'])){
                        if((empty($emp_details['makerchecker'])&&$emp_details['is_makerchecker_journey']=='yes')||(strtolower($emp_details['makerchecker'])=='maker'&&$emp_details['is_makerchecker_journey']=='yes') ){
                            ?>
                            <button id="final_submit" type="button" class="btn sub-btn mt-4 pr-4 pl-4 mb-3" style="
    float: right;">Submit to AV </button>
                            <?php
                        }else if(strtolower($emp_details['makerchecker'])=='checker'&&$emp_details['is_makerchecker_journey']=='yes'){
                            ?>
                            <button id="final_submit" type="button" class="btn sub-btn mt-4 pr-4 pl-4 mb-3" style="
    float: right;">Payment Link Trigger</button>
                            <?php
                        }else{
                            ?>
                            <button id="final_submit" type="button" class="btn sub-btn mt-4 pr-4 pl-4 mb-3" style="
    float: right;">Proceed</button>

                            <?php
                        }
                    }
                    ?>
                    <?php
                    /*if(!empty($_GET['leadid'])){
                    if(strtolower($emp_detail['makerchecker'])=='maker'&&$emp_detail['is_makerchecker_journey']=='yes'){
                    ?>
                            <button id="final_submit" type="button" class="btn sub-btn mt-4 pr-4 pl-4 mb-3" style="
                        float: right;">Submit to AV </button>
                    <?php
                    }else{
                    ?>
                            <button id="final_submit" type="button" class="btn sub-btn mt-4 pr-4 pl-4 mb-3" style="
                        float: right;">Proceed</button>

                    <?php
                    }
                    }*/
                    ?>





                </div>
            </div>


        </div>

            <div class="col-md-12 text-center" style='display:none' id="proceed_to_payment">
                <input type="hidden" value="<?php echo base_url(); ?>teleproposal/payment_redirect_view/<?php echo $emp_id_encrypt; ?>" id="proceed_for_payment_href">
                <button type="button" class="btn sub-btn mt-4 pr-4 pl-4 mb-3" id="proceed_for_payment_button">Proceed</button>
            </div>
                <!-- end form -->
            </div>
        </div>
        </div>
    </div>
    </div>


<!-- Modal -->
<?php
function combo_indivisual_member($policy_detail)
{
    //print_Pre($policy_detail);exit;


    $member = '';



    if ($policy_detail['comboData']) {
        $combo = $policy_detail['comboData'];
        // $member .= '<h6  id = "product_name" class = "header-title title header-tl-xs">' . $combo['plan_name'] . '</h6>';
        foreach ($combo['customer_detail'] as $value) {
            $member .= $value;
        }
    }
    if ($policy_detail['indivisual']) {
        $ind = $policy_detail['indivisual'];

        //$member .= '<h6 id = "product_name">' . $ind['product_name'] . '</h6>';

        $member .= $ind['customer_detail'];
    }

    echo $member;
    //return $member;

}
?>
<script>
    $(document).ready(function() {
debugger;

        $("#proceed_for_payment_button").click(function() {
            if($('input[name="customer_declaration_checkbox"]').is(':checked'))
            {
                var location_str = $("#proceed_for_payment_href").val();
                window.location.href = location_str;
            }else
            {
                swal('In order to proceed further please check the disclaimer');
            }
        });

       /* var ghd_data = '<?php echo $js_ghd; ?>';

        var ghd = JSON.parse(ghd_data);
        debugger;
        var len = ghd.length;
        var i;
        for (i = 0; i < len; i++) {
            var member = ghd[i];
            console.log(member);
            if (member.type == 'C1') {
                $("#C_data").show();
            }
            if (member.type == 'D1') {
                $("#D_data").show();
            }
            if (member.type != '') {
                $("#" + member.type + "_data").show();
                if (member.format == 'Yes') {
                    $("." + member.type + "_display").text(member.format);

                    $("#" + member.type + "_remark").css('display', 'block');
                    $("#" + member.type + "_remark").val(member.remark);

                } else {
                    $("." + member.type + "_display").text(member.format);
                    $("#" + member.remark + "_remark").css('display', 'none');
                }
            }
        }*/
        var rediect_email = '<?php echo $redirectFrom_email; ?>';
        alert(rediect_email);
        if (rediect_email == 'Yes') {
            $("#final_submit").hide();
            $("#proceed_to_payment").show();
            $("#edit_emp").hide();
            /*navigation bar*/
            $("#navbarTogglerDemo01").removeClass('navbar-collapse');
            $(".user-profile").hide();

        } else {
            $("#final_submit").show();
            $("#edit_emp").show();
            $("#proceed_to_payment").hide();
            $("#navbarTogglerDemo01").addClass('navbar-collapse');
            $(".user-profile").show();
        }
    });
    $(document).ready(function() {
        $(document).on("click", ".pay_now", function() {

            if ($("#declare").prop('checked') == true) {
                window.location.href = '/quotes/redirect_to_pg';
            } else {
                swal("Alert", "Please accept Terms & conditions.", "warning");
            }
        })
    });

    $("#final_submit").click(function() {
        debugger;
        var product_id = '';
        var product_name = $("#product_name").html();
        let searchParams = new URLSearchParams(window.location.search);
        var product_id = '<?php echo $emp_details["product_id"]; ?>';
        alert(product_id);
        var ismakerchecker=$('#updateadddispostion').val();
        var mode_of_payment = '<?php echo $emp_details["payment_mode"]; ?>';
        $("#final_submit").html("Please wait...");
        $("#final_submit").attr("disabled", true);

        $.ajax({
            url: "<?php echo base_url(); ?>teleproposal/aprove_status",

            type: "POST",
            async: false,
            data: {
                product_id: product_id,
                mode_of_payment : mode_of_payment,
                ismakerchecker:ismakerchecker
            },

            success: function(response) {
                var res = JSON.parse(response);

                if (res.status == false) {

                    ajaxindicatorstop();

                    displayMsg("error", res.message);
                
                            $("#final_submit").html("Proceed");
                            $("#final_submit").attr("disabled", false);

                    return;
                }
                if (res.status == true) {
                    var product_id = '';
                    var product_id = '';
                    var product_name = $("#product_name").html();
                    let searchParams = new URLSearchParams(window.location.search);
                    var product_id = '<?php echo $emp_details["product_id"]; ?>';

                    /*
                    upendra - maker/checker 30-07-2021
                    */
                    var is_maker_checker = '<?php echo $is_maker_checker; ?>';
                    if(is_maker_checker != "yes"){
                        $.ajax({
                            url: "<?php echo base_url(); ?>teleproposal/summary_url",
                            type: "POST",
                            async: false,
                            data: {
                                product_id: product_id
                            },
                            success: function(response) {

                                displayMsg("success", "Proposal Created Successfully");
                                $("#final_submit").html("Proceed");
                                $("#final_submit").attr("disabled", false);
                                $("#dummyForm").submit();

                            }
                        });
                    }

                    /*
                    upendra - maker/checker 30-07-2021
                    */

                    if(is_maker_checker == "yes"){
                        var lead_id = '<?php echo $lead_id; ?>';
                        $.ajax({
                            url: "/tls_maker_checker_update",
                            type: "POST",
                            async: false,
                            data: {
                                lead_id: lead_id
                            },
                            success: function(response) {

                                $("#final_submit").html("Proceed");
                                $("#final_submit").attr("disabled", false);

                            }
                        });

                    }

                   // ajaxindicatorstop();


                }

            }
        });
    });

    function tele_thank_you() {

        $("#dummyForm").submit();

    }
</script>