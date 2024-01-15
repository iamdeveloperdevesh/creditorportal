<style>
    .card-si {
        width: 81%;
        margin:0 auto;
    }

    .btn-premium {
        margin-top: 2px;
        float: none;
        margin-left: 0;
    }

    #vw-more {
        display: none;
    }

    /* New css 17-01-2023 */

    .product-head2{
        display: flex;
    }

    span.ml-2.tl-ml-2.product-title {
        position: relative;
    }

    .prodcFeature .prodcFeatureCard .greenCheck{
        position: relative;
        left:0px;

    }
    .prodcFeature .prodcFeatureCard {
        height: 227px;
        overflow: hidden;
        overflow-y: auto;
    }

    .prodcFeature .prodcFeatureCard li.mt-1 {
        display: flex;
    }
    .prodcFeature .prodcFeatureCard::-webkit-scrollbar {
        width: 4px;
        background: #96a4b5;
        border-radius: 30px;
        height: 6px;
        display: none;
    }
    /*.btn-buynow{
        background-color: #F2581B !important;
    }*/
    /* New css 17-01-2023 */

</style>
<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
    <div class="container cont-product">
        <div class="product-msg mt-4">

            <div class="col-md-8 offset-md-2">
                <div class="card card-product  pdt-11">
                    <div class="card-body text-center">
                        <span class="pro-text"> All Rounder Health Insurance Covers, <span class="txt-brand"> Exclusively for you!</span> </span><img class="pro-img" src="/assets/abc/images/product-select.png" width="60">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-3 ">

        <div class="row">
            <?php

            function getIndianCurrency(float $number)
            {
                $decimal = round($number - ($no = floor($number)), 2) * 100;
                $hundred = null;
                $digits_length = strlen($no);
                $i = 0;
                $str = array();
                $words = array(0 => '', 1 => 'one', 2 => 'two',
                    3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
                    7 => 'seven', 8 => 'eight', 9 => 'nine',
                    10 => 'ten', 11 => 'eleven', 12 => 'twelve',
                    13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
                    16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
                    19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
                    40 => 'forty', 50 => 'fifty', 60 => 'sixty',
                    70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
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
                        $str [] = ($number < 21) ? $number . ' ' . $digits[$counter] . $plural . ' ' . $hundred : trim(floor($number / 10) * 10) . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                    } else $str[] = null;
                }
                $Rupees = implode('', array_reverse($str));
                $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
                return ($Rupees ? $Rupees : '') . $paise;
            }



            foreach($plan_data as $plan_det)
            {


            ?>
            <div class="col-lg-4 col-md-6 col-12 col-sm-6 m-77">
                <?php
                foreach($plan_det['premium_details'] as $premium)
                {

                    $premium_amt = $premium['sum_insured'];
                ?>
                <div class="card-si"> Cover <?php echo getIndianCurrency($premium['sum_insured']);?> <br> <button class="btn btn-premium">Premium - <?php echo $premium['premium_amount'];?> /- </button> </div>
                <?php } ?>
                <div class="card login-card">
                    <div class="card-body">
                        <div class="product-head2">
                            <span class="img-pro-2"><img src="/assets/abc/images/combo.png" width="34"></span>

                            <span class="ml-2 tl-ml-2 product-title"><?php echo $plan_det['plan_name'];?></span>

                        </div>
                        <div class="border-pro"></div>
                        <div class="product-body">
                            <div style="height: auto;" class="button-ul-group">
                                <div class="mt-4">
                                    <?php
                                    foreach($plan_det['member_details'] as $members)
                                    {


                                    ?>
                                    <button class="btn fam-btn"><?php echo $members['member_type'];?></button>
                                    <?php
                                    }
                                    ?>

                                </div>
                                <div class="prodcFeature">
                                    <ul class="pro-list pro-height mt-2 prodcFeatureCard">
                                        <!--    <li class="mt-1"> <i class="fa fa-check"></i> Inpatient Hospitalization covered </li>
                                            <li class="mt-1"> <i class="fa fa-check"></i> 527-day care procedures covered </li>
                                            <li class="mt-1"> <i class="fa fa-check"></i> Organ Donor expenses covered. </li>
                                            <li class="mt-1"> <i class="fa fa-check"></i> Pre & post hospitalisation cover. </li>
                                            <li class="mt-1"> <i class="fa fa-check"></i> Ambulance charges covered. </li>
                                            <li class="mt-1"> <i class="fa fa-check"></i> Initial 30 Days waiting period except Claims related to Accident. </li>
                                            <li class="mt-1"> <i class="fa fa-check"></i> 2 years waiting periods for specified illness. </li>
                                            <li class="mt-1"> <i class="fa fa-check"></i> Waiting period of 3 years for pre-existing diseases. </li>-->

                                        <?php
                                        foreach($plan_det['feature_config'] as $fetaure)
                                        {


?>
                                        <li class="mt-1"> <i class="fa fa-dot-circle-o mr-2" style="color: #0cc50c"></i><span> <?php echo $fetaure['long_description'];?></span> </li>
<?php }?>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-center col-md-12">
                                </div>
                                <div class="text-center col-md-12">
                                    <input type="hidden" name="lead_id" value="<?php echo $plan_det['lead_id'];?>" id="compare_one_<?php echo $plan_det['policy_id']; ?>_leadid">
                                    <input type="hidden" name="customer_id" value = "<?php echo $plan_det['customer_id'];?>" id="compare_one_<?php echo $plan_det['policy_id']; ?>_customerid">
                                    <input type="hidden" name="trace_id" value = "<?php echo $plan_det['trace_id'];?>" id="compare_one_<?php echo $plan_det['policy_id']; ?>_trace_id">
                                    <input type="hidden" name="cover" value = "<?php echo $premium_amt;?>" id="compare_one_<?php echo $plan_det['policy_id']; ?>_cover">


                                    <a href="javascript:void(0)" class="cardBuyNow" data-planid="<?php echo $plan_det['plan_id']; ?>" data-policyid="<?php echo $plan_det['policy_id']; ?>">
                                        <button class="btn btn-buynow ClickBtn mt-2">Buy Now <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>

                                    </a>
                                    <!-- <button type="button" class="btn btn-buynow  mt-2" ty onclick="PolicyIssue()">Buy Now <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <?php
            }
            ?>
        </div>


    </div>
