<?php
$logo=$this->session->userdata('logo');
$lead_id=$this->session->userdata('lead_id');
?>
<style>

    .shareModalCode.emailwrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .shareModalCode .sendMailbtn {
        color: #fff !important;
        background-color: #005478 !important;
        border-color: #005478 !important;
        border-radius: 100px !important;
        padding: 14px 32px;
        font-size: 18px;
        box-shadow: none;
        width: 29%;
    }

    .shareModalCode .emailBox {
        font-weight: 500;
        line-height: 1.4375em;
        font-family: Poppins, sans-serif;
        box-sizing: border-box;
        cursor: text;
        display: inline-flex;
        -webkit-box-align: center;
        align-items: center;
        position: relative;
        border-radius: 4px;
        padding-left: 14px;
        font-size: 14px;
        color: rgb(161, 161, 182);
        height: 60px;
        border: 1px solid #eaeaea;
        width: 70%;
    }
    .shareModalCode .modal-title {
        margin: 0px;
        font-family: Poppins, sans-serif;
        font-weight: 600;
        font-size: 25px;
        line-height: 1.6;
        padding: 4px 24px;
        flex: 0 0 auto;
    }

    .shareModalCode .emailBox img {
        height: 31px;
    }



    .shareModalCode input.emailInpt {
        font: inherit;
        letter-spacing: inherit;
        color: currentcolor;
        border: 0px;
        background: none;
        height: 1.4375em;
        margin: 0px;
        -webkit-tap-highlight-color: transparent;
        display: block;
        min-width: 0px;
        width: 100%;
        animation-name: mui-auto-fill-cancel;
        animation-duration: 10ms;
        box-sizing: border-box;
        padding: 19.5px 14px 14.5px;
    }




    .shareModalCode .sendMailbtn {
        color: #fff !important;
        background-color: #005478 !important;
        border-color: #005478 !important;
        border-radius: 100px !important;
        padding: 14px 32px;
        font-size: 18px;
        box-shadow: none;
    }


    .shareModalCode .modal-header {
        padding: 4px;
        border-bottom: 1px solid #e5e5e5;
        flex-direction: row-reverse;
    }

    .shareModalCode .modal-body {
        position: relative;
        padding: 22px;
    }

    .shareModalCode .modal-header .close {
        margin-top: 3px;
        margin-right: 6px;
        background: #f2f2f2;
        opacity: 1;
        padding: 3px 8px;
        border-radius: 30px;
    }

    .shareModalCode .sendMailbtn span{


        font-size: 18px;
        border-radius: 5px;
        box-shadow: none;
    }

    .shareModalCode .emailwrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .shareModalCode .emailInpt:focus-visible {
        border: none;
        box-shadow: none;
        outline: none;
    }
    button.btn.btn-default.addmoreBtn {
        background: #005478 !important;
        color: #fff;
    }button.btn.btn-default.addmoreBtn span i {
         margin-left: 10px;
     }
    .compareModal.modal-header {
        padding: 4px;
        border-bottom: 1px solid #e5e5e5;
        flex-direction: row-reverse;
    }
    .compareModal.modal-header .close {
        margin-top: 3px;
        margin-right: 6px;
        background: #f2f2f2;
        opacity: 1;
        padding: 3px 8px;
        border-radius: 30px;
    }
    .compareModal .modal-title {
        margin: 0px;
        font-family: Poppins, sans-serif;
        font-weight: 600;
        font-size: 25px;
        line-height: 1.6;
        padding: 4px 24px;
        flex: 0 0 auto;
    }
    .compareModal .modal-body {
        position: relative;
        padding: 22px;
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;
        max-height: 290px;
        overflow: hidden;
        height: 300px;
        overflow-y: auto;
    }
    .compareModal .emailwrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #005478;
        padding: 8px 17px;
        box-shadow: 2px 1px 7px #ccc9c9;
        border-radius: 4px;
        width: 31.1%;
        position: relative;
        z-index: 0;
        margin: 8px 8px;
        height: 70px;
    }
    .compareModal .emailBox {
        font-weight: 500;
        box-sizing: border-box;
        cursor: text;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        position: relative;
        border-radius: 4px;
        font-size: 15px;
        color: rgb(161, 161, 182);
        flex-direction: column;
    }
    .compareModal.emailBox img {
        height: 31px;
    }
    .compareModal .AmountBox p {
        font-weight: 700;
        font-size: 20px;
        display: flex;
        align-items: center;
    }
    .compareModal .emailBox p {
        font-size: 10px;
        font-weight: 600; margin-bottom: 0px;
    }
    .compareModal  button.btn.btn-compare {
        background: #005478;
        color: #fff;
    }
