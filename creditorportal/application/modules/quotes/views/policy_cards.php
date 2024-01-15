<?php //echo '<pre>';print_r(array_unique($premium_arr['685']));print_r(array_unique($sum_insured_arr['685']));die;
?>
<style>
    /* 13-01-2022 */
    .product_logo_title {
        background: #bcbcbc;
        display: flex;
        align-items: center;
        border-radius: 51px 0px 51px 0px !important;
        margin: 0px 30px;
    }
    .quotesPage_title .margin_left_sor_b {
        margin-left: 0;
        margin-top: 0;
    }
    .row.quotesPage_title {
        align-items: center;
    }
    .selectize-control.theme-select-menu  .selectize-input {
        width: 100%;
        /* margin: 0px 0px 0 2px; */
        border-radius: 5px;
        font-family: 'PFEncoreSansPromed';
        font-size: 15.4px !important;
    }

    .product_logo_title .font_p_main {
        margin-left: 8% !important;
        padding: 0 !important;
        line-height: inherit !important;
        width: 81%;
    }

    .product_logo_title  img {
        margin-right: 7px;
    }
    .agreement-checkbox label:before {
        border-radius: 3px;
    }

    .our-service-seo .single-block .icon {
        position: relative;
        margin-top: unset !important;
    }
    .row_cover_premium_product {
        margin: 5px 37px 8px 22px;
        padding-bottom: 10px;
        align-items: center;
    }
    .row_cover_premium_product .line_height_p_product{
        margin-bottom: 0px;
        padding: 0px !important;
    }
    .select_premium {
        width: auto;
    }
    .border_right_product_cover {
        padding-left: 11px;
        height: auto;
    }

    .border_left_product_cover {
        padding-left: 11px;
    }
    .row_cover_premium_product .line_height_p_product{
        padding-bottom: 10px !important;
    }
    .p_more_plans_arrow{
        font-size: 14px !important;
        font-family: 'PFEncoreSansPromed';
        margin-right: 0;
    }
    .over_border_txt {
        color: #e3001c !important;
    }
    .border_right_filter {
        margin-top: 0px;
    }
    .dropdown_product_m_t_b:hover {
        background: none !important;
        height: auto;
    }
    .form-group.dropdown_product_m_t_b.email.col-md-12.nm-text.filter_drpdwn {
        margin-left: 0px;
    }
    .email.expand {
        background: none !important;
        height: auto;
    }
    .email_cover.expand_cover {
        background: none !important;
        height: auto;
    }
    .email1.expand1 {
        background: none !important;
        height: auto;
    }
    .btn_filter_shadow  .form-group.dropdown_product_m_t_b.filter_drpdwn input.multiYear_inp{
        border-right: none !important;
    }
    .shrt-menu .top-header {
        border-bottom: none;
        background: #e9eff1 !important;
        padding: 1px 131px;
    }
    .form-group.dropdown_product_m_t_b.filter_drpdwn input:last-child {
        border-right: none !important;
        border-radius: 0;
    }
    .multi-button button {
        background: #fff;
        border: 1px dashed #e3001c;
    }
    .form-group.dropdown_product_m_t_b.filter_drpdwn{
        margin-top: 14px !important;
    }

    .form-group.dropdown_product_m_t_b.filter_drpdwn input {
        border-right: 2px solid #eaeaea !important;
        border-radius: 0;
    }
    .shrt-menu.shrt-menu-three.bg_white__shrt_product .btn_filter_shadow{
        border: 2px dotted #dcdbdb;
        border-radius: 6px;
    }
    .shrt-menu.shrt-menu-three.light-bg.bg_white__shrt_product {
        background-color: rgb(255, 255, 255) !important;
        box-shadow: rgb(0 1 1 / 8%) 0px 1px 6px 0px !important;
    }
    .agreement-checkbox label {
        font-size: 14px !important;
        font-family: 'PFEncoreSansPromed';
    }
    .border_left_product_cover span {
        font-size: 14px !important;
        font-family: 'PFEncoreSansPromed';
    }
    /* 13-01-2022 */

    .our-service-seo .single-block .icon {
        width: 20%;

    }

    .product_title_p_bor_pop_right_buy_f_r :after {
        background-color: none !important;
    }

    .web-none {
        display: none;
    }

    .product_title_p_bor_pop_right_buy_f_cart :after {
        background-color: none !important;
    }

    .add_plan {
        float: left;
        font-family: 'PFEncoreSansPro-book' !important;
        font-weight: 600;
        padding-left: 0px;
        font-size: 20px;
        color: #107591;
        letter-spacing: 1px;
    }

    @media (max-width: 576px) {
        .md-nrt {
            max-width: 90%;
            margin: 1.75rem auto;
        }

        .modal_title_margin {
            font-size: 17px !important;
        }

        .border_radius_50_l {
            border-radius: 10px;
        }

        .logo_add {
            position: absolute;
            top: 78%;
            left: -22%;
        }
    }

    .modal_scroll_filter_product {
        padding-right: 17px !important;
    }

    .disabled-checkbox,
    .disabled-label {
        opacity: 0.5;
        pointer-events: none;
    }

    .cart_review_data {
        font-family: 'PFEncoreSansPro-book' !important;
        font-weight: 600;
    }

    .chc-101 label:before {
        top: 7px;
    }

    .total_premium_p_s {
        margin-bottom: 0px;
    }

    .font_family_bold_quote {
        margin-bottom: 0px;
    }

    .pr-card-vr {
        background: #ffeeee;
        padding: 0px 8px 0px 10px;
        border-radius: 10px;
        font-family: 'PFEncoreSansPro-book' !important;
    }

    @media only screen and (max-device-width : 480px) {

        .none-display,
        .edit_member_icon {
            display: none !important;
        }

        .single-block {
            padding-top: 0px !important;
        }

        .quotes_compare_container {
            padding-top: 16px;
            padding-bottom: 15px;
            height: 90px;
        }

        .quotes_compare_plan_add,
        .quotes_compare_plan_name {
            padding: 0px !important;
        }

        .agreement-checkbox input[type="checkbox"]:checked+label:before {
            line-height: 16px;
            font-size: 10px;
        }

        .pd-f-0 {
            padding-left: 0px;
        }

        .web-none {
            display: block !important;
        }

        .colmd-sm {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 33.333333%;
            flex: 0 0 27.333333%;
            max-width: 27.333333%;
        }

        .mb-sdetails {
            position: absolute;
            right: 7%;
            font-weight: 600;
            font-size: 14px;
            margin-top: 3%;
            background: #ffe3e6;
            color: #107591;
            padding: 1px 7px;
            border-radius: 10px;
            letter-spacing: 0.4px;
        }

        .pd-0 {
            padding: 0px;
        }

        .product_logo_title img {
            width: 15% !important;
            position: relative !important;
            left: -15px !important;
            top: 56% !important;
        }

        .tooltip-inner,
        .mar_color_grey,
        .hr_margin_que {
            display: none;
        }

        .plc-txt {
            position: absolute;
            bottom: 9px;
            right: 0;
        }

        .margin_top_checkbox_card {
            top: -60%;
            float: left;
            left: 20%;
        }

        .margin_top_feature {
            background: none;
            box-shadow: none;
        }

        .solid-button-buy {
            position: absolute;
            right: 0;
            top: -85%;
        }

        .agreement-checkbox label:before {
            content: '';
            width: 20px;
            height: 20px;
            left: -18px;
            top: -9px;
        }

        .agreement-checkbox label {
            font-size: 14px;
        }

        .margin_internal_card_copay,
        .product_logo_title {
            background: none;
        }

        .margin_top_feature {
            margin-top: 0px;
        }

        .line_height_p_product {
            margin-bottom: 0px;
        }

        .border_box {
            margin-top: 0px;
        }

        .img_IC_width {
            height: auto !important;
            width: 13% !important;
            margin-top: 10px !important;
        }

        .flaot_left_button {
            font-size: 15px;
        }

        .plan_for {
            font-size: 21px;
            margin-top: 12px;
        }

        .font_feature_p_card b {
            background: #ffefd6;
            border-radius: 5px;
            padding: 5px;
            color: #000;
        }

        .quotes_compare_span_plan_name,
        .quotes_compare_span_plan_name1 {
            display: none;
        }

        .p_more_plans_arrow_img,
        .p_more_plans_arrow {
            visibility: hidden;
        }

        .quotes_compare_span_add_plan {
            font-size: 13px;
            width: 38px;
            line-height: 14px;
        }

        .font_p_main {
            margin-left: 26px !important;
            margin-top: 18px;
        }

        .lbl-ed {
            background: #107591;
            color: #fff;
            letter-spacing: 1px;
            font-weight: 500;
            border-radius: 10px;
            padding: 2px 6px;
            position: absolute;
            top: 156%;
            left: -3%;
            display: block;
        }

        .quotes_compare_button {
            padding: 9px 0px !important;
            font-size: 10px !important;
        }

        .quotes_compare_remove_button {
            font-size: 13px;
            position: absolute;
            top: -19%;
            border: none;
            color: red;
            border: none;
        }

        .quotes_compare_button {
            width: 100%;
        }

        .mar_color_grey {
            margin-left: 0px !important;
        }

        .pd-0 {
            padding: 0px;
        }

        .row_cover_premium_product {
            margin-left: 9px;
            margin-right: 9px;
            margin-top: 20%;
        }

        .font_p_main {
            margin-top: -25px;
            position: relative;
        }

        /* .hr_margin_que {
            border: none;
        } */
    }

    @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
        .ipad-01 {
            flex: 0 0 100%;
            max-width: 100% !important;
        }

        .quote-vc {
            display: none;
        }

        .mar-78 {
            margin-left: 0px;
        }

        .mb-fl3 {
            width: 900px;
        }
    }

