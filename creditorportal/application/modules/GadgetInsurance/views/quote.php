<?php
$logo=$this->session->userdata('logo');
?>
<style>
    .cmp_srt {
        display: flex;
    }
    .drpdwnWrapper {
        margin-left: 5px;
    }

    ul .dropdwnBox li { font-size: 15px;
        line-height: 1.24;
        padding: 7px 4px;
        text-align: center; }
    ul .dropdwnBox li a {
        color: #000000;
        text-decoration: none;
        background-color: transparent;
    }
    ul .dropdwnBox li a:hover {
        color: #005478;
        text-decoration: none;
    }

    .qutbox1.quotedetail {
        width: 32%;
        margin: 7px 8px;
        z-index: 1;
        background: #fff;
    }
    .sideimg-left img {
        position: absolute;
        bottom: -10%;
        z-index: -1;
    }
    .quotes h5 {
        font-size: 12px;
        line-height: 2;
    }
    .productFeatures p {
        text-align: unset;
    }
    .productFeatures p { display : flex; }
    .desktop-hidden{ display:none; }
    @media screen and (max-width: 576px){
        .qutbox1.quotedetail {
            width: 100%;
            position: relative;
        }
        .mobile-hidden{
            display: none;
        }
        .productFeatures .btn_details{
            margin-top: 7px;
        }
        .qut .border-right {
            padding-right: 9px;
        }
        .dflex-btn .dflx-left{
            flex-direction: column;
        }
        .quotebtn.cmp_srt .cmp_btn{
            height: fit-content;
        }
        .chkbxDiv {
            position: absolute;
            right: 0;
            top: 0;
        }
        .checkquote {
            position: absolute;
            right: 0;
        }
        .quotes_compare_plan_name i{
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            border-radius: 30px;
            padding: 2px 3px 4px 3px;
            font-size: xx-small;
            color: #fff;
            text-align: center;

        }
        .quotes_compare_container .quotes_compare_container_wrapper {
            flex-direction: column;
        }
        .quotes_compare_div {
            width: 100%;
        }
        .quotes_compare_remove_button{
            display: none;
        }
        .dflex.quotebottom {
            flex-direction: row;
            flex-wrap: nowrap;
        }
        .productIcon {
            width: fit-content;
        }
        .quotes_compare_plan_add, .quotes_compare_plan_name {
            padding: 7px;
            height: auto;
            width: 100%;
            flex-direction: column;
            align-items: center;
        }
        .quotes_compare_image, .quotes_compare_image1 {
            width: 55px;
            height: 51px;
            flex-direction: column;
        }
        .quotes_compare_span_plan_name, .quotes_compare_span_plan_name1 {
            font-size: 14px;
            color: #253858;
            width: 100%;
            margin-left: 0px;
            overflow: hidden;
        }
        .desktop-hidden{ display:block; }
        .compare_plan_name2 {
            height: 50px;
            font-size: 12px;
            text-align: center;
            margin-top: 6px;
        }
    }


</style>


