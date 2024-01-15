<style>
    .agreement-checkbox input[type="checkbox"]:checked+label::before {
        content: "✓";
        font-family: font-awesome;
        color: rgb(255, 255, 255);
        background: rgb(199, 34, 41);
        border-color: rgb(199, 34, 41);
    }

    .feature-offer-box_basic:hover {
        background: none !important;
    }

    .txt-features {
        font-family: 'PFEncoreSansPro-book';
        font-weight: 500;
        color: black;
        line-height: 18px;
        letter-spacing: 0.3px;
    }

    @media only screen and (max-device-width : 480px) {
        .mdl-0 {
            margin-top: 0px !important;
        }

        .ds-nne {
            display: none;
        }

        .theme-tab-basic.theme-tab .tabs-menu li.z-active a:before {
            border: 3px solid #107591;
            position: absolute;
            bottom: -15%;
        }

        .pd-00 {
            padding: 0px;
        }

        .nav-pills .nav-link.claim.active {
            position: relative;
            padding: 6px;
            font-size: 9px;
            top: 15px;
            width: 38%;
            font-weight: 500 !important;
        }

        .step2-nw {
            margin-top: -26px;
            font-size: 10px;
            width: 38%;
            font-weight: 500 !important;
            position: relative;
            top: 19%;
            left: 39%;
        }

        .step3-nw {
            margin-top: -43px;
            font-size: 10px;
            width: 38%;
            font-weight: 500 !important;
            position: relative;
            top: 19%;
            left: 81%;
        }

    }
</style>

<?php
//echo "<pre>";
//print_r($group_by_policies);
//print_r($sum_insured_arr);exit;

function getIndianCurrency(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );
    $digits = array('', 'Hundred', 'Thousand', 'Lac', 'Crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;

            //$str [] = ($number < 21) ? $number.' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            $str[] = ($number < 21) ? $number . ' ' . $digits[$counter] . $plural . ' ' . $hundred : trim(floor($number / 10) * 10) . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees  : '') . $paise;
}