</style>
<style>
    .emailwrapper label.checkquote {
        position: absolute;
        top: -9px;
        right: -20px;
    }
    .qutbox.quotedetail {
        width: 32%;
        margin: 7px 8px;
    }
    .qutbox1.quotedetail {
        width: 32%;
        margin: 7px 8px;
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
</style>
<style>
    .remove_IC {
        align-items: center;
        background-color: #fff;
        border: 1px solid #090909;
        border-radius: 100%;
        cursor: pointer;
        display: inline-block;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        font-style: normal;
        font-variant: normal;
        font-weight: 400;
        font-weight: 700;
        justify-content: center;
        line-height: 0;
        padding: 8px;
        position: absolute;
        right: 22px;
        speak: none;
        text-align: center;
        text-transform: none;
        top: 9px;
        width: 25px;
        height: 26px;
        color: #fff;
        border: none;
        /* background: #ed3f51; */
    }
    tr.mobile_row td {
        display: table-cell !important;
        text-align: left !important;
        padding-left: 11px;
    }
    tr.mobile_row {
        display: none;
    }
    .buynow_btn { display : block; }
    @media only screen and (max-width: 576px) {
        tr.mobile_font td {
            font-size: 15px;
        }
        tr.mobile_row {
            border: 1px solid #e3e4e8;
            background: #dbdbdb;
            height: 31px;
            display: revert !important;
            width: 100%;
            display: block;
        }
        .compareModal .modal-title {
            font-size: 17px;
            padding: 4px 17px;
        }
        .compareModal .modal-header {
            flex-direction: row-reverse;
        }
        .compareModal .emailwrapper {
            width: 100%;
        }
        button.btn.btn-default.addmoreBtn {
            padding: 3px 2px;
            border-radius: 4px;
            border: none !important;
        }
        .back-btn button {
            margin: 0px 21px 0px 0px;
        }
        .compTableHeadBtn span:first-child {
            font-size: 14px;
        }
        .compareTableWrap table {

            font-size: 13px;
        }
        .remove_IC {
            background-color: transparent !important;
            border: none;
            right: 4px;
            top: 4px;
            color: #000;
        }
        .compTableHeadLogo {
            max-height: 40px;
        }
        .compTableHeadBtn span {
            font-size: 15px;
            line-height: 20px;
        }
        .buynow_btn{ display : none; }
        .compTableHead.customProductComp .compTableHeadBtn {
            margin: 8px auto 8px;
        }
        .cardOtherItemInner.cardOtherItemInnerNoBorder .fbold {
            font-weight: 700;
            overflow: hidden;
            height: 40px !important;
            -webkit-line-clamp: 2;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            white-space: normal;
        }
    }
    @media only screen and (min-width: 320px) and (max-width: 767px) {
        .compareTableWrap .compareTableWrapStick {
            width: 100% !important;
        }
    }

</style>

<div class="container  mt-5 quotes genralCompare">
                <div class="compare_wrapper">
                    <div class=" dflex-btn">
                        <div class="d-flex">
                            <a onclick="history.back()">
                                <div class="back-btn">
                                    <button><i class="fa fa-long-arrow-left"></i> Go Back</button>
                                </div>
                            </a>

                        </div>
                        <div class="quotebtn ">
                            <button type="button" data-toggle="modal" data-target="#compareModal" class="btn btn-default addmoreBtn">
                                <span>Add More<i class="fa fa-plus"> </i></span>
                            </button>
                            <button class="btn btn-default" data-toggle="modal" data-target="#shareModal">
                                <span>Share<img src="/assets/gadget/img/share.jpeg"></span>
                            </button>
                            <button class="btn btn-default" type="button" onclick="OpencomparePDF()">
                                <span>Download<img src="/assets/gadget/img/download.jpeg"></span>
                            </button>

                        </div>
                    </div> 
                    <div class="sideimg">
                        <img src="/assets/gadget/img/vector-2.png">
                    </div>  
                    <div class="compareTableWrap">
				
                        <table class=" col-md-12 compareTableWrapStick">
                        
                            <tbody>
                            <tr id="logo_div">
                                <td class="compTablePoint genralCompareLaxmi">
                                    <div><h3 class="compare">Compare Plans</h3></div>
                                </td>

                                <?php
                                $plan_name=explode(",",$plan['plan_name']);
                                $plan_id=explode(",",$plan['plan_id']);
                                $premium=explode(",",$plan['premium']);
                                $cover=explode(",",$plan['cover']);
                                $tenure=explode(",",$plan['tenure']);
                                $policy_id=explode(",",$plan['policy_id']);
                                $i=1;
                                foreach ($plan_name as $key=>$item){ ?>
                                    <td class="compTableHead customProductComp" style="display: table-cell;">
                                        <a class="remove_IC" onclick="RemoveBtn(<?php echo $i; ?>)" ><img class="svgpdfshow" src="/assets/gadget/img/crossbtn.jpeg" alt="Logo"></a>
                                        <div class="comparelogo">
                                            <img class="compTableHeadLogo customWithoutHome" src="<?php echo $logo; ?>" alt="Logo">
                                        </div>

                                        <!-- <input type="hidden" id="insurance_company_name"> -->

                                        <div class="otherCardExtWrap" style="height: auto;margin: 5px;visibility: hidden;">
                                            <div class="cardInspecWaveCompareRenewal bhgh"></div>
                                        </div>
                                        <div class="cardOtherItemInner cardOtherItemInnerNoBorder">
                                            <span class="fbold"> <?php echo $item; ?> </span>
                                        </div>
                                        <button class="compTableHeadBtn btn btn-buy btn-sm" type="button"
                                                onclick="final_buy_now(<?php echo $plan_id[$key]; ?>,<?php echo $cover[$key]; ?>,<?php echo $premium[$key]; ?>,'<?php echo $item; ?>',<?php echo $policy_id[$key]; ?>)">
                                                <span  class="buynow_btn">BUY NOW</span>
                                                <span class="net_premium buyNow0" >₹  <?php echo $premium[$key]; ?></span>
                                            </button>
                                    </td>
                              <?php
                                $i++;
                                }
                                ?>
                            </tr>
                            <tr class="mobile_row"> <td colspan="4"> Sum Insured </td> </tr>
                            <tr class="mobile_font">
                                <td class="">
                                    Sum Insure
                                </td>
                                <?php

                                foreach ($cover as $item){ ?>
                                    <td class="" style="display: table-cell;">
                                      <?php echo $item; ?>
                                    </td>
                                <?php  }
                                ?>
                            </tr>
                            <tr class="mobile_row"> <td colspan="4"> Tenure (in years) </td> </tr>
                            <tr class="mobile_font">
                                <td class="">
                                    Tenure (in years)
                                </td>
                                <?php

                                foreach ($tenure as $item){ ?>
                                    <td class="" style="display: table-cell;">
                                        <?php echo $item; ?>
                                    </td>
                                <?php  }
                                ?>
                            </tr>
                            <?php
                            foreach ($compare_features as $f){ ?>
                                <tr class="mobile_row"> <td colspan="4">  <?php echo $f->feature; ?> </td> </tr>
                            <tr>
                                <td class="">
                                    <?php echo $f->feature; ?>
                                </td>
                                <?php

                                foreach ($plan_id as $item){ ?>
                                    <td class="" style="display: table-cell;">
                                        <?php
                                        $exp=explode(',',$features[$item]);
                                        if(in_array($f->id,$exp)){

                                            ?>
                                            <img class="svgpdfshow" src="/assets/gadget/img/check.png"></span>
                                     <?php   }else{ ?>
                                            <img class="svgpdfshow" src="/assets/gadget/img/cross.png"></span>
                                            <?php

                                        }
                                        ?>
                                    </td>
                                <?php  }
                                ?>
                            </tr>
                          <?php  }
                            ?>
                        </tbody>
                        </table>


    
                    </div>
                </div>          
                
            </div>
            <div class="sideimg-left">
                <img src="/assets/gadget/img/Vector-3.png">
            </div>
           
    </div>
<div class="modal fade shareModalCode" id="shareModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">Share Quotes</h2>
            </div>
            <div class="modal-body">
                <div class="emailwrapper">
                    <div class="emailBox">
                        <img src="/assets/gadget/img/email_icon.jpeg">
                        <input aria-invalid="false" name="email"  id="email" placeholder="test@test.com" type="email" class="emailInpt" value="">
                    </div>

                    <button class="sendMailbtn btn btn-buy btn-sm" type="button" onclick="sendMail()">
                        <span >Send</span>

                    </button>

                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade compareModal " id="compareModal" style=" padding-right: 17px;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h2 class="modal-title" style="float: left">Add Upto 3 Plans To Compare</h2>
            </div>
            <div class="modal-body">


                    <?php
                    foreach ($quoteData['master_policy'] as $qut){

                        ?>
                        <div class="emailwrapper">
                            <?php
                            $check='';
                            //var_dump($plan_id);
                            if(in_array($qut->plan_id,$plan_id)){
                                $check='checked';
                            }
                            ?>
                            <label class="checkquote"  style="display: block;">
                                <input type="checkbox" <?php echo $check; ?> class="compare_checkbox"
                                       name="compare_checkbox[]" data-id="2"
                                       value="<?php echo $qut->plan_id ."_".$qut->plan_name."_".$qut->premium[0]->rate."_".$qut->policy_id."_".$qut->sumInsure[0]->sum_insured; ?>" id="compareCehck2"
                                       >
                                <span class="checkmark"></span>
                            </label>
                            <div class="emailBox">
                                <img src="<?php echo $logo; ?>">
                                <p> <?php echo $qut->plan_name; ?> </p>

                            </div>



                            <div class="AmountBox">

                                <p> <i class="fa fa-inr" aria-hidden="true" style="
"></i>
                                    <span><?php echo $qut->premium[0]->rate; ?></span> </p>

                            </div></div>
                    <?php  }
                    ?>
               </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-compare" onclick="compareNow()">Compare</button>
            </div>
        </div>

    </div>
</div>
<form action="<?php echo base_url() . "GadgetInsurance/compare" ?>" id="compareCardForm" method="POST">
    <input type="hidden"  name="plan_id_compare" id = "compareplanid" value="<?php echo $plan['plan_id']; ?>">
    <input type="hidden"  name="cover_compare" id="comparecoverid" value="<?php echo $plan['cover']; ?>">
    <input type="hidden"  name="premium_compare" id="comparepremium" value="<?php echo $plan['premium']; ?>">
    <input type="hidden" name="plan_compare" id="compareplanname" value="<?php echo $plan['plan_name']; ?>">
    <input type="hidden"  name="tenure_compare" id="comparetenure" value="<?php echo $plan['tenure']; ?>">
    <input type="hidden"  name="policy_id_compare" id="comparePolicyid"  value="<?php echo $plan['policy_id']; ?>">

    <input type="submit" style="display:none;">


</form>
<form action="<?php echo base_url() . "/GadgetInsurance/generate_proposal" ?>" id="hiddenCardForm" method="POST">
    <input type="hidden" name="plan_id" id="hiddenplanid" >
    <input type="hidden" name="lead_id_new" id="lead_id_new" value="<?php echo $lead_id;?>">
    <input type="hidden" name="cover" id="hiddencover" >
    <input type="hidden" name="premium" id="hiddenpremium" >
    <input type="hidden" name="plan_name" id="hiddenplanname" >
    <input type="hidden" name="policy_id" id="hiddenpolicyid" >
    <input type="submit" style="display:none;">
</form>
        <script> 
            $(document).ready(function() {
      /*  if($(".genralCompare").length > 0){
            var topPosition = $('.compareTableWrapStick').offset().top;
            $(window).scroll(function(){
            var sticky = $('.compareTableWrapStick'),
                scroll = $(window).scrollTop();

            if (scroll >= topPosition) sticky.addClass('fixed');
            else sticky.removeClass('fixed');
            });
        }*/
        });
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
         duration: 2000,
         });

        function OpencomparePDF() {
            window.open('/GadgetInsurance/sendmailcompare?email=0'
            );
        }
        function sendMail() {
            var email=$("#email").val();
            if(email == ''){
                alert('Email Id is Required!');
            }else{
                window.open('/GadgetInsurance/sendmailcompare?email='+email
                );
            }
        }
        function RemoveBtn(i) {

            var plan_id=$("#compareplanid").val();
            var cover=$("#comparecoverid").val();
            var premium=$("#comparepremium").val();
            var plan_name=$("#compareplanname").val();
            var tenure=$("#comparetenure").val();
            var policy_id=$("#comparePolicyid").val();
            var index=i-1;
            var arr_plan_id=plan_id.split(',');
            var arr_cover=cover.split(',');
            var arr_premium=premium.split(',');
            var arr_plan_name=plan_name.split(',');
            var arr_tenure=tenure.split(',');
            var arr_policy_id=policy_id.split(',');
            if(arr_plan_id.length <= 2){
                alert("You can't remove more than 1 plan.");
                return;
            }
            if (index > -1) { // only splice array when item is found
                arr_plan_id.splice(index, 1); // 2nd parameter means remove one item only
                arr_cover.splice(index, 1); // 2nd parameter means remove one item only
                arr_premium.splice(index, 1); // 2nd parameter means remove one item only
                arr_plan_name.splice(index, 1); // 2nd parameter means remove one item only
                arr_tenure.splice(index, 1); // 2nd parameter means remove one item only
                arr_policy_id.splice(index, 1); // 2nd parameter means remove one item only
            }
            console.log(arr_plan_id.join(','));
           $("#compareplanid").val(arr_plan_id.join(','));
           $("#comparecoverid").val(arr_cover.join(','));
            $("#comparepremium").val(arr_premium.join(','));
           $("#compareplanname").val(arr_plan_name.join(','));
           $("#comparetenure").val(arr_tenure.join(','));
            $("#comparePolicyid").val(arr_policy_id.join(','));
            $('#compareCardForm').submit();
        }
        function final_buy_now(plan_id,cover,premium,plan_name,policy_id) {
            $('#hiddenplanid').val(plan_id);
            $('#hiddencover').val(cover);
            $('#hiddenpremium').val(premium);
            $('#hiddenplanname').val(plan_name);
            $('#hiddenpolicyid').val(policy_id);
            $('#hiddenCardForm').submit();
        }
        
        function compareNow() {
            if($("input[name='compare_checkbox[]']:checked").length<=1)
            {
                alert("Please select plan greater than 1 for comparison");
                return;
            }
            if($("input[name='compare_checkbox[]']:checked").length>3)
            {
                alert("You can't select more than 3 plans.");
                return;
            }
            var values = new Array();
            var plan_id = new Array();
            var plan_name = new Array();
            var premium = new Array();
            var cover = new Array();
            var policy_id = new Array();
            var arr_tenure = new Array();
            $.each($("input[name='compare_checkbox[]']:checked"), function() {
                values.push($(this).val());
                var valnew=$(this).val();
                arr=valnew.split('_');
                plan_id.push(arr[0]);
                plan_name.push(arr[1]);
                premium.push(arr[2]);
                policy_id.push(arr[3]);
                cover.push(arr[4]);
                arr_tenure.push(1);
            });
            $("#compareplanid").val(plan_id.join(','));
            $("#comparecoverid").val(cover.join(','));
            $("#comparepremium").val(premium.join(','));
            $("#compareplanname").val(plan_name.join(','));
            $("#comparetenure").val(arr_tenure.join(','));
            $("#comparePolicyid").val(policy_id.join(','));
            $('#compareCardForm').submit();

        }
       </script>
</body>
</html>