<div class="container-fluid m-b-5 mt-5 quotes">
    <div class="row">
        <div class="col-sm-12 dflex-btn">
            <div class="d-flex dflx-left">
                <a onclick="history.back()">
                    <div class="back-btn">
                        <button><i class="fa fa-long-arrow-left"></i> Go Back</button>
                    </div>
                </a>
                <h5><?php echo count($master_policy); ?> Quotes</h5>
            </div>
            <div class="quotebtn cmp_srt">
                <button class="btn btn-default cmp_btn" type="button" onclick="compareBtncClick()">
                    <span>Compare <img src="/assets/gadget/img/vector-5.png"></span>
                </button>
                <div class="drpdwnWrapper">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span>Sort by <img src="/assets/gadget/img/vector-4.png"></span>
                    </button>

                    <ul class="dropdown-menu"  style="">
                        <div class="dropdwnBox" style="">
                            <li><a href="/GadgetInsurance/quote?Lead=<?php echo $_GET['Lead'];?>&sort=1">High To Low </a></li>
                            <li><a href="/GadgetInsurance/quote?Lead=<?php echo $_GET['Lead'];?>&sort=2">Low to High</a></li>

                        </div>
                    </ul>
                </div>

            </div>


        </div>
        <div class="sideimg">
            <img src="/assets/gadget/img/vector-2.png">
        </div>
    </div>
    <div class="row qutoebox" data-aos="zoom-in">
        <input type="hidden" id="lead_id" value="<?php echo $_GET['Lead'];?>">
        <?php
        $i = 1;
        foreach ($master_policy as $item) {
            ?>

            <div class=" qutbox1 quotedetail">
                <div class="row dflex quotebottom">
                    <div class="col-md-4 productIcon">
                        <figure>
                            <img src="<?php  echo $logo; ?>">
                        </figure>

                        <button class="btn btn-orange btn-sm mobile-hidden" data-toggle="modal"
                                onclick="seedetail(<?php echo $i; ?>,<?php echo $item->basis_type;?>);" data-target="#detailModal">See Details <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </button>
                    </div>
                    <input type="hidden" id="policy_id<?php echo $i; ?>" value="<?php echo $item->policy_id;?>">
                    <input type="hidden" id="plan_name<?php echo $i; ?>" value="<?php echo $item->plan_name;?>">
                    <input type="hidden" id="plan_id<?php echo $i; ?>" value="<?php echo $item->plan_id;?>">
                    <div class="col-md-6 productFeatures">
                        <b><?php echo $item->plan_name; ?></b>
                        <?php
                        if (count($item->features) > 0) {

                            foreach ($item->features as $key=>$arrFeature) {
                                if($key > 3){
                                    break;
                                }
                                if (strlen($arrFeature->short_description) > 45){
                                    $str = substr($arrFeature->short_description, 0, 45) . '...';
                                }else{
                                    $str=$arrFeature->short_description;
                                }


                                ?>

                              <!--  <p><img src="/assets/gadget/img/icon.png"><?php /*echo $str; */?></p>-->
                                <p><span> <img src="/assets/gadget/img/icon.png"> </span><span>  <?php echo $str; ?> </span></p>

                                <?php

                            }
                        } else { ?>
                            <p><span> <img src="/assets/gadget/img/icon.png"> </span><span>  Theft or robbery </span></p>
                            <p><span> <img src="/assets/gadget/img/icon.png"> </span><span>  Protection against accidental damage </span></p>


                        <?php }
                        ?>
                        <button class="btn btn-orange btn-sm desktop-hidden" data-toggle="modal"
                                onclick="seedetail(<?php echo $i; ?>,<?php echo $item->basis_type;?>);" data-target="#detailModal">See Details <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="col-md-2 chkbxDiv">
                        <label class="checkquote">.
                            <input type="checkbox"  class="compare_checkbox" name="compare_checkbox[]" data-id="<?php echo $i; ?>" value ="<?php echo $item->plan_id; ?>" id="compareCehck<?php echo $i; ?>"
                                   onclick="getCompareData(this.value,<?php echo $item->plan_id; ?>,<?php echo $i; ?>)"
                            >
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="row dflex  mt-3 qut">
                    <div class="border-right">
                        <p>Sum Insured (in ₹)</p>
                        <input type="hidden" id="basis_id<?php echo $i; ?>" value="<?php echo $item->basis_type; ?>">
                        <?php if ($item->basis_type == 1) {
                            $option = '<option disabled>select</option>';
                            $p = 1;
                            foreach ($item->sumInsure as $si) {
                                if ($si->sum_insured != 0) {
                                    $selected='';
                                    if($p == 1){
                                        $selected='selected';
                                    }

                                    $option .= '<option '.$selected.' value="' . $si->sum_insured . '_' . $p . '" >' . $si->sum_insured . '</option>';
                                    $p++;
                                }

                            }
                            ?>
                            <select class="form-control sum_insures" id="sumInsure<?php echo $i; ?>"
                                    onchange="getDataPremium(this.value,<?php echo $item->plan_id; ?>,<?php echo $i; ?>)"
                                    style="height: 33px;padding: 2px;font-size: 15px;font-weight: 1000;    color: #0b0b0c;">
                                <?php
                                echo $option;
                                ?>
                            </select>

                            <?php
                        } else {

                            ?>
                            <b class = "sum_insures" id="sumInsure<?php echo $i; ?>"><?php echo $item->sumInsure[0]->sum_insured; ?></b>
                            <?php
                        }
                        ?>

                    </div>
                    <div class="border-right">
                        <p>Premium</p>
                        <?php // if ($item->basis_type == 1) {
                        $optionq = '<option disabled selected>select</option>';
                        $m = 1;
                        foreach ($item->premium as $pre) {
                            $selected='';
                            if($m == 1){
                                $selected='selected';
                                $firstrate=$pre->rate;
                            }
                            $optionq .= '<option disabled '.$selected.' value="' . $item->plan_id . '_' . $m . '" >' . $pre->rate . '</option>';
                            $m++;
                        }
                        ?>
                        <select class="form-control" style="display: none;"  id="Premium<?php echo $i; ?>"
                                style="height: 33px;padding: 2px;font-size: 13px;">
                            <?php
                            echo $optionq;
                            ?>
                        </select>
                        <b><i class="fa fa-inr" aria-hidden="true"></i><span
                                id="PremiumSpan<?php echo $i; ?>"><?php echo $firstrate; ?></span></b>

                        <?php
                        // } else {

                        ?>
                        <!--  <b><i class="fa fa-inr" aria-hidden="true"></i><span
                                            id="Premium<?php /*echo $i; */?>"><?php /*echo $item->premium[0]->rate; */?></span></b>-->
                        <?php
                        //  }
                        ?>

                    </div>
                    <div >
                        <p>Tenure (Year)</p>
                        <b id="Tenure<?php echo $i; ?>" >1</b>
                    </div>
                    <div>
                        <p>
                            <button class="btn btn-buy btn-sm mt-3" type="button"
                                    onclick="buyNow(<?php echo $i; ?>)">BUY NOW
                            </button>
                            </a></p>
                    </div>
                </div>
            </div>
            <?php
            $i++;
        }
        ?>


    </div>