</style>
<div class="container-fluid col-md-10 offset-md-1 nw-width ipad-01 mar-78">
    <div class="row">
        <div class="col-md-9 pd-0 ipad-01">
            <!-- <div class="product_title_p_bor"> -->
            <div class="row quotesPage_title">
                <div class="col-md-9">
                    <div class="product_title_p_bor">
                        <p class="font_bold margin_showing font_family_encoreblack margin_title_product_plan f-20" style="font-size: 23px;">Showing <?php echo (isset($getQuotePageData['member_ages'][0]['suminsured_type'])) ? $getQuotePageData['member_ages'][0]['suminsured_type'] : "Family Floater"; ?> Plans for <?php echo $members_string;  ?></p>
                    </div>
                </div>
                <!-- <button class="white-shdw-button btn_white_filter_sort" style="float: right; margin: -43px 0px;"> <img src="images/filter.png"></button> -->
                <div class="col-md-3 margin_left_sor_b">
                    <!-- <label class="label_sort">Sort By</label>
                                    <div class="input-group" style="width: 80%;">
                                        <select class="theme-select-menu">
                                            <option value="23">relevance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                                            <option value="12">premium low to high</option>
                                        </select>
                                    </div>  -->
                    <!-- /.input-group -->
                </div>
            </div>
            <!-----------card start-------------->
            <?php
            $i = 0;
            foreach ($group_by_policies as $single_policy) {

                ?>


                <div class="col-md-12 pd-0 ipad-01" class="checkout-form">

                    <div class="single-block pd-0">
                        <div class="row mb-40 margin_top_feature" id="insurance-plan-1">
                            <div class="col-md-5">
                                <div class="row">
                                    <!-- see details -->
                                    <div class="web-none">
                                        <span class="mb-sdetails">See Details</span>
                                    </div>
                                    <div class=" col-md-10 col-lg-10 product_logo_title ">
                                        <img src="<?php echo ($single_policy['creditor_logo'] != '') ? $single_policy['creditor_logo'] : base_url() . 'assets/images/ad-logo.png'; ?>" alt="" class="icon img_IC_width" id="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>_logo">
                                        <p class="font_p_main" id="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>_plan_name" style="margin-left: -11px;">
                                            <!-- <?php echo $key; ?> -->
                                            <?php echo  $single_policy['creaditor_name'] . ' - ' . $single_policy['plan_name']; ?>

                                        </p>
                                    </div>
                                </div>
                                <div class="flex-row1 row_cover_premium_product">

                                    <div class="col-md-5 no_padding">
                                        <div class="border_right_product_cover">
                                            <p class="line_height_p_product">Cover</p>
                                            <!-- <p class="p_line_sub_product" id="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>_cover"><i class="fa fa-inr"></i> 
                                                            
                                                        </p> -->

                                            <select class="p_line_sub_product  dropdown_sum_insured" name="<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>" id="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>_cover">

                                                <?php
                                                // $sum_insured_arr = array_unique($sum_insured_arr);
                                                $sa_array_check = array();
                                                // echo 'pooja';

                                                foreach ($sum_insured_arr as $key => $single_sa) {

                                                    ksort($single_sa);
                                                    $abc =  array_unique($single_sa);
                                                    sort($abc);

                                                    if ($key == $single_policy['policy_id']) {
                                                        foreach ($abc as $single_sub_sa){
                                                            // var_dump($single_sub_sa[0]);
                                                            foreach ($single_sub_sa as $item) {
                                                                if (!in_array($item['sum_insured'], $sa_array_check)) {
                                                                    ?>
                                                                    <option value="<?php echo $single_policy['plan_id'] . ',' . $item['sum_insured']; ?>"><?php echo $item['sum_insured']; ?></option>
                                                                    <?php
                                                                    array_push($sa_array_check, $item['sum_insured']);
                                                                }
                                                            }

                                                        }
                                                    }
                                                }


                                                //print_r($sa_array_check);
                                                ?>




                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-md-7 no_padding">
                                        <div class="border_left_product_cover">
                                        <p class="line_height_p_product">Premium</p>

                                            <i class="fa fa-inr premium-inr"></i>
                                        <select class="select_premium" id="select_premium_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>" disabled>

                                            <?php
                                            //echo 'pooja';
                                            // $sum_insured_arr = array_unique($sum_insured_arr);
                                            $sa_array_check_premium = array();
                                            foreach ($sum_insured_arr as $key => $single_sa) {

                                                ksort($single_sa);
                                                $abc =  array_unique($single_sa);
                                                sort($abc);

                                                if ($key == $single_policy['policy_id']) {
                                                    foreach ($abc as $single_sub_sa){

                                                        foreach ($single_sub_sa as $key1=>$item) {
                                                            if (!in_array($item['rate'], $sa_array_check_premium)) {
                                                                ?>
                                                                <option value="<?php echo $single_policy['plan_id'] . ',' . $sa_array_check[$key1]; ?>"><?php echo $item['rate']; ?></option>
                                                                <?php
                                                                array_push($sa_array_check_premium, $item['rate']);
                                                            }
                                                        }

                                                    }
                                                }
                                            }

                                            ?>






                                        </select>


                                        <span>/ annual</span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 margin_internal_card_copay">
                                <div class="row">
                                    <?php foreach ($single_policy['features'] as $arrFeature) { ?>
                                        <div class="col-md-4 colmd-sm pd-f-0">
                                            <a href="#">
                                                <div class=" border_box font_l_box mar_left_12" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo strip_tags($arrFeature['long_description']); ?>">
                                                    <img src="<?php echo base_url() . 'assets/features/' . $arrFeature['file_name']; ?>" class="margin_img_inline none-display">
                                                    <span class="text-left font_feature_p_card"><?php echo $arrFeature['title']; ?><span class="color_grey display_block mar_color_grey"><?php echo $arrFeature['short_description']; ?></span></span>
                                                </div>
                                            </a>

                                        </div>
                                    <?php } ?>
                                    <!-- <div class="col-md-4">
                                        <a href="#">
                                            <div class=" border_box font_l_box mar_left_12" data-toggle="tooltip" data-placement="right" title="" data-original-title="A specific percent of (00% of) claim amount is paid by the Insured person.">
                                                <img src="/assets/images/copay.png" class="margin_img_inline">
                                                <span class="text-left font_feature_p_card"><b>Copay</b><span class="color_grey display_block mar_color_grey">0%</span></span>
                                            </div>
                                        </a>

                                    </div>
                                    <div class="col-md-4">
                                        <a href="#">
                                            <div class=" border_box font_l_box mar_0" data-toggle="tooltip" data-placement="right" title="" data-original-title="Expenses incurred for room &amp; boarding of a Hospital room.">
                                                <img src="/assets/images/roomrent.png" class="margin_img_inline">
                                                <span class="text-left font_feature_p_card"><b>Room
                                                        Rent</b><span class="color_grey display_block mar_color_grey">No
                                                        Limit</span></span>
                                            </div>
                                        </a>

                                    </div>
                                    <div class="col-md-4">
                                        <a href="#">
                                            <div class="font_l_box mar_left__14" data-toggle="tooltip" data-placement="left" title="" data-original-title="A specific percent of claim amount is paid by the Insured person in case the treatment is taken at a hospital not mentioned in the Network Hospitals list.">
                                                <img src="/assets/images/cashless.png" class="margin_img_inline">
                                                <span class="text-left font_feature_p_card"><b>Cashless
                                                        Hospital</b><br><span class="color_grey display_block mar_color_grey">16+</span></span>
                                            </div>
                                        </a>
                                    </div>



                                    <div class="col-md-4">
                                        <a href="#">
                                            <div class=" border_box font_l_box mar_left_12" data-toggle="tooltip" data-placement="right" title="" data-original-title="Medical expenses incurred for any illness diagnosed or diagnosable can be claimed after the initial waiting period of 00 Days except in case of accident/bodily injury.">
                                                <img src="/assets/images/waiting.png" class="margin_img_inline">
                                                <span class="text-left font_feature_p_card"><b>Waiting
                                                        Period</b><span class="color_grey display_block mar_color_grey">3
                                                        Years</span></span>
                                            </div>
                                        </a>

                                    </div>
                                    <div class="col-md-4">
                                        <a href="#">
                                            <div class=" border_box font_l_box mar_0" data-toggle="tooltip" data-placement="right" title="" data-original-title="Increase in SI by 00% for every claim free year for a maximum of 00% without increase in premium.">
                                                <img src="/assets/images/renewal.png" class="margin_img_inline">
                                                <span class="text-left font_feature_p_card"><b>Renewal
                                                        Bonus</b><br><span class="color_grey display_block mar_color_grey">10.0%</span></span>
                                            </div>
                                        </a>

                                    </div> -->
                                    <div class="col-md-4 no_padding plc-txt">
                                        <a href="javascript:void(0)" class="cardBuyNow" data-planid="<?php echo $single_policy['plan_id']; ?>" data-policyid="<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>"><button class="solid-button-buy ">Buy Now</button></a>

                                    </div>
                                </div>
                                <!------------end feature row----------->
                            </div>
                            <!----------col-md-8---------->
                        </div>
                        <div class="col-md-12 mt-12">
                            <hr class="hr_margin_que">
                        </div>
                        <div class="container-fluid box_post_back">
                            <div class="row">
                                <div class="col-md-4 col-5" class="accordion" id="accordionExample">
                                    <!-- <div id="headingOne">
                                                    <p class="p_more_plans_arrow" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        <?php echo count($single_policy) - 1; ?> More Plans <img class="p_more_plans_arrow_img_four" src="images/down_ar.png"></p>

                                                </div> -->
                                </div>

                                <div class="col-md-5 col-5 text-center padding_see_details_plan none-display" data-toggle="modal" data-target="#myModal" style="cursor: pointer;">
                                    <form action="<?php echo base_url() . "quotes/quotedetails" ?>" id="seeCardForm" method="POST">
                                        <input type="hidden" value="" name="plan_id_see" id="seeplanid">
                                        <input type="hidden" value="" name="cover_see" id="seecover">

                                        <input type="hidden" value="" name="premium_see" id="seepremium">
                                        <input type="hidden" value="" name="seepolicy_arr" id="seepolicy_arr">
                                        <input type="hidden" value="" name="plan_for_see" id="seeplan_for">
                                        <input type="hidden" value="" name="policy_id_see" id="seepolicyid">
                                        <input type="hidden" value="" name="creditor_id_see" id="seecreditorid">
                                        <input type="hidden" value="" name="total_premium_data" id="total_premium_datas">
                                        <input type="submit" style="display:none;">
                                        <a id="see_details_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>" onclick="see_details('<?php echo encrypt_decrypt_password($single_policy['policy_id']) ?>','<?php echo encrypt_decrypt_password($single_policy['plan_id']); ?>','<?php echo encrypt_decrypt_password($single_policy['creditor_id']) ?>')">
                                            <p class="p_more_plans_arrow">See Details </p><img class="p_more_plans_arrow_img" src="/assets/images/see_plan.png">

                                    </form>


                                    </a>

                                </div>
                                <!-- <div class="col-md-1 col-1 cover_underline">
										<span class="text_dash_grey">|</span>
									</div> -->
                                <div class="col-md-3 col-12 pad_left text-right">
                                    <div class="agreement-checkbox margin_top_checkbox_card">
                                        <div>
                                            <input type="checkbox" name="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>" id="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>" data-planid="<?php echo $single_policy['plan_id']; ?>" class="compare-checkbox" value="<?php echo encrypt_decrypt_password($single_policy['plan_id']); ?>">
                                            <label for="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>">Add to Compare</label>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!------------accordian more plans----------------->
                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                            <div class="card-body card_body_padding margin_bottom_more_plan_card">
                                <!----------------card more plans start------------------->
                                <!-----------card start-------------->
                                <div class="col-md-12" class="checkout-form">

                                    <div class="single-block bg_light_pink_more_plan">
                                        <div class="row mb-40 margin_top_feature">
                                            <div class="col-md-5">
                                                <div class="row">
                                                    <div class="col-md-10 product_logo_title_more_plan">
                                                        <p class="font_p_main_more_plan" id="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>_plan_name" style="margin-left: -11px;">Care Advantage
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex-row1 row_cover_premium_product_more_plan">
                                                    <div class="col-md-5 no_padding">
                                                        <div class="border_right_product_cover">
                                                            <p class="line_height_p_product">Cover</p>
                                                            <p class="p_line_sub_product"><i class="fa fa-inr"></i> 4.2L</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7 no_padding">
                                                        <div class="border_left_product_cover">
                                                        <p class="line_height_p_product">Premium</p>
                                                        <p class="p_line_sub_product"><i class="fa fa-inr"></i> 10,300 <span>/
                                                                year</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="col-md-7 margin_internal_card_copay">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <a href="#">
                                                            <div class=" border_box_more_plan font_l_box mar_left_12" data-toggle="tooltip" data-placement="right" title="" data-original-title="A specific percent of (00% of) claim amount is paid by the Insured person.">
                                                                <img src="/assets/images/copay.png" class="margin_img_inline">
                                                                <span class="text-left font_feature_p_card"><b>Copay</b><span class="color_grey display_block mar_color_grey">0%</span></span>
                                                            </div>
                                                        </a>

                                                    </div>
                                                    <div class="col-md-4">
                                                        <a href="#">
                                                            <div class=" border_box_more_plan font_l_box mar_0" data-toggle="tooltip" data-placement="right" title="" data-original-title="Expenses incurred for room & boarding of a Hospital room.">
                                                                <img src="/assets/images/roomrent.png" class="margin_img_inline">
                                                                <span class="text-left font_feature_p_card"><b>Room
                                                                        Rent</b><span class="color_grey display_block mar_color_grey">No
                                                                        Limit</span></span>
                                                            </div>
                                                        </a>

                                                    </div>
                                                    <div class="col-md-4">
                                                        <a href="#">
                                                            <div class="font_l_box mar_left__14" data-toggle="tooltip" data-placement="left" title="" data-original-title="A specific percent of claim amount is paid by the Insured person in case the treatment is taken at a hospital not mentioned in the Network Hospitals list.">
                                                                <img src="/assets/images/cashless.png" class="margin_img_inline">
                                                                <span class="text-left font_feature_p_card"><b>Cashless
                                                                        Hospital</b><br><span class="color_grey display_block mar_color_grey">16+</span></span>
                                                            </div>
                                                        </a>

                                                    </div>



                                                    <div class="col-md-4">
                                                        <a href="#">
                                                            <div class=" border_box_more_plan font_l_box mar_left_12" data-toggle="tooltip" data-placement="right" title="" data-original-title="Medical expenses incurred for any illness diagnosed or diagnosable can be claimed after the initial waiting period of 00 Days except in case of accident/bodily injury.">
                                                                <img src="/assets/images/waiting.png" class="margin_img_inline">
                                                                <span class="text-left font_feature_p_card"><b>Waiting
                                                                        Period</b><span class="color_grey display_block mar_color_grey">3
                                                                        Years</span></span>
                                                            </div>
                                                        </a>

                                                    </div>
                                                    <div class="col-md-4">
                                                        <a href="#">
                                                            <div class=" border_box_more_plan font_l_box mar_0" data-toggle="tooltip" data-placement="right" title="" data-original-title="Increase in SI by 00% for every claim free year for a maximum of 00% without increase in premium.">
                                                                <img src="/assets/images/renewal.png" class="margin_img_inline">
                                                                <span class="text-left font_feature_p_card"><b>Renewal
                                                                        Bonus</b><br><span class="color_grey display_block mar_color_grey">10.0%</span></span>
                                                            </div>
                                                        </a>

                                                    </div>
                                                    <div class="col-md-4 no_padding">
                                                        <a href="#"><button class="solid-button-buy_more_plan">Buy
                                                                Now</button></a>

                                                    </div>
                                                </div>
                                                <!------------end feature row----------->
                                            </div>
                                            <!----------col-md-8---------->
                                        </div>
                                        <div class="col-md-12 mt-12">
                                            <hr class="hr_margin_que">
                                        </div>
                                        <div class="container-fluid box_post_back bg_light_pink_more_plan">
                                            <div class="row">
                                                <div class="col-md-4 col-5">
                                                    &nbsp;
                                                </div>

                                                <!-- <div class="col-md-1 col-1 cover_underline">
										<span class="text_dash_grey">|</span>
									</div> -->
                                                <div class="col-md-5 col-5 text-center padding_see_details_more_plan" data-toggle="modal" data-target="#myModal" style="cursor: pointer;">
                                                    <a href="quotedetails.php">
                                                        <p class="p_more_plans_arrow">See Details </p><img class="p_more_plans_arrow_img" src="assets/images/see_plan_more.png">
                                                    </a>
                                                </div>
                                                <!-- <div class="col-md-1 col-1 cover_underline">
										<span class="text_dash_grey">|</span>
									</div> -->
                                                <div class="col-md-3 col-5 pad_left text-right">
                                                    <div class="agreement-checkbox margin_top_checkbox_card">
                                                        <div>
                                                            <input type="checkbox" id="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>" class="compare-checkbox">
                                                            <label for="compare_one_<?php echo encrypt_decrypt_password($single_policy['policy_id']); ?>">Add to
                                                                Compare</label>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>



                                    </div> <!-- /.single-block -->


                                </div> <!-- /card end - -->
                                <!--------------------------------------------------------->


                                <!----------------end more plans end---------------------->
                            </div>
                            <div class="row text-center margin_bottom_hide_plan_btn_row">
                                <a onclick="$('#collapseOne').collapse('hide')" href="#insurance-plan-1" class="btn btn_hide_plans">Hide plans <img src="images/hide_plan.png" class="img_hide_btn"></a>
                            </div>
                        </div>
                        <!------------end accordian more plans------------->

                    </div> <!-- /.single-block -->


                </div> <!-- /card end - -->
                <?php
                foreach ($single_policy as $single_sub_policy) {
                    $i++;
                }
            }
            ?>



        </div>
        <!--------end 9 col--------------->
        <!-----------card start-------------->
        <div class="col-md-3" class="checkout-form" style="    margin-top: -21px;">
            <!-- <div class="single-block recommend_product_block">
                                <div class="feature-offer-box access-feature js-tilt" style="will-change: transform; transform: perspective(300px) rotateX(0deg) rotateY(0deg);">
                                    <div class="icon-box">&nbsp;</div>
                                    <div class="product_title_p_bor_modal_recommed">
                                        <h4 class="title" style="margin:-28px 17px 8px;">Get your personalised Plan</h4>
                                    </div>
                                    <p style="line-height: 18px;color: #c0c0c0;font-weight: 400;">Lorem ipsum dolor si met, an dusino si sinconstituto mir set gil amilu.Lorem ipsum dolor si met, an dusino si sinconstituto mir set gil amilu.Lorem ipsum dolor si met, an dusino si sinconstituto mir set gil amilu.</p>




                                </div>
                                <div class="col-md-12 col-5" style="text-align: center;">
                                    <a href="#" data-toggle="modal" data-target="#mb-3-w"><button class="solid-button-buy_m" style="margin: 13px 12px;">Recommend</button></a>
                                </div>

                            </div> -->
            <div class="quote-vc ">
                <img src="/assets/images/vector-quote.png" width="300">
                <span>All premium in plans are gst inclusive</span>
            </div>
        </div> <!-- /card end - -->
    </div> <!-- /.row -->