?>
<style>
    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        background: transparent;
        color: #107591;
    }

    .z-tabs>.z-container>.z-content>.z-content-inner {
        height: 500px;
    }

    .our-feature-app .text-wrapper ul li:before {
        content: "✔";
    }

    .section {
        font-family: 'PFEncoreSansPro-book';
    }

    .z-tabs.horizontal>ul>li {
        width: 26.3333% !important;
    }

    .p_plan_title_customize,
    .plan_table_add_modal_p {
        padding: 2px 20px
    }

    .p_compare_price_modal {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
        background-color: #fff4f5 !important;
        font-size: 23px;
        border: 1px dashed #da8089;
        font-weight: 900;
        color: rgb(199, 34, 42);
        padding: 10px 28px;
        border-radius: 0.25em;
        cursor: pointer;
        background-color: var(--light-pink);
        margin: 0px 3px;
        width: 137px;
        min-width: fit-content;
    }

    .a:hover {
        color: #107591 !important;
        text-decoration: underline;
    }

    @media only screen and (max-device-width : 480px) {
        .z-tab {
            width: 32.3333% !important;
        }

        .theme-tab-basic.theme-tab .tabs-menu li:first-child a,
        .theme-tab-basic.theme-tab .tabs-menu li a {
            width: 100%;
        }

        .tab_modal_product_d {
            margin-left: -1% !important;
            box-shadow: none !important;
            border: none !important;
        }

        .dis-bck {
            margin-left: 21% !important;
        }

        .mr6 {
            margin: 6px;
        }

        .plan_a_t_claim :after {
            display: none;
        }

        .margin_col_a_t_claim {
            margin-top: -61%;
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

    <div class="shrt-menu shrt-menu-one light-bg text-dark" style="background: #fff !important;">



        <!--
        =============================================
            Theme Solid Inner Banner
        ==============================================
        -->


        <!-- large modal -->

        <div class="modal_right_pad mdl-0">

            <div class="modal-dialog modal-lg mt-0 mb-0" style="max-width: 100%;">
                <div class="modal-content background_transparent">
                    <div class="modal-header bg_red hd-g">

                        <h5 class="modal-title ds-nne"><img src="<?php echo ($plan_details[0]['creditor_logo'] != "") ? $plan_details[0]['creditor_logo'] : base_url() . "assets/images/ad-logo.png"; ?>" class="plan_details_ic"></h5>
                        <div>
                            <p class="care_popup_title ds-nne"><?php echo $plan_details[0]['plan_name'] . ' - ' . $plan_details[0]['creditor_name']; ?></p>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 col-lg-7 margin_l_modal_product col-12 dis-none">
                                    <div class="row">
                                        <div class="col-md-4 col-4">
                                            <h6 class="color_white_font border_right_effect font_22">
                                                <span class="font_20 color_fixed_title">Cover </span>
                                                <br><span class="color_white_font font_20"><b><i class="fa fa-inr"></i> <?php echo getIndianCurrency($_REQUEST['cover_see']); ?> / per year</b></span>
                                            </h6>
                                        </div>
                                        <div class="col-md-4 col-5">
                                            <h6 class="color_white_font border_right_effect_2 font_22">
                                                <span class="font_20 color_fixed_title">Premium</span>
                                                <br><span class="color_white_font font_20  premium_details"><b><i class="fa fa-inr"></i> <?php echo $_REQUEST['total_premium_data']; ?> / per year</b></span>
                                            </h6>
                                        </div>
                                        <div class="col-md-4 col-4">
                                            <h6 class="color_white_font  font_22">
                                                <span class="font_20 color_fixed_title">Claim Settlement Ratio</span>
                                                <br><span class="color_white_font font_20"><b> 93%</b></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>


                                <!-- ----- end col-8 ----- -->

                                <div class="col-md-9 col-lg-4 bg_pink_f_f">

                                    <div class="row margin_27_footer_fix">
                                        <div class="col-md-6 col-6">
                                            <p class="color_black bg_premium_txt_btn_f_p_d">Total Premium</p>
                                            <p class="color_red font_22 nw-fnt total_premium_data"> <i class="fa fa-inr"></i> <?php echo $_REQUEST['total_premium_data']; ?></p>
                                        </div>
                                        <div class="col-md-6 text-center col-6">
                                            <button type="button" class="btn btn_preoceed_product_fix cart_continue1">Proceed to Buy <i class="fa fa-next"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <!-- -------col-md-4------ -->
                            </div>
                            <!-- ----------end row------- -->
                        </div>
                        <div>

                        </div>
                    </div>

                    <div class="modal-body background_transparent text-center p-lg pd-00">
                        <a href="/quotes"><button type="button" class="btn btn-white recom_close_css" style="z-index: 1;cursor: pointer !important;"><i class="fa fa-close"></i></button></a>
                        <div class="col-lg-12 m-auto mdl-nw" style="padding: 0px;">
                            <div class="dis-mb-only">
                                <div class="col-md-12" style="padding: 0px;">
                                    <p class="font_17 plan_right_member_e_c_proposal_form"><img class="contain img_right_panel_addon_add_proposal_form" src="/assets/images/ad-logo.png"> <span class="span_proposal_form_i cover_plan_name">Fedfina MSE LAP - GHI Combo</span>
                                    </p>
                                </div>
                                <div class="row mb-ttl" style="padding: 7px 0px; margin: 0px 4px;">
                                    <div class="col-lg-6 col-md-4 col-4">
                                        <p class="color_cover_p_r_b" style="font-size: 14px;">Cover Amount</p>
                                        <p class="p_c_r_b_p cover_amt" style="font-size: 15px;"><i class="fa fa-inr"></i>100000</p>
                                    </div>
                                    <div class="col-lg-6 col-md-4 col-4">
                                        <p class="color_cover_p_r_b" style="font-size: 14px;">Total Premium</p>
                                        <p class="p_c_r_b_p cover_premium" style="font-size: 15px;"><i class="fa fa-inr"></i>4881.00</p>
                                    </div>
                                    <div class="col-lg-6 col-md-4 col-4">
                                        <p class="color_cover_p_r_b" style="font-size: 14px;">Policy Tenure</p>
                                        <p class="p_c_r_b_p cover_tenure" style="font-size: 15px;"><i class=""></i> 1 Year</p>
                                    </div>
                                </div>
                            </div>
                            <!-- The value of data-role should be z-tabs, data-options is optional to set options -->
                            <div id="theme-tab-twlv" class="theme-tab-basic tab-dark theme-tab hover contained medium z-icons-dark z-shadows z-bordered z-tabs horizontal top top-left silver" data-role="z-tabs" data-options="{&quot;theme&quot;: &quot;silver&quot;, &quot;orientation&quot;: &quot;horizontal&quot;, &quot;animation&quot;: {&quot;duration&quot;: 400, &quot;effects&quot;: &quot;slideH&quot;}}">
                                <ul class="z-tabs-nav z-tabs-mobile z-state-closed" style="display: none;">
                                    <li><a class="z-link" style="text-align: left;"><span class="z-title">Graphics</span><span class="flaticon-setup drp-icon"></span></a></li>
                                </ul><i class="z-dropdown-arrow"></i>

                                <ul class="mdl-0 tab_modal_product_d tabs-menu clearfix z-tabs-nav z-tabs-desktop z-hide-menu">
                                    <li class="z-tab z-first ztab-11" data-link="tab1"><a class="z-link lk-1"><img src="/assets/images/plan_details.png" class="dis-bck"> <span class="ds-inline">Add-on Coverages </span></a></a></li>
                                    <!--  <li class="z-tab  ztab-11" data-link="tab3"><a class="z-link lk-1"><img src="/assets/images/cashless_p.png" /> Cashless Hospitals</a></li> -->
                                    <li class="z-tab  ztab-11 add_on_cover  z-active" data-link="tab2"><a class="z-link lk-1"><img src="/assets/images/add_on.png" />Plan Details </a></li>
                                    <li class="z-tab  ztab-11 z-last" data-link="tab3"><a class="z-link lk-1"><img src="/assets/images/claims_p.png" class="dis-bck" class="ds-inline"> <span> Claims Process </span> </a></a></li>
                                </ul>
                                <!-- Tab Navigation Menu -->

                                <!-- Content container -->

                                <!-- Themes -->
                                <div class="tab-container z-container">


                                    <!-- Design -->
                                    <!-- /.tab-container tab 1 -->
                                    <div class="z-content" style="position: relative; left: 0px;">
                                        <div class="tab_inner_product_detail z-content-inner">
                                            <div class="our-feature-app" id="feature">

                                                <div class="our-service-app">

                                                    <div class="main-content hide-pr show-pr">
                                                        <div class="row margin_important_row">



                                                            <hr class="hr_p_b_cliam_w">

                                                            <div class="modal-content">
                                                                <div class="modal-header bg_more_header_filters" style="border-bottom: 1px solid #fff;">
                                                                    <div class="product_title_p_bor_modal_filters">
                                                                        <h5 class="modal-title modal_title_margin text-center">Hey User, Take a minute and review your
                                                                            cart before you proceed</h5>
                                                                    </div>

                                                                    <!-- <button type="button" class="btn btn-white border_radius_modal" data-dismiss="modal"><i class="fa fa-close"></i></button> -->

                                                                </div>
                                                                <div class="modal-body p-lg modal_body_padding_filters_product modal_scroll_filter_product">
                                                                    <div class="row">
                                                                        <div class="col-md-12">

                                                                            <section class="light">
                                                                                <div class="row" style="margin-bottom: 1%;">
                                                                                    <div class="col-md-8">
                                                                                        <h5 class="text_title_filter p_modal_title_bg_filters_product plans_for"> <?php echo $_REQUEST['plan_for_see']; ?></h5>
                                                                                    </div>

                                                                                </div>

                                                                                <div class="cart_review_data1">


                                                                                </div>
                                                                                <p style="float: left; display:none" class="add_plan1"> Additional Plans </p>
                                                                                </br>
                                                                                <div class="cart_review_data_additional1">


                                                                                </div>


                                                                            </section>

                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!---------row---------->
                                                </div> <!-- /.main-content -->
                                            </div>

                                        </div>
                                    </div>
                                    <!-- /.tab-container tab 1 -->
                                    <!-- /.tab-container tab 2 -->
                                    <!-- Graphics -->
                                    <div class="z-content z-active" style="display: block; position: relative; left: 0px;">
                                        <section class="header">
                                            <div class="container py-4">
                                                <!-- -------------ipad accordian--------------- -->
                                                <div class="panel-group margin_left__ipad_acc hidden-lg" id="accordion" role="tablist" aria-multiselectable="true">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingOne">
                                                            <h4 class="panel-title">
                                                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                    Basic Features
                                                                    <p class="active p_color_gray p_color_gray1">To find out more about the company and it's products, kindly refer the documents given below</p>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseOne" class="panel-collapse collapse  show" role="tabpanel" aria-labelledby="headingOne">

                                                            <div class="col-lg-12 bg_pink_plan">
                                                                <hr class="hr_p_b_w">
                                                                <?php foreach ($features[0]['features'] as $val) { ?>
                                                                    <div class="feature-offer-box_basic support-feature js-tilt">
                                                                        <div class="row">
                                                                            <div class="col-md-2 col-2">
                                                                                <div class="icon-box"><img src="/assets/features/<?php echo $val['file_name']; ?>"></div>
                                                                            </div>
                                                                            <div class="col-md-10 col-10">
                                                                                <h4 class="title"><?php echo $val['title']; ?></h4>
                                                                                <p class="txt-features"><?php echo strip_tags($val['long_description']); ?></p>
                                                                                <ul>

                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div> <!-- /.feature-offer-box -->

                                                                    <hr class="hr_p_b">
                                                                <?php } ?>



                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingTwo">
                                                            <h4 class="panel-title">
                                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                                    Special Features
                                                                    <p class="active p_color_gray p_color_gray1">To find out more about the company and it's products, kindly refer the documents given below</p>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                                            <div class="col-lg-12 bg_pink_plan">
                                                                <hr class="hr_p_b_w">
                                                                <?php foreach ($features[1]['features'] as $val) { ?>
                                                                    <div class="feature-offer-box_basic support-feature js-tilt">
                                                                        <div class="row">
                                                                            <div class="col-md-2 col-2">
                                                                                <div class="icon-box"><img src="/assets/features/<?php echo $val['file_name']; ?>"></div>
                                                                            </div>
                                                                            <div class="col-md-10 col-10">
                                                                                <h4 class="title"><?php echo $val['title']; ?></h4>
                                                                                <p class="txt-features"><?php echo strip_tags($val['long_description']); ?></p>

                                                                            </div>
                                                                        </div>
                                                                    </div> <!-- /.feature-offer-box -->


                                                                    <hr class="hr_p_b">

                                                                <?php } ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingThree">
                                                            <h4 class="panel-title">
                                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                                    Waiting Period
                                                                    <p class="active p_color_gray p_color_gray1">To find out more about the company and it's products, kindly refer the documents given below</p>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                                            <div class="col-lg-12 bg_pink_plan">
                                                                <hr class="hr_p_b_w">
                                                                <?php foreach ($features[2]['features'] as $val) { ?>
                                                                    <div class="feature-offer-box_basic support-feature js-tilt">
                                                                        <div class="row">
                                                                            <div class="col-md-2 col-2">
                                                                                <div class="icon-box"><img src="/assets/features/<?php echo $val['file_name']; ?>"></div>
                                                                            </div>
                                                                            <div class="col-md-10 col-10">
                                                                                <h4 class="title"><?php echo $val['title']; ?></h4>
                                                                                <p class="txt-features"><?php echo strip_tags($val['long_description']); ?></p>
                                                                                <ul>

                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div> <!-- /.feature-offer-box -->

                                                                    <hr class="hr_p_b">
                                                                <?php } ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading" role="tab" id="headingThree">
                                                            <h4 class="panel-title">
                                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
                                                                    What's not Covered?
                                                                    <p class="active p_color_gray p_color_gray1">To find out more about the company and it's products, kindly refer the documents given below</p>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapsefour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingfour">
                                                            <div class="row our-feature-app bg_pink_plan">
                                                                <div class="col-lg-12 order-lg-last">
                                                                    <div class="text-wrapper">

                                                                        <ul>
                                                                            <li>OPD Treatment</li>
                                                                            <li>Medical expenses incurred for treatment of AIDS</li>
                                                                            <li>Treatment arising from or traceable to pregnancy and childbirth</li>
                                                                            <li>miscarriage</li>
                                                                            <li>abortion and its consequences</li>
                                                                            <li>Cogenital disease</li>
                                                                            <li>Tests and treatment relating to infertility</li>
                                                                            <li>War or similar situations</li>
                                                                            <li>Breach of law</li>
                                                                            <li>Dangerous acts (including sports)</li>
                                                                            <li>Substance abuse and de-addiction programs</li>
                                                                            <li>Expenses attributable to self-infected injury (resulting from suicide, attempted suicide)</li>
                                                                        </ul>
                                                                        <!-- <a href="#" class="explore-button">Explore More</a> -->
                                                                    </div> <!-- /.text-wrapper -->
                                                                </div> <!-- /.col- -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!----------------end ipad accordian------------------->
                                                <div class="row margin_row_plan_basic hidden-md dis-none">
                                                    <div class="col-md-12 col-lg-5 text-left">
                                                        <!-- Tabs nav -->
                                                        <div class="nav flex-column nav-pills nav-pills-custom border_left_tab_plan" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                                            <a class="nav-link mb-3 p-3 shadow active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                                                                <!-- <i class="fa fa-user-circle-o mr-2"></i> -->
                                                                <span class="font-weight-bold">Basic Features</span>
                                                                <p class="active">To find out more about the company and it's products, kindly refer the documents given below</p>
                                                            </a>

                                                            <a class="nav-link mb-3 p-3 shadow" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                                                                <!--  <i class="fa fa-calendar-minus-o mr-2"></i> -->
                                                                <span class="font-weight-bold">Special Features</span>
                                                                <p class="active">To find out more about the company and it's products, kindly refer the documents given below</p>
                                                            </a>

                                                            <a class="nav-link mb-3 p-3 shadow" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">
                                                                <!--  <i class="fa fa-star mr-2"></i> -->
                                                                <span class="font-weight-bold">Waiting Period</span>
                                                                <p class="active">To find out more about the company and it's products, kindly refer the documents given below</p>
                                                            </a>

                                                            <a class="nav-link mb-3 p-3 shadow" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                                                                <!-- <i class="fa fa-check mr-2"></i> -->
                                                                <span class="font-weight-bold">What's not covered?</span>
                                                                <p class="active">To find out more about the company and it's products, kindly refer the documents given below</p>
                                                            </a>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-7 text-left">
                                                        <!-- Tabs content -->
                                                        <div class="tab-content" id="v-pills-tabContent">
                                                            <div class="tab-pane fade shadow rounded bg-white show active p-5" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                                <div class="col-lg-12 bg_pink_plan">
                                                                    <hr class="hr_p_b_w">
                                                                    <?php foreach ($features[0]['features'] as $val) { ?>


                                                                        <div class="feature-offer-box_basic support-feature js-tilt">
                                                                            <div class="row">
                                                                                <div class="col-md-2 col-2">
                                                                                    <div class="icon-box"><img src="/assets/features/<?php echo $val['file_name']; ?>"></div>
                                                                                </div>
                                                                                <div class="col-md-10 col-10">
                                                                                    <h4 class="title"><?php echo $val['title']; ?></h4>
                                                                                    <p class="txt-features"><?php echo strip_tags($val['long_description']); ?></p>
                                                                                    <ul>

                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div> <!-- /.feature-offer-box -->

                                                                        <hr class="hr_p_b">
                                                                    <?php } ?>



                                                                </div> <!-- /.col- -->
                                                            </div>

                                                            <div class="tab-pane fade shadow rounded bg-white p-5" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                                                <div class="col-lg-12 bg_pink_plan">
                                                                    <hr class="hr_p_b_w">
                                                                    <?php foreach ($features[1]['features'] as $val) { ?>
                                                                        <div class="feature-offer-box_basic support-feature js-tilt">
                                                                            <div class="row">
                                                                                <div class="col-md-2 col-2">
                                                                                    <div class="icon-box"><img src="/assets/features/<?php echo $val['file_name']; ?>"></div>
                                                                                </div>
                                                                                <div class="col-md-10 col-10">
                                                                                    <h4 class="title"><?php echo $val['title']; ?></h4>
                                                                                    <p class="txt-features"><?php echo $val['short_description']; ?></p>

                                                                                </div>
                                                                            </div>
                                                                        </div> <!-- /.feature-offer-box -->


                                                                        <hr class="hr_p_b">
                                                                    <?php } ?>



                                                                </div> <!-- /.col- -->
                                                            </div>

                                                            <div class="tab-pane fade shadow rounded bg-white p-5" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                                                <div class="col-lg-12 bg_pink_plan">
                                                                    <hr class="hr_p_b_w">
                                                                    <?php foreach ($features[2]['features'] as $val) { ?>
                                                                        <div class="feature-offer-box_basic support-feature js-tilt">
                                                                            <div class="row">
                                                                                <div class="col-md-2 col-2">
                                                                                    <div class="icon-box"><img src="/assets/features/<?php echo $val['file_name']; ?>"></div>
                                                                                </div>
                                                                                <div class="col-md-10 col-10">
                                                                                    <h4 class="title"><?php echo $val['title']; ?></h4>
                                                                                    <p class="txt-features"><?php echo $val['short_description']; ?></p>
                                                                                    <ul>

                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div> <!-- /.feature-offer-box -->

                                                                        <hr class="hr_p_b">
                                                                    <?php } ?>

                                                                </div>
                                                            </div>

                                                            <div class="tab-pane fade shadow rounded bg-white p-5" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                                                <div class="row our-feature-app bg_pink_plan">
                                                                    <div class="col-lg-12 order-lg-last">
                                                                        <div class="text-wrapper">

                                                                            <ul>
                                                                                <li>OPD Treatment</li>
                                                                                <li>Medical expenses incurred for treatment of AIDS</li>
                                                                                <li>Treatment arising from or traceable to pregnancy and childbirth</li>
                                                                                <li>miscarriage</li>
                                                                                <li>abortion and its consequences</li>
                                                                                <li>Cogenital disease</li>
                                                                                <li>Tests and treatment relating to infertility</li>
                                                                                <li>War or similar situations</li>
                                                                                <li>Breach of law</li>
                                                                                <li>Dangerous acts (including sports)</li>
                                                                                <li>Substance abuse and de-addiction programs</li>
                                                                                <li>Expenses attributable to self-infected injury (resulting from suicide, attempted suicide)</li>
                                                                            </ul>
                                                                            <!-- <a href="#" class="explore-button">Explore More</a> -->
                                                                        </div> <!-- /.text-wrapper -->
                                                                    </div> <!-- /.col- -->
                                                                </div> <!-- /.row -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                    </div>
                                    <!-- /.tab-container tab 2 -->
                                    <!-- /.tab-container tab 3 -->
                                    <div class="z-content">
                                        <div class="tab_inner_product_detail z-content-inner">
                                            <div class="our-feature-app" id="feature">

                                                <div class="our-service-app">

                                                    <div class="main-content hide-pr show-pr">
                                                        <div class="row margin_important_row">
                                                            <div class="col-lg-4 col-md-12 bg_white_plan">
                                                                <div class="feature-box_basic support-feature js-tilt padding_imp_row">
                                                                    <div class="row">

                                                                        <div class="col-md-10 col-10">
                                                                            <div class="imp_claim_p_bor">
                                                                                <h4 class="title imp_title_row_l">Important Number and email address</h4>
                                                                            </div>
                                                                            <p class="p_important_sub txt-features">Don't Hesitate to contact us for any information.</p>
                                                                            <ul>

                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div> <!-- /.feature-offer-box -->

                                                                <hr class="hr_p_b_cliam">
                                                                <div class="row mr6">
                                                                    <div class="col-md-4 col-lg-12">
                                                                        <div class="feature-offer-box_basic support-feature js-tilt">
                                                                            <div class="row">
                                                                                <div class="col-md-12 col-lg-2">
                                                                                    <div class="img_icon_important_claim icon-box claim_width_img"><img src="/assets/images/tel.png"></div>
                                                                                </div>
                                                                                <div class="col-md-12 col-lg-10">
                                                                                    <h4 class="font_20 title margin_bottom_claim">Toll free number:</h4>
                                                                                    <p class="margin_bottom_claim">+(111)65_458_856 </p>

                                                                                </div>
                                                                            </div>
                                                                        </div> <!-- /.feature-offer-box -->
                                                                    </div>
                                                                    <hr class="hr_p_b_cliam">
                                                                    <div class="col-md-4  col-lg-12">
                                                                        <div class="feature-offer-box_basic support-feature js-tilt">
                                                                            <div class="row">
                                                                                <div class="col-md-12 col-lg-2">
                                                                                    <div class="img_icon_important_claim icon-box claim_width_img"><img src="/assets/images/mail.png"></div>
                                                                                </div>
                                                                                <div class="col-md-12 col-lg-10">
                                                                                    <h4 class="font_20 title margin_bottom_claim">Email Drop Us</h4>
                                                                                    <p class="margin_bottom_claim">test21@gmail.com </p>

                                                                                </div>
                                                                            </div>
                                                                        </div> <!-- /.feature-offer-box -->
                                                                    </div>
                                                                    <hr class="hr_p_b_cliam">
                                                                    <div class="col-md-4  col-lg-12">
                                                                        <div class="feature-offer-box_basic support-feature js-tilt">
                                                                            <div class="row">
                                                                                <div class="col-md-12 col-lg-2">
                                                                                    <div class="img_icon_important_claim icon-box claim_width_img"><img src="/assets/images/mobile.png"></div>
                                                                                </div>
                                                                                <div class="col-md-12 col-lg-10">
                                                                                    <h4 class="font_20 title margin_bottom_claim">Manager Number:</h4>
                                                                                    <p class="margin_bottom_claim">+91 9579578988</p>

                                                                                </div>
                                                                            </div>
                                                                        </div> <!-- /.feature-offer-box -->
                                                                    </div>
                                                                </div>
                                                                <hr class="hr_p_b_cliam_w">
                                                            </div>
                                                            <div class="col-lg-8 col-md-12 margin_col_a_t_claim">
                                                                <div class="plan_a_t_claim">
                                                                    <h2 class="title_h4 title_h4_title_claim">How do I file a claim?</h2>
                                                                </div>

                                                                <div class="p-5 rounded shadow mb-5 how_do_i_width_claim">
                                                                    <!-- Bordered tabs-->
                                                                    <ul id="myTab1" role="tablist" class="nav nav-tabs nav-pills with-arrow flex-column flex-sm-row text-center">
                                                                        <li class="nav-item flex-sm-fill">
                                                                            <a id="home1-tab" data-toggle="tab" href="#home1" role="tab" aria-controls="home1" aria-selected="true" class="nav-link claim  font-weight-bold mr-sm-3 rounded-0 border active">Cashless Claim</a>
                                                                        </li>
                                                                        <li class="nav-item flex-sm-fill">
                                                                            <a id="profile1-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="profile1" aria-selected="false" class="nav-link claim font-weight-bold mr-sm-3 rounded-0 border">Documents Required</a>
                                                                        </li>
                                                                        <li class="nav-item flex-sm-fill">
                                                                            <a id="contact1-tab" data-toggle="tab" href="#contact1" role="tab" aria-controls="contact1" aria-selected="false" class="nav-link claim font-weight-bold rounded-0 border">Reimbursement Claim</a>
                                                                        </li>
                                                                    </ul>
                                                                    <div id="myTab1Content" class="tab-content">
                                                                        <div id="home1" role="tabpanel" aria-labelledby="home-tab" class="tab-pane fade px-4 py-5 show active">
                                                                            <img src="/assets/images/cashless_m.png" style="width: 110px;">
                                                                            <h3 class="text-left cashless_t_r_t_main">Cashless Claim</h3>
                                                                            <p class="ClaimMain__Paragraph-sc-h7cdi1-0 jXbJJL leade_p" style="text-align: justify; white-space: normal;">
                                                                            <p><span><b>Step 1 : Claim Intimation</b></span><span style="white-space:pre"> </span>&#xFEFF;</p>
                                                                            <p><span>F</span><span>or emergency hospitalization: Call and inform the Insurer at 1800-102-4488 within 24 hours of your admission.</span></p>
                                                                            <p><span>For planned hospitalization: Kindly intimate the Insurer 48 hours prior to your admission by calling on the same number or writing at</span></p>
                                                                            <p><span><br></span><b style="font-size: 1rem;"><span>Step 2 : Initiating the process for Pre-Authorization</span></b><span style="font-size: 1rem; white-space: pre;"> </span></p>
                                                                            <p><span>A Pre-Authorization form will be available at the hospital's Insurance/TPA desk, or you can alternatively download the same from here</span><span style="white-space: pre; font-family: Arial;"> </span></p>
                                                                            <p><span>Please fill the first section of the form by giving your personal details and hand over the signed Pre-Authorization form to the hospital's Insurance/TPA desk for them to fill up the balance details.</span><span style="white-space: pre; font-family: Arial;"> </span></p>
                                                                            <p><span>Hospital will fax the completed Pre-Authorization form to Insurer at 1800-200-6677.</span></p>
                                                                            <p><span><br></span><b style="font-size: 1rem;"><span>Step 3 : Processing a request for Pre-Authorization</span></b><span style="font-size: 1rem; white-space: pre; font-family: Arial;"> </span></p>
                                                                            <p><span>Care Health's in-house medical team will review the case and documents submitted by the hospital.</span></p>
                                                                            <p><span>If your request for Pre-Authorization is approved, you and the hospital will be duly informed by us.</span><span style="font-size: 1rem; white-space: pre; font-family: Arial;"> </span></p>
                                                                            <p><span>In case of any information deficiency or further information requirement, you and the hospital will be regularly intimated by Insurer to ensure resolution of the same at the earliest.</span></p>
                                                                            <p><span>If your request for Pre-Authorization is not approved, it only indicates that Insurer is not able to process your request basis the requisite information available with us at this point of time. In such cases, you may claim for reimbursement of your expenses after discharge from the hospital.</span><span style="font-size: 1rem;">&nbsp;</span><span>We will ensure that you are updated at all important stages of your claim process. To help us serve you better, please ensure the following-&nbsp;</span></p>
                                                                            <p><span>The Pre-Authorization/Claim form is filled completely, sincerely and truly and all the required documents are submitted along with the form and in original, wherever specified. Retain a copy of the duly filled forms.</span></p>
                                                                            <p><span>We will provide a reference id for all communication pertaining to claim request. Kindly quote that reference number for all communication related to the above.</span><span style="white-space: pre; font-family: Arial;"> </span></p>
                                                                            </p>
                                                                        </div>
                                                                        <div id="profile1" role="tabpanel" aria-labelledby="profile-tab" class="tab-pane fade px-4 py-5">
                                                                            <img src="/assets/images/cashless_m_2.png" style="width: 110px;">
                                                                            <h3 class="text-left cashless_t_r_t_main">Documents Required</h3>
                                                                            <p class="ClaimMain__Paragraph-sc-h7cdi1-0 jXbJJL leade_p" style="text-align: justify; white-space: normal;">
                                                                            <div style="text-align: left;">
                                                                                <div><span><b>List of Documents:</b></span></div>
                                                                                <div><span><b><br></b></span></div>
                                                                                <div><span>1.Duly completed and signed Claim form, in original</span></div>
                                                                                <div><span>2.Valid photo-id proof</span></div>
                                                                                <div><span>3.Medical practitioner's referral letter advising Hospitalization</span></div>
                                                                                <div><span>4.Medical practitioner's prescription advising drugs/diagnostic tests/consultation</span></div>
                                                                                <div><span>5.Original bills, receipts and Discharge card from the Hospital/Medical Practitioner</span></div>
                                                                                <div><span>6.Original bills from pharmacy/Chemists</span></div>
                                                                                <div><span>7.Original pathological/diagnostic tests reports/radiology reports and payment receipts 8. Indoor case papers</span></div>
                                                                                <div><span>8.First information Report, final police report, if applicable</span></div>
                                                                                <div><span>9.Post mortem report, if conducted Any other document as required by the company to assess the claim</span><span style="white-space: pre; font-family: Arial;"> </span><br></div>
                                                                                <div><span style="white-space: pre;"> </span></div>
                                                                                <div><span>Any other document as required by the company to assess the claim</span><span style="white-space: pre; font-family: Arial;"> </span></div>
                                                                            </div>
                                                                            </p>
                                                                        </div>
                                                                        <div id="contact1" role="tabpanel" aria-labelledby="contact-tab" class="tab-pane fade px-4 py-5">
                                                                            <img src="/assets/images/cashless_m_3.png" style="width: 110px;">
                                                                            <h3 class="text-left cashless_t_r_t_main">Reimbursement Claim</h3>
                                                                            <p class="ClaimMain__Paragraph-sc-h7cdi1-0 jXbJJL leade_p" style="text-align: justify; white-space: normal;">
                                                                            <p><span><b>For Reimbursement Claim:&nbsp;</b></span></p>
                                                                            <p><span>To initiate the reimbursement claim processing, please proceed with the hassle free online claim submission process by visiting the <span>Online Claim Submission</span></a></p>
                                                                            <p><span>Step 1: On the day of discharge, visit website or enter policy details to initiate the process.Review the checklist to ensure that you have all the required documents.</span></p>
                                                                            <p><span>Step 2: Scan a copy of claim support documents or take picture of the same and submit along with a copy of cancelled cheque, valid ID &amp; address proof.</span><br></p>
                                                                            <p><span>Step 3: Receive instant acknowledgement of your submission while your claim is being reviewed.</span><br></p>
                                                                            <p><span><b>Note:</b></span><br></p>
                                                                            <p><span>For Planned Hospitalization contact your service provider two days prior to admission.&nbsp;</span></p>
                                                                            <p><span>For Emergency Hospitalization contact your service provider within 24 hours of hospitalization.&nbsp;</span></p>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <!-- End bordered tabs -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!---------row---------->
                                                    </div> <!-- /.main-content -->
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-container tab 3 -->
                                    <!-- Themes -->

                                </div>
                            </div> <!-- /.theme-tab -->
                        </div>
                    </div>

                </div><!-- /.modal-content -->
            </div>
        </div>
        <!-- / .modal -->
        <form action="<?php echo base_url() . "quotes/generate_proposal" ?>" id="hiddenCardForm1" method="POST">
            <input type="hidden" value="<?php echo $plan_details[0]['plan_id']; ?>" name="plan_id" id="hiddenplanid">
            <input type="hidden" value="<?php echo $_REQUEST['cover_see']; ?>" name="cover" id="hiddencover">
            <input type="hidden" value="<?php echo $_REQUEST['premium_see']; ?>" name="premium" id="hiddenpremium">
            <input type="hidden" value="<?php echo $plan_details[0]['plan_name'] . ' - ' . $plan_details[0]['creditor_name']; ?>" name="plan_name" id="hiddenplanname">
            <input type="hidden" value="<?php echo $_REQUEST['policy_id_see']; ?>" name="policy_id" id="hiddenpolicyid">
            <input type="submit" style="display:none;">
        </form>
        <!-- Scroll Top Button -->
        <!-- <button class="scroll-top tran3s">
            <i class="fa fa-angle-up" aria-hidden="true"></i>
        </button> -->


    </div> <!-- /.main-page-wrapper -->
</body>
<script>
    $(document).ready(function() {
        debugger;

        var policy_id = $("#hiddenpolicyid").val();
        var plan_id = $("#hiddenplanid").val();
        var cover = $('#hiddencover').val();


        data = {};

        data.plan_id = plan_id;
        data.policy_id = policy_id;
        data.cover = cover;
        $.ajax({
            url: "/quotes/get_all_data_card",
            type: "POST",
            async: false,
            data: data,
            dataType: "json",
            success: function(response) {
                var i;
                var rep = response;
                var res = JSON.parse(rep.data);
                console.log(res[0]);
                var str;
                var str1;
                var k = 1;
                var j = 1;
                let total = 0;
                var checked = '';
                $(".cart_review_data1").html('');
                $(".cart_review_data_additional1").html('');
                for (i = 0; i < res.length; i++) {
                    // console.log(res[i].member_id);
                    if ((res[i].is_combo == 1 && res[i].is_optional == 0) || (res[i].is_combo == 0 && res[i].is_optional == 0)) {
                        $(".add_plan").hide();
                        str = '<p style="float: left;"> ' + res[i].policy_sub_type_name + '</p>';
                        str += '<div class=""><br><div class="col-md-12 col-12 pad_left text-right"><div class="agreement-checkbox margin_top_checkbox_card chc-101"><div><input type="checkbox" checked readonly name = "chk_status[]" id= "checkbox_' + k + '" disabled class="compare-checkbox1 disabled-checkbox" value="' + res[i].policy_id + '"><label for="checkbox_' + k + '" class="col-md-12"> <div class="row bg_cart col-md-12" style="margin-top: -2% !important;border: 1px solid #e2e2e2;margin: 2px;"><span class = "addition_plan" style = "display:none">0</span><div class="col-md-2 col-6"> <div class="logo_add float_left_addon_c_cart"><img class="contain border_radius_50_l" src=' + res[i].creditor_logo + ' width="34"></div> </div> <div class="col-md-3 text-left col-6" style="margin: 0 -18px 0 17px !important;"> <p class="text-black font_14 font_family_bold_quote">Cover</p> <div class="product_title_p_bor_pop_right_buy_f_cart"> <p class="text-black font_13 font_family_bold_quote"><span class="color_red covers"><i class="fa fa-inr"></i> ' + cover + '</span> </p> </div> </div> <div class="col-md-3 text-left col-6" style="margin: 0 14px 0 -13px !important;"> <p class="text-black font_14 font_family_bold_quote">Premium</p> <div class="product_title_p_bor_pop_right_buy_f_r"> <p class="text-black font_13 font_family_bold_quote"><span class="color_red premiums"><i class="fa fa-inr"></i> ' + res[i].premium + '</span> </p> </div> </div> <div class="col-md-3 text-left col-6" style=" margin: 0 -46px 0 14px !important"> <p class="text-black font_14 font_family_bold_quote">Tenure</p> <p class="text-black font_13 font_family_bold_quote">1 year</p> </div></div></label></div></div></div></div>';
                        $(".cart_review_data1").append(str);
                        total += parseFloat(res[i].premium);
                    }
                    if (res[i].is_combo == 0 && res[i].is_optional == 1) {
                        $(".add_plan1").show();
                        if (res[i].already_avail == 1) {
                            checked = 'checked';
                            total += parseFloat(res[i].premium);

                        } else {
                            checked = '';
                        }
                        str1 = '<p style="float: left;"> ' + res[i].policy_sub_type_name + '</p><span class = "addition_plan" style = "display:none">1</span>';
                        str1 += '<div class=""><br><div class="col-md-12 col-12 pad_left text-right"><div class="agreement-checkbox margin_top_checkbox_card chc-101"><div><input type="checkbox" name = "chk_status[]" id= "checkbox_' + k + '" class="compare-checkbox1" value="' + res[i].policy_id + '" ' + checked + '><label for="checkbox_' + k + '" class="col-md-12"> <div class="row bg_cart col-md-12" style="margin-top: -2% !important;border: 1px solid #e2e2e2;margin: 2px;"><span class = "addition_plan" style = "display:none">1</span><div class="col-md-2 col-6"> <div class="logo_add float_left_addon_c_cart"><img class="contain border_radius_50_l" src=' + res[i].creditor_logo + ' width="34"></div> </div> <div class="col-md-3 text-left col-6" style="margin: 0 -18px 0 17px !important;"> <p class="text-black font_14 font_family_bold_quote">Cover</p> <div class="product_title_p_bor_pop_right_buy_f_cart"> <p class="text-black font_13 font_family_bold_quote"><span class="color_red covers"><i class="fa fa-inr"></i> ' + cover + '</span> </p> </div> </div> <div class="col-md-3 text-left col-6" style="margin: 0 14px 0 -13px !important;"> <p class="text-black font_14 font_family_bold_quote">Premium</p> <div class="product_title_p_bor_pop_right_buy_f_r"> <p class="text-black font_13 font_family_bold_quote "><span class="color_red premiums"><i class="fa fa-inr"></i> ' + res[i].premium + '</span> </p> </div> </div> <div class="col-md-3 text-left col-6" style=" margin: 0 -46px 0 14px !important"> <p class="text-black font_14 font_family_bold_quote">Tenure</p> <p class="text-black font_13 font_family_bold_quote">1 year</p> </div></div></label></div></div></div></div>';
                        $(".cart_review_data_additional1").append(str1);
                    }
                    k++;
                }
                $('.total_premium_data').html(total.toFixed(2));


            }


        });




    });
    $("body").on("change", 'input[name="chk_status[]"]', function() {
        var ids = $(this).attr('id');
        var total = $(".total_premium_data").text();
        // $('.total_premium').html('');
        var abc = $('#' + ids).closest('div').find('.premiums').text();
        if ($("#" + ids).is(':checked')) {


            var add_premium = parseFloat(abc) + parseFloat(total);
            $('.total_premium_data').html(add_premium.toFixed(2));
            $('.premium_details').html(add_premium.toFixed(2));
            $('#hiddenpremium').val(add_premium.toFixed(2));
        } else {

            var sub_premium = parseFloat(total) - parseFloat(abc);
            $('.total_premium_data').html(sub_premium.toFixed(2));
            $('.premium_details').html(sub_premium.toFixed(2));
            $('#hiddenpremium').val(sub_premium.toFixed(2));
        }


    });
    $(document).on("click", ".cart_continue1", function() {
        debugger;
        var plan_id = $("#hiddenplanid").val();


        var total_premium = $('.total_premium_data').text();
        data = {};


        var TableData = [];
        $('input[name="chk_status[]"]:checked').each(function(row) {
            var ids = $(this).attr('id');

            // $('.total_premium').html('');
            var abc = $(this).closest('div').find('.premiums').text();
            var cover = $(this).closest('div').find('.covers').text();
            var additional_plan = $(this).closest('div').find('.addition_plan').text();
            TableData[row] = {
                "policy_id": $(this).val(),
                "premium": abc,
                "cover": cover,
                "plan_id": plan_id,
                "total_premium": total_premium,
                "tenure": '1 year',
                "plan_flag": additional_plan
            }

        });

        data.policy_details = TableData
        $.ajax({
            url: "/quotes/create_policy_member_plan",
            type: "POST",
            async: false,
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.status == 'success') {
                    $("#hiddenCardForm1").submit();
                }
            }





        });


    });

    /* requestData['source'] = 'customer'
     requestData['age'] = $('#max_age').val();
     requestData['age'] = $('#max_age').val();
     requestData['family_members_ac_count'] = family_constrcut;

     var quote_action_url = "/policyproposal/generateQuote";

     $.ajax({
         url: quote_action_url,
         data: requestData,
         type: 'post',
         dataType: 'json',
         cache: false,
         clearForm: false,
         success: function(response) {
             //if (response.success) {
             let data = response.data;
             populateQuoteData(data);
             //}
         }
     });*/
</script>