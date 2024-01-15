
<?php
if(isset($_SESSION['linkUI_configuaration'])){
    $image1= $this->session->userdata('linkUI_configuaration')[0]['summary_page_image1'];
    $image2= $this->session->userdata('linkUI_configuaration')[0]['summary_page_image2'];
    $image3= $this->session->userdata('linkUI_configuaration')[0]['summary_page_image3'];
    if(!$image1){

    }else{  ?>
        <style>
            .fm1 {
                background: url(<?php echo $image1; ?>) no-repeat center;
            }
        </style>
        <?php
    }
    if(!$image2){

    }else{  ?>
        <style>
            .fm2 {
                background: url(<?php echo $image2; ?>) no-repeat center;
            }
        </style>
        <?php
    }
    if(!$image3){

    }else{  ?>
        <style>
            .fm3 {
                background: url(<?php echo $image3; ?>) no-repeat center;
                background-size: 30%;
            }
        </style>
        <?php
    }
}else{ ?>
    <style>
        .fm1 {
            background: url(/assets/images/rv-pg1.png) no-repeat center;
        }
        .fm2 {
            background: url(/assets/images/rv-pg2.png) no-repeat center;
        }

        .fm3 {
            background: url(/assets/images/rv-pg3.png) no-repeat center;
            background-size: 30%;
        }
    </style>
<?php  }
?>
<style>
    .swal-button{
        background-color: #F2581B !important;
    }
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
        /*background-color: #107591 !important;*/
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