</div>
<div class="sideimg-left">
    <img src="/assets/gadget/img/Vector-3.png">
</div>
<!-- The Modal -->
<div class="modal fade" id="detailModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal body -->
            <div class="modal-body p-0">
                <button type="button" class="close p-2" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="p-3  b-g-dark col-md-4 modalquote">
                        <figure>
                            <img src="/assets/gadget/img/img1.png">
                        </figure>
                        <p><b id="prodName"></b></p>
                        <hr/>
                        <div class="modal_compare">
                            <p><span>Sum Insured (in : <i class="fa fa-inr"></i>)</span>
                                <select class="form-control" id="sumInsureMod" onchange="getPremium(this.value)"
                                        style="height: 33px;padding: 2px;font-size: 13px;display: none">

                                </select>
                                <span class="f-right" style="display: none" id="sumInsureModR">0.00</span>
                            </p>
                            <p><span> Premium (in : <i class="fa fa-inr"></i>)</span> <span class="f-right"
                                                                                            id="PremiumMod">0.00</span>
                            </p>
                            <p><span>Tenure (Year)</span> <span class="f-right">1</span></p>
                        </div>
                        <div>
                            <button class="btn btn-download"><i class="fa fa-download"></i> Download Brochure
                            </button>
                        </div>

                    </div>
                    <div class="col-md-8 modal-right">
                        <!--  <p> Own a high-value cycle? Love your fitness partner? Make sure you secure it with our
                              Cycle Protect Plan. A plan that ensures you can ride on worry-free. Get worldwide
                              emergency travel & roadside assistance with the entertainment package of annual
                              membership for Live TV (ZEE5). This plan can be availed within 45 days of cycle
                              purchase.</p>-->
                        <h5>Key Features & Benefits</h5>
                        <div class="features"></div>


                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="quotes_compare_container" style="display: none"
