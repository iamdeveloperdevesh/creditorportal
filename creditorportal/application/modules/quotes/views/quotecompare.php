<style>
    .table-hover tbody tr:hover {
        background: none !important;
    }

    .op-row-nw {
        background-color: rgb(255, 241, 242);
        border-radius: 7px;
        padding-top: 9px;
        padding-bottom: 10px;
        margin-bottom: 10px;
        line-height: 17px;
    }

    .cbx-nw {
        margin: -12px -4px !important;
    }

    .inr-txt3 {
        font-size: 16px;
        color: #107591;
    }

    .pd-lfr {
        padding-right: 0px !important;
    }

    .inr-txt2 {
        font-size: 13px;
        color: #107591;
    }

    @media only screen and (max-device-width : 480px) {
        .none-display {
            display: none !important;
        }

        .pd-l0 {
            padding-left: 7px;
        }

        .txt-l0 {
            line-height: 19px;
            font-size: 13px;
            font-weight: 600;
        }

        .table tr td {
            display: flow-root;
        }

        .txt112 {
            font-size: 18px;
        }

        .tbody_bg_border_th {
            font-size: 19px;
        }

        .text-black {
            margin-bottom: 0px;
        }

        .bg_th_i {
            font-size: 20px;
            letter-spacing: 1px;
        }

        .img_IC_width {
            height: auto !important;
            width: 40% !important;
            margin-top: 2px !important;
            position: relative;
            left: -28%;
        }
    }

    .quotes_compare_container .quotes_compare_container_wrapper {
        display: flex;
        flex-direction: row;
        align-items: center;
        width: 100%;
        max-width: 1298px;
        margin: 0 28%;
    }


    .m-12 {
        margin-top: -12px;
    }

    .fa-arrow-left {
        margin-top: 6px;
    }

    .img_coorect {
        width: 13%;
    }

    .table tr td {
        text-align: center;
    }

    form {
        width: 100%;
    }

    .form label {
        position: relative;
        color: #202026;
        background-color: #fff;
        font-size: 26px;
        text-align: center;
        height: 112px;
        line-height: 125px;
        display: block;
        cursor: pointer;
        border: 3px solid transparent;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        box-shadow: 0 8px 12px 0 rgb(16 24 48 / 12%);
        border-radius: 15px;
        border: 1px solid #f6f6f9;
        margin-bottom: 39px;
        height: 154px;
        padding-left: 0px;
    }

    .form label:hover {
        position: relative;
        color: #202026;
        background-color: #fff;
        font-size: 26px;
        text-align: center;
        height: 112px;
        line-height: 125px;
        display: block;
        cursor: pointer;
        border: 3px solid transparent;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        box-shadow: 0 8px 12px 0 rgb(16 24 48 / 12%);
        border-radius: 15px;
        border: 1px solid #f6f6f9;
        margin-bottom: 39px;
        height: 154px;
        padding-left: 0px;
        border: 2px solid #f2d8d9;

    }

    .form .plan input:checked+label,
    .form .payment-plan input:checked+label,
    .form .payment-type input:checked+label {
        border: 2px solid #f2d8d9;
        background-color: #fff;
        transition: 0.8s;
        border-radius: 15px;
        height: 154px;
    }

    .form .plan input+label:after,
    form .payment-plan input+label:after,
    .form .payment-type input+label:after {
        content: "\f067";
        width: 25px;
        height: 25px;
        line-height: 34px;
        border-radius: 100%;
        border: 1px solid #9498b5;
        background-color: #9498b7;
        z-index: 999;
        position: absolute;
        top: 88px !important;
        right: 41px;
        color: #fff;
        font-family: font-awesome;
        font-size: 18px;
    }

    .form .plan input:checked+label:after,
    form .payment-plan input:checked+label:after,
    .form .payment-type input:checked+label:after {
        content: "\2713";
        width: 35px;
        height: 35px;
        line-height: 34px;
        border-radius: 100%;
        border: 2px solid #ffffff;
        background-color: #c7222a;
        z-index: 999;
        position: absolute;
        top: 87px !important;
        right: 36px;
        color: #fff;
        font-family: 'FontAwesome';
        font-size: 15px;
    }

    .cover_compare span {
        color: #ffffff;
        font-size: 12px;
        background: #c7222a;
        padding: 10px 12px;
        margin-top: -7px !important;
        border-radius: 6px;
        line-height: 19px;
    }

    body {
        /* Set "my-sec-counter" to 0 */
        counter-reset: my-sec-counter;
    }

    table {

        position: relative;
    }

    .faq-tab-wrapper .faq-panel .panel .panel-heading .panel-title a:before {
        /* Increment "my-sec-counter" by 1 */
        counter-increment: my-sec-counter;
        content: counter(my-sec-counter);
        padding: 8px 15px;
    }

    .table-hover tbody tr:hover {
        background-color: #f9f4ec;
    }

    .faq-tab-wrapper .faq-panel .panel .panel-heading .panel-title .collapsed1:before {
        counter-increment: my-sec-counter;
        content: "\2713";
        padding: 9px 13px 17px 13px;
        font-size: 20px;
    }

    .table thead tr th {
        border-top: 0 !important;
        border-right: 0 !important;
        width: 25%;
        background-color: white;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #e9decd;
    }

    .title_compare_t {
        color: #c7222a !important;

        text-transform: uppercase;
        font-family: PFEncoreSansProblck;
    }

    .bg_th_i {
        background-image: linear-gradient(to right, #ffe7e7, #fff);
        padding: 12px 14px;
    }

    .table tr td {
        border-top: 1px solid #fff;
        border-right: 1px solid #dde2ec;
    }

    .table tr th {
        border-top: 1px solid #fff;
        border-right: 1px solid #dde2ec;
        color: #3e593c;
        font-family: PFEncoreSansProblck;
        font-size: 16px;
        position: inherit;

    }

    div.sticky {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 9;
    }

    .scope_row {
        background: transparent;
    }

    .table td {
        /* padding: 1.65rem; */
        /* padding: 14px 0; */
        padding: 12px 36px;
        vertical-align: top;
        border-top: 1px solid #e9ecef;
        text-align: left;
        font-size: 15px;
        color: #000;
        font-family: 'PFEncoreSansProblck';
    }

    /*table tr th .tbody_bg_border_th :after{
    content: '';
    height: 18.6%;
    width: 4px;
    position: absolute;
    left: 3px;
    top: 0;
    background-color: #fecc28;
    border-radius: 50px;
}*/
    .tbody_bg_border_th {
        border-left: 6px solid #f5bc00;
        /*padding: 0 12px;*/
        padding-left: 10px;
        border-radius: 4px;
    }

    .tbody_bg_border_th_bor_bootom {
        border-bottom: 2px dotted #3e593c;
        padding: 0px;
        margin: -19px 0 0 17px;
        width: fit-content;
        color: transparent;
    }

    .table tbody+tbody {
        border-top: 2px solid #fff;
    }

    table tr td:last-child {
        border-left: 0;
    }

    .arris {
        border: none;
    }

    select {
        border: none;
        /*border-bottom: 4px solid #0baeee;*/
        padding-left: 0;
        padding-right: 0;
        color: #000;
        background: none;
        font-size: 20px;
        font-weight: 600;
    }

    select option {
        color: #202026;
        font-size: 18px;
    }

    .fixed {
        position: fixed;
        top: 0;
        margin-left: 0px !important;
        background: rgb(255, 255, 255);
        z-index: 99999;
    }

    /*.faq-tab-wrapper .faq-panel .panel .panel-heading.active-panel .panel-title a:before {
        content: "\2713";
     position: absolute; 
    
    font-size: large;
    
    padding: 7px 14px;
}*/
    /* Fixed Headers */

    th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    th[scope=row] {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 1;
    }

    th[scope=row] {
        vertical-align: top;
        color: inherit;
        background-color: inherit;
        /*background: linear-gradient(90deg, transparent 0%, transparent calc(100% - .05em), #d6d6d6 calc(100% - .05em), #d6d6d6 100%);*/
    }

    table th {
        border-right: 1px solid #fff;
    }

    table:nth-of-type(2) th:not([scope=row]):first-child {
        left: 0;
        z-index: 3;
        background: linear-gradient(90deg, #666 0%, #666 calc(100% - .05em), #ccc calc(100% - .05em), #ccc 100%);
    }

    table tr td:first-child,
    table tr th:first-child {
        border-left: 0;
        width: 27.6%;
        padding-top: 19px;
    }

    .btn_preoceed_product_fix_compare {
        background: #fff1f1 !important;
        box-shadow: none !important;
    }

    .img_IC_width {
        width: 20%;
        height: auto;
        border-radius: 10px !important;
        border-top: -5px !important;
    }

    .product_logo_title {
        border-radius: 10px;
        margin-top: -31px;
    }

    .p_compare_title {
        font-size: 27px;
    }

    @media only screen and (max-device-width : 480px) {
        .wd-101 {
            width: 100%;
        }

        .dis-none11 {
            display: none !important;
        }

        .pd-0 {
            padding: 8px 9px !important;
        }

        .container-fluid {
            padding: 0px
        }

    }
</style>
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

    <div class="shrt-menu shrt-menu-one light-bg text-dark">


        <!---------------------end header--------------->

        <!-- 
			=============================================
				Theme Main Banner One
			============================================== 
			-->

        <div id="theme-banner-one">

            <!-- <img src="images/shape/oval-1.svg" alt="" class="oval-one">
				<img src="images/shape/shape-1.svg" alt="" class="shape-three">
				<img src="images/shape/shape-55.svg" alt="" class="shape-four">
				<img src="images/shape/shape-56.svg" alt="" class="shape-five">
				<img src="images/shape/shape-57.svg" alt="" class="shape-six">
				<img src="images/shape/shape-58.svg" alt="" class="shape-seven">
				<img src="images/shape/shape-59.svg" alt="" class="shape-eight">
				<img src="images/shape/shape-60.svg" alt="" class="shape-nine">
				<img src="images/shape/shape-61.svg" alt="" class="shape-ten">
				<img src="images/shape/shape-62.svg" alt="" class="shape-eleven"> -->


            <!-- 
			=============================================
				Our Service
			============================================== 
			-->
            <div id="theme-banner-one">

                <!-- <img src="images/shape/oval-1.svg" alt="" class="oval-one">
				<img src="images/shape/shape-1.svg" alt="" class="shape-three">
				<img src="images/shape/shape-55.svg" alt="" class="shape-four">
				<img src="images/shape/shape-56.svg" alt="" class="shape-five">
				<img src="images/shape/shape-57.svg" alt="" class="shape-six">
				<img src="images/shape/shape-58.svg" alt="" class="shape-seven">
				<img src="images/shape/shape-59.svg" alt="" class="shape-eight">
				<img src="images/shape/shape-60.svg" alt="" class="shape-nine">
				<img src="images/shape/shape-61.svg" alt="" class="shape-ten">
				<img src="images/shape/shape-62.svg" alt="" class="shape-eleven"> -->
                <!--
			=====================================================
				Our Pricing
			=====================================================
			-->
                <div class="agn-our-pricing pb-200">
                    <!-- <img src="images/shape/shape-55.svg" alt="" class="shape-one">
				<img src="images/shape/shape-62.svg" alt="" class="shape-two">
				<img src="images/shape/shape-1.svg" alt="" class="shape-three">
				<img src="images/shape/shape-60.svg" alt="" class="shape-four">
				<img src="images/shape/shape-57.svg" alt="" class="shape-five"> -->
                    <div class="container">

                        <div class="theme-title-one text-center">
                            <a href="/quotes/" class="go_back_quote compare_back">
                                <p><i class="fa fa-arrow-left font_size_go_back"></i>&nbsp;Go Back</p>
                            </a>
                            <!-- <div class="upper-title">Compare Benefits and Features</div> -->
                            <h2 class="main-title comp_m_top_line">&nbsp;</h2>
                        </div> <!-- /.theme-title-one -->

                    </div> <!-- /.container -->



                    <div class="tab-content tab-content_mt_comapre container-fluid">
                        <!-- ^^^^^^^^^^^^^^^^^^^^^ Monthly ^^^^^^^^^^^^^^^^^^^^^^^^^^^ -->
                        <div id="month" class="tab-pane fade show active">
                            <div class="table-wrapper">
                                <table class="table table-hover">
                                    <thead id="product-comparison-header">

                                    <!-- <div class="none-web">
                                        <p class="p_compare_title">Product Comparision</p>
                                        <div class="row">
                                            <div class="col-4">

                                            </div>
                                            <div class="col-8">
                                                <div></div>
                                                <div></div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="sticky">
                                        <tr>
                                            <th scope="row" class="none-display">
                                                <div class="compare_t_bor_l dis-none11">
                                                    <p class="p_compare_title">Product Comparision</p>
                                                </div>
                                                <div class="agreement-checkbox_compare margin_top_checkbox_card" style="visibility: hidden;">
                                                    <div>
                                                        <input type="checkbox" id="compare_one" class="compare-checkbox">
                                                        <label for="compare_one">Show Difference</label>
                                                    </div>

                                                </div>
                                                <button type="button" class="btn  btn_preoceed_product_fix_compare" style="visibility: hidden;"><img src="/assets/images/print.png" class="img_download_compare">
                                                    Download</button>
                                            </th>
                                            <?php
                                            foreach ($compare_data as $single_plan) {
                                                // echo "<PRE>";print_r($single_plan);
                                                ?>
                                                <th scope="row" class="pd-0">
                                                    <div class="row price_IC_box text-center wd-101">
                                                        <!-- <a href="#" class="remove_IC"><i class="fa fa-close"></i></a> -->
                                                        <div class="col-md-12">
                                                            <img src="<?php echo ($single_plan['creditor_logo'] != "") ? $single_plan['creditor_logo'] : base_url() . "assets/images/ad-logo.png"; ?>" alt="" class="margin_12_t icon_img_compare img_IC_width">
                                                            <p class="text-black txt-l0">

                                                                <?php echo $single_plan['creditor_name'] . ' - ' . $single_plan['plan_name']; ?>

                                                            </p>
                                                        </div>
                                                        <div class="col-md-12 m-12 pd-l0">
                                                            <form action="<?php echo base_url() . "quotes/generate_proposal" ?>" id="hiddenCardForm_compare" method="POST">
                                                                <input type="hidden" value="<?php echo $single_plan['plan_id']; ?>" name="plan_id" id="hiddenplanid">
                                                                <input type="hidden" value="<?php echo $single_plan['sum_insured'][0]; ?>" name="cover" id="hiddencover">
                                                                <input type="hidden" value="<?php echo $single_plan['premium']; ?>" name="premium" id="hiddenpremium">
                                                                <input type="hidden" value="<?php echo $single_plan['creditor_name'] . ' - ' . $single_plan['plan_name']; ?>" name="plan_name" id="hiddenplanname">
                                                                <input type="hidden" value="<?php echo encrypt_decrypt_password($single_plan['policy_id']); ?>" name="policy_id" id="hiddenpolicyid">
                                                                <!-- <input type="submit" style="display:none;"> -->
                                                            </form>
                                                            <a href="#"><button id = "" class="theme-button-two_compare text-center" onclick = "buy_now('<?php echo encrypt_decrypt_password($single_plan['policy_id']); ?>','<?php echo $single_plan['plan_id']; ?>')"><i class="fa fa-inr"></i><span class="total_premium_<?php echo $single_plan['plan_id']; ?>">
                                                                            <?php echo $single_plan['total_premium']; ?></span><span>/annual</span></button></a>



                                                        </div>
                                                    </div>
                                                </th>

                                                <?php
                                            }
                                            ?>
                                        </tr>
                                    </div>
                                    </thead>
                                    <tr>
                                        <td colspan="<?php echo count($compare_data) + 1 ?>"></td>
                                    </tr>
                                    <tbody class="tbody_bg">
                                    <tr>
                                        <th colspan="<?php echo count($compare_data) + 1 ?>" class="title_compare_t">
                                            <span class="bg_th_i">Plan Details</span>
                                        </th>
                                    </tr>

                                    <tr>
                                        <th scope="row"><span class="tbody_bg_border_th">Sum Insured </span>
                                            <!-- <div class="tbody_bg_border_th_bor_bootom">Sum Insured &nbsp;</div> -->
                                        </th>
                                        <?php
                                        foreach ($compare_data as $i=>$single_plan) {
                                            ?>
                                            <td>
                                                <select class="sum_insured_<?php echo $single_plan['plan_id'] ;?>" onchange = "sum_inured('<?php echo $single_plan['plan_id'];?>','<?php echo encrypt_decrypt_password($single_plan['policy_id']) ;?>',this.value,'<?php echo $single_plan['policy_id'];?>','<?php echo $i; ?>')">
                                                    <?php
                                                    foreach ($single_plan['sum_insured'] as $single_single_insured) {
                                                        ?>
                                                        <option value = "<?php echo $single_single_insured; ?>"><?php echo $single_single_insured; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    <tr class="scope_row">
                                        <th scope="row"><span class="tbody_bg_border_th">Tenure </span>
                                            <!-- <div class="tbody_bg_border_th_bor_bootom">Tenure &nbsp;</div> -->
                                        </th>
                                        <?php
                                        foreach ($compare_data as $single_plan) {
                                            ?>
                                            <td>
                                                <p class="text-black txt112"><?php echo $single_plan['tenure']; ?> Year</p>
                                            </td>
                                            <?php
                                        }
                                        ?>

                                    </tr>
                                    <tr>
                                        <td colspan="<?php echo count($compare_data) + 1 ?>"></td>
                                    </tr>
                                    </tbody>

                                    <tr>
                                        <td colspan="<?php echo count($compare_data) + 1 ?>"></td>
                                    </tr>


                                    <tbody class="tbody_bg">
                                    <tr>
                                        <th colspan="<?php echo count($compare_data) + 1 ?>" class="title_compare_t">
                                            <span class="bg_th_i">ADDITIONAL FEATURES</span>

                                        </th>
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <span class="tbody_bg_border_th">Add Ons</span>

                                        </th>
                                        <?php
                                        $showDivFlag=false;
                                        foreach ($compare_data as $j=>$single_plan) {
                                            ?>
                                            <td >
                                                <div class="js_appned_plan<?php echo $j; ?>">
                                                    <?php
                                                    if (isset($single_plan['add_on'])) {
                                                    foreach ($single_plan['add_on'] as $key => $single_add_on) {
                                                        if(($single_add_on['is_combo'] == 0 && $single_add_on['is_optional'] == 0 )|| ($single_add_on['is_combo'] == 1 && $single_add_on['is_optional'] == 0)){

                                                            ?>
                                                            <div class="row op-row-nw">
                                                                <div class="col-md-6  text-left pd-lfr">
                                                                    <b class="inr-txt">
                                                                        <?php echo $single_add_on['policy_name'];?>
                                                                    </b>
                                                                </div>
                                                                <div class="col-md-6 text-right search-sec search-sec_<?php echo $single_plan['plan_id']; ?>">
                                                                    <span class = "addition_plan" style = "display:none">0</span>
                                                                    <b class="inr-txt3 premiums"><i class="fa fa-inr inr-txt2"></i>  <?php echo $single_add_on['premium'];?></b>
                                                                    <input class="inp-cbx family_members_chk" id="plan_<?php echo $key;?>" type="checkbox" data-id = "<?php echo $single_plan['plan_id']; ?>" checked  name = "chk_status[]" value="<?php echo $key;?>" disabled />
                                                                    <label class="cbx cbx-nw" for="plan_<?php echo $key;?>">
                                                            <span>
                                                                <svg width="12px" height="10px">
                                                                    <use xlink:href="#check"></use>
                                                                </svg>
                                                            </span>
                                                                    </label>
                                                                    <svg class="inline-svg">
                                                                        <symbol id="check" viewbox="0 0 12 10">
                                                                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                                        </symbol>
                                                                    </svg>
                                                                </div>
                                                            </div>

                                                            <?php
                                                        }else{
                                                            $showDivFlag = true;
                                                        }

                                                    }

                                                    ?>
                                                </div>
                                                <p class ="additional_data" <?php if ($showDivFlag===false){?>style="display:none"<?php } ?>> Additional Plans</p>
                                                <div class="js_appned_plan_additional">
                                                    <?php
                                                    //Additonal plans

                                                    foreach ($single_plan['add_on'] as $key => $single_add_on) {

                                                        if($single_add_on['is_combo'] == 0 && $single_add_on['is_optional'] == 1 )
                                                        {
                                                            if($single_add_on['already_avail'] == 1)
                                                            {
                                                                $checked = 'checked';
                                                            }
                                                            else{
                                                                $checked = '';
                                                            }
                                                            ?>
                                                            <div class="row op-row-nw">
                                                                <div class="col-md-6  text-left pd-lfr">
                                                                    <b class="inr-txt">
                                                                        <?php echo $single_add_on['policy_name'];?>
                                                                    </b>
                                                                </div>
                                                                <div class="col-md-6 text-right search-sec search-sec_<?php echo $single_plan['plan_id']; ?>">
                                                                    <span class = "addition_plan" style = "display:none">1</span>
                                                                    <b class="inr-txt3 premiums"><i class="fa fa-inr inr-txt2 "></i>  <?php echo $single_add_on['premium'];?></b>
                                                                    <input class="inp-cbx family_members_chk" id="plan_<?php echo $key;?>" type="checkbox" name = "chk_status[]" value="<?php echo $key;?>" data-id = "<?php echo $single_plan['plan_id']; ?>"  <?php echo $checked;?> />
                                                                    <label class="cbx cbx-nw" for="plan_<?php echo $key;?>">
                                               <span>
                                                   <svg width="12px" height="10px">
                                                       <use xlink:href="#check"></use>
                                                   </svg>
                                               </span>
                                                                    </label>
                                                                    <svg class="inline-svg">
                                                                        <symbol id="check" viewbox="0 0 12 10">
                                                                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                                        </symbol>
                                                                    </svg>
                                                                </div>
                                                            </div>

                                                            <?php
                                                        }

                                                    }
                                                    }
                                                    ?>
                                                </div>
                                                <!-- end of  Cover  -->
                                                <!--  Cover  -->

                                                <!-- end of  Cover  -->
                                                <!--  Cover  -->

                                                <!-- end of  Cover  -->
                                                <!--  Cover  -->

                                                <!-- end of  Cover  -->
                                                <!--  Cover  -->


                                            </td>
                                        <?php }?>
                                        <!-- new row -->
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                                <span class="tbody_bg_border_th">E-Opinion for Critical
                                                    Illnesses</span>

                                        </th>
                                        <?php
                                        foreach ($compare_data as $single_plan) {
                                            ?>
                                            <!-- <td>Covered for 15 critical illness</td> -->
                                            <td><?php
                                                if (!empty($single_plan['numbers_of_ci'])) {
                                                    echo "Cover upto " . $single_plan['numbers_of_ci'][0] . ' Critical Illnesses';
                                                } else {
                                                    ?>
                                                    <img src="/assets/images/wrong.png" class="img_coorect">
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                            <?php
                                        }
                                        ?>

                                    </tr>
                                    <tr>
                                        <td colspan="<?php echo count($compare_data) + 1 ?>"></td>
                                    </tr>

                                    <!-- end of Optional Cover -->

                                    </tbody>
                                </table>
                            </div> <!-- /.table-wrapper -->
                        </div> <!-- /#annual -->
                    </div>

                </div> <!-- /.agn-our-pricing -->


            </div> <!-- /#theme-banner-one -->

            <!--
			=====================================================
				Footer
			=====================================================
			-->


            <!-- <ul class="menu topRight">

                <li class="share bottom">
                    <i class="fa fa-share-alt share"></i>

                    <ul class="submenu">
                        <li><a href="#" data-toggle="modal" data-target="#email_m" data-toggle-class="fade-right" data-toggle-class-target="#animate" class="facebook"><i class="fa fa-envelope-o"></i></a></li>
                        <li><a href="#" data-toggle="modal" data-target="#whatsapp_m" data-toggle-class="fade-right" data-toggle-class-target="#animate" class="twitter"><i class="fa fa-whatsapp"></i></a></li>
                        
      <li><a href="#" class="googlePlus"><i class="fa fa-google-plus"></i></a></li>
      <li><a href="#" class="instagram"><i class="fa fa-instagram"></i></a></li>
                    </ul>
                </li>
            </ul> -->
            <!-- <ul class="menu topLeft">
  <a href="product.html"><li class="share bottom">
    <i class="fa fa-reply"></i>
   
  </li></a>
</ul> -->


            <!-- <button  id="mybutton"><img src="images/share.png" style="width: 25px;"></button>
	      
			<button class="scroll-top tran3s">
				<i class="fa fa-angle-up" aria-hidden="true"></i>
			</button> -->

            <!-- .modal -->
            <div id="m-a-a" class="modal fade model" data-backdrop="true">
                <div class="modal-dialog modal-lg animate" id="animate">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="bor_right_m_p_s_title_main_send_link_com">
                                <h6 class="modal-title" style="font-family: 'PFEncoreSansProblck'; font-size: 19px; margin: 12px 23px;">Add upto 3 plans to compare
                                </h6>
                            </div>
                            <button type="button" class="btn btn-white" data-dismiss="modal" style="border-radius: 50px;"><i class="fa fa-close"></i></button>
                        </div>
                        <div class="modal-body text-center p-lg" style="overflow-y: scroll; height: 400px; overflow-x: hidden;">
                            <div class="container">


                                <form class="form cf">

                                    <section class="plan cf">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="radio" name="radio1" id="free" value="free">
                                                <label class="free-label four col" for="free">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row row_logo_plan_n">
                                                                <div class="col-md-6">
                                                                    <img src="images/logo/care_health.png" alt="" class="margin_12_t_compare icon img_IC_width mt_30">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="compare_table_add_modal_p">Care Advantage</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row row_compare_matu">
                                                                <div class="col-md-6">
                                                                    <p class="p_compare_modal text-right">Maturity
                                                                        amount</p>
                                                                </div>
                                                                <div class="col-md-4 no_padding">
                                                                    <h4 class="title text-right box_btn mb-4 p_maturity_compare"><a href="#" class="p_compare_price_modal p_compare_margin"><i class="fa fa-inr"></i> 3,439</a></h4>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="plus"><i class="fa fa-plus"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" name="radio1" id="free2" value="free2">
                                                <label class="free-label four col" for="free2">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row row_logo_plan_n">
                                                                <div class="col-md-6">
                                                                    <img src="images/logo/care_health.png" alt="" class="margin_12_t_compare icon img_IC_width mt_30">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="compare_table_add_modal_p">Care Advantage</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row row_compare_matu">
                                                                <div class="col-md-6">
                                                                    <p class="p_compare_modal text-right">Maturity
                                                                        amount</p>
                                                                </div>
                                                                <div class="col-md-4 no_padding">
                                                                    <h4 class="title text-right box_btn mb-4 p_maturity_compare"><a href="#" class="p_compare_price_modal p_compare_margin"><i class="fa fa-inr"></i> 3,439</a></h4>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="plus"><i class="fa fa-plus"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" name="radio1" id="free3" value="free3">
                                                <label class="free-label four col" for="free3">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row row_logo_plan_n">
                                                                <div class="col-md-6">
                                                                    <img src="images/logo/care_health.png" alt="" class="margin_12_t_compare icon img_IC_width mt_30">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="compare_table_add_modal_p">Care Advantage</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row row_compare_matu">
                                                                <div class="col-md-6">
                                                                    <p class="p_compare_modal text-right">Maturity
                                                                        amount</p>
                                                                </div>
                                                                <div class="col-md-4 no_padding">
                                                                    <h4 class="title text-right box_btn mb-4 p_maturity_compare"><a href="#" class="p_compare_price_modal p_compare_margin"><i class="fa fa-inr"></i> 3,439</a></h4>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="plus"><i class="fa fa-plus"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" name="radio1" id="free4" value="free4">
                                                <label class="free-label four col" for="free4">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row row_logo_plan_n">
                                                                <div class="col-md-6">
                                                                    <img src="images/logo/care_health.png" alt="" class="margin_12_t_compare icon img_IC_width mt_30">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="compare_table_add_modal_p">Care Advantage</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row row_compare_matu">
                                                                <div class="col-md-6">
                                                                    <p class="p_compare_modal text-right">Maturity
                                                                        amount</p>
                                                                </div>
                                                                <div class="col-md-4 no_padding">
                                                                    <h4 class="title text-right box_btn mb-4 p_maturity_compare"><a href="#" class="p_compare_price_modal p_compare_margin"><i class="fa fa-inr"></i> 3,439</a></h4>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="plus"><i class="fa fa-plus"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" name="radio1" id="free5" value="free5">
                                                <label class="free-label four col" for="free5">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row row_logo_plan_n">
                                                                <div class="col-md-6">
                                                                    <img src="images/logo/care_health.png" alt="" class="margin_12_t_compare icon img_IC_width mt_30">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="compare_table_add_modal_p">Care Advantage</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row row_compare_matu">
                                                                <div class="col-md-6">
                                                                    <p class="p_compare_modal text-right">Maturity
                                                                        amount</p>
                                                                </div>
                                                                <div class="col-md-4 no_padding">
                                                                    <h4 class="title text-right box_btn mb-4 p_maturity_compare"><a href="#" class="p_compare_price_modal p_compare_margin"><i class="fa fa-inr"></i> 3,439</a></h4>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="plus"><i class="fa fa-plus"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" name="radio1" id="free6" value="free6">
                                                <label class="free-label four col" for="free6">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row row_logo_plan_n">
                                                                <div class="col-md-6">
                                                                    <img src="images/logo/care_health.png" alt="" class="margin_12_t_compare icon img_IC_width mt_30">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="compare_table_add_modal_p">Care Advantage</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row row_compare_matu">
                                                                <div class="col-md-6">
                                                                    <p class="p_compare_modal text-right">Maturity
                                                                        amount</p>
                                                                </div>
                                                                <div class="col-md-4 no_padding">
                                                                    <h4 class="title text-right box_btn mb-4 p_maturity_compare"><a href="#" class="p_compare_price_modal p_compare_margin"><i class="fa fa-inr"></i> 3,439</a></h4>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="plus"><i class="fa fa-plus"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </section>
                                    <!-- <section class="payment-plan cf">
				<h2>Select a payment plan:</h2>
				<input type="radio" name="radio2" id="monthly" value="monthly" checked><label class="monthly-label four col" for="monthly">Monthly</label>
				<input type="radio" name="radio2" id="yearly" value="yearly"><label class="yearly-label four col" for="yearly">Yearly</label>
			</section>
			<section class="payment-type cf">
				<h2>Select a payment type:</h2>
				<input type="radio" name="radio3" id="credit" value="credit"><label class="credit-label four col" for="credit">Credit Card</label>
				<input type="radio" name="radio3" id="debit" value="debit"><label class="debit-label four col" for="debit">Debit Card</label>
				<input type="radio" name="radio3" id="paypal" value="paypal" checked><label class="paypal-label four col" for="paypal">Paypal</label>
			</section>	 -->
                                    <!-- <input class="submit" type="submit" value="Submit">		 -->
                                </form>

                            </div>
                        </div>
                        <a href="compare_table.html">
                            <div class="modal-footer" style="padding: 35px;">

                                <button class="solid-button-one">Compare</button>
                            </div>
                        </a>
                    </div><!-- /.modal-content -->
                </div>
            </div>
            <!-- / .modal -->


            <!-- .email modal -->
            <div id="email_m" class="modal fade model" data-backdrop="true">
                <div class="modal-dialog modal-md animate" id="animate">
                    <div class="modal-content">
                        <div class="modal-header" style="border-bottom-color: #fff;">
                            <h6 class="modal-title whatsapp_title_modal">
                                &nbsp;
                            </h6>
                            <button type="button" class="btn btn-white" data-dismiss="modal" style="border-radius: 50px;"><i class="fa fa-close"></i></button>
                        </div>
                        <div class="modal-body text-center p-lg mb-50">
                            <img src="images/mail.png" class="img_whatsapp">
                            <p class="mb-15 font_24">Are you sure want to share compare plan?</p>
                            <form class="form">

                                <input type="email" class="form__field" placeholder="Enter Your Email Address">
                                <button type="button" class="btn_call btn--primary btn--inside uppercase">Share &nbsp;&nbsp;&nbsp;<i class="fa fa-share font_15"></i></button>
                                <br><br>
                            </form>
                            <!-- <div class="container text-center">
	 <a href="compare.html"><button class="btn btn-primary" data-dismiss="modal">Cancel</button></a>
	                <a href="compare.html"><button class="btn btn-primary" data-dismiss="modal">Yes</button></a>
				
		
	
</div> -->
                        </div>

                    </div><!-- /.modal-content -->
                </div>
            </div>
            <!-- / .email modal -->

            <!-- .whatsapp modal -->
            <div id="whatsapp_m" class="modal fade model" data-backdrop="true">
                <div class="modal-dialog modal-md animate" id="animate">
                    <div class="modal-content">
                        <div class="modal-header" style="border-bottom-color: #fff;">
                            <h6 class="modal-title whatsapp_title_modal">&nbsp;
                            </h6>

                            <button type="button" class="btn btn-white" data-dismiss="modal" style="border-radius: 50px;"><i class="fa fa-close"></i></button>
                        </div>
                        <div class="modal-body text-center p-lg mb-50">
                            <img src="images/whatsapp.png" class="img_whatsapp">
                            <p class="mb-15 font_24">Are you sure want to share compare plan?</p>
                            <form class="form">

                                <input type="email" class="form__field" placeholder="Enter Your Mobile Number">
                                <button type="button" class="btn_call btn--primary btn--inside uppercase">Share &nbsp;&nbsp;&nbsp;<i class="fa fa-share font_15"></i></button>
                                <br><br>
                            </form>


                        </div>

                    </div><!-- /.modal-content -->
                </div>
            </div>


        </div> <!-- /.main-page-wrapper -->
        <!-- <script>
            $(window).scroll(function(e) {
                var $el = $('#product-comparison-header');
                var isPositionFixed = ($el.css('position') == 'fixed');
                if ($(this).scrollTop() < 200 && isPositionFixed) {
                    $el.css({
                        'position': 'static',
                        'top': '0px'
                    });
                }

                if ($(this).scrollTop() > 200 && !isPositionFixed) {
                    $el.css({
                        'position': 'fixed',
                        'top': '0px'
                    });
                }
            });
        </script> -->
        <script>
            $(document).ready(function() {
                $("body").on("change", 'input[name="chk_status[]"]', function() {
                    debugger;
                    var ids = $(this).attr('id');
                    var data_id = $(this).data('id');
                    var total = $(".total_premium_"+data_id).text();
                    // $('.total_premium').html('');
                    var abc = $('#' + ids).parent('.search-sec').find('.premiums').text();
                    if ($("#" + ids).is(':checked')) {


                        var add_premium = parseFloat(abc) + parseFloat(total.trim());
                        $(".total_premium_"+data_id).html(add_premium.toFixed(2));
                        $('#hiddenpremium').val(add_premium.toFixed(2));
                    } else {

                        var sub_premium = parseFloat(total.trim()) - parseFloat(abc);
                        $(".total_premium_"+data_id).html(sub_premium.toFixed(2));
                        $('#hiddenpremium').val(sub_premium.toFixed(2));
                    }


                });

            });
            $("body").on("click", '.compare_buy', function() {



                var plan_id = $("#hiddenplanid").val();


                var total_premium = $('.total_premium').text();
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
                        $("#hiddenCardForm").submit();
                    }





                });
            });

            function buy_now(policy_ids,plan_id) {
                debugger;
                var plan_id =plan_id;

                var input_values = $(".search-sec_"+plan_id).find('input[name="chk_status[]"]:checked');
                var total_premium = $('.total_premium_'+plan_id).text();
                data = {};


                var TableData = [];
                input_values.each(function(row) {
                    var ids = $(this).attr('id');

                    // $('.total_premium').html('');
                    var policy_id = $('#'+ids).val();
                    var abc = $("#"+ids).parent('.search-sec_'+plan_id).find('.premiums').text();
                    var cover = $(".sum_insured_"+plan_id).find(':selected').val();
                    var additional_plan = $("#"+ids).parent('.search-sec_'+plan_id).find('.addition_plan').text();
                    TableData[row] = {
                        "policy_id": policy_id,
                        "premium": abc,
                        "cover": cover,
                        "plan_id": plan_id,
                        "total_premium": total_premium.trim(),
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
                        $("#hiddenCardForm_compare").submit();
                    }





                });

            }

            function sum_inured(plan_id,policy_id,e,policy_ids,id)
            {
                debugger;
                var policy_id =  policy_id;
                var cover =  e;


                data = {};


                data.policy_id = policy_id;
                data.cover = cover;
                data.plan_id = plan_id;
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
                        $(".js_appned_plan"+id).html('');
                        $(".js_appned_plan_additional").html('');
                        for (i = 0; i < res.length; i++) {
                            // console.log(res[i].member_id);
                            if ((res[i].is_combo == 1 && res[i].is_optional == 0) || (res[i].is_combo == 0 && res[i].is_optional == 0)) {
                                $(".additional_data").hide();
                                str = '<div class="row op-row-nw"> <div class="col-md-6 text-left pd-lfr"> <b class="inr-txt"> '+res[i].policy_sub_type_name+ '</b> </div> <div class="col-md-6 text-right search-sec search-sec_'+plan_id+'"> <span class = "addition_plan" style = "display:none">0</span> <b class="inr-txt3 premiums"><i class="fa fa-inr inr-txt2 "></i> '+res[i].premium+'</b> <input class="inp-cbx family_members_chk" id="plan_'+res[i].policy_id+'" type="checkbox" name = "chk_status[]" value="'+res[i].policy_id+'" data-id = "'+plan_id+'" checked /> <label class="cbx cbx-nw" for="plan_'+res[i].policy_id+'"> <span> <svg width="12px" height="10px"> <use xlink:href="#check"></use> </svg> </span> </label> <svg class="inline-svg"> <symbol id="check" viewbox="0 0 12 10"> <polyline points="1.5 6 4.5 9 10.5 1"></polyline> </symbol> </svg> </div> </div>';
                                $(".js_appned_plan"+id).append(str);
                                total += parseFloat(res[i].premium);
                            }
                            if (res[i].is_combo == 0 && res[i].is_optional == 1) {
                                $(".additional_data").show();
                                if (res[i].already_avail == 1) {
                                    checked = 'checked';
                                    total += parseFloat(res[i].premium);

                                } else {
                                    checked = '';
                                }
                                str1 = '<div class="row op-row-nw"> <div class="col-md-6 text-left pd-lfr"> <b class="inr-txt"> '+res[i].policy_sub_type_name+ '</b> </div> <div class="col-md-6 text-right search-sec search-sec_'+plan_id+'"> <span class = "addition_plan" style = "display:none">1</span> <b class="inr-txt3 premiums"><i class="fa fa-inr inr-txt2 "></i> '+res[i].premium+'</b> <input class="inp-cbx family_members_chk" id="plan_'+res[i].policy_id+'" type="checkbox" name = "chk_status[]" value="'+res[i].policy_id+'" data-id = "'+plan_id+'" checked /> <label class="cbx cbx-nw" for="plan_'+res[i].policy_id+'"> <span> <svg width="12px" height="10px"> <use xlink:href="#check"></use> </svg> </span> </label> <svg class="inline-svg"> <symbol id="check" viewbox="0 0 12 10"> <polyline points="1.5 6 4.5 9 10.5 1"></polyline> </symbol> </svg> </div> </div>';
                                $(".js_appned_plan_additional").append(str1);
                            }
                            k++;
                        }

                        $('.total_premium_'+plan_id).html(total.toFixed(2));



                    }


                });

            }
            //family_members_chk



        </script>