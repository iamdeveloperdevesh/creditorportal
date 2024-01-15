<?php //print_r($post_data);die; ?>
<style>
    .ui-datepicker{
        position: absolute;
        left: 420.267px;
        z-index: 9;
        display: block;
        top: 537.883px;
    }
    #ui-datepicker-div{
        display:none;
    }
    .color_text {
        color: #000;
    }
    
    .nominee_rel{
        color:#8d90a5;
    }    
    .more-less {
        float: right;
        color: #fff;
        left: -54px;
        position: relative;
        margin-top: 5px;
    }
    .swal-button{
        background-color: #F2581B !important;

    }
    .error {
        color: red!important;
        font-weight: 500;
        font-size: 15px;
        margin-top: -1%;
    }
    .plan_right_member_e_c_proposal_form.sideWindowForm {
        display: flex;
        align-items: center;
    }
    @media only screen and (max-width: 576px) {
        .mobile_continerFluid{
            padding-bottom: 10px;
        }
        .theme-tab.tab-dark .z-content-inner p.dis-web.cl-web.proposal_title{
            color: #107591 !important;
            font-size: 19px !important;
        }
        .z-content-inner .signUp-page form .input-group label, .over_border_txt_proposal_add {
            font-size: 15px !important;;
        }
        .z-content-inner .signUp-page form .input-group input {
            font-size: 18px !important;;
        }
        .proposal_continue_back_margin .btn.btn_start_proposal_back {
            font-size: 18px !important;;
        }
        #proposerDetails .form-control-age {
            font-size: 18px !important;
        }

    }

    @media only screen and (max-device-width: 480px) {
        .mb-form {
            box-shadow: none !important;
            border: none !important;
            background: none !important;
            margin-left: -4% !important;
        }

        .cl-web {
            margin-bottom: 32px;
            font-size: 22px !important;
            margin-top: -7% !important;
            font-weight: 600 !important;
            color: #107591 !important;
            display: block !important;
        }

        .margin_top_40_proposal_insured {
            margin-right: 17px;
        }

        .signUp-page {
            margin-top: 11% !important;
            padding-bottom: 0px;
        }

        .box-shadow_plan_box_p_s_s_proposal_form {
            display: none;
        }

        .form-control-age_nominee {
            font-size: 20px;
        }

        .faq-tab-wrapper .faq-panel .panel .panel-heading.active-panel .panel-title a:before {
            padding: 2px 4px !important;
        }

        .faq-tab-wrapper .faq-panel .panel .panel-heading .panel-title a {
            font-size: 20px;
        }

        .quotes_compare_container {
            display: none !important;
        }

        .margin_top_proposal_details_proposer {
            margin-right: 14px !important;
            margin-top: 2%;
        }

        .form-group {
            margin-bottom: 2.3rem !important;
        }

        .signUp-page form .input-group input {
            font-size: 20px;
        }

        .signUp-page form .input-group {
            margin-bottom: 34px !important;
        }

        .element-section .btn {
            font-size: 23px;
        }

        .signUp-page form .input-group label,
        .over_border_txt_proposal_add {
            font-size: 20px;
        }

        .form-control-age {
            font-size: 22px;
            line-height: 1.5;
        }

        .form-control-age:hover {
            font-size: 21px;
            line-height: 2.5;
        }

        .go_back_prposal_p {
            position: relative;
            left: 22%;
            font-family: 'PFEncoreSansProblck';
            font-size: 21px;
        }

        .element-tile-two {
            margin-top: 0px;
            font-size: 24px;
            line-height: 20px;
        }
        .element-tile-two.mobile_title {
            font-size: 19px !important;
            text-align: left;
            line-height: 25px;
        }

        .ds-bk {
            display: block !important;
        }
    }

    .z-tabs>.z-container>.z-content>.z-content-inner {
        height: auto;
    }

    .theme-tab-basic.theme-tab .tabs-menu li.z-active a:before {
        background: none;
    }
    .tab_modal_product_d {
        padding: 11px 30px 8px 30px !important
    }

    .z-tabs.horizontal>ul>li:first-child {
        width: <?php if ($plan_data["nominee_mandatory"]==1){ echo '33.3333%';}else{echo '60%';}?> !important;
    }

    .z-tabs.horizontal>ul>li {
        width: <?php if ($plan_data["nominee_mandatory"]==1){ echo '33.3333%';}else{echo '50%';}?> !important;
    }

    .z-tabs.horizontal>ul>li:last-child {
        width: <?php if ($plan_data["nominee_mandatory"]==1){ echo '33.3333%';}else{echo '40%';}?> !important;
    }

    .z-content-inner {
        overflow: hidden !important;
    }

    .error {
        position: relative !important;
        top: 112% !important;
    }

    .edit_btn_proposal_form {
        right: 10%;
        border-radius: 50px;
        background: transparent;
    }

    .faq-tab-wrapper .faq-panel .panel .panel-heading .panel-title a::before {
        content: '';
        padding: 5px 7px;
        font-size: 27px;
        right: 42px;
        color: #fff;
        position: absolute;
        background: #107591;
        top: 53%;
        border-radius: 81px;
    }

    .faq-tab-wrapper .faq-panel .panel .panel-heading .panel-title a {
        width: 98%;
    }
    .proposal_continue_back_margin {
        text-align: center;
        margin: 0 !important;
    }
    .signUp-page form .input-group input.error, .signUp-page form .input-group select.error {
        color: #939393;
    }
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active{
        -webkit-box-shadow: 0 0 0 40px white inset !important;
    }

     .btn_start_proposal_back {
        color: #fff;
        background: #107591 !important;
        width: 135px;
        height: 51px;
        padding: 12px 0.75rem;
    }
</style>