</div>
<br>
<form action="<?php echo base_url() . "quotes/redirect_to_pg" ?>" id="hiddenCardForm" method="POST">
    <input type="text" name="customer_id" id="cutomer_id_det">
    <input type="text" name="lead_id" id="lead_id">
    <input type="text" name="trace_id" id="trace_id_det">
    <input type="text" name="plan_id" id="plan_id_det">
    <input type="hidden" name="cover" id="cover">
    <input type="hidden" name="policy_id" id="hiddenpolicyid">

    <input type="submit" style="display:none;">
</form>

<input type="hidden" id="plans_for_count_get" value="1">
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", ".cardBuyNow", function() {

            var policy_id = $(this).data("policyid");

            $('#plan_id_det').val($(this).data("planid"));
            $('#policy_id').val(policy_id);
            $('#cutomer_id_det').val($('#compare_one_' + policy_id + '_customerid').val());
            $('#lead_id').val($('#compare_one_' + policy_id + '_leadid').val());
            $('#trace_id_det').val($('#compare_one_' + policy_id + '_traceid').val());
            $('#cover').val($('#compare_one_' + policy_id + '_cover').val());




            $("#hiddenCardForm").submit();



        });


        function checkCDbalance(plan_id,premium) {

            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: "/quotes/checkCDbalanceThreshold",
                    type: "POST",
                    //async: false,
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

</script>
<!-- main content area end -->
<script>
    $(document).ready(function($) {
        var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
        var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

        function inWords (num) {
            if ((num = num.toString()).length > 9) return 'overflow';
            n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
            if (!n) return; var str = '';
            str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
            str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
            str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
            str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
            str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'only ' : '';
            return str;
        }


        var maxHeight = Math.max.apply(null, $(".button-ul-group").map(function() {
            return $(this).height();
        }).get());
        $(".button-ul-group").height(maxHeight);
    });

    function PolicyIssue() {
        $.ajax({
            url: "/quotes/PolicyIssueApiNew",
            type: "POST",
            async: false,
            dataType: "json",
            success: function(response) {
                if(response.success == true){
                    window.location.href =("http://fyntunecreditoruat.benefitz.in/quotes/success_view/"+response.LeadId);
                }
            }


        });
    }

</script>