</div> <!-- /.container -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">
            <div class="modal-header bg_more_header_filters" style="border-bottom: 1px solid #fff;">
                <div class="product_title_p_bor_modal_filters">
                    <h5 class="modal-title modal_title_margin text-center">Hey User, Take a minute and review your
                        cart before you proceed</h5>
                </div>

                <button type="button" class="btn btn-white border_radius_modal" data-dismiss="modal"><i class="fa fa-close"></i></button>

            </div>
            <div class="modal-body p-lg modal_body_padding_filters_product modal_scroll_filter_product">
                <div class="row">
                    <div class="col-md-12">

                        <section class="light">
                            <div class="row" style="margin-bottom: 1%;">
                                <div class="col-md-8">
                                    <h5 class="text_title_filter p_modal_title_bg_filters_product plans_for"> </h5>
                                </div>

                            </div>

                            <div class="cart_review_data">


                            </div>
                            <p style="float: left; display:none" class="add_plan"> Additional Plans </p>
                            </br>
                            <div class="cart_review_data_additional">


                            </div>


                        </section>
                        <div class="modal-footer tex-center" style="background-color: none !important;">
                            <div class="col-md-4 col-6">
                                <div class="pr-card-vr">
                                    <p class="total_premium_p_s">Total Premium</p>
                                    <p class="total_p_red_rs_p_s total_premium"><i class="fa fa-inr"></i> 10,513</p>
                                </div>
                            </div>
                            <div class="col-md-5 col-6">
                                <button class="btn btn_p_s_pay_now cart_continue">Continue <i class="fas fa-long-arrow-alt-right rht-aw" aria-hidden="true"></i></button>
                            </div>

                            <div class="">

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<form action="<?php echo base_url() . "quotes/generate_proposal?lead_id=".$_REQUEST['lead_id']; ?>" id="hiddenCardForm" method="POST">
    <input type="hidden" name="plan_id" id="hiddenplanid">
    <input type="hidden" name="cover" id="hiddencover">
    <input type="hidden" name="premium" id="hiddenpremium">
    <input type="hidden" name="plan_name" id="hiddenplanname">
    <input type="hidden" name="policy_id" id="hiddenpolicyid">
    <input type="hidden" name="policy_sub_type_id" id="hiddenpolicysubtypeid">
    <input type="submit" style="display:none;">