<body>
<div class="main-page-wrapper">

    <?php
    $iback =true;
    $nback = true;
    $pterms = false;
    $iterms= false;
    if(!empty($plan_data["payment_page"])) {

        if($plan_data["payment_first"] == 1)
        {
            $iback=false;
        }
        if($plan_data["payment_page"]==2 ){
            $iback=false;
            $pterms = true;
        }
        if($plan_data["payment_page"]==3 ){
            $nback=false;
            $iterms = true;
        }

    }
    ?>
    <!-- ===================================================
            Loading Transition
        ==================================================== -->
    <!-- Preloader -->
    <section>
        <div id="preloader">
            <div id="ctn-preloader" class="ctn-preloader">
                <div class="animation-preloader">
                    <div class="spinner"></div>
                    <div class="txt-loading">

                        <?php
                        if(isset($_SESSION['linkUI_configuaration'])){
                            $Loader_text= $this->session->userdata('linkUI_configuaration')[0]['Loader_text'];
                            $result = str_split($Loader_text);
                            foreach ($result as $letter){
                                ?>
                                <span data-text-preloader="<?php echo $letter; ?>" class="letters-loading">
                                <?php echo $letter; ?>
                            </span>

                                <?php
                            }

                        }else{ ?>
                            <span data-text-preloader="F" class="letters-loading">
                                E
                            </span>
                            <span data-text-preloader="Y" class="letters-loading">
                                L
                            </span>
                            <span data-text-preloader="N" class="letters-loading">
                                E
                            </span>
                            <span data-text-preloader="T" class="letters-loading">
                                P
                            </span>
                            <span data-text-preloader="U" class="letters-loading">
                                H
                            </span>
                            <span data-text-preloader="N" class="letters-loading">
                                A
                            </span>
                            <span data-text-preloader="E" class="letters-loading">
                                N
                            </span>
                            <span data-text-preloader="E" class="letters-loading">
                                T
                            </span>
                        <?php  }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--
        =============================================
            Theme Main Menu
        ==============================================
        -->
    <div class="shrt-menu shrt-menu-one light-bg text-dark">






        <!--
        =============================================
            Theme Solid Inner Banner
        ==============================================
        -->
        <div class="quotes_compare_container hidden-lg" style="z-index: 111;">
            <div class="col-xs-12 text-center">
                <p><i class="fa fa-angle-up margin_angle_up_i"></i></p>
            </div>
            <div class="quotes_compare_container_wrapper proposal_form_p_f" style="max-width: 90%;">

                <div>
                    <p class="total_premium_p_s_form">Total Premium
                        <input type="hidden" name="policy_id" id="hiddenpolicyid" value='<?php echo $post_data['policy_id']; ?>'>
                        <input type="hidden" name="premium_data" id="hiddenpremium" value='<?php echo $post_data['premium']; ?>'>

                        <span class="total_p_red_rs_p_s"><i class="fa fa-inr"></i> <?php echo $post_data['premium']; ?></span>
                    </p>
                </div>
                <div class="quotes_compare_buttons_div" style="margin-left: 19%;">
                    <a href="proposal_summary.html">
                        <div class="row btn_p_form_p_pay_now">
                            <div class="col-md-6">
                                <button class="btn btn_p_s_pay_now">Pay Now <i class="flaticon-next" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid mobile_continerFluid mt-20 pb-10">
            <!-- /.theme-sidebar-widget -->

            <div class="element-section mb-10">
         <!--       <a href="/quotes/generate_quote_abc?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>">
                    <p class="go_back_prposal_p"><i class="fa fa-long-arrow-left" style="width: 27px;"></i> Go Back</p>
                </a>-->
                <?php
                if(isset($_SESSION['linkUI_configuaration'])){
                    $image_proposal= $this->session->userdata('linkUI_configuaration')[0]['proposal_header_text'];
                    ?>
                    <div class="element-tile-two mobile_title"><?php echo $image_proposal;?></div>

                    <?php
                }else{
                    ?>

                    <div class="element-tile-two mobile_title">You are Just 5 minutes away from investing for your future</div>
                <?php }?>
                <div class="row margin_top_tab_proposal">
                    <div class="col-lg-9 col-md-12 aos-init" data-aos="fade-right">
                        <!-- The value of data-role should be z-tabs, data-options is optional to set options -->
                        <!-- The value of data-role should be z-tabs, data-options is optional to set options -->
                        <div id="theme-tab-twlv" class="theme-tab-basic tab-dark theme-tab hover contained medium z-icons-dark z-shadows z-bordered z-tabs horizontal top top-left silver" data-role="z-tabs" data-options="{&quot;theme&quot;: &quot;silver&quot;, &quot;orientation&quot;: &quot;horizontal&quot;, &quot;animation&quot;: {&quot;duration&quot;: 400, &quot;effects&quot;: &quot;slideH&quot;}}">
                            <ul class="z-tabs-nav z-tabs-mobile z-state-closed" style="display: none;">
                                <li><a class="z-link" style="text-align: left;"><span class="z-title">Graphics</span><span class="flaticon-setup drp-icon"></span></a></li>
                            </ul><i class="z-dropdown-arrow"></i>
                            <ul class="tab_modal_product_d tabs-menu clearfix z-tabs-nav z-tabs-desktop z-hide-menu mb-form">
                                <li class="z-tab z-first z-active pdetails" data-link="tab1" style="pointer-events: none;"><a class="z-link">
                                        <?php
                                        if(isset($_SESSION['linkUI_configuaration'])){
                                            $image= $this->session->userdata('linkUI_configuaration')[0]['proposer_details_image'];
                                            ?>
                                            <img src="<?php echo $image; ?>" class="ds-bk">
                                            <?php
                                        }else{ ?>
                                            <img src="/assets/images/proposal_details.png" class="ds-bk">
                                        <?php  }
                                        ?>

                                        <span class="dis-mb"> Proposer Details</span></a></li>
                                <li class="z-tab idetails " data-link="tab2" style="pointer-events: none;"><a class="z-link insured_det">
                                        <?php
                                        if(isset($_SESSION['linkUI_configuaration'])){
                                            $image2= $this->session->userdata('linkUI_configuaration')[0]['insured_detail_image'];
                                            ?>
                                            <img src="<?php echo $image2; ?>" class="ds-bk">
                                            <?php
                                        }else{ ?>
                                            <img src="/assets/images/Insured_details.png" class="ds-bk">
                                        <?php  }
                                        ?>

                                        <span class="dis-mb"> Insured Details</span></a></li>
                                <!-- <li class=" z-tab" data-link="tab3"><a class="z-link"><img src="images/product/medical.png" /> Medical Details </a></li> -->
                                <?php if ($plan_data["nominee_mandatory"]==1){ ?>
                                <li class="z-tab z-last ndetails" data-link="tab4" style="pointer-events: none;"><a class="z-link nomineee_det">
                                        <?php
                                        if(isset($_SESSION['linkUI_configuaration'])){
                                            $image3= $this->session->userdata('linkUI_configuaration')[0]['nominee_detail_image'];
                                            ?>
                                            <img src="<?php echo $image3; ?>" class="ds-bk">
                                            <?php
                                        }else{ ?>
                                            <img src="/assets/images/Nominee_details.png" class="ds-bk">
                                        <?php  }
                                        ?>

                                        <span class="dis-mb">Nominee Details</span> </a></li>
                                         <?php } ?>
                            </ul>
                            <!-- Tab Navigation Menu -->

                            <!-- Content container -->
                            <!-- /.tab-container -->
                         <?php
                           $member_type_array = array_column($customer_details['members'], 'member_type');

                          // echo '<pre>';print_r($customer_details['members']);exit;
                           $self=false;
                           $single=true;
                           if(in_array('Self', $member_type_array) || in_array(1, $member_type_array)){
                                $self=true;
                            }
                            if(in_array('Spouse', $member_type_array) || in_array(2, $member_type_array)){
                                $single=false;
                            }
                            $validage = '';    
                            $maxage = '';    
                            if($self==true && ($plan_data['payment_first']==1)){
                                 $age = getSelfAge();
                                // print_r($age);die;
                                 if(!empty($age)){
                                    $maxage = 'maxage='.$age->member_age.' year_month=year';
                                    $validage = 'validage';
                                    $year = date('Y')-23;
                                    $month = date('m');
                                    $d = date('d');

                                    $get_yr = $year."-".$month."-".$d;

                                 }
                            }
                             $salutation_array = ['Mr'=>'Mr','Ms'=>'Ms','Mrs'=>'Mrs']; 
                             if(!empty($plan_data['gender'])){
                                if($plan_data['gender'] == 'F'){
                                    if($single != true){
                                        $salutation_array = ['Mr'=>'Mr']; 

                                    }else if($self ==true){
                                        $salutation_array = ['Ms'=>'Ms','Mrs'=>'Mrs'];
                                    }
                                }else if($plan_data['gender']=='M'){
                                    if($single != true ){
                                        $salutation_array = ['Mrs'=>'Mrs']; 
                                    }else if($self ==true){
                                        $salutation_array = ['Mr'=>'Mr'];
                                    }

                                }



                            }

                        ?>
                            <div class=" tab-container" style="min-height: 330px;">
                                <!-- Graphics -->
                                <div class="z-content z-active" style="display: block; position: relative; left: 0px; top: 0px;">
                                    <div class="z-content-inner">
                                        <div class="signUp-page signUp-minimal pb-10">

                                            <div class="signin-form-wrapper pad_proposal_s">
                                                <p class="dis-web cl-web proposal_title" style="color: #107591 !important; "> Proposer Details</p>
                                                <form id="proposerDetails">
                                                    <div class="row margin_top_proposal_details_proposer">
                                                        <div class="col-md-12">
                                                            <!-- <p class="intr_tiltle_medical">Proposer Basic Details</p> -->
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-12">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <select name="salutation" class="form-control-age" id="salutation">
                                                                    <option value="">- Select Salutation-</option>
                                                                    <?php foreach ($salutation_array as $key => $value) {
                                                                        echo '<option value="'.$value.'"';
                                                                        if($customer_details['customer_details']['salutation'] == $value){
                                                                            echo ' selected';
                                                                        }

                                                                         echo '>'.$value.'</option>';
                                                                    }
                                                                    ?>

                                                                </select>
                                                                <label class="over_border_txt_proposal_add">Salutation <span style="color:#FF0000">*</span></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">

                                                                <input type="text" class="form-control-age alphabates_only"  id="fname"  name="fname" placeholder="Enter First Name" value="<?php echo $customer_details['customer_details']['first_name']; ?>" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode === 32 && !/\s$/.test(this.value))">

                                                                <label>First Name <span style="color:#FF0000">*</span></label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">

                                                                <input type="text" class="form-control-age alphabates_only"  id="lname"  name="lname" placeholder="Enter Last Name" value="<?php echo $customer_details['customer_details']['last_name']; ?>" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode === 32 && !/\s$/.test(this.value))">

                                                                <label>Last Name   <span style="color:#FF0000">*</span></label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-lg-4 col-md-6 col-12">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <select name="gender" class="form-control-age" id="gender" style="pointer-events: none;">
                                                                    <option value="">- Select Gender-</option>
                                                                    <option value="Male" <?php echo ($customer_details['customer_details']['gender'] == "Male") ? "selected" : ""; ?>>Male</option>
                                                                    <option value="Female" <?php echo ($customer_details['customer_details']['gender'] == "Female") ? "selected" : ""; ?>>Female</option>
                                                                </select>
                                                                <label>Gender <span style="color:#FF0000">*</span></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-lg-4 col-md-6 col-12">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <select class="form-control-age" name="status"  id="marital_status">
                                                                    <option value="">- Select Martial Status -</option>
                                                                    <?php if($single==true){ ?>
                                                                    <option value="Single" <?php echo ($customer_details['customer_details']['marital_status'] == "Single") ? "selected" : ""; ?>>Single</option>
                                                                     <?php }?>
                                                                    <option value="Married" <?php echo ($customer_details['customer_details']['marital_status'] == "Married") ? "selected" : ""; ?>>Married</option>
                                                                    <?php if($single==true){ ?>
                                                                    <option value="Divorced" <?php echo ($customer_details['customer_details']['marital_status'] == "Divorced") ? "selected" : ""; ?>>Divorced</option>
                                                                    <option value="Widowed" <?php echo ($customer_details['customer_details']['marital_status'] == "Widowed") ? "selected" : ""; ?>>Widowed</option>
                                                                    <?php }?>
                                                                </select>
                                                                <label>Martial Status <span style="color:#FF0000">*</span></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="text" inputmode="none" class="form-control-age  datepicker" onkeydown = "return false;" name="proposer_dob" id="proposer_dob" <?php echo $maxage;?> <?php if(!empty($customer_details['customer_details']['dob']) && $customer_details['customer_details']['dob']!='0000-00-00') { echo 'value="'.date('d-m-Y',strtotime($customer_details['customer_details']['dob'])).'"'; }?> placeholder="dd-mm-yyyy" autocomplete="off" <?php if($self==true){echo 'readonly';} ?>>

                                                                <label>Date of Birth <span style="color:#FF0000">*</span></label>

                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <!-- <div class="col-12 col-lg-4 col-md-6">
                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                <input type="text" placeholder="Height in CM" required>
                                                        <label>Height in CM</label>
                                            </div>
                                            </div> -->
                                                        <!-- /.col- -->
                                                        <!-- <div class="col-12 bor_spa">
                                            <p>&nbsp;</p>
                                            </div> -->
                                                        <!-- <div class="col-12 col-lg-4 col-md-6 mb_8">
                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                <input type="text" placeholder="Weight in KG" required>
                                                <label>Weight in KG</label>
                                            </div>
                                            </div> -->
                                                        <!-- /.col- -->
                                                       
                                                        <div class="col-lg-4 col-md-6 col-12">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                 <select class="form-control-age" name="is_proposer_insured" readonly id="is_proposer_insured" style="pointer-events:none;">
                                                                    <option value="">- Select -</option>

                                                                    
                                                                    
                                                                    <option value="Yes" <?php echo ($self==true) ? "selected" : ""; ?>>Yes</option>
                                                                    <option value="No" <?php echo ($self==false) ? "selected" : ""; ?>>No</option>

                                                                </select>
                                                                <label>Is Proposer Same as Insured <span style="color:#FF0000">*</span></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input  type="tel" maxlength="10"  name="mobile_no" id="mobile_no" placeholder="Enter Mobile Number" class="numeric_only" value="<?php echo $customer_details['customer_details']['mobile_no']; ?>">
                                                                <label>Mobile Number <span style="color:#FF0000">*</span></label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="text" name="email" value="<?php echo $customer_details['customer_details']['email_id']; ?>" placeholder="Enter Email Id">
                                                                <label>Email <span style="color:#FF0000">*</span></label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="text" id="proposer_pan" name="proposer_pan" value="<?php echo $customer_details['customer_details']['pan']; ?>" placeholder="Enter Pan Card Number" maxlength="10">
                                                                <label>Pan Card Number <?php if ($plan_data["pan_mandatory"]==1){ ?><span style="color:#FF0000">*</span> <?php }?></label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="text" name="gstin" id="gstin" value="<?php echo $customer_details['customer_details']['gstin']; ?>" placeholder="Enter GSTIN Number" maxlength="15">
                                                                <label>GSTIN</label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                               <input type="text" value="<?php echo $customer_details['customer_details']['address_line1']; ?>" name="proposer_address" placeholder="Enter Address 1" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode === 32 && !/\s$/.test(this.value))">
                                                                <label>Address 1 <span style="color:#FF0000">*</span></label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="text" value="<?php echo $customer_details['customer_details']['address_line2']; ?>" name="proposer_address2" placeholder="Enter Address 2" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode === 32 && !/\s$/.test(this.value))">
                                                                <label>Address 2</label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-lg-4 col-md-6 mb_8">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="text" name="proposer_pincode" id="proposer_pincode" placeholder="Enter Pincode" maxlength="6" value="<?php echo $customer_details['customer_details']['pincode']; ?>">
                                                                <label>Pincode <span style="color:#FF0000">*</span></label>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-lg-4 col-md-6 col-12">
                                                            <div class="input-group dropdown_product_m_t_b_p">
                                                                <input type="text" value="<?php echo $customer_details['customer_details']['state']; ?>" name="proposer_state" placeholder="State" id="state" readonly>
                                                                <label>State <span style="color:#FF0000">*</span></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-lg-4 col-md-6 col-12">
                                                            <div class="input-group dropdown_product_m_t_b_p">
                                                                <input type="text" name="proposer_city" placeholder="City" id="city" value="<?php echo $customer_details['customer_details']['city']; ?>" readonly>
                                                                <label>City <span style="color:#FF0000">*</span></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <!-- /.col- -->
                                                         <?php if($pterms == true) { ?>
                                                        <ul class="col-md-12" id="termscondition">
                                                            <li class=" hidden-md">
                                                                <div>
                                                                <input class="inp-cbx" id="declare" name="declare" type="checkbox">
                                                                <label class="cbx" for="declare">
                                                           <span>
                                                              <svg width="12px" height="10px">
                                                                 <use xlink:href="#check"></use>
                                                              </svg>
                                                           </span>
                                                                    <span class="ml-1">I Accept the </span>
                                                                    <span class="tm-txt" ata-toggle="modal" data-target="#exampleModalCenter">
                                                              <a href="javascript:void(0)" class="btn-none" data-toggle="modal" data-target="#exampleModalCenter">
                                                                 Terms &amp; Conditions
                                                              </a>

                                                           </span>
                                                                </label>


                                                                <!--SVG Sprites-->
                                                                <svg class="inline-svg">
                                                                    <symbol id="check" viewBox="0 0 12 10">
                                                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                                    </symbol>
                                                                </svg>
                                                            </div>
                                                            </li>
                                                            <br>



                                                        </ul>
                                                    <?php }?>
                                                    </div>
                                                    <!-- /.row -->
                                                    <div class="proposal_continue_back_margin">
                                                        <?php if($iback ){?>
                                                        <a href="/quotes/generate_quote_abc?lead_id=<?php echo $_REQUEST['lead_id']?>" class="btn btn_start_proposal_back"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back </a>
                                                      <?php }?>
                                                        <!-- <a href="" class="btn btn_start_proposal">Continue <i class="flaticon-next" aria-hidden="true"></i></a> -->
                                                        <!-- <button id="submit_lead" type="submit" class="btn sub-btn pr-4 pl-4">Continue
                                            <i class="ti-arrow-right arrow-xd">
                                            </i>
                                            </button> -->
                                                        <button class="btn btn-color-all save-proceed-mem memProposerBtn ClickBtn">Continue <i class="fa fa-long-arrow-right fwt"></i></button>
                                                    </div>
                                                </form>

                                            </div> <!-- /.sign-up-form-wrapper -->

                                        </div>
                                    </div>
                                </div>



                                <!-- Vectors -->
                                <div class="z-content">
                                    <div class="z-content-inner">
                                        <div class="row justify-content-md-center">

                                            <div class="col-lg-12 col-md-12 offset-md-1">
                                                <div class="faq-tab-wrapper">
                                                    <div class="faq-panel">

                                                        <div class="panel-group theme-accordion get_all_data" id="accordion">

                                                            <form id="submit_insure_data">
                                                                <p class="dis-web cl-web proposal_title" style="margin-top: 3% !important; color: #107591 !important;"> Insured Details</p>
                                                                <div class="get_title">

                                                                </div>

                                                                <?php if($iterms == true) { ?>
                                                        <ul class="col-md-12" id="termscondition">
                                                            <li class=" hidden-md">
                                                                <div>
                                                                <input class="inp-cbx" id="declare" name="declare" type="checkbox" required>
                                                                <label class="cbx" for="declare">
                                                           <span>
                                                              <svg width="12px" height="10px">
                                                                 <use xlink:href="#check"></use>
                                                              </svg>
                                                           </span>
                                                                    <span class="ml-1">I Accept the </span>
                                                                    <span class="tm-txt" ata-toggle="modal" data-target="#exampleModalCenter">
                                                              <a href="javascript:void(0)" class="btn-none" data-toggle="modal" data-target="#exampleModalCenter">
                                                                 Terms &amp; Conditions
                                                              </a>

                                                           </span>
                                                                </label>


                                                                <!--SVG Sprites-->
                                                                <svg class="inline-svg">
                                                                    <symbol id="check" viewBox="0 0 12 10">
                                                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                                    </symbol>
                                                                </svg>
                                                            </div>
                                                            </li>
                                                            <br>



                                                        </ul>
                                                    <?php }?>

                                        </form>




                                                        </div> <!-- end #accordion -->
                                                    </div> <!-- End of .faq-panel -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="proposal_continue_back_margin">

                                        <?php if($iback){?>
                                            <a href="#" class="btn btn_start_proposal_back mem_back"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back </a>
                                        <?php }?>
                                            <button type="button" class="btn save-proceed-mem memProposerBtn ClickBtn submit_insure_data ">Continue <i class="fa fa-long-arrow-right"></i></button>

                                        </div>


                                    </div>
                                </div>

                                <!-- Themes -->
                                <!-- <div class="z-content" style="display: none; position: absolute; left: 0px; top: 330px;"><div class="z-content-inner">
                                                                <div class="container">


                                                            <div class="box_shadow_box_card_medical">
                                                                <div class="row">
                                                                    <div class="col-lg-8 col-md-12">
                                                                        <img src="images/plus.png" style="float: left;     margin: 0 12px 0 2px;">
                                                                <p class="mb-10 p_propsal_form_r_q_m">Have insured been diagnosed / hospitalized for any illness / injury during last 48 months?</p>
                                                            </div>
                                                            <div class="col-lg-4 col-md-12 middle">


                        <label>
                        <input type="radio" name="radio" checked/>
                        <div class="front-end box">
                            <span>Yes</span>
                        </div>
                        </label>

                        <label>
                        <input type="radio" name="radio"/>
                        <div class="back-end box">
                            <span>No</span>
                        </div>
                        </label>


                                                            </div>
                                                            </div>
                                                                <div class="agreement-checkbox_medical margin_top_checkbox_card_proposal_m">
                                                                <div>
                                                                <input type="checkbox" id="compare_one_m1" class="compare-checkbox">
                                                                <label for="compare_one_m1">Self</label>
                                                            </div>
                                                            </div>
                                                            <div class="agreement-checkbox_medical margin_top_checkbox_card_proposal_m">
                                                                <div>
                                                                <input type="checkbox" id="compare_one_m2" class="compare-checkbox">
                                                                <label for="compare_one_m2">Spouse</label>
                                                            </div>
                                                            </div>
                                                            <div class="agreement-checkbox_medical margin_top_checkbox_card_proposal_m">
                                                                <div>
                                                                <input type="checkbox" id="compare_one_m3" class="compare-checkbox">
                                                                <label for="compare_one_m3">Daughter</label>
                                                            </div>
                                                            </div>
                                                            </div>
                                                            <div class="group">
                            <input type="radio" name="rb" id="rb1" checked="" />
                            <label for="rb1">Self</label>
                            <input type="radio" name="rb" id="rb2" />
                            <label for="rb2">Spouse</label>
                            <input type="radio" name="rb" id="rb3" />
                            <label for="rb3">Daughter</label>
                            <input type="radio" name="rb" id="rb4" />
                            <label for="rb4">Son</label>
                        </div>

                        </div>
                        <hr class="hr_border_bottom_pro_f">
                                                            <div class="box_shadow_box_card_medical">
                                                                <div class="row">
                                                                    <div class="col-lg-8 col-md-12">
                                                                        <img src="images/plus.png" style="float: left;     margin: 0 12px 0 2px;">
                                                                <p class="mb-10 p_propsal_form_r_q_m">Have insured been diagnosed / hospitalized for any illness / injury during last 48 months?</p>
                                                            </div>
                                                            <div class="col-lg-4 col-md-12 middle">


                        <label>
                        <input type="radio" name="radio" checked/>
                        <div class="front-end box">
                            <span>Yes</span>
                        </div>
                        </label>

                        <label>
                        <input type="radio" name="radio"/>
                        <div class="back-end box">
                            <span>No</span>
                        </div>
                        </label>


                                                            </div>
                                                            </div>

                                                            </div>
                                                            <div class="row row_bg_m_q">
                                                                <div class="col-lg-4 col-md-4 margin_proposal_form_self">
                                                                    <div class="agreement-checkbox_r_p margin_top_checkbox_card_proposal_m">
                                                            <div>
                                                                <input type="checkbox" id="compare_one_p_o" class="compare-checkbox" checked="">
                                                                <label for="compare_one_p_o"> Self</label>
                                                            </div>
                                                            </div>
                                                            <div class="agreement-checkbox_r_p margin_top_checkbox_card_proposal_m">
                                                            <div>
                                                                <input type="checkbox" id="compare_one_p_e" class="compare-checkbox" checked="">
                                                                <label for="compare_one_p_e"> Spouse</label>
                                                            </div>
                                                            </div>
                                                                <div class="agreement-checkbox_r_p margin_top_checkbox_card_proposal_m">
                                                            <div>
                                                                <input type="checkbox" id="compare_one_p_d" class="compare-checkbox" checked="">
                                                                <label for="compare_one_p_d"> Daughter</label>
                                                            </div>
                                                            </div>
                                                                <div class="agreement-checkbox_r_p margin_top_checkbox_card_proposal_m">
                                                            <div>
                                                                <input type="checkbox" id="compare_one_p_s" class="compare-checkbox" checked="">
                                                                <label for="compare_one_p_s"> Son</label>
                                                            </div>
                                                            </div>

                                                                </div>
                                                                <div class="col-md-8 bg_8_m_q">
                                                                    <p class="bg_p_8_m_q">Select Medical History</p>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Hypertension / High BP</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Respiratory disorders</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">HIV / AIDS / STD</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Liver Disease</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Cancer / Tumor</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Cardiac Disease</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Arthritis / Joint pain</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Kidney Disease</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Paralysis / Stroke</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Congenital Disorder</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row row_m_q_t">
                                                                        <div class="col-md-6 border_right_p_m_q">
                                                                            <p class="p_dieases_n_s">Any Other Diseases</p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control-age_m">
                                                                                <option>MM-YY</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="proposal_continue_back_margin">
                                                <a href="#" class="btn btn_start_proposal_back"><i class="flaticon-back" aria-hidden="true"></i> Back </a>
                                                <a href="#" class="btn btn_start_proposal">Continue <i class="flaticon-next" aria-hidden="true"></i></a>
                                            </div>
                                                        </div>

                                                            </div> -->


                                <!-- Themes -->
                                <div class="z-content" style="display: none; position: absolute; left: 0px; top: 330px;">
                                    <div class="z-content-inner">
                                        <div class="signUp-page signUp-minimal pb-10">

                                            <div class="signin-form-wrapper pad_proposal_s">
                                                <p class="dis-web cl-web proposal_title" style="color: #107591 !important;"> Nominee Details</p>
                                                <form id="nominee-form">
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group dropdown_product_m_t_b_p">
                                                                <select name="nominee_relation" class="form-control-age_nominee nominee_rel">
                                                                    <option value="" selected="selected">Enter Nominee Relation</option>
                                                                    <?php foreach ($nominee_relations as $key => $value) { ?>
                                                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                                <label class="over_border_txt_proposal_add">Enter Nominee Relation <?php if ($plan_data["nominee_mandatory"]==1){ ?><span style="color:#FF0000">*</span> <?php }?></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="text" class="alphabates_only nominee_fname" name="nominee_name" placeholder="Enter Nominee Name">
                                                                <label class="over_border_txt_proposal_add">Nominee Name <?php if ($plan_data["nominee_mandatory"]==1){ ?><span style="color:#FF0000">*</span> <?php }?></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->

                                                        <!-- /.col- -->
                                                    </div>
                                                    <!-- /.row -->
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="date" class="nominee_dob" id="DOBInput" name="nominee_dob" placeholder="Enter Nominee DOB" onkeydown = "return false;">
                                                                <label>Nominee DOB <?php if ($plan_data["nominee_mandatory"]==1){ ?><span style="color:#FF0000">*</span> <?php }?></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                        <!-- /.col- -->
                                                        <div class="col-12 col-md-6">
                                                            <div class="input-group mb_15_point dropdown_product_m_t_b_p">
                                                                <input type="text" class="numeric_only nominee_no" name="nominee_contact_number" placeholder="Enter Nominee Contact Number" maxlength="10">
                                                                <label>Nominee Contact Number <?php if ($plan_data["nominee_mandatory"]==1){ ?><span style="color:#FF0000">*</span> <?php }?></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                            <!-- /.input-group -->
                                                        </div>
                                                    </div>
                                                    <!-- <div class="container hidden-lg">
                                         <div class="box_shadow_box_card_medical">
                                            <div class="row">
                                               <div class="col-lg-8 col-md-12">
                                                  <img src="images/plus.png" style="float: left;     margin: 0 12px 0 2px;">
                                                  <p class="mb-10 p_propsal_form_r_q_m">Have insured ever filled / applied a claim with their current / previous insurer?</p>
                                               </div>
                                            </div>
                                         </div>
                                         <div class="group no_padding_ipad">
                                            <input type="radio" name="rb" id="rb11" checked="">
                                            <label for="rb11">Self</label>
                                            <input type="radio" name="rb" id="rb12">
                                            <label for="rb12">Spouse</label>
                                            <input type="radio" name="rb" id="rb13">
                                            <label for="rb13">Daughter</label>
                                            <input type="radio" name="rb" id="rb14">
                                            <label for="rb14">Son</label>
                                         </div>
                                      </div>
                                      <hr class="hr_border_bottom_pro_f hidden-lg">
                                      <div class="container hidden-lg">
                                         <div class="box_shadow_box_card_medical">
                                            <div class="row">
                                               <div class="col-lg-8 col-md-12">
                                                  <img src="images/plus.png" style="float: left;     margin: 0 12px 0 2px;">
                                                  <p class="mb-10 p_propsal_form_r_q_m">Has any proposal for health insurance been declined, cancelled or charged a higher premium?</p>
                                               </div>
                                            </div>
                                         </div>
                                         <div class="group no_padding_ipad">
                                            <input type="radio" name="rb" id="rb21" checked="">
                                            <label for="rb21">Self</label>
                                            <input type="radio" name="rb" id="rb22">
                                            <label for="rb22">Spouse</label>
                                            <input type="radio" name="rb" id="rb23">
                                            <label for="rb23">Daughter</label>
                                            <input type="radio" name="rb" id="rb24">
                                            <label for="rb24">Son</label>
                                         </div>
                                      </div>
                                      <hr class="hr_border_bottom_pro_f">
                                      <div class="container hidden-lg">
                                         <div class="box_shadow_box_card_medical">
                                            <div class="row">
                                               <div class="col-lg-8 col-md-12">
                                                  <img src="images/plus.png" style="float: left;     margin: 0 12px 0 2px;">
                                                  <p class="mb-10 p_propsal_form_r_q_m">Is insured already covered under any other health insurance policy of Care Health Insurance(formerely known as Religare Health Insurance Company Limited)?</p>
                                               </div>
                                            </div>
                                         </div>
                                         <div class="group no_padding_ipad">
                                            <input type="radio" name="rb" id="rb31" checked="">
                                            <label for="rb31">Self</label>
                                            <input type="radio" name="rb" id="rb32">
                                            <label for="rb32">Spouse</label>
                                            <input type="radio" name="rb" id="rb33">
                                            <label for="rb33">Daughter</label>
                                            <input type="radio" name="rb" id="rb34">
                                            <label for="rb34">Son</label>
                                         </div>
                                      </div>
                                      <p class="margin_b_o_table hidden-md">Check the box if the answer is yes</p>
                                      <ul class="responsive-table ul_row_box hidden-md">
                                         <li class="table-header">
                                            <div class="col col-1">Type</div>
                                            <div class="col col-2">Self</div>
                                            <div class="col col-3">Spouse</div>
                                            <div class="col col-4">Daughter</div>
                                            <div class="col col-5">Son</div>
                                            <div class="col col-6">Adult1</div>
                                            <div class="col col-7">Son1</div>
                                            <div class="col col-8">Son3</div>
                                         </li>
                                         <li class="table-row">
                                            <div class="col col-1" data-label="Job Id">Have insured ever filled / applied a claim with their current / previous insurer?</div>
                                            <div class="col col-2" data-label="Customer Name">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p" class="compare-checkbox" checked="">
                                                     <label for="compare_one_p">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-3" data-label="Amount">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-4" data-label="Payment Status">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-5">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-6">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-7">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-8">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                         </li>
                                         <li class="table-row">
                                            <div class="col col-1" data-label="Job Id">Has any proposal for health insurance been declined, cancelled or charged a higher premium?</div>
                                            <div class="col col-2" data-label="Customer Name">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p" class="compare-checkbox" checked="">
                                                     <label for="compare_one_p">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-3" data-label="Amount">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-4" data-label="Payment Status">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-5">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-6">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-7">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-8">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                         </li>
                                         <li class="table-row">
                                            <div class="col col-1" data-label="Job Id">Is insured already covered under any other health insurance policy of Care Health Insurance(formerely known as Religare Health Insurance Company Limited)?</div>
                                            <div class="col col-2" data-label="Customer Name">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p" class="compare-checkbox" checked="">
                                                     <label for="compare_one_p">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-3" data-label="Amount">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-4" data-label="Payment Status">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-5">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-6">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-7">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="col col-8">
                                               <div class="agreement-checkbox_r_other margin_top_checkbox_card_proposal_other">
                                                  <div>
                                                     <input type="checkbox" id="compare_one_p_r" class="compare-checkbox">
                                                     <label for="compare_one_p_r">&nbsp;</label>
                                                  </div>
                                               </div>
                                            </div>
                                         </li>
                                         <div class="theme-pagination-one text-center pt-15">
                                            <ul>
                                               <li><a href="#" class="btn btn_back_proposal_pagination"><i class="flaticon-back"></i> </a></li>
                                               <li class="active"><a href="#">1</a></li>
                                               <li><a href="#">2</a></li>
                                               <li><a href="#">3</a></li>
                                               <li><a href="#">4</a></li>
                                               <li><a href="#" class="btn btn_next_proposal_pagination"><i class="flaticon-next" style="margin:0 -3px 0 -14px !important;"></i></a></li>
                                            </ul>
                                         </div>
                                      </ul>-->

                                                    <div class="proposal_continue_back_margin">
                                                        <?php if($nback){?>
                                                        <a href="#" class="btn btn_start_proposal_back nom_back"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back </a>
                                                    <?php }?>
                                                        <!-- <a href="#" class="btn btn_start_proposal">Continue <i class="flaticon-next" aria-hidden="true"></i></a> -->
                                                        <button class="btn save-proceed-nominee memNomineeBtn ClickBtn memProposerBtn SubmitProposal">Continue <i class="fa fa-long-arrow-right"></i></button>
                                                    </div>
                                                </form>
                                            </div> <!-- /.sign-up-form-wrapper -->

                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div> <!-- /.theme-tab -->
                    </div>


                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-8 col-12  box-shadow_plan_box_p_s_s_proposal_form aos-init" data-aos="fade-left" style="margin-top:7%;">
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

                            <div class="single-block mb-80 main-menu-list">
                                <?php
                                //                                var_dump($get_summary_details);
                                //                                exit;
                                foreach($get_summary_details['insured_member'] as $req_val){
                                    ?>
                                    <div class="row margin_top_proposal_summary_r_b">
                                        <p class="col-md-12 text-left plan_ic_name_y"><?php echo $req_val['policy_sub_type_name'];?></p>



                                        <div class="col-lg-6 col-md-4">
                                            <p class="color_cover_p_r_b">Cover Amount</p>
                                            <p class="p_c_r_b_p cover_amt"><i class="fa fa-inr"></i> <?php echo $req_val['cover'];?></p>
                                        </div>
                                        <div class="col-lg-6 col-md-4">
                                            <p class="color_cover_p_r_b"> Premium</p>
                                            <p class="p_c_r_b_p base_premium"><i class="fa fa-inr"></i> <?php echo $req_val['premium'];?></p>
                                        </div>
                                        <div class="col-lg-6 col-md-4">
                                            <p class="color_cover_p_r_b">Policy Tenure</p>
                                            <p class="p_c_r_b_p cover_tenure">1 Year</p>
                                        </div>

                                        <?php if(!empty($deductable['deductable'])){ ?>

                                        <div class="col-lg-6 col-md-4">
                                            <p class="color_cover_p_r_b">Deductible</p>
                                            <p class="p_c_r_b_p deductable"><i class="fa fa-inr"></i> <?php echo $deductable['deductable'];?></p>
                                        </div>
                                    <?php }?>

                                    </div>
                                <?php   } ?>
                                <ul class="list-item">
                                    <!-- <br> -->



                                    <li class="total_premium_btn_proposal_form"><a href="" class="addon_font_s_s_pro">Total Premium<small>(Incl gst)</small>
                                            <span class="font_bold total_premium_btn_addon_r_p_f cover_premium"><i class="fa fa-inr"></i> <?php echo $req_val['total_premium'];?></span></a></li>
                                </ul>
                                <div class="row">
                                    <!-- <a href="#" data-toggle="modal" data-target="#m-md" class="read-more text-center">Click here <i class="flaticon-next-1"></i></a> -->
                                    <!-- <div class="col-md-12"> -->

                                </div>
                            </div>

                        </div> <!-- /.single-block -->

                    </div>


                </div>
            </div>
        </div>




    </div> <!-- /.full-width-container -->









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
                            <p style="font-size: 15px;">Your premium has increased/decreased due to following reasons.<br>Your Premium is<b> <i class="fa fa-inr"></i> 6,149</b></p>
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

    <!-- Terms and conditions modal---->
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

            <!-- end Terms and conditions modal---->


    <!-- Scroll Top Button -->
    <button class="scroll-top tran3s">
        <i class="fa fa-angle-up" aria-hidden="true"></i>
    </button>




    <script>
        /*$(function () {
            var dob_proposer = $('#proposer_dob').attr('maxage');
            if(dob_proposer != ''){
                let today = new Date().toISOString().split('T')[0];
                let date3m = new Date();
                alert(date3m.getFullYear());
                date3m.setFullYear(date3m.getFullYear() - 20);
                date3m = date3m.toISOString().split('T')[0];
                //  document.getElementsByName("proposer_dob")[0].setAttribute('min', today);
                document.getElementsByName("proposer_dob")[0].setAttribute('max', date3m);
            }else {


                var year = dtToday.getFullYear() - 18;
                if (month < 10)
                    month = '0' + month.toString();
                if (day < 10)
                    day = '0' + day.toString();
                var minDate = year + '-' + month + '-' + day;
                var maxDate = year + '-' + month + '-' + day;
                $('#proposer_dob').attr('max', maxDate);
            }
            $("#proposer_dob").datepicker({
                autoclose: true,
                format: "dd/mm/yyyy",
                changeMonth: true,
                changeYear: true,
                yearRange: '-99:-18'

            });
          //  $("#proposer_dob").datepicker({'maxDate',"-18yr"});
            $("#proposer_dob").datepicker().datepicker('setDate', '-18y');
            $("#proposer_dob").datepicker().datepicker('endDate', '-18y');

        });*/
    </script>



    <script>

        function ajaxindicatorstart(text) {
            text = typeof text !== "undefined" ? text : "Please wait....";

            var res = "";

            if ($("body").find("#resultLoading").attr("id") != "resultLoading") {
                res += "<div id='resultLoading' style='display: none'>";
                res += "<div id='resultcontent'>";
                res += "<div id='ajaxloader' class='txt'>";
                res +=
                    '<svg class="lds-curve-bars" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g transform="translate(50,50)"><circle cx="0" cy="0" r="8.333333333333334" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="26.179938779914945 26.179938779914945" transform="rotate(2.72337)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="0" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="16.666666666666668" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="52.35987755982989 52.35987755982989" transform="rotate(64.7343)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.2" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="25" fill="none" stroke="#ffffff" stroke-width="4" stroke-dasharray="78.53981633974483 78.53981633974483" transform="rotate(150.07)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.4" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="33.333333333333336" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="104.71975511965978 104.71975511965978" transform="rotate(239.433)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.6" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="41.666666666666664" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="130.89969389957471 130.89969389957471" transform="rotate(320.34)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.8" repeatCount="indefinite"></animateTransform></circle></g></svg>';
                res += "<br/>";
                res += "<span id='loadingMsg'></span>";
                res += "</div>";
                res += "</div>";
                res += "</div>";

                $("body").append(res);
            }

            $("#loadingMsg").html(text);

            $("#resultLoading").find("#resultcontent > #ajaxloader").css({
                position: "absolute",
                width: "500px",
                height: "75px",
            });

            $("#resultLoading").css({
                width: "100%",
                height: "100%",
                position: "fixed",
                "z-index": "10000000",
                top: "0",
                left: "0",
                right: "0",
                bottom: "0",
                margin: "auto",
            });

            $("#resultLoading").find("#resultcontent").css({
                background: "#ffffff",
                opacity: "0.7",
                width: "100%",
                height: "100%",
                "text-align": "center",
                "vertical-align": "middle",
                position: "fixed",
                top: "0",
                left: "0",
                right: "0",
                bottom: "0",
                margin: "auto",
                "font-size": "16px",
                "z-index": "10",
                color: "#000000",
            });

            $("#resultLoading").find(".txt").css({
                position: "absolute",
                top: "-25%",
                bottom: "0",
                left: "0",
                right: "0",
                margin: "auto",
            });

            $("#resultLoading").fadeIn(300);

            $("body").css("cursor", "wait");
        }

        function ajaxindicatorstop() {
            $("#resultLoading").fadeOut(300);

            $("body").css("cursor", "default");
        }
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            $(function () {
                debugger;
                var dob_proposer = $('#proposer_dob').attr('maxage');
                let maxDate = '';
                let minDates = '';
                let mindate = '';
                var month = '';
                if (dob_proposer != '' && dob_proposer != undefined) {



                    let today = new Date().toISOString().split('T')[0];
                    let date3m = new Date();
                    var date = date3m.getDate()+1;
                    month = date3m.getMonth()-1;
                    date3m.setFullYear(date3m.getFullYear() - dob_proposer);

                    minDates = date3m.getFullYear()-1;


                    // date3m = date3m.toISOString().split('T')[0];
                    maxDate = '-' + dob_proposer + 'y -1d';
                    mindate = new Date(minDates, month + 1, date) ;

                } else {



                    maxDate = '-18y';

                    minDates = '1940';
                    mindate = new Date(minDates,1,-1,1);

                }
                $("#proposer_dob").datepicker({
                    beforeShow: function(i) {
                      if ($(this).prop('readonly')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                        }
                    },
                    dateFormat: 'dd-mm-yy',
                    currentDate: null,

                    prevText: '<i class="fa fa-angle-left"></i>',
                    nextText: '<i class="fa fa-angle-right"></i>',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-100:" + new Date('Y'),
                    minDate: mindate,

                    maxDate: maxDate

                });
            });
        });
    </script>
    <script>
        $("#proposer_dob").keydown(function (e) {
            if (e.keyCode !== undefined) {
                //alert(e.keyCode);
                e.preventDefault();
                return false;
            }
            return false;
        });
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
        $(document).on('change','#salutation', function(event) {
            if($(this).val()=='Mr'){
                console.log($(this).val())
                $('#gender').val('Male')
            }else{
                $('#gender').val('Female')
            }

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



        $(document).ready(function() {




            $.ajax({
                url: "/quotes/fetch_nominee_details",
                type: "POST",
                async: false,
                dataType: "json",
                success: function(response) {
                    var res = JSON.parse(response.data);

                    if (res) {
                        var member_name = res.policy_data.nominee_first_name + "    " + res.policy_data.nominee_last_name;
                        $('.nominee_fname').val(member_name);
                        $('.nominee_dob').val(res.policy_data.nominee_dob);
                        $('.nominee_rel').val(res.policy_data.nominee_relation);
                        $('.nominee_no').val(res.policy_data.nominee_contact);

                    }
                }
            });

            $("body").on("keyup", "#mobile_no", function(e) {
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

            $.validator.addMethod('validMobile', function(value, element, param) {
                var mobileInput = value;
                var validMobRe = new RegExp('^[6-9][0-9]{9}$');
                return this.optional(element) || validMobRe.test(mobileInput) && mobileInput.length > 0;
            }, 'Enter valid mobile number');


            $.validator.addMethod('validGSTIN', function(value, element, param) {
                var gstvalue = value;
                var validGstRe =  /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([0-9]){1}?$/;
                console.log(validGstRe.test(gstvalue));
                return this.optional(element) || validGstRe.test(gstvalue) && gstvalue.length > 0;
            }, 'Enter valid GSTIN number');

            $.validator.addMethod("valid_address", function(value, element, param) {
                if (value.length == 0) {
                    return true;
                }
                var reg = /^[0-9a-zA-Z/,/-]+$/;
                return reg.test(value); // Compare with regular expression
            }, "Please enter a valid address.");

            $.validator.addMethod("validate_pincode", function(value, element, param) {
                var regs = /^\d{6}$/g;
                return this.optional(element) || regs.test(value);
            }, "Enter a valid Pin Code");

            $.validator.addMethod("validate_pancard", function(value, element, param) {
                var regex = /([A-Z]){5}([0-9]){4}([A-Z]){1}$/;
                return this.optional(element) || regex.test(value.toUpperCase());
            }, "Enter a valid Pancard Number");

            $("body").on("keyup", ".numeric_only", function(e) {
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

            $("body").on("keyup", ".alphabates_only", function(e) {
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
                        $th.val().replace(/[^A-Za-z ]/g, function(str) {
                            return "";
                        })
                    );
                }
                return;
            });

             $("body").on("keyup", "#proposer_pan,#gstin", function (e) {
                var $th = $(this);
                $(this).val($(this).val().toUpperCase());

                $th.val(
                    $th.val().replace(/[^A-Z0-9 ]/g, function(str) {
                        return "";
                    })
                );
            });


            $("#proposer_pincode").keyup(function(e) {
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
                $("#city").val('');
                $("#state").val('');
                var pincode = $(this).val();
                if (pincode.length == 6) {

                    $.ajax({
                        url: "/quotes/axis_pincode_get_state_city",
                        type: "POST",
                        async: false,
                        data: {
                            'pincode': pincode
                        },
                        dataType: "json",
                        success: function(response) {

                            if (response == null) {
                                swal("Alert", "Pincode not found in pincode master.", "warning");
                                //$('#pincode').after('<label id="pincode-error" class="error" for="pincode">Pincode is unavailable in the pincode master. Please get in touch with ABHI Operations team to get the Pincode added in the master.</label>');

                            } else if (response.city != null && response.state != null) {
                                $("#city").val(response.city);
                                $("#state").val(response.state);
                                //$('#pincode').html('');
                            }



                        }
                    });
                }


            });
            $.validator.addMethod(
                "validateEmail",
                function(value, element, param) {
                    if (value.length == 0) {
                        return true;
                    }
                    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                    return reg.test(value); // Compare with regular expression
                },
                 "Please enter a valid Email ID."
            );

            jQuery.validator.addMethod(
                "minDOB",
                function(value, element) {              
                    var from = value.split("-"); 
                    var day = from[2];
                    var month = from[1];
                    var year = from[0];
                    var age = 18;

                    var mydate = new Date();
                    mydate.setFullYear(year, month-1, day);

                    var currdate = new Date();
                    var setDate = new Date();

                    setDate.setFullYear(mydate.getFullYear() + age, month-1, day);

                    if ((currdate - setDate) > 0){
                        return true;
                    }else{
                        return false;
                    }
                },
                "You must be 18 years of age to proceed"
            );


            function getMonth(dateString) {

              var birthdate = new Date(dateString).getTime();
               var now = new Date();
                var d2 = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
                d2.setDate(d2.getDate() -1);
                console.log(d2)
              var now = d2.getTime();
              // now find the difference between now and the birthdate
              var n = (now - birthdate)/1000;

              if (n < 604800) { // less than a week
                var day_n = Math.floor(n/86400);
                return day_n + ' day' + (day_n > 1 ? 's' : '');
              } else if (n < 2629743) {  // less than a month
                var week_n = Math.floor(n/604800);
                return week_n + ' week' + (week_n > 1 ? 's' : '');
              } else if (n < 63113852) { // less than 24 months
                var month_n = Math.floor(n/2629743);
                return parseInt(month_n);
              } else { 
                var year_n = Math.floor(n/31556926);
                return year_n + ' year' + (year_n > 1 ? 's' : '');
              }
            }

            var age = '';     
            var yearmonth = '';     
            jQuery.validator.addMethod(
                "validAge",
                function(value, element, arg) { 

                    var from = value.split("-"); 
                    var day = from[2];
                    var month = from[1];
                    var year = from[0];
                    age = parseInt($(element).attr('maxage'));
                    yearmonth = $(element).attr('year_month');
                    var mydate = new Date(year,month-1,day, 23, 59, 59);


                    if(yearmonth=='year'){
                        if(age>1){
                            yearmonth = 'years';  
                        }
                        var now = new Date();
                        var d2 = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
                        d2.setDate(d2.getDate() -1);
                       
                        var diff = d2.getTime() - mydate.getTime();
                        var dobage = Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));


                    }else{
                        dobage = getMonth(mydate);

                    }

                    if (dobage == age){
                        return true;
                    }else{
                        return false;
                    }
                    
                }, function() {return 'You must be ' + age + ' '+yearmonth+' of age to proceed '}
            );
            jQuery.validator.addMethod(
                "validDOB",
                function(value, element) {   
                           
                    var from = value.split("-"); 
                    var day = from[2];
                    var month = from[1];
                    var year = from[0];
                    var age = 18;

                    var mydate = new Date();
                    mydate.setFullYear(year, month-1, day);

                    var currdate = new Date();
                    currdate = new Date(currdate-1);
                    
                    if ((mydate>currdate) > 0){
                        return false;
                    }else{
                        return true;
                    }
                },
                "Please enter valid dob"
            );
            jQuery.validator.addClassRules('bdateInsuredmember', {
                validDOB: true,
            });

            jQuery.validator.addClassRules('validage', {
                validAge: true,
            });

            let rules= {

                proposer_dob: {
                    required: true,

                    minDOB:true,
                   // validDOB:true

                },
                lname: {
                    required: true,


                },
                fname: {
                    required: true,


                },
                salutation: {
                    required: true,


                },
                email: {
                    required: true,
                    validateEmail: true,
                },
                mobile_no: {
                    required: true,
                    validMobile: true
                },
                proposer_address: {
                    required: true,
                    //valid_address : true
                },
                proposer_pincode: {
                    required: true,
                    validate_pincode: true,
                },
                status: {
                    required: true
                },
                 gender: {
                     required: true
                 },
                proposer_pan:{
                    validate_pancard: true
                },
                gstin:{
                    validGSTIN: true
                },
                declare:{
                    required: false
                }
            };

            var pan_man = '<?php echo $plan_data["pan_mandatory"] ?>';
            var nominee_man = '<?php echo $plan_data["nominee_mandatory"] ?>';
            var payment_page = '<?php echo $plan_data["payment_page"] ?>';
            var payment_first = '<?php echo ($plan_data["payment_first"]==1 )?1:''; ?>';
            var pterms = '<?php echo $pterms ?>';
            var iterms = '<?php echo $iterms ?>';
            if(pan_man=='1'){
                rules.proposer_pan.required = true
            }
            if(pterms=='1' || pterms=='true' ){
                rules.declare.required = true
            }


            $("#proposerDetails").validate({
                ignore: ".ignore",
                rules: rules,
                messages: {
                    declare:{required:"Please accept Terms & conditions."},
                },
                 errorPlacement: function(label, element) {
                    if (element.attr("id") == "declare") {

                        label.insertAfter(element.parents().find('#termscondition div'))

                    }else{
                        label.insertAfter(element);
                    } 



                  },
                submitHandler: function(form) {
debugger;
                    var is_proposer_insured=$('#is_proposer_insured').val();
                    age=[];
                    /*if(is_proposer_insured == 'Yes' && payment_first!='1'){
                        var newdates = $('#proposer_dob').val();
                        var dob1 = newdates.split("-").reverse().join("-");
                        dob = new Date(dob1);
                        var today = new Date();
                        var a=Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));

                        age.push(Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000)));
                        var i;
                        //if insured memebr alredy done
                        var inputs = $(".bdateInsuredmember");

                        for( i = 0; i < inputs.length; i++){
                            dob=$(inputs[i]).val();
                   
                            if(dob!='' && dob != '0000-00-00' && dob !=NaN){
                                var date_components = dob.split("-");
                                var day = date_components[0];
                                var month = date_components[1];
                                var year = date_components[2];  
                                dob = month+'-'+day+'-'+year;
                                dob = new Date(dob);
                            
                                var today = new Date();
                                age.push(Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000)));
                            }
                            
                        }
                        const newArray = age.filter(function (value) {
                            return !Number.isNaN(value);
                        });

                        age_val = Math.max.apply(Math, newArray);

                      getNewPremium(age_val).then(e => {
                        if(e !== 'false'){
                            swal({
                                title: 'Alert',
                                text: "As per entered DOB, age and Premium are updated",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ok'
                            }).then(function() {
                                var form = $("#proposerDetails").serialize();
                                $.post("/quotes/update_proposer_details", form, function(e) {
                                    var response = JSON.parse(e);
                                    if (response.status == 'success') {
                                        swal("success", "Proposer details updated successfully !", "success");
                                        if(payment_first==1 && payment_page==2){
                                            window.location.href = '/quotes/redirect_to_pg?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>';
                                        }else{
                                            $(".insured_det").trigger("click");
                                        }
                                        
                                    } else {
                                        swal("Alert", response.message, "warning");
                                    }

                                    // $(".nomineee_det").trigger("click");
                                });
                            })
                        }else{
                            var form = $("#proposerDetails").serialize();
                            $.post("/quotes/update_proposer_details", form, function(e) {
                                var response = JSON.parse(e);
                                if (response.status == 'success') {
                                    swal("success", "Proposer details updated successfully !", "success");
                                    if(payment_first==1 && payment_page==2){
                                        window.location.href = '/quotes/redirect_to_pg?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>';
                                    }else{
                                        $(".insured_det").trigger("click");
                                    }
                                } else {
                                    swal("Alert", response.message, "warning");
                                }

                                // $(".nomineee_det").trigger("click");
                            });
                        }

                      });
                    }
                    
                    else{*/
                        var form = $("#proposerDetails").serialize();
                                $.post("/quotes/update_proposer_details", form, function(e) {
                                    var response = JSON.parse(e);
                                    if (response.status == 'success') {

                                        swal("success", "Proposer details updated successfully !", "success");

                                        if(payment_first==1 && payment_page==2){
                                            window.location.href = '/quotes/redirect_to_pg?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>';
                                        }else{
                                            $(".insured_det").trigger("click");
                                        }
                                    } else {
                                        swal("Alert", response.message, "warning");
                                    }

                                    // $(".nomineee_det").trigger("click");
                                });
                    //}


                }
            });
            let nominee_rules= {nominee_contact_number:{validMobile: true},nominee_dob:{validDOB: true}};
            if(nominee_man=='1'){
                nominee_rules={
                    nominee_name: {
                        required: true
                    },
                    nominee_relation: {
                        required: true
                    },
                    nominee_dob: {
                        required: true,
                        validDOB:true
                    },
                    nominee_contact_number: {
                        required: true,
                        validMobile: true
                    }
                };
            }
            $("#nominee-form").validate({
                ignore: ".ignore",
                rules: nominee_rules,
                messages: {

                },
                 errorPlacement: function(label, element) {
                    if (element.is("select") || element.attr("type") == "date") {

                       element.removeClass('error');

                    } 
                      label.insertAfter(element);


                  },
                submitHandler: function(form) {
                    var form = $("#nominee-form").serialize();
                    $.post("/quotes/update_nominee_details", form, function(e) {

                        create_proposal();

                        
                    })

                }
            });




        });

        function submit_nominee(){
            var form = $("#nominee-form").serialize();
            $.post("/quotes/update_nominee_details", form, function(e) {
                    create_proposal();

            })
        }

        function create_proposal(){

            var get_policy_id = $('#hiddenpolicyid').val();
            var get_premium = $('#hiddenpremium').val();

            data = {};
            data.policy_id = get_policy_id;
            data.premium = get_premium;

            $.ajax({
                url: "/quotes/create_proposal",
                type: "POST",
                async: false,
                data: data,
                dataType: "json",
                success: function(response) {
                    var nominee_man = '<?php echo $plan_data['nominee_mandatory'];?>';
                    if(response.status == false){
                        swal("error", response.messages, "error");
                    }else{
                        if(nominee_man=='1'){
                            swal("success", "Nomineee details updated successfully !", "success");
                            swal({
                                title: 'Success',
                                text: "Proposal  details updated successfully !",
                                type: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ok'
                            }).then(function() {
                                ajaxindicatorstart();
                                window.location = "/quotes/proposal_summary?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>";


                            });
                            
                        }else{
                            $('#preloader').show();
                            $('#ctn-preloader').show();
                            window.location = "/quotes/proposal_summary?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>";
                            // swal("success", "Proposal details updated successfully !", "success");
                            // swal({
                            //     title: 'Success',
                            //     text: "Proposal details updated successfully !",
                            //     type: 'success',
                            //     showCancelButton: true,
                            //     confirmButtonColor: '#3085d6',
                            //     cancelButtonColor: '#d33',
                            //     confirmButtonText: 'Ok'
                            // }).then(function() {
                            //     ajaxindicatorstart();
                            //     window.location = "/quotes/proposal_summary?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>";


                            // });
                        }



                    }


                }
            });
        }


        function get_member_insure() {
            
            $.ajax({
                url: "/quotes/get_member_insure",
                type: "POST",
                async: false,
                data: {},
                dataType: "json",
                success: function(response) {


                   debugger;

                    
                    var res = response;
                    var i;
                    var j;
                    var data;
                    if (res) {
                        $('.get_title').html('');
                        var fname = '';
                        var lname = '';
                        var dob = '';
                        var marital_status = '';
                        var gender = '';
                        var disable;
                        var sel_option = '';
                        var is_array = '';
                        var sel_rel = '';
                        var salutation = '';
                        var kid_no = 1;
                        var pf ='<?php echo ($plan_data['payment_first']==1)?'true':''?>';
                        for (i = 0; i < res.length; i++) {
                            //alert(res[i].policy_member_first_name);

                            if (res[i].policy_member_first_name != null) {
                                //  alert(res[i].policy_member_first_name)
                                fname = res[i].policy_member_first_name;
                            } else {
                                fname = '';
                            }

                            
                            if (res[i].policy_member_last_name !== undefined && res[i].policy_member_last_name != null) {
                                lname = res[i].policy_member_last_name;

                            } else {
                                lname = '';
                            }

                            if (res[i].policy_member_dob !== undefined && res[i].policy_member_dob != null) {
                                dob = res[i].policy_member_dob;

                            } else {
                                dob = '';
                            }

                            if (res[i].policy_member_salutation !== undefined && res[i].policy_member_salutation != null) {
                                salutation = res[i].policy_member_salutation;

                            } else {
                                salutation = '';
                            }



                            if (res[i].policy_member_gender !== undefined && res[i].policy_member_gender != null) {
                                gender = res[i].policy_member_gender;

                            } else {
                                gender = '';
                            }



                            if($('#is_proposer_insured').val()=='Yes' && res[i].id=='1'){
                                fname = $('#fname').val();
                                lname = $('#lname').val();
                                gender = $('#gender').val();
                                salutation = $('#salutation').val();
                            }
                            
                            if (res[i].policy_member_marital_status !== undefined && res[i].policy_member_marital_status != null) {
                                marital_status = res[i].policy_member_marital_status;

                            } else {
                                marital_status = '';
                            }
                            
                            var readonly = '';
                            if (gender == '' && res[i].member_type == 'Son') {
                                gender = 'Male';
                                readonly = 'style="pointer-events: none;"';
                            }

                            if (gender == '' && res[i].member_type == 'Daughter') {

                                gender = 'Female';
                                readonly = 'style="pointer-events: none;"';
                            }

                            if (i == 0) {
                                var clsfa = 'fa-minus';
                            } else {
                                var clsfa = 'fa-plus';
                            }
                            var proposal_gender = $('#gender').val();


                            var validage = '<?php echo ($plan_data["payment_first"]==1)?'validage':'';?>';
                            var year_month = '<?php echo ($plan_data["payment_first"]==1)?' year_month=':'';?>';
                            if(year_month!='')
                            {
                                year_month = year_month+res[i].member_age_month;
                            }


                            var validage = '<?php echo ($plan_data["payment_first"]==1)?'validage':'';?>';
                            var marital_dropdown = '<option value="Single">Single</option><option value="Married">Married</option><option value="Divorced">Divorced</option><option value="Widowed">Widowed</option>';


                            var single = '<?php echo $single; ?>';

                            if(res[i].is_adult == 'Y'){
                                if((res[i].id=='1' || res[i].id=='2') && single !='1' ){
                                    var marital_dropdown = '<option value="Married">Married</option>';
                                }

                                sel_option='<option value="' + res[i].id + '" selected="selected">' + res[i].member_type + '</option>';

                                var maxage = res[i].member_age;
                                var kids = 1;

                                data = '<div class="panel"><div class="panel-heading"><h6 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse' + i + '">' + res[i].member_type + ' <i class="more-less fa ' + clsfa + '"></i> </a><input type ="hidden" name = display[' + res[i].id + '][member_ages_id] value ="' + res[i].member_ages_id + '"></h6></div>';


                                data += '<div id="collapse' + i + '" class="panel-collapse collapse"><div class="panel-body"><div class="signUp-page signUp-minimal"><div class="signin-form-wrapper pad_proposal_s"><div class="row margin_top_40_proposal_insured"> <div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled ' + res[i].id + '_salutation" id ="' + res[i].id + '_salutation" name = "display[' + res[i].id + '][salutation]" required="required" data-error="This field is required."><option value="">- Select -</option><option value="Mr">Mr</option><option value="Mrs">Mrs</option><option value="Ms">Ms</option></select><label class="over_border_txt_proposal_add">Salutation<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div> <div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled color_text  sel_'+res[i].id+' kidchange"  name ="display[' + res[i].id + '][rel]"  required="required" data-error="Valid email is required." id="'+res[i].id+'">'+sel_option+' </select> <label class="over_border_txt_proposal_add">Relation</label><div class="help-block with-errors"></div></div></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text" class="form-control-age ' + res[i].id + '_disabled alphabates_only "  placeholder="Enter First Name" name ="display[' + res[i].id + '][first_name]" value = "' + fname + '" required><label class="over_border_txt_proposal_add">First Name<span style="color:#FF0000">*</span></label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text" class="form-control-age ' + res[i].id + '_disabled alphabates_only "name="display[' + res[i].id + '][last_name]" value ="' + lname + '"  placeholder="Enter Last Name" required><label class="over_border_txt_proposal_add ">Last Name<span style="color:#FF0000">*</span></label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text" inputmode="none" class="bdateInsuredmember datepicker1 form-control-age ' + res[i].id + '" kids = "1" onkeydown=" return false;" onclick="changeInuseredbdate(this)" autocomplete="off" name = "display[' + res[i].id + '][dob]" id ="'+res[i].id+'_id" maxage="'+maxage+'" '+year_month+'  value = "' + dob + '" placeholder="dd-mm-yyyy" required readonly><label class="over_border_txt_proposal_add">DOB<span style="color:#FF0000">*</span></label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled" id ="' + res[i].id + '_gender" name = "display[' + res[i].id + '][gender]" required="required" data-error="Valid email is required." ' + readonly + '><option value="">- Select -</option> <option value="Male">Male</option> <option value="Female">Female</option></select><label class="over_border_txt_proposal_add">Gender<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div> <div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled ' + res[i].id + '_marital_status" id ="' + res[i].id + '_marital_status" name = "display[' + res[i].id + '][marital_status]" required="required" data-error="This field is required."><option value="">- Select -</option>'+marital_dropdown+'</select><label class="over_border_txt_proposal_add">Marital Status<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div></div> <!-- /.row -->';


                                $('.get_title').append(data);
                            }
                            else{
                                sel_rel = res[i].id;
                                is_array =   Array.isArray(res[i].id);
                                var kids = 'kids';
                                if(is_array) {

                                    var maxage =res[i].member_age;
                                    res[i].id = "kid"+kid_no;
                                    
                                    data= '<div class="panel"><div class="panel-heading"><h6 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse' + i + '">' + res[i].id + ' <i class="more-less fa ' + clsfa + '"></i> </a><input type ="hidden" name = display[' + res[i].id + '][member_ages_id] value ="' + res[i].member_ages_id + '"></h6></div>';

                                    data += '<div id="collapse' + i + '" class="panel-collapse collapse"><div class="panel-body"><div class="signUp-page signUp-minimal"><div class="signin-form-wrapper pad_proposal_s"><div class="row margin_top_40_proposal_insured"><div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled ' + res[i].id + '_salutation" id ="' + res[i].id + '_salutation" name = "display[' + res[i].id + '][salutation]" required="required" data-error="This field is required."><option value="">- Select -</option><option value="Ms">Ms</option><option value="Master">Master</option></select><label class="over_border_txt_proposal_add">Salutation<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div> <div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled color_text  sel_'+res[i].id+' kidchange"  name ="display[' + res[i].id + '][rel]"  required="required" data-error="Valid email is required." id="sel_'+res[i].id+'">'+sel_option+' </select> <label class="over_border_txt_proposal_add">Relation</label><div class="help-block with-errors"></div></div></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text" class="form-control-age ' + res[i].id + '_disabled alphabates_only " placeholder="Enter First Name" name ="display[' + res[i].id + '][first_name]" value = "' + fname + '" required><label class="over_border_txt_proposal_add">First Name<span style="color:#FF0000">*</span></label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text" class="form-control-age ' + res[i].id + '_disabled alphabates_only "name="display[' + res[i].id + '][last_name]" value ="' + lname + '" placeholder="Enter Last Name" required><label class="over_border_txt_proposal_add ">Last Name<span style="color:#FF0000">*</span></label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text" inputmode="none" id ="'+res[i].id+'_id" class="bdateInsuredmember datepicker1  form-control-age ' + res[i].id + '_disabled" kids = "kids" onkeydown=" return false;" onclick="changeInuseredbdate(this)" name = "display[' + res[i].id + '][dob]" value = "' + dob + '" placeholder="dd-mm-yyyy" autocomplete="off" required readonly><label class="over_border_txt_proposal_add">DOB<span style="color:#FF0000">*</span></label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled" id ="' + res[i].id + '_gender" name = "display[' + res[i].id + '][gender]" required="required" data-error="Valid email is required." ' + readonly + '><option value="">- Select -</option> <option value="Male" selected="selected">Male</option> <option value="Female">Female</option></select><label class="over_border_txt_proposal_add">Gender<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div> <!-- /.col- --><div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled ' + res[i].id + '_marital_status" id ="' + res[i].id + '_marital_status" name = "display[' + res[i].id + '][marital_status]" required="required" data-error="This field is required."><option value="">- Select -</option><option value="Single">Single</option><option value="Married">Married</option><option value="Divorced">Divorced</option><option value="Widowed">Widowed</option></select><label class="over_border_txt_proposal_add">Marital Status<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div></div> <!-- /.row -->';




                                    $('.get_title').append(data);

                                }else{
                                    var maxage = res[i].member_age;
                                    
                                    var gen_option  = '<option value="Male" selected="selected">Male</option> <option value="Female">Female</option>';
                                    var salutation_option  = '<option value="Ms">Ms</option><option value="Master">Master</option>';
                                    sel_option='<option value="' + res[i].id + '" selected="selected">' + res[i].member_type + '</option>';
                                    if(pf!=''){
                                        res[i].id = res[i].member_type+i;
                                        sel_rel = res[i].id;
                                        if(res[i].member_type=='Son'){
                                             gen_option  = '<option value="Male" selected="selected">Male</option>';
                                             salutation_option  = '<option value="Master" selected="selected">Master</option>';
                                        }else{
                                            gen_option=' <option value="Female"  selected="selected">Female</option>';
                                            salutation_option='<option value="Ms" selected>Ms</option>';
                                        }
                                    }
                                    data = '<div class="panel"><div class="panel-heading"><h6 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse' + i + '">' + res[i].member_type + ' <i class="more-less fa ' + clsfa + '"></i> </a><input type ="hidden" name = display[' + res[i].id + '][member_ages_id] value ="' + res[i].member_ages_id + '"></h6></div>';

                                    data += '<div id="collapse' + i + '" class="panel-collapse collapse"><div class="panel-body"><div class="signUp-page signUp-minimal"><div class="signin-form-wrapper pad_proposal_s"><div class="row margin_top_40_proposal_insured"><div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled ' + res[i].id + '_salutation" id ="' + res[i].id + '_salutation" name = "display[' + res[i].id + '][salutation]" required="required" data-error="This field is required."><option value="">- Select -</option>'+salutation_option+'</select><label class="over_border_txt_proposal_add">Salutation<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div> <div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled color_text  sel_'+res[i].id+' kidchange"  name ="display[' + res[i].id + '][rel]"  required="required" data-error="Valid email is required." id="'+sel_rel+'">'+sel_option+' </select> <label class="over_border_txt_proposal_add">Relation</label><div class="help-block with-errors"></div></div></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text" class="form-control-age ' + res[i].id + '_disabled alphabates_only " placeholder="Enter First Name" name ="display[' + res[i].id + '][first_name]" value = "' + fname + '" required><label class="over_border_txt_proposal_add">First Name<span style="color:#FF0000">*</span></label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text" class="form-control-age ' + res[i].id + '_disabled alphabates_only "name="display[' + res[i].id + '][last_name]" value ="' + lname + '" placeholder="Enter Last Name" required><label class="over_border_txt_proposal_add ">Last Name</label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-12 col-lg-4 col-md-6"><div class="form-group mb_15_point dropdown_product_m_t_b_p"><input type="text"  inputmode="none" class="bdateInsuredmember datepicker1 form-control-age ' + res[i].id + '_disabled" '+year_month+'  onkeydown=" return false;" autocomplete="off" onclick="changeInuseredbdate(this)" id ="'+res[i].id+'_id" maxage="'+maxage+'"  name = "display[' + res[i].id + '][dob]" value = "' + dob + '" placeholder="dd-mm-yyyy" required readonly><label class="over_border_txt_proposal_add">DOB<span style="color:#FF0000">*</span></label></div> <!-- /.input-group --></div> <!-- /.col- --><div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled" id ="' + sel_rel + '_gender" name = "display[' + res[i].id + '][gender]" required="required" data-error="Valid email is required." ' + readonly + '><option value="">- Select -</option> '+gen_option+'</select><label class="over_border_txt_proposal_add">Gender<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div> <!-- /.col- --><div class="col-lg-4 col-md-6 col-12"><div class="form-group dropdown_product_m_t_b_p"><select class="form-control-age ' + res[i].id + '_disabled ' + res[i].id + '_marital_status" id ="' + res[i].id + '_marital_status" name = "display[' + res[i].id + '][marital_status]" required="required" data-error="This field is required."><option value="">- Select -</option><option value="Single">Single</option><option value="Married">Married</option><option value="Divorced">Divorced</option><option value="Widowed">Widowed</option></select><label class="over_border_txt_proposal_add">Marital Status<span style="color:#FF0000">*</span></label><div class="help-block with-errors"></div></div></div></div> <!-- /.row -->';





                                    $('.get_title').append(data);
                                }
                                var sal_id = '#'+ res[i].id;
                                var mar_id = '#'+ res[i].id;
                                if(res[i].id=='5' || res[i].id=='6'){

                                    sal_id = '.'+ res[i].id;
                                    mar_id = '.'+ res[i].id;
                                }
                                if(pf==''){

                                    if (salutation!= '' && gender == 'Female') {
                                        $(sal_id +"_salutation option[value='Master']").hide();
                                        $(sal_id +"_salutation option[value='Ms']").show();
                                    }
                                    else{
                                        $(sal_id +"_salutation option[value='Master']").show();
                                        $(sal_id +"_salutation option[value='Ms']").hide();
                                    }

                                }
                                
                            }
                            if(res[i].is_adult == 'N' && res[i].id == 'kid'+kid_no){


                                $('.sel_'+res[i].id).html('');
                                // res[i].id = "kid"+i;
                                for ( j = 0; j < sel_rel.length; j++) {
                                    sel_option = '<option value="' + sel_rel[j]['member'].member_id + '" >' + sel_rel[j]['member'].construct + '</option>';
                                    $('.sel_'+res[i].id).append(sel_option);
                                }
                                if(res[i].member_type == 'Son'){
                                    $('.sel_'+res[i].id).val('5');
                                }if(res[i].member_type == 'Daughter'){
                                    $('.sel_'+res[i].id).val('6');
                                }
                                kid_no = kid_no+1;
                                //sel_option='<option value="' + res[i].id + '" selected="selected">' + res[i].member_type + '</option>';
                            }
                            
                            var sal_id = '#'+ res[i].id;
                            var mar_id = '#'+ res[i].id;
                            if(res[i].id=='5' || res[i].id=='6'){
                                sal_id = '.'+ res[i].id;
                                mar_id = '.'+ res[i].id;
                            }
                            if (marital_status!= '') {
                                $(mar_id +"_marital_status").val(marital_status)

                            }

                            if (salutation!= '') {
                                $(sal_id +"_salutation").val(salutation)

                            }
                            $("#" + res[i].id + "_gender option[value='" + gender + "']").prop('selected', true);
                            
                            $('.kidchange').trigger('change')


                            if (res[i].id == '1') {

                                $(mar_id +"_marital_status").val($('#marital_status').val())
                                $("." + res[i].id + "_disabled").css('pointer-events', 'none');

                            }
                           


                            var prod_gender = '<?php echo $plan_data['gender'];?>';
                            if(prod_gender=='M'){
                                $(sal_id +"_salutation option[value='Mrs']").hide();
                                $(sal_id +"_salutation option[value='Ms']").hide();
                                $(sal_id +"_salutation option[value='Mr']").show();

                                $("#" + res[i].id + "_gender").val('Male');
                            }else if(prod_gender=='F'){

                                $(sal_id +"_salutation option[value='Mr']").hide();
                                $(sal_id +"_salutation option[value='Mrs']").show();
                                $(sal_id +"_salutation option[value='Ms']").show();
                                $("#" + res[i].id + "_gender").val('Female');
                                $("#" + res[i].id + "_gender").css('pointer-events', 'none');
                            }else{

                                var gender_data = $("#1_gender").val();


                                if (gender_data == 'Male') {
                                    $("#2_gender").val('Female');
                                    $("#2_salutation option[value='Mrs']").show();
                                    $("#2_salutation option[value='Mr']").hide();
                                   
                                } else {
                                    $("#2_gender").val('Male');
                                    $("#2_salutation option[value='Mrs']").hide();
                                    $("#2_salutation option[value='Ms']").hide();
                                    $("#2_salutation option[value='Mr']").show();
                                }
                                if(proposal_gender=='Male' && res[i].member_type=='Spouse'){
                                    $("#" + res[i].id + "_gender").val('Female');
                                }if(proposal_gender=='Female' && res[i].member_type=='Spouse'){
                                    $("#" + res[i].id + "_gender").val('Male');
                                }

                            }
                            $("#2_gender").css('pointer-events', 'none');

                            
                            

                            $("#collapse0").addClass('show');
                            var dob_proposer = maxage ;
                            var input = res[i].id+'_id' ;
                            var kids = kids;


                            //  alert(input);
                            let maxDate = '';
                            let minDates = '';
                            let mindate = '';
                            var month;
                            if (dob_proposer != '' && dob_proposer != undefined) {
                                let today = new Date().toISOString().split('T')[0];
                                let date3m = new Date();
                                var date = date3m.getDate()+1;
                                month = date3m.getMonth()-1;
                                date3m.setFullYear(date3m.getFullYear() - dob_proposer);

                                minDates = date3m.getFullYear()-1;


                                // date3m = date3m.toISOString().split('T')[0];
                                maxDate = '-' + dob_proposer + 'y -1d';
                                mindate = new Date(minDates, month + 1, date) ;

                            } else {

                                if(kids !='1') {

                                    let datekids = new Date();
                                    maxDate = 0;

                                    minDates = '1940';
                                    mindate = new Date(minDates, 1, -1, 1);
                                }else {


                                    maxDate = '-18y -1d';

                                    minDates = '1940';
                                    mindate = new Date(minDates, 1, -1, 1);
                                }
                            }
                            $("#"+input).datepicker({
                                beforeShow: function(i) {
                                  if ($(this).prop('readonly')) {
                                    e.preventDefault();
                                    e.stopImmediatePropagation();
                                    return false;
                                    }
                                },
                                dateFormat: 'dd-mm-yy',
                                prevText: '<i class="fa fa-angle-left"></i>',
                                nextText: '<i class="fa fa-angle-right"></i>',
                                changeMonth: true,
                                changeYear: true,
                                yearRange: "-100:" + new Date('Y'),
                                minDate: mindate,

                                maxDate: maxDate

                            });

                        }
                    }



                }
            });


        }


        $(document).on("change", ".kidchange", function() {
            debugger;
           var id = $(this).attr('id');
           var str='';
            if(id.indexOf('kid') != -1){
               str='kid';
            }
           var relation  = $('#'+id +' option:selected').text().trim();
           id=id.replace ( /[^\d.]/g, '' );
           id=str+id;
           if(relation == 'Daughter'){
             $("#" + id + "_gender").val('Female');
             $("#" + id + "_gender").css('pointer-events', 'none');
             $('.' + id +"_salutation option[value='Master']").hide();
             $('.' + id +"_salutation option[value='Ms']").show();
             $('.' + id +"_salutation").val('Ms');



           }if(relation == 'Son'){
             $("#" + id + "_gender").val('Male');
             $("#" + id + "_gender").css('pointer-events', 'none');
                $('.' + id +"_salutation option[value='Master']").show();
                $('.' + id +"_salutation option[value='Ms']").hide();
                $('.' + id +"_salutation").val('Master');
           }

           
        })

        $(".insured_det").click(function() {
            get_member_insure();

        });
        var prod_gender = '<?php echo $plan_data['gender'];?>';
        
        $(document).ready(function() {
            get_member_insure();
            fetchPremium();
            fetch_additional_plans();
            $('#gender').trigger('change');

        });

        var prod_gender = '<?php echo $plan_data['gender'];?>';


        /*$(document).on("change", ".kidchange", function() {
           var id = $(this).attr('id');
           var relation  = $('#'+id +' option:selected').text().trim();
           id=id.replace ( /[^\d.]/g, '' );
           if(relation == 'Daughter'){
             $("#" + id + "_gender").val('Female');
             $("#" + id + "_gender").css('pointer-events', 'none');
           }if(relation == 'Son'){
             $("#" + id + "_gender").val('Male');
             $("#" + id + "_gender").css('pointer-events', 'none');
           }


        })*/

        function fetchPremium() {
            var plan_name = '<?php echo $post_data["plan_name"];?>';
            $.ajax({
                url: "/quotes/fetchPremium",
                type: "POST",
                async: false,
                data: {},
                dataType: "json",
                success: function(response) {
                    if(plan_name !== ''){
                        $('.cover_plan_name').html(plan_name);
                    }else{
                        $('.cover_plan_name').html(response.creaditor_name + " - " + response.plan_name);
                    }

                    //  $('.cover_plan_name').html(response.creaditor_name + " - " + response.plan_name);
                    $('.cover_amt').html('<i class="fa fa-inr"></i>' + response.sum_insured);
                    $('.cover_premium').html('<i class="fa fa-inr"></i>' + response.total_premium);
                   // $('.base_premium').html('<i class="fa fa-inr"></i>' + response.total_premium);
                    // $('.base_premium').html('<i class="fa fa-inr"></i>' + response.premium_rate);
                    $('.cover_tenure').html('<i class=""></i> ' + response.duration + ' Year');
                   
                    $('#hiddenpremium').val(response.total_premium);
                }
            });
        }


        /*$('.SubmitProposal').click(function(){

        var get_policy_id = $('#hiddenpolicyid').val();
        var get_premium = $('#hiddenpremium').val();

        data = {};
                    data.policy_id = get_policy_id;
                    data.premium = get_premium;

            $.ajax({
                                         url: "/quotes/create_proposal",
                                         type: "POST",
                                         async: false,
                                         data:data,
                                         dataType: "json",
                                         success: function (response)
                                         {




                                         }
                                 });

        });*/

        $('.nominee_rel').change(function() {

            var nominee_rel = $('.nominee_rel').val();


            data = {};
            data.nominee_rel = nominee_rel;


            $.ajax({
                url: "/quotes/get_family_data_exist",
                type: "POST",
                async: false,
                data: data,
                dataType: "json",
                success: function(response) {
                    debugger;
                    var res = JSON.parse(response.data);
                    var member_name;
                    if (res != '') {
                        // alert('here');
                        for (i = 0; i < res.length; i++) {
                            console.log(res[i]);
                            member_name = res[i].policy_member_first_name + " " + res[i].policy_member_last_name;
                            $('.nominee_fname').val(member_name);
                            $('.nominee_dob').val(res[i].policy_member_dob);

                        }
                    } else {
                        // alert();
                        $('.nominee_fname').val('');
                        $('.nominee_dob').val('');
                        $('.nominee_contact_number').val('');

                    }

                }


            });


        });


        $(document).ready(function() {
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                    results = regex.exec(location.search);
                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
            }
            setTimeout(function() {
                var qsp = 'view',
                    para = getUrlParameter(qsp);

                if (para == 'idetails') {

                    $('.idetails').trigger("click");
                } else if (para == 'ndetails') {
                    $('.ndetails').trigger("click");
                }
            }, 1000);

            $(document).on("click", ".mem_back", function() {
                $('.pdetails').trigger("click");
            })

            $(document).on("click", ".nom_back", function() {
                $('.idetails').trigger("click");
            })
        });

        function fetch_additional_plans() {
            $.ajax({
                url: "/quotes/fetch_additional_plans",
                type: "POST",
                async: false,
                dataType: "json",
                success: function(response) {

                    if (response.status == 'Success') {
                        $('.total_premium_btn_proposal_form').prepend(response.additional_plans);
                    } else {
                        $('.total_premium_btn_proposal_form').prepend("<br>");
                    }
                }
            });

        }
        //proposer_dob
        $("#proposer_dob").change(function(){
           /* var is_proposer_insured=$('#is_proposer_insured').val();
            age=[];
            if(is_proposer_insured == 'Yes'){
                var dob=$("#proposer_dob").val();

                dob = new Date(dob);
                var today = new Date();
                var a=Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));

                age.push(Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000)));

                //if insured memebr alredy done
                var inputs = $(".bdateInsuredmember");

                for(var i = 0; i < inputs.length; i++){
                    dob=$(inputs[i]).val();
                    dob = new Date(dob);
                    var today = new Date();
                    age.push(Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000)));
                }
                age_val= Math.max.apply(Math,age);
                getNewPremium(age_val);
            }else{
                return;
            }*/



        });
        function changeInuseredbdate(e1){
debugger;
            if(e1.attributes['maxage']) {
                var dob_proposer = e1.attributes['maxage'].value;
            }

            var kids = e1.attributes['kids'];
            var input = e1.attributes['id'].value ;
          //  alert(input);
            let maxDate = '';
            let minDates = '';
            let mindate = '';
            var month;
            if (dob_proposer != '' && dob_proposer != undefined) {
                let today = new Date().toISOString().split('T')[0];
                let date3m = new Date();
                var date = date3m.getDate()+1;
                month = date3m.getMonth()-1;
                date3m.setFullYear(date3m.getFullYear() - dob_proposer);

                minDates = date3m.getFullYear()-1;


                // date3m = date3m.toISOString().split('T')[0];
                maxDate = '-' + dob_proposer + 'y -1d';
                mindate = new Date(minDates, month + 1, date) ;

            } else {


if(kids !=''){

    let datekids = new Date();
    maxDate = datekids;

    minDates = '1940';
    mindate = new Date(minDates,1,-1,1);

}else{
    maxDate = '-18y -1d';

    minDates = '1940';
    mindate = new Date(minDates,1,-1,1);
}


            }
            $("#"+input).datepicker({
                beforeShow: function(i) {
                  if ($(this).prop('readonly')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                    }
                },
                dateFormat: 'dd-mm-yy',
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:" + new Date('Y'),
                minDate: mindate,

                maxDate: maxDate

            });
           /* age=[];
           for(var i = 0; i < inputs.length; i++){
               dob=$(inputs[i]).val();
               dob = new Date(dob);
               var today = new Date();
                age.push(Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000)));
           }
           age_val= Math.max.apply(Math,age);*/
           
        }

        $(".submit_insure_data").click(function() {

             var nominee_man ='<?php echo $plan_data["nominee_mandatory"] ?>';

            $('#submit_insure_data').validate({messages: {
                     declare:{required:"Please accept Terms & conditions."},
                },errorPlacement: function(label, element) {
                    if (element.is("select") || element.attr("type") == "date") {
                      
                       element.removeClass('error');
                      
                    } if (element.attr("id") == "declare") {

                        label.insertAfter(element.parents().find('#termscondition div'))

                    }else{
                        label.insertAfter(element.next('label'));
                    } 
                    
                  },});

                if($('#submit_insure_data').valid() ){

             var payment_page = '<?php echo $plan_data["payment_page"] ?>';
            var payment_first = '<?php echo ($plan_data["payment_first"])?'1':''; ?>';
            
                var inputs = $(".bdateInsuredmember");

            age=[];
            var cnt=0;
            /*for(var i = 0; i < inputs.length; i++){
                dob=$(inputs[i]).val();
                if(dob!='' && dob != '0000-00-00'){
                    var date_components = dob.split("-");
                    var day = date_components[0];
                    var month = date_components[1];
                    var year = date_components[2];  
                    dob = month+'-'+day+'-'+year;       
                    dob = new Date(dob);
                 
                    var today = new Date();

                
                    age.push(Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000)));
                    cnt++;
                }
                
                
            }
            //age_val= Math.max.apply(Math,age);
            const newArray = age.filter(function (value) {
                return !Number.isNaN(value);
            });

            age_val = Math.max.apply(Math, newArray);
            
            if(age.length !== 0 && age.length == cnt  && payment_first!='1'){
                getNewPremium(age_val).then(e => {
                    if(e !== 'false') {
                     //   var resp = swal("Alert", "As per entered DOB, age and Premium are updated", "warning");
                        swal({
                            title: 'Alert',
                            text: "As per entered DOB, age and Premium are updated",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok'
                        }).then(function() {
                            var form = $("#submit_insure_data").serialize();
                            $.post("/quotes/Submitinsure_data", form, function (e) {
                                var response = JSON.parse(e);
                                if (response.status == 'success') {
                                    swal("success", "Insured details updated successfully !", "success");
                                    setTimeout(function() {
                                        if(payment_first==1 && payment_page==3){
                                            window.location.href = '/quotes/redirect_to_pg?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>';
                                        }else{
                                            if(nominee_man == '1') {
                                                $(".nomineee_det").trigger("click");
                                            }else {
                                                submit_nominee();
                                            }
                                        }
                                    }, 2000);
                                    
                                } else {
                                    swal("Alert", response.message, "warning");
                                }


                            });
                        })

                    }else{
                        var form = $("#submit_insure_data").serialize();
                        $.post("/quotes/Submitinsure_data", form, function(e) {
                            var response = JSON.parse(e);
                            if (response.status == 'success') {
                                swal("success", "Insured details updated successfully !", "success");
                                setTimeout(function() {
                                    if(nominee_man == '1') {
                                        $(".nomineee_det").trigger("click");
                                    }else {
                                        submit_nominee();
                                    }
                                }, 2000);
                                
                            } else {
                                swal("Alert", response.message, "warning");
                            }


                        });
                    }

                });

            }else{*/
                var form = $("#submit_insure_data").serialize();
                $.post("/quotes/Submitinsure_data", form, function(e) {
                    var response = JSON.parse(e);
                    if (response.status == 'success') {
                        swal("success", "Insured details updated successfully !", "success");
                        setTimeout(function() {
                            if(payment_first==1 && payment_page==3){
                                window.location.href = '/quotes/redirect_to_pg?lead_id=<?php echo str_replace('#','',$_REQUEST['lead_id']);?>';
                            }else{
                                if(nominee_man == '1') {
                                    $(".nomineee_det").trigger("click");
                                }else {
                                   submit_nominee();
                                }
                            }
                        }, 2000);
                        
                    } else {
                        swal("Alert", response.message, "warning");
                    }


                });
            //}

            }




        });
        function getNewPremium(age) {
            debugger;
         //   $('#preloader').show();
            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: "/quotes/getPremiumNew",
                    type: "POST",
                    async: false,
                    data:{age},
                    dataType: "json",
                    success: function (response) {

                        var res = JSON.parse(response.data);
                        
                        if (res.status == 200) {
                            //As per entered DOB, age and Premium are updated
                            $('.cover_premium').text(res.total_premium);
                            for (i = 0; i < res.policy_det.length; i++) {
                                //    $('.cover_det').html('');
                                policy_id = res.policy_det[i]['policy_id'];
                                
                                $('.base_premium').text(res.policy_det[i]['premium']);
                            }
                            resolve("As per entered DOB, age and Premium are updated");
                            //  swal("Alert", "As per entered DOB, age and Premium are updated", "warning");

                            //  return;
                            // $('.sk-circle-wrapper').hide();
                        }else{
                            resolve("false");
                        }
                        /*if (res.status == 200) {


                        } else {
                            swal("Alert", res.msg, "warning");
                            return;
                        }*/
                    }


                });
            });

        }

        $(".memProposerBtn").click(function () { 
            $('#marital_status').removeClass('error');
        }); 

        $(document).ready(function() {

            $(".bdateInsuredmember").keydown(function (e) {

                return false;
            });

            function ajaxindicatorstart(text) {
                text = typeof text !== "undefined" ? text : "Please wait....";

                var res = "";

                if ($("body").find("#resultLoading").attr("id") != "resultLoading") {
                    res += "<div id='resultLoading' style='display: none'>";
                    res += "<div id='resultcontent'>";
                    res += "<div id='ajaxloader' class='txt'>";
                    res +=
                        '<svg class="lds-curve-bars" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g transform="translate(50,50)"><circle cx="0" cy="0" r="8.333333333333334" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="26.179938779914945 26.179938779914945" transform="rotate(2.72337)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="0" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="16.666666666666668" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="52.35987755982989 52.35987755982989" transform="rotate(64.7343)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.2" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="25" fill="none" stroke="#ffffff" stroke-width="4" stroke-dasharray="78.53981633974483 78.53981633974483" transform="rotate(150.07)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.4" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="33.333333333333336" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="104.71975511965978 104.71975511965978" transform="rotate(239.433)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.6" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="41.666666666666664" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="130.89969389957471 130.89969389957471" transform="rotate(320.34)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.8" repeatCount="indefinite"></animateTransform></circle></g></svg>';
                    res += "<br/>";
                    res += "<span id='loadingMsg'></span>";
                    res += "</div>";
                    res += "</div>";
                    res += "</div>";

                    $("body").append(res);
                }

                $("#loadingMsg").html(text);

                $("#resultLoading").find("#resultcontent > #ajaxloader").css({
                    position: "absolute",
                    width: "500px",
                    height: "75px",
                });

                $("#resultLoading").css({
                    width: "100%",
                    height: "100%",
                    position: "fixed",
                    "z-index": "10000000",
                    top: "0",
                    left: "0",
                    right: "0",
                    bottom: "0",
                    margin: "auto",
                });

                $("#resultLoading").find("#resultcontent").css({
                    background: "#ffffff",
                    opacity: "0.7",
                    width: "100%",
                    height: "100%",
                    "text-align": "center",
                    "vertical-align": "middle",
                    position: "fixed",
                    top: "0",
                    left: "0",
                    right: "0",
                    bottom: "0",
                    margin: "auto",
                    "font-size": "16px",
                    "z-index": "10",
                    color: "#000000",
                });

                $("#resultLoading").find(".txt").css({
                    position: "absolute",
                    top: "-25%",
                    bottom: "0",
                    left: "0",
                    right: "0",
                    margin: "auto",
                });

                $("#resultLoading").fadeIn(300);

                $("body").css("cursor", "wait");
            }

            function ajaxindicatorstop() {
                $("#resultLoading").fadeOut(300);

                $("body").css("cursor", "default");
            }
        });
        function changeInuseredbdate(e1){
            debugger;
            if(e1.attributes['maxage']) {
                var dob_proposer = e1.attributes['maxage'].value;
            }
            var kids = e1.attributes['kids'].value;


            var input = e1.attributes['id'].value ;
            var kids = e1.attributes['kids'];

            //  alert(input);
            let maxDate = '';
            let minDates = '';
            let mindate = '';
            var month;
            if (dob_proposer != '' && dob_proposer != undefined) {
                let today = new Date().toISOString().split('T')[0];
                let date3m = new Date();
                var date = date3m.getDate()+1;
                month = date3m.getMonth()-1;
                date3m.setFullYear(date3m.getFullYear() - dob_proposer);

                minDates = date3m.getFullYear()-1;


                // date3m = date3m.toISOString().split('T')[0];
                maxDate = '-' + dob_proposer + 'y -1d';
                mindate = new Date(minDates, month + 1, date) ;
                $("#"+input).datepicker({
                    beforeShow: function(i) {
                      if ($(this).prop('readonly')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                        }
                    },
                    dateFormat: 'dd-mm-yy',
                    prevText: '<i class="fa fa-angle-left"></i>',
                    nextText: '<i class="fa fa-angle-right"></i>',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-100:" + new Date('Y'),
                    minDate: mindate,

                    maxDate: maxDate

                });
            } else {



                if(kids !='1'){

                    let datekids = new Date();
                    maxDate = 0;

                    minDates = '1940';
                    mindate = new Date(minDates,1,-1,1);
                    $("#"+input).datepicker({
                        beforeShow: function(i) {
                          if ($(this).prop('readonly')) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            return false;
                            }
                        },
                        dateFormat: 'dd-mm-yy',
                        prevText: '<i class="fa fa-angle-left"></i>',
                        nextText: '<i class="fa fa-angle-right"></i>',
                        changeMonth: true,
                        changeYear: true,


                    });
                }else{
                    maxDate = '-18y -1d';

                    minDates = '1940';
                    mindate = new Date(minDates,1,-1,1);
                    $("#"+input).datepicker({
                        beforeShow: function(i) {
                          if ($(this).prop('readonly')) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            return false;
                            }
                        },
                        dateFormat: 'dd-mm-yy',
                        prevText: '<i class="fa fa-angle-left"></i>',
                        nextText: '<i class="fa fa-angle-right"></i>',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-100:" + new Date('Y'),
                        minDate: mindate,

                        maxDate: maxDate

                    });
                }

            }

            /* age=[];
            for(var i = 0; i < inputs.length; i++){
                dob=$(inputs[i]).val();
                dob = new Date(dob);
                var today = new Date();
                 age.push(Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000)));
            }
            age_val= Math.max.apply(Math,age);*/

        }

    </script>





</div> <!-- /.main-page-wrapper -->
</body>
<script>
    function toggleIcon(e) {
        $(e.target)
            .prev('.panel-heading')
            .find(".more-less")
            .toggleClass('fa-plus fa-minus');
    }
    $('.panel-group').on('hidden.bs.collapse', toggleIcon);
    $('.panel-group').on('shown.bs.collapse', toggleIcon);

    document.title = 'Proposal Details';
</script>