>
    <div class="quotes_compare_container_wrapper">
        <div class="quotes_compare_div">
            <div class="quotes_compare_plan_name add_plan" id="compare_cart_item_1">
                <span class="quotes_compare_span_add_plan ">Add a Plan</span>


            </div>
            <div class="quotes_compare_plan_name add_plan" id="compare_cart_item_2">
                <span class="quotes_compare_span_add_plan ">Add a Plan</span>



            </div>
            <div class="quotes_compare_plan_add add_plan "  id="compare_cart_item_3">
                <span class="quotes_compare_span_add_plan">Add a Plan</span>
               
            </div>
        </div>
        <div class="quotes_compare_buttons_div">
            <form action="<?php echo base_url() . "GadgetInsurance/compare" ?>" id="compareCardForm" method="POST">
                <input type="hidden" value="" name="plan_id_compare" id = "compareplanid">
                <input type="hidden" value="" name="cover_compare" id="comparecoverid">
                <input type="hidden" value="" name="premium_compare" id="comparepremium">
                <input type="hidden" value="" name="plan_compare" id="compareplanname">
                <input type="hidden" value="" name="tenure_compare" id="comparetenure">
                <input type="hidden" value="" name="policy_id_compare" id="comparePolicyid">




                <input type="submit" style="display:none;">


            </form>
            <a href="#" onclick="compare_policies_gadget();">

                <div class="quotes_compare_button btn btn-buy btn-sm p-2" id="quotes_compare_btn">Compare Now</div>
            </a>
            <div onclick="closeCompare()" class="quotes_compare_remove_button">Close</div>
        </div>
    </div>
</div>

<form action="<?php echo base_url() . "/GadgetInsurance/generate_proposal" ?>" id="hiddenCardForm" method="POST">
    <input type="hidden" name="plan_id" id="hiddenplanid">
    <input type="hidden" name="lead_id_new" id="lead_id_new">
    <input type="hidden" name="cover" id="hiddencover">
    <input type="hidden" name="premium" id="hiddenpremium">
    <input type="hidden" name="plan_name" id="hiddenplanname">
    <input type="hidden" name="policy_id" id="hiddenpolicyid">
    <input type="submit" style="display:none;">