</form>
<input type="hidden" id="plans_for_count_get" value="<?php echo count($group_by_policies); ?>">
<script type="text/javascript">

    let memberTypeSelected='';
    $(document).ready(function() {

        $(document).on("click", ".cardBuyNow", function() {
            debugger;
            var policy_id = $(this).data("policyid");
            var plan_id = $(this).data("planid");

            var cover = $('#compare_one_' + policy_id + '_cover').find(':selected').text();
            var planname = $('#compare_one_' + policy_id + '_plan_name').text().trim();
            var premium = $('#select_premium_' + policy_id).find(':selected').text();

            checkCDbalance(plan_id,premium).then(e => {

                if(e !== 'true'){
                    swal("Alert", e, "warning");
                    return;
                }else{

                    data = {};

                    data.plan_id = $(this).data("planid");
                    data.policy_id = policy_id;
                    data.cover = $('#compare_one_' + policy_id + '_cover').find(':selected').text();
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
                            $(".cart_review_data").html('');
                            $(".cart_review_data_additional").html('');
                            var cnt=1;
                            for (i = 0; i < res.length; i++) {
                                memberTypeSelected=res[i].memberTypeSelected;
                                if ((res[i].is_combo == 1 && res[i].is_optional == 0) || (res[i].is_combo == 0 && res[i].is_optional == 0) || (res[i].is_combo == 1 && res[i].is_optional == 1)) {
                                    $(".add_plan").hide();
                                    str = '<input type="hidden" id="hdn_memType'+cnt+'" value="'+res[i].family_construct+'"><p style="float: left;"> ' + res[i].policy_sub_type_name + '</p><span id="constraint'+cnt+'"> ('+res[i].member_typeName+')</span>';
                                    str += '<div class=""><br><div class="col-md-12 col-12 pad_left text-right"><div class="agreement-checkbox margin_top_checkbox_card chc-101"><div><input type="checkbox" checked readonly name = "chk_status[]" id= "checkbox_' + k + '" disabled class="compare-checkbox1 disabled-checkbox" value="' + res[i].policy_id + '"><label for="checkbox_' + k + '" class="col-md-12"> <div class="row bg_cart col-md-12" style="margin-top: -2% !important;border: 1px solid #e2e2e2;margin: 2px;"><span class = "addition_plan" style = "display:none">0</span><div class="col-md-2 col-6"> <div class="logo_add float_left_addon_c_cart"><img class="contain border_radius_50_l" src=' + res[i].creditor_logo + ' width="34"></div> </div> <div class="col-md-3 text-left col-6" style="margin: 0 -18px 0 17px !important;"> <p class="text-black font_14 font_family_bold_quote">Cover</p> <div class="product_title_p_bor_pop_right_buy_f_cart"> <p class="text-black font_13 font_family_bold_quote"><span class="color_red covers"><i class="fa fa-inr"></i> ' + res[i].cover + '</span> </p> </div> </div> <div class="col-md-3 text-left col-6" style="margin: 0 14px 0 -13px !important;"> <p class="text-black font_14 font_family_bold_quote">Premium</p> <div class="product_title_p_bor_pop_right_buy_f_r"> <p class="text-black font_13 font_family_bold_quote"><span class="color_red premiums"><i class="fa fa-inr"></i> ' + res[i].premium + '</span> </p> </div> </div> <div class="col-md-3 text-left col-6" style=" margin: 0 -46px 0 14px !important"> <p class="text-black font_14 font_family_bold_quote">Tenure</p> <p class="text-black font_13 font_family_bold_quote">1 year</p> </div></div></label></div></div></div></div>';
                                    $(".cart_review_data").append(str);
                                    total += parseFloat(res[i].premium);
                                }
                                if (res[i].is_combo == 0 && res[i].is_optional == 1) {
                                    $(".add_plan").show();
                                    if (res[i].already_avail == 1) {
                                        checked = 'checked';
                                        total += parseFloat(res[i].premium);

                                    } else {
                                        checked = '';
                                    }
                                    str1 = '<input type="hidden" id="hdn_memType'+cnt+'" value="'+res[i].family_construct+'"><p style="float: left;"> ' + res[i].policy_sub_type_name + '</p><span id="constraint'+cnt+'">('+res[i].member_typeName+')</span><span class = "addition_plan" style = "display:none">1</span>';
                                    str1 += '<div class=""><br><div class="col-md-12 col-12 pad_left text-right"><div class="agreement-checkbox margin_top_checkbox_card chc-101"><div><input type="checkbox" name = "chk_status[]" id= "checkbox_' + k + '" class="compare-checkbox1" value="' + res[i].policy_id + '" ' + checked + '><label for="checkbox_' + k + '" class="col-md-12"> <div class="row bg_cart col-md-12" style="margin-top: -2% !important;border: 1px solid #e2e2e2;margin: 2px;"><span class = "addition_plan" style = "display:none">1</span><div class="col-md-2 col-6"> <div class="logo_add float_left_addon_c_cart"><img class="contain border_radius_50_l" src=' + res[i].creditor_logo + ' width="34"></div> </div> <div class="col-md-3 text-left col-6" style="margin: 0 -18px 0 17px !important;"> <p class="text-black font_14 font_family_bold_quote">Cover</p> <div class="product_title_p_bor_pop_right_buy_f_cart"> <p class="text-black font_13 font_family_bold_quote"><span class="color_red covers"><i class="fa fa-inr"></i> ' + res[i].cover + '</span> </p> </div> </div> <div class="col-md-3 text-left col-6" style="margin: 0 14px 0 -13px !important;"> <p class="text-black font_14 font_family_bold_quote">Premium</p> <div class="product_title_p_bor_pop_right_buy_f_r"> <p class="text-black font_13 font_family_bold_quote "><span class="color_red premiums"><i class="fa fa-inr"></i> ' + res[i].premium + '</span> </p> </div> </div> <div class="col-md-3 text-left col-6" style=" margin: 0 -46px 0 14px !important"> <p class="text-black font_14 font_family_bold_quote">Tenure</p> <p class="text-black font_13 font_family_bold_quote">1 year</p> </div></div></label></div></div></div></div>';
                                    $(".cart_review_data_additional").append(str1);
                                }
                                k++;
                                cnt++;
                            }

                            $('.total_premium').html(total.toFixed(2));
                            // $('input[name="chk_status[]').trigger('change');
                            $('#exampleModal').modal('show');

                            $('#hiddenplanid').val(plan_id);

                            $('#hiddenpolicyid').val(policy_id);
                            $('.plans_for').text(' <?php echo str_replace(',', '+', $members_string);  ?>');

                            $('#hiddencover').val(cover);
                            $('#hiddenplanname').val(planname);
                            $('#hiddenpremium').val(total);
                        }


                    });
                }

            });




        });

        function checkCDbalance(plan_id,premium) {

            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: "/quotes/checkCDbalanceThreshold",
                    type: "POST",
                    async: false,
                    dataType: "json",
                    data:{plan_id,premium},
                    success: function(response) {
                        if(response.status == 200){
                            resolve(response.msg);
                        }else{
                            resolve(response.msg);

                        }
                    }


                });
            });
        }
    });
    $(document).ready(function() {
        $("body").on("change", 'input[name="chk_status[]"]', function() {
            var ids = $(this).attr('id');
            var total = $(".total_premium").text();
            // $('.total_premium').html('');
            var abc = $('#' + ids).closest('div').find('.premiums').text();
            if ($("#" + ids).is(':checked')) {


                var add_premium = parseFloat(abc) + parseFloat(total);
                $('.total_premium').html(add_premium.toFixed(2));
                $('#hiddenpremium').val(add_premium.toFixed(2));
            } else {

                var sub_premium = parseFloat(total) - parseFloat(abc);
                $('.total_premium').html(sub_premium.toFixed(2));
                $('#hiddenpremium').val(sub_premium.toFixed(2));
            }


        });

    });
    $("body").on("click", '.cart_continue', function() {
        debugger;

        var plan_id = $("#hiddenplanid").val();


        var total_premium = $('.total_premium').text();
        data = {};


        var TableData = [];
        var j=1;
        var err=false;
        $('input[name="chk_status[]"]:checked').each(function(row) {
            var ids = $(this).attr('id');
            console.log(ids);
            if($('#'+ids).prop('checked')==true){
                err= true;
            }
            var hdn_memType=$("#hdn_memType"+j).val();
            //memberTypeSelected

            //memberTypeSelected=memberTypeSelected.split(",");
            hdn_memType=hdn_memType.split(",");
            /*if(memberTypeSelected != hdn_memType){
                err=false;
                swal("Alert", "You can only select the policy with <?php echo $members_string?>.", "warning");
                return;
            }*/
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
            j++
        });
        if(err == true){
            data.policy_details = TableData
            $.ajax({
                url: "/quotes/create_policy_member_plan",
                type: "POST",
                async: false,
                data: data,
                dataType: "json",
                success: function(response) {
// return;
                    $("#hiddenCardForm").submit();
                }





            });
        }else{
            swal("Alert", "Please Select the Policy.", "warning");
        }

    });


    function see_details(policy_id, plan_id, creditor_id) {
        debugger
        var policy_id = policy_id;
        var href = $("#see_details_" + policy_id).attr('href');
        var cover = $('#compare_one_' + policy_id + '_cover').find(':selected').text();
        var premium = $('#select_premium_' + policy_id).find(':selected').text();
        var plan_for = ' <?php echo str_replace(',', '+', $members_string);  ?>';

        data = {};


        data.policy_id = policy_id;
        data.cover = cover;
        data.plan_id = plan_id;
        $.ajax({
            url: "/quotes/get_all_premium",
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
                var arr = [];
                for (i = 0; i < res.length; i++) {
                    // console.log(res[i].member_id);
                    if ((res[i].is_combo == 1 && res[i].is_optional == 0) || (res[i].is_combo == 0 && res[i].is_optional == 0)) {

                        arr.push(res[i].policy_id);
                        total += parseFloat(res[i].premium);
                    }
                    if (res[i].is_combo == 0 && res[i].is_optional == 1) {

                        if (res[i].already_avail == 1) {
                            arr.push(res[i].policy_id);
                            total += parseFloat(res[i].premium);

                        } else {

                        }

                    }

                }

                $('#total_premium_datas').val(total.toFixed(2));
                $("#seecover").val(cover);
                $("#seepolicy_arr").val(arr);
                $("#seepremium").val(premium);
                $("#seepolicyid").val(policy_id);
                $("#seeplanid").val(plan_id);
                $("#seecreditorid").val(creditor_id);

                $("#seeplan_for").val(plan_for);
                $("#seeCardForm").submit();


            }


        });

    }
    $('.dropdown_sum_insured').change(function() {
        var val = $(this).val();
        var val_name = $(this).attr('name');
        updatePremimum(val, val_name);
        // alert(val_name);
        // $(this).val('407,200000');
    });

    function updatePremimum(val, val_name) {

        data = {};
        data.plan_sa = val;
        //   alert(val);
        $("#select_premium_" + val_name).val(val);
    }
</script>