<div class="container-fluid mt-20 pb-100 continue_fluid_width_custom">
    <!-- /.theme-sidebar-widget -->
    <div class="element-section mb-150">
        <br>

        <div class="row margin_top_tab_proposal">
            <div class="col-lg-8 col-md-12 aos-init" data-aos="fade-right">
                <!--------------start Proposal Details ------------------>
                <div class="container">
                    <div id="accordion" class="accordion ad-nw1">
                        <div class="card mb-0 cd-nw1">
                            <div class="card-header active ch-nw" data-toggle="collapse" href="#collapseOne">
                                <a class="card-title">
                                    Personal Details
                                </a>
                            </div>
                            <div id="collapseOne" class="card-body collapse show" data-parent="#accordion">
                                <div class="card_proposal_summary fm1">
                                    <div class="col-md-12 wd-edit">
                                        <a href="/quotes/generate_proposal?lead_id=<?php echo $_REQUEST['lead_id'];?>&view=pdetails"><img src="/assets/images/edit_p_s.png" class="img_edit_p_s"></a>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Salutation</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['salutation']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">First Name</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['first_name']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Last Name</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['last_name']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Gender</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['gender']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Marital Status</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['marital_status']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Date of Birth</p>
                                            <p class="font_sub_p_s"><?php echo date('d-m-Y',strtotime($customer_details['customer_details']['dob'])); ?></p>
                                        </div>

                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Is Proposer same as Insured</p>
                                            <p class="font_sub_p_s"><?php echo ($customer_details['customer_details']['is_proposer_insured'] == 1) ? 'Yes' : 'No'; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Pan Card Number</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['pan']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">GSTIN Number (Optional)</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['gstin']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Address 1</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['address_line1']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Address 2</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['address_line2']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">Pincode</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['pincode']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">State</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['state']; ?></p>
                                        </div>
                                        <div class="col-md-4 mb-12">
                                            <p class="font_15_p_s">City</p>
                                            <p class="font_sub_p_s"><?php echo $customer_details['customer_details']['city']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--------------end Proposal Details ------------------>
                            <div class="card-header collapsed ch-nw" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" <?php if($get_summary_details['creditor']['nominee_mandatory']==1){ echo ' style="border-radius: 0px;"'; }else{ echo ' style="border-radius:0px 0px 22px 22px;"';}?> >
                                <a class="card-title">
                                    Insured Details
                                </a>
                            </div>
                            <div id="collapseTwo" class="card-body collapse" data-parent="#accordion">
                                <!--------------start Insured Details ------------------>
                                <div class="card_proposal_summary fm2">
                                    <div class="col-md-12 wd-edit">
                                        <a href="/quotes/generate_proposal?lead_id=<?php echo $_REQUEST['lead_id'];?>&view=idetails"><img src="/assets/images/edit_p_s.png" class="img_edit_p_s"></a>
                                    </div>
                                    <br>
                                    <?php $temp_arr = array();
                                    if(count($member_details) > 0){
                                        foreach ($member_details as $key => $value) {
                                            $i = 0;
                                            ?>

                                            <p class="details_insured_p_s"><?php echo $key; ?></p>


                                            <?php foreach ($value as $key1 => $val) {

                                                ?>

                                                <div class="details_border_b_p_s">
                                                    <p class="details_insured_p_s">Details Of Insured<?php echo $i + 1; ?></p>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-12">
                                                            <p class="font_15_p_s">Relation</p>
                                                            <p class="font_sub_p_s"><?php echo $val['member_type']; ?></p>
                                                        </div>
                                                        <div class="col-md-4 mb-12">
                                                            <p class="font_15_p_s">Salutation</p>
                                                            <p class="font_sub_p_s"><?php echo $val['policy_member_salutation']; ?></p>
                                                        </div>
                                                        <div class="col-md-4 mb-12">
                                                            <p class="font_15_p_s">First Name</p>
                                                            <p class="font_sub_p_s"><?php echo $val['policy_member_first_name']; ?></p>
                                                        </div>
                                                        <div class="col-md-4 mb-12">
                                                            <p class="font_15_p_s">Last Name</p>
                                                            <p class="font_sub_p_s"><?php echo $val['policy_member_last_name']; ?></p>
                                                        </div>

                                                        <div class="col-md-4 mb-12">
                                                            <p class="font_15_p_s">Date of Birth</p>
                                                            <p class="font_sub_p_s"><?php echo date('d-m-Y',strtotime( $val['policy_member_dob'])); ?></p>
                                                        </div>
                                                        <div class="col-md-4 mb-12">
                                                            <p class="font_15_p_s">Gender</p>
                                                            <p class="font_sub_p_s"><?php echo $val['policy_member_gender']; ?></p>
                                                        </div>
                                                        <div class="col-md-4 mb-12">
                                                            <p class="font_15_p_s">Marital Status</p>
                                                            <p class="font_sub_p_s"><?php echo $val['policy_member_marital_status']; ?></p>
                                                        </div>
                                                        <?php
                                                        if($si_type == 1){ ?>
                                                            <div class="col-md-4 mb-12">
                                                                <p class="font_15_p_s">Cover</p>
                                                                <p class="font_sub_p_s"><?php echo $val['cover']; ?></p>
                                                            </div>
                                                        <?php  }
                                                        ?>
                                                    </div>
                                                </div>
                                                <br>
                                                <?php
                                                $i++;
                                            }
                                        }
                                    }
                                    ?>

                                </div>
                                <!--------------end insured Details ------------------>
                            </div>
                            <?php if($get_summary_details['creditor']['nominee_mandatory']==1){?>
                            <div class="card-header collapsed ch-nw" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" style="border-radius:0px 0px 22px 22px;">
                                <a class="card-title">
                                    Nominee Details
                                </a>
                            </div>
                            <div id="collapseThree" class="collapse" data-parent="#accordion">
                                <div class="card-body">

                                    <!--------------start other Details ------------------>
                                    <div class="card_proposal_summary fm3">
                                        <div class="col-md-12 wd-edit">
                                            <a href="/quotes/generate_proposal?lead_id=<?php echo $_REQUEST['lead_id'];?>&view=ndetails"><img src="/assets/images/edit_p_s.png" class="img_edit_p_s"></a>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-4 mb-12">
                                                <p class="font_15_p_s">Nominee Relation</p>
                                                <p class="font_sub_p_s"><?php echo $nominee_details['policy_data']['nominee_relation_name']; ?></p>
                                            </div>
                                            <div class="col-md-4 mb-12">
                                                <p class="font_15_p_s">First Name</p>
                                                <p class="font_sub_p_s"><?php echo $nominee_details['policy_data']['nominee_first_name']; ?></p>
                                            </div>
                                            <div class="col-md-4 mb-12">
                                                <p class="font_15_p_s">Last Name</p>
                                                <p class="font_sub_p_s"><?php echo $nominee_details['policy_data']['nominee_last_name']; ?></p>
                                            </div>
                                            <div class="col-md-4 mb-12">
                                                <p class="font_15_p_s">DOB</p>
                                                <p class="font_sub_p_s"><?php if(!empty( $nominee_details['policy_data']['nominee_dob'])) { echo date('d-m-Y',strtotime( $nominee_details['policy_data']['nominee_dob'])); } ?></p>
                                            </div>
                                            <div class="col-md-4 mb-12">
                                                <p class="font_15_p_s">Contact Number</p>
                                                <p class="font_sub_p_s"><?php echo $nominee_details['policy_data']['nominee_contact']; ?></p>
                                            </div>
                                        </div>

                                    </div>
                                    <!--------------end other Details ------------------>
                                </div>
                            </div>
                             <?php }?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-1 col-md-1" style="max-width: 6%; ">
                &nbsp;
            </div>
            <div class="col-xl-3 col-lg-3 col-md-11 col-sm-8 col-12 bx-shw aos-init box-shadow_plan_box_p_s_s_proposal_form" data-aos="fade-left">
                <div class="row">
                    <div class="col-md-12">
                        <div class="addon_plan_a_t_addon_cover_r_cart_proposal_form">
                            <p class="text-left plan_ic_name_y">Summary</p>
                        </div>
                    </div>
                    <div class="col-md-12">


                        <?php
                        if(isset($_SESSION['linkUI_configuaration'])){
                            $image= $this->session->userdata('linkUI_configuaration')[0]['quote_card_image'];
                            ?>
                            <p class="font_17 plan_right_member_e_c_proposal_form sideWindowForm"><img class="contain img_right_panel_addon_add_proposal_form" src="<?php echo $image?>"> <span class="span_proposal_form_i cover_plan_name"><?php echo $get_summary_details['creditor']['creaditor_name']."-".$get_summary_details['creditor']['plan_name']?></span>
                            </p>
                            <?php
                        }else{ ?>
                            <p class="font_17 plan_right_member_e_c_proposal_form sideWindowForm"><img class="contain img_right_panel_addon_add_proposal_form" src="<?php echo $get_summary_details['creditor']['creditor_logo']?>"> <span class="span_proposal_form_i cover_plan_name"><?php echo $get_summary_details['creditor']['creaditor_name']."-".$get_summary_details['creditor']['plan_name']?></span>
                            </p>
                        <?php  }
                        ?>

                    </div>
                </div>
                <div class="theme-sidebar-widget theme-sidebar-widget-margin-proposal">
                    <div class="single-block main-menu-list mb2">
                        <?php

                        foreach($get_summary_details['insured_member'] as $req_val){
                            ?>
                            <div class="row margin_top_proposal_summary_r_b">
                                <div class="col-lg-6 col-md-4 col-6">
                                    <p class="color_cover_p_r_b">Cover Amount</p>
                                    <p class="p_c_r_b_p"><i class="fa fa-inr"></i><?php /*if(!empty($premium_details['sum_insured'])) {echo $premium_details['sum_insured'];}else{echo $premium_details['cover']; } */?>
                                        <?php echo $req_val['cover'];  ?>
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-4 col-6">
                                    <p class="color_cover_p_r_b">Total Premium</p>
                                    <p class="p_c_r_b_p"><i class="fa fa-inr"></i> <?php /*if(!empty($premium_details['total_premium'])) {echo $premium_details['total_premium'];}else{echo $premium_details['premium']; } */?>
                                        <?php echo $req_val['premium'];  ?>
                                   </p>
                                </div>
                                <div class="col-lg-6 col-md-4 col-12">
                                    <p class="color_cover_p_r_b">Policy Tenure</p>
                                    <p class="p_c_r_b_p"><i class="fa fa-inr"></i> <?php echo $duration; echo ($duration>1)?' Years':' Year'; ?></p>
                                </div>
                                <?php if(!empty($deductable['deductable'])){ ?>
                                    <div class="col-lg-6 col-md-4 col-12">
                                        <p class="color_cover_p_r_b">Deductible</p>
                                        <p class="p_c_r_b_p"><i class="fa fa-inr"></i> <?php echo $deductable['deductable'];  ?></p>
                                    </div>
                                <?php }?>
                            </div>
                        <?php   } ?>
                        <div class="col-md-12">
                            <?php
                            if(isset($_SESSION['linkUI_configuaration'])){
                                $image= $this->session->userdata('linkUI_configuaration')[0]['summary_page_image4'];
                                if(!$image){

                                }else{  ?>

                                    <img src="<?php echo $image; ?>" width="100" class="ds-mb-none">
                                    <?php

                                }
                            }else{ ?>
                                <img src="/assets/images/terms.png" width="100" class="ds-mb-none">
                            <?php  }
                            ?>

                        </div>
                        <ul class=" list-item">
                            <li class="margin_top_li_check_i hidden-md" style="margin-bottom:20px !important; margin-left:0 !important; margin-right:0 !important; ">
                                <input class="inp-cbx" id="declare" type="checkbox">
                                <label class="cbx" for="declare">
                           <span>
                              <svg width="12px" height="10px">
                                 <use xlink:href="#check"></use>
                              </svg>
                           </span>
                                    <span class="ml-1">I Accept the </span>
                                    <span class="tm-txt" ata-toggle="modal" data-target="#exampleModalCenter">
                              <button type="button" class="btn-none" data-toggle="modal" data-target="#exampleModalCenter">
                                 Terms &amp; Conditions
                              </button>

                           </span>
                                </label>


                                <!--SVG Sprites-->
                                <svg class="inline-svg">
                                    <symbol id="check" viewBox="0 0 12 10">
                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                    </symbol>
                                </svg>
                            </li>
                            <br>
                            <li class="total_premium_btn_proposal_summary">
                                <a href="javascript:void(0);" class="addon_font_s_s_pro_s_s font_17 pay_now">
                                    Pay Now
                                    <span class="font_bold total_premium_btn_addon_r_s">
                              <p class="total_p_text_proposal_summary">Total Premium</p>
                              <br>₹ <?php echo $req_val['total_premium'];?>
                           </span>
                                </a>
                            </li>
                            <br>


                        </ul>
                        <div class="row">
                            <!-- <a href="#" data-toggle="modal" data-target="#m-md" class="read-more text-center">Click here <i class="flaticon-next-1"></i></a> -->
                            <!-- <div class="col-md-12"> -->
                        </div>
                    </div>
                </div>
                <!-- /.single-block -->
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Terms & Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <?php echo $tc_text; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on("click", ".pay_now", function() {

            if ($("#declare").prop('checked') == true) {
                window.location.href = '/quotes/redirect_to_pg?lead_id=<?php echo $_REQUEST['lead_id'];?>';
            } else {
                swal("Alert", "Please accept Terms & conditions.", "warning");
            }
        })
    })
    document.title = 'Proposal summary'
</script>