</form>
</div>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 0,
    });
    let msater_policy_data = '';
    $(document).ready(function () {
        msater_policy_data = JSON.parse('<?php echo json_encode($master_policy); ?>');
        console.log(msater_policy_data);
        $('.checkquote').hide();

    });

    function seedetail(i,basis_type) {
        $("#PremiumMod").html('0.00');
        var j = i - 1;
        console.log(msater_policy_data[i - 1])
        var data = msater_policy_data[i - 1];
        $('#prodName').html(data.plan_name);
        var op = '<option selected>Select</option>';
        var k = 0;
        var valuer='';
        var valuefirst='';
        $.each(data.sumInsure, function (key, val) {
            var select='';
            if(k == 0){
                var    select='selected';
                valuer= val.sum_insured + '_' + k + '_' + j;
                valuefirst= val.sum_insured;
            }
            op += '<option '+select+' value="' + val.sum_insured + '_' + k + '_' + j + '">' + val.sum_insured + '</option>';
            k++;
        });
        if(basis_type == 1){
            $('#sumInsureModR').hide();
            $('#sumInsureMod').show();
            $('#sumInsureMod').html(op);
        }else{
            $('#sumInsureMod').hide();
            $('#sumInsureModR').show();
            $('#sumInsureModR').html(valuefirst);
        }


        getPremium(valuer);
        getAllfeaturs(data.creditor_id, data.plan_id);

    }

    function getAllfeaturs(creditor_id, plan_id) {
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>GadgetInsurance/getfeatures",
            data: {
                creditor_id: creditor_id,
                plan_id: plan_id
            },
            dataType: "json",
            success: function (response) {
                console.log(response.features);
                var html='';
                $.each(response.features, function (key, val) {
                    console.log(val.long_description);
                    html +='  <div class="detail-box">' +
                        '                  <b class="p-0"> <img src="/assets/gadget/img/icon.png"> '+val.short_description+'</b>\n' +
                        '              <p>' +val.long_description +'</p>' +
                        '              </div>';
                });
                if(html == ''){
                    $('.features').html('<div class="detail-box">\n' +
                        '                                <b class="p-0"> <img src="/assets/gadget/img/icon.png"> Complimentary Add-on Benefit</b>\n' +
                        '                                <p>\n' +
                        '                                    Stay covered for damage caused to your cycle due to fire, burglary, riots, strikes\n' +
                        '                                    or any such unforeseen situations within just 7 days of your membership. Coverage\n' +
                        '                                    includes the agreed value or Invoice Value (whichever is less) of the cycle provided\n' +
                        '                                    it is for personal use only.\n' +
                        '                                </p>\n' +
                        '                            </div>\n' +
                        '                            <div class="detail-box">\n' +
                        '                                <b class="p-0"> <img src="/assets/gadget/img/icon.png"> Complimentary Add-on Benefit</b>\n' +
                        '                                <p>\n' +
                        '                                    Stay covered for damage caused to your cycle due to fire, burglary, riots, strikes\n' +
                        '                                    or any such unforeseen situations within just 7 days of your membership. Coverage\n' +
                        '                                    includes the agreed value or Invoice Value (whichever is less) of the cycle provided\n' +
                        '                                    it is for personal use only.\n' +
                        '                                </p>\n' +
                        '                            </div>\n' +
                        '                            <div class="detail-box">\n' +
                        '                                <b class="p-0"> <img src="/assets/gadget/img/icon.png"> Complimentary Add-on Benefit</b>\n' +
                        '                                <p>\n' +
                        '                                    Stay covered for damage caused to your cycle due to fire, burglary, riots, strikes\n' +
                        '                                    or any such unforeseen situations within just 7 days of your membership. Coverage\n' +
                        '                                    includes the agreed value or Invoice Value (whichever is less) of the cycle provided\n' +
                        '                                    it is for personal use only.\n' +
                        '                                </p>\n' +
                        '                            </div>')
                }else{
                    $('.features').html(html);
                }

            }
        });
    }

    function getDataPremium(value, num, sequence) {
        arr = value.split("_");
        $("#Premium" + sequence).val(num + '_' + arr[1]);
        var e = document.getElementById("Premium"+sequence);
        var premium = e.options[e.selectedIndex].text;
        $("#PremiumSpan" + sequence).html(premium);
    }
    function compare_policies_gadget() {
        debugger;
        // alert(1);
        // plan[]=1&plan[]=3
        if($("input[name='compare_checkbox[]']:checked").length<=1)
        {
            alert("Please select plan greater than 1 for comparison");
            return;
        }
        var plans = '';
        var sum_insured = '';
        var premium = '';
        var plan_name ='';
        var data_id;
        var tenure ='';
        var policy_id ='';
        $('.compare_checkbox').each(function() {
            if (this.checked) {
                // alert($(this).val());
                // plans += "plan[]="+$(this).val()+"&";
                plans += $(this).val() + ",";
                data_id = $(this).data('id');
                plan_name += $('#plan_name'+ data_id).val() + ",";
                tenure += $("#Tenure" + data_id).text() + ",";
                policy_id += $("#policy_id" + data_id).val() + ",";

                if( $("#sumInsure" + data_id).has('option').length>0)
                {
                    sum_insured+= $("#sumInsure" + data_id + " option:selected").text() + ",";
                    premium+= $("#Premium" + data_id + " option:selected").text() + ",";


                }
                else {
                    sum_insured+= $("#sumInsure" + data_id).text() + ",";
                    premium+= $("#PremiumSpan" + data_id).text() + ",";
                }
                // sum_insured += sum_insured + ",";
                //console.log(sum_insured);


            }

        });
        // plans = plans.replace(/&+$/,'');
        plans = plans.replace(/,+$/, '');
        sum_insured = sum_insured.replace(/,+$/, '');
        premium = premium.replace(/,+$/, '');
        plan_name = plan_name.replace(/,+$/, '');
        tenure = tenure.replace(/,+$/, '');
        policy_id = policy_id.replace(/,+$/, '');



        $('#compareplanid').val(plans);
        $('#comparecoverid').val(sum_insured);
        $('#comparepremium').val(premium);
        $('#compareplanname').val(plan_name);
        $('#comparetenure').val(tenure);
        $('#comparePolicyid').val(policy_id);




        $('#compareCardForm').submit();
    }
    function getCompareData(value,plan_id,sequence)
    {
        //compare_checkbox
        if($('input[name="compare_checkbox[]"]:checked').length>3)
        {
            alert("You can't select more than 3 plans");
            //  $("#sumInsure" + sequence).checked=false;
            $('#compareCehck'+ sequence).prop('checked', false);
            return;
        }
//data-aos="fade-up"
        // quotes_compare_container
        //$('#quotes_compare_container').attr('data-aos', 'fade-up');
        if( $("#sumInsure" + sequence).has('option').length>0)
        {
            var sum_insured = $("#sumInsure" + sequence + " option:selected").text();
            var premium = $("#Premium" + sequence + " option:selected").text();


        }else
        {
            var sum_insured = $("#sumInsure" + sequence).text();
            var premium = $("#Premium" + sequence ).text();


        }
        var tenure = $("#Tenure" + sequence).text();
        var plan_name = $("#plan_name" + sequence).val();
        var plan_id = $("#plan_id" + sequence).val();

        var add_length = $('.add_plan').length;
        if($('#compareCehck'+sequence).is(':checked'))
        {

            var i;
            var str = '';

            for (i = 1; i <= 3; i++) {
                if ($("#compare_cart_item_" + i).hasClass('add_plan') && !$("#compare_cart_item_" + i).hasClass('add_info')) {
                    str = '<div class="quotes_compare_image"><img alt="Care Health" class="quotes_img_compare1" layout="fill"src="<?php echo $logo ; ?>"></div><div class="quotes_compare_span_plan_name"><span class="compare_plan_name2">' + plan_name + '</span><div class="cover_compare"><p><span>Sum Insured (in : <i class="fa fa-inr"></i>)</span> <span class="f-right">' + sum_insured + '</span></p><p><span> Premium (in : <i class="fa fa-inr"></i>)</span> <span class="f-right">' + premium + '</span></p><p><span>Tenure (Year)</span> <span class="f-right">' + tenure + '</span></p> </div></div>' +
                        '<!--<div class="div_remove_compare"><span class="span_cross plan_add_close_btn" data-plan="compare_one">x</span></div>-->';
                    $("#compare_cart_item_" + i).html(str);
                    $("#compare_cart_item_" + i).removeClass('add_plan');

                    $("#compare_cart_item_" + i).addClass('plan_id'+plan_id);
                    $("#compare_cart_item_" + i).addClass('add_info');
                    return;

                }

            }
        }
        else
        {
            for (i = 1; i <= 3; i++) {
                if ($("#compare_cart_item_" + i).hasClass('plan_id'+plan_id)) {
                    $("#compare_cart_item_" + i).html("<span class='quotes_compare_span_add_plan'>Add a Plan</span>");
                    $("#compare_cart_item_" + i).addClass('add_plan');

                    $("#compare_cart_item_" + i).removeClass('plan_id'+plan_id);
                    $("#compare_cart_item_" + i).removeClass('add_info');
                    return;

                }

            }
        }

    }
    function getPremium(value) {
        arr = value.split("_");
        var op_num = arr[1];
        var arrseq = arr[2];
        var data = msater_policy_data[arrseq];
        $("#PremiumMod").html(data.premium[op_num]['rate']);
    }
    function buyNow(num) {
        var lead_id=$('#lead_id').val();
        var policy_id=$('#policy_id'+num).val();
        var plan_name=$('#plan_name'+num).val();
        var basis_id=$('#basis_id'+num).val();
        var plan_id=$('#plan_id'+num).val();
        var e = document.getElementById("Premium"+num);
        var premium = e.options[e.selectedIndex].text;
        if(basis_id == 1){
            var t = document.getElementById("sumInsure"+num);
            var cover = t.options[t.selectedIndex].text;

        }else{
            var cover = $("#sumInsure"+num).text();
        }
        //  alert(cover);return;

        $('#lead_id_new').val(lead_id);
        $('#hiddenplanid').val(plan_id);
        $('#hiddencover').val(cover);
        $('#hiddenpremium').val(premium);
        $('#hiddenplanname').val(plan_name);
        $('#hiddenpolicyid').val(policy_id);
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>GadgetInsurance/create_policy_member_plan",
            data: {lead_id,policy_id,plan_name,plan_id,premium,cover
            },
            dataType: "json",
            success: function (response) {
                $("#hiddenCardForm").submit();
            }
        });
    }

    function compareBtncClick() {

        if ($('.quotes_compare_container').is(":visible")){
            $('.quotes_compare_container').hide();
        }else{
            $('.quotes_compare_container').show();
        }
        if ($('.checkquote').is(":visible")){
            $('.checkquote').hide();
        }else{
            $('.checkquote').show();
        }
        $('.compare_checkbox').prop('checked',false);
    }
    function closeCompare() {
        $('.quotes_compare_container').hide();
        $('.checkquote').hide();
    }
</script>
</body>
</html>
