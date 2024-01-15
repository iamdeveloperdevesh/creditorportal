

<style>
    @media only screen and (max-device-width : 480px) {

        .group_input input[type=checkbox]:checked+label,
        .group_input input[type=radio]:checked+label {
            text-align: center !important;
        }
    }

    .pinode{
        margin-top: -42px !important;
    }

    @media(max-width:425px){
        .p_input_title{
            margin-bottom: 52px;
        }
    }
    /*.action-button {
        background-color: #F2581B !important;
    }*/

     .steps input {
        margin: 0;
    }
    .mb_15_point {
        margin-bottom: 2.5rem !important;
    }
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active{
        -webkit-box-shadow: 0 0 0 40px white inset !important;
    }
    .error {
        display: block;
    }
     @media screen and (max-width:767px) {
        .p_input_title {
            background-image: linear-gradient(to right, #FFF , #fff);
        }
        #input {
            background-image: linear-gradient(to left, #fff 100%, #E6F9FF 30%) !important;
        }
        .steps .previous, .steps .submit {
            top: 334px;
        }
        .label_pincode {
            top: 119px;
        }
        .input_place_css_r {
            margin-bottom: 0!important;
        }
        .steps .next {
            right: 4px;
        }
    }
</style>

<body>
<div class="main-page-wrapper">

    <!-- ===================================================
            Loading Transition
        ==================================================== -->
    <!-- Preloader -->
    <!-- <section>
        <div id="preloader">
            <div id="ctn-preloader" class="ctn-preloader">
                <div class="animation-preloader">
                    <div class="spinner"></div>
                    <div class="txt-loading">
                        <span data-text-preloader="F" class="letters-loading">
                            F
                        </span>
                        <span data-text-preloader="Y" class="letters-loading">
                            Y
                        </span>
                        <span data-text-preloader="N" class="letters-loading">
                            N
                        </span>
                        <span data-text-preloader="T" class="letters-loading">
                            T
                        </span>
                        <span data-text-preloader="U" class="letters-loading">
                            U
                        </span>
                        <span data-text-preloader="N" class="letters-loading">
                            N
                        </span>
                        <span data-text-preloader="E" class="letters-loading">
                            E
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!--
        =============================================
            Theme Main Menu
        ==============================================
        -->
        <?php 

        $deductable_modal = 'false';
        $deductable_amount = '';
        if(!empty($deductable)){
            $deductable_modal = 'true';
            $deductible_text = $this->session->userdata('linkUI_configuaration')[0]['deductible_text'];
            if(!empty($get_customer_details) && !empty($get_customer_details['existing_all_members'])){
               $deductable_amount=$get_customer_details['existing_all_members'][0]['deductable']; 
            }
        }?>
    <div class="shrt-menu shrt-menu-one light-bg text-dark">
        <div class="container-fluid margin-2p" id="input">


            <div class="element-section">
                <div class="row">
                    <!--------------start col-md-4-------------->
                    <div class="col-md-4">
                        <div class="">
                            <p class="p_input_title_l">
                                <?php
                                if(isset($_SESSION['linkUI_configuaration'])){
                                    echo $this->session->userdata('linkUI_configuaration')[0]['first_page_header'];
                                }else{ ?>
                                    Protect your Family's <br> Health with us
                                <?php  }
                                ?>

                            </p>
                        </div>
                        <div class="text-wrapper_input">
                            <ul>
                                <?php
                                if(isset($_SESSION['linkUI_configuaration'])){
                                    $first_page_features= $this->session->userdata('linkUI_configuaration')[0]['first_page_features'];
                                    $arr=explode(',',$first_page_features);
                                    foreach ($arr as $li){ ?>
                                        <li><i class="fa fa-check mr-2 in-ck"></i><?php echo $li; ?></li>
                                    <?php }
                                }else{ ?>
                                    <li><i class="fa fa-check mr-2 in-ck"></i>Compare top Health Insurance plans</li>
                                    <li><i class="fa fa-check mr-2 in-ck"></i>Instant policy insurance</li>
                                    <li><i class="fa fa-check mr-2 in-ck"></i>Get an unbiased advise</li>
                                    <li><i class="fa fa-check mr-2 in-ck"></i>Free Claims Assistance</li>
                                <?php  }
                                ?>

                            </ul>
                        </div>
                        <!-- <div class="covid_plans">
                            <img src="images/covid.png" class="img_covid">
                            <p class="p_covid font_family_encoreblack">Covid Plans</p>
                            <p class="p_covid_sub">All health plans cover covid-19 treatment</p>
                        </div> -->
                        <div class="col-md-12" style="display:flex; align-items:center; justify-content:center;">
                            <?php
                            if(isset($_SESSION['linkUI_configuaration'])){
                                $image= $this->session->userdata('linkUI_configuaration')[0]['first_page_image'];
                                ?>
                                <img src="<?php echo $image; ?>" width="400" class="dis-none">

                                <?php
                            }else{ ?>
                                <img src="<?php echo base_url(); ?>assets/images/Family-new.gif" width="400" class="dis-none">
                            <?php  }
                            ?>

                        </div>

                    </div>
                    <!--------------enn col-md-4-------------->
                    <!-- <div class="col-md-6">
                        <div class="box-shadow_plan_box_p_s_s_input">
                            <div class="set-size charts-container">
                                <div class="pie-wrapper progress-45 style-2">
                                    <span class="label">25% <br><span class="smaller">Completed</span></span>

                                    <div class="pie">
                                        <div class="left-side half-circle"></div>
                                        <div class="right-side half-circle"></div>
                                    </div>
                                    <div class="shadow"></div>
                                </div>
                            </div>
                            <div class="bg_pink">
                                <img src="images/input/hand.png" class="img_right">
                                <p>Hello there</p>
                                <span>Let's Start with the basic details</span>
                            </div>
                        </div>
                        <p class="text_gray_input">Pincode:40002 <span class="float_right txt_g"><i
                                    class="fa fa-pencil"></i></span></p>
                    </div> -->
                    <div class="col-md-8 pb-100">
                        <form class="steps" accept-charset="UTF-8" enctype="multipart/form-data"  id="form-plan">
                            <div class="row">

                                <?php
                                if(isset($_SESSION['linkUI_configuaration']) && (!empty($_SESSION['linkUI_configuaration'][0]['lead_header_text']))){
                                    ?>
                                    <p class="p_input_title">
                                        <?php echo $this->session->userdata('linkUI_configuaration')[0]['lead_header_text'];?></p>

                                    <?php
                                }else{ ?>
                                    <p class="p_input_title">Buy health insurance plan in <span class="font_bold font_30 color_five">4</span> simple steps</p>
                                <?php } ?>
                            </div>
                            <ul id="progressbar">
                                <li <?php if(!empty($dropout_page) && ($dropout_page>0 || $dropout_page<4) )  { } else { echo 'class="active"';}?>>&nbsp;</li>
                                <li <?php if(!empty($dropout_page) && $dropout_page==1) { echo 'class="active"'; } ?>>&nbsp;</li>
                                <li <?php if(!empty($dropout_page) && $dropout_page==2) { echo 'class="active"'; } ?>>&nbsp;</li>
                                <?php if(empty($_SESSION['product_id_session'])){?>

                                    <li <?php if(!empty($dropout_page) && $dropout_page==3) { echo 'class="active"'; } ?>>&nbsp;</li>
                                    <?php

                                }
                                ?>

                            </ul>


                            <!-- USER INFORMATION FIELD SET -->
                            <fieldset>

                                <!-- Begin What's Your First Name Field -->
                                <div class="hs_firstname field hs-form-field" id="">
                                    <!-- <p class="go_back_arrow_input"><i class="icon flaticon-back"></i></p> -->
                                    <label for="firstname-99a6d115-5e68-4355-a7d0-529207feb0b3_2983"><span class="lbl-new">Tell us about
                                                yourself?</span></label>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="input-group mb_15_point">

                                                <input id="edit_customer" name="edit_customer" type="hidden" value="<?php echo $_REQUEST['lead_id']?>">
                                                <input type="text" class="input_place_css_r details fullname" name="name" id="name" placeholder="Enter Name" <?php if(!empty($get_customer_details)) echo 'value="'.$get_customer_details['customer_details']['first_name'].' '.$get_customer_details['customer_details']['last_name'].'"'; ?> onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode === 32 && !/\s$/.test(this.value))">

                                                <label class="input_i_css">Name <span style="color:#FF0000">*</span></label>
                                            </div> <!-- /.input-group -->
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="input-group mb_15_point">
                                                <input type="tel" class="input_place_css_r details mobile" name="mobile" id="mobile" placeholder="Enter Mobile No" maxlength="10" <?php if(!empty($get_customer_details)) echo 'value="'.$get_customer_details['customer_details']['mobile_no'].'"'; ?>>
                                                <label class="input_i_css">Mobile No. <span style="color:#FF0000">*</span></label>
                                            </div> <!-- /.input-group -->
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="input-group mb_15_point">
                                                <input type="text" class="input_place_css_r details email"  name="email" id="email" placeholder="Enter Email ID" <?php if(!empty($get_customer_details)) echo 'value="'.$get_customer_details['customer_details']['email_id'].'"'; ?>>
                                                <label class="input_i_css">Email ID <span style="color:#FF0000">*</span></label>
                                            </div> <!-- /.input-group -->
                                        </div>
                                    </div>
                                    <p class="p_input_css_title">Select Your Gender</p>
                                    <div class="group_input">

                                        <input type="radio" class="details" name="gender" id="rb1" value="male" <?php if(!empty($get_customer_details) && $get_customer_details['customer_details']['gender']== 'Male') echo 'checked'; else if(empty($get_customer_details)) echo 'checked'; ?>>
                                        <label for="rb1">Male</label>
                                        <input type="radio" class="details" name="gender" value="female" id="rb2" <?php if(!empty($get_customer_details) && $get_customer_details['customer_details']['gender']== 'Female') echo 'checked'; ?>>
                                        <label for="rb2">Female</label>
                                    </div>
                                </div>
                                <!-- End What's Your First Name Field -->

                                <?php
                                $disable_btn='';
                                $error_msg='';
                                if($error_cd['status'] == 201){
                                    $disable_btn='disabled="disabled"';
                                    $error_msg="Low CD Balance.Can't Proceed further.";

                                }
                                ?>
                                <button type="button" data-page="1" name="next" <?php echo $disable_btn; ?> class="next action-button submitLead" id="submitLeadBtn" value="Get Started"
                                         style="float: inherit; left: 4px; position: inherit; top: -30px;">Get Started<i class="fas fa-long-arrow-alt-right rht-aw"></i></button>

                                <br><br><br>
                                <label for="" style="margin-top: -95px;" ><span class="ml-3" style="color:red;"><?php echo $error_msg; ?></span></label>
                                <!-- <div class="explanation btn btn-small modal-trigger" data-modal-id="modal-3"><img
                                        src="images/input/irdai.png" class="img_input">IRDAI Direct Broker Code:
                                    IRDAI/123456<br>CIN: 14734876985-07</div> -->



                            </fieldset>
                            <fieldset>

                                <!-- Begin What's Your First Name Field -->
                                <div class="hs_firstname field hs-form-field mb_15_point">
                                    <!-- <p class="go_back_arrow_input"><i class="icon flaticon-back"></i></p> -->
                                    <label for="firstname-99a6d115-5e68-4355-a7d0-529207feb0b3_2983 lbl-new"><span class="lbl-new">Tell us where
                                                do you live?</span></label>
                                    <!-- <img src="images/input/search.png" class="img_icon_se"> -->

                                    <!-- <select placeholder="Enter City or PIN code" class="form-contol_input_css_pincode">
                                        <option>Enter City or PIN code</option>
                                        <option>MUMBAI - 400017</option>
                                        <option>MUMBAI - 400018</option>
                                        <option>MUMBAI - 400019</option>
                                        <option>MUMBAI - 400020</option>
                                        <option>MUMBAI - 400021</option>
                                    </select> -->
                                    <label class="pinode">Pincode <sup style="color: red;">*</sup></label>
                                    <input type="text" class="input_place_css_r details pincode" name="pin_code"  id="pin_code" maxlength="6" placeholder="Enter City or PIN Code" <?php if(!empty($get_customer_details)) echo 'value="'.$get_customer_details['customer_details']['pincode'].'"'; ?>>


                                    <span class="error1" style="display: none;">
                                            <i class="error-log fa fa-exclamation-triangle"></i>
                                        </span>
                                </div>
                                <!-- End What's Your First Name Field -->


                                <p class="p_found_sub">Popular Cities</p>
                                <!-- <div class="explanation btn btn-small modal-trigger" data-modal-id="modal-3"><img
                                src="images/input/irdai.png" class="img_input">IRDAI Direct Broker Code:
                                    IRDAI/123456<br>CIN: 14734876985-07</div> -->

                                <div class="section over-hide z-bigger">
                                    <div class="container  text-left">
                                        <div class="row">

                                            <div class="col-11 pb-5 pd-lft">
                                                <input class="checkbox-tools" type="radio" name="pop_city" id="tool-1" value="Delhi" <?php if(!empty($get_customer_details) && $get_customer_details['customer_details']['city']== 'Delhi') echo 'checked'; ?>>
                                                <label class="for-checkbox-tools" for="tool-1">
                                                    Delhi
                                                </label><input class="checkbox-tools" type="radio" name="pop_city" id="tool-2" value="Pune" <?php if(!empty($get_customer_details) && $get_customer_details['customer_details']['city']== 'Pune') echo 'checked'; ?>>
                                                <label class="for-checkbox-tools" for="tool-2">
                                                    Pune
                                                </label>
                                                <!-- <input class="checkbox-tools" type="radio" name="pop_city" id="tool-3" value="Bengaluru">
                                                <label class="for-checkbox-tools" for="tool-3">
                                                    Bengaluru
                                                </label> --><input class="checkbox-tools" type="radio" name="pop_city" id="tool-4" value="Mumbai" <?php if(!empty($get_customer_details) && $get_customer_details['customer_details']['city']== 'Mumbai') echo 'checked'; ?>>
                                                <label class="for-checkbox-tools" for="tool-4">
                                                    Mumbai
                                                </label><input class="checkbox-tools" type="radio" name="pop_city" id="tool-5" value="Gurgaon"  <?php if(!empty($get_customer_details) && $get_customer_details['customer_details']['city']== 'Gurgaon') echo 'checked'; ?>>
                                                <label class="for-checkbox-tools" for="tool-5">
                                                    Gurgaon
                                                </label>
                                                <input class="checkbox-tools" type="radio" name="pop_city" id="tool-6" value="Ahmedabad" <?php if(!empty($get_customer_details) && $get_customer_details['customer_details']['city']== 'Ahmedabad') echo 'checked'; ?>>
                                                <label class="for-checkbox-tools" for="tool-6">
                                                    Ahmedabad
                                                </label>
                                                <input class="checkbox-tools" type="radio" name="pop_city" id="tool-7" value="Thane" <?php if(!empty($get_customer_details) && $get_customer_details['customer_details']['city']== 'Thane') echo 'checked'; ?>>
                                                <label class="for-checkbox-tools" for="tool-7">
                                                    Thane
                                                </label>
                                            </div>



                                        </div>
                                    </div>
                                </div>

                                <button type="button" data-page="1" name="previous" class="previous action-button" value="Back" style="display:inline-block !important; margin:0px !important; margin-right:1rem; position:relative;  top:0 !important; float:none !important; height:fit-content !important;"  ><i class="fas fa-long-arrow-alt-left rht-aw"></i> Back </button>
                                <button type="button" data-page="1" name="next" class="next action-button submitpincode" value="Get Started" id="pin_code_continue" style="display:inline-block !important; right:0 !important;  position:relative; top:0 !important; float:none !important; height:fit-content !important;"  >Continue <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>

                            </fieldset>







                            <!-- Cultivation FIELD SET -->
                            <fieldset>
                                <!-- Begin What's Your First Name Field -->
                                <div class="hs_firstname field hs-form-field">

                                    <!-- <label for="firstname-99a6d115-5e68-4355-a7d0-529207feb0b3_2983">What's your First Name? *</label> -->
                                    <!-- <img src="images/input/member.png" class="img_icon_se"> -->

                                    <div id="members_selected_boxes"></div>
                                    <!-- <input id="firstname-99a6d115-5e68-4355-a7d0-529207feb0b3_2983" name="firstname" required="required" type="text" value="" placeholder="Who would you like to get insured?" data-rule-required="true" data-msg-required="Please include your first name" > -->
                                    <!-- <p class="go_back_arrow_input"><i class="icon flaticon-back"></i></p> -->
                                    <label for="firstname-99a6d115-5e68-4355-a7d0-529207feb0b3_2983"><span class="lbl-new">Who all would
                                                you
                                                like to insure</span></label>
                                    <div class="container">
                                        <div class="row" id="proposal_members">

                                            <?php

                                            $c=0;
                                            $existing_ids = [];
                                            $ch_ids=[];
                                            foreach ($family_construct_arr as $single_member) {
                                                $c++;
                                                $selected = false;
                                                $age = '';
                                                $claas= 'family_members_chk';
                                                if(!empty($get_customer_details) && !empty($get_customer_details['existing_all_members'])){
                                                    foreach ($get_customer_details['existing_all_members'] as $key => $value) {

                                                        if( $single_member['id'] == $value['member_type']){
                                                            $existing_ids[$value['member_type']][$value['id']]=$value['member_age'];
                                                            if(!in_array($value['member_type'], $ch_ids)){
                                                               $selected =true;
                                                               $age=$value['member_age']; 
                                                            }
                                                            $ch_ids[]=$value['member_type'];
                                                            
                                                           
                                                        }
                                                    }
                                                }
                                                if (!empty($single_member['member_min_age']) || !empty($single_member['member_min_age_days'])) {
                                                    if( $single_member['id'] ==5 ){
                                                        $claas= 'family_members_chk child_chk';
                                                        $son_min =!empty($single_member['member_min_age'])?$single_member['member_min_age']:$single_member['member_min_age_days'];
                                                        $son_max=$single_member['member_max_age'];
                                                        if(!empty($single_member['member_min_age_days'])){
                                                            $son_days=true;
                                                        }
                                                    }if( $single_member['id'] ==6 ){
                                                        $claas= 'family_members_chk child_chk';
                                                        $daughter_min =!empty($single_member['member_min_age'])?$single_member['member_min_age']:$single_member['member_min_age_days'];
                                                        $daughter_max=$single_member['member_max_age'];
                                                        if(!empty($single_member['member_min_age_days'])){
                                                            $daughter_days=true;
                                                        }
                                                    }
                                                    ?>
                                                    <div class="col-lg-6 col-md-12" id="<?php echo $single_member['member_type']; ?>_div">
                                                        <div class="row">
                                                            <div class="col-md-6 col-6"><input class="inp-cbx <?php echo $claas; ?>" id="<?php echo $single_member['member_type']; ?>" type="checkbox" data-rel_no="<?php echo $c;?>" value="<?php echo $single_member['id']; ?>" <?php if($selected) echo 'checked';?> /> <label class="cbx" for="<?php echo $single_member['member_type']; ?>"><span> <svg width="12px" height="10px">
                                                                                <use xlink:href="#check"></use>
                                                                            </svg></span><span><?php echo $single_member['member_type']; ?></span></label>
                                                                <!--SVG Sprites--> <svg class="inline-svg">
                                                                    <symbol id="check" viewbox="0 0 12 10">
                                                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                                    </symbol>
                                                                </svg>
                                                            </div>
                                                            <!--------end col-md-6------>
                                                            <div class="col-md-6 col-6 mb-w">
                                                                <div class="input-group margin_input"> 

                                                                    <?php 
                                                                    $kid_min = ''; 
                                                                    $kid_days = ''; 
                                                                    $kid_max='';
                                                                    if($single_member['member_type']=='Son'){
                                                                       $kid_min = $son_min; 
                                                                       $kid_days = $son_days; 
                                                                       $kid_max=$son_max;
                                                                    }else if($single_member['member_type']=='Daughter'){
                                                                       $kid_min = $daughter_min; 
                                                                       $kid_days = $daughter_days;
                                                                       $kid_max=$daughter_max;
                                                                    }if(!empty($kid_min)) {

                                                                    if(!empty($kid_days)){
                                                                        $kid_months = intdiv($kid_min,30);
                                                                        $kid_min=1;
                                                                        
                                                                    }
                                                                    if($single_member['id']=='5' || $single_member['id']=='6'){
                                                                        
                                                                ?>
                                                                <select class="theme-select-menu"  data-age_no="<?php echo $c;?>" >
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($kid_months)) { for ($m = $kid_months; $m <= 11; $m++) { $t=$m.' months';             ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $kid_min; $i <= $kid_max; $i++) {
                                                                                ?> <option value="<?php echo $i; ?>" <?php if($i==$age && strpos($age, 'months') === false){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>

                                                                </select>
                                                            <?php }}else{?>
                                                                    <select class="theme-select-menu" data-age_no="<?php echo $c;?>">
                                                                        <option value="0"><i class="fa fa-inr"></i> Select Age</option> <?php for ($i = $single_member['member_min_age']; $i <= $single_member['member_max_age']; $i++) {             ?> <option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                                    </select>
                                                                    <?php }?> </div> <!-- /.input-group -->
                                                            </div>
                                                            <!--------end col-md-6------>
                                                        </div>
                                                        <!--------end row------>
                                                    </div>
                                                    <?php
                                                } elseif ($single_member['is_adult'] == "N") {
                                                    $son_min = '';
                                                    $son_max = '';
                                                    $daughter_min = '';
                                                    $daughter_max = '';
                                                    ?>
                                                    <div class="col-lg-6 col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 col-6">
                                                                <input class="inp-cbx family_members_chk child_chk" id="<?php echo $single_member['member_type']; ?>" <?php if($selected) echo 'checked';?> type="checkbox" value="<?php echo $single_member['id']; ?>"  data-rel_no="<?php echo $c;?>"  />
                                                                <label class="cbx" for="<?php echo $single_member['member_type']; ?>"><span>
                                                                            <svg width="12px" height="10px">
                                                                                <use xlink:href="#check"></use>
                                                                            </svg></span><span><?php echo $single_member['member_type']; ?></span></label>
                                                                <!--SVG Sprites-->
                                                                <svg class="inline-svg">
                                                                    <symbol id="check" viewbox="0 0 12 10">
                                                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                                    </symbol>
                                                                </svg>
                                                            </div>
                                                            <!--------end col-md-6------>
                                                            <div class="col-md-6 col-6 mb-w">

                                                                <div class="input-group margin_input">
                                                                    
                                                                <select class="theme-select-menu"  data-age_no="<?php echo $c;?>" >
                                                                        <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                        <option value="3 months" <?php if('3 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 3 months</option>
                                                                        <option value="4 months" <?php if('4 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 4 months</option>
                                                                        <option value="5 months" <?php if('5 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 5 months</option>
                                                                        <option value="6 months" <?php if('6 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 6 months</option>
                                                                        <option value="7 months" <?php if('7 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 7 months</option>
                                                                        <option value="8 months" <?php if('8 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 8 months</option>
                                                                        <option value="9 months" <?php if('9 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 9 months</option>
                                                                        <option value="10 months" <?php if('10 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 10 months</option>
                                                                        <option value="11 months" <?php if('11 months'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 11 months</option>
                                                                        <option value="1 year" <?php if('1 year'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 1 Year</option>
                                                                        <option value="2 Years" <?php if('2 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 2 Years</option>
                                                                        <option value="3 Years" <?php if('3 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 3 Years</option>
                                                                        <option value="4 Years" <?php if('4 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 4 Years</option>
                                                                        <option value="5 Years" <?php if('5 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 5 Years</option>
                                                                        <option value="6 Years" <?php if('6 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 6 Years</option>
                                                                        <option value="7 Years" <?php if('7 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 7 Years</option>
                                                                        <option value="8 Years" <?php if('8 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 8 Years</option>
                                                                        <option value="9 Years" <?php if('9 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 9 Years</option>
                                                                        <option value="10 Years" <?php if('10 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 10 Years</option>
                                                                        <option value="11 Years" <?php if('11 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 11 Years</option>
                                                                        <option value="12 Years" <?php if('12 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 12 Years</option>
                                                                        <option value="13 Years" <?php if('13 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 13 Years</option>
                                                                        <option value="14 Years" <?php if('14 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 14 Years</option>
                                                                        <option value="15 Years" <?php if('15 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 15 Years</option>
                                                                        <option value="16 Years" <?php if('16 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 16 Years</option>
                                                                        <option value="17 Years" <?php if('17 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 17 Years</option>
                                                                        <option value="18 Years" <?php if('18 Years'==$age) echo 'selected';?>><i class="fa fa-inr"></i> 18 Years</option>
                                                                    
                                                                    </select>
                                                                </div> <!-- /.input-group -->

                                                            </div>
                                                            <!--------end col-md-6------>
                                                        </div>
                                                        <!--------end row------>
                                                    </div>
                                                    <?php
                                                }
                                            }

                                            ?>











                                            <div class="col-md-12 other-members-section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx" id="brother" type="checkbox" />
                                                        <label class="cbx" for="brother"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Brother</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu">
                                                                <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                <?php
                                                                for ($i = 18; $i <= 60; $i++) {
                                                                    ?> <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <div class="col-md-12 other-members-section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx" id="sister" type="checkbox" />
                                                        <label class="cbx" for="sister"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Sister</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu">
                                                                <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                <?php
                                                                for ($i = 18; $i <= 60; $i++) {
                                                                    ?> <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <div class="col-md-6 other-members-section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx" id="fatherinlaw" type="checkbox" />
                                                        <label class="cbx" for="fatherinlaw"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Father-In-Law</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu">
                                                                <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                <?php
                                                                for ($i = 18; $i <= 60; $i++) {
                                                                    ?> <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <div class="col-md-6 other-members-section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx" id="motherinlaw" type="checkbox" />
                                                        <label class="cbx" for="motherinlaw"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Mother-In-Law</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu">
                                                                <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                <?php
                                                                for ($i = 18; $i <= 60; $i++) {
                                                                    ?> <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <div class="col-md-6 other-members-section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx" id="grandfather" type="checkbox" />
                                                        <label class="cbx" for="grandfather"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Grand Father</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu">
                                                                <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                <?php
                                                                for ($i = 18; $i <= 60; $i++) {
                                                                    ?> <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <div class="col-md-6 other-members-section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx" id="grandmother" type="checkbox" />
                                                        <label class="cbx" for="grandmother"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Grand Mother</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu">
                                                                <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                <?php
                                                                for ($i = 18; $i <= 60; $i++) {
                                                                    ?> <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <?php
                                            $style = 'style="display: none;"';
                                            $age = ''; 
                                            $checked = ''; 
                                            if(!empty($existing_ids) && !empty($existing_ids[5]) && count($existing_ids[5])>1){
                                                $style = 'style="display: block;"';
                                                $age_array = array_values($existing_ids[5]);
                                                $age = $age_array[1];
                                                $checked = 'checked';
                                            }?>
                                            <div class="col-md-6" id="son2_section" <?php echo $style;?>>
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx family_members_chk child_chk" id="son2" type="checkbox" value="<?php echo $son_id; ?>"  data-rel_no="<?php echo $c+2;?>" <?php echo $checked;?> />
                                                        <label class="cbx" for="son2"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Son 2</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu" data-age_no="<?php echo $c+2;?>">
                                                            <?php if(!empty($son_min)) {

                                                                if(!empty($son_days)){
                                                                    $son_min_days = $son_min;
                                                                    $son_months = intdiv($son_min,30);
                                                                    $son_min=1;
                                                                    
                                                                }
                                                                ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($son_months)) { for ($m = $son_months; $m <= 11; $m++) {       $t=$m.' months';      ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $son_min; $i <= $son_max; $i++) {             ?> <option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                               
                                                            <?php } else{ ?>
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                    <option value="3 months"><i class="fa fa-inr"></i> 3 months</option>
                                                                    <option value="4 months"><i class="fa fa-inr"></i> 4 months</option>
                                                                    <option value="5 months"><i class="fa fa-inr"></i> 5 months</option>
                                                                    <option value="6 months"><i class="fa fa-inr"></i> 6 months</option>
                                                                    <option value="7 months"><i class="fa fa-inr"></i> 7 months</option>
                                                                    <option value="8 months"><i class="fa fa-inr"></i> 8 months</option>
                                                                    <option value="9 months"><i class="fa fa-inr"></i> 9 months</option>
                                                                    <option value="10 months"><i class="fa fa-inr"></i> 10 months</option>
                                                                    <option value="11 months"><i class="fa fa-inr"></i> 11 months</option>
                                                                    <option value="1 year"><i class="fa fa-inr"></i> 1 Year</option>
                                                                    <option value="2 Years"><i class="fa fa-inr"></i> 2 Years</option>
                                                                    <option value="3 Years"><i class="fa fa-inr"></i> 3 Years</option>
                                                                    <option value="4 Years"><i class="fa fa-inr"></i> 4 Years</option>
                                                                    <option value="5 Years"><i class="fa fa-inr"></i> 5 Years</option>
                                                                    <option value="6 Years"><i class="fa fa-inr"></i> 6 Years</option>
                                                                    <option value="7 Years"><i class="fa fa-inr"></i> 7 Years</option>
                                                                    <option value="8 Years"><i class="fa fa-inr"></i> 8 Years</option>
                                                                    <option value="9 Years"><i class="fa fa-inr"></i> 9 Years</option>
                                                                    <option value="10 Years"><i class="fa fa-inr"></i> 10 Years</option>
                                                                    <option value="11 Years"><i class="fa fa-inr"></i> 11 Years</option>
                                                                    <option value="12 Years"><i class="fa fa-inr"></i> 12 Years</option>
                                                                    <option value="13 Years"><i class="fa fa-inr"></i> 13 Years</option>
                                                                    <option value="14 Years"><i class="fa fa-inr"></i> 14 Years</option>
                                                                    <option value="15 Years"><i class="fa fa-inr"></i> 15 Years</option>
                                                                    <option value="16 Years"><i class="fa fa-inr"></i> 16 Years</option>
                                                                    <option value="17 Years"><i class="fa fa-inr"></i> 17 Years</option>
                                                                    <option value="18 Years"><i class="fa fa-inr"></i> 18 Years</option>
                                                               
                                                            <?php }?>
                                                        </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <?php
                                            $style = 'style="display: none;"';
                                            $age = ''; 
                                            $checked = ''; 
                                            if(!empty($existing_ids) && !empty($existing_ids[6]) && count($existing_ids[6])>1){
                                                $style = 'style="display: block;"';
                                                $age_array = array_values($existing_ids[6]);;
                                                $age = $age_array[1];
                                                $checked = 'checked';
                                            }?>
                                            <div class="col-md-6" id="daughter2_section" <?php echo $style;?>>
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx family_members_chk child_chk" id="daughter2" type="checkbox" value="<?php echo $daughter_id; ?>"   data-rel_no="<?php echo $c+3;?>" <?php echo $checked;?>/>
                                                        <label class="cbx" for="daughter2"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Daughter 2</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            
                                                                <select class="theme-select-menu" data-age_no="<?php echo $c+3;?>">

                                                                    <?php if(!empty($daughter_min)) {

                                                                if(!empty($daughter_days)){
                                                                    $daughter_months = intdiv($daughter_min,30);
                                                                    $daughter_min_days = $daughter_min;
                                                                    $daughter_min=1;
                                                                    
                                                                }
                                                                ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($daughter_months)) { for ($m = $daughter_months; $m <= 11; $m++) {    $t=$m.' months';         ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $daughter_min; $i <= $daughter_max; $i++) {             ?> <option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                            

                                                            <?php } else{ ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                    <option value="3 months"><i class="fa fa-inr"></i> 3 months</option>
                                                                    <option value="4 months"><i class="fa fa-inr"></i> 4 months</option>
                                                                    <option value="5 months"><i class="fa fa-inr"></i> 5 months</option>
                                                                    <option value="6 months"><i class="fa fa-inr"></i> 6 months</option>
                                                                    <option value="7 months"><i class="fa fa-inr"></i> 7 months</option>
                                                                    <option value="8 months"><i class="fa fa-inr"></i> 8 months</option>
                                                                    <option value="9 months"><i class="fa fa-inr"></i> 9 months</option>
                                                                    <option value="10 months"><i class="fa fa-inr"></i> 10 months</option>
                                                                    <option value="11 months"><i class="fa fa-inr"></i> 11 months</option>
                                                                    <option value="1 year"><i class="fa fa-inr"></i> 1 Year</option>
                                                                    <option value="2 Years"><i class="fa fa-inr"></i> 2 Years</option>
                                                                    <option value="3 Years"><i class="fa fa-inr"></i> 3 Years</option>
                                                                    <option value="4 Years"><i class="fa fa-inr"></i> 4 Years</option>
                                                                    <option value="5 Years"><i class="fa fa-inr"></i> 5 Years</option>
                                                                    <option value="6 Years"><i class="fa fa-inr"></i> 6 Years</option>
                                                                    <option value="7 Years"><i class="fa fa-inr"></i> 7 Years</option>
                                                                    <option value="8 Years"><i class="fa fa-inr"></i> 8 Years</option>
                                                                    <option value="9 Years"><i class="fa fa-inr"></i> 9 Years</option>
                                                                    <option value="10 Years"><i class="fa fa-inr"></i> 10 Years</option>
                                                                    <option value="11 Years"><i class="fa fa-inr"></i> 11 Years</option>
                                                                    <option value="12 Years"><i class="fa fa-inr"></i> 12 Years</option>
                                                                    <option value="13 Years"><i class="fa fa-inr"></i> 13 Years</option>
                                                                    <option value="14 Years"><i class="fa fa-inr"></i> 14 Years</option>
                                                                    <option value="15 Years"><i class="fa fa-inr"></i> 15 Years</option>
                                                                    <option value="16 Years"><i class="fa fa-inr"></i> 16 Years</option>
                                                                    <option value="17 Years"><i class="fa fa-inr"></i> 17 Years</option>
                                                                    <option value="18 Years"><i class="fa fa-inr"></i> 18 Years</option>
                                                               
                                                            <?php }?>
                                                             </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <?php
                                            $style = 'style="display: none;"';
                                            $age = ''; 
                                            $checked = ''; 
                                            if(!empty($existing_ids) && !empty($existing_ids[5]) && count($existing_ids[5])>2){
                                                $style = 'style="display: block;"';
                                                $age_array = array_values($existing_ids[5]);;
                                                $age = $age_array[2];
                                                $checked = 'checked';
                                            }?>
                                            <div class="col-md-6" id="son3_section" <?php echo $style;?>>
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx family_members_chk child_chk" id="son3" type="checkbox" value="<?php echo $son_id; ?>"   data-rel_no="<?php echo $c+4;?>" <?php echo $checked;?> />
                                                        <label class="cbx" for="son3"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Son 3</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu" data-age_no="<?php echo $c+4;?>">
                                                                
                                                                     <?php if(!empty($son_min)) {

                                                                if(!empty($son_days)){
                                                                    $son_months = intdiv($son_min_days,30);
                                                                    $son_min=1;
                                                                    
                                                                }
                                                                ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($son_months)) { for ($m = $son_months; $m <= 11; $m++) {  $t=$m.' months';           ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $son_min; $i <= $son_max; $i++) {             ?> <option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                               
                                                            <?php } else{ ?>
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                    <option value="3 months"><i class="fa fa-inr"></i> 3 months</option>
                                                                    <option value="4 months"><i class="fa fa-inr"></i> 4 months</option>
                                                                    <option value="5 months"><i class="fa fa-inr"></i> 5 months</option>
                                                                    <option value="6 months"><i class="fa fa-inr"></i> 6 months</option>
                                                                    <option value="7 months"><i class="fa fa-inr"></i> 7 months</option>
                                                                    <option value="8 months"><i class="fa fa-inr"></i> 8 months</option>
                                                                    <option value="9 months"><i class="fa fa-inr"></i> 9 months</option>
                                                                    <option value="10 months"><i class="fa fa-inr"></i> 10 months</option>
                                                                    <option value="11 months"><i class="fa fa-inr"></i> 11 months</option>
                                                                    <option value="1 year"><i class="fa fa-inr"></i> 1 Year</option>
                                                                    <option value="2 Years"><i class="fa fa-inr"></i> 2 Years</option>
                                                                    <option value="3 Years"><i class="fa fa-inr"></i> 3 Years</option>
                                                                    <option value="4 Years"><i class="fa fa-inr"></i> 4 Years</option>
                                                                    <option value="5 Years"><i class="fa fa-inr"></i> 5 Years</option>
                                                                    <option value="6 Years"><i class="fa fa-inr"></i> 6 Years</option>
                                                                    <option value="7 Years"><i class="fa fa-inr"></i> 7 Years</option>
                                                                    <option value="8 Years"><i class="fa fa-inr"></i> 8 Years</option>
                                                                    <option value="9 Years"><i class="fa fa-inr"></i> 9 Years</option>
                                                                    <option value="10 Years"><i class="fa fa-inr"></i> 10 Years</option>
                                                                    <option value="11 Years"><i class="fa fa-inr"></i> 11 Years</option>
                                                                    <option value="12 Years"><i class="fa fa-inr"></i> 12 Years</option>
                                                                    <option value="13 Years"><i class="fa fa-inr"></i> 13 Years</option>
                                                                    <option value="14 Years"><i class="fa fa-inr"></i> 14 Years</option>
                                                                    <option value="15 Years"><i class="fa fa-inr"></i> 15 Years</option>
                                                                    <option value="16 Years"><i class="fa fa-inr"></i> 16 Years</option>
                                                                    <option value="17 Years"><i class="fa fa-inr"></i> 17 Years</option>
                                                                    <option value="18 Years"><i class="fa fa-inr"></i> 18 Years</option>
                                                                
                                                            <?php }?>
                                                        </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <?php
                                            $style = 'style="display: none;"';
                                            $age = ''; 
                                            $checked = ''; 
                                            if(!empty($existing_ids) && !empty($existing_ids[6]) && count($existing_ids[6])>2){
                                                $style = 'style="display: block;"';
                                                $age_array = array_values($existing_ids[6]);;
                                                $age = $age_array[2];
                                                $checked = 'checked';
                                            }?>
                                            <div class="col-md-6" id="daughter3_section" <?php echo $style; ?>>
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx family_members_chk child_chk" id="daughter3" type="checkbox" value="<?php echo $daughter_id; ?>"   data-rel_no="<?php echo $c+5;?>" <?php echo $checked;?>/>
                                                        <label class="cbx" for="daughter3"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Daughter 3</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            
                                                                <select class="theme-select-menu" data-age_no="<?php echo $c+5;?>">
                                                                    <?php if(!empty($daughter_min)) {

                                                                if(!empty($daughter_days)){
                                                                    $daughter_months = intdiv($daughter_min_days,30);
                                                                    $daughter_min=1;
                                                                    
                                                                }
                                                                ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($daughter_months)) { for ($m = $daughter_months; $m <= 11; $m++) {  $t=$m.' months';           ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $daughter_min; $i <= $daughter_max; $i++) {             ?> <option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                            

                                                            <?php } else{ ?>
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                    <option value="3 months"><i class="fa fa-inr"></i> 3 months</option>
                                                                    <option value="4 months"><i class="fa fa-inr"></i> 4 months</option>
                                                                    <option value="5 months"><i class="fa fa-inr"></i> 5 months</option>
                                                                    <option value="6 months"><i class="fa fa-inr"></i> 6 months</option>
                                                                    <option value="7 months"><i class="fa fa-inr"></i> 7 months</option>
                                                                    <option value="8 months"><i class="fa fa-inr"></i> 8 months</option>
                                                                    <option value="9 months"><i class="fa fa-inr"></i> 9 months</option>
                                                                    <option value="10 months"><i class="fa fa-inr"></i> 10 months</option>
                                                                    <option value="11 months"><i class="fa fa-inr"></i> 11 months</option>
                                                                    <option value="1 year"><i class="fa fa-inr"></i> 1 Year</option>
                                                                    <option value="2 Years"><i class="fa fa-inr"></i> 2 Years</option>
                                                                    <option value="3 Years"><i class="fa fa-inr"></i> 3 Years</option>
                                                                    <option value="4 Years"><i class="fa fa-inr"></i> 4 Years</option>
                                                                    <option value="5 Years"><i class="fa fa-inr"></i> 5 Years</option>
                                                                    <option value="6 Years"><i class="fa fa-inr"></i> 6 Years</option>
                                                                    <option value="7 Years"><i class="fa fa-inr"></i> 7 Years</option>
                                                                    <option value="8 Years"><i class="fa fa-inr"></i> 8 Years</option>
                                                                    <option value="9 Years"><i class="fa fa-inr"></i> 9 Years</option>
                                                                    <option value="10 Years"><i class="fa fa-inr"></i> 10 Years</option>
                                                                    <option value="11 Years"><i class="fa fa-inr"></i> 11 Years</option>
                                                                    <option value="12 Years"><i class="fa fa-inr"></i> 12 Years</option>
                                                                    <option value="13 Years"><i class="fa fa-inr"></i> 13 Years</option>
                                                                    <option value="14 Years"><i class="fa fa-inr"></i> 14 Years</option>
                                                                    <option value="15 Years"><i class="fa fa-inr"></i> 15 Years</option>
                                                                    <option value="16 Years"><i class="fa fa-inr"></i> 16 Years</option>
                                                                    <option value="17 Years"><i class="fa fa-inr"></i> 17 Years</option>
                                                                    <option value="18 Years"><i class="fa fa-inr"></i> 18 Years</option>

                                                            <?php }?>

                                                                </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <?php
                                            $style = 'style="display: none;"';
                                            $age = ''; 
                                            $checked = ''; 
                                            if(!empty($existing_ids) && !empty($existing_ids[5]) && count($existing_ids[5])>3){
                                                $style = 'style="display: block;"';
                                                $age_array = array_values($existing_ids[5]);;
                                                $age = $age_array[3];
                                                $checked = 'checked';
                                            }?>
                                            <div class="col-md-6" id="son4_section" <?php echo $style;?>>
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx family_members_chk child_chk" id="son4" type="checkbox" value="<?php echo $son_id; ?>"   data-rel_no="<?php echo $c+6;?>" <?php echo $checked;?>/>
                                                        <label class="cbx" for="son4"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Son 4</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu" data-age_no="<?php echo $c+6;?>">
                                                            
                                                                     <?php if(!empty($son_min)) {

                                                                if(!empty($son_days)){
                                                                    $son_months = intdiv($son_min_days,30);
                                                                    $son_min=1;
                                                                    
                                                                }
                                                                ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($son_months)) { for ($m = $son_months; $m <= 11; $m++) {    $t=$m.' months';         ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $son_min; $i <= $son_max; $i++) {             ?><option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                               
                                                            <?php } else{ ?>
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                    <option value="3 months"><i class="fa fa-inr"></i> 3 months</option>
                                                                    <option value="4 months"><i class="fa fa-inr"></i> 4 months</option>
                                                                    <option value="5 months"><i class="fa fa-inr"></i> 5 months</option>
                                                                    <option value="6 months"><i class="fa fa-inr"></i> 6 months</option>
                                                                    <option value="7 months"><i class="fa fa-inr"></i> 7 months</option>
                                                                    <option value="8 months"><i class="fa fa-inr"></i> 8 months</option>
                                                                    <option value="9 months"><i class="fa fa-inr"></i> 9 months</option>
                                                                    <option value="10 months"><i class="fa fa-inr"></i> 10 months</option>
                                                                    <option value="11 months"><i class="fa fa-inr"></i> 11 months</option>
                                                                    <option value="1 year"><i class="fa fa-inr"></i> 1 Year</option>
                                                                    <option value="2 Years"><i class="fa fa-inr"></i> 2 Years</option>
                                                                    <option value="3 Years"><i class="fa fa-inr"></i> 3 Years</option>
                                                                    <option value="4 Years"><i class="fa fa-inr"></i> 4 Years</option>
                                                                    <option value="5 Years"><i class="fa fa-inr"></i> 5 Years</option>
                                                                    <option value="6 Years"><i class="fa fa-inr"></i> 6 Years</option>
                                                                    <option value="7 Years"><i class="fa fa-inr"></i> 7 Years</option>
                                                                    <option value="8 Years"><i class="fa fa-inr"></i> 8 Years</option>
                                                                    <option value="9 Years"><i class="fa fa-inr"></i> 9 Years</option>
                                                                    <option value="10 Years"><i class="fa fa-inr"></i> 10 Years</option>
                                                                    <option value="11 Years"><i class="fa fa-inr"></i> 11 Years</option>
                                                                    <option value="12 Years"><i class="fa fa-inr"></i> 12 Years</option>
                                                                    <option value="13 Years"><i class="fa fa-inr"></i> 13 Years</option>
                                                                    <option value="14 Years"><i class="fa fa-inr"></i> 14 Years</option>
                                                                    <option value="15 Years"><i class="fa fa-inr"></i> 15 Years</option>
                                                                    <option value="16 Years"><i class="fa fa-inr"></i> 16 Years</option>
                                                                    <option value="17 Years"><i class="fa fa-inr"></i> 17 Years</option>
                                                                    <option value="18 Years"><i class="fa fa-inr"></i> 18 Years</option>
                                                            <?php }?>
                                                        </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <?php
                                            $style = 'style="display: none;"';
                                            $age = ''; 
                                            $checked = ''; 
                                            if(!empty($existing_ids) && !empty($existing_ids[6]) && count($existing_ids[6])>3){
                                                $style = 'style="display: block;"';
                                                $age_array = array_values($existing_ids[6]);;
                                                $age = $age_array[3];
                                                $checked = 'checked';
                                            }?>
                                            <div class="col-md-6" id="daughter4_section" <?php echo $style; ?>>
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx family_members_chk child_chk" id="daughter4" type="checkbox" value="<?php echo $daughter_id; ?>"   data-rel_no="<?php echo $c+7;?>" <?php echo $checked;?>/>
                                                        <label class="cbx" for="daughter4"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Daughter 4</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                             <select class="theme-select-menu" data-age_no="<?php echo $c+7;?>">
                                                               
                                                                    
                                                                <?php if(!empty($daughter_min)) {

                                                                if(!empty($daughter_days)){
                                                                    $daughter_months = intdiv($daughter_min_days,30);
                                                                    $daughter_min=1;
                                                                    
                                                                }
                                                                ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($daughter_months)) { for ($m = $daughter_months; $m <= 11; $m++) {   $t=$m.' months';          ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $daughter_min; $i <= $daughter_max; $i++) {             ?> <option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                            

                                                            <?php } else{ ?>
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                    <option value="3 months"><i class="fa fa-inr"></i> 3 months</option>
                                                                    <option value="4 months"><i class="fa fa-inr"></i> 4 months</option>
                                                                    <option value="5 months"><i class="fa fa-inr"></i> 5 months</option>
                                                                    <option value="6 months"><i class="fa fa-inr"></i> 6 months</option>
                                                                    <option value="7 months"><i class="fa fa-inr"></i> 7 months</option>
                                                                    <option value="8 months"><i class="fa fa-inr"></i> 8 months</option>
                                                                    <option value="9 months"><i class="fa fa-inr"></i> 9 months</option>
                                                                    <option value="10 months"><i class="fa fa-inr"></i> 10 months</option>
                                                                    <option value="11 months"><i class="fa fa-inr"></i> 11 months</option>
                                                                    <option value="1 year"><i class="fa fa-inr"></i> 1 Year</option>
                                                                    <option value="2 Years"><i class="fa fa-inr"></i> 2 Years</option>
                                                                    <option value="3 Years"><i class="fa fa-inr"></i> 3 Years</option>
                                                                    <option value="4 Years"><i class="fa fa-inr"></i> 4 Years</option>
                                                                    <option value="5 Years"><i class="fa fa-inr"></i> 5 Years</option>
                                                                    <option value="6 Years"><i class="fa fa-inr"></i> 6 Years</option>
                                                                    <option value="7 Years"><i class="fa fa-inr"></i> 7 Years</option>
                                                                    <option value="8 Years"><i class="fa fa-inr"></i> 8 Years</option>
                                                                    <option value="9 Years"><i class="fa fa-inr"></i> 9 Years</option>
                                                                    <option value="10 Years"><i class="fa fa-inr"></i> 10 Years</option>
                                                                    <option value="11 Years"><i class="fa fa-inr"></i> 11 Years</option>
                                                                    <option value="12 Years"><i class="fa fa-inr"></i> 12 Years</option>
                                                                    <option value="13 Years"><i class="fa fa-inr"></i> 13 Years</option>
                                                                    <option value="14 Years"><i class="fa fa-inr"></i> 14 Years</option>
                                                                    <option value="15 Years"><i class="fa fa-inr"></i> 15 Years</option>
                                                                    <option value="16 Years"><i class="fa fa-inr"></i> 16 Years</option>
                                                                    <option value="17 Years"><i class="fa fa-inr"></i> 17 Years</option>
                                                                    <option value="18 Years"><i class="fa fa-inr"></i> 18 Years</option>
                                                                
                                                            <?php }?>
                                                        </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <?php
                                            $style = 'style="display: none;"';
                                            $age = ''; 
                                            $checked = ''; 
                                            if(!empty($existing_ids) && !empty($existing_ids[5]) && count($existing_ids[5])>4){
                                                $style = 'style="display: block;"';
                                                $age_array = array_values($existing_ids[5]);;
                                                $age = $age_array[4];
                                                $checked = 'checked';
                                            }?>
                                            <div class="col-md-6" id="son5_section" <?php echo $style;?>>
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx family_members_chk child_chk" id="son5" type="checkbox" value="<?php echo $son_id; ?>"  data-rel_no="<?php echo $c+8;?>" <?php echo $checked;?>/>
                                                        <label class="cbx" for="son5"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Son 5</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                             <select class="theme-select-menu" data-age_no="<?php echo $c+8;?>">
                                                               
                                                                     <?php if(!empty($son_min)) {

                                                                if(!empty($son_days)){
                                                                    $son_months = intdiv($son_min_days,30);
                                                                    $son_min=1;
                                                                }
                                                                ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($son_months)) { for ($m = $son_months; $m <= 11; $m++) {    $t=$m.' months';         ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $son_min; $i <= $son_max; $i++) {             ?> <option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                               
                                                            <?php } else{ ?>
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                    <option value="3 months"><i class="fa fa-inr"></i> 3 months</option>
                                                                    <option value="4 months"><i class="fa fa-inr"></i> 4 months</option>
                                                                    <option value="5 months"><i class="fa fa-inr"></i> 5 months</option>
                                                                    <option value="6 months"><i class="fa fa-inr"></i> 6 months</option>
                                                                    <option value="7 months"><i class="fa fa-inr"></i> 7 months</option>
                                                                    <option value="8 months"><i class="fa fa-inr"></i> 8 months</option>
                                                                    <option value="9 months"><i class="fa fa-inr"></i> 9 months</option>
                                                                    <option value="10 months"><i class="fa fa-inr"></i> 10 months</option>
                                                                    <option value="11 months"><i class="fa fa-inr"></i> 11 months</option>
                                                                    <option value="1 year"><i class="fa fa-inr"></i> 1 Year</option>
                                                                    <option value="2 Years"><i class="fa fa-inr"></i> 2 Years</option>
                                                                    <option value="3 Years"><i class="fa fa-inr"></i> 3 Years</option>
                                                                    <option value="4 Years"><i class="fa fa-inr"></i> 4 Years</option>
                                                                    <option value="5 Years"><i class="fa fa-inr"></i> 5 Years</option>
                                                                    <option value="6 Years"><i class="fa fa-inr"></i> 6 Years</option>
                                                                    <option value="7 Years"><i class="fa fa-inr"></i> 7 Years</option>
                                                                    <option value="8 Years"><i class="fa fa-inr"></i> 8 Years</option>
                                                                    <option value="9 Years"><i class="fa fa-inr"></i> 9 Years</option>
                                                                    <option value="10 Years"><i class="fa fa-inr"></i> 10 Years</option>
                                                                    <option value="11 Years"><i class="fa fa-inr"></i> 11 Years</option>
                                                                    <option value="12 Years"><i class="fa fa-inr"></i> 12 Years</option>
                                                                    <option value="13 Years"><i class="fa fa-inr"></i> 13 Years</option>
                                                                    <option value="14 Years"><i class="fa fa-inr"></i> 14 Years</option>
                                                                    <option value="15 Years"><i class="fa fa-inr"></i> 15 Years</option>
                                                                    <option value="16 Years"><i class="fa fa-inr"></i> 16 Years</option>
                                                                    <option value="17 Years"><i class="fa fa-inr"></i> 17 Years</option>
                                                                    <option value="18 Years"><i class="fa fa-inr"></i> 18 Years</option>
                                                            <?php }?>
                                                        </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <?php
                                            $style = 'style="display: none;"';
                                            $age = ''; 
                                            $checked = ''; 
                                            if(!empty($existing_ids) && !empty($existing_ids[6]) && count($existing_ids[6])>4){
                                                $style = 'style="display: block;"';
                                                $age_array = array_values($existing_ids[6]);;
                                                $age = $age_array[4];
                                                $checked = 'checked';
                                            }?>
                                            <div class="col-md-6" id="daughter5_section" <?php echo $style; ?> >
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input class="inp-cbx family_members_chk child_chk" id="daughter5" type="checkbox" value="<?php echo $daughter_id; ?>"  data-rel_no="<?php echo $c+9;?>" <?php echo $checked;?>/>
                                                        <label class="cbx" for="daughter5"><span>
                                                                    <svg width="12px" height="10px">
                                                                        <use xlink:href="#check"></use>
                                                                    </svg></span><span>Daughter 5</span></label>
                                                        <!--SVG Sprites-->
                                                        <svg class="inline-svg">
                                                            <symbol id="check" viewbox="0 0 12 10">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </symbol>
                                                        </svg>
                                                    </div>
                                                    <!--------end col-md-6------>
                                                    <div class="col-md-6 col-6 mb-w">

                                                        <div class="input-group margin_input">
                                                            <select class="theme-select-menu" data-age_no="<?php echo $c+9;?>">
                                                                
                                                                    <?php if(!empty($daughter_min)) {

                                                                if(!empty($daughter_days)){
                                                                    $daughter_months = intdiv($daughter_min_days,30);
                                                                    $daughter_min=1;
                                                                    
                                                                }
                                                                ?>
                                                                
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option> 
                                                                    <?php if(!empty($daughter_months)) { for ($m = $daughter_months; $m <= 11; $m++) {  $t=$m.' months';           ?> <option value="<?php echo $m; ?> months" <?php if($t==$age){ echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $m; ?> months</option> <?php             } }            ?>


                                                                    <?php for ($i = $daughter_min; $i <= $daughter_max; $i++) {             ?> <option value="<?php echo $i; ?>" <?php if($i==$age) {echo 'selected';}?>><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option> <?php             }             ?>
                                                            

                                                            <?php } else{ ?>
                                                                    <option value="0"><i class="fa fa-inr"></i> Select Age</option>
                                                                    <option value="3 months"><i class="fa fa-inr"></i> 3 months</option>
                                                                    <option value="4 months"><i class="fa fa-inr"></i> 4 months</option>
                                                                    <option value="5 months"><i class="fa fa-inr"></i> 5 months</option>
                                                                    <option value="6 months"><i class="fa fa-inr"></i> 6 months</option>
                                                                    <option value="7 months"><i class="fa fa-inr"></i> 7 months</option>
                                                                    <option value="8 months"><i class="fa fa-inr"></i> 8 months</option>
                                                                    <option value="9 months"><i class="fa fa-inr"></i> 9 months</option>
                                                                    <option value="10 months"><i class="fa fa-inr"></i> 10 months</option>
                                                                    <option value="11 months"><i class="fa fa-inr"></i> 11 months</option>
                                                                    <option value="1 year"><i class="fa fa-inr"></i> 1 Year</option>
                                                                    <option value="2 Years"><i class="fa fa-inr"></i> 2 Years</option>
                                                                    <option value="3 Years"><i class="fa fa-inr"></i> 3 Years</option>
                                                                    <option value="4 Years"><i class="fa fa-inr"></i> 4 Years</option>
                                                                    <option value="5 Years"><i class="fa fa-inr"></i> 5 Years</option>
                                                                    <option value="6 Years"><i class="fa fa-inr"></i> 6 Years</option>
                                                                    <option value="7 Years"><i class="fa fa-inr"></i> 7 Years</option>
                                                                    <option value="8 Years"><i class="fa fa-inr"></i> 8 Years</option>
                                                                    <option value="9 Years"><i class="fa fa-inr"></i> 9 Years</option>
                                                                    <option value="10 Years"><i class="fa fa-inr"></i> 10 Years</option>
                                                                    <option value="11 Years"><i class="fa fa-inr"></i> 11 Years</option>
                                                                    <option value="12 Years"><i class="fa fa-inr"></i> 12 Years</option>
                                                                    <option value="13 Years"><i class="fa fa-inr"></i> 13 Years</option>
                                                                    <option value="14 Years"><i class="fa fa-inr"></i> 14 Years</option>
                                                                    <option value="15 Years"><i class="fa fa-inr"></i> 15 Years</option>
                                                                    <option value="16 Years"><i class="fa fa-inr"></i> 16 Years</option>
                                                                    <option value="17 Years"><i class="fa fa-inr"></i> 17 Years</option>
                                                                    <option value="18 Years"><i class="fa fa-inr"></i> 18 Years</option>
                                                                
                                                            <?php }?>
                                                        </select>
                                                        </div> <!-- /.input-group -->

                                                    </div>
                                                    <!--------end col-md-6------>
                                                </div>
                                                <!--------end row------>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-danger text-sm" style="display: none;" id="member-age-validation-message">Please select age of member
                                                </p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-danger text-sm" style="display: none;" id="adult-mandatory">
                                                </p>
                                            </div>
                                            <?php if(!isset($child_count) || (isset($child_count) && $child_count >1)) {
                                                if(empty($product_gender) || (!empty($product_gender) && $product_gender=='B') ||  (!empty($product_gender) && $product_gender=='M') ){?>
                                                <hr class="hr_i">
                                                <div class="col-md-6 col-lg-4 col-6">
                                                    <button type="button" class="video-button-one btn-son" onclick="addSon(<?php echo $son_id; ?>)"><i class="fa fa-plus-circle i_add"></i> Add Son</button>
                                                </div>
                                            <?php }?>
                                            <?php if(empty($product_gender) ||  (!empty($product_gender) && $product_gender=='B')){?>
                                                <div class="col-md-6 col-lg-4 col-6">
                                                    <button type="button" class="video-button-one btn-dt" onclick="addDaughter(<?php echo $daughter_id; ?>)"><i class="fa fa-plus-circle i_add"></i>
                                                        Add Daughter</button>
                                                </div>
                                                <hr class="hr_i">
                                            <?php } }{

                                            }?>
                                            <!-- <div class="col-md-4">
                                                <button type="button" class="video-button-one"
                                                    onclick="showOtherMembers()"><i
                                                        class="fa fa-plus-circle i_add"></i> Other Members</button>
                                            </div> -->
                                            <!-- <div class="col-md-12 col-lg-4">
                                                <button type="button" class="video-button-one" data-toggle="modal" data-target="#m"><i class="fa fa-plus-circle i_add"></i> Other
                                                    Members</button>
                                            </div> -->

                                            <hr class="hr_i">
                                        </div>
                                        <!-----------end row--------------->
                                    </div>
                                    <!-----------end container--------------->
                                    <span class="error1" style="display: none;">
                                            <i class="error-log fa fa-exclamation-triangle"></i>
                                        </span>
                                </div>

                                <!-- End What's Your First Name Field -->
                                <!-- End Total Number of Donors in Year 1 Field -->

                                <?php if(!empty($_SESSION['product_id_session']) && empty($deductable))
                                {
                                    ?>
                                    <button type="button" class="hs-button primary large action-button next SubmitInsuredtype" onclick="validateMembers(event)"  value="View Quotes" style="left: -304px; position: initial;">View Quotes <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>
                                <?php }
                                else if(!empty($_SESSION['product_id_session']) && !empty($deductable)){ ?>
                                    <button type="button" data-page="1" name="next" class="next action-button SubmitInsuredtype1" onclick="validateMembers(event)" value="Get Started" style=" left: -304px; position: initial;">Continue <i class="fas fa-long-arrow-alt-rigth rht-aw"></i></button>

                                <?php }
                                else
                                {
                                    ?>
                                    <button type="button" data-page="1" name="next" class="next action-button sum_insured_type_show" onclick="validateMembers(event)" value="Get Started" style=" left: -304px; position: initial;">Continue <i class="fas fa-long-arrow-alt-rigth rht-aw"></i></button>
                                    <?php
                                }
                                ?>
                                <button type="button" data-page="1" name="previous" class="previous action-button" style="float: left;  left: -304px; position: initial;" value="Back" ><i class="fas fa-long-arrow-alt-left lft-aw"></i> Back </button>
                            </fieldset>



                            <!-- Cultivation2 FIELD SET -->
                            <?php if(empty($_SESSION['product_id_session']))
                            {
                                ?>
                                <fieldset class="plan_type">
                                    <!--  <div class="plan_type"> -->
                                    <!-- Begin What's Your First Name Field -->
                                    <div class="hs_firstname field hs-form-field">
                                        <!-- <p class="go_back_arrow_input"><i class="icon flaticon-back"></i></p> -->
                                        <label for="firstname-99a6d115-5e68-4355-a7d0-529207feb0b3_2983"><span class="lbl-new">Which plan
                                                    would you like to opt for?</span></label>
                                        <!-- <label for="firstname-99a6d115-5e68-4355-a7d0-529207feb0b3_2983">What's your First Name? *</label> -->
                                        <div class="page">
                                            <!-- tabs -->
                                            <div class="pcss3t pcss3t-effect-scale pcss3t-theme-1 sum_insured_type">

                                            </div>
                                            <!--/ tabs -->
                                        </div>
                                        <span class="error1" style="display: none;">
                                                <i class="error-log fa fa-exclamation-triangle"></i>
                                            </span>
                                    </div>
                                    <!-- End What's Your First Name Field -->
                                    <!-- End Total Number of Donors in Year 1 Field -->
                                    <button type="button" class="hs-button primary large action-button next SubmitInsuredtype" value="View Quotes" style="left: -304px; position: initial;">View Quotes <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>

                                    <button type="button" data-page="1" name="previous" class="previous action-button" value="Back"  style="left: -304px; position: initial;"><i class="fas fa-long-arrow-alt-left lft-aw"></i> Back </button>


                                    <!-- <button type="button" data-page="1" name="next" class="next action-button SubmitInsuredtype" value="Get Started" style="float: left;  left: -304px; position: initial;">Continue <i class="fas fa-long-arrow-alt-right rht-aw"></i>
                                    </button>
                                </div> -->
                                </fieldset>
                                <?php
                            }
                            ?>
                            <!-- Cultivation2 FIELD SET -->
                            <!-- <fieldset class="disease">
                                <div class="hs_firstname field hs-form-field">
                                    <label for="firstname-99a6d115-5e68-4355-a7d0-529207feb0b3_2983"><span clasS="lbl-new">Does any member
                                            has any medical history?</span>
                                    </label>
                                    <div class="page page_five_padding_t">
                                        <div class="pcss3t pcss3t-effect-scale pcss3t-theme-1">
                                            <input type="radio" name="disease_check" id="tab3" class="tab-content-first details disease_check" value="Yes">
                                            <label for="tab3" style="padding: 17px 68px">
                                                Yes
                                            </label>

                                            <input type="radio" name="disease_check" id="tab4" class="tab-content-2
                                                No
                                            </label>

                                            <ul>
                                                <input class="inp-cbx lead_id" id="hidden_lead_id" name="lead_id" type="hidden">
                                                <input class="inp-cbx cust_id" id="hidden_cust_id" name="cust_id" type="hidden">

                                                <li class="tab-content tab-content-first typography">
                                                    <h1>Let us know if it is from any of the following?</h1>
                                                    <div class="row margin_following_diabetes disease_data ml-2">



                                                    </div>
                                                </li>

                                                <li class="tab-content tab-content-2 typography">
                                                    <h1>Great, nice to know that</h1>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <span class="error1" style="display: none;">
                                        <i class="error-log fa fa-exclamation-triangle"></i>
                                    </span>
                                </div>

                                <button type="button" class="hs-button primary large action-button next submitDisease" value="View Quotes" style="float: left;  left: -304px; position: initial;">View Quotes <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>


                            </fieldset> -->
                        </form>
                    </div>
                    <!--------------enn col-md-6-------------->
                </div>
            </div>


        </div>

    </div> <!-- /.full-width-container -->
    <?php if(!empty($deductable)){ 
        ?>
    <div class="modal fade" id="deductable_modal" style="display: none; padding-right: 16px;">
        <div class="modal-dialog  modal-dialog-centered"  role="document" style="max-width:800px">
            <div class="modal-content login-card" style="padding-bottom:0px !important;">
                <div class="modal-header">
                    <h5 class="modal-title head-tittle" style="margin:0 !important;"><span>Select Deductible</span></h5>
                    <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close"><span class="">×</span></button>
                </div>
                <div class="modal-body">
                    <form id="submit_member_data">

                        <div class="mb-2 col-md-12 row ">
                            <div class="col-md-12">
                                <div class="form-group">

                                   
                                    <label class="col-form-label">Select Deductible<span style="color:#FF0000">*</span></label>
                                    
                                    <div style="display:flex;"> 
                                    <select class="form-control" name="deductible" id="deductible"><option data-opt="" value="">Select Deductible</option>
                                        <?php foreach ($deductable as $ded) {
                                            $selected='';
                                            if($ded==$deductable_amount){
                                                $selected='selected';
                                            }
                                            echo '<option value="'.$ded.'" '.$selected.'>'.$ded.'</option>';
                                            // code...
                                        }?>
                                    </select> 
                                    <?php if(!empty($deductible_text)){ ?>
                                    <button type="button" class="btn-none  ml-3"  data-toggle="modal" data-target="#deductibleText"><i class="fa fa-info-circle">
                                    </i></button>
                                <?php }?>
                                    </div>  

                                </div> 
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-footer" style="margin:0 !important; border-top-left-radius:0 !important; border-top-right-radius:0 !important; ">
                    <button type="button" class="btn btn-submit SubmitInsuredtype" id ="deductible_submit">View Quotes <i class="ti-check"></i></button>
                </div>

            </div>
        </div>

    </div>

    <div class="modal fade" id="deductibleText" tabindex="-1" role="dialog" aria-labelledby="deductibleTextTitle">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Deductible</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php echo $deductible_text;?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        </div>
                    </div>
                </div>
            </div>
    <?php }?>

    <!-- ^^^^^^^^^^^^^^^^^^^^^^^^^^ Modal ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ -->
    <div id="m" class="modal" data-backdrop="true" style="z-index: 111111">
        <div class="modal-dialog" style="max-width: 693px; margin-top: 170px;">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom-color: #fff;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    border-bottom-right-radius: 0px;
    border-bottom-left-radius: 0px;">
                    <div class="bb_modal">
                        <h5 class="modal-title font_family_input_title">All your family members</h5>
                    </div>

                </div>
                <button type="button" data-target="#m" data-toggle="modal" class="btn btn-white recom_close_css" style="margin-top: -8px;"><i class="fa fa-close"></i></button>
                <div class="modal-body  p-lg">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 other-members-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="inp-cbx" id="brother" type="checkbox" />
                                        <label class="cbx" for="brother"><span>
                                                    <svg width="12px" height="10px">
                                                        <use xlink:href="#check"></use>
                                                    </svg></span><span>Brother</span></label>
                                        <!--SVG Sprites-->
                                        <svg class="inline-svg">
                                            <symbol id="check" viewbox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </symbol>
                                        </svg>
                                    </div>
                                    <!--------end col-md-6------>
                                    <div class="col-md-5">

                                        <div class="input-group margin_top_modal_input margin_input">
                                            <select class="theme-select-menu">
                                                <option value="0"><i class="fa fa-inr"></i> Select
                                                    Age</option>
                                                <?php
                                                for ($i = 18; $i <= 60; $i++) {
                                                    ?>
                                                    <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                    <?php
                                                }
                                                ?>

                                            </select>
                                        </div> <!-- /.input-group -->

                                    </div>
                                    <!--------end col-md-6------>
                                </div>
                                <!--------end row------>
                            </div>
                            <div class="col-md-6 other-members-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="inp-cbx" id="sister" type="checkbox" />
                                        <label class="cbx" for="sister"><span>
                                                    <svg width="12px" height="10px">
                                                        <use xlink:href="#check"></use>
                                                    </svg></span><span>Sister</span></label>
                                        <!--SVG Sprites-->
                                        <svg class="inline-svg">
                                            <symbol id="check" viewbox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </symbol>
                                        </svg>
                                    </div>
                                    <!--------end col-md-6------>
                                    <div class="col-md-5">

                                        <div class="input-group margin_top_modal_input margin_input">
                                            <select class="theme-select-menu">
                                                <option value="0"><i class="fa fa-inr"></i> Select
                                                    Age</option>
                                                <?php
                                                for ($i = 18; $i <= 60; $i++) {
                                                    ?>
                                                    <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div> <!-- /.input-group -->

                                    </div>
                                    <!--------end col-md-6------>
                                </div>
                                <!--------end row------>
                            </div>
                            <div class="col-md-6 other-members-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="inp-cbx" id="fatherinlaw" type="checkbox" />
                                        <label class="cbx" for="fatherinlaw"><span>
                                                    <svg width="12px" height="10px">
                                                        <use xlink:href="#check"></use>
                                                    </svg></span><span>Father-In-Law</span></label>
                                        <!--SVG Sprites-->
                                        <svg class="inline-svg">
                                            <symbol id="check" viewbox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </symbol>
                                        </svg>
                                    </div>
                                    <!--------end col-md-6------>
                                    <div class="col-md-5">

                                        <div class="input-group margin_input margin_top_modal_input">
                                            <select class="theme-select-menu">
                                                <option value="0"><i class="fa fa-inr"></i> Select
                                                    Age</option>
                                                <?php
                                                for ($i = 18; $i <= 60; $i++) {
                                                    ?>
                                                    <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div> <!-- /.input-group -->

                                    </div>
                                    <!--------end col-md-6------>
                                </div>
                                <!--------end row------>
                            </div>
                            <div class="col-md-6 other-members-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="inp-cbx" id="motherinlaw" type="checkbox" />
                                        <label class="cbx" for="motherinlaw"><span>
                                                    <svg width="12px" height="10px">
                                                        <use xlink:href="#check"></use>
                                                    </svg></span><span>Mother-In-Law</span></label>
                                        <!--SVG Sprites-->
                                        <svg class="inline-svg">
                                            <symbol id="check" viewbox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </symbol>
                                        </svg>
                                    </div>
                                    <!--------end col-md-6------>
                                    <div class="col-md-5">

                                        <div class="input-group margin_input margin_top_modal_input">
                                            <select class="theme-select-menu">
                                                <option value="0"><i class="fa fa-inr"></i> Select
                                                    Age</option>
                                                <?php
                                                for ($i = 18; $i <= 60; $i++) {
                                                    ?>
                                                    <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                    <?php
                                                }
                                                ?>

                                            </select>
                                        </div> <!-- /.input-group -->

                                    </div>
                                    <!--------end col-md-6------>
                                </div>
                                <!--------end row------>
                            </div>
                            <div class="col-md-6 other-members-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="inp-cbx" id="grandfather" type="checkbox" />
                                        <label class="cbx" for="grandfather"><span>
                                                    <svg width="12px" height="10px">
                                                        <use xlink:href="#check"></use>
                                                    </svg></span><span>Grand Father</span></label>
                                        <!--SVG Sprites-->
                                        <svg class="inline-svg">
                                            <symbol id="check" viewbox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </symbol>
                                        </svg>
                                    </div>
                                    <!--------end col-md-6------>
                                    <div class="col-md-5">

                                        <div class="input-group margin_input margin_top_modal_input">
                                            <select class="theme-select-menu">
                                                <option value="0"><i class="fa fa-inr"></i> Select
                                                    Age</option>
                                                <?php
                                                for ($i = 18; $i <= 60; $i++) {
                                                    ?>
                                                    <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                    <?php
                                                }
                                                ?>

                                            </select>
                                        </div> <!-- /.input-group -->

                                    </div>
                                    <!--------end col-md-6------>
                                </div>
                                <!--------end row------>
                            </div>
                            <div class="col-md-6 other-members-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="inp-cbx" id="grandmother" type="checkbox" />
                                        <label class="cbx" for="grandmother"><span>
                                                    <svg width="12px" height="10px">
                                                        <use xlink:href="#check"></use>
                                                    </svg></span><span>Grand Mother</span></label>
                                        <!--SVG Sprites-->
                                        <svg class="inline-svg">
                                            <symbol id="check" viewbox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </symbol>
                                        </svg>
                                    </div>
                                    <!--------end col-md-6------>
                                    <div class="col-md-5">

                                        <div class="input-group margin_input margin_top_modal_input">
                                            <select class="theme-select-menu">
                                                <option value="0"><i class="fa fa-inr"></i> Select
                                                    Age</option>
                                                <?php
                                                for ($i = 18; $i <= 60; $i++) {
                                                    ?>
                                                    <option value="<?php echo $i; ?>"><i class="fa fa-inr"></i> <?php echo $i; ?> Years</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div> <!-- /.input-group -->

                                    </div>
                                    <!--------end col-md-6------>
                                </div>
                                <!--------end row------>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-bottom-color: #fff;
    border-top-left-radius: 0px;
    border-top-right-radius: 0px;
    border-bottom-right-radius: 14px;
    border-bottom-left-radius: 14px;">
                    <button type="button" class="btn btn-primary add_modal_btn_input" data-dismiss="modal">Add</button>
                </div>
            </div><!-- /.modal-content -->
        </div>
    </div>
    <!-- / .modal -->

    <!-- .modal -- allocate -->
    <div id="a_p_imp" class="modal fade model" data-backdrop="true">
        <div class="modal-dialog modal-sm animate" id="animate">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" style="font-size: 20px;">Important Highlight
                    </h6>
                    <button type="button" class="btn btn-white" data-dismiss="modal" style="border-radius: 50px;"><i class="fa fa-close"></i></button>
                </div>
                <div class="modal-body text-center p-lg">
                    <div class="container">
                        <div class="row">
                            <p style="font-size: 15px;">Your premium has increased/decreased due to following
                                reasons.<br>Your Premium is<b> <i class="fa fa-inr"></i> 6,149</b></p>
                        </div>

                    </div>
                </div>
                <a href="#">
                    <div class="modal-footer">
                        <button class="btn btn-primary">Continue</button>
                        <button class="btn btn-primary_1">Edit Details</button>
                    </div>
                </a>
            </div><!-- /.modal-content -->
        </div>
    </div>
    <!-- / important highlight modal -->


    <!-- .modal -- allocate -->
    <div id="a_p" class="modal fade model" data-backdrop="true">
        <div class="modal-dialog modal-lg animate" id="animate">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Allocate Percentage
                    </h6>
                    <button type="button" class="btn btn-white" data-dismiss="modal" style="border-radius: 50px;"><i class="fa fa-close"></i></button>
                </div>
                <div class="modal-body text-center p-lg">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="single-blog-post blog-text-style post_data_addon">
                                    <div class="post-data post_data_addon_pad box_shadow_proposal">
                                        <div class="row">
                                            <div class="col-lg-2 col-4 mb_addon_mo">
                                                <a class="nav-link all_percentage" data-toggle="tab" href="#service">Wife</a>
                                            </div>
                                            <div class="col-lg-5 col-4 text-center  pad_0 mar_top_mo mb_addon_mo">
                                                <img src="images/pro_01.png" class="pro_all_img">

                                                <form action="#" class="checkout-form">


                                                    <div class="row">
                                                        <div class="col-lg-12"><input type="text" placeholder="Percentage*" class="single-input-wrapper"></div>

                                                    </div> <!-- /.row -->

                                                </form> <!-- /.checkout-form -->
                                            </div>
                                            <div class="col-lg-5 col-4 text-center  mar_top_mo pad_0 mb_addon_mo">
                                                <img src="images/pro_02.png" class="pro_all_img">
                                                <p class="font_bold_proposal_addon margin_all">70 Lacs</p>
                                            </div>



                                        </div>
                                    </div> <!-- /.post-data -->

                                </div>
                                <!------single post----------->
                            </div>
                            <div class="col-md-6">
                                <div class="single-blog-post blog-text-style post_data_addon">
                                    <div class="post-data post_data_addon_pad box_shadow_proposal">
                                        <div class="row">
                                            <div class="col-lg-2 col-4 mb_addon_mo">
                                                <a class="nav-link all_percentage" data-toggle="tab" href="#service">Mother</a>
                                                <!-- <a href="#" class="contact-us white-shdw-button-login">Mother</a> -->
                                            </div>
                                            <div class="col-lg-5 col-4 text-center  pad_0 mar_top_mo mb_addon_mo">
                                                <img src="images/pro_01.png" class="pro_all_img">

                                                <form action="#" class="checkout-form">

                                                    <div class="user-profile-data">
                                                        <div class="row">
                                                            <div class="col-lg-12"><input type="text" placeholder="Percentage*" class="single-input-wrapper"></div>

                                                        </div> <!-- /.row -->
                                                    </div> <!-- /.user-profile-data -->
                                                </form> <!-- /.checkout-form -->
                                            </div>
                                            <div class="col-lg-5 col-4 text-center  mar_top_mo pad_0 mb_addon_mo">
                                                <img src="images/pro_02.png" class="pro_all_img">
                                                <p class="font_bold_proposal_addon margin_all">70 Lacs</p>
                                            </div>



                                        </div>
                                    </div> <!-- /.post-data -->

                                </div>
                                <!------single post----------->
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="modal-footer">

                        <button class="btn btn-primary">Done</button>
                    </div>
                </a>
            </div><!-- /.modal-content -->
        </div>
    </div>

    <!-- .modal -- allocate -->
    <div id="a_p" class="modal fade model" data-backdrop="true">
        <div class="modal-dialog modal-lg animate" id="animate">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Allocate Percentage
                    </h6>
                    <button type="button" class="btn btn-white" data-dismiss="modal" style="border-radius: 50px;"><i class="fa fa-close"></i></button>
                </div>
                <div class="modal-body text-center p-lg">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="single-blog-post blog-text-style post_data_addon">
                                    <div class="post-data post_data_addon_pad box_shadow_proposal">
                                        <div class="row">
                                            <div class="col-lg-2 col-4 mb_addon_mo">
                                                <a class="nav-link all_percentage" data-toggle="tab" href="#service">Wife</a>
                                            </div>
                                            <div class="col-lg-5 col-4 text-center  pad_0 mar_top_mo mb_addon_mo">
                                                <img src="images/pro_01.png" class="pro_all_img">

                                                <form action="#" class="checkout-form">


                                                    <div class="row">
                                                        <div class="col-lg-12"><input type="text" placeholder="Percentage*" class="single-input-wrapper"></div>

                                                    </div> <!-- /.row -->

                                                </form> <!-- /.checkout-form -->
                                            </div>
                                            <div class="col-lg-5 col-4 text-center  mar_top_mo pad_0 mb_addon_mo">
                                                <img src="images/pro_02.png" class="pro_all_img">
                                                <p class="font_bold_proposal_addon margin_all">70 Lacs</p>
                                            </div>



                                        </div>
                                    </div> <!-- /.post-data -->

                                </div>
                                <!------single post----------->
                            </div>
                            <div class="col-md-6">
                                <div class="single-blog-post blog-text-style post_data_addon">
                                    <div class="post-data post_data_addon_pad box_shadow_proposal">
                                        <div class="row">
                                            <div class="col-lg-2 col-4 mb_addon_mo">
                                                <a class="nav-link all_percentage" data-toggle="tab" href="#service">Mother</a>
                                                <!-- <a href="#" class="contact-us white-shdw-button-login">Mother</a> -->
                                            </div>
                                            <div class="col-lg-5 col-4 text-center  pad_0 mar_top_mo mb_addon_mo">
                                                <img src="images/pro_01.png" class="pro_all_img">

                                                <form action="#" class="checkout-form">

                                                    <div class="user-profile-data">
                                                        <div class="row">
                                                            <div class="col-lg-12"><input type="text" placeholder="Percentage*" class="single-input-wrapper"></div>

                                                        </div> <!-- /.row -->
                                                    </div> <!-- /.user-profile-data -->
                                                </form> <!-- /.checkout-form -->
                                            </div>
                                            <div class="col-lg-5 col-4 text-center  mar_top_mo pad_0 mb_addon_mo">
                                                <img src="images/pro_02.png" class="pro_all_img">
                                                <p class="font_bold_proposal_addon margin_all">70 Lacs</p>
                                            </div>



                                        </div>
                                    </div> <!-- /.post-data -->

                                </div>
                                <!------single post----------->
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="modal-footer">

                        <button class="btn btn-primary">Done</button>
                    </div>
                </a>
            </div><!-- /.modal-content -->
        </div>
    </div>
    <!-- / .modal -->


    <!-- / .modal -->


    <!-- Scroll Top Button -->
    <button class="scroll-top tran3s">
        <i class="fa fa-angle-up" aria-hidden="true"></i>
    </button>




    <script>
       
        
        var steps = '<?php if(!empty($dropout_page) && ($dropout_page>0 && $dropout_page<4 )){ echo $dropout_page;}?>';

        if(steps){

            $("fieldset").hide();
            $("fieldset").eq(steps).show();
            $("#progressbar li").eq(steps).addClass("active")
        }
        var steps = $("fieldset").length;
        /*$('#pin_code').on('keyup', function() {
            if ($("#pin_code").val().length != 6) {
                return;
            }
            pincode_insert();
        });
*/
        $('.fullname').keypress(function(e) {
            var regex = new RegExp(/^[a-zA-Z\s]+$/);
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });

        $("body").on("keyup", ".mobile,#pin_code", function(e) {
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

        function pincode_insert() {
            var pin_code = $("#pin_code").val();
            data = {};
            data.pin_code = pin_code;

            $.ajax({

                url: '/customerportal/pincode_insert',
                method: 'POST',
                data: data,
                async: false,
                cache: false,
                dataType: "json",
                success: function(response) {

                    if (response.status != 'success') {
                        return false;
                        // alert("please enter correct pin code");

                    } else {
                        $('input:radio[name=pop_city]').val([response.CITY]);
                        $("#pin_code_continue").attr("disabled", false);
                        return true;
                    }
                }
            });
        }

        $('input[type=radio][name=pop_city]').change(function() {
            insert_pop_city();
            $('span.error').css('display', 'none');
        });

        function insert_pop_city() {
            var city_name = $("input[name=pop_city]:checked").val().trim();

            var pin_code = $("#pin_code").val();
            data = {};
            data.city_name = city_name;

            $.ajax({
                url: '/customerportal/insert_pop_city',
                method: 'POST',
                data: data,
                async: false,
                cache: false,
                dataType: "json",
                success: function(response) {

                    if (response.status != 'success') {
                        // alert("please enter correct pin code");

                    } else {
                        $("#pin_code").val(response.PINCODE);
                        // pincode_insert();
                    }
                }
            });
        }
        /*$('#deductible_submit').on('click', function() {
            var deductible = $('#deductible').val()
            if(deductible<=0){
                swal("Alert", "Please select deductible amount", "warning")
                event.stopImmediatePropagation();
                return;

            }else{
                data = {};
                data.deductible = deductible;



                $.ajax({

                    url: '/customerportal/SubmitDeductible',
                    method: 'POST',
                    data: data,
                    async: false,
                    cache: false,
                    success: function(response) {

                        //  debugger;
                        var res = JSON.parse(response);
                        
                        window.location.href = '/quotes?lead_id='+res.lead_id;
                        
                        

                        // $(".disease").attr("style", 'display:block');


                    }
                });
            }
        });*/

        /* $('.sum_insured_type_show').on('click', function() {

             debugger;

                            /// $('.row ul > li:nth-child(3)').removeClass('active');
                             $('.row ul > li:nth-child(4)').addClass('active');*/
        $.ajax({
            url: '/customerportal/getSuminsuredType',
            method: 'POST',
            data: '',
            async: false,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);
                var i;
                var result = '';
                var disabled = '';
                if (res) {
                    $('.sum_insured_type').html('');
                    for (i = 0; i < res.data.length; i++) {
                        var data;
                        // if(res.existing_members_count <= 1)
                        // {

                        var edit_customer = $('#edit_customer').val();

                        if ((res.data[i].suminsured_type_id == 1 && edit_customer!='') || (res.si_type_id ==1 && edit_customer=='') ) {
                            result = 'checked';

                            data = '<input type="radio" '+ disabled +'name="SumInsuredtype" ' + result + ' value ="' + res.data[i].suminsured_type_id + '" id="tab' + [i] + '" class="tab-content-first"><label for="tab' + [i] + '"> ' + res.data[i].suminsured_type + '</label>';
                            $('.sum_insured_type').append(data);
                        }
                        else{
                            data = '';
                        }
                        // } else {
                        if ((res.data[i].suminsured_type_id != 1 && edit_customer!='') || (res.si_type_id !=1 && edit_customer=='') ) {
                            result = 'checked';

                            data = '<input type="radio" '+ disabled +'name="SumInsuredtype" ' + result + ' value ="' + res.data[i].suminsured_type_id + '" id="tab' + [i] + '" class="tab-content-first"><label for="tab' + [i] + '"> ' + res.data[i].suminsured_type + '</label>';
                            $('.sum_insured_type').append(data);
                        }

                        // }


                    }

                }
            }
        });
        /* });*/

        $('.SubmitInsuredtype').on('click', function() {
            var i;
            var val = [];
            var SumInsuredtype = $("input[name='SumInsuredtype']:checked").val();
            
            var deductable_modal = '<?php echo $deductable_modal?>';
            var submit=true;
            data = {};
            data.SumInsuredtype = SumInsuredtype;
            if(deductable_modal=='true'){
                submit=false;
                var deductible = $("#deductible").val();       
                if(deductible<=0){
                    swal("Alert", "Please select deductible amount", "warning")
                    event.stopImmediatePropagation();
                    return;

                }else{
                    submit=true;
                    data.deductible = deductible;
                }
            }
            if(submit==true){
                $.ajax({

                    url: '/customerportal/SubmitInsuredtype',
                    method: 'POST',
                    data: data,
                    async: false,
                    cache: false,
                    success: function(response) {

                        //  debugger;
                        var res = JSON.parse(response);
                        
                        window.location.href = '/quotes?lead_id='+res.lead_id;
                        
                        

                        // $(".disease").attr("style", 'display:block');


                    }
                });

            }

            



        });

        function getDisease() {
            $.ajax({

                url: '/customerportal/getMasterDisease',
                method: 'POST',
                data: '',
                async: false,
                cache: false,
                success: function(response) {

                    var res = JSON.parse(response);
                    var i;
                    if (res) {
                        $('.disease_data').html('');
                        for (i = 0; i < res.data.length; i++) {
                            var data;
                            data = '<div class="col-lg-3 col-md-6"><input class="inp-cbx" id="' + res.data[i].cd_id + '" name = "disease_type[]" type="checkbox" value = "' + res.data[i].cd_id + '"><label class="cbx" for="' + res.data[i].cd_id + '"><span class="mt_10_c_c"><svg width="12px" height="10px"><use xlink:href="#check"></use></svg></span><span class="span_custom_d">' + res.data[i].disease_name + '</span></label><svg class="inline-svg"><symbol id="check" viewBox="0 0 12 10"><polyline points="1.5 6 4.5 9 10.5 1"></polyline></symbol></svg></div>';
                            $('.disease_data').append(data);
                        }
                    }
                }
            });
        }
        

        $('#tab3').on('click', function() {
            getDisease();
        });
        $('.submitDisease').on('click', function() {

            // $(".disease").attr("style",'display:block;opacity:1');

            var i;
            var val = [];
            $("input[name='disease_type[]']:checked").each(function(i) {

                val[i] = $(this).val();
                console.log(val[i]);
            });
            console.log(val);

            var disease_type = val;

            var disease_check = $("input[name='disease_check']:checked").val();

            var lead_id = $.trim($('#hidden_lead_id').val());
            var customer_id = $.trim($('#hidden_lead_id').val());
            data = {};
            data.disease_type = disease_type;
            data.customer_id = customer_id;
            data.lead_id = lead_id,
                data.disease_check = disease_check;

            $('.details').closest('div').find('span.error').remove();
            hasError = false;

            if ($('input[name="disease_check"]:checked').length == 0) {
                hasError = true;

                $('.disease_check').closest('div').append('<span class="error">Name is required</span>');


            }
            if (hasError) {
                event.stopImmediatePropagation();
            }
            //    $(".disease").attr("style",'display:block;opacity:1');


            if (!hasError) {
                $.ajax({

                    url: '/customerportal/submitDisease',
                    method: 'POST',
                    data: data,
                    async: false,
                    cache: false,
                    success: function(response) {

                        response = JSON.parse(response);
                        if (response.status_code == 200) {

                            window.location.href = '/quotes';
                        }
                    }

                });

            }
            // $(".disease").attr("style",'display:block;opacity:1');


        });
        $(document).ready(function() {
            $.validator.addMethod(
                "validateEmail",
                function(value, element, param) {
                    if (value.length == 0) {
                        return true;
                    }
                    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                    return reg.test(value); // Compare with regular expression
                },
                "Please enter a valid Email ID. Correct Customer Email ID is mandatory to process further."
            );

            $.validator.addMethod('validMobile', function(value, element, param) {
                var mobileInput = value;
                var validMobRe = new RegExp('^[6-9][0-9]{9}$');
                return this.optional(element) || validMobRe.test(mobileInput) && mobileInput.length > 0;
            }, 'Enter valid mobile number');

            $.validator.addMethod("validate_pincode", function(value, element, param) {
                var regs = /^[1-9][0-9]{5}$/g;
                return this.optional(element) || regs.test(value);
            }, "Enter a valid Pin Code");


            let leadRules= {
                name: {
                    required: true
                },
                email: {
                    required: true,
                    validateEmail: true,
                },
                mobile: {
                    required: true,
                    validMobile: true
                },
                gender: {
                    required: true,
                    //valid_address : true
                },

                pin_code:{
                    required: true,
                    validate_pincode: true,
                },
            };
            var leadMessages = {
                name: {
                    required: "Name is required."
                },
                email: {
                    required: "Email is required",
                    validateEmail: "Enter valid email address",
                },
                mobile: {
                    required: "Mobile no. is required",
                    validMobile: "Enter valid 10 digit no. starting from 6 to 9"
                },
                gender: {
                    required: "Gender is required.",
                    //valid_address : true
                },
                pin_code:{
                    required: "Pincode is required.",
                    validate_pincode: "Enter valid pincode",
                },
            };
            var product_gender = '<?php echo $product_gender; ?>';
            function relationDivProductGender(gender){
                $('#Self_div').show();
                $('#Spouse_div').show();
                if((gender == 'male' && product_gender=='F') || (gender == 'female' && product_gender=='M')){
                    $('#Self_div').hide();
                }if(gender == 'female' && product_gender=='M'){
                    $('#Self_div').hide();
                }
                if(gender == 'male' && product_gender=='M'){
                    $('#Spouse_div').hide();
                }if(gender == 'female' && product_gender=='F'){
                    $('#Spouse_div').hide();
                }
            }
            relationDivProductGender($.trim($('input[name="gender"]:checked').val()));

            $('.submitLead').on('click', function(event) {

                var form  = $("#form-plan");
                form.validate({
                    errorElement: 'span',
                    rules: leadRules,
                    messages: leadMessages
                });
                if (form.valid() === true){



                    var name = $.trim($('.fullname').val());
                    var mobile = $.trim($('.mobile').val());
                    var email = $.trim($('.email').val());
                    var edit = $.trim($('#edit_customer').val());
                    var gender = $.trim($('input[name="gender"]:checked').val());
                    data = {};
                    data.fullname = name;
                    data.email = email,
                        data.mobile = mobile;
                    data.gender = gender;
                    data.edit = edit;

                    $.ajax({

                        url: '/customerportal/submitLead',
                        method: 'POST',
                        data: data,
                        async: false,
                        cache: false,
                        success: function(response) {
                            var res = JSON.parse(response);

                            $('#hidden_lead_id').val(res.data.lead_id);
                            $('#hidden_cust_id').val(res.data.customer_id);
                            var url = window.location.href;

                            if (url.indexOf("lead_id") < 0){
                                if (url.indexOf("?") < 0)
                                    url += "?lead_id=" + res.data.lead_id;
                                else
                                    url += "&lead_id=" + res.data.lead_id;


                                var url = window.history.pushState( {} , '', url );
                            }


                            relationDivProductGender(gender);


                        }
                    });
                }else{
                    event.stopImmediatePropagation();
                }


            });

            $('.submitpincode').on('click', function(event) {



                var form  = $("#form-plan");
                form.validate({
                    errorElement: 'span',
                    rules: leadRules,
                    messages: leadMessages
                });
                if (form.valid() !== true){
                    event.stopImmediatePropagation();
                }else{
                    var pin_code = $("#pin_code").val();
                    data = {};
                    data.pin_code = pin_code;

                    $.ajax({

                        url: '/customerportal/pincode_insert',
                        method: 'POST',
                        data: data,
                        async: false,
                        cache: false,
                        dataType: "json",
                        success: function(response) {

                            if (response.status != 'success') {
                                swal("Alert", "Please enter valid pincode", "warning")

                                event.stopImmediatePropagation();

                            } else {
                                $('input:radio[name=pop_city]').val([response.CITY]);
                                $("#pin_code_continue").attr("disabled", false);
                                $.ajax({

                                    url: '/customerportal/updateLeadLastVisited',
                                    method: 'POST',
                                    async: false,
                                    cache: false,
                                    dataType: "json",
                                    success: function(response) {

                                    }
                                });
                                return true;
                            }
                        }
                    });




                }
                return false;

            });



            $('[data-toggle="tooltip"]').tooltip();

            $('.insured_members_checkbox').click(function() {

                let html = "";

                $('.insured_members_checkbox').each(function() {
                    if ($(this).is(':checked')) {
                        let label = $(this).data('member');
                        html += "<div>" + label + "</div>";
                    }
                });

                if (html) {
                    $("#members_selected_boxes").html(html);
                    $("#insured_members_select").hide();
                } else {
                    $("#members_selected_boxes").html("");
                    $("#insured_members_select").show();
                }
            });
        });
    </script>
    <script>
         
        function validateSelectedMembers(event){

            let member_details = [];
            let validCheckBoxSelection = false;
            let validAgeSelection = false;
            var rel_count = 0;
            var child_count = 0;
            var allowed_child_count = '<?php echo $child_count;?>';
            var age_count = 0;
            $('#proposal_members .inp-cbx').each(function (index, element) {
                
                if ($(element).is(':checked') && $(element).is(":visible")) {
                    if($(element).hasClass("child_chk")){
                       child_count++; 
                    }
                    var rel_no = $(element).data('rel_no');
                    validCheckBoxSelection = true;
                    member_details.push({
                        member_type: $(element).attr('value')
                    });
                    rel_count++;

                    if( $('select.theme-select-menu[data-age_no="'+rel_no+'"]').val()!=0 ){

                    }else{
                        swal("Alert", "Please select Age", "warning")
                        event.stopImmediatePropagation();
                        return;

                    }


                }
            });
            $('#proposal_members select.theme-select-menu').each(function (index, element) {
                var age_no = $(element).data('age_no');
                if($('.inp-cbx[data-rel_no="'+age_no+'"]').is(':visible')){

                    if (element.value != '0') {
                        var age_no = $(element).data('age_no');
                        validAgeSelection = true;
                        member_details.push({
                            age: element.value
                        });
                        age_count++;

                        if (!$('.inp-cbx[data-rel_no="'+age_no+'"]').is(':checked')) {
                            swal("Alert", "Please select relation", "warning")
                            event.stopImmediatePropagation();
                            return;

                        }


                    }
                }
            });


            var max_insured_count =  '<?php echo $max_insured_count; ?>';
            if(max_insured_count>0 && max_insured_count<rel_count ){
                swal("Alert", "Please select only "+max_insured_count+" insured member at a time." , "warning");
                event.stopImmediatePropagation();
                return;
            }
            if(parseInt(allowed_child_count)>0 && child_count > parseInt(allowed_child_count)){
                swal("Alert", "Please select only "+parseInt(allowed_child_count)+" child member at a time.", "warning")
                event.stopImmediatePropagation();
                return;
            }
            if(rel_count == 0  && age_count == 0){
                //theme-select-menu
                swal("Alert", "Please select Age and relation", "warning")
                event.stopImmediatePropagation();
                return;
            }
            if(rel_count < age_count){
                swal("Alert", "Please select Relation", "warning")
                event.stopImmediatePropagation();
                return;
            }
            if(rel_count > age_count){
                swal("Alert", "Please select Age", "warning")
                event.stopImmediatePropagation();
                return;
            }
            if (rel_count == age_count){
                if (validCheckBoxSelection && validAgeSelection) {
                    return member_details;
                }
            }else {
                swal("Alert", "Please select Age and relation", "warning")
                event.stopImmediatePropagation();
                return;
            }
            $('#proposal_members .selectize-input').each(function(index, element) {
                $(element).css('border', '1px solid red');
            });
            event.stopImmediatePropagation();

        }
        function validateMembers(event) {
            
            var proceed_members = validateSelectedMembers(event);
            
            if (proceed_members!=false && proceed_members!='' && proceed_members != undefined){

                data = {};
                data.data = JSON.stringify(proceed_members);
                var deductable_modal = '<?php echo $deductable_modal;?>';
                data.dropout_page=3; 
                if(deductable_modal=='true'){
                   data.dropout_page=2; 
                   data.deductable='<?php echo $deductable_amount; ?>'; 
                }

                $.ajax({
                    url: '/customerportal/submitMembers',
                    data: data,
                    cache: false,
                    async: false,
                    type: 'POST',
                    success: function (response) {
                        // event.stopImmediatePropagation();
                        var res = JSON.parse(response);
                        if (res.status == 201 || res.status == 200) {
                            $("#adult-mandatory").html(res.msg);
                            $('#adult-mandatory').show();
                            if(deductable_modal=='true'){
                                $('#deductable_modal').modal('show');
                                event.stopImmediatePropagation();
                                return;

                            }
                            var p_id = '<?php echo $_SESSION['product_id_session'];?>';
                            if(p_id<=0){
                                //event.stopImmediatePropagation();
                                return;
                            }
                        }
                        return;
                        console.log(res.plan);
                        if (res.plan == 1) {

                            // $("#showtab").attr("style").show();
                        } else {


                        }
                    }
                });
                // event.stopImmediatePropagation();
                return;

            }

        }
    </script>
    <script>
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.dropdown-select').length) {
                $('.option-list, .search-box').hide();
            }
        });
        $('.select').click(function(event) {
            //$('.option-list, .search-box').hide();
            $(this).closest('.dropdown-select').find('.option-list, .search-box').toggle();
            $('.option-list a').click(function() {
                var select = $(this).text();
                $(this).closest('.dropdown-select').children('.select').text(select);
                $('.option-list, .search-box').hide();
            });
        });
        //Search
        $('.seach-control').keyup(function() {
            var val = $(this).val().toLowerCase();
            var list = $(this).closest('.dropdown-select').find('li')
            list.each(function() {
                var text = $(this).text().toLowerCase();
                if (text.indexOf(val) == -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }

            })
        });

        function displayInsuredMemberList() {
            document.getElementById("insured_member_list").style.display = 'block';
        }
        let son_counter = 1;

        let daugher_counter = 1;

        let children_total_count = 2;

        function addSon() {
            son_counter++;
            children_total_count++;

            if (children_total_count > 6) {
                return;
            }

            $("#son" + son_counter + "_section").show();
        }

        function addDaughter() {
            daugher_counter++;
            children_total_count++;

            if (children_total_count > 6) {
                return;
            }

            $("#daughter" + daugher_counter + "_section").show();
        }

        function showOtherMembers(event) {
            $(".other-members-section").toggle();
        }
    </script>


</div> <!-- /.main-page-wrapper -->
</body>